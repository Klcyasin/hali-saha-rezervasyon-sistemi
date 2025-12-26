<?php
session_start();
require_once('db_baglanti.php');
require_once('config.php');

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad_soyad = trim($_POST['ad_soyad']);
    $email = trim($_POST['email']);
    $sifre = $_POST['sifre'];

    if (!empty($ad_soyad) && !empty($email) && !empty($sifre)) {
        // E-posta kontrolü (Risk Azaltma)
        $kontrol = $db->prepare("SELECT id FROM kullanicilar WHERE email = ?");
        $kontrol->execute([$email]);
        
        if ($kontrol->fetch()) {
            $mesaj = "<div class='alert alert-warning'>Bu e-posta zaten kayıtlı!</div>";
        } else {
            $hashli_sifre = password_hash($sifre, PASSWORD_DEFAULT);
            $ekle = $db->prepare("INSERT INTO kullanicilar (ad_soyad, email, sifre, rol) VALUES (?, ?, ?, 'musteri')");
            if ($ekle->execute([$ad_soyad, $email, $hashli_sifre])) {
                $mesaj = "<div class='alert alert-success'>Kayıt başarılı! Giriş yapabilirsiniz.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kaydol | Halı Saha Sistemi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1529900748604-07564a03e7a6?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .register-card {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            color: white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 10px;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            box-shadow: none;
            border-color: #a4de02;
        }
        .btn-register {
            background: #1e5631;
            border: 2px solid #a4de02;
            color: #a4de02;
            font-weight: bold;
            padding: 12px;
            border-radius: 10px;
            transition: 0.3s;
        }
        .btn-register:hover {
            background: #a4de02;
            color: #1e5631;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="register-card">
                <div class="text-center mb-4">
                    <h3>Sisteme Katılın</h3>
                    <p class="text-white-50">Ücretsiz kayıt olun ve hemen yerinizi ayırtın.</p>
                </div>
                <?= $mesaj ?>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label><i class="fas fa-user mr-2"></i> Ad Soyad</label>
                            <input type="text" name="ad_soyad" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label><i class="fas fa-envelope mr-2"></i> E-Posta</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label><i class="fas fa-lock mr-2"></i> Şifre Belirleyin</label>
                            <input type="password" name="sifre" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-register btn-block shadow">KAYIT OL</button>
                </form>
                <div class="mt-4 text-center">
                    <p class="mb-0">Zaten üye misiniz? <a href="giris.php" style="color: #a4de02;">Giriş Yapın</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>