<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/i18n.php';

/**
 * Returns the Telegram bot token from environment or database.
 */
function telegram_bot_token(): string
{
    $token = trim((string) (getenv('TELEGRAM_BOT_TOKEN') ?: ''));
    if ($token !== '') {
        return $token;
    }
    try {
        $val = db()->query("SELECT `value` FROM startup_meta WHERE `key` = 'telegram_bot_token'")->fetchColumn();
        return trim((string) ($val ?: ''));
    } catch (\Throwable $e) {
        return '';
    }
}

/**
 * Returns the Telegram bot username (e.g. 'LingoUzBot' without '@').
 */
function telegram_bot_username(): string
{
    $username = trim((string) (getenv('TELEGRAM_BOT_USERNAME') ?: ''));
    if ($username !== '') {
        return ltrim($username, '@');
    }
    try {
        $val = db()->query("SELECT `value` FROM startup_meta WHERE `key` = 'telegram_bot_username'")->fetchColumn();
        if ($val) {
            return ltrim(trim((string) $val), '@');
        }
    } catch (\Throwable $e) {
    }
    return 'LingoUzBot';
}

/**
 * Returns secret token for webhook verification.
 */
function telegram_webhook_secret(): string
{
    $secret = trim((string) (getenv('TELEGRAM_WEBHOOK_SECRET') ?: ''));
    return $secret !== '' ? $secret : 'lingo_secret_2026_webhook';
}

/**
 * Executes a call to the Telegram Bot API.
 */
function telegram_api(string $method, array $params = []): ?array
{
    $token = telegram_bot_token();
    if ($token === '') {
        return null;
    }

    $url = "https://api.telegram.org/bot{$token}/{$method}";
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($params, JSON_UNESCAPED_UNICODE),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 12,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err || !$response) {
        error_log("[Telegram API Error] Method {$method} failed: " . ($err ?: 'No response'));
        return null;
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded) || empty($decoded['ok'])) {
        error_log("[Telegram API Error] Method {$method} response: " . $response);
    }
    return $decoded;
}

/**
 * Sends a message to a Telegram chat with automatic New Latin alphabet conversion.
 */
function telegram_send_message(string|int $chatId, string $text, ?array $replyMarkup = null, string $parseMode = 'HTML'): ?array
{
    $formattedText = to_new_latin($text);
    $params = [
        'chat_id' => $chatId,
        'text' => $formattedText,
        'parse_mode' => $parseMode,
        'disable_web_page_preview' => false,
    ];
    if ($replyMarkup !== null) {
        $params['reply_markup'] = $replyMarkup;
    }
    return telegram_api('sendMessage', $params);
}

/**
 * Sends a native quiz poll to Telegram.
 */
function telegram_send_poll(string|int $chatId, string $question, array $options, int $correctOptionId, string $explanation = ''): ?array
{
    $formattedQuestion = to_new_latin($question);
    $formattedOptions = array_map(fn ($opt) => to_new_latin((string) $opt), array_values($options));
    $params = [
        'chat_id' => $chatId,
        'question' => mb_substr($formattedQuestion, 0, 300),
        'options' => array_slice($formattedOptions, 0, 10),
        'type' => 'quiz',
        'correct_option_id' => max(0, min($correctOptionId, count($options) - 1)),
        'is_anonymous' => false,
    ];
    if ($explanation !== '') {
        $params['explanation'] = mb_substr(to_new_latin($explanation), 0, 200);
        $params['explanation_parse_mode'] = 'HTML';
    }
    return telegram_api('sendPoll', $params);
}

/**
 * Gets or creates a unique linking token for the user.
 */
