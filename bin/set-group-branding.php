<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$token = telegram_bot_token();

echo "1. Guruh nomini o'zgartirish...\n";
$newTitle = "LingoUz | Dev & System Ops";
$resTitle = telegram_api('setChatTitle', [
    'chat_id' => $chatId,
    'title' => $newTitle,
]);
echo "Title natijasi: " . json_encode($resTitle, JSON_UNESCAPED_UNICODE) . "\n";

echo "2. Guruh tavsifini (description) o'rnatish...\n";
$description = "LingoUz (lingo.vexa.uz) rasmiy dasturchilar va monitoring guruhi. Relizlar, yangi talabalar, lidlar va tizim ogohlantirishlari.";
$resDesc = telegram_api('setChatDescription', [
    'chat_id' => $chatId,
    'description' => $description,
]);
echo "Description natijasi: " . json_encode($resDesc, JSON_UNESCAPED_UNICODE) . "\n";

echo "3. Guruh rasmini (photo/avatar) o'rnatish...\n";
$photoPath = __DIR__ . '/../public/icon-512.png';

$ch = curl_init("https://api.telegram.org/bot{$token}/setChatPhoto");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'chat_id' => $chatId,
        'photo' => new CURLFile($photoPath, 'image/png', 'group-avatar.png'),
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
]);
$raw = curl_exec($ch);
curl_close($ch);

$resPhoto = json_decode((string)$raw, true);
echo "Photo natijasi: " . json_encode($resPhoto, JSON_UNESCAPED_UNICODE) . "\n";
