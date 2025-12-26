<?php
session_start();
require_once('config.php'); // URL sabiti için

// ----------------------------------------------------
// MÜŞTERİ ÇIKIŞI İŞLEMİ
// ----------------------------------------------------
if (isset($_SESSION['kullanici_id'])) {
    // Müşteri oturum değişkenlerini temizle
    unset($_SESSION['kullanici_id']);
    unset($_SESSION['kullanici_adi']);
}

// ----------------------------------------------------
// YÖNETİCİ ÇIKIŞI İŞLEMİ (Eğer admin klasöründen çağrılırsa)
// ----------------------------------------------------
// Normalde bu dosya admin klasöründen çağrılmamalı, ama güvenlik için kontrol edelim.
if (isset($_SESSION['yonetici_id'])) {
    // Yönetici oturum değişkenlerini temizle
    unset($_SESSION['yonetici_id']);
    unset($_SESSION['yonetici_adi']);
    
    // Yöneticinin çıkışı, Admin Giriş sayfasına yönlendirilir
    header("Location: " . BASE_URL . "/admin/index.php"); 
    exit;
}

// Oturumu tamamen sonlandır
session_destroy();

// Müşteriyi Giriş sayfasına yönlendir
header("Location: " . BASE_URL . "/giris.php"); 
exit;
?>