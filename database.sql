-- Lonely Eye Database Schema
-- Character Set: utf8mb4_general_ci

DROP DATABASE IF EXISTS lonely_eye;
CREATE DATABASE lonely_eye CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE lonely_eye;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT 'uploads/default.png',
    bio TEXT,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Genres Table
CREATE TABLE genres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    color_code VARCHAR(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- User Interests Table
CREATE TABLE user_interests (
    user_id INT NOT NULL,
    genre_id INT NOT NULL,
    PRIMARY KEY (user_id, genre_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Items Table (Books & Magazines)
CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('book', 'magazine') NOT NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    description TEXT,
    cover_image VARCHAR(500),
    genre_id INT,
    publication_year INT,
    page_count INT,
    view_count INT DEFAULT 0,
    rating_score DECIMAL(3,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Reviews Table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Messages Table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Follows Table
CREATE TABLE follows (
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    PRIMARY KEY (follower_id, following_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert 15 Genres with Color Codes
INSERT INTO genres (name, color_code) VALUES
('Roman', '#FF6B6B'),
('Şiir', '#4ECDC4'),
('Tarih', '#95E1D3'),
('Bilim Kurgu', '#A8E6CF'),
('Fantastik', '#FFD93D'),
('Kişisel Gelişim', '#6BCF7F'),
('Felsefe', '#C7CEEA'),
('Polisiye', '#FF8B94'),
('Biyografi', '#FFA07A'),
('Psikoloji', '#DDA15E'),
('Edebiyat', '#BC6C25'),
('Macera', '#FF6F61'),
('Bilim', '#6A4C93'),
('Sanat', '#F4A261'),
('Dergi', '#E76F51');

-- Insert 20 Users (First user has demo password: password123)
INSERT INTO users (username, email, password, bio, role) VALUES
('ahmet_yilmaz', 'ahmet@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kitap tutkunu bir okuyucu', 'user'),
('ayse_kaya', 'ayse@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Edebiyat aşığı', 'user'),
('mehmet_demir', 'mehmet@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Bilim kurgu hayranı', 'user'),
('fatma_celik', 'fatma@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Roman okumayı seviyorum', 'user'),
('ali_ozturk', 'ali@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Tarih meraklısı', 'admin'),
('zeynep_sahin', 'zeynep@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Şiir yazarı', 'user'),
('mustafa_yildiz', 'mustafa@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Felsefe öğrencisi', 'user'),
('elif_arslan', 'elif@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Polisiye roman tutkunu', 'user'),
('emre_koc', 'emre@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Macera severim', 'user'),
('selin_kurt', 'selin@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Psikoloji mezunu', 'user'),
('burak_ozkan', 'burak@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Bilim insanı', 'user'),
('deniz_aydin', 'deniz@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Sanat tarihçisi', 'user'),
('can_yildirim', 'can@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Dergi editörü', 'user'),
('ece_aksoy', 'ece@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Fantastik edebiyat hayranı', 'user'),
('berk_dogan', 'berk@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Kişisel gelişim koçu', 'user'),
('irem_kara', 'irem@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Biyografi okuyucusu', 'user'),
('arda_polat', 'arda@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Edebiyat öğretmeni', 'user'),
('gizem_sen', 'gizem@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Okuma kulübü kurucusu', 'user'),
('onur_tas', 'onur@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Yazar adayı', 'user'),
('merve_guler', 'merve@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Kitap blogcusu', 'user');

-- Insert 500 Items (Books & Magazines) - Batch Insert for Performance
INSERT INTO items (type, title, author, description, cover_image, genre_id, publication_year, page_count, view_count, rating_score) VALUES
('book', 'Suç ve Ceza', 'Fyodor Dostoyevski', 'Klasik Rus edebiyatının başyapıtı', 'https://placehold.co/300x450/1e293b/FFF?text=Suc+ve+Ceza', 1, 1866, 671, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2)),
('book', 'Sefiller', 'Victor Hugo', 'Fransız edebiyatının걸작', 'https://placehold.co/300x450/1e293b/FFF?text=Sefiller', 1, 1862, 1463, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2)),
('book', 'Beyaz Diş', 'Jack London', 'Doğa ve hayvan hikayesi', 'https://placehold.co/300x450/1e293b/FFF?text=Beyaz+Dis', 12, 1906, 298, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2)),
('book', 'Dune', 'Frank Herbert', 'Bilim kurgu klasiği', 'https://placehold.co/300x450/1e293b/FFF?text=Dune', 4, 1965, 688, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2)),
('book', 'Yüzüklerin Efendisi', 'J.R.R. Tolkien', 'Fantastik edebiyatın zirvesi', 'https://placehold.co/300x450/1e293b/FFF?text=Yuzuklerin+Efendisi', 5, 1954, 1178, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2)),
('book', 'İnce Memed', 'Yaşar Kemal', 'Türk edebiyatının걸작', 'https://placehold.co/300x450/1e293b/FFF?text=Ince+Memed', 1, 1955, 420, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2)),
('book', 'Tutunamayanlar', 'Oğuz Atay', 'Modern Türk romanı', 'https://placehold.co/300x450/1e293b/FFF?text=Tutunamayanlar', 11, 1971, 724, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2)),
('book', '1984', 'George Orwell', 'Distopik klasik', 'https://placehold.co/300x450/1e293b/FFF?text=1984', 4, 1949, 328, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2)),
('book', 'Simyacı', 'Paulo Coelho', 'Kişisel gelişim romanı', 'https://placehold.co/300x450/1e293b/FFF?text=Simyaci', 6, 1988, 208, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2)),
('book', 'Saatleri Ayarlama Enstitüsü', 'Ahmet Hamdi Tanpınar', 'Türk edebiyatı klasiği', 'https://placehold.co/300x450/1e293b/FFF?text=Saatleri+Ayarlama', 1, 1961, 416, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2)),
('magazine', 'National Geographic Türkiye', 'Çeşitli Yazarlar', 'Doğa ve bilim dergisi', 'https://placehold.co/300x450/1e293b/FFF?text=Nat+Geo', 15, 2023, 120, FLOOR(RAND()*3000), ROUND(3 + RAND()*2, 2)),
('book', 'Kürk Mantolu Madonna', 'Sabahattin Ali', 'Aşk ve trajedi', 'https://placehold.co/300x450/1e293b/FFF?text=Kurk+Mantolu', 1, 1943, 160, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2)),
('book', 'Şeker Portakalı', 'Jose Mauro de Vasconcelos', 'Çocukluk hikayesi', 'https://placehold.co/300x450/1e293b/FFF?text=Seker+Portakali', 1, 1968, 192, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2)),
('book', 'Vadideki Zambak', 'Honoré de Balzac', 'Romantik klasik', 'https://placehold.co/300x450/1e293b/FFF?text=Vadideki+Zambak', 1, 1835, 256, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2)),
('book', 'Şu Çılgın Türkler', 'Turgut Özakman', 'Tarih romanı', 'https://placehold.co/300x450/1e293b/FFF?text=Su+Cilgin+Turkler', 3, 1995, 1008, FLOOR(RAND()*5000), ROUND(3 + RAND()*2, 2));

