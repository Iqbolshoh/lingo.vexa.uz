<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/i18n.php';
require_once __DIR__ . '/og.php';

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
 * Returns the Telegram bot username (e.g. 'vexa_lingo_bot' without '@').
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
    return 'vexa_lingo_bot';
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
 * Logs Telegram debug/error events into storage/logs/telegram.log without polluting FastCGI stderr.
 */
function telegram_log(string $message): void
{
    $dir = __DIR__ . '/../storage/logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $logFile = $dir . '/telegram.log';
    $time = date('Y-m-d H:i:s');
    @file_put_contents($logFile, "[{$time}] {$message}\n", FILE_APPEND | LOCK_EX);
}

/**
 * Returns the developer / system updates Telegram chat ID (group or supergroup).
 */
function telegram_dev_chat_id(): string
{
    $chatId = trim((string) (getenv('TELEGRAM_DEV_CHAT_ID') ?: ''));
    if ($chatId !== '') {
        return $chatId;
    }
    try {
        $val = db()->query("SELECT `value` FROM startup_meta WHERE `key` = 'telegram_dev_chat_id'")->fetchColumn();
        if ($val) {
            return trim((string) $val);
        }
    } catch (\Throwable $e) {
    }
    return '-1003979299740';
}

/**
 * Executes a call to the Telegram Bot API.
 */
function telegram_api(string $method, array $params = [], bool $isMultipart = false): ?array
{
    $token = telegram_bot_token();
    if ($token === '') {
        return null;
    }

    $url = "https://api.telegram.org/bot{$token}/{$method}";
    $ch = curl_init($url);

    if ($isMultipart) {
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
    } else {
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($params, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
    }

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err || !$response) {
        telegram_log("[Telegram API Error] Method {$method} failed: " . ($err ?: 'No response'));
        return null;
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded) || empty($decoded['ok'])) {
        telegram_log("[Telegram API Error] Method {$method} response: " . $response);
    }
    return $decoded;
}

/**
 * Answers a callback query (for inline button clicks).
 */
function telegram_answer_callback(string $callbackQueryId, string $text = '', bool $showAlert = false): ?array
{
    $params = [
        'callback_query_id' => $callbackQueryId,
        'text' => to_new_latin($text),
        'show_alert' => $showAlert,
    ];
    return telegram_api('answerCallbackQuery', $params);
}

/**
 * Sends a text message with automatic formatting and New Latin conversion for Uzbek.
 */
