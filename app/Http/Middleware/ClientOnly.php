<?php
namespace App\Http\Middleware;

use App\Models\Subscriber;
use Closure;
use Illuminate\Http\Request;

class ClientOnly
{
    public function handle(Request $request, Closure $next)
    {
        $s = Subscriber::with('quartier')
            ->whereNotNull('verified_at')
            ->find(session('subscriber_id'));

        if (!$s) {
            session()->forget('subscriber_id');
            return redirect('/connexion');
        }

        $request->attributes->set('subscriber', $s);
        return $next($request);
    }
}