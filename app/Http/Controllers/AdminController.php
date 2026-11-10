<?php
namespace App\Http\Controllers;
use App\Jobs\SendAlert;
use App\Models\{Alert, Quartier};
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        if (!session('admin')) return view('admin-login');
        return view('admin', [
            'quartiers' => Quartier::withCount(['subscribers' => fn ($q) => $q->whereNotNull('verified_at')])->orderBy('name')->get(),
            'alerts' => Alert::with('quartier')->latest()->take(10)->get(),
        ]);
    }

    public function login(Request $r)
    {
        $key = (string) config('alerts.admin_key');
        if ($key !== '' && hash_equals($key, (string) $r->input('key'))) {
            session(['admin' => true]);
            return redirect('/admin');
        }
        return back()->withErrors(['key' => 'Clé incorrecte.']);
    }

    public function send(Request $r)
    {
        abort_unless(session('admin'), 403);
        $d = $r->validate([
            'quartier_id' => 'required|exists:quartiers,id',
            'type' => 'required|in:inond,eau,elec',
            'message' => 'required|max:140',
        ]);
        $a = Alert::create($d);
        SendAlert::dispatch($a->id);
        return redirect('/admin')->with('ok', "Envoi lancé pour {$a->quartier->name}.");
    }
}
