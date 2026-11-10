<?php
namespace App\Services;

use App\Models\Subscriber;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Notifier
{
    public static function send(Subscriber $s, string $text): void
    {
        $s->channel === 'telegram'
            ? self::telegram($s->telegram_chat_id, $text)
            : self::sms($s->phone, $text);
    }

    public static function telegram($chat, string $text): void
    {
        // Mode test : sans clé Telegram, on écrit dans le journal
        if (!config('alerts.telegram_token')) {
            Log::info("Telegram vers $chat : $text");
            return;
        }

        Http::post('https://api.telegram.org/bot' . config('alerts.telegram_token') . '/sendMessage', [
            'chat_id' => $chat,
            'text' => $text,
        ])->throw();
    }

    // À adapter au format de ton fournisseur SMS (URL, en-têtes, noms des champs).
    public static function sms(string $to, string $text): void
    {
        // Mode test : sans URL de fournisseur, on écrit dans le journal
        if (!config('alerts.sms_url')) {
            Log::info("SMS vers $to : $text");
            return;
        }

        Http::withToken((string) config('alerts.sms_key'))
            ->post(config('alerts.sms_url'), [
                'to' => $to,
                'from' => config('alerts.sms_from'),
                'message' => $text,
            ])->throw();
    }
}