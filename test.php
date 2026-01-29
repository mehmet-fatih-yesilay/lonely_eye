<?php
/**
 * Test Page - Verify Design System
 * This page tests all components without requiring database
 */

$page_title = "Test Sayfası";
require_once 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">🎨 Lonely Eye Tasarım Sistemi Testi</h1>
            <p class="text-muted">Tüm bileşenlerin düzgün çalıştığını doğrulayın.</p>
        </div>
    </div>

    <!-- Cards Test -->
    <div class="row mt-4">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-book"></i> Normal Kart
                </div>
                <div class="card-body">
                    <h5>Kart Başlığı</h5>
                    <p class="text-muted">Bu normal bir kart örneğidir. Hover efektini test edin.</p>
                    <button class="btn btn-primary">
                        <i class="fas fa-heart"></i> Beğen
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card card-glass">
                <div class="card-header">
                    <i class="fas fa-star"></i> Glassmorphism Kart
                </div>
                <div class="card-body">
                    <h5>Cam Efekti</h5>
                    <p class="text-muted">Bu glassmorphism efektli bir karttır.</p>
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-bookmark"></i> Kaydet
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5>Avatar Testi</h5>
                    <div class="d-flex gap-3 align-items-center">
                        <img src="https://ui-avatars.com/api/?name=Test+User&background=38BDF8&color=fff" alt="Avatar"
                            class="avatar">
                        <img src="https://ui-avatars.com/api/?name=User+Two&background=0EA5E9&color=fff" alt="Avatar"
                            class="avatar-lg">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Buttons Test -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-mouse-pointer"></i> Buton Çeşitleri
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3">
                        <button class="btn btn-primary">
                            <i class="fas fa-plus"></i> Primary
                        </button>
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Outline Primary
                        </button>
                        <button class="btn btn-secondary">
                            <i class="fas fa-times"></i> Secondary
                        </button>
                        <button class="btn btn-primary btn-sm">
                            <i class="fas fa-check"></i> Küçük
                        </button>
                        <button class="btn btn-primary btn-lg">
                            <i class="fas fa-rocket"></i> Büyük
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Test -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-keyboard"></i> Form Elemanları
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Normal Input</label>
                        <input type="text" class="form-control" placeholder="Bir şeyler yazın...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">İkonlu Input</label>
                        <div class="input-group-icon">
                            <input type="text" class="form-control" placeholder="Kullanıcı adı">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Select</label>
                        <select class="form-select">
                            <option>Seçenek 1</option>
                            <option>Seçenek 2</option>
                            <option>Seçenek 3</option>
                        </select>
                    </div>

                    <button class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane"></i> Gönder
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tags"></i> Badges & Tags
                </div>
                <div class="card-body">
                    <h6>Badges:</h6>
                    <div class="d-flex gap-2 mb-3">
                        <span class="badge badge-primary">Primary</span>
                        <span class="badge badge-secondary">Secondary</span>
                    </div>

                    <h6>Tür Etiketleri:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="genre-tag" style="background: #FF6B6B; color: white;">Roman</span>
                        <span class="genre-tag" style="background: #4ECDC4; color: white;">Şiir</span>
                        <span class="genre-tag" style="background: #95E1D3; color: white;">Tarih</span>
                        <span class="genre-tag" style="background: #FFD93D; color: #333;">Fantastik</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Card Test -->
    <div class="row mt-4">
        <div class="col-md-3 mb-4">
            <div class="item-card">
                <img src="https://placehold.co/300x450/1e293b/38BDF8?text=Kitap+1" alt="Kitap">
                <div class="item-card-overlay">
                    <h6 class="text-white mb-1">Kitap Başlığı</h6>
                    <p class="text-white-50 small mb-0">Yazar Adı</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="item-card">
                <img src="https://placehold.co/300x450/1e293b/0EA5E9?text=Kitap+2" alt="Kitap">
                <div class="item-card-overlay">
                    <h6 class="text-white mb-1">Başka Kitap</h6>
                    <p class="text-white-50 small mb-0">Başka Yazar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="item-card">
                <img src="https://placehold.co/300x450/1e293b/FF6B6B?text=Dergi+1" alt="Dergi">
                <div class="item-card-overlay">
                    <h6 class="text-white mb-1">Dergi Başlığı</h6>
                    <p class="text-white-50 small mb-0">Editör</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="item-card">
                <img src="https://placehold.co/300x450/1e293b/4ECDC4?text=Dergi+2" alt="Dergi">
                <div class="item-card-overlay">
                    <h6 class="text-white mb-1">Başka Dergi</h6>
                    <p class="text-white-50 small mb-0">Yayıncı</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Animation Test -->
    <div class="row mt-4 mb-5">
        <div class="col-md-4">
            <div class="card fade-in">
                <div class="card-body text-center">
                    <i class="fas fa-magic fa-3x text-primary mb-3"></i>
                    <h5>Fade In</h5>
                    <p class="text-muted">Animasyon testi</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card slide-in">
                <div class="card-body text-center">
                    <i class="fas fa-bolt fa-3x text-primary mb-3"></i>
                    <h5>Slide In</h5>
                    <p class="text-muted">Animasyon testi</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-heart fa-3x text-primary mb-3 pulse"></i>
                    <h5>Pulse</h5>
                    <p class="text-muted">Animasyon testi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Messages -->
    <div class="row mt-4 mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-check-circle"></i> Durum Mesajları
                </div>
                <div class="card-body">
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-check-circle"></i> Başarılı! İşlem tamamlandı.
                    </div>
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i> Bilgi: Tema değiştirmeyi deneyin!
                    </div>
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i> Uyarı: Dikkatli olun.
                    </div>
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-times-circle"></i> Hata: Bir sorun oluştu.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>