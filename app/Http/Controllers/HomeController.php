<?php
namespace App\Http\Controllers;
use App\Models\{Alert, Quartier, Subscriber};
use App\Services\Notifier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'quartiers' => Quartier::orderBy('name')->get(),
            'alerts' => Alert::with('quartier')->where('created_at', '>', now()->subDay())->latest()->get(),
        ]);
    }

    public function subscribe(Request $r)
    {
        $d = $r->validate([
            'phone' => ['required', 'regex:/^\+?[0-9 ]{8,16}$/'],
            'quartier_id' => 'required|exists:quartiers,id',
            'channel' => 'required|in:sms,telegram',
            'consent' => 'accepted',
        ], [
            'phone.regex' => 'Numéro invalide.',
            'consent.accepted' => 'Cochez la case pour accepter de recevoir des alertes.',
            'required' => 'Champ obligatoire.',
        ]);

        $phone = preg_replace('/\s+/', '', $d['phone']);
        if (preg_match('/^\d{8}$/', $phone)) $phone = '+228' . $phone;

        $s = Subscriber::firstOrNew(['phone' => $phone]);
        if ($s->verified_at) return back()->with('ok', 'Ce numéro est déjà inscrit.');

        $s->fill([
            'quartier_id' => $d['quartier_id'],
            'channel' => $d['channel'],
            'token' => Str::random(10),
            'code' => (string) random_int(100000, 999999),
        ])->save();

        if ($s->channel === 'sms') {
            Notifier::sms($s->phone, "Alerte Inondations : votre code de confirmation est {$s->code}");
        }
        return redirect("/verifier/{$s->token}");
    }

    public function verifyForm(string $token)
    {
        return view('verify', ['s' => Subscriber::where('token', $token)->firstOrFail()]);
    }

    public function verify(Request $r, string $token)
    {
        $s = Subscriber::where('token', $token)->firstOrFail();
        if ($s->code && hash_equals($s->code, (string) $r->input('code'))) {
            $s->update(['verified_at' => now(), 'code' => null]);
            return redirect('/')->with('ok', "Inscription confirmée pour {$s->quartier->name}.");
        }
        return back()->withErrors(['code' => 'Code incorrect.']);
    }
}
