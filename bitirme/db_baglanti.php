<?php
// Lütfen bu bilgileri KENDİ InfinityFree veritabanı bilgilerinizle değiştirin!
$host = "sql103.infinityfree.com"; 
$kullanici = "if0_40106441"; 
$sifre = "oxeZSrLxoi9Eg"; 
$db_adi = "if0_40106441_bitirme"; 

try {
    // ÖNEMLİ: Bağlantı dizesine 'charset=utf8' ifadesini ekleyin
    $db = new PDO("mysql:host=$host;dbname=$db_adi;charset=utf8", $kullanici, $sifre);
    
    // Gerekirse, bağlantı kurulduktan hemen sonra Türkçe karakter seti ayarını zorlayın
    $db->exec("SET NAMES utf8");
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı kurulamadı: " . $e->getMessage());
}
?>