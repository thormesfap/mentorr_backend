<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = [1, 5, 10];

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $to,
        private readonly Mailable $mailable
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $now = Carbon::now();

        // Chaves para controle de limite
        $hourlyKey = 'email_limit:hourly:' . $now->format('Y-m-d-H');
        $dailyKey = 'email_limit:daily:' . $now->format('Y-m-d');
        $secondKey = 'email_limit:second:' . $now->format('Y-m-d-H-i-s');

        // Verifica limite por segundo usando lock
        $lock = Cache::lock('email_lock:' . $secondKey, 1);
        if (!$lock->get()) {
            $this->release(1); // Tenta novamente em 1 segundo
            return;
        }

        // Verifica e incrementa limite por hora (500)
        $hourlyCount = Cache::get($hourlyKey, 0);
        if ($hourlyCount >= 500) {
            $lock->release();
            $this->release(60); // Tenta novamente em 1 minuto
            return;
        }

        // Verifica e incrementa limite por dia (2000)
        $dailyCount = Cache::get($dailyKey, 0);
        if ($dailyCount >= 2000) {
            $lock->release();
            $this->release(3600); // Tenta novamente em 1 hora
            return;
        }

        // Incrementa contadores
        Cache::increment($hourlyKey);
        Cache::increment($dailyKey);

        // Define TTL se ainda não existir
        if ($hourlyCount === 0) {
            Cache::put($hourlyKey, 1, Carbon::now()->addHour());
        }
        if ($dailyCount === 0) {
            Cache::put($dailyKey, 1, Carbon::now()->addDay());
        }

        try {
            // Envia o email
            Mail::to($this->to)->send($this->mailable);
        } finally {
            $lock->release();
        }
    }
}