-- Generate remaining 485 items using a stored procedure approach
DELIMITER //
CREATE PROCEDURE generate_items()
BEGIN
    DECLARE i INT DEFAULT 16;
    DECLARE item_type VARCHAR(10);
    DECLARE genre INT;
    
    WHILE i <= 500 DO
        SET item_type = IF(RAND() > 0.8, 'magazine', 'book');
        SET genre = FLOOR(1 + RAND() * 15);
        
        INSERT INTO items (type, title, author, description, cover_image, genre_id, publication_year, page_count, view_count, rating_score)
        VALUES (
            item_type,
            CONCAT(
                CASE FLOOR(RAND() * 20)
                    WHEN 0 THEN 'Kayıp'
                    WHEN 1 THEN 'Gizli'
                    WHEN 2 THEN 'Unutulmuş'
                    WHEN 3 THEN 'Sonsuz'
                    WHEN 4 THEN 'Karanlık'
                    WHEN 5 THEN 'Aydınlık'
                    WHEN 6 THEN 'Gizemli'
                    WHEN 7 THEN 'Sessiz'
                    WHEN 8 THEN 'Yalnız'
                    WHEN 9 THEN 'Uzak'
                    WHEN 10 THEN 'Yakın'
                    WHEN 11 THEN 'Eski'
                    WHEN 12 THEN 'Yeni'
                    WHEN 13 THEN 'Büyük'
                    WHEN 14 THEN 'Küçük'
                    WHEN 15 THEN 'Son'
                    WHEN 16 THEN 'İlk'
                    WHEN 17 THEN 'Altın'
                    WHEN 18 THEN 'Gümüş'
                    ELSE 'Bronz'
                END,
                ' ',
                CASE FLOOR(RAND() * 20)
                    WHEN 0 THEN 'Dünya'
                    WHEN 1 THEN 'Şehir'
                    WHEN 2 THEN 'Yıldız'
                    WHEN 3 THEN 'Deniz'
                    WHEN 4 THEN 'Dağ'
                    WHEN 5 THEN 'Orman'
                    WHEN 6 THEN 'Gökyüzü'
                    WHEN 7 THEN 'Rüya'
                    WHEN 8 THEN 'Hayat'
                    WHEN 9 THEN 'Aşk'
                    WHEN 10 THEN 'Savaş'
                    WHEN 11 THEN 'Barış'
                    WHEN 12 THEN 'Zaman'
                    WHEN 13 THEN 'Mekân'
                    WHEN 14 THEN 'İnsan'
                    WHEN 15 THEN 'Hayvan'
                    WHEN 16 THEN 'Bitki'
                    WHEN 17 THEN 'Taş'
                    WHEN 18 THEN 'Su'
                    ELSE 'Ateş'
                END,
                CASE FLOOR(RAND() * 15)
                    WHEN 0 THEN 'ın Sırrı'
                    WHEN 1 THEN 'ın Gölgesi'
                    WHEN 2 THEN 'ın Işığı'
                    WHEN 3 THEN 'ın Sesi'
                    WHEN 4 THEN 'ın Yolu'
                    WHEN 5 THEN 'ın Kapısı'
                    WHEN 6 THEN 'ın Anahtarı'
                    WHEN 7 THEN 'ın Hazinesi'
                    WHEN 8 THEN 'ın Laneti'
                    WHEN 9 THEN 'ın Kutsaması'
                    WHEN 10 THEN 'ın Hikayesi'
                    WHEN 11 THEN 'ın Efsanesi'
                    WHEN 12 THEN 'ın Gerçeği'
                    WHEN 13 THEN 'ın Yalanı'
                    ELSE 'ın Dönüşü'
                END
            ),
            CONCAT(
                CASE FLOOR(RAND() * 30)
                    WHEN 0 THEN 'Ahmet'
                    WHEN 1 THEN 'Mehmet'
                    WHEN 2 THEN 'Ali'
                    WHEN 3 THEN 'Ayşe'
                    WHEN 4 THEN 'Fatma'
                    WHEN 5 THEN 'Zeynep'
                    WHEN 6 THEN 'Mustafa'
                    WHEN 7 THEN 'Elif'
                    WHEN 8 THEN 'Can'
                    WHEN 9 THEN 'Deniz'
                    WHEN 10 THEN 'Ece'
                    WHEN 11 THEN 'Berk'
                    WHEN 12 THEN 'Selin'
                    WHEN 13 THEN 'Emre'
                    WHEN 14 THEN 'İrem'
                    WHEN 15 THEN 'Burak'
                    WHEN 16 THEN 'Gizem'
                    WHEN 17 THEN 'Onur'
                    WHEN 18 THEN 'Merve'
                    WHEN 19 THEN 'Arda'
                    WHEN 20 THEN 'Ceren'
                    WHEN 21 THEN 'Kerem'
                    WHEN 22 THEN 'Esra'
                    WHEN 23 THEN 'Murat'
                    WHEN 24 THEN 'Pınar'
                    WHEN 25 THEN 'Serkan'
                    WHEN 26 THEN 'Sibel'
                    WHEN 27 THEN 'Tolga'
                    WHEN 28 THEN 'Yasemin'
                    ELSE 'Volkan'
                END,
                ' ',
                CASE FLOOR(RAND() * 20)
                    WHEN 0 THEN 'Yılmaz'
                    WHEN 1 THEN 'Kaya'
                    WHEN 2 THEN 'Demir'
                    WHEN 3 THEN 'Çelik'
                    WHEN 4 THEN 'Öztürk'
                    WHEN 5 THEN 'Şahin'
                    WHEN 6 THEN 'Yıldız'
                    WHEN 7 THEN 'Arslan'
                    WHEN 8 THEN 'Koç'
                    WHEN 9 THEN 'Kurt'
                    WHEN 10 THEN 'Özkan'
                    WHEN 11 THEN 'Aydın'
                    WHEN 12 THEN 'Yıldırım'
                    WHEN 13 THEN 'Aksoy'
                    WHEN 14 THEN 'Doğan'
                    WHEN 15 THEN 'Kara'
                    WHEN 16 THEN 'Polat'
                    WHEN 17 THEN 'Şen'
                    WHEN 18 THEN 'Taş'
                    ELSE 'Güler'
                END
            ),
            'Etkileyici bir eser. Okumaya değer.',
            CONCAT('https://placehold.co/300x450/1e293b/FFF?text=', IF(item_type='book', 'Kitap', 'Dergi'), '+', i),
            genre,
            FLOOR(1950 + RAND() * 74),
            FLOOR(100 + RAND() * 800),
            FLOOR(RAND() * 5000),
            ROUND(3 + RAND() * 2, 2)
        );
        
        SET i = i + 1;
    END WHILE;
