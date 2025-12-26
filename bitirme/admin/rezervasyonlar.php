<?php
session_start();
require_once('../db_baglanti.php');
require_once('../config.php');

// Admin Kontrolü
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: index.php");
    exit;
}

$mesaj = "";

// ✅ ONAYLA VEYA ❌ İPTAL ET İŞLEMİ (Operasyonel Risk Yönetimi) [cite: 37, 59]
if (isset($_GET['islem']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $yeni_durum = ($_GET['islem'] == 'onayla') ? 'onaylandı' : 'iptal';
    
    $guncelle = $db->prepare("UPDATE rezervasyonlar SET durum = ? WHERE id = ?");
    if ($guncelle->execute([$yeni_durum, $id])) {
        $mesaj = "<div class='alert alert-success shadow-sm'>İşlem başarıyla tamamlandı. Durum: " . strtoupper($yeni_durum) . "</div>";
    }
}

// LİSTEYİ ÇEK (Raporlama ve Şeffaflık) [cite: 83, 84]
$sorgu = $db->query("SELECT r.*, s.saha_adi, k.ad_soyad, k.email 
                     FROM rezervasyonlar r 
                     JOIN sahalar s ON r.saha_id = s.id 
                     JOIN kullanicilar k ON r.kullanici_id = k.id 
                     ORDER BY r.durum DESC, r.tarih ASC");
$rezervasyonlar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Rezervasyon Yönetimi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .table-card { border-radius: 15px; border: none; overflow: hidden; background: white; }
        .status-pill { padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.8rem; }
        .btn-action { width: 35px; height: 35px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s; }
        .btn-action:hover { transform: scale(1.1); }
    </style>
</head>
<body>

<div class="container-fluid mt-5 px-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-calendar-check text-primary mr-2"></i> Rezervasyon Talepleri</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-chevron-left"></i> Paneli Dön</a>
    </div>

    <?= $mesaj ?>

    <div class="card table-card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Müşteri</th>
                            <th>Saha</th>
                            <th>Tarih</th>
                            <th>Saat</th>
                            <th>Durum</th>
                            <th class="text-center">Hızlı İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rezervasyonlar as $r): ?>
                        <tr>
                            <td class="align-middle">
                                <div class="font-weight-bold"><?= htmlspecialchars($r['ad_soyad']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($r['email']) ?></small>
                            </td>
                            <td class="align-middle"><?= htmlspecialchars($r['saha_adi']) ?></td>
                            <td class="align-middle"><?= date('d.m.Y', strtotime($r['tarih'])) ?></td>
                            <td class="align-middle font-weight-bold"><?= substr($r['baslangic_saati'], 0, 5) ?></td>
                            <td class="align-middle">
                                <?php 
                                $d = $r['durum'];
                                $badge = ($d == 'onaylandı') ? 'success' : (($d == 'iptal') ? 'danger' : 'warning');
                                ?>
                                <span class="status-pill badge-<?= $badge ?> text-<?= $badge ?> bg-light border border-<?= $badge ?>">
                                    <?= strtoupper($d) ?>
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <?php if($d == 'onay bekliyor'): ?>
                                    <a href="?islem=onayla&id=<?= $r['id'] ?>" class="btn-action bg-success text-white mr-2" title="Onayla">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="?islem=iptal&id=<?= $r['id'] ?>" class="btn-action bg-danger text-white" title="Reddet" onclick="return confirm('Bu rezervasyonu iptal etmek istediğinize emin misiniz?')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">İşlem Tamamlandı</span>
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