<?php
session_start();
require_once('../db_baglanti.php');
require_once('../config.php');

// Admin Kontrolü
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: index.php");
    exit;
}

// İstatistikleri Çek
$toplam_saha = $db->query("SELECT COUNT(*) FROM sahalar")->fetchColumn();
$toplam_uye = $db->query("SELECT COUNT(*) FROM kullanicilar WHERE rol = 'musteri'")->fetchColumn();
$onay_bekleyen = $db->query("SELECT COUNT(*) FROM rezervasyonlar WHERE durum = 'onay bekliyor'")->fetchColumn();
$toplam_kazanc = $db->query("SELECT SUM(s.saatlik_fiyat) FROM rezervasyonlar r JOIN sahalar s ON r.saha_id = s.id WHERE r.durum = 'onaylandı'")->fetchColumn();
$toplam_kazanc = $toplam_kazanc ? $toplam_kazanc : 0;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönetim Paneli | Halı Saha</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: #1e5631; min-height: 100vh; color: white; padding-top: 20px; }
        .sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; display: block; padding: 12px 20px; transition: 0.3s; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid #a4de02; }
        .stat-card { border: none; border-radius: 15px; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .bg-gradient-green { background: linear-gradient(45deg, #1e5631, #2d8149); color: white; }
        .bg-gradient-blue { background: linear-gradient(45deg, #2c3e50, #4ca1af); color: white; }
        .bg-gradient-orange { background: linear-gradient(45deg, #f39c12, #f1c40f); color: white; }
        .bg-gradient-red { background: linear-gradient(45deg, #c0392b, #e74c3c); color: white; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar d-none d-md-block shadow">
            <div class="text-center mb-4">
                <i class="fas fa-user-shield fa-3x"></i>
                <h6 class="mt-2">Admin Panel</h6>
            </div>
            <hr class="bg-secondary">
            <a href="dashboard.php"><i class="fas fa-home mr-2"></i> Dashboard</a>
            <a href="sahalar.php"><i class="fas fa-futbol mr-2"></i> Saha Yönetimi</a>
            <a href="rezervasyonlar.php"><i class="fas fa-calendar-check mr-2"></i> Rezervasyonlar 
                <?php if($onay_bekleyen > 0): ?>
                    <span class="badge badge-danger"><?= $onay_bekleyen ?></span>
                <?php endif; ?>
            </a>
            <a href="kullanicilar.php"><i class="fas fa-users mr-2"></i> Üyeler</a>
            <a href="ayarlar.php"><i class="fas fa-cog mr-2"></i> Ayarlar</a>
            <a href="cikis.php" class="text-danger"><i class="fas fa-sign-out-alt mr-2"></i> Çıkış Yap</a>
        </div>

        <div class="col-md-10 p-5">
            <h2 class="mb-4">Sistem Özeti</h2>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="card stat-card shadow-sm bg-gradient-blue p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase small">Toplam Saha</h6>
                                <h2 class="mb-0"><?= $toplam_saha ?></h2>
                            </div>
                            <i class="fas fa-map-marked-alt fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stat-card shadow-sm bg-gradient-green p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase small">Aktif Üyeler</h6>
                                <h2 class="mb-0"><?= $toplam_uye ?></h2>
                            </div>
                            <i class="fas fa-users fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stat-card shadow-sm bg-gradient-orange p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase small">Onay Bekleyen</h6>
                                <h2 class="mb-0"><?= $onay_bekleyen ?></h2>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stat-card shadow-sm bg-gradient-red p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase small">Toplam Kazanç</h6>
                                <h2 class="mb-0"><?= number_format($toplam_kazanc, 0, ',', '.') ?> ₺</h2>
                            </div>
                            <i class="fas fa-lira-sign fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4 border-0" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="card-title mb-4 text-success font-weight-bold">Hızlı İşlemler</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <a href="rezervasyonlar.php" class="btn btn-outline-warning btn-block py-3">
                                <i class="fas fa-tasks mr-2"></i> Bekleyen Onaylara Git
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="sahalar.php" class="btn btn-outline-primary btn-block py-3">
                                <i class="fas fa-plus-circle mr-2"></i> Yeni Saha Tanımla
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="../index.php" target="_blank" class="btn btn-outline-info btn-block py-3">
                                <i class="fas fa-external-link-alt mr-2"></i> Siteyi Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>