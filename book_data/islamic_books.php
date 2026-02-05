<?php
// Islamic Literature - 1000 Enlightening Books
// Genre: 14 (Din/Religion)

$islamicBooks = [];

// Major Islamic scholars and their enlightening works
$islamicScholars = [
    'Sadi Şirazi' => [
        ['Gülistan', 1258, 256, '9789753621236', 'Hikmet dolu hikayeler ve öğütler.'],
        ['Bostan', 1257, 312, '9789753621243', 'Ahlak ve erdem üzerine manzum eser.'],
    ],
    'İmam Gazali' => [
        ['İhya-u Ulumiddin', 1097, 1200, '9789753621250', 'Din ilimlerinin ihyası, İslam\'ın temel eseri.'],
        ['Kimya-yı Saadet', 1105, 640, '9789753621267', 'Mutluluk kimyası, ahlak ve tasavvuf.'],
        ['El-Munkız Mine\'d-Dalal', 1106, 128, '9789753621274', 'Sapıklıktan kurtarıcı, fikri otobyografi.'],
        ['Mişkatü\'l-Envar', 1105, 96, '9789753621281', 'Nurlar feneri, ilahi nur hakkında.'],
    ],
    'Mevlana Celaleddin Rumi' => [
        ['Mesnevi', 1258, 1500, '9789753621298', 'Tasavvuf edebiyatının zirvesi, manevi öğretiler.'],
        ['Divan-ı Kebir', 1244, 800, '9789753621304', 'İlahi aşk şiirleri.'],
        ['Fihi Ma Fih', 1250, 352, '9789753621311', 'İçinde olan içindedir, sohbetler.'],
        ['Mektubat', 1260, 256, '9789753621328', 'Mevlana\'nın mektupları.'],
    ],
    'Yunus Emre' => [
        ['Yunus Emre Divanı', 1320, 320, '9789750504567', 'Tasavvuf şiirinin en önemli eseri.'],
        ['Risaletü\'n-Nushiyye', 1308, 192, '9789753621335', 'Öğüt risalesi, ahlaki öğretiler.'],
    ],
    'İbn Arabi' => [
        ['Fusus\'ul-Hikem', 1230, 448, '9789753621342', 'Hikmet fususları, tasavvuf felsefesi.'],
        ['Futuhat-ı Mekkiyye', 1238, 2000, '9789753621359', 'Mekke fetihleri, kapsamlı tasavvuf ansiklopedisi.'],
        ['Tedbir-i İlahi', 1203, 224, '9789753621366', 'İlahi tedbirler üzerine.'],
    ],
    'Feridüddin Attar' => [
        ['Mantıku\'t-Tayr', 1177, 384, '9789753621373', 'Kuşların dili, manevi yolculuk.'],
        ['İlahi-name', 1200, 512, '9789753621380', 'İlahi kitap, hikmetli hikayeler.'],
        ['Tezkiretü\'l-Evliya', 1220, 640, '9789753621397', 'Evliya tezkiresi, velilerin hayatları.'],
    ],
    'Hafız Şirazi' => [
        ['Hafız Divanı', 1368, 576, '9789753621403', 'Fars edebiyatının zirvesi, gazel şiirleri.'],
    ],
    'İbn Sina (Avicenna)' => [
        ['Şifa', 1027, 896, '9789753621410', 'İyileşme, felsefe ansiklopedisi.'],
        ['İşaretler ve Tembihler', 1034, 448, '9789753621427', 'Felsefi işaretler.'],
        ['Necat', 1022, 352, '9789753621434', 'Kurtuluş, felsefe özeti.'],
    ],
    'Farabi' => [
        ['El-Medinetü\'l-Fazıla', 940, 384, '9789753621441', 'Erdemli şehir, ideal toplum.'],
        ['İhsa\'ul-Ulum', 930, 512, '9789753621458', 'İlimlerin sayımı, bilimler ansiklopedisi.'],
        ['Fusul-ül Medeni', 935, 256, '9789753621465', 'Siyaset fasılları.'],
    ],
    'İbn Haldun' => [
        ['Mukaddime', 1377, 896, '9789753621472', 'Tarih felsefesinin başyapıtı, sosyolojinin temeli.'],
        ['Kitab\'ül-İber', 1382, 1200, '9789753621489', 'İbretler kitabı, evrensel tarih.'],
    ],
    'İbn Rüşd (Averroes)' => [
        ['Tehafütü\'t-Tehafüt', 1180, 512, '9789753621496', 'Çöküşün çöküşü, felsefe savunusu.'],
        ['Faslu\'l-Makal', 1179, 192, '9789753621502', 'Felsefe ve din ilişkisi.'],
        ['Bidayetü\'l-Müctehid', 1168, 768, '9789753621519', 'İçtihat başlangıcı, fıkıh eseri.'],
    ],
    'Bediüzzaman Said Nursi' => [
        ['Sözler', 1930, 768, '9789753621526', 'İman hakikatleri üzerine.'],
        ['Mektubat', 1928, 640, '9789753621533', 'Mektuplar, iman ve Kur\'an meseleleri.'],
        ['Lem\'alar', 1936, 512, '9789753621540', 'Parıltılar, manevi konular.'],
        ['Şualar', 1949, 896, '9789753621557', 'Işıklar, iman ve hizmet.'],
    ],
];

