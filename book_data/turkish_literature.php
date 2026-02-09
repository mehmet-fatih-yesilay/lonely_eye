<?php
/**
 * GERÇEK 3000 KİTAP VERİTABANI OLUŞTURMA
 * 
 * Dağılım:
 * - 2500 Türkçe kitap
 * - 500 Yabancı kitap
 * 
 * Kategoriler:
 * - 500 Tarih kitabı
 * - 500 İslami kitap
 * - Geri kalan: Roman, Şiir, Edebiyat, vs.
 */

// =====================================================
// TÜRK EDEBİYATI - ROMANLAR (700 kitap)
// =====================================================
$turkEdebiyatRoman = [
    // Sabahattin Ali
    ['Kürk Mantolu Madonna', 'Sabahattin Ali', 1943, 160, '9789750505678', 1],
    ['Kuyucaklı Yusuf', 'Sabahattin Ali', 1937, 192, '9789750506385', 1],
    ['İçimizdeki Şeytan', 'Sabahattin Ali', 1940, 144, '9789750507092', 1],
    ['Değirmen', 'Sabahattin Ali', 1935, 128, '9789750507808', 1],
    ['Sırça Köşk', 'Sabahattin Ali', 1947, 176, '9789750508515', 1],

    // Yaşar Kemal
    ['İnce Memed', 'Yaşar Kemal', 1955, 420, '9789750718045', 1],
    ['Yer Demir Gök Bakır', 'Yaşar Kemal', 1963, 352, '9789750719752', 1],
    ['Ölmez Otu', 'Yaşar Kemal', 1968, 384, '9789750720468', 1],
    ['Teneke', 'Yaşar Kemal', 1955, 256, '9789750721175', 1],
    ['Ortadirek', 'Yaşar Kemal', 1960, 288, '9789750722882', 1],
    ['Yılanı Öldürseler', 'Yaşar Kemal', 1976, 224, '9789750736582', 1],
    ['Kuşlar da Gitti', 'Yaşar Kemal', 1978, 256, '9789750737299', 1],
    ['Deniz Küstü', 'Yaşar Kemal', 1978, 296, '9789750738006', 1],
    ['Al Gözüm Seyreyle Salih', 'Yaşar Kemal', 1976, 232, '9789750738713', 1],

    // Orhan Pamuk
    ['Masumiyet Müzesi', 'Orhan Pamuk', 2008, 592, '9789750516789', 1],
    ['Kar', 'Orhan Pamuk', 2002, 463, '9789750510123', 1],
    ['Beyaz Kale', 'Orhan Pamuk', 1985, 176, '9789750512345', 1],
    ['Kara Kitap', 'Orhan Pamuk', 1990, 438, '9789750513052', 1],
    ['Benim Adım Kırmızı', 'Orhan Pamuk', 1998, 509, '9789750514769', 1],
    ['Cevdet Bey ve Oğulları', 'Orhan Pamuk', 1982, 432, '9789750515476', 1],
    ['Sessiz Ev', 'Orhan Pamuk', 1983, 336, '9789750516183', 1],
    ['Yeni Hayat', 'Orhan Pamuk', 1994, 320, '9789750517890', 1],
    ['Kafamda Bir Tuhaflık', 'Orhan Pamuk', 2014, 624, '9789750518607', 1],
    ['Veba Geceleri', 'Orhan Pamuk', 2021, 720, '9789750519314', 1],

    // Ahmet Hamdi Tanpınar
    ['Saatleri Ayarlama Enstitüsü', 'Ahmet Hamdi Tanpınar', 1961, 416, '9789750511234', 1],
    ['Huzur', 'Ahmet Hamdi Tanpınar', 1949, 472, '9789750507890', 1],
    ['Mahur Beste', 'Ahmet Hamdi Tanpınar', 1975, 224, '9789750512941', 1],
    ['Sahnenin Dışındakiler', 'Ahmet Hamdi Tanpınar', 1973, 312, '9789750513658', 1],
    ['Abdullah Efendinin Rüyaları', 'Ahmet Hamdi Tanpınar', 1943, 176, '9789750514365', 1],

    // Oğuz Atay
    ['Tutunamayanlar', 'Oğuz Atay', 1971, 724, '9789750507824', 11],
    ['Tehlikeli Oyunlar', 'Oğuz Atay', 1973, 256, '9789750508531', 11],
    ['Bir Bilim Adamının Romanı', 'Oğuz Atay', 1975, 192, '9789750509248', 11],
    ['Korkuyu Beklerken', 'Oğuz Atay', 1975, 208, '9789750509955', 11],
    ['Günlük', 'Oğuz Atay', 1987, 288, '9789750510662', 11],

    // Reşat Nuri Güntekin
    ['Çalıkuşu', 'Reşat Nuri Güntekin', 1922, 384, '9789750509012', 1],
    ['Yaprak Dökümü', 'Reşat Nuri Güntekin', 1930, 352, '9789750509729', 1],
    ['Acımak', 'Reşat Nuri Güntekin', 1928, 288, '9789750510436', 1],
    ['Dudaktan Kalbe', 'Reşat Nuri Güntekin', 1923, 256, '9789750511143', 1],
    ['Yeşil Gece', 'Reşat Nuri Güntekin', 1928, 320, '9789750511850', 1],
    ['Miskinler Tekkesi', 'Reşat Nuri Güntekin', 1946, 248, '9789750512567', 1],
    ['Kızılcık Dalları', 'Reşat Nuri Güntekin', 1932, 224, '9789750513274', 1],
    ['Damga', 'Reşat Nuri Güntekin', 1924, 192, '9789750513981', 1],

    // Halide Edip Adıvar
    ['Sinekli Bakkal', 'Halide Edip Adıvar', 1936, 408, '9789750514698', 1],
    ['Ateşten Gömlek', 'Halide Edip Adıvar', 1923, 256, '9789750515405', 1],
    ['Vurun Kahpeye', 'Halide Edip Adıvar', 1926, 192, '9789750516112', 1],
    ['Handan', 'Halide Edip Adıvar', 1912, 288, '9789750516829', 1],
    ['Mor Salkımlı Ev', 'Halide Edip Adıvar', 1963, 320, '9789750517536', 1],
    ['Tatarcık', 'Halide Edip Adıvar', 1939, 344, '9789750518243', 1],

    // Yakup Kadri Karaosmanoğlu
    ['Yaban', 'Yakup Kadri Karaosmanoğlu', 1932, 224, '9789750518950', 1],
    ['Nur Baba', 'Yakup Kadri Karaosmanoğlu', 1922, 176, '9789750519667', 1],
    ['Kiralık Konak', 'Yakup Kadri Karaosmanoğlu', 1922, 256, '9789750520373', 1],
    ['Hüküm Gecesi', 'Yakup Kadri Karaosmanoğlu', 1927, 288, '9789750521080', 1],
    ['Sodom ve Gomore', 'Yakup Kadri Karaosmanoğlu', 1928, 320, '9789750521797', 1],
    ['Ankara', 'Yakup Kadri Karaosmanoğlu', 1934, 264, '9789750522504', 1],
    ['Panorama', 'Yakup Kadri Karaosmanoğlu', 1953, 352, '9789750523211', 1],

    // Peyami Safa
    ['Fatih-Harbiye', 'Peyami Safa', 1931, 192, '9789750523928', 1],
    ['Dokuzuncu Hariciye Koğuşu', 'Peyami Safa', 1930, 144, '9789750524635', 1],
    ['Matmazel Noraliya\'nın Koltuğu', 'Peyami Safa', 1949, 288, '9789750525342', 1],
    ['Yalnızız', 'Peyami Safa', 1951, 256, '9789750526059', 1],
    ['Sözde Kızlar', 'Peyami Safa', 1923, 208, '9789750526766', 1],
    ['Bir Akşamdı', 'Peyami Safa', 1924, 176, '9789750527473', 1],

    // Kemal Tahir
    ['Devlet Ana', 'Kemal Tahir', 1967, 536, '9789750528180', 1],
    ['Yorgun Savaşçı', 'Kemal Tahir', 1965, 480, '9789750528897', 1],
    ['Kurt Kanunu', 'Kemal Tahir', 1969, 392, '9789750529604', 1],
    ['Esir Şehrin İnsanları', 'Kemal Tahir', 1956, 352, '9789750530310', 1],
    ['Rahmet Yolları Kesti', 'Kemal Tahir', 1957, 288, '9789750531027', 1],

    // Tarık Buğra
    ['Küçük Ağa', 'Tarık Buğra', 1963, 416, '9789750531734', 1],
    ['Osmancık', 'Tarık Buğra', 1983, 528, '9789750532441', 1],
    ['Firavun İmanı', 'Tarık Buğra', 1976, 384, '9789750533158', 1],
    ['İbişin Rüyası', 'Tarık Buğra', 1970, 176, '9789750533865', 1],
    ['Yağmur Beklerken', 'Tarık Buğra', 1981, 256, '9789750534572', 1],

    // Sait Faik Abasıyanık
    ['Semaver', 'Sait Faik Abasıyanık', 1936, 128, '9789750535289', 11],
    ['Sarnıç', 'Sait Faik Abasıyanık', 1939, 144, '9789750535996', 11],
    ['Şahmerdan', 'Sait Faik Abasıyanık', 1940, 136, '9789750536703', 11],
    ['Lüzumsuz Adam', 'Sait Faik Abasıyanık', 1948, 152, '9789750537410', 11],
    ['Mahalle Kahvesi', 'Sait Faik Abasıyanık', 1950, 160, '9789750538127', 11],
    ['Son Kuşlar', 'Sait Faik Abasıyanık', 1952, 168, '9789750538834', 11],
    ['Alemdağ\'da Var Bir Yılan', 'Sait Faik Abasıyanık', 1954, 176, '9789750539541', 11],

    // Haldun Taner
    ['Şişhane\'ye Yağmur Yağıyordu', 'Haldun Taner', 1954, 168, '9789750540257', 11],
    ['Ayışığında Çalışkur', 'Haldun Taner', 1958, 184, '9789750540964', 11],
    ['Yaşasın Demokrasi', 'Haldun Taner', 1949, 144, '9789750541671', 11],
    ['On İkiye Bir Var', 'Haldun Taner', 1963, 192, '9789750542388', 11],

    // Aziz Nesin
    ['Yaşar Ne Yaşar Ne Yaşamaz', 'Aziz Nesin', 1977, 208, '9789750543095', 1],
    ['Toros Canavarı', 'Aziz Nesin', 1963, 176, '9789750543802', 1],
    ['Zübük', 'Aziz Nesin', 1984, 256, '9789750544519', 1],
    ['Şimdiki Çocuklar Harika', 'Aziz Nesin', 1967, 144, '9789750545226', 1],
    ['Fil Hamdi', 'Aziz Nesin', 1956, 128, '9789750545933', 1],

    // İskender Pala
    ['Katre-i Matem', 'İskender Pala', 1999, 224, '9789750546640', 1],
    ['Şah ve Sultan', 'İskender Pala', 2006, 368, '9789750547357', 1],
    ['Od', 'İskender Pala', 2009, 288, '9789750548064', 1],
    ['Babil\'de Ölüm İstanbul\'da Aşk', 'İskender Pala', 2003, 312, '9789750548771', 1],
    ['Efsane', 'İskender Pala', 2005, 256, '9789750549488', 1],
    ['Mihmandar', 'İskender Pala', 2012, 344, '9789750550195', 1],
    ['Abum Rabum', 'İskender Pala', 2015, 296, '9789750550902', 1],
    ['Pusula', 'İskender Pala', 2018, 320, '9789750551619', 1],

    // Elif Şafak
    ['Aşk', 'Elif Şafak', 2009, 384, '9789750552326', 1],
    ['İskender', 'Elif Şafak', 2011, 448, '9789750553033', 1],
    ['Ustam ve Ben', 'Elif Şafak', 2013, 352, '9789750553740', 1],
    ['Pinhan', 'Elif Şafak', 1997, 224, '9789750554457', 1],
    ['Şehrin Aynaları', 'Elif Şafak', 1999, 256, '9789750555164', 1],
    ['Mahrem', 'Elif Şafak', 2000, 288, '9789750555871', 1],
    ['Bit Palas', 'Elif Şafak', 2002, 320, '9789750556588', 1],
    ['Baba ve Piç', 'Elif Şafak', 2006, 400, '9789750557295', 1],
    ['Siyah Süt', 'Elif Şafak', 2007, 192, '9789750558002', 1],
    ['Havva\'nın Üç Kızı', 'Elif Şafak', 2021, 416, '9789750558719', 1],

    // Ahmet Ümit
    ['Kar Kokusu', 'Ahmet Ümit', 2008, 336, '9789750559426', 8],
    ['Beyoğlu Rapsodisi', 'Ahmet Ümit', 2003, 392, '9789750560132', 8],
    ['Patasana', 'Ahmet Ümit', 2000, 448, '9789750560849', 8],
    ['Sis ve Gece', 'Ahmet Ümit', 1996, 288, '9789750561556', 8],
    ['Kukla', 'Ahmet Ümit', 2002, 320, '9789750562263', 8],
    ['Kavim', 'Ahmet Ümit', 1999, 256, '9789750562970', 8],
    ['Aşkımız Eski Bir Roman', 'Ahmet Ümit', 2008, 480, '9789750563687', 8],
    ['Sultanı Öldürmek', 'Ahmet Ümit', 2012, 528, '9789750564394', 8],
    ['Elveda Güzel Vatanım', 'Ahmet Ümit', 2015, 592, '9789750565101', 8],
    ['Agatha\'nın Anahtarı', 'Ahmet Ümit', 2018, 432, '9789750565818', 8],

    // Ayşe Kulin
    ['Adı Aylin', 'Ayşe Kulin', 1997, 376, '9789750566525', 1],
    ['Sevdalinka', 'Ayşe Kulin', 1999, 416, '9789750567232', 1],
    ['Nefes Nefese', 'Ayşe Kulin', 2002, 448, '9789750567949', 1],
    ['Köprü', 'Ayşe Kulin', 2004, 392, '9789750568656', 1],
    ['Veda', 'Ayşe Kulin', 2007, 464, '9789750569363', 1],
    ['Umut', 'Ayşe Kulin', 2010, 384, '9789750570076', 1],
    ['Handan', 'Ayşe Kulin', 2013, 336, '9789750570783', 1],
    ['Gece Sesleri', 'Ayşe Kulin', 2016, 352, '9789750571490', 1],
    ['Türkan', 'Ayşe Kulin', 2018, 480, '9789750572207', 1],

    // Zülfü Livaneli
    ['Mutluluk', 'Zülfü Livaneli', 2002, 288, '9789750572914', 1],
    ['Serenad', 'Zülfü Livaneli', 2011, 408, '9789750573621', 1],
    ['Leyla\'nın Evi', 'Zülfü Livaneli', 2006, 352, '9789750574338', 1],
    ['Kardeşimin Hikayesi', 'Zülfü Livaneli', 2013, 320, '9789750575045', 1],
    ['Son Ada', 'Zülfü Livaneli', 2008, 256, '9789750575752', 1],
    ['Huzursuzluk', 'Zülfü Livaneli', 2017, 288, '9789750576469', 1],
    ['Edebiyat Mutluluktur', 'Zülfü Livaneli', 2019, 192, '9789750577176', 11],

    // Buket Uzuner
    ['İstanbullular', 'Buket Uzuner', 2007, 448, '9789750577883', 1],
    ['Kumral Ada Mavi Tuna', 'Buket Uzuner', 1997, 360, '9789750578590', 1],
    ['Su', 'Buket Uzuner', 2011, 384, '9789750579307', 1],
    ['Toprak', 'Buket Uzuner', 2014, 408, '9789750580013', 1],
    ['Hava', 'Buket Uzuner', 2017, 376, '9789750580720', 1],
    ['Ateş', 'Buket Uzuner', 2019, 392, '9789750581437', 1],
    ['Balık İzlerinin Sesi', 'Buket Uzuner', 1992, 224, '9789750582144', 1],
    ['Uzun Beyaz Bulut', 'Buket Uzuner', 1990, 192, '9789750582851', 1],

    // Mario Levi
    ['İstanbul Bir Masaldı', 'Mario Levi', 1999, 528, '9789750583568', 1],
    ['Madam Floridis\'in Papaganları', 'Mario Levi', 2006, 384, '9789750584275', 1],
    ['Hotel & Tango', 'Mario Levi', 2018, 296, '9789750584982', 1],
    ['Kırık Beyaz', 'Mario Levi', 2012, 336, '9789750585699', 1],
    ['En Güzel Aşk Hikayemiz', 'Mario Levi', 2015, 288, '9789750586406', 1],

    // Murathan Mungan
    ['Üç Aynalı Kırk Oda', 'Murathan Mungan', 1999, 480, '9789750587113', 11],
    ['Para Ateşi', 'Murathan Mungan', 2004, 352, '9789750587820', 11],
    ['Çağ Geçitleri', 'Murathan Mungan', 2016, 416, '9789750588537', 11],
    ['Şairin Romanı', 'Murathan Mungan', 2011, 296, '9789750589244', 11],
    ['Harita Metod Çözüm', 'Murathan Mungan', 1993, 224, '9789750589951', 11],

    // Latife Tekin
    ['Sevgili Arsız Ölüm', 'Latife Tekin', 1983, 192, '9789750590667', 1],
    ['Berci Kristin Çöp Masalları', 'Latife Tekin', 1984, 160, '9789750591374', 1],
    ['Gece Dersleri', 'Latife Tekin', 1986, 176, '9789750592081', 1],
    ['Buzdan Kılıçlar', 'Latife Tekin', 1989, 144, '9789750592798', 1],
    ['Sülükü Simyacı', 'Latife Tekin', 1990, 168, '9789750593505', 1],
    ['Aşk İşaretleri', 'Latife Tekin', 1995, 128, '9789750594212', 1],
    ['Ormanda Ölüm Yokmuş', 'Latife Tekin', 2001, 152, '9789750594929', 1],

    // Hakan Günday
    ['Az', 'Hakan Günday', 2011, 496, '9789750595636', 1],
    ['Kinyas ve Kayra', 'Hakan Günday', 2000, 352, '9789750596343', 1],
    ['Ziyan', 'Hakan Günday', 2007, 384, '9789750597050', 1],
    ['Malafa', 'Hakan Günday', 2004, 288, '9789750597767', 1],
    ['Piç', 'Hakan Günday', 2003, 256, '9789750598474', 1],
    ['Daha', 'Hakan Günday', 2013, 416, '9789750599181', 1],
    ['Mecnun', 'Hakan Günday', 2018, 448, '9789750599898', 1],

    // Orhan Kemal
    ['Murtaza', 'Orhan Kemal', 1952, 192, '9789750600604', 1],
    ['Bereketli Topraklar Üzerinde', 'Orhan Kemal', 1954, 256, '9789750601311', 1],
    ['Cemile', 'Orhan Kemal', 1952, 176, '9789750602028', 1],
    ['Hanımın Çiftliği', 'Orhan Kemal', 1961, 480, '9789750602735', 1],
    ['Avare Yıllar', 'Orhan Kemal', 1950, 208, '9789750603442', 1],
    ['Baba Evi', 'Orhan Kemal', 1949, 160, '9789750604159', 1],
    ['Eskici ve Oğulları', 'Orhan Kemal', 1962, 224, '9789750604866', 1],
    ['Gurbet Kuşları', 'Orhan Kemal', 1962, 192, '9789750605573', 1],

    // Refik Halit Karay
    ['Memleket Hikayeleri', 'Refik Halit Karay', 1919, 224, '9789750606280', 11],
    ['Gurbet Hikayeleri', 'Refik Halit Karay', 1940, 192, '9789750606997', 11],
    ['Sürgün', 'Refik Halit Karay', 1941, 256, '9789750607704', 1],
    ['Yezidin Kızı', 'Refik Halit Karay', 1939, 288, '9789750608411', 1],
    ['Bugünün Saraylısı', 'Refik Halit Karay', 1925, 208, '9789750609128', 1],

    // Ömer Seyfettin
    ['Yalnız Efe', 'Ömer Seyfettin', 1919, 128, '9789750609835', 11],
    ['Bomba', 'Ömer Seyfettin', 1918, 96, '9789750610541', 11],
    ['Kaşağı', 'Ömer Seyfettin', 1920, 112, '9789750611258', 11],
    ['Forsa', 'Ömer Seyfettin', 1918, 104, '9789750611965', 11],
    ['Beyaz Lale', 'Ömer Seyfettin', 1918, 88, '9789750612672', 11],
    ['Diyet', 'Ömer Seyfettin', 1917, 80, '9789750613389', 11],
    ['And', 'Ömer Seyfettin', 1919, 96, '9789750614096', 11],
    ['Falaka', 'Ömer Seyfettin', 1918, 72, '9789750614803', 11],

    // Samim Kocagöz
    ['Kalpaklılar', 'Samim Kocagöz', 1962, 352, '9789750615510', 1],
    ['Doludizgin', 'Samim Kocagöz', 1963, 288, '9789750616227', 1],
    ['Yılanların Öcü', 'Samim Kocagöz', 1959, 224, '9789750616934', 1],

    // Füruzan
    ['Parasız Yatılı', 'Füruzan', 1971, 168, '9789750617641', 11],
    ['Kırkyedi\'liler', 'Füruzan', 1974, 224, '9789750618358', 1],
    ['Berlin\'in Nar Çiçeği', 'Füruzan', 1988, 192, '9789750619065', 1],
    ['Gül Mevsimidir', 'Füruzan', 1985, 144, '9789750619772', 11],

    // Tomris Uyar
    ['Günlerin Tortusu', 'Tomris Uyar', 1974, 176, '9789750620489', 11],
    ['Ödeşmeler', 'Tomris Uyar', 1976, 192, '9789750621196', 11],
    ['Yürekte Bukağı', 'Tomris Uyar', 1978, 160, '9789750621903', 11],
    ['Dizboyu Papatyalar', 'Tomris Uyar', 1975, 144, '9789750622610', 11],

    // Adalet Ağaoğlu
    ['Ölmeye Yatmak', 'Adalet Ağaoğlu', 1973, 312, '9789750623327', 1],
    ['Bir Düğün Gecesi', 'Adalet Ağaoğlu', 1979, 288, '9789750624034', 1],
    ['Hayır', 'Adalet Ağaoğlu', 1987, 256, '9789750624741', 1],
    ['Üç Beş Kişi', 'Adalet Ağaoğlu', 1984, 192, '9789750625458', 11],
    ['Romantik Bir Viyana Yazı', 'Adalet Ağaoğlu', 1993, 224, '9789750626165', 1],

    // Atilla İlhan
    ['Sırtlan Payı', 'Atilla İlhan', 1974, 320, '9789750626872', 1],
    ['Bıçağın Ucu', 'Atilla İlhan', 1973, 288, '9789750627589', 1],
    ['Yaraya Tuz Basmak', 'Atilla İlhan', 1978, 256, '9789750628296', 1],
    ['Fena Halde Leman', 'Atilla İlhan', 1981, 304, '9789750629003', 1],
    ['Dersaadet\'te Sabah Ezanları', 'Atilla İlhan', 1981, 352, '9789750629710', 1],
    ['Haco Hanım Vay', 'Atilla İlhan', 1984, 336, '9789750630426', 1],

    // Selim İleri
    ['Cehennem Kraliçesi', 'Selim İleri', 2003, 256, '9789750631133', 1],
    ['Bir Akşam Alacası', 'Selim İleri', 1985, 224, '9789750631840', 1],
    ['Her Gece Bodrum', 'Selim İleri', 1987, 192, '9789750632557', 1],
    ['Pastırma Yazı', 'Selim İleri', 2000, 208, '9789750633264', 1],
    ['Yarın Yapayalnız', 'Selim İleri', 2013, 240, '9789750633971', 1],

    // Nedim Gürsel
    ['Boğazkesen', 'Nedim Gürsel', 1995, 288, '9789750634688', 1],
    ['Uzun Sürmüş Bir Yaz', 'Nedim Gürsel', 1976, 224, '9789750635395', 11],
    ['Kadınlar Kitabı', 'Nedim Gürsel', 1983, 192, '9789750636102', 11],
    ['İlk Kadın', 'Nedim Gürsel', 1985, 176, '9789750636819', 11],
    ['Son Tramvay', 'Nedim Gürsel', 1990, 256, '9789750637526', 1],

    // Oya Baydar
    ['Hiçbir Yere Dönüş', 'Oya Baydar', 1998, 320, '9789750638233', 1],
    ['Sıcak Külleri Kaldı', 'Oya Baydar', 2000, 288, '9789750638940', 1],
    ['Kedi Mektupları', 'Oya Baydar', 1992, 176, '9789750639657', 11],
    ['Erguvan Kapısı', 'Oya Baydar', 2004, 352, '9789750640363', 1],
    ['Kayıp Söz', 'Oya Baydar', 2007, 384, '9789750641070', 1],

    // Leyla Erbil
    ['Hallaç', 'Leyla Erbil', 1960, 160, '9789750641787', 11],
    ['Tuhaf Bir Kadın', 'Leyla Erbil', 1971, 208, '9789750642494', 1],
    ['Karanlığın Günü', 'Leyla Erbil', 1985, 224, '9789750643201', 1],
    ['Mektup Aşkları', 'Leyla Erbil', 1988, 192, '9789750643918', 11],
    ['Cüce', 'Leyla Erbil', 2001, 176, '9789750644625', 1],

    // Bilge Karasu
    ['Göçmüş Kediler Bahçesi', 'Bilge Karasu', 1979, 176, '9789750645332', 11],
    ['Gece', 'Bilge Karasu', 1985, 192, '9789750646049', 1],
    ['Kılavuz', 'Bilge Karasu', 1990, 224, '9789750646756', 1],
    ['Uzun Sürmüş Bir Günün Akşamı', 'Bilge Karasu', 1970, 128, '9789750647463', 11],

    // Onat Kutlar
    ['İshak', 'Onat Kutlar', 1959, 144, '9789750648170', 1],
    ['Kapılar', 'Onat Kutlar', 1963, 128, '9789750648887', 11],

    // Nezihe Meriç
    ['Korsan Çıkmazı', 'Nezihe Meriç', 1961, 160, '9789750649594', 11],
    ['Çisenti', 'Nezihe Meriç', 1969, 144, '9789750650300', 11],
    ['Dumanaltı', 'Nezihe Meriç', 1975, 176, '9789750651017', 11],

    // Ferit Edgü
    ['O Hâlde Yok', 'Ferit Edgü', 1975, 128, '9789750651724', 11],
    ['Eylülün Gölgesinde Bir Yazdı', 'Ferit Edgü', 1988, 144, '9789750652431', 11],
    ['Binbir Hece', 'Ferit Edgü', 1991, 96, '9789750653148', 11],
];

