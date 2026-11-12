<?php
namespace App\Http\Controllers;

use App\Models\{Alert, Quartier, Subscriber};
use App\Services\Notifier;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ClientController extends Controller
{
    private const TTL_MIN = 10;
    private const MAX_ATTEMPTS = 5;

    public function loginForm()
    {
        return session('subscriber_id') ? redirect('/mon-espace') : view('client.login');
    }

    public function requestCode(Request $r)
    {
        $r->validate(
            ['phone' => ['required', 'regex:/^\+?[0-9 ]{8,16}$/']],
            ['phone.regex' => 'Numéro invalide.', 'required' => 'Champ obligatoire.']
        );

        $phone = Phone::normalize($r->input('phone'));
        $s = Subscriber::where('phone', $phone)->whereNotNull('verified_at')->first();

        // Pas de nouvel envoi si un code a été émis il y a moins d'une minute
        $exp = $s && $s->login_code_expires_at ? Carbon::parse($s->login_code_expires_at) : null;
        $recent = $exp && $exp->gt(now()->addMinutes(self::TTL_MIN - 1));

        if ($s && !$recent) {
            $code = (string) random_int(100000, 999999);
            $s->forceFill([
                'login_code'            => $this->hash($code),
                'login_code_expires_at' => now()->addMinutes(self::TTL_MIN),
                'login_attempts'        => 0,
            ])->save();
            $this->envoyer($s, $code);
        }

        // Même réponse que le numéro existe ou non : on ne révèle pas qui est inscrit
        session(['login_phone' => $phone]);
        return redirect('/connexion/code')->with('ok', 'Si ce numéro est inscrit, un code vient d’être envoyé.');
    }

    public function codeForm()
    {
        return session('login_phone') ? view('client.code') : redirect('/connexion');
    }

    public function verifyCode(Request $r)
    {
        $r->validate(['code' => 'required|digits:6'], ['code.*' => 'Le code contient 6 chiffres.']);

        $phone = session('login_phone');
        if (!$phone) return redirect('/connexion');

        $s = Subscriber::where('phone', $phone)->whereNotNull('verified_at')->first();
        $exp = $s && $s->login_code_expires_at ? Carbon::parse($s->login_code_expires_at) : null;

        if ($s && $s->login_code && $exp && $exp->isFuture() && $s->login_attempts < self::MAX_ATTEMPTS) {
            $s->increment('login_attempts');

            if (hash_equals($s->login_code, $this->hash((string) $r->input('code')))) {
                $s->forceFill([
                    'login_code' => null, 'login_code_expires_at' => null,
                    'login_attempts' => 0, 'last_login_at' => now(),
                ])->save();

                $r->session()->regenerate();
                session(['subscriber_id' => $s->id]);
                return redirect('/mon-espace');
            }
        }

        return back()->withErrors(['code' => 'Code incorrect ou expiré.']);
    }

    public function dashboard(Request $r)
    {
        $s = $r->attributes->get('subscriber');

        return view('client.dashboard', [
            's'          => $s,
            'actives'    => Alert::where('quartier_id', $s->quartier_id)
                ->where('created_at', '>', now()->subDay())->latest()->get(),
            'historique' => Alert::where('quartier_id', $s->quartier_id)->latest()->simplePaginate(10),
            'quartiers'  => Quartier::orderBy('name')->get(),
        ]);
    }

    public function updateQuartier(Request $r)
    {
        $d = $r->validate(['quartier_id' => 'required|exists:quartiers,id']);
        $r->attributes->get('subscriber')->update(['quartier_id' => $d['quartier_id']]);
        return redirect('/mon-espace')->with('ok', 'Quartier mis à jour.');
    }

    public function unsubscribe(Request $r)
    {
        $r->attributes->get('subscriber')->delete();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect('/')->with('ok', 'Vous êtes désinscrit. Vous ne recevrez plus d’alertes.');
    }

    public function logout(Request $r)
    {
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect('/connexion');
    }

    private function hash(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }

    private function envoyer(Subscriber $s, string $code): void
    {
        $texte = "Alerte Inondations : votre code de connexion est {$code} (valable " . self::TTL_MIN . " min)";

        if ($s->channel === 'telegram') {
            if ($s->telegram_chat_id) Notifier::telegram($s->telegram_chat_id, $texte);
        } else {
            Notifier::sms($s->phone, $texte);
        }
    }
}