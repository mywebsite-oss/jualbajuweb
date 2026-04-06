<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_penjualan";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}
?>