<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$pdo = db();

$topics = [
    'releases' => ['name' => '🚀 Yangilanishlar & Relizlar', 'color' => 0x6FB9F0, 'thread_id' => 18],
    'new_users' => ['name' => '👥 Yangi O‘quvchilar', 'color' => 0x8EEE98],
    'enrollments' => ['name' => '📝 Kursga Arizalar', 'color' => 0xFFD67E],
    'milestones' => ['name' => '🏆 Yutuqlar & Sertifikatlar', 'color' => 0xCB86DB],
    'daily_stats' => ['name' => '📊 Kunlik Hisobotlar', 'color' => 0xFF93B2],
    'alerts' => ['name' => '⚠️ Tizim & Monitoring', 'color' => 0xFB6F5F],
];

$results = [];

foreach ($topics as $key => $info) {
    $threadId = null;
    if (isset($info['thread_id'])) {
        $threadId = $info['thread_id'];
    } else {
        $res = telegram_api('createForumTopic', [
            'chat_id' => $chatId,
            'name' => $info['name'],
            'icon_color' => $info['color'],
        ]);
        if (!empty($res['ok'])) {
            $threadId = (int) $res['result']['message_thread_id'];
        } else {
            echo "Failed for {$key}: " . json_encode($res, JSON_UNESCAPED_UNICODE) . "\n";
            continue;
        }
    }
    $metaKey = 'tg_topic_' . $key;
    $pdo->prepare("REPLACE INTO startup_meta (`key`, `value`) VALUES (?, ?)")->execute([$metaKey, (string)$threadId]);
    $results[$key] = $threadId;
    echo "Topic [{$key}]: {$info['name']} -> thread_id: {$threadId}\n";
}

echo "All topics configured successfully!\n";
