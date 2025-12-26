<?php
session_start();
require_once('db_baglanti.php');
require_once('config.php');

// Oturum kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: " . BASE_URL . "/giris.php");
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];
$mesaj = "";

// REZERVASYON İPTAL İŞLEMİ
if (isset($_GET['iptal_id'])) {
    $iptal_id = (int)$_GET['iptal_id'];
    $iptal_sorgu = $db->prepare("UPDATE rezervasyonlar SET durum = 'iptal' WHERE id = ? AND kullanici_id = ? AND durum = 'onay bekliyor'");
    if ($iptal_sorgu->execute([$iptal_id, $kullanici_id])) {
        $mesaj = "<div class='alert alert-info shadow-sm'>Rezervasyon talebiniz iptal edildi.</div>";
    }
}

// 📊 İSTATİSTİKLERİ ÇEK (Bilgi Kartı İçin)
try {
    // Toplam Rezervasyon Sayısı
    $sorgu_count = $db->prepare("SELECT COUNT(*) FROM rezervasyonlar WHERE kullanici_id = ?");
    $sorgu_count->execute([$kullanici_id]);
    $toplam_rezervasyon = $sorgu_count->fetchColumn();

    // Onaylı Rezervasyon Sayısı
    $sorgu_onayli = $db->prepare("SELECT COUNT(*) FROM rezervasyonlar WHERE kullanici_id = ? AND durum = 'onaylandı'");
    $sorgu_onayli->execute([$kullanici_id]);
    $onayli_sayisi = $sorgu_onayli->fetchColumn();

    // Tüm listeyi çek
    $sorgu = $db->prepare("SELECT r.*, s.saha_adi, s.saatlik_fiyat 
                         FROM rezervasyonlar r 
                         JOIN sahalar s ON r.saha_id = s.id 
                         WHERE r.kullanici_id = ? 
                         ORDER BY r.tarih DESC, r.baslangic_saati DESC");
    $sorgu->execute([$kullanici_id]);
    $rezervasyonlar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim | Halı Saha Sistemi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .profile-card { background: white; border-radius: 15px; border: none; overflow: hidden; }
        .profile-header { background: linear-gradient(45deg, #1e5631, #a4de02); padding: 30px; color: white; }
        .stat-box { background: #f8f9fa; border-radius: 10px; padding: 15px; margin-bottom: 10px; border: 1px solid #eee; }
        .status-badge { font-size: 0.75rem; font-weight: bold; border-radius: 50px; padding: 5px 12px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card profile-card shadow-sm">
                <div class="profile-header text-center">
                    <i class="fas fa-user-circle fa-4x mb-2"></i>
                    <h5 class="mb-0"><?= htmlspecialchars($_SESSION['kullanici_adi']) ?></h5>
                    <small>Aktif Oyuncu</small>
                </div>
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small font-weight-bold mb-3">Hesap Özeti</h6>
                    
                    <div class="stat-box d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-list-ul text-primary mr-2"></i> Toplam Talep</span>
                        <span class="badge badge-primary badge-pill"><?= $toplam_rezervasyon ?></span>
                    </div>

                    <div class="stat-box d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-double text-success mr-2"></i> Onaylı Maçlar</span>
                        <span class="badge badge-success badge-pill"><?= $onayli_sayisi ?></span>
                    </div>

                    <hr>
                    <a href="index.php" class="btn btn-outline-success btn-block"><i class="fas fa-plus-circle"></i> Yeni Rezervasyon</a>
                    <a href="cikis.php" class="btn btn-outline-danger btn-block btn-sm mt-2">Güvenli Çıkış</a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <?= $mesaj ?>
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Saha / Tarih</th>
                                    <th>Saat</th>
                                    <th>Durum</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($rezervasyonlar as $r): ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-bold"><?= htmlspecialchars($r['saha_adi']) ?></div>
                                        <small class="text-muted"><?= date('d.m.Y', strtotime($r['tarih'])) ?></small>
                                    </td>
                                    <td class="align-middle"><?= substr($r['baslangic_saati'], 0, 5) ?></td>
                                    <td class="align-middle">
                                        <?php 
                                        $s = $r['durum'];
                                        $cls = ($s == 'onaylandı') ? 'success' : (($s == 'iptal') ? 'danger' : 'warning');
                                        ?>
                                        <span class="status-badge badge-<?= $cls ?>"><?= strtoupper($s) ?></span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php if($s == 'onay bekliyor'): ?>
                                            <a href="?iptal_id=<?= $r['id'] ?>" class="text-danger" title="Talebi İptal Et"><i class="fas fa-trash-alt"></i></a>
                                        <?php else: ?>
                                            -
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
    </div>
</div>

</body>
</html>