<?php
session_start();
require_once('../db_baglanti.php');
require_once('../config.php');

// Güvenlik: Admin girişi kontrolü
if (!isset($_SESSION['yonetici_id'])) { header("Location: index.php"); exit; }

$id = $_GET['id'];
$mesaj = "";

// 1. MEVCUT BİLGİLERİ GETİR
$sorgu = $db->prepare("SELECT * FROM sahalar WHERE id = ?");
$sorgu->execute([$id]);
$saha = $sorgu->fetch(PDO::FETCH_ASSOC);

// 2. GÜNCELLEME İŞLEMİ (POST GELİNCE)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $saha_adi = $_POST['saha_adi'];
    $fiyat = $_POST['fiyat'];
    $aciklama = $_POST['aciklama'];
    $ozellikler = $_POST['ozellikler'];

    try {
        $update = $db->prepare("UPDATE sahalar SET saha_adi = ?, saatlik_fiyat = ?, aciklama = ?, ozellikler = ? WHERE id = ?");
        $update->execute([$saha_adi, $fiyat, $aciklama, $ozellikler, $id]);
        $mesaj = "<div class='alert alert-success'>Saha başarıyla güncellendi!</div>";
        // Güncel veriyi tekrar çek
        $sorgu->execute([$id]);
        $saha = $sorgu->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $mesaj = "<div class='alert alert-danger'>Hata: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Saha Düzenle</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Saha Bilgilerini Güncelle</h5>
            </div>
            <div class="card-body">
                <?= $mesaj ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Saha Adı</label>
                        <input type="text" name="saha_adi" class="form-control" value="<?= htmlspecialchars($saha['saha_adi']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Saatlik Ücret (TL)</label>
                        <input type="number" name="fiyat" class="form-control" value="<?= $saha['saatlik_fiyat'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Açıklama</label>
                        <textarea name="aciklama" class="form-control" rows="3"><?= htmlspecialchars($saha['aciklama']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Özellikler (Virgülle ayırın)</label>
                        <input type="text" name="ozellikler" class="form-control" value="<?= htmlspecialchars($saha['ozellikler']) ?>" placeholder="Duş, Otopark, Kafeterya">
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-success">Değişiklikleri Kaydet</button>
                    <a href="sahalar.php" class="btn btn-secondary">İptal / Geri Dön</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>