# Lonely Eye - Dijital Otağ & Kitap Sosyal Ağı

![Lonely Eye Banner](https://placehold.co/1200x400/121212/FF0000?text=Lonely+Eye:+Digital+Otağ)

> **"Bilgiye açılan dijital bir kapı, modern bir otağ."**

Lonely Eye, kitap severleri bir araya getiren, modern ve estetik bir **Kitap Sosyal Ağı** projesidir. Kullanıcıların kütüphanelerini oluşturabileceği, kitaplar hakkında tartışabileceği, yeni eserler keşfedebileceği ve diğer okurlarla etkileşime girebileceği kapsamlı bir platformdur.

Proje, **"Dijital Otağ"** vizyonuyla geliştirilmiş olup, Türk kültürünün derinliğini modern web teknolojileri ve **"Clean Dark & Vibrant Accents"** tasarım diliyle birleştirir.

## 🌟 Öne Çıkan Özellikler

### 📚 Geniş Kütüphane & Keşif
- **Zengin Veritabanı:** Binlerce kitap ve dergi içeren yerel veritabanı.
- **Google Books Entegrasyonu:** Dünya çapındaki tüm kitaplara erişim sağlayan arama motoru.
- **Sonsuz Kaydırma (Infinite Scroll):** Kesintisiz bir keşif deneyimi.
- **Detaylı Filtreleme:** Tür, yazar ve dile göre gelişmiş arama seçenekleri.

### 👤 Sosyal Etkileşim
- **Kullanıcı Profilleri:** Okuma geçmişi, favoriler ve kişisel biyografi.
- **Takip Sistemi:** Diğer okurları takip etme ve etkileşimde bulunma.
- **Mesajlaşma:** Kullanıcılar arası özel mesajlaşma (DM) sistemi.
- **Yorumlar ve Tartışmalar:** Kitaplara detaylı incelemeler yazma ve diğer yorumlara yanıt verme (Instagram tarzı alt yorumlar).

### 🎨 Modern & Premium Tasarım
- **Clean Dark Tema:** Göz yormayan, premium karanlık mod (`#121212`).
- **Canlı Aksan Renkler:** 
  - **Kırmızı (#FF0000):** Başlıklar ve vurgular.
  - **Bebek Mavisi (#40C4FF) & Derin Mavi (#0000FF):** Logo ve marka kimliği.
  - **Mor (#9D00FF):** Butonlar ve etkileşimli öğeler.
  - **Turuncu (#FF4500):** Footer vurguları.
- **Responsive Arayüz:** Mobil ve masaüstü uyumlu, akıcı tasarım.

### 🛠 Yönetim Paneli
- **Admin Dashboard:** Kullanıcıları, kitapları ve içerikleri yönetmek için gelişmiş panel.
- **İstatistikler:** Platform kullanımı hakkında detaylı veriler.

## 🚀 Kurulum (Localhost)

Bu projeyi kendi bilgisayarınızda çalıştırmak için aşağıdaki adımları izleyin:

### Gereksinimler
- **XAMPP** (veya muadili PHP/MySQL sunucusu)
- **Git**

### Adım Adım Kurulum

1. **Projeyi Klonlayın:**
   Terminalinizi açın ve `htdocs` klasörünüze gidin:
   ```bash
   cd c:\xampp\htdocs
   git clone https://github.com/KULLANICI_ADINIZ/lonely_eye.git
   ```

2. **Veritabanını İçe Aktarın:**
   - `http://localhost/phpmyadmin` adresine gidin.
   - Yeni bir veritabanı oluşturun: `lonely_eye`
   - `lonely_eye` klasöründeki `database.sql` dosyasını bu veritabanına **İçe Aktar (Import)** sekmesinden yükleyin.

3. **Veritabanı Ayarlarını Kontrol Edin:**
   `includes/db.php` dosyasını açın ve veritabanı bağlantı bilgilerinizin doğru olduğundan emin olun (Genellikle XAMPP varsayılanları şöyledir):
   ```php
   // includes/db.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'lonely_eye');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_CHARSET', 'utf8mb4');
   ```

4. **Projeyi Başlatın:**
   Tarayıcınızda şu adrese gidin:
   `http://localhost/lonely_eye`

## 📂 Proje Yapısı

```
lonely_eye/
├── admin/          # Yönetim paneli dosyaları
├── api/            # AJAX istekleri için backend API'leri
├── assets/         # CSS, JS, Resim dosyaları
├── includes/       # Veritabanı bağlantısı, header, footer vb.
├── uploads/        # Kullanıcı yüklemeleri (avatar, kapak fotosu)
├── index.php       # Ana sayfa
├── dashboard.php   # Kullanıcı akış sayfası
├── profile.php     # Profil sayfası
├── library.php     # Kütüphane ve arama
└── database.sql    # Veritabanı şeması
```

## 🤝 Katkıda Bulunma

1. Bu depoyu (repository) Fork'layın.
2. Yeni bir özellik dalı (feature branch) oluşturun (`git checkout -b ozellik/YeniOzellik`).
3. Değişikliklerinizi kaydedin (`git commit -m 'Yeni özellik eklendi'`).
4. Dalınızı Push'layın (`git push origin ozellik/YeniOzellik`).
5. Bir Pull Request oluşturun.

## 📝 Lisans

Bu proje **MIT Lisansı** ile lisanslanmıştır. Detaylar için `LICENSE` dosyasına bakınız.

---
**Lonely Eye** - *Gözler Kalbin Aynasıdır, Kitaplar İse Ruhun.*
