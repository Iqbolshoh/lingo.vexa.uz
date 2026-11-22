<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$pdo = db();

$topics = [
    'releases' => ['name' => '🚀 Releases', 'color' => 0x6FB9F0, 'thread_id' => 18],
    'new_users' => ['name' => '👥 Students', 'color' => 0x8EEE98, 'thread_id' => 23],
    'enrollments' => ['name' => '📝 Leads', 'color' => 0xFFD67E, 'thread_id' => 24],
    'milestones' => ['name' => '🏆 Milestones', 'color' => 0xCB86DB, 'thread_id' => 26],
    'daily_stats' => ['name' => '📊 Reports', 'color' => 0xFF93B2, 'thread_id' => 28],
    'alerts' => ['name' => '⚠️ Alerts', 'color' => 0xFB6F5F, 'thread_id' => 29],
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