function get_or_create_telegram_link_token(int $userId): string
{
    $pdo = db();
    $stmt = $pdo->prepare('SELECT telegram_link_token FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $token = (string) $stmt->fetchColumn();

    if ($token !== '' && strlen($token) >= 16) {
        return $token;
    }

    $token = bin2hex(random_bytes(16));
    $up = $pdo->prepare('UPDATE users SET telegram_link_token = ? WHERE id = ?');
    $up->execute([$token, $userId]);
    return $token;
}

/**
 * Generates the 1-click Telegram deep link (algor.uz style).
 */
function get_telegram_link_url(array $user): string
{
    $token = get_or_create_telegram_link_token((int) $user['id']);
    $bot = telegram_bot_username();
    return "https://t.me/{$bot}?start=link_{$token}";
}

/**
 * Returns main persistent reply keyboard for the bot.
 */
function telegram_main_keyboard(): array
{
    return [
        'keyboard' => [
            [['text' => '📖 Bugungi söz'], ['text' => '🎯 Mini-test']],
            [['text' => '🔥 Streak holati'], ['text' => '📊 Mening hisobim']],
            [['text' => '🌐 LingoUz Saytiga kiriş']]
        ],
        'resize_keyboard' => true,
        'one_time_keyboard' => false,
    ];
}

/**
 * Selects today's word from vocabulary.
 */
function telegram_today_word(): ?array
{
    $pdo = db();
    $count = (int) $pdo->query('SELECT count(*) FROM vocabulary')->fetchColumn();
    if ($count === 0) {
        return null;
    }
    $offset = (int) date('z') % $count;
    $stmt = $pdo->prepare('SELECT * FROM vocabulary ORDER BY id ASC LIMIT 1 OFFSET ?');
    $stmt->bindValue(1, $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch() ?: null;
}

/**
 * Selects a random choice quiz question.
 */
function telegram_random_quiz(): ?array
{
    $pdo = db();
    $stmt = $pdo->query("SELECT * FROM questions WHERE type = 'choice' AND options_json IS NOT NULL ORDER BY RAND() LIMIT 1");
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    $options = json_decode((string) $row['options_json'], true);
    if (!is_array($options) || count($options) < 2) {
        return null;
    }
    $row['parsed_options'] = $options;
    return $row;
}

/**
 * Handles incoming webhook update from Telegram.
 */
function handle_telegram_webhook(array $update): void
{
    $message = $update['message'] ?? $update['edited_message'] ?? null;
    if (!$message) {
        return;
    }

    $chatId = $message['chat']['id'] ?? null;
    $from = $message['from'] ?? [];
    $text = trim((string) ($message['text'] ?? ''));

    if (!$chatId) {
        return;
    }

    $tgUsername = $from['username'] ?? '';
    $tgFirstName = $from['first_name'] ?? ($from['username'] ?? 'Döst');
    $pdo = db();

    // 1. Deep linking: /start link_<token>
    if (preg_match('/^\/start\s+link_([a-f0-9]{24,64})/i', $text, $matches)) {
        $token = $matches[1];
        $stmt = $pdo->prepare('SELECT * FROM users WHERE telegram_link_token = ? LIMIT 1');
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            $isFirstLink = empty($user['telegram_chat_id']);
            $rewardXp = $isFirstLink ? 20 : 0;
            $rewardCoins = $isFirstLink ? 5 : 0;

            $up = $pdo->prepare('
                UPDATE users 
                SET telegram_chat_id = ?, 
                    telegram_username = ?, 
                    telegram_first_name = ?, 
                    telegram_linked_at = NOW(),
                    telegram_notifications = 1,
                    xp = xp + ?, 
                    coins = coins + ? 
                WHERE id = ?
            ');
            $up->execute([
                (string) $chatId,
                $tgUsername ?: null,
                $tgFirstName ?: null,
                $rewardXp,
                $rewardCoins,
                $user['id']
            ]);

            $welcomeMsg = "Assalomu alaykum, <b>" . e($user['display_name']) . "</b>! 🎉\n\n" .
                "Tabriklaymiz, sizning LingoUz hisobingiz Telegram botimizga muvaffaqiyatli ulandi!\n\n";

            if ($isFirstLink) {
                $welcomeMsg .= "🎁 <b>Sovğa:</b> Hisobingizga <b>+20 XP</b> va <b>5 ta tanga</b> taqdim etildi!\n\n";
            }

            $welcomeMsg .= "Endi siz:\n" .
                "• Har kuni 1 ta yangi o‘zbekcha so‘z kartochkasi\n" .
                "• Qiziqarli mini-viktorina va savollar\n" .
                "• Streakingiz (ketma-ketlik) uzilib qolmasligi uchun eslatmalarni qabul qilasiz.\n\n" .
                "Bilim olishni davom ettirish uchun pastdagi menyudan foydalaning! 👇";

            telegram_send_message($chatId, $welcomeMsg, telegram_main_keyboard());
            return;
        } else {
            $failMsg = "Kechirasiz, havola yaroqsiz yoki muddati o‘tgan. Iltimos, <a href=\"https://lingo.vexa.uz/dashboard\">LingoUz Boshqaruv paneliga</a> kirib, yangi havola orqali urinib ko‘ring.";
            telegram_send_message($chatId, $failMsg);
            return;
        }
    }

    // Find linked user for this chat
    $uStmt = $pdo->prepare('SELECT * FROM users WHERE telegram_chat_id = ? LIMIT 1');
    $uStmt->execute([(string) $chatId]);
    $user = $uStmt->fetch();

    // 2. Command: /start (without token)
    if ($text === '/start') {
        if ($user) {
            $msg = "Qaytganingiz bilan, <b>" . e($user['display_name']) . "</b>! 👋\n\n" .
                "🔥 <b>Streakingiz:</b> " . (int) $user['streak_days'] . " kun\n" .
                "⭐ <b>XP:</b> " . (int) $user['xp'] . " | 🪙 <b>Tangalar:</b> " . (int) $user['coins'] . "\n\n" .
                "Bugun nima o‘rganamiz? Quyidagi tugmalardan birini tanlang:";
            telegram_send_message($chatId, $msg, telegram_main_keyboard());
        } else {
            $msg = "Assalomu alaykum! 👋 Men LingoUz platformasining rasmiy yordamchisiman.\n\n" .
                "Hisobingizni ulash va har kuni dars eslatmalari hamda yangi so‘zlarni qabul qilish uchun saytimizdagi shaxsiy kabinetingizga kiring va <b>«Telegram botni ulash»</b> tugmasini bosing:\n\n" .
                "👉 https://lingo.vexa.uz/dashboard\n\n" .
                "Hisobingizni ulagan zahotingiz sizga <b>+20 XP</b> va <b>5 ta tanga</b> sovg‘a qilinadi! 🎁";
            telegram_send_message($chatId, $msg, [
                'inline_keyboard' => [
                    [['text' => '🌐 Saytga o‘tish va hisobni ulash', 'url' => 'https://lingo.vexa.uz/dashboard']]
                ]
            ]);
        }
        return;
    }

    // 3. Command: Bugungi so'z / /word
    if ($text === '📖 Bugungi söz' || $text === '📖 Bugungi so‘z' || $text === '/word') {
        $word = telegram_today_word();
        if ($word) {
            $msg = "📖 <b>Bugungi yangi so‘z:</b>\n\n" .
                "🇺🇿 <b>" . e($word['word_uz']) . "</b>" . ($word['pronunciation'] ? " [<i>" . e($word['pronunciation']) . "</i>]" : "") . "\n" .
                "🇬🇧 Tarjimasi: <b>" . e($word['word_en']) . "</b>\n\n";
            if (!empty($word['example'])) {
                $msg .= "💡 <b>Misol:</b>\n<i>«" . e($word['example']) . "»</i>\n\n";
            }
            $msg .= "🎧 Ovozli talaffuz va barcha so‘zlarni ko‘rish uchun Flashcards trenajyoriga o‘ting:";

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🎴 Aqlli Flashcards trenajyori', 'url' => 'https://lingo.vexa.uz/flashcards']],
                    [['text' => '📚 Barcha darslar', 'url' => 'https://lingo.vexa.uz/courses']]
                ]
            ];
            telegram_send_message($chatId, $msg, $keyboard);
        } else {
            telegram_send_message($chatId, "Hozircha yangi so‘zlar bazasi tayyorlanmoqda.");
        }
        return;
    }

    // 4. Command: Mini-test / /quiz
    if ($text === '🎯 Mini-test' || $text === '/quiz') {
        $quiz = telegram_random_quiz();
        if ($quiz) {
            telegram_send_poll(
                $chatId,
                $quiz['prompt'],
                $quiz['parsed_options'],
                (int) $quiz['correct_index'],
                (string) ($quiz['explanation'] ?? 'To‘g‘ri javob!')
            );
        } else {
            telegram_send_message($chatId, "Savollar bazasi yangilanmoqda.");
        }
        return;
    }

    // 5. Command: Streak holati / /streak
    if ($text === '🔥 Streak holati' || $text === '/streak') {
        if ($user) {
            $streak = (int) $user['streak_days'];
            $flames = str_repeat('🔥', max(1, min(5, $streak)));
            $msg = "{$flames} <b>Sizning ketma-ketligingiz: " . $streak . " kun!</b>\n\n" .
                ($streak > 0 
                    ? "Ajoyib natija! Har kuni 5 daqiqa dars qilish orqali o‘zbek tilini mukammal o‘rgana olasiz. Ketma-ketlikni to‘xtatib qo‘ymang!" 
                    : "Siz hali streakni boshlamadingiz. Bugunoq bitta darsni yakunlab, 1 kunlik olovli yutuqqa ega bo‘ling!") . "\n\n" .
                "👉 <a href=\"https://lingo.vexa.uz/dashboard\">Darsga o‘tish va streakni oshirish</a>";
            telegram_send_message($chatId, $msg, [
                'inline_keyboard' => [
                    [['text' => '🚀 Darsni boshlash', 'url' => 'https://lingo.vexa.uz/dashboard']]
                ]
            ]);
        } else {
            telegram_send_message($chatId, "Hisobingiz ulanmagan. Saytdagi profilingizdan botni ulang: https://lingo.vexa.uz/dashboard");
        }
        return;
    }

    // 6. Command: Mening hisobim / /stats / /profile
    if ($text === '📊 Mening hisobim' || $text === '/stats' || $text === '/profile') {
        if ($user) {
            $msg = "👤 <b>O‘quvchi profili:</b>\n\n" .
                "Ism: <b>" . e($user['display_name']) . "</b>\n" .
                "⭐ To‘plangan XP: <b>" . (int) $user['xp'] . "</b>\n" .
                "🪙 Tangalar: <b>" . (int) $user['coins'] . "</b>\n" .
                "🔥 Streak: <b>" . (int) $user['streak_days'] . " kun</b>\n" .
                "🎯 Daraja: <b>" . e($user['language_level'] ?: 'A0') . "</b>\n\n" .
                "Batafsil natijalar va yangi yutuqlar uchun shaxsiy kabinetingizga kiring:";
            telegram_send_message($chatId, $msg, [
                'inline_keyboard' => [
                    [['text' => '🌐 Shaxsiy kabinetga kirish', 'url' => 'https://lingo.vexa.uz/dashboard']],
                    [['text' => '🏆 Reyting va Liga', 'url' => 'https://lingo.vexa.uz/leaderboard']]
                ]
            ]);
        } else {
            telegram_send_message($chatId, "Hisobingiz ulanmagan. Iltimos, <a href=\"https://lingo.vexa.uz/dashboard\">saytga</a> kirib hisobingizni ulang.");
        }
        return;
    }

    // 7. Command: LingoUz sayti
    if ($text === '🌐 LingoUz Saytiga kiriş' || $text === '🌐 LingoUz Sayti' || $text === '/site') {
        telegram_send_message($chatId, "Platformaga tezkor o‘tish uchun havolani bosing:\n\n👉 https://lingo.vexa.uz/dashboard", [
            'inline_keyboard' => [
                [['text' => '🚀 LingoUz Platformasini Ochish', 'url' => 'https://lingo.vexa.uz/dashboard']]
            ]
        ]);
        return;
    }

    // 8. Command: /unlink
    if ($text === '/unlink') {
        $pdo->prepare('UPDATE users SET telegram_chat_id = NULL, telegram_username = NULL, telegram_linked_at = NULL WHERE telegram_chat_id = ?')
            ->execute([(string) $chatId]);
        telegram_send_message($chatId, "Telegram hisobingiz LingoUz platformasidan uzildi. Qayta ulash uchun saytdagi Boshqaruv paneliga kiring.", ['remove_keyboard' => true]);
        return;
    }

    // Default response for unhandled text
    $helpMsg = "Assalomu alaykum! Men sizga o‘zbek tilini o‘rganishda yordam beraman.\n\nPastdagi menyu tugmalaridan birini tanlang yoki /word (yangi so‘z), /quiz (test), /streak (ketma-ketlik) buyruqlarini yuboring.";
    telegram_send_message($chatId, $helpMsg, telegram_main_keyboard());
}

