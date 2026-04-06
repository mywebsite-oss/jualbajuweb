<?php
session_start();
include '../includes/koneksi.php';

// Proteksi: Cek apakah user sudah login
if(!isset($_SESSION['user_id'])){
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

// Cek role (admin akan diarahkan ke admin dashboard)
if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){
    echo "<script>window.location='../admin/dashboard.php';</script>";
    exit;
}

$id_user = $_SESSION['user_id'];

$query = "SELECT p.*, pr.nama_barang, pr.gambar, pb.bukti_transfer 
          FROM pesanan p 
          JOIN produk pr ON p.id_produk = pr.id_produk 
          LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
          WHERE p.id_user = '$id_user' 
          ORDER BY p.tgl_pesanan DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container navbar">
            <div class="logo">My Account</div>
            <nav class="nav-links">
                <a href="index.php">Belanja</a>
                <a href="chart.php">Keranjang</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>
    <div class="container" style="padding: 40px 20px;">
        <h2>Halo, <?= $_SESSION['nama'] ?? $_SESSION['user']['nama'] ?> 👋</h2>
        <p style="color:#777; margin-bottom:20px;">Role: <strong><?= strtoupper($_SESSION['role'] ?? 'user') ?></strong></p>
        
        <h3 style="margin:20px 0 15px;">Riwayat Pesanan</h3>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?= $row['id_pesanan'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['tgl_pesanan'])) ?></td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <img src="../assets/produk/<?= $row['gambar'] ?>" style="width:40px; height:40px; object-fit:cover; border-radius:5px;" onerror="this.src='https://via.placeholder.com/40'">
                                <?= $row['nama_barang'] ?>
                            </div>
                        </td>
                        <td><?= $row['qty'] ?></td>
                        <td>Rp <?= number_format($row['total']) ?></td>
                        <td><?= $row['method_bayar'] ?></td>
                        <td>
                            <span class="badge <?= $row['status'] == 'Berhasil' ? 'bg-success' : ($row['status'] == 'Gagal' ? 'bg-danger' : 'bg-warning') ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td><?= $row['status_bayar'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div style="text-align:center; padding:50px; background:#fff; border-radius:8px; margin-top:20px;">
            <p style="color:#777; font-size:1.1rem;">Belum ada pesanan</p>
            <a href="index.php" class="btn" style="margin-top:15px;">Mulai Belanja</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>