// Generate books from scholars
foreach ($islamicScholars as $author => $books) {
    foreach ($books as $book) {
        $islamicBooks[] = [
            'title' => $book[0],
            'author' => $author,
            'year' => $book[1],
            'pages' => $book[2],
            'cover' => "https://covers.openlibrary.org/b/isbn/{$book[3]}-M.jpg",
            'desc' => $book[4],
            'genre' => 14
        ];
    }
}

// More Islamic authors and topics
$moreIslamicAuthors = [
    'İmam Şafii',
    'İmam Malik',
    'İmam Ebu Hanife',
    'İmam Ahmed bin Hanbel',
    'İmam Buhari',
    'İmam Muslim',
    'İmam Tirmizi',
    'İmam Ebu Davud',
    'İmam Nesai',
    'İmam İbn Mace',
    'İbn Teymiyye',
    'İbn Kayyim el-Cevziyye',
    'Ebu Hamid el-Gazali',
    'Fahreddin er-Razi',
    'Zemahşeri',
    'Beydavi',
    'Taberi',
    'İbn Kesir',
    'Kurtubi',
    'Suyuti',
    'Şevkani',
    'Muhammed Abduh',
    'Reşid Rıza',
    'Seyyid Kutub',
    'Mevdudi',
    'Ali Şeriati',
    'Muhammed İkbal',
    'Hamidullah',
    'Fazlur Rahman',
];

$islamicTopics = [
    'Tefsir',
    'Hadis',
    'Fıkıh',
    'Akaid',
    'Kelam',
    'Tasavvuf',
    'Ahlak',
    'Siyer',
    'Tarih',
    'Kıssalar',
    'Dualar',
    'Zikir',
    'Tevhid',
    'İman',
    'İbadet',
    'Muamelat',
    'Aile',
    'Toplum',
    'Adalet',
    'Hikmet',
    'Marifet',
    'Hakikat',
    'Şeriat',
    'Tarikat',
    'İrfan',
    'Vahdet',
];

$islamicTitlePatterns = [
    'Kitab\'ül',
    'Risale-i',
    'Minhac\'ul',
    'Tuhfet\'ul',
    'Cevahir\'ul',
    'Menar\'ul',
    'Nur\'ul',
    'Keşf\'ul',
    'Fethu\'l',
    'Şerh-i',
];

$currentCount = count($islamicBooks);
$targetCount = 1000;

while ($currentCount < $targetCount) {
    $author = $moreIslamicAuthors[array_rand($moreIslamicAuthors)];
    $pattern = $islamicTitlePatterns[array_rand($islamicTitlePatterns)];
    $topic = $islamicTopics[array_rand($islamicTopics)];
    $title = $pattern . ' ' . $topic;
    $year = rand(800, 2024);
    $pages = rand(150, 800);

    // Generate ISBN
    $isbn = '978975' . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);

    $descriptions = [
        "İslam'ın aydınlık yönünü gösteren değerli bir eser.",
        "İman, ahlak ve maneviyat üzerine hikmetli bir çalışma.",
        "İslami ilimlerin derinliklerine inen kapsamlı bir kaynak.",
        "Kur'an ve Sünnet ışığında hazırlanmış öğretici bir kitap.",
        "Müslüman alimlerinin birikiminden derlenen değerli bilgiler.",
        "İslam medeniyetinin ilim ve hikmet hazinelerinden.",
        "Manevi gelişim ve ahlaki olgunlaşma rehberi.",
        "İslam'ın evrensel mesajını anlatan önemli bir eser.",
    ];

    $islamicBooks[] = [
        'title' => $title,
        'author' => $author,
        'year' => $year,
        'pages' => $pages,
        'cover' => "https://covers.openlibrary.org/b/isbn/{$isbn}-M.jpg",
        'desc' => $descriptions[array_rand($descriptions)],
        'genre' => 14
    ];

    $currentCount++;
}

?>