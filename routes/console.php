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

Artisan::command('lingo:notify-dev {message}', function (string $message) {
    require_once base_path('src/bootstrap.php');
    require_once base_path('src/telegram.php');
    $this->info("Dasturchilar guruhiga xabar yuborilmoqda: {$message}");
    $ok = telegram_notify_dev($message);
    if ($ok) {
        $this->info("Muvaffaqiyatli yuborildi!");
    } else {
        $this->error("Xabar yuborishda xatolik yuz berdi.");
    }
})->purpose('Dasturchilar guruhiga (-1003979299740) tezkor xabar yuborish');

Artisan::command('lingo:notify-release {title} {--ver=v1.2.0}', function (string $title) {
    require_once base_path('src/bootstrap.php');
    require_once base_path('src/telegram.php');
    $ver = (string) $this->option('ver');
    $this->info("Dasturchilar guruhiga yangilanish e'loni yuborilmoqda: {$title} ({$ver})");
    $ok = telegram_notify_release(
        $title,
        $ver,
        [
            'Telegram bot: Doimiy menyu va til tugmalari yangilandi',
            'Dasturchilar guruhi (-1003979299740) integratsiyasi faollashtirildi',
            'Ovoz va animatsiyalar butun platforma bo‘ylab integratsiya qilindi',
            'Yangi o‘quvchilar va arizalar bo‘yicha avtomatik bildirishnomalar',
        ]
    );
    if ($ok) {
        $this->info("Yangilanish muvaffaqiyatli e'lon qilindi!");
    } else {
        $this->error("Xatolik yuz berdi.");
    }
})->purpose('Dasturchilar guruhiga dastur yangilanishi va reliz e\'lonini yuborish');

Artisan::command('lingo:dev-summary {period=evening}', function (string $period) {
    require_once base_path('src/bootstrap.php');
    require_once base_path('src/telegram.php');
    $this->info("Platforma bo‘yicha kunlik hisobot yuborilmoqda ({$period})...");
    $ok = telegram_notify_dev_daily_summary($period);
    if ($ok) {
        $this->info("Kunlik hisobot guruhga yetkazildi!");
    } else {
        $this->error("Hisobot yuborishda xatolik.");
    }
})->purpose('Dasturchilar guruhiga kunlik platforma hisoboti (morning/evening) yuborish');