function telegram_send_message(string|int $chatId, string $text, ?array $replyMarkup = null, string $parseMode = 'HTML', string $lang = 'uz'): ?array
{
    $formattedText = $lang === 'uz' ? to_new_latin($text) : $text;
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
 * Sends a photo message (e.g. dynamic OG share card) with caption and buttons.
 */
function telegram_send_photo(string|int $chatId, string $photoPathOrUrl, string $caption = '', ?array $replyMarkup = null, string $parseMode = 'HTML', string $lang = 'uz'): ?array
{
    $formattedCaption = $lang === 'uz' ? to_new_latin($caption) : $caption;
    $params = [
        'chat_id' => $chatId,
        'caption' => $formattedCaption,
        'parse_mode' => $parseMode,
    ];

    if (file_exists($photoPathOrUrl)) {
        $params['photo'] = new CURLFile($photoPathOrUrl);
        if ($replyMarkup !== null) {
            $params['reply_markup'] = is_string($replyMarkup) ? $replyMarkup : json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
        }
        return telegram_api('sendPhoto', $params, true);
    } else {
        $params['photo'] = $photoPathOrUrl;
        if ($replyMarkup !== null) {
            $params['reply_markup'] = is_array($replyMarkup) ? $replyMarkup : json_decode($replyMarkup, true);
        }
        return telegram_api('sendPhoto', $params, false);
    }
}

/**
 * Sends a native quiz poll to Telegram.
 */
function telegram_send_poll(string|int $chatId, string $question, array $options, int $correctOptionId, string $explanation = '', string $lang = 'uz'): ?array
{
    $formattedQuestion = $lang === 'uz' ? to_new_latin($question) : $question;
    $formattedOptions = array_map(fn ($opt) => $lang === 'uz' ? to_new_latin((string) $opt) : (string) $opt, array_values($options));

    $params = [
        'chat_id' => $chatId,
        'question' => mb_substr($formattedQuestion, 0, 300),
        'options' => array_slice($formattedOptions, 0, 10),
        'type' => 'quiz',
        'correct_option_id' => max(0, min($correctOptionId, count($options) - 1)),
        'is_anonymous' => false,
    ];
    if ($explanation !== '') {
        $formattedExplanation = $lang === 'uz' ? to_new_latin($explanation) : $explanation;
        $params['explanation'] = mb_substr($formattedExplanation, 0, 200);
        $params['explanation_parse_mode'] = 'HTML';
    }
    return telegram_api('sendPoll', $params);
}

/**
 * Detects user preferred language ('uz' or 'en').
 */
function telegram_user_lang(?array $user, array $from = []): string
{
    if ($user && !empty($user['telegram_lang'])) {
        return $user['telegram_lang'] === 'en' ? 'en' : 'uz';
    }
    $langCode = strtolower((string) ($from['language_code'] ?? ''));
    if (str_starts_with($langCode, 'en')) {
        return 'en';
    }
    return 'uz';
}

/**
 * Returns main persistent reply keyboard in user's language.
 */
function telegram_main_keyboard(string $lang = 'uz'): array
{
    if ($lang === 'en') {
        return [
            'keyboard' => [
                [['text' => '📖 Word of the day'], ['text' => '🎯 Mini-quiz']],
                [['text' => '🔥 Streak status'], ['text' => '📊 My profile']],
                [['text' => '📚 Audio stories'], ['text' => '🎓 Certificate']],
                [['text' => '🌐 Change language'], ['text' => '🚀 Open LingoUz']]
            ],
            'resize_keyboard' => true,
            'is_persistent' => true,
        ];
    }

    return [
        'keyboard' => [
            [['text' => '📖 Bugungi so‘z'], ['text' => '🎯 Mini-test']],
            [['text' => '🔥 Streak holati'], ['text' => '📊 Mening hisobim']],
            [['text' => '📚 Ovozli ertaklar'], ['text' => '🎓 Diplom & Sertifikat']],
            [['text' => '🌐 Tilni almashtirish'], ['text' => '🚀 LingoUz platformasi']]
        ],
        'resize_keyboard' => true,
        'is_persistent' => true,
    ];
}

/**
 * Robust command matching supporting text buttons with emojis and slash commands.
 */
function telegram_match_command(string $text): string
{
    $t = mb_strtolower(trim($text), 'UTF-8');
    // Strip emojis for clean text matching
    $clean = preg_replace('/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1FA00}-\x{1FAFF}]/u', '', $t);
    $clean = trim($clean);

    if (str_starts_with($t, '/start')) return 'start';
    if (str_starts_with($t, '/word') || str_contains($clean, 'bugungi') || str_contains($clean, 'word of the day') || $clean === 'soz' || $clean === 'söz') return 'word';
    if (str_starts_with($t, '/quiz') || str_contains($clean, 'mini-test') || str_contains($clean, 'mini-quiz') || $clean === 'test' || $clean === 'quiz') return 'quiz';
    if (str_starts_with($t, '/streak') || str_contains($clean, 'streak')) return 'streak';
    if (str_starts_with($t, '/profile') || str_starts_with($t, '/stats') || str_contains($clean, 'mening hisobim') || str_contains($clean, 'my profile') || $clean === 'profil' || $clean === 'profile') return 'profile';
    if (str_starts_with($t, '/cert') || str_contains($clean, 'sertifikat') || str_contains($clean, 'diplom') || str_contains($clean, 'certificate')) return 'cert';
    if (str_starts_with($t, '/stories') || str_starts_with($t, '/story') || str_starts_with($t, '/ertak') || str_contains($clean, 'ertak') || str_contains($clean, 'stories')) return 'stories';
    if (str_starts_with($t, '/lang') || str_contains($clean, 'tilni') || str_contains($clean, 'language')) return 'lang';
    if (str_starts_with($t, '/site') || str_contains($clean, 'lingouz') || str_contains($clean, 'sayt') || str_contains($clean, 'open')) return 'site';
    if (str_starts_with($t, '/unlink')) return 'unlink';

    return 'unknown';
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
 * Generates the 1-click Telegram deep link.
 */
function get_telegram_link_url(array $user): string
{
    $token = get_or_create_telegram_link_token((int) $user['id']);
    $bot = telegram_bot_username();
    return "https://t.me/{$bot}?start=link_{$token}";
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
 * Main Webhook Handler for Telegram updates.
 */
function handle_telegram_webhook(array $update): void
{
    $pdo = db();

    // 1. Handle Inline Button Callback Queries (e.g. Language Switcher)
    if (!empty($update['callback_query'])) {
        $cb = $update['callback_query'];
        $cbId = (string) $cb['id'];
        $cbData = (string) ($cb['data'] ?? '');
        $chatId = $cb['message']['chat']['id'] ?? ($cb['from']['id'] ?? null);

        if (str_starts_with($cbData, 'set_lang:')) {
            $newLang = $cbData === 'set_lang:en' ? 'en' : 'uz';
            if ($chatId) {
                $pdo->prepare('UPDATE users SET telegram_lang = ? WHERE telegram_chat_id = ?')
                    ->execute([$newLang, (string) $chatId]);
            }

            if ($newLang === 'en') {
                telegram_answer_callback($cbId, "Language set to English! 🇬🇧");
                $msg = "✅ <b>Language successfully updated to English!</b>\n\nNow all daily words, quizzes, and streak reminders will be sent with English guides.";
                telegram_send_message($chatId, $msg, telegram_main_keyboard('en'), 'HTML', 'en');
            } else {
                telegram_answer_callback($cbId, "Til o‘zbekchaga o‘rnatildi! 🇺🇿");
                $msg = "✅ <b>Til muvaffaqiyatli O‘zbekchaga o‘rnatildi! 🇺🇿</b>\n\nEndi barcha xabarlar, kunlik so‘zlar va viktorinalar o‘zbek tilida keladi.";
                telegram_send_message($chatId, $msg, telegram_main_keyboard('uz'), 'HTML', 'uz');
            }
            return;
        }

        telegram_answer_callback($cbId);
        return;
    }

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
    $tgFirstName = $from['first_name'] ?? ($from['username'] ?? 'Learner');

    // Find linked user if exists
    $uStmt = $pdo->prepare('SELECT * FROM users WHERE telegram_chat_id = ? LIMIT 1');
    $uStmt->execute([(string) $chatId]);
    $user = $uStmt->fetch() ?: null;

    $lang = telegram_user_lang($user, $from);

    // 2. Deep linking: /start link_<token>
    if (preg_match('/^\/start\s+link_([a-f0-9]{24,64})/i', $text, $matches)) {
        $token = $matches[1];
        $stmt = $pdo->prepare('SELECT * FROM users WHERE telegram_link_token = ? LIMIT 1');
        $stmt->execute([$token]);
        $targetUser = $stmt->fetch();

        if ($targetUser) {
            $isFirstLink = empty($targetUser['telegram_chat_id']);
            $rewardXp = $isFirstLink ? 20 : 0;
            $rewardCoins = $isFirstLink ? 5 : 0;
            $userLang = telegram_user_lang($targetUser, $from);

            $up = $pdo->prepare('
                UPDATE users 
                SET telegram_chat_id = ?, 
                    telegram_username = ?, 
                    telegram_first_name = ?, 
                    telegram_linked_at = NOW(),
                    telegram_notifications = 1,
                    telegram_lang = ?,
                    xp = xp + ?, 
                    coins = coins + ? 
                WHERE id = ?
            ');
            $up->execute([
                (string) $chatId,
                $tgUsername ?: null,
                $tgFirstName ?: null,
                $userLang,
                $rewardXp,
                $rewardCoins,
                $targetUser['id']
            ]);

            // Refresh user row
            $targetUser['xp'] += $rewardXp;
            $targetUser['coins'] += $rewardCoins;

            // Generate Professional OG Profile Card
            $ogCardPath = generate_og_profile_image((int) $targetUser['id']);

            if ($userLang === 'en') {
                $caption = "Welcome, <b>" . e($targetUser['display_name']) . "</b>! 🎉\n\n" .
                    "Congratulations, your LingoUz learning account has been linked!\n\n";
                if ($isFirstLink) {
                    $caption .= "🎁 <b>Welcome Gift:</b> <b>+20 XP</b> and <b>5 coins</b> have been added to your profile!\n\n";
                }
                $caption .= "Every day you will receive:\n" .
                    "• 1 Word of the Day card with audio & translation\n" .
                    "• Interactive mini-quizzes\n" .
                    "• Streak protection alerts so you never lose your streak!\n\n" .
                    "Choose an option below to start exploring:";

                $keyboard = telegram_main_keyboard('en');
            } else {
                $caption = "Assalomu alaykum, <b>" . e($targetUser['display_name']) . "</b>! 🎉\n\n" .
                    "Tabriklaymiz, sizning LingoUz hisobingiz botga muvaffaqiyatli ulandi!\n\n";
                if ($isFirstLink) {
                    $caption .= "🎁 <b>Sovg‘a:</b> Hisobingizga <b>+20 XP</b> va <b>5 ta tanga</b> taqdim etildi!\n\n";
                }
                $caption .= "Endi siz:\n" .
                    "• Har kuni 1 ta yangi o‘zbekcha so‘z kartochkasi\n" .
                    "• Qiziqarli mini-viktorinalar\n" .
                    "• Streakingiz (ketma-ketlik) uzilib qolmasligi uchun eslatmalarni olasiz.\n\n" .
                    "Bilim olishni davom ettirish uchun pastdagi menyudan foydalaning! 👇";

                $keyboard = telegram_main_keyboard('uz');
            }

            telegram_send_photo($chatId, $ogCardPath, $caption, $keyboard, 'HTML', $userLang);
            return;
        } else {
            $failMsg = $lang === 'en'
                ? "Sorry, this invitation link is invalid or expired. Please visit your <a href=\"https://lingo.vexa.uz/dashboard\">LingoUz Dashboard</a> to get a new link."
                : "Kechirasiz, havola yaroqsiz yoki muddati o‘tgan. Iltimos, <a href=\"https://lingo.vexa.uz/dashboard\">LingoUz Boshqaruv paneliga</a> kirib, yangi havola orqali urinib ko‘ring.";
            telegram_send_message($chatId, $failMsg, null, 'HTML', $lang);
            return;
        }
    }

    $cmd = telegram_match_command($text);

    // 3. Command: /start (without token)
    if ($cmd === 'start') {
        if ($user) {
            $ogPath = generate_og_profile_image((int) $user['id']);
            if ($lang === 'en') {
                $caption = "Welcome back, <b>" . e($user['display_name']) . "</b>! 👋\n\n" .
                    "🔥 <b>Current Streak:</b> " . (int) $user['streak_days'] . " days\n" .
                    "⭐ <b>Total XP:</b> " . (int) $user['xp'] . " | 🪙 <b>Coins:</b> " . (int) $user['coins'] . "\n\n" .
                    "Choose an option below to continue learning Uzbek:";
            } else {
                $caption = "Qaytganingiz bilan, <b>" . e($user['display_name']) . "</b>! 👋\n\n" .
                    "🔥 <b>Ketma-ketlik (Streak):</b> " . (int) $user['streak_days'] . " kun\n" .
                    "⭐ <b>Jami XP:</b> " . (int) $user['xp'] . " | 🪙 <b>Tangalar:</b> " . (int) $user['coins'] . "\n\n" .
                    "Bugun nima o‘rganamiz? Quyidagi menyudan tanlang:";
            }
            telegram_send_message($chatId, $lang === 'en' ? "✨ Navigation buttons are active below 👇" : "✨ Asosiy menyu tugmalari quyida faollashtirildi 👇", telegram_main_keyboard($lang), 'HTML', $lang);
            telegram_send_photo($chatId, $ogPath, $caption, null, 'HTML', $lang);
        } else {
            if ($lang === 'en') {
                $caption = "Hello! 👋 Welcome to <b>LingoUz | Learn Uzbek Online</b>!\n\n" .
                    "I am Lingo — your smart companion for mastering the Uzbek language.\n\n" .
                    "🎯 <b>What you can do here:</b>\n" .
                    "• 📖 Learn 1 new Uzbek word every day with audio\n" .
                    "• 🎯 Solve interactive quizzes & polls\n" .
                    "• 🔥 Build a daily learning streak\n" .
                    "• 📚 Listen to Uzbek folk stories\n\n" .
                    "👉 <b>Connect your account</b> to claim <b>+20 XP</b> and <b>5 coins</b> bonus!\n\n" .
                    "Tap any button below to get started 👇";
                $btnText = "🌐 Connect Account (+20 XP Bonus) ↗";
            } else {
                $caption = "Assalomu alaykum! 👋 <b>LingoUz | O‘zbek Tili Akademiyasi</b> rasmiy botiga xush kelibsiz!\n\n" .
                    "Men Lingo — sizning o‘zbek tilini qiziqarli va professional o‘rganishdagi aqlli do‘stingizman.\n\n" .
                    "🎯 <b>Bot imkoniyatlari:</b>\n" .
                    "• 📖 Har kuni 1 ta yangi o‘zbekcha so‘z kartochkasi\n" .
                    "• 🎯 Interaktiv mini-viktorinalar\n" .
                    "• 🔥 Streak (ketma-ketlik) va kunlik eslatmalar\n" .
                    "• 📚 Ovozli xalq ertaklari va hikoyalar\n\n" .
                    "👉 Hisobingizni ulab, <b>+20 XP</b> va <b>5 ta tanga</b> sovg‘aga ega bo‘lish uchun quyidagi tugmani bosing!\n\n" .
                    "Quyidagi qulay tugmalardan foydalaning 👇";
                $btnText = "🌐 Saytdagi hisobni ulash (+20 XP) ↗";
            }

            $ogDefault = '/var/www/startups/lingo/public/og/default.png';
            $inlineKeyboard = [
                'inline_keyboard' => [
                    [['text' => $btnText, 'url' => 'https://lingo.vexa.uz/dashboard']],
                    [['text' => $lang === 'en' ? '🚀 Open LingoUz Platform' : '🚀 LingoUz Saytiga Kirish', 'url' => 'https://lingo.vexa.uz/dashboard']]
                ]
            ];

            // Always activate persistent reply keyboard
            telegram_send_message($chatId, $lang === 'en' ? "✨ Navigation buttons are active below 👇" : "✨ Asosiy menyu tugmalari quyida faollashtirildi 👇", telegram_main_keyboard($lang), 'HTML', $lang);

            if (file_exists($ogDefault)) {
                telegram_send_photo($chatId, $ogDefault, $caption, $inlineKeyboard, 'HTML', $lang);
            } else {
                telegram_send_message($chatId, $caption, $inlineKeyboard, 'HTML', $lang);
            }
        }
        return;
    }

    // 4. Command: Word of the Day / Bugungi so'z
    if ($cmd === 'word') {
        $word = telegram_today_word();
        if ($word) {
            $ogPath = generate_og_word_image((int) $word['id']);
            if ($lang === 'en') {
                $caption = "📖 <b>Word of the Day:</b>\n\n" .
                    "🇺🇿 <b>" . e($word['word_uz']) . "</b>" . ($word['pronunciation'] ? " [<i>" . e($word['pronunciation']) . "</i>]" : "") . "\n" .
                    "🇬🇧 Meaning: <b>" . e($word['word_en']) . "</b>\n\n";
                if (!empty($word['example'])) {
                    $caption .= "💡 <b>Example:</b>\n<i>«" . e($word['example']) . "»</i>\n\n";
                }
                $caption .= "🎧 Listen to audio pronunciation and practice cards on LingoUz:";
                $btnTrain = "🎴 Smart Flashcards Trainer";
                $btnCourses = "📚 All Uzbek Courses";
            } else {
                $caption = "📖 <b>Bugungi yangi so‘z:</b>\n\n" .
                    "🇺🇿 <b>" . e($word['word_uz']) . "</b>" . ($word['pronunciation'] ? " [<i>" . e($word['pronunciation']) . "</i>]" : "") . "\n" .
                    "🇬🇧 Tarjimasi: <b>" . e($word['word_en']) . "</b>\n\n";
                if (!empty($word['example'])) {
                    $caption .= "💡 <b>Misol:</b>\n<i>«" . e($word['example']) . "»</i>\n\n";
                }
                $caption .= "🎧 Ovozli talaffuz va barcha so‘zlarni ko‘rish uchun Flashcards trenajyoriga o‘ting:";
                $btnTrain = "🎴 Aqlli Flashcards trenajyori";
                $btnCourses = "📚 Barcha darslar";
            }

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => $btnTrain, 'url' => 'https://lingo.vexa.uz/flashcards']],
                    [['text' => $btnCourses, 'url' => 'https://lingo.vexa.uz/courses']],
                    [['text' => '📤 Share / Ulashish', 'url' => 'https://t.me/share/url?url=https://lingo.vexa.uz/flashcards&text=' . urlencode("Bugungi o‘zbekcha so‘z: " . $word['word_uz'] . " (" . $word['word_en'] . ")! LingoUz orqali o‘rganing!")]]
                ]
            ];
            telegram_send_photo($chatId, $ogPath, $caption, $keyboard, 'HTML', $lang);
        } else {
            telegram_send_message($chatId, $lang === 'en' ? "Vocabulary database is loading..." : "Lug‘at bazasi yangilanmoqda.", null, 'HTML', $lang);
        }
        return;
    }

    // 5. Command: Mini-quiz / Mini-test
    if ($cmd === 'quiz') {
        $quiz = telegram_random_quiz();
        if ($quiz) {
            $prompt = $quiz['prompt'];
            if ($lang === 'en' && !empty($quiz['prompt_en'])) {
                $prompt = $quiz['prompt_en'];
            }
            telegram_send_poll(
                $chatId,
                $prompt,
                $quiz['parsed_options'],
                (int) $quiz['correct_index'],
                (string) ($quiz['explanation'] ?? ($lang === 'en' ? 'Great job!' : 'To‘g‘ri javob!')),
                $lang
            );
        } else {
            telegram_send_message($chatId, $lang === 'en' ? "Quiz questions are being updated." : "Savollar bazasi yangilanmoqda.", null, 'HTML', $lang);
        }
        return;
    }

    // 6. Command: Streak status / Streak holati
    if ($cmd === 'streak') {
        if ($user) {
            $streak = (int) $user['streak_days'];
            $flames = str_repeat('🔥', max(1, min(7, $streak)));
            if ($lang === 'en') {
                $msg = "{$flames} <b>Your Current Streak: " . $streak . " days!</b>\n\n" .
                    ($streak > 0 
                        ? "Great dedication! Just 5 minutes of Uzbek practice each day builds real fluency. Keep your streak alive today!" 
                        : "You haven't started your streak yet. Complete one quick lesson today to ignite your 1-day flame badge!") . "\n\n" .
                    "👉 <a href=\"https://lingo.vexa.uz/dashboard\">Continue your lesson on LingoUz</a>";
                $btnText = "🚀 Practice & Boost Streak";
            } else {
                $msg = "{$flames} <b>Sizning ketma-ketligingiz: " . $streak . " kun!</b>\n\n" .
                    ($streak > 0 
                        ? "Ajoyib natija! Har kuni 5 daqiqa dars qilish orqali o‘zbek tilini mukammal o‘rgana olasiz. Ketma-ketlikni to‘xtatib qo‘ymang!" 
                        : "Siz hali streakni boshlamadingiz. Bugunoq bitta darsni yakunlab, 1 kunlik olovli yutuqqa ega bo‘ling!") . "\n\n" .
                    "👉 <a href=\"https://lingo.vexa.uz/dashboard\">Darsga o‘tish va streakni oshirish</a>";
                $btnText = "🚀 Darsni boshlash";
            }

            telegram_send_message($chatId, $msg, [
                'inline_keyboard' => [
                    [['text' => $btnText, 'url' => 'https://lingo.vexa.uz/dashboard']],
                    [['text' => '🏆 Leaderboard / Reyting', 'url' => 'https://lingo.vexa.uz/leaderboard']]
                ]
            ], 'HTML', $lang);
        } else {
            $msg = $lang === 'en'
                ? "⚠️ <b>Account not connected yet!</b>\n\nLink your Telegram account to start tracking your daily streak and earn rewards:\n\n👉 https://lingo.vexa.uz/dashboard"
                : "⚠️ <b>Hisobingiz hali ulanmagan!</b>\n\nStreak va yutuqlaringizni saqlab borish uchun saytdagi hisobingizni ulang:\n\n👉 https://lingo.vexa.uz/dashboard";
            telegram_send_message($chatId, $msg, [
                'inline_keyboard' => [
                    [['text' => $lang === 'en' ? '🌐 Connect Account (+20 XP)' : '🌐 Hisobni ulash (+20 XP)', 'url' => 'https://lingo.vexa.uz/dashboard']]
                ]
            ], 'HTML', $lang);
        }
        return;
    }

    // 7. Command: Profile / Mening hisobim
    if ($cmd === 'profile') {
        if ($user) {
            $ogPath = generate_og_profile_image((int) $user['id']);
            if ($lang === 'en') {
                $caption = "👤 <b>Learner Profile Card:</b>\n\n" .
                    "Name: <b>" . e($user['display_name']) . "</b>\n" .
                    "⭐ Total XP: <b>" . (int) $user['xp'] . "</b>\n" .
                    "🪙 Coins: <b>" . (int) $user['coins'] . "</b>\n" .
                    "🔥 Streak: <b>" . (int) $user['streak_days'] . " days</b>\n" .
                    "🎯 Level: <b>" . e($user['language_level'] ?: 'A0 Beginner') . "</b>\n\n" .
                    "View your detailed gradebook, certificates, and badges on LingoUz:";
                $btnDash = "🌐 Open My Dashboard";
                $btnLeader = "🏆 Global Leaderboard";
                $shareText = "Men LingoUz platformasida " . (int)$user['xp'] . " XP va " . (int)$user['streak_days'] . " kunlik streak to‘pladim! Siz ham o‘zbek tilini o‘rganing:";
            } else {
                $caption = "👤 <b>O‘quvchi profili:</b>\n\n" .
                    "Ism: <b>" . e($user['display_name']) . "</b>\n" .
                    "⭐ To‘plangan XP: <b>" . (int) $user['xp'] . "</b>\n" .
                    "🪙 Tangalar: <b>" . (int) $user['coins'] . "</b>\n" .
                    "🔥 Ketma-ketlik: <b>" . (int) $user['streak_days'] . " kun</b>\n" .
                    "🎯 Daraja: <b>" . e($user['language_level'] ?: 'A0 Boshlang‘ich') . "</b>\n\n" .
                    "Batafsil natijalar va yangi yutuqlar uchun shaxsiy kabinetingizga kiring:";
                $btnDash = "🌐 Shaxsiy kabinetga kirish";
                $btnLeader = "🏆 Reyting va Liga";
                $shareText = "Men LingoUz platformasida " . (int)$user['xp'] . " XP to‘pladim! Siz ham o‘zbek tilini o‘rganing:";
            }

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => $btnDash, 'url' => 'https://lingo.vexa.uz/dashboard']],
                    [['text' => $btnLeader, 'url' => 'https://lingo.vexa.uz/leaderboard']],
                    [['text' => $lang === 'en' ? '🎓 My Certificate' : '🎓 Mening sertifikatim', 'url' => 'https://lingo.vexa.uz/verify/LU-1-' . str_pad((string)$user['id'], 4, '0', STR_PAD_LEFT)]],
                    [['text' => '📤 Natijani ulashish (Share)', 'url' => 'https://t.me/share/url?url=https://lingo.vexa.uz/profile&text=' . urlencode($shareText)]]
                ]
            ];
            telegram_send_photo($chatId, $ogPath, $caption, $keyboard, 'HTML', $lang);
        } else {
            $msg = $lang === 'en'
                ? "⚠️ <b>Account not connected yet!</b>\n\nTo view your personalized profile card with XP and coins, please link your Telegram account:\n\n👉 https://lingo.vexa.uz/dashboard"
                : "⚠️ <b>Hisobingiz hali ulanmagan!</b>\n\nShaxsiy profil kartochkangiz va yutuqlaringizni ko‘rish uchun saytimizdagi hisobingizni ulang:\n\n👉 https://lingo.vexa.uz/dashboard";
            telegram_send_message($chatId, $msg, [
                'inline_keyboard' => [
                    [['text' => $lang === 'en' ? '🌐 Connect Account & Get +20 XP' : '🌐 Hisobni ulash (+20 XP)', 'url' => 'https://lingo.vexa.uz/dashboard']]
                ]
            ], 'HTML', $lang);
        }
        return;
    }

    // 8. Command: Certificate / Sertifikat
    if ($cmd === 'cert') {
        if ($user) {
            $certCode = 'LU-1-' . str_pad((string)$user['id'], 4, '0', STR_PAD_LEFT);
            $verifyLink = "https://lingo.vexa.uz/verify/" . $certCode;
            $fullCertLink = "https://lingo.vexa.uz/certificate?course=1";

            if ($lang === 'en') {
                $certMsg = "🎓 <b>LingoUz Academy Certificate:</b>\n\n" .
                    "Student: <b>" . e($user['display_name']) . "</b>\n" .
                    "Certificate ID: <code>" . $certCode . "</code>\n\n" .
                    "View and verify your official course completion certificate below:";
                $btnView = "🖨 View & Print Diploma";
                $btnVerify = "🔍 Official Verification Link";
            } else {
                $certMsg = "🎓 <b>LingoUz Akademiyasi Sertifikati:</b>\n\n" .
                    "O‘quvchi: <b>" . e($user['display_name']) . "</b>\n" .
                    "Sertifikat ID: <code>" . $certCode . "</code>\n\n" .
                    "Diplomingizni ko‘rish va haqiqiyligini tekshirish uchun quyidagi tugmalardan foydalaning:";
                $btnView = "🖨 Diplomni ko‘rish va chop etish";
                $btnVerify = "🔍 Rasmiy tekshirish havolasi";
            }

            telegram_send_message($chatId, $certMsg, [
                'inline_keyboard' => [
                    [['text' => $btnView, 'url' => $fullCertLink]],
                    [['text' => $btnVerify, 'url' => $verifyLink]]
                ]
            ], 'HTML', $lang);
        } else {
            $msg = $lang === 'en'
                ? "🎓 <b>LingoUz Official Certificate:</b>\n\nTo view or verify your diplomas, please link your learning account:\n\n👉 https://lingo.vexa.uz/dashboard"
                : "🎓 <b>LingoUz Rasmiy Sertifikatlari:</b>\n\nDiplomlaringizni ko‘rish uchun hisobingizni ulang:\n\n👉 https://lingo.vexa.uz/dashboard";
            telegram_send_message($chatId, $msg, [
                'inline_keyboard' => [
                    [['text' => $lang === 'en' ? '🌐 Connect Account' : '🌐 Hisobni ulash', 'url' => 'https://lingo.vexa.uz/dashboard']]
                ]
            ], 'HTML', $lang);
        }
        return;
    }

    // 9. Command: Stories / Ertaklar
    if ($cmd === 'stories') {
        if ($lang === 'en') {
            $storyMsg = "📚 <b>Interactive Uzbek Folk Stories & Audio Tales:</b>\n\n" .
                "Experience the rich culture of Uzbekistan through beloved folk tales with sentence-by-sentence audio, clickable glossary, and comprehension mini-quizzes:\n\n" .
                "1. <b>Zumrad and Qimmat</b> — Tale of kindness and good deeds\n" .
                "2. <b>The Kingdom of Susambil</b> — Fable of unity & friendship\n" .
                "3. <b>The Golden Watermelon</b> — Honesty & generous heart\n" .
                "4. <b>Afandi and the Scholar</b> — Humor & sharp wit\n\n" .
                "Select a story to read & listen:";
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '✨ Zumrad & Qimmat', 'url' => 'https://lingo.vexa.uz/stories/zumrad-qimmat']],
                    [['text' => '🦁 Susambil', 'url' => 'https://lingo.vexa.uz/stories/susambil']],
                    [['text' => '🍉 The Golden Watermelon', 'url' => 'https://lingo.vexa.uz/stories/oltin-tarvuz']],
                    [['text' => '🧙 Afandi & Scholar', 'url' => 'https://lingo.vexa.uz/stories/afandi-donishmand']],
                    [['text' => '📚 All Stories / Barcha Ertaklar', 'url' => 'https://lingo.vexa.uz/stories']]
                ]
            ];
        } else {
            $storyMsg = "📚 <b>Ovozli O‘zbek Xalq Ertaklari va Hikoyalar:</b>\n\n" .
                "O‘zbek xalqining boy madaniyati, donoligi va ibratli ertaklarini gapma-gap ovozli eshitib o‘rganing:\n\n" .
                "1. <b>Zumrad va Qimmat</b> — Mehnatsevarlik va ezgulik ertagi\n" .
                "2. <b>Susambil</b> — Do‘stlik va birdamlik ertagi\n" .
                "3. <b>Oltin Tarvuz</b> — Saxovat va halollik ertagi\n" .
                "4. <b>Afandi va Donishmand</b> — O‘tkir aql va latifa\n\n" .
                "O‘qish va tinglash uchun ertakni tanlang:";
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '✨ Zumrad va Qimmat', 'url' => 'https://lingo.vexa.uz/stories/zumrad-qimmat']],
                    [['text' => '🦁 Susambil', 'url' => 'https://lingo.vexa.uz/stories/susambil']],
                    [['text' => '🍉 Oltin Tarvuz', 'url' => 'https://lingo.vexa.uz/stories/oltin-tarvuz']],
                    [['text' => '🧙 Afandi va Donishmand', 'url' => 'https://lingo.vexa.uz/stories/afandi-donishmand']],
                    [['text' => '📚 Barcha Ertaklar Katalogi', 'url' => 'https://lingo.vexa.uz/stories']]
                ]
            ];
        }
        telegram_send_message($chatId, $storyMsg, $keyboard, 'HTML', $lang);
        return;
    }

    // 10. Command: Language Switcher / Tilni almashtirish
    if ($cmd === 'lang') {
        $msg = "🌐 <b>Tilni tanlang / Choose your language:</b>\n\n" .
            "Quyidagi tugmalardan birini bosing / Select an option below:";
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🇺🇿 O‘zbekcha', 'callback_data' => 'set_lang:uz'],
                    ['text' => '🇬🇧 English', 'callback_data' => 'set_lang:en']
                ]
            ]
        ];
        telegram_send_message($chatId, $msg, $keyboard, 'HTML', $lang);
        return;
    }

    // 11. Command: Open Site
    if ($cmd === 'site') {
        $msg = $lang === 'en'
            ? "Tap the button below to launch the LingoUz learning platform directly:\n\n👉 https://lingo.vexa.uz/dashboard"
            : "Platformaga tezkor o‘tish uchun quyidagi havolani bosing:\n\n👉 https://lingo.vexa.uz/dashboard";
        telegram_send_message($chatId, $msg, [
            'inline_keyboard' => [
                [['text' => $lang === 'en' ? '🚀 Launch LingoUz Web App' : '🚀 LingoUz Platformasini Ochish', 'url' => 'https://lingo.vexa.uz/dashboard']]
            ]
        ], 'HTML', $lang);
        return;
    }

    // 12. Command: /unlink
    if ($cmd === 'unlink') {
        $pdo->prepare('UPDATE users SET telegram_chat_id = NULL, telegram_username = NULL, telegram_linked_at = NULL WHERE telegram_chat_id = ?')
            ->execute([(string) $chatId]);
        $unlinkedMsg = $lang === 'en'
            ? "Your Telegram account has been unlinked from LingoUz. You can reconnect anytime from your Dashboard."
            : "Telegram hisobingiz LingoUz platformasidan uzildi. Qayta ulash uchun Boshqaruv paneliga kiring.";
        telegram_send_message($chatId, $unlinkedMsg, ['remove_keyboard' => true], 'HTML', $lang);
        return;
    }

    // Default Fallback Help Message
    if ($lang === 'en') {
        $help = "Hello! 👋 I am Lingo — your friendly Uzbek learning companion.\n\n" .
            "Available commands:\n" .
            "• 📖 <b>Word of the day</b> (/word)\n" .
            "• 🎯 <b>Mini-quiz</b> (/quiz)\n" .
            "• 🔥 <b>Streak status</b> (/streak)\n" .
            "• 📊 <b>My profile & stats</b> (/profile)\n" .
            "• 🌐 <b>Change language</b> (/lang)\n" .
            "• 🚀 <b>Open website</b> (/site)";
    } else {
        $help = "Assalomu alaykum! 👋 Men Lingo — sizning o‘zbek tili do‘stingiz va yordamchingizman.\n\n" .
            "Mavjud buyruqlar:\n" .
            "• 📖 <b>Bugungi so‘z</b> (/word)\n" .
            "• 🎯 <b>Mini-test</b> (/quiz)\n" .
            "• 🔥 <b>Streak holati</b> (/streak)\n" .
            "• 📊 <b>Mening hisobim</b> (/profile)\n" .
            "• 🌐 <b>Tilni almashtirish</b> (/lang)\n" .
            "• 🚀 <b>Saytga kirish</b> (/site)";
    }
    telegram_send_message($chatId, $help, telegram_main_keyboard($lang), 'HTML', $lang);
}

