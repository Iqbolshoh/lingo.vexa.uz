<?php

declare(strict_types=1);

/**
 * Schema v2 content: course catalogue metadata, reading texts and culture notes
 * for the starter course, Englify-style exercises (listen / build / fill) and the
 * "Ikki madaniyat ko‘prigi" culture course (food, holidays, cities, manners).
 *
 * Idempotent: every insert is guarded, so re-running never duplicates rows.
 */
function seed_v2_content(PDO $pdo): void
{
    if ((int) $pdo->query('SELECT count(*) FROM courses')->fetchColumn() === 0) {
        require_once __DIR__ . '/content.php';
        seed_initial_data($pdo);
    }

    $pdo->exec("
        UPDATE courses SET
            title = 'O‘zbek tili: Ilk qadamlar',
            subtitle = 'Salomlashish, oila, raqamlar, ranglar va mevalar',
            category = 'language',
            icon = 'book-open',
            theme = 'lapis',
            sort_order = 1,
            level = 'A0–A1',
            description = 'AQShda ulg‘ayayotgan bolalar uchun interaktiv, o‘yinli boshlang‘ich o‘zbek tili kursi. Har dars: mikro-sahna, lug‘at, o‘qish, o‘yin va test.'
        WHERE id = 1
    ");

    $starter = [
        1 => [
            'icon' => 'message-square',
            'reading_uz' => "Salom! Mening ismim Ali.\nMen Nyu-Yorkda yashayman.\nBu mening do‘stim Laylo.\nLaylo: «Assalomu alaykum, ustoz!»\nUstoz: «Va alaykum assalom! Xush kelibsiz!»",
            'reading_en' => "Hello! My name is Ali.\nI live in New York.\nThis is my friend Laylo.\nLaylo: \"Peace be upon you, teacher!\"\nTeacher: \"And peace upon you! Welcome!\"",
            'culture_title' => 'Qo‘l ko‘ksida salom',
            'culture_note' => 'O‘zbekistonda kattalar bilan salomlashganda o‘ng qo‘l ko‘krakka qo‘yiladi va biroz egiladi. Bu — hurmat belgisi. Amerikada esa ko‘pincha qo‘l siqib yoki «High five» qilib salomlashiladi.',
            'extra' => [
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'Rahmat', 'options' => ['Thank you', 'Hello', 'Goodbye', 'Welcome'], 'correct' => 0, 'explanation' => '«Rahmat» — «Thank you».'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'My name is Ali.', 'answer_text' => 'Mening ismim Ali', 'options' => ['Salom', 'sizning', 'Xayr'], 'explanation' => '«Mening ismim Ali» — «My name is Ali».'],
            ],
        ],
        2 => [
            'icon' => 'users',
            'reading_uz' => "Bu mening oilam.\nDadam shifokor, oyim o‘qituvchi.\nMening akam va singlim bor.\nYakshanba kuni buvimnikiga boramiz.\nBuvim bizga choy quyib beradi.",
            'reading_en' => "This is my family.\nMy dad is a doctor, my mom is a teacher.\nI have an older brother and a younger sister.\nOn Sunday we go to grandma's house.\nGrandma pours tea for us.",
            'culture_title' => 'Katta oila — katta baxt',
            'culture_note' => 'O‘zbek oilalarida bobo-buvilar ko‘pincha nevaralari bilan birga yoki juda yaqin yashaydi. Har bir yakshanba — butun oila bir dasturxon atrofida yig‘iladigan kun.',
            'extra' => [
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'Buvi', 'options' => ['Grandfather', 'Grandmother', 'Sister', 'Mother'], 'correct' => 1, 'explanation' => '«Buvi» — «Grandmother».'],
                ['type' => 'fill', 'prompt' => 'Bo‘sh joyni to‘ldiring: «Mening ___ shifokor» (dad)', 'prompt_en' => 'Fill in the blank: "My ___ is a doctor" (dad)', 'answer_text' => 'dadam', 'explanation' => '«Dadam» — «my dad». Javob: «Mening dadam shifokor».'],
            ],
        ],
        3 => [
            'icon' => 'hash',
            'reading_uz' => "Bozorda uchta olma oldim.\nAkamga ikkita, singlimga bitta berdim.\nMen yetti yoshdaman.\nSinfimizda o‘n bitta bola bor.",
            'reading_en' => "I bought three apples at the bazaar.\nI gave two to my brother and one to my sister.\nI am seven years old.\nThere are eleven kids in our class.",
            'culture_title' => 'Bozorda sanash',
            'culture_note' => 'O‘zbek bozorlarida sotuvchilar mevani ko‘pincha donalab emas, kilogrammlab sotadi va xaridorga «bonus» qilib bitta olma qo‘shib beradi. Buni «barakasi» deyishadi.',
            'extra' => [
                ['type' => 'listen', 'prompt' => 'Tinglang va raqamni toping', 'prompt_en' => 'Listen and pick the number', 'audio_text' => 'Sakkiz', 'options' => ['6', '8', '3', '10'], 'correct' => 1, 'explanation' => '«Sakkiz» — 8.'],
                ['type' => 'fill', 'prompt' => 'Raqamni so‘z bilan yozing: 5', 'prompt_en' => 'Write the number as a word: 5', 'answer_text' => 'besh', 'explanation' => '5 — «besh».'],
            ],
        ],
        4 => [
            'icon' => 'palette',
            'reading_uz' => "Mening sharim qizil.\nOsmon ko‘k, maysa yashil.\nQuyosh sariq va issiq.\nMushugim oq, kuchugim qora.",
            'reading_en' => "My balloon is red.\nThe sky is blue, the grass is green.\nThe sun is yellow and warm.\nMy cat is white, my puppy is black.",
            'culture_title' => 'Ko‘k gumbazlar',
            'culture_note' => 'Samarqanddagi Registon va Go‘ri Amir maqbarasining gumbazlari ko‘k-feruza rangda. Ustalar bu rangni osmon va tinchlik ramzi deb bilgan.',
            'extra' => [
                ['type' => 'listen', 'prompt' => 'Tinglang va rangni toping', 'prompt_en' => 'Listen and pick the colour', 'audio_text' => 'Yashil', 'options' => ['Red', 'Yellow', 'Green', 'Black'], 'correct' => 2, 'explanation' => '«Yashil» — «Green».'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'The sky is blue.', 'answer_text' => 'Osmon ko‘k', 'options' => ['qizil', 'maysa', 'sariq'], 'explanation' => '«Osmon ko‘k» — «The sky is blue».'],
            ],
        ],
        5 => [
            'icon' => 'apple',
            'reading_uz' => "Yozda O‘zbekistonda qovun va tarvuz juda shirin bo‘ladi.\nBuvim bog‘dan uzum va anor uzadi.\nMen olmani yaxshi ko‘raman.\nOyim sabzi va pomidordan salat qiladi.",
            'reading_en' => "In summer, melons and watermelons in Uzbekistan are very sweet.\nGrandma picks grapes and pomegranates from the garden.\nI love apples.\nMom makes a salad from carrots and tomatoes.",
            'culture_title' => 'Qovunlar vatani',
            'culture_note' => 'O‘zbekistonda qovunning 150 dan ortiq navi bor! Mirzacho‘l qovuni shu qadar shirinki, qadimda uni Bag‘dod xalifalariga sovg‘a qilib yuborishgan.',
            'extra' => [
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'Anor', 'options' => ['Pomegranate', 'Grapes', 'Apple', 'Carrot'], 'correct' => 0, 'explanation' => '«Anor» — «Pomegranate».'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'I love apples.', 'answer_text' => 'Men olmani yaxshi ko‘raman', 'options' => ['uzum', 'sen', 'qovun'], 'explanation' => '«Men olmani yaxshi ko‘raman» — «I love apples».'],
            ],
        ],
    ];

    $updLesson = $pdo->prepare('
        UPDATE lessons SET icon = ?, reading_uz = ?, reading_en = ?, culture_title = ?, culture_note = ?
        WHERE id = ? AND (reading_uz IS NULL OR reading_uz = \'\')
    ');
    foreach ($starter as $lessonId => $data) {
        $updLesson->execute([$data['icon'], $data['reading_uz'], $data['reading_en'], $data['culture_title'], $data['culture_note'], $lessonId]);
        $quizId = (int) $pdo->query("SELECT id FROM quizzes WHERE lesson_id = {$lessonId}")->fetchColumn();
        if ($quizId > 0) {
            add_v2_questions($pdo, $quizId, $data['extra']);
        }
    }

    $badge = $pdo->prepare('INSERT OR IGNORE INTO badges (code, title, description, icon) VALUES (?, ?, ?, ?)');
    foreach ([
        ['culture_explorer', 'Madaniyat sayyohi', '«Ikki madaniyat ko‘prigi» kursidan birinchi darsni o‘zlashtirdingiz!', 'map-pin'],
        ['streak_3', 'Uch kunlik olov', '3 kun ketma-ket dars qildingiz!', 'flame'],
        ['xp_500', '500 XP yulduzi', '500 tajriba ochkosi (XP) to‘pladingiz!', 'star'],
        ['perfect_score', 'Mukammal natija', 'Testdan 100% oldingiz!', 'target'],
    ] as $b) {
        $badge->execute($b);
    }
    $pdo->exec("UPDATE badges SET description = 'Birinchi kursning barcha darslarini a’lo tugatdingiz!' WHERE code = 'lingo_master'");

    // The v1 seed scheduled the demo session at "now + 3 days" with seconds; move it to Saturday 11:00 ET.
    $sat = (new DateTimeImmutable('next saturday 11:00', new DateTimeZone('America/New_York')))->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    $pdo->prepare("UPDATE live_sessions SET starts_at = ?, timezone = 'America/New_York' WHERE id = 1 AND strftime('%S', starts_at) <> '00'")->execute([$sat]);

    // Culture course
    $exists = (int) $pdo->query("SELECT count(*) FROM courses WHERE title = 'Ikki madaniyat ko‘prigi'")->fetchColumn();
    if ($exists > 0) {
        return;
    }

    $pdo->prepare("
        INSERT INTO courses (title, subtitle, level, age_range, description, status, category, icon, theme, sort_order, created_at)
        VALUES (?, ?, 'A1', '7–11 yosh', ?, 'published', 'culture', 'registon', 'gold', 2, datetime('now'))
    ")->execute([
        'Ikki madaniyat ko‘prigi',
        'Osh, non, Navro‘z, qadimiy shaharlar va odob',
        'O‘zbekiston va Amerika madaniyatini solishtirib o‘rganamiz: milliy taomlar, bayramlar, mehmondo‘stlik, qadimiy shaharlar va kiyimlar. Bola o‘z ildizini qiziqib, faxrlanib o‘rganadi.',
    ]);
    $courseId = (int) $pdo->lastInsertId();

    foreach (culture_course_lessons() as $position => $l) {
        $seq = (int) $pdo->query('SELECT COALESCE(MAX(sequence_number), 0) + 1 FROM lessons')->fetchColumn();
        $pdo->prepare('
            INSERT INTO lessons (sequence_number, course_id, position, title, title_en, objective, duration_min, summary,
                                 passing_score, status, icon, reading_uz, reading_en, culture_title, culture_note)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 80, \'published\', ?, ?, ?, ?, ?)
        ')->execute([
            $seq, $courseId, $position + 1, $l['title'], $l['title_en'], $l['objective'], $l['duration'], $l['summary'],
            $l['icon'], $l['reading_uz'], $l['reading_en'], $l['culture_title'], $l['culture_note'],
        ]);
        $lessonId = (int) $pdo->lastInsertId();

        $vocab = $pdo->prepare("INSERT INTO vocabulary (lesson_id, word_uz, word_en, pronunciation, icon, example) VALUES (?, ?, ?, ?, '', ?)");
        foreach ($l['vocab'] as $v) {
            $vocab->execute([$lessonId, $v[0], $v[1], $v[2], $v[3]]);
        }

        $pdo->prepare('INSERT INTO quizzes (lesson_id, title, pass_score) VALUES (?, ?, 80)')
            ->execute([$lessonId, $l['title'] . ' — Yakuniy Test']);
        add_v2_questions($pdo, (int) $pdo->lastInsertId(), $l['quiz']);
    }
}

