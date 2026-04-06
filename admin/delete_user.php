<?php
session_start();
include '../includes/koneksi.php';

// Proteksi: Hanya admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../index.php"); exit;
}

$id = $_GET['id'];

// Cek apakah user yang akan dihapus adalah diri sendiri
if($id == $_SESSION['user']['id_user']){
    echo "<script>alert('Tidak bisa menghapus akun sendiri!'); window.location='dashboard.php';</script>";
    exit;
}

// Cek apakah user punya pesanan
$check = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM pesanan WHERE id_user='$id'");
$data = mysqli_fetch_assoc($check);

if($data['count'] > 0){
    echo "<script>alert('User tidak bisa dihapus karena masih memiliki pesanan!'); window.location='dashboard.php';</script>";
    exit;
}

// Hapus user
mysqli_query($koneksi, "DELETE FROM users WHERE id_user='$id'");
echo "<script>alert('User berhasil dihapus!'); window.location='dashboard.php';</script>";
?>