/**
 * Sends daily broadcast notifications in the subscriber's preferred language.
 */
function telegram_send_daily_broadcast(): array
{
    $pdo = db();
    $stmt = $pdo->query('
        SELECT id, display_name, streak_days, telegram_chat_id, xp, coins, telegram_lang 
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

        $lang = ($sub['telegram_lang'] ?? 'uz') === 'en' ? 'en' : 'uz';
        $streak = (int) $sub['streak_days'];

        if ($lang === 'en') {
            $streakText = $streak > 0 
                ? "🔥 <b>Streak Alert: {$streak} days!</b> Complete today's 5-minute lesson to keep your streak burning."
                : "🔥 Start your Uzbek learning streak today with just 1 quick lesson!";

            $msg = "🌅 <b>Good morning, " . e($sub['display_name']) . "!</b>\n\n" .
                $streakText . "\n\n";

            if ($word) {
                $msg .= "📖 <b>Word of the Day:</b>\n" .
                    "🇺🇿 <b>" . e($word['word_uz']) . "</b>" . ($word['pronunciation'] ? " [<i>" . e($word['pronunciation']) . "</i>]" : "") . " — " . e($word['word_en']) . "\n";
                if (!empty($word['example'])) {
                    $msg .= "💡 <i>«" . e($word['example']) . "»</i>\n";
                }
                $msg .= "\n";
            }
            $msg .= "Take today's mini-quiz below and continue your lesson! 👇";
            $btnLesson = "🚀 Continue Lesson";
            $btnTrain = "🎴 Flashcards Trainer";
        } else {
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
            $btnLesson = "🚀 Darsni davom ettirish";
            $btnTrain = "🎴 Flashcards trenajyori";
        }

        $keyboard = [
            'inline_keyboard' => [
                [['text' => $btnLesson, 'url' => 'https://lingo.vexa.uz/dashboard']],
                [['text' => $btnTrain, 'url' => 'https://lingo.vexa.uz/flashcards']]
            ]
        ];

        // Send with OG word card image if available
        if ($word) {
            $ogWordPath = generate_og_word_image((int) $word['id']);
            $res = telegram_send_photo($chatId, $ogWordPath, $msg, $keyboard, 'HTML', $lang);
        } else {
            $res = telegram_send_message($chatId, $msg, $keyboard, 'HTML', $lang);
        }

        if ($res && !empty($res['ok'])) {
            $sentCount++;
            // Send quiz poll
            if ($quiz) {
                $prompt = ($lang === 'en' && !empty($quiz['prompt_en'])) ? $quiz['prompt_en'] : $quiz['prompt'];
                telegram_send_poll(
                    $chatId,
                    $prompt,
                    $quiz['parsed_options'],
                    (int) $quiz['correct_index'],
                    (string) ($quiz['explanation'] ?? ''),
                    $lang
                );
            }
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

/**
 * Registers commands in Telegram so they appear in the bot's "/" menu.
 */
function telegram_set_commands(): array
{
    $uzCommands = [
        ['command' => 'start', 'description' => 'Asosiy menyu va hisobni ulash'],
        ['command' => 'word', 'description' => 'Bugungi yangi so‘z kartochkasi'],
        ['command' => 'quiz', 'description' => 'Interaktiv mini-viktorina'],
        ['command' => 'stories', 'description' => 'Ovozli o‘zbek xalq ertaklari'],
        ['command' => 'streak', 'description' => 'Streak (ketma-ketlik) holati'],
        ['command' => 'profile', 'description' => 'O‘quvchi profili va natijalar'],
        ['command' => 'cert', 'description' => 'Diplom va rasmiy sertifikat'],
        ['command' => 'lang', 'description' => 'Tilni tanlash (UZ/EN)'],
        ['command' => 'site', 'description' => 'LingoUz platformasini ochish'],
    ];

    $enCommands = [
        ['command' => 'start', 'description' => 'Main navigation menu & link account'],
        ['command' => 'word', 'description' => 'Word of the day with audio'],
        ['command' => 'quiz', 'description' => 'Interactive mini-quiz'],
        ['command' => 'stories', 'description' => 'Interactive Uzbek folk tales'],
        ['command' => 'streak', 'description' => 'Check current streak status'],
        ['command' => 'profile', 'description' => 'My learner profile & stats'],
        ['command' => 'cert', 'description' => 'Course completion certificate'],
        ['command' => 'lang', 'description' => 'Switch language (EN/UZ)'],
        ['command' => 'site', 'description' => 'Open LingoUz web platform'],
    ];

    telegram_api('setMyCommands', ['commands' => $uzCommands]);
    telegram_api('setMyCommands', ['commands' => $enCommands, 'language_code' => 'en']);
    telegram_api('setChatMenuButton', ['menu_button' => ['type' => 'commands']]);
    return ['ok' => true];
}

/**
 * Sets the official bot profile photo using Bot API 7.0+ setMyProfilePhoto.
 */
function telegram_set_bot_profile_photo(string $photoPath = '/var/www/startups/lingo/public/assets/icon-512.png'): ?array
{
    $token = telegram_bot_token();
    if ($token === '' || !file_exists($photoPath)) {
        return ['ok' => false, 'description' => 'Token or photo missing.'];
    }

    $boundary = '----WebKitFormBoundary' . bin2hex(random_bytes(16));
    $body = '';

    // input_profile_photo
    $inputPhoto = json_encode(['type' => 'static', 'photo' => 'attach://avatar_file']);
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Disposition: form-data; name=\"photo\"\r\n\r\n";
    $body .= $inputPhoto . "\r\n";

    // avatar_file
    $fileBytes = file_get_contents($photoPath);
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Disposition: form-data; name=\"avatar_file\"; filename=\"avatar.png\"\r\n";
    $body .= "Content-Type: image/png\r\n\r\n";
    $body .= $fileBytes . "\r\n";
    $body .= "--{$boundary}--\r\n";

    $url = "https://api.telegram.org/bot{$token}/setMyProfilePhoto";
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => ["Content-Type: multipart/form-data; boundary={$boundary}"],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $resp = curl_exec($ch);
    curl_close($ch);
    return json_decode((string)$resp, true);
}

/**
 * Updates full bot identity: name, photo, description, commands, menu button.
 */
function telegram_sync_full_bot_profile(): array
{
    // 1. Bot Name
    telegram_api('setMyName', ['name' => 'LingoUz | O‘zbek Tili Akademiyasi']);
    telegram_api('setMyName', ['name' => 'LingoUz | Learn Uzbek Online', 'language_code' => 'en']);

    // 2. Short Description
    telegram_api('setMyShortDescription', [
        'short_description' => 'LingoUz rasmiy boti: Kunlik o‘zbekcha yangi so‘zlar, viktorinalar va streak eslatmalari.'
    ]);
    telegram_api('setMyShortDescription', [
        'short_description' => 'Official LingoUz bot: Daily Uzbek words, interactive quizzes, and streak alerts.',
        'language_code' => 'en'
    ]);

    // 3. Full Description
    telegram_api('setMyDescription', [
        'description' => "LingoUz — O‘zbek tilini interaktiv, qiziqarli va professional tarzda o‘rganish platformasining rasmiy boti.\n\nBu yerda siz:\n• 📖 Har kuni 1 ta yangi o‘zbekcha so‘z kartochkasi\n• 🎯 Interaktiv mini-viktorinalar va savollar\n• 🔥 Streak (ketma-ketlik) va dars eslatmalari\n• 📚 Ovozli xalq ertaklari\n• 📊 O‘quvchi profili va sertifikatlar\n\nBoshlash uchun «Start» tugmasini bosing!"
    ]);
    telegram_api('setMyDescription', [
        'description' => "LingoUz — The official Telegram assistant for learning the Uzbek language online.\n\nHere you can:\n• 📖 Get 1 Word of the Day card with audio & translation\n• 🎯 Solve interactive mini-quizzes\n• 🔥 Keep your learning streak alive with daily reminders\n• 📚 Listen to Uzbek folk stories with audio\n• 📊 Track your XP, coins, and certificates\n\nTap «Start» to begin learning!",
        'language_code' => 'en'
    ]);

    // 4. Commands & Menu Button
    telegram_set_commands();

    // 5. Bot Profile Photo
    $photoRes = telegram_set_bot_profile_photo();

    return [
        'ok' => true,
        'photo' => $photoRes,
        'message' => 'Bot identity, name, photo, commands, and descriptions synced!'
    ];
}

// ============================================================================
// DEVELOPER & SYSTEM UPDATES GROUP INTEGRATION (-1003979299740)
// ============================================================================

/**
 * Sends a message or photo card to the developer/updates supergroup.
 */
function telegram_notify_dev(string $text, ?array $replyMarkup = null, ?string $photo = null): bool
{
    $devChatId = telegram_dev_chat_id();
    if ($devChatId === '') {
        return false;
    }

    try {
        if ($photo !== null && file_exists($photo)) {
            $res = telegram_send_photo($devChatId, $photo, $text, $replyMarkup);
        } else {
            $res = telegram_send_message($devChatId, $text, $replyMarkup);
        }
        return !empty($res['ok']);
    } catch (\Throwable $e) {
        telegram_log('[Telegram Dev Notify Error] ' . $e->getMessage());
        return false;
    }
}

/**
 * Broadcasts a platform release / code update changelog to the developer group.
 */
function telegram_notify_release(
    string $title,
    string $version = 'v1.2.0',
    array $changes = [],
    ?string $author = null,
    ?string $commitHash = null
): bool {
    $now = (new \DateTime('now', new \DateTimeZone('Asia/Tashkent')))->format('Y-m-d H:i');
    $author = $author ?: 'Antigravity / Vexa Team';

    $lines = [
        "🚀 <b>LingoUz tizim yangilanishi</b> ({$version})",
        "",
        "📌 <b>Mavzu:</b> " . htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        "🕒 <b>Vaqt:</b> {$now} (Toshkent)",
        "👤 <b>Muallif:</b> " . htmlspecialchars($author, ENT_QUOTES, 'UTF-8'),
    ];

    if ($commitHash) {
        $lines[] = "🔗 <b>Commit:</b> <code>" . htmlspecialchars(substr($commitHash, 0, 7), ENT_QUOTES, 'UTF-8') . "</code>";
    }

    if (!empty($changes)) {
        $lines[] = "";
        $lines[] = "✨ <b>Asosiy o‘zgarishlar va yangiliklar:</b>";
        foreach ($changes as $item) {
            $lines[] = "• " . htmlspecialchars((string) $item, ENT_QUOTES, 'UTF-8');
        }
    }

    $lines[] = "";
    $lines[] = "🌐 <b>Sayt:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a>";
    $lines[] = "🤖 <b>Bot:</b> <a href=\"https://t.me/vexa_lingo_bot\">@vexa_lingo_bot</a>";

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '🚀 Platformaga o‘tish', 'url' => 'https://lingo.vexa.uz'],
                ['text' => '🤖 Botni ochish', 'url' => 'https://t.me/' . telegram_bot_username()],
            ],
            [
                ['text' => '⚙️ Admin Panel', 'url' => 'https://lingo.vexa.uz/admin'],
                ['text' => '📊 Statistika', 'url' => 'https://lingo.vexa.uz/admin/stats'],
            ],
        ],
    ];

    return telegram_notify_dev(implode("\n", $lines), $keyboard);
}