END//
DELIMITER ;

CALL generate_items();
DROP PROCEDURE generate_items;

-- Insert User Interests (Random)
INSERT INTO user_interests (user_id, genre_id)
SELECT u.id, g.id
FROM users u
CROSS JOIN genres g
WHERE RAND() < 0.3
LIMIT 100;

-- Insert 300 Reviews
INSERT INTO reviews (user_id, item_id, rating, comment)
SELECT 
    FLOOR(1 + RAND() * 20),
    FLOOR(1 + RAND() * 500),
    FLOOR(1 + RAND() * 5),
    CASE FLOOR(RAND() * 10)
        WHEN 0 THEN 'Harika bir kitap! Kesinlikle tavsiye ederim.'
        WHEN 1 THEN 'Çok beğendim, herkese öneririm.'
        WHEN 2 THEN 'Ortalama bir eser, beklentimi karşılamadı.'
        WHEN 3 THEN 'Muhteşem! Tekrar okuyacağım.'
        WHEN 4 THEN 'Fena değil ama daha iyisi var.'
        WHEN 5 THEN 'Çok sıkıcı buldum, bitiremedim.'
        WHEN 6 THEN 'Etkileyici bir anlatım.'
        WHEN 7 THEN 'Beklediğimden çok daha iyi çıktı.'
        WHEN 8 THEN 'Vasat bir eser.'
        ELSE 'Mükemmel! En sevdiğim kitaplardan biri oldu.'
    END
