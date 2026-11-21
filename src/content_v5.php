<?php

declare(strict_types=1);

/**
 * Schema v5 content: English versions of course texts and lesson objectives /
 * culture notes for the English interface. Matched by the Uzbek title and only
 * written into empty *_en columns, so admin edits are never overwritten.
 */
function seed_v5_content(PDO $pdo): void
{
    $courses = [
        'O‘zbek tili: Ilk qadamlar' => [
            'Uzbek: First steps',
            'Greetings, family, numbers, colours and fruit',
            'An interactive, playful course for complete beginners — kids and adults alike. Every lesson: a micro-scene, audio vocabulary, reading, a game, practice and a test.',
        ],
        'Ikki madaniyat ko‘prigi' => [
            'Bridge of two cultures',
            'Plov, bread, Navruz, ancient cities and good manners',
            'Discover Uzbek culture — food, holidays, hospitality, ancient cities and clothing — compared with American and other cultures. Fun for families abroad and travellers alike.',
        ],
        'Sayohatchilar uchun o‘zbek tili' => [
            'Uzbek for travellers',
            'Bazaar, taxi, teahouse, hotel',
            'The phrases you need most on a trip to Uzbekistan: greetings, asking prices and bargaining, giving directions to a taxi driver, ordering at a teahouse, the hotel and asking for help.',
        ],
    ];
    $upd = $pdo->prepare("
        UPDATE courses SET
            title_en = COALESCE(NULLIF(title_en, ''), ?),
            subtitle_en = COALESCE(NULLIF(subtitle_en, ''), ?),
            description_en = COALESCE(NULLIF(description_en, ''), ?)
        WHERE title = ?
    ");
    foreach ($courses as $title => [$t, $s, $d]) {
        $upd->execute([$t, $s, $d, $title]);
    }

    $lessons = [
        'Salomlashish va O‘zini Tanishtirish' => ['Understand and say “Salom”, “Xush kelibsiz”, “Ismingiz nima?” and “Mening ismim…”.', 'A hand on the heart',
            'In Uzbekistan people greet elders by placing the right hand on the chest and bowing slightly — a sign of respect. In America people usually shake hands or give a high five.'],
        'Mening Ahil Oilam' => ['Name family members in Uzbek: father, mother, brothers, sisters, grandparents.', 'Big family, big happiness',
            'In Uzbek families grandparents often live with or very close to their grandchildren. Sunday is the day the whole family gathers around one table.'],
        'Sehrli Raqamlar (1 dan 10 gacha)' => ['Count from 1 to 10 in Uzbek and say how many things there are.', 'Counting at the bazaar',
            'At Uzbek bazaars fruit is usually sold by the kilo, and sellers often add one extra apple “for luck” — they call it “barakasi”.'],
        'Ranglar va Shakllar' => ['Know the basic colours: red, blue, green, yellow, white, black.', 'Blue domes',
            'The domes of the Registan and the Gur-e-Amir mausoleum in Samarkand are turquoise blue. Masters saw this colour as a symbol of the sky and of peace.'],
        'Mazzali Meva va Sabzavotlar' => ['Name favourite fruit and vegetables and say “Menga … yoqadi” (I like …).', 'Home of melons',
            'Uzbekistan has more than 150 varieties of melon! Mirzachul melons are so sweet that long ago they were sent as gifts to the caliphs of Baghdad.'],
        'Osh — dasturxon shohi' => ['Name the ingredients of plov in Uzbek and know when it is cooked.', 'Plov — UNESCO heritage',
            'In 2016 Uzbek plov was added to UNESCO’s list of Intangible Cultural Heritage. Just as turkey is cooked for Thanksgiving in America, plov is always served at weddings and holidays in Uzbekistan.'],
        'Non, somsa va choy' => ['Know the words bread, samsa, tea and tea bowl, and why bread is respected.', 'Bread is sacred',
            'In an Uzbek home bread is never thrown away or placed upside down. When guests arrive, bread is broken first. In America sandwich bread comes from a store — in Uzbekistan it comes hot from the tandoor!'],
        'Navro‘z — bahor bayrami' => ['Know when Navruz is celebrated, the words sumalak and spring, and compare it with American holidays.', 'Navruz and Thanksgiving',
            'On Thanksgiving American families gather to give thanks. Navruz is similar: the whole neighbourhood cooks sumalak together and makes wishes around the cauldron. Both holidays are about unity and gratitude.'],
        'Mehmondo‘stlik va odob' => ['Know the phrases for welcoming guests and how to show respect to elders.', 'Tea is poured a little',
            'In Uzbekistan a guest’s tea bowl is filled only a little — it means “stay longer, I’ll pour you more”. In America the glass is filled up. Both customs show care for the guest!'],
        'Samarqand, Buxoro, Xiva' => ['Recognise Uzbekistan’s three ancient cities and their famous sights.', 'The Great Silk Road',
            'Thousands of years ago caravans carried silk, spices and paper from China to Europe along a road through Samarkand, Bukhara and Khiva. That is why they are called the “pearls of the East”.'],
        'Do‘ppi, atlas va milliy o‘yinlar' => ['Know the names of national clothes and the games Uzbek kids play.', 'The pepper on the doppi',
            'The white pattern on a Chust doppi is shaped like a pepper pod, believed to protect from the evil eye. The doppi is as popular in Uzbekistan as the baseball cap is in America!'],
        'Birinchi kun: salom va rahmat' => ['Greet people, say thank you, apologise and say “I didn’t understand”.', 'Hand on heart',
            'Placing the right hand on the chest when greeting is a warm sign of respect in Uzbekistan. Men usually shake hands; if a woman does not offer her hand, a hand on the heart and a nod is perfect.'],
        'Bozorda: necha pul?' => ['Ask the price, say “too expensive” and bargain politely.', 'Bargaining is an art',
            'At Uzbek bazaars prices are often negotiable. Asking “Arzonroq bo‘ladimi?” with a smile is not rude — sellers enjoy it. Supermarket prices are fixed. Smile, bargain and say “rahmat” either way.'],
        'Taksida: o‘ngga, chapga, to‘xtang' => ['Tell a driver your destination and give directions.', '“Aka” and “opa”',
            'In Uzbekistan you call an unknown man “aka” (older brother) and a woman “opa” (older sister). Say “Rahmat, aka!” to your driver and you’ll make a friend instantly.'],
        'Choyxonada: osh va choy' => ['Order food, ask for water or tea and ask for the bill.', 'Tea is poured back three times',
            'At a teahouse, freshly brewed tea is poured into a bowl and back into the pot three times so it brews well — don’t be surprised! In many places a 10–15% service charge is already on the bill.'],
        'Mehmonxona va yordam' => ['Ask for a room, ask for directions and call for help if needed.', 'Emergency numbers',
            'In Uzbekistan: 101 — fire, 102 — police, 103 — ambulance, 112 — the single emergency number from any phone. People are very willing to help — don’t be shy to say “Yordam bering!”.'],
        'Raqamlar, pul va vaqt' => ['Understand big numbers (hundred, thousand) and ask the time.', 'The Afrosiyob train',
            'The Afrosiyob high-speed train takes you from Tashkent to Samarkand in about 2 hours. Book tickets early — they sell out fast in spring and autumn. Prices in sum have many zeros: “ming” (thousand) is the word you’ll hear most.'],
    ];
    $upd = $pdo->prepare("
        UPDATE lessons SET
            objective_en = COALESCE(NULLIF(objective_en, ''), ?),
            culture_title_en = COALESCE(NULLIF(culture_title_en, ''), ?),
            culture_note_en = COALESCE(NULLIF(culture_note_en, ''), ?)
        WHERE title = ?
    ");
    foreach ($lessons as $title => [$o, $ct, $cn]) {
        $upd->execute([$o, $ct, $cn, $title]);
    }
}
