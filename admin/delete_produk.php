<?php
session_start();
include '../includes/koneksi.php';

// Proteksi: Hanya admin yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../user/index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Gunakan Prepared Statement
    $stmt = $koneksi->prepare("DELETE FROM produk WHERE id_produk = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: dashboard.php");
?>