/**
 * Sends instant alert when a new student signs up.
 */
function telegram_notify_new_user(array $user): bool
{
    $pdo = db();
    $totalCount = 0;
    try {
        $totalCount = (int) $pdo->query("SELECT count(*) FROM users WHERE role = 'student'")->fetchColumn();
    } catch (\Throwable $e) {}

    $now = (new \DateTime('now', new \DateTimeZone('Asia/Tashkent')))->format('Y-m-d H:i');
    $name = htmlspecialchars($user['display_name'] ?? 'Yangi o‘quvchi', ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($user['email'] ?? '—', ENT_QUOTES, 'UTF-8');
    $level = htmlspecialchars($user['language_level'] ?? 'A0', ENT_QUOTES, 'UTF-8');
    $age = htmlspecialchars($user['age_band'] ?? '7-8', ENT_QUOTES, 'UTF-8');
    $userId = (int) ($user['id'] ?? 0);

    $lines = [
        "🆕 <b>Yangi o‘quvchi ro‘yxatdan o‘tdi</b>",
        "",
        "👤 <b>Ism:</b> {$name}",
        "📧 <b>Email:</b> {$email}",
        "🎯 <b>Darajasi:</b> {$level} ({$age} yosh)",
        "👥 <b>Jami o‘quvchilar:</b> {$totalCount} kishi",
        "🕒 <b>Vaqt:</b> {$now}",
    ];

    $keyboard = null;
    if ($userId > 0) {
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '👤 O‘quvchi profili', 'url' => "https://lingo.vexa.uz/admin/students/{$userId}"],
                    ['text' => '👥 Barcha o‘quvchilar', 'url' => 'https://lingo.vexa.uz/admin/students'],
                ],
            ],
        ];
    }

    return telegram_notify_dev(implode("\n", $lines), $keyboard);
}

