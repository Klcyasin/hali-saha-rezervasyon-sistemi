<?php
session_start();
require_once('../db_baglanti.php');
require_once('../config.php');

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad_soyad = trim($_POST['ad_soyad']);
    $email = trim($_POST['email']);
    $sifre = trim($_POST['sifre']);
    $ozel_kod = trim($_POST['ozel_kod']); // Güvenlik için: Herkes admin olamasın diye bir kod

    // İşletme sahibi kayıt kodu (Bunu hoca için 'HALISAHA2025' yapalım)
    $dogrulama_kodu = "HALISAHA2025";

    if (empty($ad_soyad) || empty($email) || empty($sifre)) {
        $mesaj = "<div class='alert alert-danger'>Lütfen tüm alanları doldurun.</div>";
    } elseif ($ozel_kod !== $dogrulama_kodu) {
        $mesaj = "<div class='alert alert-danger'>Yönetici kayıt kodu hatalı!</div>";
    } else {
        try {
            $stmt = $db->prepare("SELECT id FROM kullanicilar WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $mesaj = "<div class='alert alert-danger'>Bu e-posta zaten bir yöneticiye ait.</div>";
            } else {
                $hashli_sifre = password_hash($sifre, PASSWORD_DEFAULT);
                $rol = 'yonetici'; // Burası kritik: Yetkiyi yönetici olarak veriyoruz.
                
                $stmt = $db->prepare("INSERT INTO kullanicilar (ad_soyad, email, sifre, rol) VALUES (?, ?, ?, ?)");
                $stmt->execute([$ad_soyad, $email, $hashli_sifre, $rol]);
                
                $mesaj = "<div class='alert alert-success'>Yönetici hesabı başarıyla oluşturuldu! <a href='index.php'>Giriş Yap</a></div>";
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
    <title>Saha Yöneticisi Kaydı</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background-color: #343a40; color: white; } .container { max-width: 450px; margin-top: 50px; } </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Saha İşletmecisi Kaydı</h2>
        <p class="text-center text-muted">Yönetici paneline erişim için hesap oluşturun.</p>
        <hr class="bg-light">
        <?php echo $mesaj; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Ad Soyad</label>
                <input type="text" name="ad_soyad" class="form-control" required>
            </div>
            <div class="form-group">
                <label>E-Posta</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="sifre" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Yönetici Kayıt Kodu (Hoca için: HALISAHA2025)</label>
                <input type="text" name="ozel_kod" class="form-control" placeholder="İşletme yetki kodunu girin" required>
            </div>
            <button type="submit" class="btn btn-warning btn-block">Yönetici Olarak Kaydol</button>
        </form>
        <div class="mt-3 text-center">
            <a href="index.php" class="text-light">Zaten hesabım var, giriş yap</a>
        </div>
    </div>
</body>
</html>