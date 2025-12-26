<?php
session_start();
require_once('../db_baglanti.php');
require_once('../config.php');

// GÜVENLİK KONTROLÜ (Erişim Denetimi)
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: index.php");
    exit;
}

$mesaj = "";

// 1. YENİ SAHA EKLEME İŞLEMİ (Risk Azaltma Stratejisi [cite: 63])
if (isset($_POST['saha_ekle'])) {
    $saha_adi = trim($_POST['saha_adi']);
    $fiyat = $_POST['fiyat'];
    $aciklama = trim($_POST['aciklama']);
    $ozellikler = trim($_POST['ozellikler']);

    if (!empty($saha_adi) && !empty($fiyat)) {
        try {
            $ekle = $db->prepare("INSERT INTO sahalar (saha_adi, saatlik_fiyat, aciklama, ozellikler) VALUES (?, ?, ?, ?)");
            $ekle->execute([$saha_adi, $fiyat, $aciklama, $ozellikler]);
            $mesaj = "<div class='alert alert-success shadow-sm'>Yeni saha başarıyla sisteme eklendi!</div>";
        } catch (PDOException $e) {
            $mesaj = "<div class='alert alert-danger'>Hata: " . $e->getMessage() . "</div>";
        }
    } else {
        $mesaj = "<div class='alert alert-warning'>Lütfen saha adı ve fiyat alanlarını doldurun.</div>";
    }
}

// 2. SAHA SİLME İŞLEMİ
if (isset($_GET['sil_id'])) {
    $sil_id = (int)$_GET['sil_id'];
    $db->prepare("DELETE FROM sahalar WHERE id = ?")->execute([$sil_id]);
    header("Location: sahalar.php?mesaj=silindi");
    exit;
}

// 3. MEVCUT SAHALARI LİSTELE (Raporlama ve İletişim [cite: 83])
$sahalar = $db->query("SELECT * FROM sahalar ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Saha Yönetimi | Admin</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 15px; border: none; }
        .table img { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-futbol text-success"></i> Saha Yönetim Paneli</h2>
        <a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Panele Dön</a>
    </div>

    <?= $mesaj ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white font-weight-bold">
                    Yeni Saha Ekle
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label>Saha Adı</label>
                            <input type="text" name="saha_adi" class="form-control" placeholder="Örn: Kuzey Sahası" required>
                        </div>
                        <div class="form-group">
                            <label>Saatlik Ücret (TL)</label>
                            <input type="number" name="fiyat" class="form-control" placeholder="Örn: 500" required>
                        </div>
                        <div class="form-group">
                            <label>Saha Açıklaması</label>
                            <textarea name="aciklama" class="form-control" rows="3" placeholder="Zemin türü, ışıklandırma vb."></textarea>
                        </div>
                        <div class="form-group">
                            <label>İmkanlar (Virgülle ayırın)</label>
                            <input type="text" name="ozellikler" class="form-control" placeholder="Örn: Duş, Otopark, Kafeterya">
                        </div>
                        <button type="submit" name="saha_ekle" class="btn btn-success btn-block shadow-sm">
                            <i class="fas fa-plus-circle"></i> Sahayı Kaydet
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white font-weight-bold">
                    Mevcut Sahalar
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Saha Adı</th>
                                <th>Fiyat</th>
                                <th class="text-center">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sahalar as $saha): ?>
                            <tr>
                                <td><?= $saha['id'] ?></td>
                                <td><strong><?= htmlspecialchars($saha['saha_adi']) ?></strong></td>
                                <td><?= number_format($saha['saatlik_fiyat'], 2) ?> TL</td>
                                <td class="text-center">
                                    <a href="saha_duzenle.php?id=<?= $saha['id'] ?>" class="btn btn-sm btn-info" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?sil_id=<?= $saha['id'] ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Bu sahayı silmek istediğinize emin misiniz?')" title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </a>
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

</body>
</html>