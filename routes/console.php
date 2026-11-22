<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('lingo:telegram-daily', function () {
    require_once base_path('src/bootstrap.php');
    require_once base_path('src/telegram.php');
    $this->info("Telegram kunlik xabarnomalarini yuborish boshlandi...");
    $res = telegram_send_daily_broadcast();
    $this->info("Tugallandi! Jami obunachilar: {$res['total']}, Muvaffaqiyatli: {$res['sent']}, Xato: {$res['failed']}");
})->purpose('Telegram orqali o‘quvchilarga kunlik yangi so‘z, test va streak eslatmasini yuborish');
