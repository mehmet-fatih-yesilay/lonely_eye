<?php
/**
 * Populate Database with Real Book Data
 * Run this script once to populate the database with curated real books
 */

require_once 'includes/db.php';

echo "🚀 Starting book data population...\n\n";

// Real book data - Turkish and International classics/bestsellers
$books = [
    // Turkish Literature Classics
    ['title' => 'İnce Memed', 'author' => 'Yaşar Kemal', 'year' => 1955, 'pages' => 420, 'genre' => 1, 'desc' => 'Yaşar Kemal\'in başyapıtı. Anadolu\'nun zorlu yaşam koşullarında bir eşkıyanın hikayesi.', 'cover' => 'https://images.isbndb.com/covers/45/20/9789750718045.jpg'],
    ['title' => 'Tutunamayanlar', 'author' => 'Oğuz Atay', 'year' => 1971, 'pages' => 724, 'genre' => 11, 'desc' => 'Modern Türk edebiyatının걸작. Topluma tutunamayan aydınların trajikomik hikayesi.', 'cover' => 'https://images.isbndb.com/covers/78/24/9789750507824.jpg'],
    ['title' => 'Saatleri Ayarlama Enstitüsü', 'author' => 'Ahmet Hamdi Tanpınar', 'year' => 1961, 'pages' => 416, 'genre' => 1, 'desc' => 'Doğu-Batı çatışmasını ironik bir dille anlatan걸작roman.', 'cover' => 'https://images.isbndb.com/covers/12/34/9789750511234.jpg'],
    ['title' => 'Kürk Mantolu Madonna', 'author' => 'Sabahattin Ali', 'year' => 1943, 'pages' => 160, 'genre' => 1, 'desc' => 'Aşk, hayal kırıklığı ve yalnızlık üzerine dokunaklı bir hikaye.', 'cover' => 'https://images.isbndb.com/covers/56/78/9789750505678.jpg'],
    ['title' => 'Çalıkuşu', 'author' => 'Reşat Nuri Güntekin', 'year' => 1922, 'pages' => 384, 'genre' => 1, 'desc' => 'Türk edebiyatının en sevilen romanlarından biri. Feride\'nin hayat hikayesi.', 'cover' => 'https://images.isbndb.com/covers/90/12/9789750509012.jpg'],
    ['title' => 'Sinekli Bakkal', 'author' => 'Halide Edip Adıvar', 'year' => 1936, 'pages' => 352, 'genre' => 1, 'desc' => 'İstanbul\'un eski mahallelerinde geçen duygusal bir aşk hikayesi.', 'cover' => 'https://images.isbndb.com/covers/34/56/9789750503456.jpg'],
    ['title' => 'Huzur', 'author' => 'Ahmet Hamdi Tanpınar', 'year' => 1949, 'pages' => 472, 'genre' => 1, 'desc' => 'İstanbul\'da geçen felsefi ve psikolojik bir aşk romanı.', 'cover' => 'https://images.isbndb.com/covers/78/90/9789750507890.jpg'],
    ['title' => 'Beyaz Kale', 'author' => 'Orhan Pamuk', 'year' => 1985, 'pages' => 176, 'genre' => 1, 'desc' => 'Kimlik ve benzerlik üzerine Osmanlı İstanbul\'unda geçen felsefi roman.', 'cover' => 'https://images.isbndb.com/covers/12/34/9789750512345.jpg'],
    ['title' => 'Masumiyet Müzesi', 'author' => 'Orhan Pamuk', 'year' => 2008, 'pages' => 592, 'genre' => 1, 'desc' => 'İstanbul\'da geçen tutkulu bir aşk hikayesi ve kayıp zamanın peşinde bir yolculuk.', 'cover' => 'https://images.isbndb.com/covers/67/89/9789750516789.jpg'],
    ['title' => 'Kar', 'author' => 'Orhan Pamuk', 'year' => 2002, 'pages' => 463, 'genre' => 1, 'desc' => 'Kars\'ta geçen politik ve felsefi bir roman.', 'cover' => 'https://images.isbndb.com/covers/01/23/9789750510123.jpg'],

    // International Classics
    ['title' => 'Suç ve Ceza', 'author' => 'Fyodor Dostoyevski', 'year' => 1866, 'pages' => 671, 'genre' => 1, 'desc' => 'Rus edebiyatının걸작. Raskolnikov\'un suç sonrası vicdani hesaplaşması.', 'cover' => 'https://images.isbndb.com/covers/45/67/9780140449136.jpg'],
    ['title' => 'Sefiller', 'author' => 'Victor Hugo', 'year' => 1862, 'pages' => 1463, 'genre' => 1, 'desc' => 'Fransız edebiyatının걸작. Jean Valjean\'ın kurtulma ve adalet arayışı.', 'cover' => 'https://images.isbndb.com/covers/89/01/9780140444308.jpg'],
    ['title' => '1984', 'author' => 'George Orwell', 'year' => 1949, 'pages' => 328, 'genre' => 4, 'desc' => 'Totaliter bir toplumda geçen distopik걸작.', 'cover' => 'https://images.isbndb.com/covers/23/45/9780451524935.jpg'],
    ['title' => 'Hayvan Çiftliği', 'author' => 'George Orwell', 'year' => 1945, 'pages' => 112, 'genre' => 4, 'desc' => 'Totalitarizm eleştirisi üzerine alegorik bir roman.', 'cover' => 'https://images.isbndb.com/covers/67/89/9780451526342.jpg'],
    ['title' => 'Yüzüklerin Efendisi', 'author' => 'J.R.R. Tolkien', 'year' => 1954, 'pages' => 1178, 'genre' => 5, 'desc' => 'Fantastik edebiyatın zirvesi. Orta Dünya\'da geçen epik macera.', 'cover' => 'https://images.isbndb.com/covers/01/23/9780544003415.jpg'],
    ['title' => 'Hobbit', 'author' => 'J.R.R. Tolkien', 'year' => 1937, 'pages' => 310, 'genre' => 5, 'desc' => 'Bilbo Baggins\'in beklenmedik yolculuğu.', 'cover' => 'https://images.isbndb.com/covers/45/67/9780547928227.jpg'],
    ['title' => 'Harry Potter ve Felsefe Taşı', 'author' => 'J.K. Rowling', 'year' => 1997, 'pages' => 223, 'genre' => 5, 'desc' => 'Genç büyücü Harry Potter\'ın maceralarının başlangıcı.', 'cover' => 'https://images.isbndb.com/covers/89/01/9780439708180.jpg'],
    ['title' => 'Dune', 'author' => 'Frank Herbert', 'year' => 1965, 'pages' => 688, 'genre' => 4, 'desc' => 'Bilim kurgu edebiyatının걸작. Arrakis gezegeninde geçen epik hikaye.', 'cover' => 'https://images.isbndb.com/covers/23/45/9780441172719.jpg'],
    ['title' => 'Vakıf', 'author' => 'Isaac Asimov', 'year' => 1951, 'pages' => 255, 'genre' => 4, 'desc' => 'Galaktik imparatorluğun çöküşü ve yeniden doğuşu.', 'cover' => 'https://images.isbndb.com/covers/67/89/9780553293357.jpg'],
    ['title' => 'Cesur Yeni Dünya', 'author' => 'Aldous Huxley', 'year' => 1932, 'pages' => 268, 'genre' => 4, 'desc' => 'Distopik bir gelecekte teknoloji ve kontrol.', 'cover' => 'https://images.isbndb.com/covers/01/23/9780060850524.jpg'],

    // Philosophy & Psychology
    ['title' => 'Böyle Buyurdu Zerdüşt', 'author' => 'Friedrich Nietzsche', 'year' => 1883, 'pages' => 352, 'genre' => 8, 'desc' => 'Nietzsche\'nin felsefi걸작. Üstinsan kavramı ve değerlerin yeniden değerlendirilmesi.', 'cover' => 'https://images.isbndb.com/covers/45/67/9780140441185.jpg'],
    ['title' => 'Devlet', 'author' => 'Platon', 'year' => -380, 'pages' => 416, 'genre' => 8, 'desc' => 'Adalet, devlet ve ideal toplum üzerine felsefi diyaloglar.', 'cover' => 'https://images.isbndb.com/covers/89/01/9780872201361.jpg'],
    ['title' => 'Meditasyonlar', 'author' => 'Marcus Aurelius', 'year' => 180, 'pages' => 254, 'genre' => 8, 'desc' => 'Stoacı felsefenin temel metinlerinden. Kişisel düşünceler ve yaşam dersleri.', 'cover' => 'https://images.isbndb.com/covers/23/45/9780140449334.jpg'],
    ['title' => 'İnsan Olmak', 'author' => 'Erich Fromm', 'year' => 1976, 'pages' => 216, 'genre' => 10, 'desc' => 'Sahip olmak ve olmak arasındaki fark üzerine psikolojik inceleme.', 'cover' => 'https://images.isbndb.com/covers/67/89/9780826417459.jpg'],
    ['title' => 'Düşünme Sanatı', 'author' => 'Rolf Dobelli', 'year' => 2011, 'pages' => 384, 'genre' => 10, 'desc' => 'Bilişsel yanılgılar ve daha iyi düşünme yolları.', 'cover' => 'https://images.isbndb.com/covers/01/23/9780062219695.jpg'],

    // Personal Development
    ['title' => 'Simyacı', 'author' => 'Paulo Coelho', 'year' => 1988, 'pages' => 208, 'genre' => 7, 'desc' => 'Kişisel efsaneyi bulma yolculuğu. İlham verici bir masal.', 'cover' => 'https://images.isbndb.com/covers/45/67/9780062315007.jpg'],
    ['title' => 'Atomik Alışkanlıklar', 'author' => 'James Clear', 'year' => 2018, 'pages' => 320, 'genre' => 7, 'desc' => 'Küçük değişikliklerle büyük sonuçlar elde etme rehberi.', 'cover' => 'https://images.isbndb.com/covers/89/01/9780735211292.jpg'],
    ['title' => 'Derin İş', 'author' => 'Cal Newport', 'year' => 2016, 'pages' => 304, 'genre' => 7, 'desc' => 'Dikkat dağınıklığı çağında odaklanma ve verimlilik.', 'cover' => 'https://images.isbndb.com/covers/23/45/9781455586691.jpg'],
    ['title' => 'Alışkanlıkların Gücü', 'author' => 'Charles Duhigg', 'year' => 2012, 'pages' => 371, 'genre' => 7, 'desc' => 'Alışkanlıkların bilimi ve hayatımızı nasıl şekillendirdikleri.', 'cover' => 'https://images.isbndb.com/covers/67/89/9780812981605.jpg'],
    ['title' => 'Bağımsız Zenginlik', 'author' => 'Robert Kiyosaki', 'year' => 1997, 'pages' => 336, 'genre' => 7, 'desc' => 'Finansal okuryazarlık ve zenginlik zihniyeti.', 'cover' => 'https://images.isbndb.com/covers/01/23/9781612680194.jpg'],

    // Mystery & Thriller
    ['title' => 'Sherlock Holmes', 'author' => 'Arthur Conan Doyle', 'year' => 1892, 'pages' => 307, 'genre' => 8, 'desc' => 'Ünlü dedektif Sherlock Holmes\'un maceraları.', 'cover' => 'https://images.isbndb.com/covers/45/67/9780553212419.jpg'],
    ['title' => 'Doğu Ekspresinde Cinayet', 'author' => 'Agatha Christie', 'year' => 1934, 'pages' => 256, 'genre' => 8, 'desc' => 'Hercule Poirot\'nun en ünlü vakalarından biri.', 'cover' => 'https://images.isbndb.com/covers/89/01/9780062693662.jpg'],
    ['title' => 'Da Vinci Şifresi', 'author' => 'Dan Brown', 'year' => 2003, 'pages' => 489, 'genre' => 8, 'desc' => 'Sanat, tarih ve gizem dolu gerilim romanı.', 'cover' => 'https://images.isbndb.com/covers/23/45/9780307474278.jpg'],
    ['title' => 'Kızıl Pazartesi', 'author' => 'Gabriel García Márquez', 'year' => 1981, 'pages' => 120, 'genre' => 8, 'desc' => 'Önceden duyurulan bir cinayetin hikayesi.', 'cover' => 'https://images.isbndb.com/covers/67/89/9781400034925.jpg'],

    // Science
    ['title' => 'Sapiens', 'author' => 'Yuval Noah Harari', 'year' => 2011, 'pages' => 443, 'genre' => 13, 'desc' => 'İnsanlığın kısa tarihi. Homo sapiens\'in evrimi ve geleceği.', 'cover' => 'https://images.isbndb.com/covers/01/23/9780062316097.jpg'],
    ['title' => 'Homo Deus', 'author' => 'Yuval Noah Harari', 'year' => 2015, 'pages' => 448, 'genre' => 13, 'desc' => 'İnsanlığın geleceği ve teknolojinin rolü.', 'cover' => 'https://images.isbndb.com/covers/45/67/9780062464347.jpg'],
    ['title' => 'Kısa Bir Zaman Tarihi', 'author' => 'Stephen Hawking', 'year' => 1988, 'pages' => 256, 'genre' => 13, 'desc' => 'Evrenin yapısı ve kökeni üzerine bilimsel keşifler.', 'cover' => 'https://images.isbndb.com/covers/89/01/9780553380163.jpg'],
    ['title' => 'Kozmos', 'author' => 'Carl Sagan', 'year' => 1980, 'pages' => 365, 'genre' => 13, 'desc' => 'Evren, bilim ve insanlık üzerine ilham verici yolculuk.', 'cover' => 'https://images.isbndb.com/covers/23/45/9780345539434.jpg'],
    ['title' => 'Gen Bencildir', 'author' => 'Richard Dawkins', 'year' => 1976, 'pages' => 360, 'genre' => 13, 'desc' => 'Evrim teorisinin gen merkezli bakış açısı.', 'cover' => 'https://images.isbndb.com/covers/67/89/9780199291151.jpg'],

    // History
    ['title' => 'Nutuk', 'author' => 'Mustafa Kemal Atatürk', 'year' => 1927, 'pages' => 648, 'genre' => 3, 'desc' => 'Türkiye Cumhuriyeti\'nin kuruluş hikayesi.', 'cover' => 'https://images.isbndb.com/covers/01/23/9789751412348.jpg'],
    ['title' => 'Şu Çılgın Türkler', 'author' => 'Turgut Özakman', 'year' => 1995, 'pages' => 1008, 'genre' => 3, 'desc' => 'Çanakkale Savaşı\'nın epik hikayesi.', 'cover' => 'https://images.isbndb.com/covers/45/67/9789753630726.jpg'],
    ['title' => 'Diriliş Çanakkale', 'author' => 'Turgut Özakman', 'year' => 2008, 'pages' => 896, 'genre' => 3, 'desc' => 'Çanakkale Savaşı\'nın detaylı anlatımı.', 'cover' => 'https://images.isbndb.com/covers/89/01/9789753638029.jpg'],
    ['title' => 'Savaş Sanatı', 'author' => 'Sun Tzu', 'year' => -500, 'pages' => 273, 'genre' => 3, 'desc' => 'Antik Çin\'den strateji ve taktik üzerine걸작.', 'cover' => 'https://images.isbndb.com/covers/23/45/9781590302255.jpg'],
    ['title' => 'Suikast', 'author' => 'İlber Ortaylı', 'year' => 2015, 'pages' => 256, 'genre' => 3, 'desc' => 'Osmanlı tarihinde suikastlar ve politik entrikalar.', 'cover' => 'https://images.isbndb.com/covers/67/89/9786053324461.jpg'],

    // Poetry
    ['title' => 'Karacaoğlan', 'author' => 'Karacaoğlan', 'year' => 1600, 'pages' => 180, 'genre' => 2, 'desc' => 'Türk halk şiirinin걸작örnekleri.', 'cover' => 'https://images.isbndb.com/covers/01/23/9789750507123.jpg'],
    ['title' => 'Yunus Emre Divanı', 'author' => 'Yunus Emre', 'year' => 1300, 'pages' => 320, 'genre' => 2, 'desc' => 'Tasavvuf şiirinin en önemli eserlerinden.', 'cover' => 'https://images.isbndb.com/covers/45/67/9789750504567.jpg'],
    ['title' => 'Nazım Hikmet Şiirleri', 'author' => 'Nazım Hikmet', 'year' => 1950, 'pages' => 450, 'genre' => 2, 'desc' => 'Modern Türk şiirinin öncüsü Nazım Hikmet\'in seçme şiirleri.', 'cover' => 'https://images.isbndb.com/covers/89/01/9789750508901.jpg'],
    ['title' => 'Orhan Veli Şiirleri', 'author' => 'Orhan Veli Kanık', 'year' => 1945, 'pages' => 240, 'genre' => 2, 'desc' => 'Garip akımının öncüsü şairin seçme şiirleri.', 'cover' => 'https://images.isbndb.com/covers/23/45/9789750502345.jpg'],

    // Biography
    ['title' => 'Steve Jobs', 'author' => 'Walter Isaacson', 'year' => 2011, 'pages' => 656, 'genre' => 9, 'desc' => 'Apple\'ın kurucusunun kapsamlı biyografisi.', 'cover' => 'https://images.isbndb.com/covers/67/89/9781451648539.jpg'],
    ['title' => 'Elon Musk', 'author' => 'Ashlee Vance', 'year' => 2015, 'pages' => 400, 'genre' => 9, 'desc' => 'Tesla ve SpaceX\'in kurucusunun hikayesi.', 'cover' => 'https://images.isbndb.com/covers/01/23/9780062301239.jpg'],
    ['title' => 'Leonardo da Vinci', 'author' => 'Walter Isaacson', 'year' => 2017, 'pages' => 624, 'genre' => 9, 'desc' => 'Rönesans dehasının yaşamı ve eserleri.', 'cover' => 'https://images.isbndb.com/covers/45/67/9781501139154.jpg'],

    // Adventure
    ['title' => 'Beyaz Diş', 'author' => 'Jack London', 'year' => 1906, 'pages' => 298, 'genre' => 12, 'desc' => 'Vahşi doğada bir kurdun yaşam mücadelesi.', 'cover' => 'https://images.isbndb.com/covers/89/01/9780486264721.jpg'],
    ['title' => 'Denizler Altında Yirmi Bin Fersah', 'author' => 'Jules Verne', 'year' => 1870, 'pages' => 442, 'genre' => 12, 'desc' => 'Kaptan Nemo ve Nautilus denizaltısının maceraları.', 'cover' => 'https://images.isbndb.com/covers/23/45/9780451529961.jpg'],
    ['title' => 'Robinson Crusoe', 'author' => 'Daniel Defoe', 'year' => 1719, 'pages' => 320, 'genre' => 12, 'desc' => 'Issız bir adada hayatta kalma hikayesi.', 'cover' => 'https://images.isbndb.com/covers/67/89/9780141439822.jpg'],
    ['title' => 'Moby Dick', 'author' => 'Herman Melville', 'year' => 1851, 'pages' => 654, 'genre' => 12, 'desc' => 'Beyaz balina Moby Dick\'in peşinde epik deniz macerası.', 'cover' => 'https://images.isbndb.com/covers/01/23/9780142437247.jpg'],

    // More Contemporary
    ['title' => 'Şeker Portakalı', 'author' => 'Jose Mauro de Vasconcelos', 'year' => 1968, 'pages' => 192, 'genre' => 1, 'desc' => 'Brezilyalı bir çocuğun dokunaklı hikayesi.', 'cover' => 'https://images.isbndb.com/covers/45/67/9789750738456.jpg'],
    ['title' => 'Vadideki Zambak', 'author' => 'Honoré de Balzac', 'year' => 1835, 'pages' => 256, 'genre' => 1, 'desc' => 'Platonik aşk üzerine romantik걸작.', 'cover' => 'https://images.isbndb.com/covers/89/01/9780140443462.jpg'],
    ['title' => 'Anna Karenina', 'author' => 'Lev Tolstoy', 'year' => 1877, 'pages' => 864, 'genre' => 1, 'desc' => 'Rus aristokrasisinde aşk, ihanet ve trajedi.', 'cover' => 'https://images.isbndb.com/covers/23/45/9780143035008.jpg'],
    ['title' => 'Savaş ve Barış', 'author' => 'Lev Tolstoy', 'year' => 1869, 'pages' => 1225, 'genre' => 1, 'desc' => 'Napolyon savaşları döneminde Rus toplumu.', 'cover' => 'https://images.isbndb.com/covers/67/89/9780307266934.jpg'],
    ['title' => 'Yüz Yıllık Yalnızlık', 'author' => 'Gabriel García Márquez', 'year' => 1967, 'pages' => 417, 'genre' => 1, 'desc' => 'Büyülü gerçekçiliğin걸작. Buendía ailesinin yedi kuşak hikayesi.', 'cover' => 'https://images.isbndb.com/covers/01/23/9780060883287.jpg'],
    ['title' => 'Aşk Zamanı Kolera Zamanı', 'author' => 'Gabriel García Márquez', 'year' => 1985, 'pages' => 368, 'genre' => 1, 'desc' => 'Elli yıl süren bir aşk hikayesi.', 'cover' => 'https://images.isbndb.com/covers/45/67/9781400034680.jpg'],
    ['title' => 'Küçük Prens', 'author' => 'Antoine de Saint-Exupéry', 'year' => 1943, 'pages' => 96, 'genre' => 1, 'desc' => 'Çocuksu saflık ve yetişkin dünyası üzerine felsefi masal.', 'cover' => 'https://images.isbndb.com/covers/89/01/9780156012195.jpg'],
    ['title' => 'Fareler ve İnsanlar', 'author' => 'John Steinbeck', 'year' => 1937, 'pages' => 107, 'genre' => 1, 'desc' => 'Büyük Buhran döneminde dostluk ve hayaller.', 'cover' => 'https://images.isbndb.com/covers/23/45/9780140177398.jpg'],
    ['title' => 'Gazap Üzümleri', 'author' => 'John Steinbeck', 'year' => 1939, 'pages' => 464, 'genre' => 1, 'desc' => 'Büyük Buhran\'da bir ailenin hayatta kalma mücadelesi.', 'cover' => 'https://images.isbndb.com/covers/67/89/9780143039433.jpg'],
    ['title' => 'Bülbülü Öldürmek', 'author' => 'Harper Lee', 'year' => 1960, 'pages' => 324, 'genre' => 1, 'desc' => 'Amerikan Güneyi\'nde ırkçılık ve adalet.', 'cover' => 'https://images.isbndb.com/covers/01/23/9780061120084.jpg'],
    ['title' => 'Çavdar Tarlasında Çocuklar', 'author' => 'J.D. Salinger', 'year' => 1951, 'pages' => 277, 'genre' => 1, 'desc' => 'Genç Holden Caulfield\'ın yabancılaşma hikayesi.', 'cover' => 'https://images.isbndb.com/covers/45/67/9780316769174.jpg'],
    ['title' => 'Uçurtma Avcısı', 'author' => 'Khaled Hosseini', 'year' => 2003, 'pages' => 371, 'genre' => 1, 'desc' => 'Afganistan\'da dostluk, ihanet ve kefaret.', 'cover' => 'https://images.isbndb.com/covers/89/01/9781594631931.jpg'],
    ['title' => 'Bin Muhteşem Güneş', 'author' => 'Khaled Hosseini', 'year' => 2007, 'pages' => 372, 'genre' => 1, 'desc' => 'Afganistan\'da iki kadının trajik hikayesi.', 'cover' => 'https://images.isbndb.com/covers/23/45/9781594489501.jpg'],
];

