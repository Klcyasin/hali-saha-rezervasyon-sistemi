<?php
session_start();
require_once('db_baglanti.php');
require_once('config.php');

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $sifre = $_POST['sifre'];

    if (!empty($email) && !empty($sifre)) {
        $sorgu = $db->prepare("SELECT * FROM kullanicilar WHERE email = ?");
        $sorgu->execute([$email]);
        $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

        if ($kullanici && password_verify($sifre, $kullanici['sifre'])) {
            $_SESSION['kullanici_id'] = $kullanici['id'];
            $_SESSION['kullanici_adi'] = $kullanici['ad_soyad'];
            header("Location: index.php");
            exit;
        } else {
            $mesaj = "<div class='alert alert-danger'>E-posta veya şifre hatalı!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap | Halı Saha Sistemi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1574629810360-7efbbe195018?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px;
            color: white;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }
        .form-control {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 12px 20px;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            box-shadow: none;
        }
        .btn-login {
            background: #a4de02;
            border: none;
            color: #1e5631;
            font-weight: bold;
            padding: 12px;
            border-radius: 10px;
            transition: 0.3s;
        }
        .btn-login:hover {
            background: #1e5631;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="login-card text-center">
                <i class="fas fa-futbol fa-4x mb-4" style="color: #a4de02;"></i>
                <h2 class="mb-4">Tekrar Hoş Geldin!</h2>
                <?= $mesaj ?>
                <form method="POST">
                    <div class="form-group text-left">
                        <label><i class="fas fa-envelope mr-2"></i> E-Posta Adresi</label>
                        <input type="email" name="email" class="form-control" placeholder="E-posta girin" required>
                    </div>
                    <div class="form-group text-left">
                        <label><i class="fas fa-lock mr-2"></i> Şifre</label>
                        <input type="password" name="sifre" class="form-control" placeholder="Şifrenizi girin" required>
                    </div>
                    <button type="submit" class="btn btn-login btn-block shadow mt-4">GİRİŞ YAP</button>
                </form>
                <div class="mt-4">
                    <p class="mb-0">Hesabınız yok mu? <a href="kayit.php" style="color: #a4de02;">Hemen Kaydolun</a></p>
                    <a href="index.php" class="text-white-50 small mt-2 d-inline-block">Misafir Olarak Devam Et</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>