function add_v2_questions(PDO $pdo, int $quizId, array $questions): void
{
    $order = (int) $pdo->query("SELECT COALESCE(MAX(order_num), 0) FROM questions WHERE quiz_id = {$quizId}")->fetchColumn();
    $exists = $pdo->prepare('SELECT count(*) FROM questions WHERE quiz_id = ? AND prompt_en = ?');
    $insert = $pdo->prepare('
        INSERT INTO questions (quiz_id, type, prompt, prompt_en, options_json, correct_index, answer_text, audio_text, explanation, order_num)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    foreach ($questions as $q) {
        $exists->execute([$quizId, $q['prompt_en']]);
        if ((int) $exists->fetchColumn() > 0) {
            continue;
        }
        $insert->execute([
            $quizId,
            $q['type'] ?? 'choice',
            $q['prompt'],
            $q['prompt_en'],
            json_encode($q['options'] ?? [], JSON_UNESCAPED_UNICODE),
            (int) ($q['correct'] ?? 0),
            $q['answer_text'] ?? null,
            $q['audio_text'] ?? null,
            $q['explanation'] ?? null,
            ++$order,
        ]);
    }
}

function culture_course_lessons(): array
{
    return [
        [
            'title' => 'Osh — dasturxon shohi',
            'title_en' => 'Plov — the king of the table',
            'icon' => 'heart',
            'duration' => 6,
            'objective' => 'Bola oshning tarkibiy qismlarini o‘zbekcha ayta oladi va osh qachon pishirilishini biladi.',
            'summary' => 'O‘zbek oshi qanday tayyorlanishi, kim pishirishi va nega u har bir bayramning yulduzi ekanini bilib olamiz.',
            'reading_uz' => "Juma kuni dadam katta qozonda osh damlaydi.\nOshga guruch, sabzi, go‘sht va piyoz solinadi.\nSabzi sariq va qizil bo‘ladi.\nOshni katta lagan bilan dasturxonga qo‘yamiz.\nHamma birga o‘tirib, osh yeymiz. Mazza!",
            'reading_en' => "On Friday my dad cooks plov in a big cauldron.\nRice, carrots, meat and onions go into the plov.\nThe carrots are yellow and red.\nWe put the plov on the table on a big platter.\nEveryone sits together and eats plov. Yummy!",
            'culture_title' => 'Osh — YUNESKO merosi',
            'culture_note' => '2016-yilda o‘zbek oshi YUNESKOning nomoddiy madaniy merosi ro‘yxatiga kiritilgan. Amerikada Thanksgiving kuni kurka pishirilgani kabi, O‘zbekistonda to‘y va bayramlarda albatta osh damlanadi.',
            'vocab' => [
                ['Osh', 'Plov (rice dish)', 'Ohsh', 'Bugun tushlikka osh!'],
                ['Guruch', 'Rice', 'Goo-rooch', 'Guruch oq va mayda.'],
                ['Sabzi', 'Carrot', 'Sab-zee', 'Sariq sabzi juda shirin.'],
                ['Go‘sht', 'Meat', 'Gohsht', 'Oshga go‘sht solinadi.'],
                ['Qozon', 'Cauldron (big pot)', 'Qo-zon', 'Qozon juda katta!'],
                ['Dasturxon', 'Tablecloth / laid table', 'Das-toor-khon', 'Dasturxonga marhamat!'],
            ],
            'quiz' => [
                ['type' => 'choice', 'prompt' => 'Osh qaysi idishda pishiriladi?', 'prompt_en' => 'What is plov cooked in?', 'options' => ['Choynak', 'Qozon', 'Piyola', 'Lagan'], 'correct' => 1, 'explanation' => 'Osh katta «qozon»da damlanadi.'],
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'Guruch', 'options' => ['Meat', 'Onion', 'Rice', 'Bread'], 'correct' => 2, 'explanation' => '«Guruch» — «Rice».'],
                ['type' => 'choice', 'prompt' => '«Carrot» o‘zbekcha qanday?', 'prompt_en' => 'How do you say "carrot" in Uzbek?', 'options' => ['Sabzi', 'Piyoz', 'Olma', 'Anor'], 'correct' => 0, 'explanation' => '«Sabzi» — «Carrot».'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'Dad cooks plov.', 'answer_text' => 'Dadam osh damlaydi', 'options' => ['non', 'oyim', 'choy'], 'explanation' => '«Dadam osh damlaydi» — «Dad cooks plov».'],
                ['type' => 'fill', 'prompt' => 'Bo‘sh joyni to‘ldiring: «Oshga guruch, sabzi va ___ solinadi» (meat)', 'prompt_en' => 'Fill in: "Rice, carrots and ___ go into plov" (meat)', 'answer_text' => 'go‘sht', 'explanation' => '«Go‘sht» — «Meat».'],
                ['type' => 'choice', 'prompt' => 'O‘zbek oshi 2016-yilda qaysi ro‘yxatga kirgan?', 'prompt_en' => 'Which list did Uzbek plov join in 2016?', 'options' => ['Olimpiada', 'YUNESKO merosi', 'Ginnes rekordlari', 'Oskar'], 'correct' => 1, 'explanation' => 'Osh — YUNESKOning nomoddiy madaniy merosi.'],
            ],
        ],
        [
            'title' => 'Non, somsa va choy',
            'title_en' => 'Bread, samsa and tea',
            'icon' => 'coins',
            'duration' => 5,
            'objective' => 'Bola non, somsa, choy va piyola so‘zlarini biladi va nonga hurmat odobini tushunadi.',
            'summary' => 'Tandir noni, issiq somsa va ko‘k choy — o‘zbek dasturxonining uch do‘sti. Nonga nega hurmat qilinishini bilib olamiz.',
            'reading_uz' => "Ertalab bobom tandirdan issiq non olib keladi.\nNon dumaloq va juda mazali.\nBuvim piyolaga ko‘k choy quyadi.\nTushlikda somsa yeymiz.\nNonni hech qachon teskari qo‘ymaymiz.",
            'reading_en' => "In the morning grandpa brings hot bread from the tandoor.\nThe bread is round and very tasty.\nGrandma pours green tea into a small bowl.\nAt lunch we eat samsa.\nWe never put bread upside down.",
            'culture_title' => 'Non — muqaddas',
            'culture_note' => 'O‘zbek oilasida non hech qachon yerga tashlanmaydi va teskari qo‘yilmaydi. Mehmon kelganda birinchi bo‘lib non sindiriladi. Amerikada sendvich noni do‘kondan olinadi, O‘zbekistonda esa non issiq tandirdan chiqadi!',
            'vocab' => [
                ['Non', 'Bread', 'Non', 'Issiq non keltiring!'],
                ['Tandir', 'Clay oven (tandoor)', 'Tan-deer', 'Tandirda non pishadi.'],
                ['Somsa', 'Samsa (meat pastry)', 'Som-sah', 'Somsa juda issiq!'],
                ['Choy', 'Tea', 'Choy', 'Ko‘k choy iching.'],
                ['Piyola', 'Tea bowl', 'Pee-yo-lah', 'Piyolaga choy quying.'],
                ['Mazali', 'Tasty', 'Ma-za-lee', 'Bu non juda mazali!'],
            ],
            'quiz' => [
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'Choy', 'options' => ['Milk', 'Tea', 'Water', 'Juice'], 'correct' => 1, 'explanation' => '«Choy» — «Tea».'],
                ['type' => 'choice', 'prompt' => 'Non qayerda pishiriladi?', 'prompt_en' => 'Where is Uzbek bread baked?', 'options' => ['Tandirda', 'Muzlatgichda', 'Qozonda', 'Piyolada'], 'correct' => 0, 'explanation' => 'O‘zbek noni «tandir»da pishiriladi.'],
                ['type' => 'choice', 'prompt' => 'Nonni qanday qo‘yish kerak emas?', 'prompt_en' => 'How should bread never be placed?', 'options' => ['Dasturxonga', 'Teskari', 'Likopchaga', 'Savatga'], 'correct' => 1, 'explanation' => 'Nonni teskari qo‘yish odobsizlik hisoblanadi.'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'The bread is very tasty.', 'answer_text' => 'Non juda mazali', 'options' => ['choy', 'issiq', 'somsa'], 'explanation' => '«Non juda mazali» — «The bread is very tasty».'],
                ['type' => 'fill', 'prompt' => 'Choy qaysi idishga quyiladi? (tea bowl)', 'prompt_en' => 'Tea is poured into a ___ (tea bowl)', 'answer_text' => 'piyola', 'explanation' => '«Piyola» — kichkina choy idishi.'],
            ],
        ],
        [
            'title' => 'Navro‘z — bahor bayrami',
            'title_en' => 'Navruz — the spring festival',
            'icon' => 'sparkles',
            'duration' => 6,
            'objective' => 'Bola Navro‘z qachon nishonlanishini, sumalak va bahor so‘zlarini biladi, uni Amerika bayramlari bilan solishtiradi.',
            'summary' => '21-mart — kun va tun tenglashgan kun. Sumalak, ko‘k somsa va bahor o‘yinlari bilan Navro‘zni kutib olamiz.',
            'reading_uz' => "Navro‘z — bahor bayrami.\nU 21-martda nishonlanadi.\nOnalar tun bo‘yi sumalak pishiradi.\nBolalar bayram kiyimini kiyib, qo‘shiq aytadi.\nHamma bir-biriga «Navro‘z muborak!» deydi.",
            'reading_en' => "Navruz is the spring festival.\nIt is celebrated on March 21.\nMothers cook sumalak all night long.\nChildren wear festive clothes and sing songs.\nEveryone says \"Happy Navruz!\" to each other.",
            'culture_title' => 'Navro‘z va Thanksgiving',
            'culture_note' => 'Amerikada Thanksgiving kuni oila yig‘ilib, shukrona aytadi. Navro‘zda ham shunday: butun mahalla birga sumalak pishiradi, qozon atrofida tilak tilaydi. Ikkala bayramning ma’nosi bir — birlik va minnatdorlik.',
            'vocab' => [
                ['Bahor', 'Spring', 'Ba-hor', 'Bahor keldi!'],
                ['Bayram', 'Holiday / festival', 'Bay-ram', 'Bugun bayram!'],
                ['Sumalak', 'Sumalak (sweet wheat dish)', 'Soo-ma-lak', 'Sumalak shirin bo‘ladi.'],
                ['Muborak', 'Blessed / Happy (greeting)', 'Moo-bo-rak', 'Navro‘z muborak!'],
                ['Gul', 'Flower', 'Gool', 'Bahorda gullar ochiladi.'],
                ['Qo‘shiq', 'Song', 'Qoh-shiq', 'Keling, qo‘shiq aytamiz!'],
            ],
            'quiz' => [
                ['type' => 'choice', 'prompt' => 'Navro‘z qaysi kuni nishonlanadi?', 'prompt_en' => 'When is Navruz celebrated?', 'options' => ['1-yanvar', '21-mart', '4-iyul', '25-dekabr'], 'correct' => 1, 'explanation' => 'Navro‘z — 21-mart, kun va tun tenglashgan kun.'],
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'Bahor', 'options' => ['Winter', 'Summer', 'Spring', 'Autumn'], 'correct' => 2, 'explanation' => '«Bahor» — «Spring».'],
                ['type' => 'build', 'prompt' => 'Bayram tabrigini tuzing', 'prompt_en' => 'Happy Navruz!', 'answer_text' => 'Navro‘z muborak', 'options' => ['bahor', 'rahmat'], 'explanation' => '«Navro‘z muborak!» — eng mashhur bayram tabrigi.'],
                ['type' => 'choice', 'prompt' => 'Navro‘zda tun bo‘yi qaysi taom pishiriladi?', 'prompt_en' => 'Which dish is cooked all night at Navruz?', 'options' => ['Pitsa', 'Sumalak', 'Burger', 'Tort'], 'correct' => 1, 'explanation' => 'Sumalak — Navro‘zning asosiy taomi.'],
                ['type' => 'fill', 'prompt' => '«Flower» so‘zini o‘zbekcha yozing', 'prompt_en' => 'Write "flower" in Uzbek', 'answer_text' => 'gul', 'explanation' => '«Gul» — «Flower».'],
            ],
        ],
        [
            'title' => 'Mehmondo‘stlik va odob',
            'title_en' => 'Hospitality & good manners',
            'icon' => 'heart',
            'duration' => 5,
            'objective' => 'Bola mehmonni kutib olish iboralarini va kattalarga hurmat odobini biladi.',
            'summary' => '«Mehmon — otangdek ulug‘». Mehmonni qanday kutib olish, choy qanday quyilishi va kattalarga qanday murojaat qilishni o‘rganamiz.',
            'reading_uz' => "Bizga mehmon keldi.\nMen eshikni ochib: «Xush kelibsiz!» dedim.\nOyim: «Marhamat, to‘rga o‘ting!» dedi.\nMen mehmonga choy uzatdim.\nMehmon: «Rahmat, qizim!» dedi.",
            'reading_en' => "Guests came to us.\nI opened the door and said: \"Welcome!\"\nMom said: \"Please, take the seat of honour!\"\nI handed tea to the guest.\nThe guest said: \"Thank you, my girl!\"",
            'culture_title' => 'Choy oz quyiladi',
            'culture_note' => 'O‘zbekistonda mehmonga piyola to‘la emas, ozgina choy quyiladi — bu «yana keling, ko‘proq o‘tiring» degan hurmat belgisi. Amerikada esa stakan to‘ldirib beriladi. Ikkala odat ham mehmonga g‘amxo‘rlik!',
            'vocab' => [
                ['Mehmon', 'Guest', 'Meh-mon', 'Bizga mehmon keldi.'],
                ['Xush kelibsiz', 'Welcome', 'Khush ke-lib-siz', 'Xush kelibsiz, aziz mehmon!'],
                ['Marhamat', 'Please / Here you go', 'Mar-ha-mat', 'Marhamat, o‘tiring.'],
                ['Kechirasiz', 'Excuse me / Sorry', 'Ke-chi-ra-siz', 'Kechirasiz, kech qoldim.'],
                ['Hurmat', 'Respect', 'Hoor-mat', 'Kattalarga hurmat qiling.'],
                ['Uzatmoq', 'To hand over', 'Oo-zat-moq', 'Choyni ikki qo‘llab uzating.'],
            ],
            'quiz' => [
                ['type' => 'choice', 'prompt' => 'Mehmon kelganda nima deymiz?', 'prompt_en' => 'What do we say when a guest arrives?', 'options' => ['Xayr', 'Xush kelibsiz', 'Kechirasiz', 'Uxlang'], 'correct' => 1, 'explanation' => '«Xush kelibsiz» — «Welcome».'],
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'Kechirasiz', 'options' => ['Excuse me', 'Thank you', 'Welcome', 'Goodbye'], 'correct' => 0, 'explanation' => '«Kechirasiz» — «Excuse me / Sorry».'],
                ['type' => 'choice', 'prompt' => 'Nega mehmonga choy oz quyiladi?', 'prompt_en' => 'Why is a guest\'s tea bowl filled only a little?', 'options' => ['Choy tugab qolgan', 'Hurmat belgisi — yana quyib beriladi', 'Choy achchiq', 'Piyola kichkina'], 'correct' => 1, 'explanation' => 'Oz quyish — «ko‘proq o‘tiring» degan hurmat.'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'Welcome, dear guest!', 'answer_text' => 'Xush kelibsiz aziz mehmon', 'options' => ['xayr', 'choy'], 'explanation' => '«Xush kelibsiz, aziz mehmon!»'],
                ['type' => 'fill', 'prompt' => '«Guest» so‘zini o‘zbekcha yozing', 'prompt_en' => 'Write "guest" in Uzbek', 'answer_text' => 'mehmon', 'explanation' => '«Mehmon» — «Guest».'],
            ],
        ],
        [
            'title' => 'Samarqand, Buxoro, Xiva',
            'title_en' => 'Samarkand, Bukhara, Khiva',
            'icon' => 'registon',
            'duration' => 6,
            'objective' => 'Bola O‘zbekistonning uch qadimiy shahrini va ularning mashhur joylarini taniydi.',
            'summary' => 'Buyuk Ipak yo‘lidagi ertaknamo shaharlar: Registon maydoni, Minorai Kalon va Ichan qal’a bo‘ylab sayohat.',
            'reading_uz' => "O‘zbekistonda qadimiy shaharlar ko‘p.\nSamarqandda Registon maydoni bor.\nBuxoroda baland minora bor.\nXiva — devor ichidagi ertak shahar.\nPoytaxtimiz — Toshkent.",
            'reading_en' => "There are many ancient cities in Uzbekistan.\nIn Samarkand there is Registan Square.\nIn Bukhara there is a tall minaret.\nKhiva is a fairy-tale city inside walls.\nOur capital is Tashkent.",
            'culture_title' => 'Buyuk Ipak yo‘li',
            'culture_note' => 'Ming yillar oldin karvonlar Xitoydan Yevropaga ipak, ziravor va qog‘oz olib o‘tgan. Bu yo‘l Samarqand, Buxoro va Xiva orqali o‘tgan. Shuning uchun bu shaharlar «Sharq gavhari» deb ataladi.',
            'vocab' => [
                ['Shahar', 'City', 'Sha-har', 'Samarqand — go‘zal shahar.'],
                ['Qadimiy', 'Ancient', 'Qa-dee-miy', 'Buxoro qadimiy shahar.'],
                ['Maydon', 'Square (plaza)', 'May-don', 'Registon maydoni katta.'],
                ['Minora', 'Minaret / tower', 'Mee-no-ra', 'Minora juda baland!'],
                ['Poytaxt', 'Capital city', 'Poy-takht', 'Poytaxt — Toshkent.'],
                ['Sayohat', 'Trip / journey', 'Sa-yo-hat', 'Xivaga sayohat qilamiz.'],
            ],
            'quiz' => [
                ['type' => 'choice', 'prompt' => 'Registon maydoni qaysi shaharda?', 'prompt_en' => 'Which city is Registan Square in?', 'options' => ['Toshkent', 'Samarqand', 'Xiva', 'Nyu-York'], 'correct' => 1, 'explanation' => 'Registon — Samarqandda.'],
                ['type' => 'choice', 'prompt' => 'O‘zbekistonning poytaxti qaysi?', 'prompt_en' => 'What is the capital of Uzbekistan?', 'options' => ['Buxoro', 'Xiva', 'Toshkent', 'Samarqand'], 'correct' => 2, 'explanation' => 'Poytaxt — Toshkent.'],
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'Qadimiy', 'options' => ['Modern', 'Ancient', 'Small', 'Beautiful'], 'correct' => 1, 'explanation' => '«Qadimiy» — «Ancient».'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'Bukhara is an ancient city.', 'answer_text' => 'Buxoro qadimiy shahar', 'options' => ['maydon', 'baland'], 'explanation' => '«Buxoro qadimiy shahar».'],
                ['type' => 'fill', 'prompt' => '«City» so‘zini o‘zbekcha yozing', 'prompt_en' => 'Write "city" in Uzbek', 'answer_text' => 'shahar', 'explanation' => '«Shahar» — «City».'],
            ],
        ],
        [
            'title' => 'Do‘ppi, atlas va milliy o‘yinlar',
            'title_en' => 'Doppi, atlas and folk games',
            'icon' => 'palette',
            'duration' => 5,
            'objective' => 'Bola milliy kiyim nomlarini va o‘zbek bolalari o‘ynaydigan o‘yinlarni biladi.',
            'summary' => 'Chust do‘ppisi, rang-barang atlas, issiq chopon va «Oq terakmi, ko‘k terak» o‘yini — bayramona sayohat.',
            'reading_uz' => "Bobom boshiga do‘ppi kiyadi.\nOpam atlas ko‘ylak kiygan.\nQishda chopon juda issiq.\nHovlida «Oq terakmi, ko‘k terak» o‘ynaymiz.\nBu o‘yin juda qiziqarli!",
            'reading_en' => "Grandpa wears a doppi on his head.\nMy sister is wearing an atlas dress.\nIn winter a chapan is very warm.\nIn the yard we play \"White poplar, blue poplar\".\nThis game is very fun!",
            'culture_title' => 'Do‘ppidagi qalampir',
            'culture_note' => 'Chust do‘ppisidagi oq naqsh — qalampir shakli. U yomon ko‘zdan asraydi deb ishonilgan. Amerikada beysbol kepkasi qanday mashhur bo‘lsa, O‘zbekistonda do‘ppi ham shunday mashhur!',
            'vocab' => [
                ['Do‘ppi', 'Doppi (embroidered cap)', 'Dohp-pee', 'Bobomning do‘ppisi chiroyli.'],
                ['Atlas', 'Atlas (bright silk)', 'At-las', 'Atlas ko‘ylak rang-barang.'],
                ['Chopon', 'Chapan (quilted robe)', 'Cho-pon', 'Chopon issiq.'],
                ['Ko‘ylak', 'Dress / shirt', 'Kohy-lak', 'Yangi ko‘ylak kiydim.'],
                ['O‘yin', 'Game', 'Oh-yin', 'Keling, o‘yin o‘ynaymiz!'],
                ['Chiroyli', 'Beautiful', 'Chee-roy-lee', 'Juda chiroyli!'],
            ],
            'quiz' => [
                ['type' => 'choice', 'prompt' => 'Boshga kiyiladigan milliy kiyim qaysi?', 'prompt_en' => 'Which national item is worn on the head?', 'options' => ['Chopon', 'Do‘ppi', 'Atlas', 'Ko‘ylak'], 'correct' => 1, 'explanation' => '«Do‘ppi» — boshga kiyiladi.'],
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'O‘yin', 'options' => ['Song', 'Game', 'Dress', 'House'], 'correct' => 1, 'explanation' => '«O‘yin» — «Game».'],
                ['type' => 'choice', 'prompt' => 'Qishda qaysi kiyim issiq saqlaydi?', 'prompt_en' => 'Which clothing keeps you warm in winter?', 'options' => ['Chopon', 'Do‘ppi', 'Piyola', 'Gul'], 'correct' => 0, 'explanation' => '«Chopon» — paxtali issiq to‘n.'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'The game is very fun.', 'answer_text' => 'O‘yin juda qiziqarli', 'options' => ['chopon', 'issiq'], 'explanation' => '«O‘yin juda qiziqarli».'],
                ['type' => 'fill', 'prompt' => '«Beautiful» so‘zini o‘zbekcha yozing', 'prompt_en' => 'Write "beautiful" in Uzbek', 'answer_text' => 'chiroyli', 'explanation' => '«Chiroyli» — «Beautiful».'],
            ],
        ],
    ];
}