// Magazines
$magazines = [
    ['title' => 'National Geographic Türkiye', 'author' => 'Çeşitli Yazarlar', 'year' => 2023, 'pages' => 120, 'genre' => 15, 'desc' => 'Doğa, bilim ve kültür dergisi.', 'cover' => 'https://images.isbndb.com/covers/67/89/9771300000001.jpg'],
    ['title' => 'Bilim ve Teknik', 'author' => 'TÜBİTAK', 'year' => 2023, 'pages' => 96, 'genre' => 15, 'desc' => 'Popüler bilim dergisi.', 'cover' => 'https://images.isbndb.com/covers/01/23/9771300000002.jpg'],
    ['title' => 'Atlas Tarih', 'author' => 'Çeşitli Yazarlar', 'year' => 2023, 'pages' => 84, 'genre' => 15, 'desc' => 'Tarih ve kültür dergisi.', 'cover' => 'https://images.isbndb.com/covers/45/67/9771300000003.jpg'],
    ['title' => 'Popüler Bilim', 'author' => 'Çeşitli Yazarlar', 'year' => 2023, 'pages' => 100, 'genre' => 15, 'desc' => 'Bilim ve teknoloji dergisi.', 'cover' => 'https://images.isbndb.com/covers/89/01/9771300000004.jpg'],
    ['title' => 'Chip Dergisi', 'author' => 'Çeşitli Yazarlar', 'year' => 2023, 'pages' => 110, 'genre' => 15, 'desc' => 'Teknoloji ve bilgisayar dergisi.', 'cover' => 'https://images.isbndb.com/covers/23/45/9771300000005.jpg'],
];

