<?php
session_start();
require_once('db_baglanti.php'); 
require_once('config.php'); 

$giris_yapildi = isset($_SESSION['kullanici_id']);
$secilen_tarih = isset($_GET['tarih']) ? $_GET['tarih'] : date('Y-m-d');
$secilen_saha_id = isset($_GET['saha_id']) ? (int)$_GET['saha_id'] : null;

try {
    $sahalar = $db->query("SELECT * FROM sahalar ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    if (!$secilen_saha_id && !empty($sahalar)) { $secilen_saha_id = $sahalar[0]['id']; }
    
    // Seçili sahanın detaylarını getir
    $saha_stmt = $db->prepare("SELECT * FROM sahalar WHERE id = ?");
    $saha_stmt->execute([$secilen_saha_id]);
    $aktif_saha = $saha_stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die("Hata: " . $e->getMessage()); }

$dolu_saatler = [];
if ($secilen_saha_id) {
    $stmt = $db->prepare("SELECT baslangic_saati FROM rezervasyonlar WHERE saha_id = ? AND tarih = ? AND durum != 'iptal'");
    $stmt->execute([$secilen_saha_id, $secilen_tarih]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $dolu_saatler[] = substr($row['baslangic_saati'], 0, 5); }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halı Saha Rezervasyon Sistemi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        :root { --primary: #1e5631; --secondary: #a4de02; --dark: #121212; --light: #f8f9fa; }
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        
        /* Navbar */
        .navbar { background: var(--primary) !important; border-bottom: 3px solid var(--secondary); }
        .navbar-brand { font-weight: 600; color: var(--secondary) !important; }
        
        /* Hero Section */
        .hero { background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1529900748604-07564a03e7a6?auto=format&fit=crop&w=1350&q=80'); 
                background-size: cover; background-position: center; color: white; padding: 80px 0; margin-bottom: -50px; }
        
        /* Cards */
        .main-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .saha-info-badge { background: var(--light); border-radius: 10px; padding: 15px; border-left: 5px solid var(--secondary); }
        
        /* Buttons */
        .btn-time { border-radius: 12px; padding: 12px; font-weight: 600; transition: all 0.3s; border: 2px solid transparent; }
        .btn-available { background: #e8f5e9; color: #2e7d32; border-color: #c8e6c9; }
        .btn-available:hover { background: var(--primary); color: white; transform: translateY(-3px); }
        .btn-full { background: #ffebee; color: #c62828; cursor: not-allowed; }
        .btn-past { background: #f5f5f5; color: #9e9e9e; cursor: not-allowed; }
        
        footer { background: var(--dark); color: white; padding: 40px 0; margin-top: 60px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-futbol mr-2"></i>GOAL ZONE</a>
        <div class="ml-auto">
            <?php if($giris_yapildi): ?>
                <a href="hesabim.php" class="btn btn-outline-light rounded-pill px-4 mr-2">Panelim</a>
                <a href="cikis.php" class="btn btn-danger rounded-pill px-4">Çıkış</a>
            <?php else: ?>
                <a href="giris.php" class="btn btn-outline-light rounded-pill px-4 mr-2">Giriş</a>
                <a href="kayit.php" class="btn btn-secondary rounded-pill px-4 text-dark font-weight-bold">Kaydol</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="hero text-center">
    <div class="container">
        <h1 class="display-4 font-weight-bold">Sahada Yerini Ayırt</h1>
        <p class="lead">Şehrin en iyi halı sahaları bir tık uzağında.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card main-card p-4 sticky-top" style="top: 100px;">
                <h5 class="text-primary font-weight-bold mb-4"><i class="fas fa-filter mr-2"></i>Hızlı Rezervasyon</h5>
                <form method="GET">
                    <div class="form-group">
                        <label class="small font-weight-bold">SAHA SEÇİN</label>
                        <select name="saha_id" class="form-control rounded-pill" onchange="this.form.submit()">
                            <?php foreach($sahalar as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= $s['id'] == $secilen_saha_id ? 'selected' : '' ?>><?= $s['saha_adi'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">TARİH SEÇİN</label>
                        <input type="date" name="tarih" class="form-control rounded-pill" value="<?= $secilen_tarih ?>" min="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
                    </div>
                </form>

                <?php if($aktif_saha): ?>
                <div class="saha-info-badge mt-4">
                    <h6 class="mb-1"><?= $aktif_saha['saha_adi'] ?></h6>
                    <p class="text-success font-weight-bold mb-2"><?= number_format($aktif_saha['saatlik_fiyat'], 2) ?> ₺ / Saat</p>
                    <a href="saha_detay.php?id=<?= $aktif_saha['id'] ?>" class="btn btn-sm btn-block btn-primary rounded-pill">Detayları Gör</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card main-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h4 class="mb-0 font-weight-bold"><?= date('d.m.Y', strtotime($secilen_tarih)) ?></h4>
                    <span class="badge badge-success p-2">Müsaitlik Durumu</span>
                </div>

                <div class="row text-center">
                    <?php 
                    $su_an_saat = date('H:i');
                    for($i=9; $i<=23; $i++): 
                        $saat = str_pad($i, 2, '0', STR_PAD_LEFT).":00";
                        $is_dolu = in_array($saat, $dolu_saatler);
                        $is_past = ($secilen_tarih == date('Y-m-d') && $saat <= $su_an_saat);
                    ?>
                        <div class="col-md-4 col-6 mb-3">
                            <?php if($is_dolu): ?>
                                <button class="btn btn-time btn-full btn-block" disabled><?= $saat ?><br><small>DOLU</small></button>
                            <?php elseif($is_past): ?>
                                <button class="btn btn-time btn-past btn-block" disabled><?= $saat ?><br><small>GEÇTİ</small></button>
                            <?php else: ?>
                                <form method="POST" action="rezervasyon_yap.php">
                                    <input type="hidden" name="saha_id" value="<?= $secilen_saha_id ?>">
                                    <input type="hidden" name="tarih" value="<?= $secilen_tarih ?>">
                                    <input type="hidden" name="saat" value="<?= $saat ?>">
                                    <button type="submit" class="btn btn-time btn-available btn-block" onclick="return confirm('<?= $saat ?> saati için rezervasyon yapılacak?')"><?= $saat ?><br><small>MÜSAİT</small></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="container text-center">
        <p class="mb-0">© 2025 Goal Zone Halı Saha Rezervasyon Sistemi</p>
        <small class="text-muted">Proje Başarıyla Geliştirilmeye Devam Ediyor</small>
    </div>
</footer>

</body>
</html>