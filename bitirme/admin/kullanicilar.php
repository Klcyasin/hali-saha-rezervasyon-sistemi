<?php
session_start();
require_once('../db_baglanti.php');
require_once('../config.php');

// GÜVENLİK: Admin Kontrolü (Sunum Madde 01: Erişim Denetimi)
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: index.php");
    exit;
}

$mesaj = "";

// 🗑️ KULLANICI SİLME (Operasyonel Risk Yönetimi)
if (isset($_GET['sil_id'])) {
    $sil_id = (int)$_GET['sil_id'];
    // Admin kendi kendini silemesin kontrolü
    if ($sil_id == $_SESSION['yonetici_id']) {
        $mesaj = "<div class='alert alert-danger shadow-sm'>Kendi hesabınızı buradan silemezsiniz!</div>";
    } else {
        $sil = $db->prepare("DELETE FROM kullanicilar WHERE id = ?");
        if ($sil->execute([$sil_id])) {
            $mesaj = "<div class='alert alert-success shadow-sm'>Kullanıcı sistemden başarıyla kaldırıldı.</div>";
        }
    }
}

// 📊 ÜYE LİSTESİNİ VE REZERVASYON SAYILARINI ÇEK (Raporlama ve İletişim)
// Her üyenin toplam kaç rezervasyon yaptığını JOIN ile hesaplıyoruz
$sorgu = $db->query("SELECT k.*, COUNT(r.id) as toplam_islem 
                     FROM kullanicilar k 
                     LEFT JOIN rezervasyonlar r ON k.id = r.kullanici_id 
                     GROUP BY k.id 
                     ORDER BY k.id DESC");
$uyeler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Üye Yönetimi | Admin</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .user-card { border-radius: 15px; border: none; overflow: hidden; }
        .user-avatar { width: 40px; height: 40px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #1e5631; }
        .role-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container-fluid mt-5 px-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users text-primary mr-2"></i> Kayıtlı Üyeler</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-chevron-left"></i> Panele Dön</a>
    </div>

    <?= $mesaj ?>

    <div class="card user-card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Kullanıcı Bilgileri</th>
                            <th>Yetki</th>
                            <th>Toplam İşlem</th>
                            <th class="text-center">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($uyeler as $u): ?>
                        <tr>
                            <td class="align-middle"><?= $u['id'] ?></td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar mr-3">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold"><?= htmlspecialchars($u['ad_soyad']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($u['email']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <?php if($u['rol'] == 'yonetici'): ?>
                                    <span class="role-badge badge badge-dark">YÖNETİCİ</span>
                                <?php else: ?>
                                    <span class="role-badge badge badge-info">MÜŞTERİ</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle font-weight-bold"><?= $u['toplam_islem'] ?> Rezervasyon</td>
                            <td class="text-center align-middle">
                                <?php if($u['rol'] != 'yonetici'): ?>
                                    <a href="?sil_id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Bu kullanıcıyı ve tüm geçmişini silmek istediğinize emin misiniz?')" title="Üyeyi Sil">
                                        <i class="fas fa-user-times"></i> Üyeliği Sil
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Korunuyor</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>