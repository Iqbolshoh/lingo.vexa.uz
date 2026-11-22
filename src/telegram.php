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
    if ($replyMarkup !== null) {
        $params['reply_markup'] = is_string($replyMarkup) ? $replyMarkup : json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
    }

    if (file_exists($photoPathOrUrl)) {
        $params['photo'] = new CURLFile($photoPathOrUrl);
        return telegram_api('sendPhoto', $params, true);
    } else {
        $params['photo'] = $photoPathOrUrl;
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
                [['text' => '🌐 Change language (UZ/EN)'], ['text' => '🚀 Open LingoUz']]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
        ];
    }

    return [
        'keyboard' => [
            [['text' => '📖 Bugungi söz'], ['text' => '🎯 Mini-test']],
            [['text' => '🔥 Streak holati'], ['text' => '📊 Mening hisobim']],
            [['text' => '🌐 Tilni almaştiriş (UZ/EN)'], ['text' => '🚀 LingoUz Sayti']]
        ],
        'resize_keyboard' => true,
        'one_time_keyboard' => false,
    ];
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
                $msg = "✅ <b>Til muvaffaqiyatli O‘zbekchaga (Yangi lotin) o‘rnatildi!</b>\n\nEndi barcha xabarlar, kunlik so‘zlar va viktorinalar yangi alifboda keladi.";
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

    // 3. Command: /start (without token)
    if ($text === '/start') {
        if ($user) {
            $ogPath = generate_og_profile_image((int) $user['id']);
            if ($lang === 'en') {
                $caption = "Welcome back, <b>" . e($user['display_name']) . "</b>! 👋\n\n" .
                    "🔥 <b>Current Streak:</b> " . (int) $user['streak_days'] . " days\n" .
                    "⭐ <b>Total XP:</b> " . (int) $user['xp'] . " | 🪙 <b>Coins:</b> " . (int) $user['coins'] . "\n\n" .
                    "Ready for today's 5-minute Uzbek lesson?";
            } else {
                $caption = "Qaytganingiz bilan, <b>" . e($user['display_name']) . "</b>! 👋\n\n" .
                    "🔥 <b>Ketma-ketlik (Streak):</b> " . (int) $user['streak_days'] . " kun\n" .
                    "⭐ <b>Jami XP:</b> " . (int) $user['xp'] . " | 🪙 <b>Tangalar:</b> " . (int) $user['coins'] . "\n\n" .
                    "Bugun nima o‘rganamiz? Quyidagi menyudan tanlang:";
            }
            telegram_send_photo($chatId, $ogPath, $caption, telegram_main_keyboard($lang), 'HTML', $lang);
        } else {
            if ($lang === 'en') {
                $msg = "Hello! 👋 I am the official AI assistant of the <b>LingoUz</b> platform.\n\n" .
                    "To link your account, receive daily words, interactive quizzes, and streak alerts, log in to your dashboard and click <b>«Connect Telegram Bot»</b>:\n\n" .
                    "👉 https://lingo.vexa.uz/dashboard\n\n" .
                    "You will receive <b>+20 XP</b> and <b>5 coins</b> bonus upon linking! 🎁";
                $btnText = "🌐 Open Website & Link Account";
            } else {
                $msg = "Assalomu alaykum! 👋 Men LingoUz platformasining rasmiy botiman.\n\n" .
                    "Hisobingizni ulash, kunlik so‘zlar va streak eslatmalarini qabul qilish uchun saytimizdagi Boshqaruv paneliga kiring va <b>«Telegram botni ulash»</b> tugmasini bosing:\n\n" .
                    "👉 https://lingo.vexa.uz/dashboard\n\n" .
                    "Hisobingizni ulagan zahotingiz sizga <b>+20 XP</b> va <b>5 ta tanga</b> sovg‘a qilinadi! 🎁";
                $btnText = "🌐 Saytga o‘tish va hisobni ulash";
            }

            telegram_send_message($chatId, $msg, [
                'inline_keyboard' => [
                    [['text' => $btnText, 'url' => 'https://lingo.vexa.uz/dashboard']]
                ]
            ], 'HTML', $lang);
        }
        return;
    }

    // 4. Command: Word of the Day / Bugungi so'z
    if (in_array($text, ['📖 Bugungi söz', '📖 Bugungi so‘z', '📖 Word of the day', '/word'], true)) {
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
    if (in_array($text, ['🎯 Mini-test', '🎯 Mini-quiz', '/quiz'], true)) {
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
    if (in_array($text, ['🔥 Streak holati', '🔥 Streak status', '/streak'], true)) {
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
            telegram_send_message($chatId, $lang === 'en' ? "Account not linked. Please connect via: https://lingo.vexa.uz/dashboard" : "Hisobingiz ulanmagan: https://lingo.vexa.uz/dashboard", null, 'HTML', $lang);
        }
        return;
    }

    // 7. Command: Profile / Mening hisobim
    if (in_array($text, ['📊 Mening hisobim', '📊 My profile', '/profile', '/stats'], true)) {
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
            telegram_send_message($chatId, $lang === 'en' ? "Please link your account at https://lingo.vexa.uz/dashboard" : "Iltimos, hisobingizni ulang: https://lingo.vexa.uz/dashboard", null, 'HTML', $lang);
        }
        return;
    }

    // Command: Certificate / Sertifikat
    if (in_array($text, ['🎓 Sertifikat', '🎓 Certificate', '/cert', '/certificate'], true)) {
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
            telegram_send_message($chatId, $lang === 'en' ? "Please link your account at https://lingo.vexa.uz/dashboard" : "Hisobingiz ulanmagan: https://lingo.vexa.uz/dashboard", null, 'HTML', $lang);
        }
        return;
    }

    // Command: Stories / Ertaklar
    if (in_array($text, ['📖 Ertaklar', '📖 Stories', '/stories', '/story', '/ertak'], true)) {
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

    // 8. Command: Language Switcher / Tilni almashtirish
    if (in_array($text, ['🌐 Tilni almaştiriş (UZ/EN)', '🌐 Change language (UZ/EN)', '/lang', '/language'], true)) {
        $msg = "🌐 <b>Tilni tanlang / Choose your language:</b>\n\n" .
            "Quyidagi tugmalardan birini bosing / Select an option below:";
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🇺🇿 O‘zbekcha (Yangi lotin)', 'callback_data' => 'set_lang:uz'],
                    ['text' => '🇬🇧 English', 'callback_data' => 'set_lang:en']
                ]
            ]
        ];
        telegram_send_message($chatId, $msg, $keyboard, 'HTML', $lang);
        return;
    }

    // 9. Command: Open Site
    if (in_array($text, ['🚀 LingoUz Sayti', '🚀 Open LingoUz', '/site'], true)) {
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

    // 10. Command: /unlink
    if ($text === '/unlink') {
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
        ['command' => 'start', 'description' => 'Botni ishga tushirish va hisobni ulash'],
        ['command' => 'word', 'description' => 'Bugungi yangi so‘z kartochkasi'],
        ['command' => 'quiz', 'description' => 'Interaktiv mini-viktorina'],
        ['command' => 'stories', 'description' => 'Ovozli o‘zbek xalq ertaklari'],
        ['command' => 'streak', 'description' => 'Streak (ketma-ketlik) holati'],
        ['command' => 'profile', 'description' => 'O‘quvchi profili va natijalar'],
        ['command' => 'cert', 'description' => 'Diplom va rasmiy sertifikat'],
        ['command' => 'lang', 'description' => 'Tilni o‘zgartirish (UZ/EN)'],
        ['command' => 'site', 'description' => 'LingoUz veb-saytiga o‘tish'],
    ];

    $enCommands = [
        ['command' => 'start', 'description' => 'Start bot & link account'],
        ['command' => 'word', 'description' => 'Word of the day with audio'],
        ['command' => 'quiz', 'description' => 'Interactive mini-quiz'],
        ['command' => 'stories', 'description' => 'Interactive Uzbek folk tales'],
        ['command' => 'streak', 'description' => 'Check current streak status'],
        ['command' => 'profile', 'description' => 'My learner profile & stats'],
        ['command' => 'cert', 'description' => 'Course completion certificate'],
        ['command' => 'lang', 'description' => 'Switch language (EN/UZ)'],
        ['command' => 'site', 'description' => 'Open LingoUz platform'],
    ];

    telegram_api('setMyCommands', ['commands' => $uzCommands]);
    telegram_api('setMyCommands', ['commands' => $enCommands, 'language_code' => 'en']);
    return ['ok' => true];
}
