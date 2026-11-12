<?php
namespace App\Http\Controllers;

use App\Jobs\SendAlert;
use App\Models\{Alert, Quartier, Subscriber};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        if (!session('admin')) return view('admin-login');

        $depuis = now()->subDays(30);

        $stats = [
            'inscrits'     => Subscriber::whereNotNull('verified_at')->count(),
            'en_attente'   => Subscriber::whereNull('verified_at')->count(),
            'alertes_30j'  => Alert::where('created_at', '>=', $depuis)->count(),
            'messages_30j' => (int) Alert::where('created_at', '>=', $depuis)->sum('sent_count'),
        ];

        $canaux = Subscriber::whereNotNull('verified_at')
            ->selectRaw('channel, count(*) as n')->groupBy('channel')->pluck('n', 'channel');

        // Alertes par jour sur 14 jours
        $parJour = Alert::where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->get(['created_at'])
            ->groupBy(fn ($a) => $a->created_at->format('Y-m-d'))
            ->map->count();

        $jours = collect(range(13, 0))->map(function ($i) use ($parJour) {
            $d = now()->subDays($i);
            return ['label' => $d->format('d/m'), 'n' => $parJour[$d->format('Y-m-d')] ?? 0];
        });

        return view('admin.dashboard', [
            'stats'     => $stats,
            'canaux'    => $canaux,
            'jours'     => $jours,
            'quartiers' => Quartier::withCount(['subscribers' => fn ($q) => $q->whereNotNull('verified_at')])
                ->orderBy('name')->get(),
            'alerts'    => Alert::with('quartier')->latest()->take(10)->get(),
        ]);
    }

    public function login(Request $r)
    {
        $key = (string) config('alerts.admin_key');
        if ($key !== '' && hash_equals($key, (string) $r->input('key'))) {
            $r->session()->regenerate();
            session(['admin' => true]);
            return redirect('/admin');
        }
        return back()->withErrors(['key' => 'Clé incorrecte.']);
    }

    public function logout(Request $r)
    {
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect('/admin');
    }

    public function send(Request $r)
    {
        abort_unless(session('admin'), 403);
        $d = $r->validate([
            'quartier_id' => 'required|exists:quartiers,id',
            'type'        => 'required|in:inond,eau,elec',
            'message'     => 'required|max:140',
        ]);
        $a = Alert::create($d);
        SendAlert::dispatch($a->id);
        return redirect('/admin')->with('ok', "Envoi lancé pour {$a->quartier->name}.");
    }

    public function subscribers(Request $r)
    {
        $q = Subscriber::with('quartier')->latest();

        if ($s = trim((string) $r->query('q'))) $q->where('phone', 'like', '%' . $s . '%');
        if ($r->filled('quartier')) $q->where('quartier_id', $r->query('quartier'));
        if ($r->query('statut') === 'verifie') $q->whereNotNull('verified_at');
        if ($r->query('statut') === 'attente') $q->whereNull('verified_at');

        return view('admin.habitants', [
            'subscribers' => $q->simplePaginate(20)->withQueryString(),
            'quartiers'   => Quartier::orderBy('name')->get(),
        ]);
    }

    public function destroySubscriber(Subscriber $subscriber)
    {
        $subscriber->delete();
        return back()->with('ok', 'Habitant supprimé.');
    }

    public function quartiers()
{
    return view('admin.quartiers', [
        'quartiers' => Quartier::withCount('subscribers')->orderBy('name')->get(),
    ]);
}

public function storeQuartier(Request $r)
{
    $d = $r->validate(['name' => 'required|string|max:60|unique:quartiers,name']);
    DB::table('quartiers')->insert(['name' => trim($d['name'])]);
    return back()->with('ok', 'Quartier ajouté.');
}

public function updateQuartier(Request $r, Quartier $quartier)
{
    $d = $r->validate(['name' => 'required|string|max:60|unique:quartiers,name,' . $quartier->id]);
    DB::table('quartiers')->where('id', $quartier->id)->update(['name' => trim($d['name'])]);
    return back()->with('ok', 'Quartier renommé.');
}

public function destroyQuartier(Quartier $quartier)
{
    if ($quartier->subscribers()->exists() || Alert::where('quartier_id', $quartier->id)->exists()) {
        return back()->withErrors(['name' => 'Impossible : ce quartier a des habitants ou des alertes.']);
    }
    DB::table('quartiers')->where('id', $quartier->id)->delete();
    return back()->with('ok', 'Quartier supprimé.');
}
}