try {
    // Clear existing items (optional - comment out if you want to keep existing data)
    echo "🗑️  Clearing existing book data...\n";
    $pdo->exec("DELETE FROM items WHERE id > 0");
    $pdo->exec("ALTER TABLE items AUTO_INCREMENT = 1");
    echo "✅ Existing data cleared\n\n";

    // Insert books
    echo "📚 Inserting books...\n";
    $stmt = $pdo->prepare("
        INSERT INTO items (type, title, author, description, cover_image, genre_id, publication_year, page_count, view_count, rating_score)
        VALUES ('book', ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $count = 0;
    foreach ($books as $book) {
        $viewCount = rand(100, 5000);
        $rating = round(3 + (rand(0, 200) / 100), 2);

        $stmt->execute([
            $book['title'],
            $book['author'],
            $book['desc'],
            $book['cover'],
            $book['genre'],
            $book['year'],
            $book['pages'],
            $viewCount,
            $rating
        ]);

        $count++;
        echo "  ✓ {$book['title']} - {$book['author']}\n";
    }

    echo "\n✅ {$count} books inserted successfully!\n\n";

    // Insert magazines
    echo "📰 Inserting magazines...\n";
    $stmt = $pdo->prepare("
        INSERT INTO items (type, title, author, description, cover_image, genre_id, publication_year, page_count, view_count, rating_score)
        VALUES ('magazine', ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $magCount = 0;
    foreach ($magazines as $mag) {
        $viewCount = rand(50, 2000);
        $rating = round(3.5 + (rand(0, 150) / 100), 2);

        $stmt->execute([
            $mag['title'],
            $mag['author'],
            $mag['desc'],
            $mag['cover'],
            $mag['genre'],
            $mag['year'],
            $mag['pages'],
            $viewCount,
            $rating
        ]);

        $magCount++;
        echo "  ✓ {$mag['title']}\n";
    }

    echo "\n✅ {$magCount} magazines inserted successfully!\n\n";

    // Summary
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🎉 DATABASE POPULATION COMPLETE!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 Total Books: {$count}\n";
    echo "📊 Total Magazines: {$magCount}\n";
    echo "📊 Grand Total: " . ($count + $magCount) . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>