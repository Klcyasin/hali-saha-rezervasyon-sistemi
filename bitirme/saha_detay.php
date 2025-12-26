<?php
session_start();
require_once('db_baglanti.php');
require_once('config.php');

$saha_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $stmt = $db->prepare("SELECT * FROM sahalar WHERE id = ?");
    $stmt->execute([$saha_id]);
    $saha = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$saha) {
        die("Saha bulunamadı!");
    }
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($saha['saha_adi']) ?> | Detaylar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        .saha-header { background: #1e5631; color: white; padding: 60px 0; }
        .feature-icon { font-size: 2rem; color: #a4de02; margin-bottom: 10px; }
        .card-detail { border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .price-tag { font-size: 1.5rem; font-weight: bold; color: #1e5631; }
    </style>
</head>
<body class="bg-light">

<div class="saha-header text-center">
    <div class="container">
        <h1><?= htmlspecialchars($saha['saha_adi']) ?></h1>
        <p class="lead">Saha Özellikleri ve Rezervasyon Bilgileri</p>
    </div>
</div>

<div class="container mt-n4">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-detail p-4 mb-4">
                <h4><i class="fas fa-info-circle text-success"></i> Saha Açıklaması</h4>
                <p class="text-muted"><?= nl2br(htmlspecialchars($saha['aciklama'] ?? 'Bu saha için açıklama girilmemiş.')) ?></p>
                
                <h4 class="mt-4"><i class="fas fa-star text-success"></i> Sunulan İmkanlar</h4>
                <div class="row text-center mt-3">
                    <?php 
                    $ozellikler = explode(',', $saha['ozellikler'] ?? '');
                    if(!empty($ozellikler[0])):
                        foreach($ozellikler as $ozellik): 
                            $icon = "fa-check-circle"; // Varsayılan ikon
                            if(stripos($ozellik, 'duş') !== false) $icon = "fa-shower";
                            if(stripos($ozellik, 'otopark') !== false) $icon = "fa-car";
                            if(stripos($ozellik, 'krampon') !== false) $icon = "fa-shoe-prints";
                    ?>
                        <div class="col-md-3 mb-3">
                            <i class="fas <?= $icon ?> feature-icon"></i>
                            <p class="small font-weight-bold"><?= trim($ozellik) ?></p>
                        </div>
                    <?php endforeach; else: echo "<p class='ml-3'>Özellik belirtilmemiş.</p>"; endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-detail p-4 sticky-top" style="top: 20px;">
                <div class="text-center mb-4">
                    <span class="text-muted d-block">Saatlik Ücret</span>
                    <span class="price-tag"><?= number_format($saha['saatlik_fiyat'], 2) ?> TL</span>
                </div>
                <hr>
                <a href="index.php?saha_id=<?= $saha['id'] ?>" class="btn btn-success btn-lg btn-block shadow">
                    <i class="fas fa-calendar-alt"></i> Hemen Yer Ayırt
                </a>
                <a href="index.php" class="btn btn-outline-secondary btn-block mt-3">Geri Dön</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>