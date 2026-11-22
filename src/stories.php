<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/i18n.php';

/**
 * Returns the curated list of interactive Uzbek folk stories.
 */
function get_all_stories(): array
{
    return [
        [
            'id' => 'zumrad-qimmat',
            'title_uz' => 'Zumrad va Qimmat',
            'title_en' => 'Zumrad and Qimmat',
            'subtitle_uz' => 'Mehnatsevarlik, mehr-oqibat va ezgulik haqidagi go‘zal xalq ertagi.',
            'subtitle_en' => 'A beloved Uzbek folk tale about kindness, patience, and good deeds.',
            'level' => 'A1 Boshlang‘ich',
            'level_en' => 'A1 Beginner',
            'read_time' => '4 daqiqa',
            'read_time_en' => '4 mins',
            'icon' => 'sparkles',
            'gradient' => 'linear-gradient(135deg, #059669 0%, #0d9488 100%)',
            'badge' => 'Oltin Sandiq',
            'paragraphs' => [
                [
                    'uz' => 'Qadim zamonda bir chol va uning mehribon qizi Zumrad yashagan ekan.',
                    'en' => 'Once upon a time, there lived an old man and his kind-hearted daughter Zumrad.',
                    'vocab' => ['qadim' => 'ancient', 'mehribon' => 'kind-hearted', 'chol' => 'old man'],
                ],
                [
                    'uz' => 'Zumrad juda odobli, chaqqon va har doim barchaga yordam berishga tayyor qiz edi.',
                    'en' => 'Zumrad was very polite, nimble, and always ready to help everyone.',
                    'vocab' => ['odobli' => 'polite', 'chaqqon' => 'nimble / active', 'yordam' => 'help'],
                ],
                [
                    'uz' => 'Bir kuni Zumrad o‘rmondagi kichik uyga borib qoldi va u yerda bir momoni uchratdi.',
                    'en' => 'One day, Zumrad arrived at a small house in the forest and met an elderly grandmother there.',
                    'vocab' => ['o‘rmon' => 'forest', 'kichik' => 'small', 'momo' => 'grandmother'],
                ],
                [
                    'uz' => 'Zumrad momoning uyini tozalab, chiroyli dasturxon yozdi va shirin choy damladi.',
                    'en' => 'Zumrad cleaned the grandmother’s home, laid a lovely tablecloth, and brewed sweet tea.',
                    'vocab' => ['dasturxon' => 'tablecloth', 'shirin' => 'sweet / delicious', 'choy' => 'tea'],
                ],
                [
                    'uz' => 'Momo uning odobidan xursand bo‘lib, unga kichik bir qizil sandiqcha sovg‘a qildi.',
                    'en' => 'The grandmother was delighted by her courtesy and gifted her a small red chest.',
                    'vocab' => ['xursand' => 'glad / happy', 'sandiqcha' => 'small chest', 'sovg‘a' => 'gift'],
                ],
                [
                    'uz' => 'Uyga qaytib sandiqni ochishsa, u qimmatbaho javohirlar va go‘zal liboslarga to‘la edi.',
                    'en' => 'When they opened the chest at home, it was full of precious jewels and beautiful garments.',
                    'vocab' => ['javohir' => 'jewels', 'libos' => 'clothes / garments', 'to‘la' => 'full'],
                ],
                [
                    'uz' => 'Ertakdan xulosa: Yaxshilik va halol mehnat doimo baxt keltiradi.',
                    'en' => 'Moral of the tale: Kindness and honest work always bring happiness.',
                    'vocab' => ['xulosa' => 'conclusion / moral', 'mehnat' => 'labor / work', 'baxt' => 'happiness'],
                ],
            ],
            'quiz' => [
                'question' => 'Zumrad qanday qiz edi?',
                'question_en' => 'What kind of girl was Zumrad?',
                'options' => ['Dangasa va jahldor', 'Odobli va mehnatsevar', 'Qo‘rqoq va parishonxol', 'Beparvo'],
                'options_en' => ['Lazy and angry', 'Polite and hardworking', 'Timid and absent-minded', 'Indifferent'],
                'correct' => 1,
                'explanation' => 'Zumrad odobli, chaqqon va har doim yordam berishga tayyor mehnatsevar qiz edi.',
            ],
        ],
        [
            'id' => 'susambil',
            'title_uz' => 'Susambil Mamlakati',
            'title_en' => 'The Kingdom of Susambil',
            'subtitle_uz' => 'Do‘stlik, hamjihatlik va birdamlik haqida qiziqarli xalq ertagi.',
            'subtitle_en' => 'A famous animal fable about friendship, unity, and justice.',
            'level' => 'A1 Boshlang‘ich',
            'level_en' => 'A1 Beginner',
            'read_time' => '5 daqiqa',
            'read_time_en' => '5 mins',
            'icon' => 'sun',
            'gradient' => 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)',
            'badge' => 'Birdamlik Yulduzi',
            'paragraphs' => [
                [
                    'uz' => 'Eski zamonda bir beozor eshak, xo‘roz, it va buqa do‘st bo‘lib yashashgan ekan.',
                    'en' => 'In old times, a gentle donkey, a rooster, a dog, and a bull lived together as good friends.',
                    'vocab' => ['eshak' => 'donkey', 'xo‘roz' => 'rooster', 'it' => 'dog', 'buqa' => 'bull'],
                ],
                [
                    'uz' => 'Ular o‘zlariga tinch va ozod maskan izlab, afsonaviy Susambil yurtiga yo‘l olishdi.',
                    'en' => 'They set out towards the legendary land of Susambil in search of a peaceful and free sanctuary.',
                    'vocab' => ['tinch' => 'peaceful', 'ozod' => 'free', 'maskan' => 'homeland / sanctuary', 'yo‘l' => 'way / path'],
                ],
                [
                    'uz' => 'Yo‘lda ular o‘rmon ichidagi bo‘rilar va ayiqning uyiga duch kelishdi.',
                    'en' => 'On their way, they came across a forest cabin inhabited by wolves and a bear.',
                    'vocab' => ['bo‘ri' => 'wolf', 'ayiq' => 'bear', 'uy' => 'house / cabin'],
                ],
                [
                    'uz' => 'Do‘stlar bir-birlarining yelkasiga chiqib, baland ovozda hayqirishdi va yovuz bo‘rilarni quvib yuborishdi.',
                    'en' => 'The friends stood upon one another’s backs, shouted loudly, and frightened the evil wolves away.',
                    'vocab' => ['yelka' => 'shoulder / back', 'baland' => 'loud / high', 'quvib' => 'chased away'],
                ],
                [
                    'uz' => 'Shundan so‘ng ular Susambilda ahil va xursandchilikda hayot kechirishdi.',
                    'en' => 'From then on, they lived happily and harmoniously together in Susambil.',
                    'vocab' => ['ahil' => 'united / amicable', 'hayot' => 'life'],
                ],
                [
                    'uz' => 'Ertakdan xulosa: Birdamlik bor joyda kuch bor, do‘stlar birgalikda har qanday qiyinchilikni yengadi.',
                    'en' => 'Moral: In unity there is strength; true friends overcome every difficulty together.',
                    'vocab' => ['kuch' => 'strength / power', 'qiyinchilik' => 'difficulty'],
                ],
            ],
            'quiz' => [
                'question' => 'Do‘stlar qayerga qarab yo‘l olishgan edi?',
                'question_en' => 'Where were the animal friends heading?',
                'options' => ['Katta shaharga', 'Susambil yurtiga', 'Dengiz bo‘yiga', 'Tog‘dagi qasrga'],
                'options_en' => ['To the big city', 'To the land of Susambil', 'To the seaside', 'To the mountain castle'],
                'correct' => 1,
                'explanation' => 'Ular tinch va erkin hayot izlab afsonaviy Susambil yurtiga yo‘l olishgan edi.',
            ],
        ],
        [
            'id' => 'oltin-tarvuz',
            'title_uz' => 'Oltin Tarvuz',
            'title_en' => 'The Golden Watermelon',
            'subtitle_uz' => 'Halollik, mehr-shafqat va saxovat haqidagi qadimiy o‘zbek ertagi.',
            'subtitle_en' => 'An ancient Uzbek tale about honesty, mercy, and genuine generosity.',
            'level' => 'A2 O‘rta',
            'level_en' => 'A2 Intermediate',
            'read_time' => '4 daqiqa',
            'read_time_en' => '4 mins',
            'icon' => 'award',
            'gradient' => 'linear-gradient(135deg, #d97706 0%, #b45309 100%)',
            'badge' => 'Saxovat Belgisi',
            'paragraphs' => [
                [
                    'uz' => 'Qishloqda bir kambag‘al dehqon va uning qo‘shnisi ochko‘z boy yashagan ekan.',
                    'en' => 'In a village, there lived a humble poor farmer and his greedy, wealthy neighbor.',
                    'vocab' => ['kambag‘al' => 'poor / humble', 'dehqon' => 'farmer', 'ochko‘z' => 'greedy'],
                ],
                [
                    'uz' => 'Bir kuni dehqon qanoti singan kichik bir laylakni ko‘rib, unga mehr bilan davo qildi.',
                    'en' => 'One day, the farmer found a small stork with a broken wing and lovingly nursed it back to health.',
                    'vocab' => ['laylak' => 'stork', 'qanot' => 'wing', 'mehr' => 'compassion / love'],
                ],
                [
                    'uz' => 'Laylak sog‘ayib ketdi va bahorda uchib kelib, dehqonga uch dona tarvuz urug‘ini qoldirdi.',
                    'en' => 'The stork healed, flew back in the spring, and gifted the farmer three watermelon seeds.',
                    'vocab' => ['bahor' => 'spring', 'urug‘' => 'seed', 'tarvuz' => 'watermelon'],
                ],
                [
                    'uz' => 'Dehqon urug‘larni yerga ekdi va katta oltindek porlagan shirin tarvuzlar yetishtirdi.',
                    'en' => 'The farmer planted the seeds in the earth and harvested huge, golden-glowing sweet watermelons.',
                    'vocab' => ['ekdi' => 'planted', 'porlagan' => 'shining', 'yetishtirdi' => 'grew / harvested'],
                ],
                [
                    'uz' => 'Tarvuzni kesishganda, uning ichidan tilla tangalar va shirin ne’matlar chiqdi.',
                    'en' => 'When they sliced the watermelon, golden coins and bountiful gifts poured forth.',
                    'vocab' => ['tilla' => 'gold', 'ne’mat' => 'bounty / blessings'],
                ],
                [
                    'uz' => 'Ertakdan xulosa: Jonzotlarga qilingan mehr-oqibat va saxovat doimo mukofotlanadi.',
                    'en' => 'Moral: Kindness to living creatures and generosity are always rewarded in life.',
                    'vocab' => ['jonzot' => 'living creature', 'mukofot' => 'reward'],
                ],
            ],
            'quiz' => [
                'question' => 'Dehqon qaysi qushga mehr bilan yordam berdi?',
                'question_en' => 'Which bird did the kind farmer help?',
                'options' => ['Burgutga', 'Laylakka', 'Qaldirg‘ochga', 'Kabutarga'],
                'options_en' => ['An eagle', 'A stork', 'A swallow', 'A pigeon'],
                'correct' => 1,
                'explanation' => 'Dehqon qanoti singan kichik laylakni parvarish qilib davoladi.',
            ],
        ],
        [
            'id' => 'afandi-donishmand',
            'title_uz' => 'Afandi va Donishmand',
            'title_en' => 'Afandi and the Scholar',
            'subtitle_uz' => 'Nasriddin Afandining zukkoligi va o‘tkir aql-idroki haqidagi ibratli latifa.',
            'subtitle_en' => 'A witty and instructive folk tale showcasing the brilliant humor of Nasriddin Afandi.',
            'level' => 'A2 O‘rta',
            'level_en' => 'A2 Intermediate',
            'read_time' => '3 daqiqa',
            'read_time_en' => '3 mins',
            'icon' => 'zap',
            'gradient' => 'linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%)',
            'badge' => 'Donolik Toji',
            'paragraphs' => [
                [
                    'uz' => 'Bir kuni uzoq o‘lkalardan kelgan mag‘rur bir donishmand Afandi bilan bahslashmoqchi bo‘ldi.',
                    'en' => 'One day, an arrogant scholar from distant lands came to debate with Nasriddin Afandi.',
                    'vocab' => ['mag‘rur' => 'proud / arrogant', 'donishmand' => 'scholar / sage', 'bahs' => 'debate'],
                ],
                [
                    'uz' => 'Donishmand so‘radi: «Afandi, ayting-chi, osmonda nechta yulduz bor?»',
                    'en' => 'The scholar asked: "Afandi, tell me, how many stars are there in the sky?"',
                    'vocab' => ['osmon' => 'sky', 'yulduz' => 'star'],
                ],
                [
                    'uz' => 'Afandi darhol eshagini ko‘rsatib: «Eshagimning ustida qancha tuk bo‘lsa, osmonda shuncha yulduz bor», dedi.',
                    'en' => 'Afandi pointed at his donkey at once and replied: "There are as many stars as hairs upon my donkey."',
                    'vocab' => ['tuk' => 'hair / fur', 'ustida' => 'upon / on'],
                ],
                [
                    'uz' => 'Donishmand hayron bo‘lib: «Buni qayerdan bilasiz?» dedi.',
                    'en' => 'The scholar was astonished and asked: "How can you possibly prove that?"',
                    'vocab' => ['hayron' => 'astonished', 'qayerdan' => 'from where'],
                ],
                [
                    'uz' => 'Afandi kulimsirab: «Ishonmasangiz, birma-bir sanab ko‘ring!» deb javob berdi.',
                    'en' => 'Afandi smiled and answered: "If you do not believe me, count them one by one!"',
                    'vocab' => ['kulimsirab' => 'smiling', 'sanab' => 'counting', 'javob' => 'answer'],
                ],
                [
                    'uz' => 'Donishmand Afandining tezkor va zukko aqliga qoyil qolib, unga ehtirom bildirdi.',
                    'en' => 'The scholar marveled at Afandi’s sharp wit and expressed his deep respect.',
                    'vocab' => ['zukko' => 'witty / clever', 'ehtirom' => 'respect'],
                ],
            ],
            'quiz' => [
                'question' => 'Afandi yulduzlar sonini nimaga qiyosladi?',
                'question_en' => 'What did Afandi compare the number of stars to?',
                'options' => ['Dengizdagi qumga', 'Eshagining tuklariga', 'Daraxtdagi barglarga', 'Bozordagi olmalarga'],
                'options_en' => ['Sand in the sea', 'The hairs on his donkey', 'Leaves on trees', 'Apples at the bazaar'],
                'correct' => 1,
                'explanation' => 'Afandi donishmandga o‘tkir aql bilan eshagining tuklari qancha bo‘lsa, shuncha yulduz borligini aytdi.',
            ],
        ],
    ];
}

/**
 * Finds a single story by ID.
 */
function find_story(string $id): ?array
{
    foreach (get_all_stories() as $story) {
        if ($story['id'] === $id) {
            return $story;
        }
    }
    return null;
}