/**
 * Sends the daily educational notification and streak reminder to all connected subscribers.
 */
function telegram_send_daily_broadcast(): array
{
    $pdo = db();
    $stmt = $pdo->query('
        SELECT id, display_name, streak_days, telegram_chat_id, xp, coins 
        FROM users 
        WHERE telegram_chat_id IS NOT NULL AND telegram_notifications = 1
    ');
    $subscribers = $stmt->fetchAll();

    $word = telegram_today_word();
    $quiz = telegram_random_quiz();

    $sentCount = 0;
    $failedCount = 0;

    foreach ($subscribers as $sub) {
        $chatId = $sub['telegram_chat_id'];
        if (!$chatId) {
            continue;
        }

        $streak = (int) $sub['streak_days'];
        $streakText = $streak > 0 
            ? "🔥 <b>Streakingiz: {$streak} kun!</b> Bugungi darsingizni yakunlab, ketma-ketlikni saqlab qoling."
            : "🔥 Bugun 1 ta dars yakunlab, o‘z streakingizni boshlang!";

        $msg = "🌅 <b>Xayrli kun, " . e($sub['display_name']) . "!</b>\n\n" .
            $streakText . "\n\n";

        if ($word) {
            $msg .= "📖 <b>Bugungi yangi so‘z:</b>\n" .
                "🇺🇿 <b>" . e($word['word_uz']) . "</b>" . ($word['pronunciation'] ? " [<i>" . e($word['pronunciation']) . "</i>]" : "") . " — " . e($word['word_en']) . "\n";
            if (!empty($word['example'])) {
                $msg .= "💡 <i>«" . e($word['example']) . "»</i>\n";
            }
            $msg .= "\n";
        }

        $msg .= "Pastdagi mini-test orqali bilimingizni sinab ko‘ring va darsga o‘ting! 👇";

        $keyboard = [
            'inline_keyboard' => [
                [['text' => '🚀 Darsni davom ettirish', 'url' => 'https://lingo.vexa.uz/dashboard']],
                [['text' => '🎴 Flashcards trenajyori', 'url' => 'https://lingo.vexa.uz/flashcards']]
            ]
        ];

        $res = telegram_send_message($chatId, $msg, $keyboard);
        if ($res && !empty($res['ok'])) {
            $sentCount++;
            // Send quiz poll
            if ($quiz) {
                telegram_send_poll(
                    $chatId,
                    $quiz['prompt'],
                    $quiz['parsed_options'],
                    (int) $quiz['correct_index'],
                    (string) ($quiz['explanation'] ?? '')
                );
            }
            // Log to database
            try {
                $log = $pdo->prepare('INSERT INTO telegram_daily_logs (user_id, chat_id, word_id, question_id, message_type) VALUES (?, ?, ?, ?, "daily")');
                $log->execute([$sub['id'], (string) $chatId, $word['id'] ?? null, $quiz['id'] ?? null]);
            } catch (\Throwable $e) {}
        } else {
            $failedCount++;
        }
    }

    return [
        'total' => count($subscribers),
        'sent' => $sentCount,
        'failed' => $failedCount,
    ];
}

/**
 * Sets the Telegram Bot Webhook to the official domain URL.
 */
function telegram_set_webhook(): array
{
    $token = telegram_bot_token();
    if ($token === '') {
        return ['ok' => false, 'description' => 'Bot token kiritilmagan.'];
    }

    $webhookUrl = 'https://lingo.vexa.uz/api/telegram/webhook';
    $secret = telegram_webhook_secret();

    $res = telegram_api('setWebhook', [
        'url' => $webhookUrl,
        'secret_token' => $secret,
        'allowed_updates' => ['message', 'callback_query', 'poll_answer'],
        'drop_pending_updates' => true,
    ]);

    return $res ?: ['ok' => false, 'description' => 'Telegram API ga ulanishda xatolik yuz berdi.'];
}