/**
 * Sends instant alert when a parent submits a course enrollment application.
 */
function telegram_notify_new_enrollment(array $enrollment): bool
{
    $now = (new \DateTime('now', new \DateTimeZone('Asia/Tashkent')))->format('Y-m-d H:i');
    $parent = htmlspecialchars($enrollment['parent_name'] ?? '—', ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($enrollment['parent_phone'] ?? '—', ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($enrollment['parent_email'] ?? '—', ENT_QUOTES, 'UTF-8');
    $child = htmlspecialchars($enrollment['child_name'] ?? '—', ENT_QUOTES, 'UTF-8');
    $age = htmlspecialchars($enrollment['child_age'] ?? '7-8', ENT_QUOTES, 'UTF-8');
    $level = htmlspecialchars($enrollment['language_level'] ?? 'A0', ENT_QUOTES, 'UTF-8');
    $notes = trim((string) ($enrollment['notes'] ?? ''));

    $lines = [
        "📝 <b>Yangi kursga ariza qabul qilindi</b>",
        "",
        "👨‍👩‍👧 <b>Ota-ona:</b> {$parent}",
        "📞 <b>Telefon:</b> {$phone}",
        "📧 <b>Email:</b> {$email}",
        "👶 <b>O‘quvchi bola:</b> {$child} ({$age} yosh)",
        "📊 <b>Tanlangan daraja:</b> {$level}",
    ];

    if ($notes !== '') {
        $lines[] = "💬 <b>Izoh:</b> <i>" . htmlspecialchars(mb_substr($notes, 0, 200), ENT_QUOTES, 'UTF-8') . "</i>";
    }

    $lines[] = "🕒 <b>Vaqt:</b> {$now}";

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '📋 Arizalarni ko‘rish', 'url' => 'https://lingo.vexa.uz/admin/enrollments'],
            ],
        ],
    ];

    return telegram_notify_dev(implode("\n", $lines), $keyboard);
}

