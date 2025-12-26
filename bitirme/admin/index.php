<?php
session_start();
require_once('../db_baglanti.php'); 
require_once('../config.php'); 

$mesaj = "";

// Kullanıcı zaten giriş yapmışsa direkt panoya yönlendir
if (isset($_SESSION['yonetici_id'])) {
    header("Location: " . BASE_URL . "/admin/dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $sifre = trim($_POST['sifre']);

    if (empty($email) || empty($sifre)) {
        $mesaj = "<div class='alert alert-danger'>Lütfen tüm alanları doldurunuz.</div>";
    } else {
        try {
            $stmt = $db->prepare("SELECT id, ad_soyad, sifre, rol FROM kullanicilar WHERE email = :email LIMIT 1");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($kullanici) {
                // GÜVENLİ KONTROL: Girilen şifreyi, DB'deki hash ile karşılaştır
                if (password_verify($sifre, $kullanici['sifre'])) { 
                    
                    if ($kullanici['rol'] === 'yonetici') {
                        
                        $_SESSION['yonetici_id'] = $kullanici['id'];
                        $_SESSION['yonetici_adi'] = $kullanici['ad_soyad'];
                        
                        // Başarılı yönlendirme
                        header("Location: " . BASE_URL . "/admin/dashboard.php"); 
                        exit;

                    } else {
                        $mesaj = "<div class='alert alert-warning'>Bu alana erişim yetkiniz yok.</div>";
                    }

                } else {
                    $mesaj = "<div class='alert alert-danger'>Hatalı e-posta veya şifre.</div>";
                }
            } else {
                $mesaj = "<div class='alert alert-danger'>Hatalı e-posta veya şifre.</div>";
            }

        } catch (PDOException $e) {
            $mesaj = "<div class='alert alert-danger'>Hata: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönetici Girişi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-container { max-width: 400px; margin-top: 100px; }
    </style>
</head>
<body>
    <div class="container login-container">
        <h2 class="text-center">Yönetici Girişi</h2>
        <?php echo $mesaj; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">E-Posta</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="sifre">Şifre</label>
                <input type="password" class="form-control" id="sifre" name="sifre" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Giriş Yap</button>
        </form>
        <div class="mt-3 text-center">
            <p>Saha yöneticisi misiniz? <br> 
            <a href="kayit.php" class="btn btn-sm btn-outline-secondary mt-2">Yeni Yönetici Kaydı Oluştur</a></p>
        </div>
    </div>
</body>
</html>