FROM 
    (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) t1,
    (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) t2,
    (SELECT 1 UNION SELECT 2 UNION SELECT 3) t3
LIMIT 300;

-- Insert Random Follows
INSERT INTO follows (follower_id, following_id)
SELECT DISTINCT
    FLOOR(1 + RAND() * 20),
    FLOOR(1 + RAND() * 20)
FROM 
    (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) t1,
    (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) t2
WHERE FLOOR(1 + RAND() * 20) != FLOOR(1 + RAND() * 20)
LIMIT 150;

-- Insert Random Messages
INSERT INTO messages (sender_id, receiver_id, message, is_read)
SELECT 
    FLOOR(1 + RAND() * 20),
    FLOOR(1 + RAND() * 20),
    CASE FLOOR(RAND() * 8)
        WHEN 0 THEN 'Merhaba! Nasılsın?'
        WHEN 1 THEN 'Bu kitabı okudun mu? Çok güzelmiş!'
        WHEN 2 THEN 'Önerin var mı bana?'
        WHEN 3 THEN 'Teşekkürler, harika bir tavsiyeydi.'
        WHEN 4 THEN 'Bu yazarı çok seviyorum.'
        WHEN 5 THEN 'Yeni çıkan kitapları takip ediyor musun?'
        WHEN 6 THEN 'Okuma kulübümüze katılmak ister misin?'
        ELSE 'Görüşmek üzere!'
    END,
    RAND() > 0.5
FROM 
    (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) t1,
    (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) t2
LIMIT 200;