/**
 * Sends notification when a student achieves a high quiz score or perfect test.
 */
function telegram_notify_quiz_milestone(array $user, array $lesson, int $score, int $total, int $percent, int $xp): bool
{
    // Only notify for exceptional achievements (100% perfect or passed final lessons)
    if ($percent < 100) {
        return false;
    }

    $now = (new \DateTime('now', new \DateTimeZone('Asia/Tashkent')))->format('Y-m-d H:i');
    $name = htmlspecialchars($user['display_name'] ?? 'O‘quvchi', ENT_QUOTES, 'UTF-8');
    $lessonTitle = htmlspecialchars($lesson['title'] ?? 'Dars', ENT_QUOTES, 'UTF-8');

    $lines = [
        "🌟 <b>Mukammal natija (100% Test topshirildi)</b>",
        "",
        "👤 <b>O‘quvchi:</b> {$name}",
        "📖 <b>Dars:</b> {$lessonTitle}",
        "🎯 <b>Natija:</b> {$score}/{$total} to‘g‘ri (100% A'lo)",
        "⚡ <b>Yutilgan ball:</b> +{$xp} XP 🎁",
        "🕒 <b>Vaqt:</b> {$now}",
    ];

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '🏆 Reytingni ko‘rish', 'url' => 'https://lingo.vexa.uz/leaderboard'],
            ],
        ],
    ];

    return telegram_notify_dev(implode("\n", $lines), $keyboard);
}

