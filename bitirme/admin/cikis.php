<?php
session_start();
require_once('../config.php'); 

// Oturum değişkenlerini ve oturumu sonlandır
unset($_SESSION['yonetici_id']);
unset($_SESSION['yonetici_adi']);
session_destroy();

// Mutlak yol kullanarak güvenli yönlendirme
header("Location: " . BASE_URL . "/admin/index.php"); 
exit;
?>