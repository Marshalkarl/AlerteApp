<?php
namespace App\Jobs;
use App\Models\{Alert, Subscriber};
use App\Services\Notifier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendAlert implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $alertId) {}

    public function handle(): void
    {
        $a = Alert::with('quartier')->find($this->alertId);
        if (!$a) return;
        $text = "Alerte {$a->quartier->name} : {$a->message}";

        Subscriber::where('quartier_id', $a->quartier_id)
            ->whereNotNull('verified_at')
            ->chunkById(100, function ($subs) use ($a, $text) {
                foreach ($subs as $s) {
                    try {
                        Notifier::send($s, $text);
                        $a->increment('sent_count');
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            });
    }
}