/**
 * Sends notification when a student completes a course and earns an official certificate.
 */
function telegram_notify_certificate(array $user, array $course, string $certCode, int $score): bool
{
    $now = (new \DateTime('now', new \DateTimeZone('Asia/Tashkent')))->format('Y-m-d H:i');
    $name = htmlspecialchars($user['display_name'] ?? 'O‘quvchi', ENT_QUOTES, 'UTF-8');
    $courseTitle = htmlspecialchars($course['title'] ?? 'O‘zbek tili', ENT_QUOTES, 'UTF-8');

    $lines = [
        "🎓 <b>Yangi Sertifikat / Diplom berildi!</b>",
        "",
        "👤 <b>O‘quvchi:</b> {$name}",
        "📚 <b>Tugallangan kurs:</b> {$courseTitle}",
        "📜 <b>Sertifikat kodi:</b> <code>{$certCode}</code>",
        "⭐ <b>Jami to‘plangan ball:</b> {$score} XP",
        "🕒 <b>Vaqt:</b> {$now}",
        "",
        "🎉 O‘quvchini katta muvaffaqiyat bilan tabriklaymiz!",
    ];

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '📜 Sertifikatni tekshirish', 'url' => "https://lingo.vexa.uz/certificate/{$certCode}"],
                ['text' => '🎓 Diplomlar ro‘yxati', 'url' => 'https://lingo.vexa.uz/admin/gradebook'],
            ],
        ],
    ];

    return telegram_notify_dev(implode("\n", $lines), $keyboard);
}

