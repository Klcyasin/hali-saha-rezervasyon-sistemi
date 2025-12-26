<?php
session_start();
require_once('../db_baglanti.php');
require_once('../config.php');

// GÜVENLİK KONTROLÜ (Erişim Denetimi)
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: index.php");
    exit;
}

$yonetici_id = $_SESSION['yonetici_id'];
$mesaj = "";

// MEVCUT BİLGİLERİ GETİR
$sorgu = $db->prepare("SELECT ad_soyad, email FROM kullanicilar WHERE id = ?");
$sorgu->execute([$yonetici_id]);
$admin = $sorgu->fetch(PDO::FETCH_ASSOC);

// GÜNCELLEME İŞLEMİ (Risk Yönetimi - Güvenlik Güncellemesi)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad_soyad = trim($_POST['ad_soyad']);
    $email = trim($_POST['email']);
    $yeni_sifre = $_POST['yeni_sifre'];

    try {
        if (!empty($yeni_sifre)) {
            // Şifre değiştiriliyorsa (Kritik Varlık Koruması)
            $hashli_sifre = password_hash($yeni_sifre, PASSWORD_DEFAULT);
            $guncelle = $db->prepare("UPDATE kullanicilar SET ad_soyad = ?, email = ?, sifre = ? WHERE id = ?");
            $guncelle->execute([$ad_soyad, $email, $hashli_sifre, $yonetici_id]);
        } else {
            // Sadece isim ve email güncelleniyorsa
            $guncelle = $db->prepare("UPDATE kullanicilar SET ad_soyad = ?, email = ? WHERE id = ?");
            $guncelle->execute([$ad_soyad, $email, $yonetici_id]);
        }
        
        $_SESSION['yonetici_adi'] = $ad_soyad; // Session güncelle
        $mesaj = "<div class='alert alert-success shadow-sm'>Profil bilgileriniz başarıyla güncellendi!</div>";
    } catch (PDOException $e) {
        $mesaj = "<div class='alert alert-danger'>Hata oluştu: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sistem Ayarları | Admin</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .settings-card { border-radius: 15px; border: none; }
        .btn-update { background: #1e5631; color: white; border-radius: 10px; font-weight: bold; }
        .btn-update:hover { background: #2d8149; color: white; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-cog text-secondary mr-2"></i> Yönetici Ayarları</h2>
                <a href="dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Geri Dön</a>
            </div>

            <?= $mesaj ?>

            <div class="card settings-card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Ad Soyad</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" name="ad_soyad" class="form-control" value="<?= htmlspecialchars($admin['ad_soyad']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">E-Posta Adresi</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3 small">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Şifrenizi değiştirmek istemiyorsanız şifre alanını boş bırakın.
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Yeni Şifre (Opsiyonel)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" name="yeni_sifre" class="form-control" placeholder="Güçlü bir şifre belirleyin">
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-update btn-block py-3 shadow-sm">
                            <i class="fas fa-save mr-2"></i> DEĞİŞİKLİKLERİ KAYDET
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4 text-muted small">
                [cite_start]<p><i class="fas fa-shield-alt mr-1"></i> Tüm veri trafiği şifrelenmiş ve oturum kontrolü ile korunmaktadır. [cite: 27, 85]</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>