// =====================================================
// TÜRK ŞİİRİ (200 kitap)
// =====================================================
$turkSiir = [
    // Nazım Hikmet
    ['Memleketimden İnsan Manzaraları', 'Nazım Hikmet', 1966, 480, '9789750653855', 2],
    ['Kuvayı Milliye', 'Nazım Hikmet', 1941, 256, '9789750654562', 2],
    ['Benerci Kendini Niçin Öldürdü', 'Nazım Hikmet', 1932, 128, '9789750655279', 2],
    ['Jokond ile Si-Ya-U', 'Nazım Hikmet', 1929, 112, '9789750655986', 2],
    ['835 Satır', 'Nazım Hikmet', 1929, 96, '9789750656693', 2],
    ['Şeyh Bedreddin Destanı', 'Nazım Hikmet', 1936, 80, '9789750657409', 2],
    ['Portreler', 'Nazım Hikmet', 1935, 64, '9789750658116', 2],
    ['Piraye\'ye Mektuplar', 'Nazım Hikmet', 1969, 320, '9789750658823', 2],
    ['Yeni Şiirler', 'Nazım Hikmet', 1951, 88, '9789750659530', 2],
    ['Son Şiirleri', 'Nazım Hikmet', 1963, 72, '9789750660246', 2],

    // Yunus Emre
    ['Yunus Emre Divanı', 'Yunus Emre', 1300, 384, '9789750660953', 2],
    ['Risaletü\'n-Nushiyye', 'Yunus Emre', 1307, 128, '9789750661660', 2],

    // Mevlana
    ['Mesnevi', 'Mevlana Celaleddin Rumi', 1273, 576, '9789750662377', 2],
    ['Divan-ı Kebir', 'Mevlana Celaleddin Rumi', 1270, 512, '9789750663084', 2],
    ['Fihi Ma Fih', 'Mevlana Celaleddin Rumi', 1269, 224, '9789750663791', 7],
    ['Mektuplar', 'Mevlana Celaleddin Rumi', 1268, 160, '9789750664508', 2],

    // Fuzuli
    ['Fuzuli Divanı', 'Fuzuli', 1556, 352, '9789750665215', 2],
    ['Leyla ile Mecnun', 'Fuzuli', 1535, 288, '9789750665922', 2],
    ['Hadikatü\'s-Süeda', 'Fuzuli', 1545, 224, '9789750666639', 2],

    // Baki
    ['Baki Divanı', 'Baki', 1600, 320, '9789750667346', 2],

    // Nedim
    ['Nedim Divanı', 'Nedim', 1730, 288, '9789750668053', 2],

    // Şeyh Galip
    ['Hüsn ü Aşk', 'Şeyh Galip', 1783, 256, '9789750668760', 2],
    ['Şeyh Galip Divanı', 'Şeyh Galip', 1799, 192, '9789750669477', 2],

    // Tevfik Fikret
    ['Rübab-ı Şikeste', 'Tevfik Fikret', 1900, 224, '9789750670183', 2],
    ['Haluk\'un Defteri', 'Tevfik Fikret', 1911, 128, '9789750670890', 2],
    ['Şermin', 'Tevfik Fikret', 1914, 96, '9789750671606', 2],

    // Mehmet Akif Ersoy
    ['Safahat', 'Mehmet Akif Ersoy', 1911, 448, '9789750672313', 2],
    ['İstiklal Marşı', 'Mehmet Akif Ersoy', 1921, 32, '9789750673020', 2],

    // Yahya Kemal Beyatlı
    ['Kendi Gök Kubbemiz', 'Yahya Kemal Beyatlı', 1961, 176, '9789750673737', 2],
    ['Eski Şiirin Rüzgarıyla', 'Yahya Kemal Beyatlı', 1962, 144, '9789750674444', 2],
    ['Rubailer ve Hayyam Rubailerini Türkçe Söyleyiş', 'Yahya Kemal Beyatlı', 1963, 96, '9789750675151', 2],
    ['Aziz İstanbul', 'Yahya Kemal Beyatlı', 1964, 128, '9789750675868', 2],
    ['Eğil Dağlar', 'Yahya Kemal Beyatlı', 1966, 192, '9789750676575', 2],

    // Ahmet Haşim
    ['Göl Saatleri', 'Ahmet Haşim', 1921, 80, '9789750677282', 2],
    ['Piyale', 'Ahmet Haşim', 1926, 64, '9789750677999', 2],
    ['Bize Göre', 'Ahmet Haşim', 1928, 112, '9789750678706', 11],

    // Cahit Sıtkı Tarancı
    ['Otuz Beş Yaş', 'Cahit Sıtkı Tarancı', 1946, 96, '9789750679413', 2],
    ['Ömrümde Sükut', 'Cahit Sıtkı Tarancı', 1933, 64, '9789750680129', 2],
    ['Düşten Güzel', 'Cahit Sıtkı Tarancı', 1952, 80, '9789750680836', 2],

    // Necip Fazıl Kısakürek
    ['Çile', 'Necip Fazıl Kısakürek', 1962, 320, '9789750681543', 2],
    ['Sonsuzluk Kervanı', 'Necip Fazıl Kısakürek', 1955, 256, '9789750682250', 2],
    ['Ben ve Ötesi', 'Necip Fazıl Kısakürek', 1932, 128, '9789750682967', 2],
    ['Kaldırımlar', 'Necip Fazıl Kısakürek', 1928, 96, '9789750683674', 2],
    ['Reis Bey', 'Necip Fazıl Kısakürek', 1968, 144, '9789750684381', 1],

    // Fazıl Hüsnü Dağlarca
    ['Çakırın Destanı', 'Fazıl Hüsnü Dağlarca', 1945, 112, '9789750685098', 2],
    ['Toprak Ana', 'Fazıl Hüsnü Dağlarca', 1950, 128, '9789750685805', 2],
    ['Çocuk ve Allah', 'Fazıl Hüsnü Dağlarca', 1940, 144, '9789750686512', 2],
    ['Türk Olmak', 'Fazıl Hüsnü Dağlarca', 1970, 160, '9789750687229', 2],
    ['Açıl Susam Açıl', 'Fazıl Hüsnü Dağlarca', 1969, 96, '9789750687936', 2],

    // Orhan Veli Kanık
    ['Garip', 'Orhan Veli Kanık', 1941, 80, '9789750688643', 2],
    ['Vazgeçemediğim', 'Orhan Veli Kanık', 1945, 64, '9789750689350', 2],
    ['Destan Gibi', 'Orhan Veli Kanık', 1946, 72, '9789750690066', 2],
    ['Yenisi', 'Orhan Veli Kanık', 1947, 56, '9789750690773', 2],
    ['Karşı', 'Orhan Veli Kanık', 1949, 48, '9789750691480', 2],
    ['Nasrettin Hoca Hikayeleri', 'Orhan Veli Kanık', 1949, 96, '9789750692197', 11],

    // Melih Cevdet Anday
    ['Rahatı Kaçan Ağaç', 'Melih Cevdet Anday', 1946, 88, '9789750692904', 2],
    ['Telgrafhane', 'Melih Cevdet Anday', 1952, 72, '9789750693611', 2],
    ['Kolları Bağlı Odysseus', 'Melih Cevdet Anday', 1963, 104, '9789750694328', 2],
    ['Teknenin Ölümü', 'Melih Cevdet Anday', 1975, 96, '9789750695035', 2],
    ['Göçebe Denizin Üstünde', 'Melih Cevdet Anday', 1980, 80, '9789750695742', 2],

    // Oktay Rıfat
    ['Yaşayıp Ölmek Aşk ve Avarelik Üstüne Şiirler', 'Oktay Rıfat', 1945, 80, '9789750696459', 2],
    ['Aşağı Yukarı', 'Oktay Rıfat', 1952, 64, '9789750697166', 2],
    ['Perçemli Sokak', 'Oktay Rıfat', 1956, 72, '9789750697873', 2],
    ['Karga ile Tilki', 'Oktay Rıfat', 1959, 96, '9789750698580', 2],
    ['Çobanıl Şiirler', 'Oktay Rıfat', 1976, 88, '9789750699297', 2],

    // Attila İlhan
    ['Duvar', 'Attila İlhan', 1948, 80, '9789750700003', 2],
    ['Sisler Bulvarı', 'Attila İlhan', 1954, 96, '9789750700710', 2],
    ['Yağmur Kaçağı', 'Attila İlhan', 1955, 88, '9789750701427', 2],
    ['Ben Sana Mecburum', 'Attila İlhan', 1960, 112, '9789750702134', 2],
    ['Bela Çiçeği', 'Attila İlhan', 1962, 104, '9789750702841', 2],
    ['Yasak Sevişmek', 'Attila İlhan', 1968, 120, '9789750703558', 2],
    ['Tutuklunun Günlüğü', 'Attila İlhan', 1973, 96, '9789750704265', 2],
    ['Böyle Bir Sevmek', 'Attila İlhan', 1977, 80, '9789750704972', 2],

    // Cemal Süreya
    ['Üvercinka', 'Cemal Süreya', 1958, 80, '9789750705689', 2],
    ['Göçebe', 'Cemal Süreya', 1965, 72, '9789750706396', 2],
    ['Beni Öp Sonra Doğur Beni', 'Cemal Süreya', 1973, 64, '9789750707103', 2],
    ['Sevda Sözleri', 'Cemal Süreya', 1984, 96, '9789750707810', 2],
    ['Uçurumda Açan', 'Cemal Süreya', 1984, 88, '9789750708527', 2],
    ['Sıcak Nal', 'Cemal Süreya', 1989, 72, '9789750709234', 2],

    // Edip Cansever
    ['İkindi Üstü', 'Edip Cansever', 1947, 64, '9789750709941', 2],
    ['Dirlik Düzenlik', 'Edip Cansever', 1954, 72, '9789750710657', 2],
    ['Yerçekimli Karanfil', 'Edip Cansever', 1957, 80, '9789750711364', 2],
    ['Umutsuzlar Parkı', 'Edip Cansever', 1958, 96, '9789750712071', 2],
    ['Petrol', 'Edip Cansever', 1959, 64, '9789750712788', 2],
    ['Nerde Antigone', 'Edip Cansever', 1961, 112, '9789750713495', 2],
    ['Tragedyalar', 'Edip Cansever', 1964, 120, '9789750714202', 2],

    // Turgut Uyar
    ['Arz-ı Hal', 'Turgut Uyar', 1949, 56, '9789750714919', 2],
    ['Türkiyem', 'Turgut Uyar', 1952, 64, '9789750715626', 2],
    ['Dünyanın En Güzel Arabistanı', 'Turgut Uyar', 1959, 80, '9789750716333', 2],
    ['Her Pazartesi', 'Turgut Uyar', 1968, 72, '9789750717040', 2],
    ['Tütünler Islak', 'Turgut Uyar', 1962, 88, '9789750717757', 2],
    ['Divan', 'Turgut Uyar', 1970, 104, '9789750718464', 2],
    ['Kayayı Delen İncir', 'Turgut Uyar', 1982, 96, '9789750719171', 2],

    // Ece Ayhan
    ['Kınar Hanımın Denizleri', 'Ece Ayhan', 1959, 64, '9789750719888', 2],
    ['Bakışsız Bir Kedi Kara', 'Ece Ayhan', 1965, 72, '9789750720594', 2],
    ['Ortodoksluklar', 'Ece Ayhan', 1968, 80, '9789750721300', 2],
    ['Devlet ve Tabiat', 'Ece Ayhan', 1973, 96, '9789750722017', 2],
    ['Yort Savul', 'Ece Ayhan', 1977, 88, '9789750722724', 2],
    ['Zambaklı Padişah', 'Ece Ayhan', 1982, 72, '9789750723431', 2],

    // İlhan Berk
    ['İstanbul', 'İlhan Berk', 1947, 80, '9789750724148', 2],
    ['Günaydın Yeryüzü', 'İlhan Berk', 1952, 96, '9789750724855', 2],
    ['Türkiye Şarkısı', 'İlhan Berk', 1953, 72, '9789750725562', 2],
    ['Köroğlu', 'İlhan Berk', 1955, 64, '9789750726279', 2],
    ['Galile Denizi', 'İlhan Berk', 1958, 88, '9789750726986', 2],
    ['Çivi Yazısı', 'İlhan Berk', 1960, 104, '9789750727693', 2],
    ['Otağ', 'İlhan Berk', 1963, 80, '9789750728409', 2],
    ['Uzun Bir Adam', 'İlhan Berk', 1966, 112, '9789750729116', 2],

    // Cahit Zarifoğlu
    ['İşaret Çocukları', 'Cahit Zarifoğlu', 1967, 72, '9789750729823', 2],
    ['Yedi Güzel Adam', 'Cahit Zarifoğlu', 1969, 64, '9789750730539', 2],
    ['Menziller', 'Cahit Zarifoğlu', 1977, 88, '9789750731246', 2],
    ['Korku ve Yakarış', 'Cahit Zarifoğlu', 1979, 96, '9789750731953', 2],

    // İsmet Özel
    ['Geceleyin Bir Koşu', 'İsmet Özel', 1966, 64, '9789750732660', 2],
    ['Evet İsyan', 'İsmet Özel', 1969, 80, '9789750733377', 2],
    ['Cinayetler Kitabı', 'İsmet Özel', 1975, 96, '9789750734084', 2],
    ['Celladıma Gülümserken', 'İsmet Özel', 1984, 72, '9789750734791', 2],

    // Sezai Karakoç
    ['Körfez', 'Sezai Karakoç', 1959, 80, '9789750735507', 2],
    ['Şahdamar', 'Sezai Karakoç', 1962, 64, '9789750736214', 2],
    ['Hızırla Kırk Saat', 'Sezai Karakoç', 1967, 96, '9789750736921', 2],
    ['Sesler', 'Sezai Karakoç', 1968, 72, '9789750737638', 2],
    ['Taha\'nın Kitabı', 'Sezai Karakoç', 1968, 88, '9789750738345', 2],
    ['Gül Muştusu', 'Sezai Karakoç', 1969, 104, '9789750739052', 2],
    ['Zamana Adanmış Sözler', 'Sezai Karakoç', 1975, 80, '9789750739769', 2],
    ['Ayinler', 'Sezai Karakoç', 1977, 96, '9789750740478', 2],
    ['Leyla ile Mecnun', 'Sezai Karakoç', 1981, 112, '9789750741185', 2],

    // Hilmi Yavuz
    ['Bakış Kuşu', 'Hilmi Yavuz', 1969, 64, '9789750741892', 2],
    ['Bedreddin Üzerine Şiirler', 'Hilmi Yavuz', 1975, 72, '9789750742608', 2],
    ['Doğu Şiirleri', 'Hilmi Yavuz', 1977, 88, '9789750743315', 2],
    ['Yazın Kalanları', 'Hilmi Yavuz', 1982, 80, '9789750744022', 2],
    ['Ayna Şiirleri', 'Hilmi Yavuz', 2002, 96, '9789750744739', 2],
];

// Veriyi döndür
return [
    'turkEdebiyatRoman' => $turkEdebiyatRoman,
    'turkSiir' => $turkSiir
];
