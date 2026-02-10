# Lonely Eye - Modern Kitap Sosyal Ağı

![Lonely Eye Banner](https://placehold.co/1200x400/121212/FF0000?text=Lonely+Eye)

> **"Kitapların dünyasında yeni bir keşif yolculuğu."**

Lonely Eye, kitap severleri bir araya getiren, modern, şık ve kullanıcı dostu bir **Kitap Sosyal Ağı** projesidir. Kullanıcıların kütüphanelerini dijital ortamda yönetebileceği, kitaplar hakkında derinlemesine tartışabileceği, yeni eserler keşfedebileceği ve diğer okurlarla etkileşime girebileceği kapsamlı bir platformdur.

Arayüz tasarımında **"Clean Dark & Vibrant Accents"** (Temiz Karanlık & Canlı Vurgular) felsefesi benimsenmiştir. Bu sayede içerik ön plana çıkarılırken, göz yormayan ve premium bir kullanıcı deneyimi sunulur.

## 🌟 Öne Çıkan Özellikler

### 📚 Geniş Kütüphane & Sınırsız Keşif
- **Hibrit Veritabanı:** Hem yerel veritabanındaki binlerce kitap hem de **Google Books API** entegrasyonu ile dünyadaki milyonlarca kitaba anında erişim.
- **Sonsuz Kaydırma (Infinite Scroll):** Kesintisiz ve akıcı bir kitap keşif deneyimi.
- **Gelişmiş Filtreleme:** Tür, yazar, dil ve diğer kriterlere göre detaylı arama seçenekleri.

### 👤 Sosyal Etkileşim & Topluluk
- **Kullanıcı Profilleri:** Okuma listeleri, favoriler, biyografi ve takipçi/takip edilen istatistikleri.
- **Etkileşim:** Diğer kullanıcıları takip etme, kitap zevklerine göre yeni insanlarla tanışma.
- **Mesajlaşma (DM):** Kullanıcılar arası anlık ve özel mesajlaşma sistemi.
- **İnceleme & Tartışma:** Kitaplara puan verme, detaylı incelemeler yazma ve yorumlara yanıt vererek (thread yapısı) tartışmalara katılma.

### 🎨 Modern Tasarım Dili
- **Clean Dark Tema:** Arka planda `#121212` ve yüzeylerde `#1E1E1E` tonları ile derinlikli, göz yormayan bir karanlık mod.
- **Canlı Aksan Renkler:** 
  - **Kırmızı (#FF0000):** Önemli başlıklar ve vurgular.
  - **Bebek Mavisi (#40C4FF) & Derin Mavi (#0000FF):** Marka kimliği ve logolar.
  - **Mor (#9D00FF):** Butonlar ve etkileşim çağrıları (CTA).
  - **Turuncu (#FF4500):** Footer alanı ve belirli uyarılar.
- **Responsive & Akıcı:** Mobil, tablet ve masaüstü cihazlarda kusursuz çalışan, yüksek performanslı arayüz.

### 🛠 Yönetim & Altyapı
- **Admin Paneli:** İçerik, kullanıcı ve sistem yönetimi için kapsamlı dashboard.
- **Güvenli Altyapı:** Modern PHP ve SQL pratikleri ile güvenli veri yönetimi.

## 🚀 Kurulum (Localhost)

Projeyi yerel ortamınızda çalıştırmak için aşağıdaki adımları izleyebilirsiniz:

### Gereksinimler
- **Web Sunucusu:** XAMPP, WAMP veya benzeri (Apache + PHP + MySQL).
- **Git:** Sürüm kontrolü için.

### Kurulum Adımları

1. **Depoyu Klonlayın:**
   Web sunucunuzun kök dizinine (örneğin `htdocs`) gidin ve terminalde şu komutu çalıştırın:
   ```bash
   git clone https://github.com/KULLANICI_ADINIZ/lonely_eye.git
   ```

2. **Veritabanını Hazırlayın:**
   - `phpMyAdmin` veya tercih ettiğiniz veritabanı yönetim aracını açın.
   - `lonely_eye` adında yeni bir veritabanı oluşturun (Character set: `utf8mb4_general_ci`).
   - Proje ana dizinindeki `database.sql` dosyasını bu veritabanına **içe aktarın (import)**.

3. **Bağlantı Ayarlarını Yapılandırın:**
   `includes/db.php` dosyasını açın ve veritabanı kimlik bilgilerinizi kontrol edin (Gerekirse düzenleyin):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'lonely_eye');
   define('DB_USER', 'root'); // Varsayılan: root
   define('DB_PASS', '');     // Varsayılan: boş
   ```

4. **Projeyi Çalıştırın:**
   Tarayıcınızı açın ve `http://localhost/lonely_eye` adresine gidin.

## 📂 Proje Yapısı

- **`/admin`**: Yönetim paneli sayfaları.
- **`/api`**: AJAX isteklerini karşılayan backend servisleri.
- **`/assets`**: CSS, JavaScript ve görsel dosyalar.
- **`/includes`**: Tekrar kullanılan PHP parçaları (header, footer, db bağlantısı vb.).
- **`index.php`**: Karşılama sayfası (Landing page).
- **`dashboard.php`**: Kullanıcı ana akış sayfası.
- **`profile.php`**: Kullanıcı profil sayfası.
- **`library.php`**: Kitap arama ve listeleme sayfası.

## 🤝 Katkıda Bulunma

Katkılarınız bizim için değerlidir! 
1. Bu projeyi **Fork** edin.
2. Yeni bir **Branch** oluşturun (`git checkout -b feature/YeniOzellik`).
3. Değişikliklerinizi **Commit** edin (`git commit -m 'Yeni özellik: X eklendi'`).
4. Branch'inizi **Push** edin (`git push origin feature/YeniOzellik`).
5. Bir **Pull Request (PR)** açın.

## 📝 Lisans

Bu proje **MIT Lisansı** altında sunulmaktadır.

---
**Lonely Eye** - *Okumanın en sosyal hali.*
