<?php

declare(strict_types=1);

function seed_initial_data(PDO $pdo): void
{
    // 1. Initial Course
    $pdo->exec("
        INSERT OR IGNORE INTO courses (id, title, level, age_range, description, status)
        VALUES (1, 'Kichkintoylar uchun O‘zbek Tili (MVP)', 'A0-A1', '7–11 yosh', 'AQShdagi bolalar uchun interaktiv, o‘yinli boshlang‘ich o‘zbek tili kursi.', 'published')
    ");

    // 2. Default Badges
    $badges = [
        ['first_step', 'Birinchi qadam', '1-darsni muvaffaqiyatli yakunladingiz!', 'award'],
        ['smart_kid', 'Bilimdon bola', 'Dars testida 80% yoki undan yuqori natija ko‘rsatildi!', 'trophy'],
        ['halfway_hero', 'Uch qahramon', '3 ta darsni to‘liq o‘zlashtirdingiz!', 'sparkles'],
        ['speaker', 'Jonli Muloqotchi', 'Zoom jonli muloqot mashg‘ulotida qatnashdingiz!', 'video'],
        ['lingo_master', 'O‘zbek Tili Chempioni', 'Barcha 5 ta MVP darslarini a’lo darajada tugatdingiz!', 'registon'],
    ];
    $stmtBadge = $pdo->prepare('INSERT OR IGNORE INTO badges (code, title, description, icon) VALUES (?, ?, ?, ?)');
    foreach ($badges as $b) {
        $stmtBadge->execute($b);
    }

    // 3. Demo Users
    // Admin user: admin@lingo.uz / admin123
    // Student demo: ali@lingo.uz / ali123
    $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
    $studentHash = password_hash('ali123', PASSWORD_DEFAULT);

    $stmtUser = $pdo->prepare('
        INSERT OR IGNORE INTO users (id, role, display_name, email, password_hash, age_band, language_level, timezone, consent_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
    ');
    $stmtUser->execute([1, 'admin', 'Gulomov Admin', 'admin@lingo.uz', $adminHash, '11', 'A2', 'America/New_York']);
    $stmtUser->execute([2, 'student', 'Alijon', 'ali@lingo.uz', $studentHash, '9-10', 'A1', 'America/New_York']);

    // 4. 5 MVP Lessons
    $lessonsData = [
        [
            'seq' => 1,
            'title' => 'Salomlashish va O‘zini Tanishtirish',
            'title_en' => 'Greetings & Introductions',
            'objective' => 'Bolalar "Salom", "Xush kelibsiz", "Ismingiz nima?" va "Mening ismim..." iboralarini tushunib, gapira oladi.',
            'duration' => 5,
            'summary' => 'O‘zbek tilida do‘stlar va kattalar bilan salomlashishni, o‘z ismini aytib tanishtirishni o‘rganamiz.',
            'video_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            'vocab' => [
                ['Salom', 'Hello / Hi', 'Sah-lom', '👋', 'Salom, mening do‘stim!'],
                ['Assalomu alaykum', 'Peace be upon you (Formal Hello)', 'As-sah-loh-mu ah-lay-kum', '🤝', 'Assalomu alaykum, ustoz!'],
                ['Xayr', 'Goodbye', 'Khayr', '🙋‍♂️', 'Xayr, ko‘rishguncha!'],
                ['Rahmat', 'Thank you', 'Rakh-mat', '🙏', 'Katta rahmat!'],
                ['Ismingiz nima?', 'What is your name?', 'Is-min-giz nee-mah?', '❓', 'Salom, ismingiz nima?'],
                ['Mening ismim...', 'My name is...', 'Meh-ning is-mim', '🏷️', 'Mening ismim Ali.'],
            ],
            'quiz' => [
                [
                    'prompt' => 'Do‘stingiz bilan ko‘rishganda o‘zbek tilida qanday salomlashasiz?',
                    'prompt_en' => 'How do you say "Hello" in Uzbek to a friend?',
                    'options' => ['Salom', 'Xayr', 'Rahmat', 'Uyqu'],
                    'correct' => 0,
                    'explanation' => '"Salom" so‘zi inglizcha "Hello" yoki "Hi" ma’nosini bildiradi.'
                ],
                [
                    'prompt' => '"Thank you" so‘zining o‘zbekcha tarjimasi qaysi?',
                    'prompt_en' => 'What is the Uzbek word for "Thank you"?',
                    'options' => ['Kechirasiz', 'Rahmat', 'Salom', 'Xush kelibsiz'],
                    'correct' => 1,
                    'explanation' => '"Rahmat" minnatdorchilik bildirish uchun ishlatiladi.'
                ],
                [
                    'prompt' => 'Agar sizdan "Ismingiz nima?" deb so‘rashsa, qanday javob berasiz?',
                    'prompt_en' => 'If someone asks "What is your name?", how do you start your answer?',
                    'options' => ['Mening kitobim...', 'Mening ismim...', 'Mening maktabim...', 'Mening shahrim...'],
                    'correct' => 1,
                    'explanation' => 'O‘z ismingizni aytish uchun "Mening ismim [Ism]" deyiladi.'
                ],
                [
                    'prompt' => 'Kattalarga hurmat bilan salomlashish iborasi qaysi?',
                    'prompt_en' => 'Which greeting shows respect to elders and teachers?',
                    'options' => ['Assalomu alaykum', 'Xayr', 'Uxladingmi', 'Mayli'],
                    'correct' => 0,
                    'explanation' => '"Assalomu alaykum" eng odobli va hurmatli salomlashishdir.'
                ],
                [
                    'prompt' => 'Xayrlashayotganda nima deyiladi?',
                    'prompt_en' => 'What do you say when saying goodbye?',
                    'options' => ['Salom', 'Osh bo‘lsin', 'Xayr', 'Iltimos'],
                    'correct' => 2,
                    'explanation' => '"Xayr" yoki "Ko‘rishguncha xayr" deb vidolashiladi.'
                ],
            ]
        ],
        [
            'seq' => 2,
            'title' => 'Mening Ahil Oilam',
            'title_en' => 'My Loving Family',
            'objective' => 'Oila a’zolarini (ota, ona, aka, uka, opa, singil, buva, buvi) o‘zbek tilida to‘g‘ri aytish.',
            'duration' => 5,
            'summary' => 'Oilamizdagi eng aziz insonlar nomini o‘rganamiz va "Bu mening otam/onam" deb gap tuzamiz.',
            'video_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            'vocab' => [
                ['Ota / Dada', 'Father / Dad', 'Oh-tah / Dah-dah', '👨', 'Bu mening dadam.'],
                ['Ona / Oyi', 'Mother / Mom', 'Oh-nah / Oh-yee', '👩', 'Bu mening onam.'],
                ['Aka', 'Older brother', 'Ah-kah', '👦', 'Mening katta akam bor.'],
                ['Uka', 'Younger brother', 'Oo-kah', '🧒', 'Ukajonim juda sho‘x.'],
                ['Opa', 'Older sister', 'Oh-pah', '👧', 'Opam kitob o‘qiyapti.'],
                ['Singil', 'Younger sister', 'Sin-gil', '👶', 'Singlimga qo‘g‘irchoq sovg‘a qildim.'],
                ['Buva / Bobo', 'Grandfather', 'Boo-vah / Boh-boh', '👴', 'Buvam ertak aytib berdilar.'],
                ['Buvi', 'Grandmother', 'Boo-vee', '👵', 'Buvim shirin pishiriq pishirdilar.'],
            ],
            'quiz' => [
                [
                    'prompt' => 'O‘zbek tilida "Mother" nima deyiladi?',
                    'prompt_en' => 'What is "Mother" in Uzbek?',
                    'options' => ['Ona / Oyi', 'Aka', 'Opa', 'Buva'],
                    'correct' => 0,
                    'explanation' => '"Ona" yoki erkalab "Oyi", "Onajon" deyiladi.'
                ],
                [
                    'prompt' => 'O‘zingizdan katta o‘g‘il bolani qanday ataysiz?',
                    'prompt_en' => 'What do you call your older brother in Uzbek?',
                    'options' => ['Uka', 'Aka', 'Singil', 'Do‘st'],
                    'correct' => 1,
                    'explanation' => 'Katta o‘g‘il bola "Aka", kichik o‘g‘il bola esa "Uka" bo‘ladi.'
                ],
                [
                    'prompt' => '"Grandfather" so‘zining o‘zbekcha tarjimasi qaysi?',
                    'prompt_en' => 'What is the word for "Grandfather"?',
                    'options' => ['Buva / Bobo', 'Tog‘a', 'Amaki', 'Ota'],
                    'correct' => 0,
                    'explanation' => 'Otamiz yoki onamizning otasi "Buva" yoki "Bobo" deyiladi.'
                ],
                [
                    'prompt' => 'O‘zingizdan kichik qiz bolani nima deysiz?',
                    'prompt_en' => 'What do you call your younger sister?',
                    'options' => ['Opa', 'Singil', 'Ona', 'Xola'],
                    'correct' => 1,
                    'explanation' => 'Katta qiz "Opa", kichik qiz esa "Singil" bo‘ladi.'
                ],
                [
                    'prompt' => '"Bu mening dadam" jumlasining inglizcha ma’nosi nima?',
                    'prompt_en' => 'What does "Bu mening dadam" mean?',
                    'options' => ['This is my brother', 'This is my dad', 'This is my sister', 'This is my friend'],
                    'correct' => 1,
                    'explanation' => '"Bu mening dadam" = "This is my dad".'
                ],
            ]
        ],
        [
            'seq' => 3,
            'title' => 'Sehrli Raqamlar (1 dan 10 gacha)',
            'title_en' => 'Magic Numbers (1 to 10)',
            'objective' => '1 dan 10 gacha bo‘lgan sonlarni o‘zbek tilida sanash va buyumlar sonini aniqlash.',
            'duration' => 4,
            'summary' => 'Bir, ikki, uch... Barmoqlarimiz va o‘yinchoqlarimizni sanash orqali o‘zbekcha raqamlarni yod olamiz.',
            'video_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            'vocab' => [
                ['Bir', 'One (1)', 'Beer', '1️⃣', 'Bir dona olma.'],
                ['Ikki', 'Two (2)', 'Ik-kee', '2️⃣', 'Ikki ko‘zimiz bor.'],
                ['Uch', 'Three (3)', 'Ooch', '3️⃣', 'Uchta qush uchib keldi.'],
                ['To‘rt', 'Four (4)', 'Tort', '4️⃣', 'Stolning to‘rtta oyog‘i bor.'],
                ['Besh', 'Five (5)', 'Besh', '5️⃣', 'Qo‘limizda beshta barmoq.'],
                ['Olti', 'Six (6)', 'Ol-tee', '6️⃣', 'Oltita qalam.'],
                ['Yetti', 'Seven (7)', 'Yet-tee', '7️⃣', 'Haftada yetti kun bor.'],
                ['Sakkiz', 'Eight (8)', 'Sak-keez', '8️⃣', 'Sakkizoyoq (octopus).'],
                ['To‘qqiz', 'Nine (9)', 'Toq-qeez', '9️⃣', 'To‘qqizta sharcha.'],
                ['O‘n', 'Ten (10)', 'On', '🔟', 'O‘nta yulduzcha porladi.'],
            ],
            'quiz' => [
                [
                    'prompt' => 'O‘zbek tilida "5" raqami qanday aytiladi?',
                    'prompt_en' => 'How do you say the number 5 in Uzbek?',
                    'options' => ['Uch', 'Besh', 'Olti', 'Bir'],
                    'correct' => 1,
                    'explanation' => '1 - Bir, 2 - Ikki, 3 - Uch, 4 - To‘rt, 5 - BESH!'
                ],
                [
                    'prompt' => 'Haftada necha kun bor? (O‘zbekcha son)',
                    'prompt_en' => 'How many days are in a week? (In Uzbek)',
                    'options' => ['Besh', 'Yetti', 'O‘n', 'Ikki'],
                    'correct' => 1,
                    'explanation' => 'Haftada yetti (7) kun bor.'
                ],
                [
                    'prompt' => '"Ikki" va "Uch" qo‘shilsa necha bo‘ladi? (2 + 3 = ?)',
                    'prompt_en' => 'What is "Ikki" + "Uch"? (2 + 3 = ?)',
                    'options' => ['To‘rt', 'Besh', 'Olti', 'Sakkiz'],
                    'correct' => 1,
                    'explanation' => '2 (ikki) + 3 (uch) = 5 (besh).'
                ],
                [
                    'prompt' => '"Ten" raqami o‘zbek tilida nima?',
                    'prompt_en' => 'What is the Uzbek word for "Ten"?',
                    'options' => ['To‘qqiz', 'Sakkiz', 'O‘n', 'Bir'],
                    'correct' => 2,
                    'explanation' => '10 raqami "O‘n" deb aytiladi.'
                ],
                [
                    'prompt' => 'Ketma-ketlikni to‘ldiring: Bir, Ikki, Uch, ... ?',
                    'prompt_en' => 'Complete the sequence: Bir, Ikki, Uch, ... ?',
                    'options' => ['To‘rt', 'Olti', 'Yetti', 'O‘n'],
                    'correct' => 0,
                    'explanation' => 'Uchdan keyin "To‘rt" (4) keladi.'
                ],
            ]
        ],
        [
            'seq' => 4,
            'title' => 'Ranglar va Shakllar',
            'title_en' => 'Colors & Shapes',
            'objective' => 'Asosiy ranglar (qizil, ko‘k, yashil, sariq, oq, qora) nomlarini bilish.',
            'duration' => 5,
            'summary' => 'Atrofdagi tabiat va buyumlarning ranglarini o‘zbek tilida ifodalashni o‘rganamiz.',
            'video_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            'vocab' => [
                ['Qizil', 'Red', 'Qee-zil', '🔴', 'Qizil olma juda shirin.'],
                ['Ko‘k', 'Blue', 'Kok', '🔵', 'Musaffo ko‘k osmon.'],
                ['Yashil', 'Green', 'Yah-shil', '🟢', 'Yashil barglar va o‘tlar.'],
                ['Sariq', 'Yellow', 'Sah-riq', '🟡', 'Issiq sariq quyosh.'],
                ['Oq', 'White', 'Oq', '⚪', 'Oq qor yog‘moqda.'],
                ['Qora', 'Black', 'Qoh-rah', '⚫', 'Qora mushukcha.'],
            ],
            'quiz' => [
                [
                    'prompt' => 'Quyosh qanday rangda? (O‘zbek tilida)',
                    'prompt_en' => 'What color is the sun? (In Uzbek)',
                    'options' => ['Ko‘k', 'Sariq', 'Qora', 'Yashil'],
                    'correct' => 1,
                    'explanation' => 'Quyosh sariq rangda bo‘ladi.'
                ],
                [
                    'prompt' => '"Blue" so‘zining o‘zbekcha tarjimasi nima?',
                    'prompt_en' => 'What is "Blue" in Uzbek?',
                    'options' => ['Qizil', 'Ko‘k', 'Oq', 'Sariq'],
                    'correct' => 1,
                    'explanation' => 'Ko‘k = Blue.'
                ],
                [
                    'prompt' => 'Daraxt barglari va maysalar qaysi rangda?',
                    'prompt_en' => 'What color are grass and leaves?',
                    'options' => ['Yashil', 'Qora', 'Qizil', 'Oq'],
                    'correct' => 0,
                    'explanation' => 'Maysa va barglar yashil rangda.'
                ],
                [
                    'prompt' => 'Qor qanday rangda?',
                    'prompt_en' => 'What color is snow?',
                    'options' => ['Qora', 'Oq', 'Ko‘k', 'Sariq'],
                    'correct' => 1,
                    'explanation' => 'Qor oppoq, ya’ni "Oq" rangda.'
                ],
                [
                    'prompt' => '"Qizil sharcha" jumlasining ma’nosi nima?',
                    'prompt_en' => 'What does "Qizil sharcha" mean?',
                    'options' => ['Red balloon', 'Blue sky', 'Green tree', 'Yellow star'],
                    'correct' => 0,
                    'explanation' => 'Qizil = Red, sharcha = balloon.'
                ],
            ]
        ],
        [
            'seq' => 5,
            'title' => 'Mazzali Meva va Sabzavotlar',
            'title_en' => 'Delicious Fruits & Vegetables',
            'objective' => 'Sevimli meva va sabzavotlar nomini o‘zbekcha aytish va "Menga ... yoqadi" iborasini qo‘llash.',
            'duration' => 5,
            'summary' => 'O‘zbekistonning mashhur shirin mevalari (olma, anor, uzum, tarvuz) va foydali sabzavotlar bilan tanishamiz.',
            'video_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            'vocab' => [
                ['Olma', 'Apple', 'Ol-mah', '🍎', 'Qizil olma shirin.'],
                ['Anor', 'Pomegranate', 'Ah-nor', '🌺', 'O‘zbekiston anorlari juda mashhur.'],
                ['Uzum', 'Grapes', 'Oo-zoom', '🍇', 'Shirin uzum mevalari.'],
                ['Tarvuz', 'Watermelon', 'Tar-vooz', '🍉', 'Yozda sovuq tarvuz yeymiz.'],
                ['Qovun', 'Melon', 'Qoh-voon', '🍈', 'Xushbo‘y shirin qovun.'],
                ['Sabzi', 'Carrot', 'Sahb-zee', '🥕', 'Sabzidan osh tayyorlanadi.'],
                ['Pomidor', 'Tomato', 'Poh-mee-dor', '🍅', 'Qizil barra pomidor.'],
            ],
            'quiz' => [
                [
                    'prompt' => 'O‘zbekistonning ramziy mevalaridan biri bo‘lgan "Pomegranate" nima deyiladi?',
                    'prompt_en' => 'What is "Pomegranate" in Uzbek?',
                    'options' => ['Olma', 'Anor', 'Sabzi', 'Uzum'],
                    'correct' => 1,
                    'explanation' => 'Pomegranate o‘zbek tilida "Anor" deyiladi.'
                ],
                [
                    'prompt' => '"Apple" so‘zining o‘zbekcha tarjimasi qaysi?',
                    'prompt_en' => 'What is the Uzbek word for "Apple"?',
                    'options' => ['Olma', 'Tarvuz', 'Qovun', 'Pomidor'],
                    'correct' => 0,
                    'explanation' => '"Apple" = "Olma".'
                ],
                [
                    'prompt' => 'Palov (Osh) tayyorlashda ishlatiladigan sabzavot qaysi?',
                    'prompt_en' => 'Which vegetable is a key ingredient in Uzbek Plov?',
                    'options' => ['Sabzi', 'Uzum', 'Olma', 'Banan'],
                    'correct' => 0,
                    'explanation' => 'Palovning asosiy boyligi bu shirin "Sabzi"dir.'
                ],
                [
                    'prompt' => 'Yozda sovuq qilib kesib yeyiladigan katta meva nima?',
                    'prompt_en' => 'Which large fruit is eaten cold in summer?',
                    'options' => ['Tarvuz', 'Kartoshka', 'Piyoz', 'Non'],
                    'correct' => 0,
                    'explanation' => 'Tarvuz (Watermelon) yozda rohat baxsh etadi.'
                ],
                [
                    'prompt' => '"Menga olma yoqadi" jumlasining ma’nosi nima?',
                    'prompt_en' => 'What does "Menga olma yoqadi" mean?',
                    'options' => ['I like apples', 'I have an apple', 'Give me an apple', 'Apples are red'],
                    'correct' => 0,
                    'explanation' => '"Menga ... yoqadi" = "I like ...".'
                ],
            ]
        ],
    ];

    $stmtLesson = $pdo->prepare('
        INSERT OR REPLACE INTO lessons (id, sequence_number, title, title_en, objective, duration_min, video_url, summary, prerequisite_lesson_id, passing_score, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 80, "published")
    ');
    $stmtVocab = $pdo->prepare('
        INSERT INTO vocabulary (lesson_id, word_uz, word_en, pronunciation, icon, example)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $stmtQuiz = $pdo->prepare('
        INSERT OR REPLACE INTO quizzes (id, lesson_id, title, pass_score)
        VALUES (?, ?, ?, 80)
    ');
    $stmtQues = $pdo->prepare('
        INSERT INTO questions (quiz_id, prompt, prompt_en, options_json, correct_index, explanation, order_num)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');

    foreach ($lessonsData as $i => $l) {
        $lessonId = $i + 1;
        $prereq = ($lessonId === 1) ? null : ($lessonId - 1);
        $stmtLesson->execute([
            $lessonId,
            $l['seq'],
            $l['title'],
            $l['title_en'],
            $l['objective'],
            $l['duration'],
            $l['video_url'],
            $l['summary'],
            $prereq,
        ]);

        // Vocabulary
        foreach ($l['vocab'] as $v) {
            $stmtVocab->execute([$lessonId, $v[0], $v[1], $v[2], $v[3], $v[4]]);
        }

        // Quiz
        $quizId = $lessonId;
        $stmtQuiz->execute([$quizId, $lessonId, $l['title'] . ' — Yakuniy Test']);

        // Questions
        foreach ($l['quiz'] as $qIdx => $q) {
            $stmtQues->execute([
                $quizId,
                $q['prompt'],
                $q['prompt_en'],
                json_encode($q['options'], JSON_UNESCAPED_UNICODE),
                $q['correct'],
                $q['explanation'],
                $qIdx + 1,
            ]);
        }
    }

    // 5. Initial Lesson Progress for Ali (Student Demo)
    // Lesson 1 is available by default
    $pdo->exec("
        INSERT OR IGNORE INTO lesson_progress (user_id, lesson_id, status, best_score)
        VALUES (2, 1, 'available', 0)
    ");
    for ($lid = 2; $lid <= 5; $lid++) {
        $pdo->exec("
            INSERT OR IGNORE INTO lesson_progress (user_id, lesson_id, status, best_score)
            VALUES (2, {$lid}, 'locked', 0)
        ");
    }

    // 6. Live Sessions (Zoom Speaking Room)
    $pdo->exec("
        INSERT OR IGNORE INTO live_sessions (id, title, description, starts_at, timezone, zoom_url, mentor_name, status)
        VALUES (1, 'Haftalik Jonli Muloqot: Do‘stlar va Oila', 'O‘tilgan mavzularni birga suhbatlashib mustahkamlaymiz. O‘yinlar va erkin muloqot.', datetime('now', '+3 days', '11:00'), 'America/New_York (EST)', 'https://zoom.us/j/998877665544', 'Gulomov Ustoz', 'scheduled')
    ");
}
