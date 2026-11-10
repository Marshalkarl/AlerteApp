<?php
namespace App\Http\Controllers;
use App\Models\Subscriber;
use App\Services\Notifier;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function webhook(Request $r, string $secret)
    {
        abort_unless(config('alerts.telegram_secret') && hash_equals((string) config('alerts.telegram_secret'), $secret), 403);

        $m = $r->input('message');
        $text = $m['text'] ?? '';
        if (str_starts_with($text, '/start ')) {
            $s = Subscriber::where('token', trim(substr($text, 7)))->first();
            if ($s) {
                $s->update(['telegram_chat_id' => $m['chat']['id'], 'verified_at' => now()]);
                Notifier::telegram($m['chat']['id'], "Inscription confirmée pour {$s->quartier->name}. Vous recevrez les alertes ici.");
            }
        }
        return response()->noContent();
    }
}