/**
 * Sends notification when a student reaches a streak milestone (e.g. 7, 14, 30 days).
 */
function telegram_notify_streak_milestone(array $user, int $streak): bool
{
    $now = (new \DateTime('now', new \DateTimeZone('Asia/Tashkent')))->format('Y-m-d H:i');
    $name = htmlspecialchars($user['display_name'] ?? 'O‘quvchi', ENT_QUOTES, 'UTF-8');

    $lines = [
        "🔥 <b>Streak rekord o‘rnatildi!</b>",
        "",
        "👤 <b>O‘quvchi:</b> {$name}",
        "⚡ <b>Ketma-ketlik:</b> {$streak} kun to‘xtovsiz dars!",
        "🕒 <b>Vaqt:</b> {$now}",
        "",
        "👏 Qoyilmaqom qat'iyat! Lingo bilan o‘rganish to‘xtamaydi.",
    ];

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '🏆 Peshqadamlar jadvali', 'url' => 'https://lingo.vexa.uz/leaderboard'],
            ],
        ],
    ];

    return telegram_notify_dev(implode("\n", $lines), $keyboard);
}

/**
 * Sends system or security alerts to developers.
 */
function telegram_notify_system_alert(string $title, string $details, string $level = 'info'): bool
{
    $now = (new \DateTime('now', new \DateTimeZone('Asia/Tashkent')))->format('Y-m-d H:i');
    $badge = match ($level) {
        'error', 'danger' => '🚨 <b>KRITIK XATO</b>',
        'warning' => '⚠️ <b>OGOHLANTIRISH</b>',
        default => 'ℹ️ <b>TIZIM BILDIRISHNOMASI</b>',
    };

    $lines = [
        $badge,
        "",
        "📌 <b>Mavzu:</b> " . htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        "📝 <b>Tafsilotlar:</b> " . htmlspecialchars($details, ENT_QUOTES, 'UTF-8'),
        "🕒 <b>Vaqt:</b> {$now}",
        "💻 <b>Server:</b> lingo.vexa.uz (PHP " . PHP_VERSION . ")",
    ];

    return telegram_notify_dev(implode("\n", $lines));
}

/**
 * Sends daily platform activity summary (morning or evening) to the dev group.
 */
function telegram_notify_dev_daily_summary(string $period = 'evening'): bool
{
    $pdo = db();
    $now = (new \DateTime('now', new \DateTimeZone('Asia/Tashkent')))->format('Y-m-d H:i');
    $todayDate = (new \DateTime('now', new \DateTimeZone('Asia/Tashkent')))->format('Y-m-d');

    $totalStudents = (int) $pdo->query("SELECT count(*) FROM users WHERE role = 'student'")->fetchColumn();
    $newStudentsToday = (int) $pdo->query("SELECT count(*) FROM users WHERE role = 'student' AND DATE(created_at) = '{$todayDate}'")->fetchColumn();
    $quizzesToday = (int) $pdo->query("SELECT count(*) FROM quiz_attempts WHERE DATE(created_at) = '{$todayDate}'")->fetchColumn();
    $tgLinked = (int) $pdo->query("SELECT count(*) FROM users WHERE telegram_chat_id IS NOT NULL")->fetchColumn();

    // Top 3 active learners
    $topUsers = $pdo->query("
        SELECT display_name, xp FROM users 
        WHERE role = 'student' 
        ORDER BY xp DESC LIMIT 3
    ")->fetchAll();

    $medals = ['🥇', '🥈', '🥉'];
    $topLines = [];
    foreach ($topUsers as $i => $u) {
        $topLines[] = ($medals[$i] ?? ($i + 1) . '.') . " " . htmlspecialchars($u['display_name'], ENT_QUOTES, 'UTF-8') . " — " . (int)$u['xp'] . " XP";
    }

    if ($period === 'morning') {
        $lines = [
            "☀️ <b>Xayrli tong! LingoUz Kunlik Brifing</b>",
            "",
            "📊 <b>Platforma ko‘rsatkichlari:</b>",
            "👥 Jami o‘quvchilar: <b>{$totalStudents}</b> kishi",
            "🤖 Telegram botga ulangan: <b>{$tgLinked}</b> kishi",
            "🕒 Sana: <b>{$todayDate}</b>",
            "",
            "🎯 Bugun barcha o‘quvchilarga kunlik so‘z va testlar o‘z vaqtida yetkaziladi.",
        ];
    } else {
        $lines = [
            "🌙 <b>LingoUz: Kun yakuniy hisoboti</b>",
            "",
            "📊 <b>Bugungi faollik ({$todayDate}):</b>",
            "👥 Jami o‘quvchilar: <b>{$totalStudents}</b> kishi",
            "🆕 Bugun qo‘shilganlar: <b>{$newStudentsToday}</b> kishi",
            "📝 Bajarilgan test va mashqlar: <b>{$quizzesToday}</b> ta",
            "🤖 Telegram faol o‘quvchilar: <b>{$tgLinked}</b> kishi",
        ];

        if (!empty($topLines)) {
            $lines[] = "";
            $lines[] = "🏆 <b>Eng yuqori natijador o‘quvchilar:</b>";
            foreach ($topLines as $tl) {
                $lines[] = $tl;
            }
        }
    }

    $lines[] = "";
    $lines[] = "🌐 <a href=\"https://lingo.vexa.uz/admin\">Admin boshqaruv paneliga o‘tish</a>";

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '📊 To‘liq statistika', 'url' => 'https://lingo.vexa.uz/admin/stats'],
                ['text' => '🏆 Reyting', 'url' => 'https://lingo.vexa.uz/leaderboard'],
            ],
        ],
    ];

    return telegram_notify_dev(implode("\n", $lines), $keyboard);
}
