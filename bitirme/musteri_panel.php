<?php
session_start();
require_once('db_baglanti.php');
require_once('config.php');

if (!isset($_SESSION['kullanici_id'])) { header("Location: giris.php"); exit; }

$kullanici_id = $_SESSION['kullanici_id'];
$sorgu = $db->prepare("SELECT r.*, s.saha_adi FROM rezervasyonlar r 
                       JOIN sahalar s ON r.saha_id = s.id 
                       WHERE r.kullanici_id = ? ORDER BY r.tarih DESC");
$sorgu->execute([$kullanici_id]);
$randevular = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>