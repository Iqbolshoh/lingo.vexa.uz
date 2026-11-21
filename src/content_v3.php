<?php

declare(strict_types=1);

/**
 * Schema v3 content: the platform is for everyone who wants to learn Uzbek
 * (kids abroad, adults, tourists), so existing courses get audience-neutral copy
 * and a "Sayohatchilar uchun" (Uzbek for travellers) survival course is added.
 * Idempotent like seed_v2_content().
 */
function seed_v3_content(PDO $pdo): void
{
    $pdo->exec("
        UPDATE courses SET age_range = 'Hamma uchun · 7+',
            description = 'O‘zbek tilini noldan boshlovchilar uchun interaktiv, o‘yinli kurs: bolalar ham, kattalar ham. Har dars: mikro-sahna, ovozli lug‘at, o‘qish, o‘yin, mashq va test.'
        WHERE id = 1 AND description LIKE 'AQShda ulg‘ayayotgan%'
    ");
    $pdo->exec("
        UPDATE courses SET age_range = 'Hamma uchun · 7+',
            description = 'O‘zbekiston madaniyatini qiziqib o‘rganamiz: milliy taomlar, bayramlar, mehmondo‘stlik, qadimiy shaharlar va kiyimlar — Amerika va boshqa madaniyatlar bilan solishtirib. Xorijdagi oilalar va sayyohlar uchun birdek qiziq.'
        WHERE title = 'Ikki madaniyat ko‘prigi' AND description LIKE 'O‘zbekiston va Amerika madaniyatini%'
    ");

    $exists = (int) $pdo->query("SELECT count(*) FROM courses WHERE title = 'Sayohatchilar uchun o‘zbek tili'")->fetchColumn();
    if ($exists > 0) {
        return;
    }

    $pdo->prepare("
        INSERT INTO courses (title, subtitle, level, age_range, description, status, category, icon, theme, sort_order, created_at)
        VALUES (?, ?, 'A0', 'Sayyohlar va kattalar', ?, 'published', 'language', 'map-pin', 'emerald', 3, datetime('now'))
    ")->execute([
        'Sayohatchilar uchun o‘zbek tili',
        'Uzbek for travellers: bozor, taksi, choyxona, mehmonxona',
        'O‘zbekistonga sayohatga keladiganlar uchun eng kerakli iboralar: salomlashish, narx so‘rash va savdolashish, taksida yo‘l ko‘rsatish, choyxonada buyurtma berish, mehmonxona va yordam so‘rash. Each lesson explains phrases in English too.',
    ]);
    $courseId = (int) $pdo->lastInsertId();

    foreach (travel_course_lessons() as $position => $l) {
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

function travel_course_lessons(): array
{
    return [
        [
            'title' => 'Birinchi kun: salom va rahmat',
            'title_en' => 'Day one: hello & thank you',
            'icon' => 'message-square',
            'duration' => 5,
            'objective' => 'Salomlashish, minnatdorchilik bildirish, kechirim so‘rash va «tushunmadim» deyish.',
            'summary' => 'Sayohatning birinchi kunida kerak bo‘ladigan 6 ta sehrli ibora. Six magic phrases for your first day in Uzbekistan.',
            'reading_uz' => "Assalomu alaykum!\nMen sayyohman.\nKechirasiz, tushunmadim.\nIltimos, sekinroq gapiring.\nKatta rahmat! Xayr!",
            'reading_en' => "Hello!\nI am a tourist.\nSorry, I didn't understand.\nPlease speak more slowly.\nThank you very much! Goodbye!",
            'culture_title' => 'Qo‘l ko‘ksida',
            'culture_note' => 'O‘zbekistonda salomlashganda o‘ng qo‘lni ko‘krakka qo‘yish — samimiy hurmat belgisi. Erkaklar odatda qo‘l berib ko‘rishadi; ayol bilan salomlashganda, u qo‘l uzatmasa, qo‘lni ko‘krakka qo‘yib bosh egish kifoya. Tip: a hand on the heart is the safest greeting.',
            'vocab' => [
                ['Assalomu alaykum', 'Hello (formal, respectful)', 'As-sa-LO-mu a-LAY-kum', 'Assalomu alaykum, aka!'],
                ['Rahmat', 'Thank you', 'Rakh-MAT', 'Katta rahmat!'],
                ['Kechirasiz', 'Excuse me / Sorry', 'Ke-chi-RA-siz', 'Kechirasiz, bu qayer?'],
                ['Tushunmadim', 'I didn’t understand', 'Too-shun-MA-dim', 'Kechirasiz, tushunmadim.'],
                ['Iltimos', 'Please', 'Il-ti-MOS', 'Iltimos, sekinroq.'],
                ['Xayr', 'Goodbye', 'Khayr', 'Xayr, ko‘rishguncha!'],
            ],
            'quiz' => [
                ['type' => 'choice', 'prompt' => '«Please» o‘zbekcha qanday?', 'prompt_en' => 'How do you say "please"?', 'options' => ['Rahmat', 'Iltimos', 'Xayr', 'Salom'], 'correct' => 1, 'explanation' => '«Iltimos» — «Please».'],
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'Tushunmadim', 'options' => ['I am a tourist', 'I didn’t understand', 'Thank you', 'Goodbye'], 'correct' => 1, 'explanation' => '«Tushunmadim» — «I didn’t understand».'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'I am a tourist.', 'answer_text' => 'Men sayyohman', 'options' => ['rahmat', 'siz'], 'explanation' => '«Men sayyohman» — «I am a tourist».'],
                ['type' => 'choice', 'prompt' => 'Birovni to‘xtatib savol bermoqchisiz. Nima deysiz?', 'prompt_en' => 'You want to stop someone to ask a question. What do you say?', 'options' => ['Xayr', 'Kechirasiz', 'Mazali', 'Qimmat'], 'correct' => 1, 'explanation' => '«Kechirasiz» — «Excuse me».'],
                ['type' => 'fill', 'prompt' => '«Thank you» so‘zini o‘zbekcha yozing', 'prompt_en' => 'Write "thank you" in Uzbek', 'answer_text' => 'rahmat', 'explanation' => '«Rahmat» — «Thank you».'],
            ],
        ],
        [
            'title' => 'Bozorda: necha pul?',
            'title_en' => 'At the bazaar: how much?',
            'icon' => 'coins',
            'duration' => 6,
            'objective' => 'Narx so‘rash, «qimmat» deyish va muloyim savdolashish.',
            'summary' => 'Chorsu va Siyob bozorlarida savdolashish — o‘yin! Bargaining at the bazaar is part of the fun.',
            'reading_uz' => "Assalomu alaykum! Bu necha pul?\nYigirma ming so‘m.\nVoy, qimmat! Arzonroq bo‘ladimi?\nMayli, o‘n besh ming.\nYaxshi, olaman. Rahmat!",
            'reading_en' => "Hello! How much is this?\nTwenty thousand sum.\nOh, that's expensive! Can it be cheaper?\nOkay, fifteen thousand.\nGood, I'll take it. Thank you!",
            'culture_title' => 'Savdolashish — san’at',
            'culture_note' => 'O‘zbek bozorlarida narx ko‘pincha «kelishiladi». Tabassum bilan «Arzonroq bo‘ladimi?» deb so‘rash odobsizlik emas, aksincha sotuvchi buni yaxshi ko‘radi. Supermarketlarda esa narx qat’iy. Tip: smile, bargain, and say «rahmat» either way.',
            'vocab' => [
                ['Necha pul?', 'How much?', 'NE-cha pul', 'Bu necha pul?'],
                ['Qimmat', 'Expensive', 'Qim-MAT', 'Voy, qimmat!'],
                ['Arzon', 'Cheap', 'Ar-ZON', 'Arzonroq bo‘ladimi?'],
                ['Ming', 'Thousand', 'Ming', 'O‘n ming so‘m.'],
                ['So‘m', 'Sum (Uzbek currency)', 'Sohm', 'Yigirma ming so‘m.'],
                ['Olaman', 'I’ll take it / I’ll buy', 'O-la-MAN', 'Yaxshi, olaman!'],
            ],
            'quiz' => [
                ['type' => 'choice', 'prompt' => 'Narxni qanday so‘raysiz?', 'prompt_en' => 'How do you ask the price?', 'options' => ['Qayerda?', 'Necha pul?', 'Kim?', 'Qachon?'], 'correct' => 1, 'explanation' => '«Necha pul?» — «How much?».'],
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'Qimmat', 'options' => ['Cheap', 'Tasty', 'Expensive', 'Big'], 'correct' => 2, 'explanation' => '«Qimmat» — «Expensive».'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'How much is this?', 'answer_text' => 'Bu necha pul', 'options' => ['qimmat', 'olaman'], 'explanation' => '«Bu necha pul?» — «How much is this?».'],
                ['type' => 'choice', 'prompt' => '«Ming» nechaga teng?', 'prompt_en' => 'What number is «ming»?', 'options' => ['10', '100', '1 000', '1 000 000'], 'correct' => 2, 'explanation' => '«Ming» — 1 000.'],
                ['type' => 'fill', 'prompt' => '«Cheap» so‘zini o‘zbekcha yozing', 'prompt_en' => 'Write "cheap" in Uzbek', 'answer_text' => 'arzon', 'explanation' => '«Arzon» — «Cheap».'],
            ],
        ],
        [
            'title' => 'Taksida: o‘ngga, chapga, to‘xtang',
            'title_en' => 'In a taxi: right, left, stop',
            'icon' => 'map-pin',
            'duration' => 5,
            'objective' => 'Haydovchiga manzil va yo‘nalishni ayta olish.',
            'summary' => 'Yandex Go yoki oddiy taksi — baribir yo‘l ko‘rsatish kerak. Directions every traveller needs.',
            'reading_uz' => "Registonga boring, iltimos.\nTo‘g‘riga yuring.\nKeyin chapga buriling.\nShu yerda to‘xtang.\nRahmat, aka!",
            'reading_en' => "To the Registan, please.\nGo straight.\nThen turn left.\nStop here.\nThank you, brother!",
            'culture_title' => '«Aka» va «opa»',
            'culture_note' => 'O‘zbekistonda notanish erkakka «aka» (aka-uka, older brother), ayolga «opa» (older sister) deb murojaat qilinadi. Haydovchiga «Rahmat, aka!» desangiz, darhol do‘st bo‘lib qolasiz. Tip: «aka» and «opa» are polite, warm ways to address strangers.',
            'vocab' => [
                ['O‘ngga', 'To the right', 'Ohn-GA', 'O‘ngga buriling.'],
                ['Chapga', 'To the left', 'Chap-GA', 'Chapga buriling.'],
                ['To‘g‘riga', 'Straight ahead', 'Tohgh-ri-GA', 'To‘g‘riga yuring.'],
                ['To‘xtang', 'Stop (please)', 'Tohkh-TANG', 'Shu yerda to‘xtang.'],
                ['Shu yerda', 'Here', 'Shoo yer-DA', 'Shu yerda to‘xtang.'],
                ['Aka', 'Brother (polite address to a man)', 'A-KA', 'Rahmat, aka!'],
            ],
            'quiz' => [
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'Chapga', 'options' => ['Right', 'Left', 'Straight', 'Stop'], 'correct' => 1, 'explanation' => '«Chapga» — «To the left».'],
                ['type' => 'choice', 'prompt' => 'Haydovchini to‘xtatish uchun nima deysiz?', 'prompt_en' => 'How do you ask the driver to stop?', 'options' => ['Yuring', 'To‘xtang', 'Boring', 'Keling'], 'correct' => 1, 'explanation' => '«To‘xtang» — «Stop».'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'Stop here.', 'answer_text' => 'Shu yerda to‘xtang', 'options' => ['chapga', 'aka'], 'explanation' => '«Shu yerda to‘xtang» — «Stop here».'],
                ['type' => 'choice', 'prompt' => 'Notanish erkakka qanday murojaat qilinadi?', 'prompt_en' => 'How do you politely address an unknown man?', 'options' => ['Opa', 'Aka', 'Buvi', 'Uka'], 'correct' => 1, 'explanation' => '«Aka» — erkakka hurmatli murojaat.'],
                ['type' => 'fill', 'prompt' => '«To the right» o‘zbekcha qanday? (o‘ ...)', 'prompt_en' => 'Write "to the right" in Uzbek', 'answer_text' => 'o‘ngga', 'explanation' => '«O‘ngga» — «To the right».'],
            ],
        ],
        [
            'title' => 'Choyxonada: osh va choy',
            'title_en' => 'At the teahouse: plov & tea',
            'icon' => 'heart',
            'duration' => 6,
            'objective' => 'Taom buyurtma qilish, suv yoki choy so‘rash va hisobni so‘rash.',
            'summary' => 'Choyxona — o‘zbek hayotining yuragi. Order like a local and ask for the bill.',
            'reading_uz' => "Menyu bormi?\nBitta osh va ikkita somsa bering.\nBir choynak ko‘k choy, iltimos.\nJuda mazali ekan!\nHisobni keltiring, iltimos.",
            'reading_en' => "Is there a menu?\nOne plov and two samsas, please.\nA pot of green tea, please.\nIt's really delicious!\nThe bill, please.",
            'culture_title' => 'Choy uch marta qaytariladi',
            'culture_note' => 'Choyxonada choy damlangandan keyin piyolaga quyib, yana choynakka uch marta qaytariladi — shunda choy yaxshi damlanadi. Bu odatni ko‘rsangiz, hayron qolmang! Tip: in many places the service charge (10–15%) is already in the bill.',
            'vocab' => [
                ['Menyu', 'Menu', 'Men-YU', 'Menyu bormi?'],
                ['Bering', 'Give (me), please', 'Be-RING', 'Bitta osh bering.'],
                ['Suv', 'Water', 'Soov', 'Bir shisha suv, iltimos.'],
                ['Choynak', 'Teapot', 'Choy-NAK', 'Bir choynak choy.'],
                ['Mazali', 'Delicious', 'Ma-za-LEE', 'Juda mazali!'],
                ['Hisob', 'The bill', 'Hi-SOB', 'Hisobni keltiring.'],
            ],
            'quiz' => [
                ['type' => 'choice', 'prompt' => 'Hisobni qanday so‘raysiz?', 'prompt_en' => 'How do you ask for the bill?', 'options' => ['Menyu bormi?', 'Hisobni keltiring', 'Suv bering', 'Juda mazali'], 'correct' => 1, 'explanation' => '«Hisobni keltiring» — «Bring the bill».'],
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'Suv', 'options' => ['Tea', 'Bread', 'Water', 'Milk'], 'correct' => 2, 'explanation' => '«Suv» — «Water».'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'One plov, please.', 'answer_text' => 'Bitta osh bering', 'options' => ['suv', 'hisob'], 'explanation' => '«Bitta osh bering» — «One plov, please».'],
                ['type' => 'choice', 'prompt' => 'Taom juda yoqdi. Nima deysiz?', 'prompt_en' => 'You loved the food. What do you say?', 'options' => ['Qimmat', 'Juda mazali!', 'To‘xtang', 'Tushunmadim'], 'correct' => 1, 'explanation' => '«Juda mazali!» — «Really delicious!».'],
                ['type' => 'fill', 'prompt' => '«Water» so‘zini o‘zbekcha yozing', 'prompt_en' => 'Write "water" in Uzbek', 'answer_text' => 'suv', 'explanation' => '«Suv» — «Water».'],
            ],
        ],
        [
            'title' => 'Mehmonxona va yordam',
            'title_en' => 'Hotel & asking for help',
            'icon' => 'shield-check',
            'duration' => 5,
            'objective' => 'Mehmonxonada xona so‘rash, yo‘l so‘rash va kerak bo‘lsa yordam chaqirish.',
            'summary' => 'Xona, kalit, hojatxona qayerda, yordam bering — tinch sayohat uchun muhim so‘zlar.',
            'reading_uz' => "Bo‘sh xona bormi?\nMen ikki kecha qolaman.\nKechirasiz, hojatxona qayerda?\nMenga yordam bering, iltimos.\nShifokor kerak!",
            'reading_en' => "Is there a free room?\nI'll stay two nights.\nExcuse me, where is the toilet?\nPlease help me.\nI need a doctor!",
            'culture_title' => 'Favqulodda raqamlar',
            'culture_note' => 'O‘zbekistonda: 101 — o‘t o‘chiruvchilar, 102 — politsiya, 103 — tez yordam, 112 — yagona favqulodda xizmat. Emergency numbers: 112 works from any phone. Odamlar odatda yordam berishga juda tayyor — «Yordam bering!» deyishdan uyalmang.',
            'vocab' => [
                ['Xona', 'Room', 'Kho-NA', 'Bo‘sh xona bormi?'],
                ['Kalit', 'Key', 'Ka-LIT', 'Kalitni bering.'],
                ['Qayerda?', 'Where is…?', 'Qa-yer-DA', 'Metro qayerda?'],
                ['Hojatxona', 'Toilet / restroom', 'Ho-jat-kho-NA', 'Hojatxona qayerda?'],
                ['Yordam bering', 'Help me, please', 'Yor-DAM be-RING', 'Iltimos, yordam bering!'],
                ['Shifokor', 'Doctor', 'Shi-fo-KOR', 'Shifokor kerak.'],
            ],
            'quiz' => [
                ['type' => 'listen', 'prompt' => 'Tinglang va ma’nosini toping', 'prompt_en' => 'Listen and choose the meaning', 'audio_text' => 'Yordam bering', 'options' => ['Give me the key', 'Help me', 'Where is it?', 'Good night'], 'correct' => 1, 'explanation' => '«Yordam bering» — «Help me».'],
                ['type' => 'choice', 'prompt' => '«Where?» o‘zbekcha qanday?', 'prompt_en' => 'How do you say "where?"', 'options' => ['Qachon?', 'Kim?', 'Qayerda?', 'Necha?'], 'correct' => 2, 'explanation' => '«Qayerda?» — «Where?».'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'Where is the toilet?', 'answer_text' => 'Hojatxona qayerda', 'options' => ['xona', 'kalit'], 'explanation' => '«Hojatxona qayerda?»'],
                ['type' => 'choice', 'prompt' => 'Yagona favqulodda xizmat raqami qaysi?', 'prompt_en' => 'Which is the single emergency number?', 'options' => ['911', '112', '999', '100'], 'correct' => 1, 'explanation' => 'O‘zbekistonda yagona raqam — 112.'],
                ['type' => 'fill', 'prompt' => '«Room» so‘zini o‘zbekcha yozing', 'prompt_en' => 'Write "room" in Uzbek', 'answer_text' => 'xona', 'explanation' => '«Xona» — «Room».'],
            ],
        ],
        [
            'title' => 'Raqamlar, pul va vaqt',
            'title_en' => 'Numbers, money & time',
            'icon' => 'hash',
            'duration' => 6,
            'objective' => 'Katta raqamlarni (yuz, ming) tushunish va soatni so‘rash.',
            'summary' => 'So‘mda narxlar katta — 50 000, 120 000. Learn to hear big numbers and ask the time.',
            'reading_uz' => "Soat necha bo‘ldi?\nSoat o‘n bir.\nPoyezd soat uchda jo‘naydi.\nChipta ellik ming so‘m.\nMana, yuz ming.",
            'reading_en' => "What time is it?\nIt's eleven o'clock.\nThe train leaves at three.\nThe ticket is fifty thousand sum.\nHere you are, one hundred thousand.",
            'culture_title' => 'Afrosiyob poyezdi',
            'culture_note' => 'Toshkentdan Samarqandga «Afrosiyob» tezyurar poyezdi bilan atigi 2 soatda yetib borasiz. Chiptani oldindan oling — ayniqsa bahor va kuzda tez tugaydi. Tip: prices in sum have many zeros; «ming» (thousand) is the word you will hear most.',
            'vocab' => [
                ['Yuz', 'Hundred', 'Yooz', 'Yuz ming so‘m.'],
                ['Ellik', 'Fifty', 'El-LIK', 'Ellik ming so‘m.'],
                ['Soat necha?', 'What time is it?', 'So-AT ne-CHA', 'Kechirasiz, soat necha?'],
                ['Chipta', 'Ticket', 'Chip-TA', 'Bitta chipta bering.'],
                ['Poyezd', 'Train', 'Po-YEZD', 'Poyezd qachon jo‘naydi?'],
                ['Mana', 'Here you are', 'Ma-NA', 'Mana, pul.'],
            ],
            'quiz' => [
                ['type' => 'listen', 'prompt' => 'Tinglang va raqamni toping', 'prompt_en' => 'Listen and pick the number', 'audio_text' => 'Ellik ming', 'options' => ['5 000', '15 000', '50 000', '500 000'], 'correct' => 2, 'explanation' => '«Ellik ming» — 50 000.'],
                ['type' => 'choice', 'prompt' => 'Vaqtni qanday so‘raysiz?', 'prompt_en' => 'How do you ask the time?', 'options' => ['Necha pul?', 'Soat necha?', 'Qayerda?', 'Kim?'], 'correct' => 1, 'explanation' => '«Soat necha?» — «What time is it?».'],
                ['type' => 'choice', 'prompt' => '«Yuz ming» nechaga teng?', 'prompt_en' => 'What is «yuz ming»?', 'options' => ['1 000', '10 000', '100 000', '1 000 000'], 'correct' => 2, 'explanation' => '«Yuz» — 100, «ming» — 1 000, demak 100 000.'],
                ['type' => 'build', 'prompt' => 'Gapni o‘zbekcha tuzing', 'prompt_en' => 'One ticket, please.', 'answer_text' => 'Bitta chipta bering', 'options' => ['poyezd', 'mana'], 'explanation' => '«Bitta chipta bering».'],
                ['type' => 'fill', 'prompt' => '«Ticket» so‘zini o‘zbekcha yozing', 'prompt_en' => 'Write "ticket" in Uzbek', 'answer_text' => 'chipta', 'explanation' => '«Chipta» — «Ticket».'],
            ],
        ],
    ];
}
