<?php
session_start();
include '../includes/koneksi.php';

// Proteksi: Hanya admin yang bisa akses
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    echo "<script>alert('Akses Ditolak! Hanya admin yang bisa masuk.'); window.location='../index.php';</script>";
    exit;
}

// Ambil data produk
$query_produk = "SELECT * FROM produk ORDER BY id_produk DESC";
$result_produk = mysqli_query($koneksi, $query_produk);

// Ambil data user
$query_user = "SELECT * FROM users ORDER BY id_user DESC";
$result_user = mysqli_query($koneksi, $query_user);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab-btn { padding: 10px 20px; border: none; background: #ddd; cursor: pointer; border-radius: 5px; font-weight: 500; }
        .tab-btn.active { background: #3498db; color: #fff; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <p style="text-align:center; color:#bdc3c7; font-size:0.9rem;">Halo, <?= $_SESSION['user']['nama'] ?></p>
            <p style="text-align:center; color:#f39c12; font-size:0.8rem; margin-bottom:15px;">Role: <?= strtoupper($_SESSION['role']) ?></p>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="tambah_produk.php">Tambah Produk</a>
            <a href="tambah_user.php">Tambah User</a>
            <a href="../index.php" target="_blank">Lihat Website</a>
            <a href="../user/logout.php">Logout</a>
        </div>
        <div class="main-content">
            <h1>Dashboard Admin</h1>
            
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('produk')">Produk</button>
                <button class="tab-btn" onclick="showTab('user')">User</button>
            </div>

            <!-- Tab Produk -->
            <div id="produk" class="tab-content active">
                <h2>Daftar Produk</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result_produk)): ?>
                            <tr>
                                <td><?= $row['id_produk'] ?></td>
                                <td><?= $row['nama_barang'] ?></td>
                                <td>Rp <?= number_format($row['harga']) ?></td>
                                <td><?= $row['stok'] ?></td>
                                <td>
                                    <?php
                                    $kat = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_kategori FROM kategori WHERE id_kategori='".$row['id_kategori']."'"));
                                    echo $kat['nama_kategori'] ?? '-';
                                    ?>
                                </td>
                                <td>
                                    <a href="edit_produk.php?id=<?= $row['id_produk'] ?>" class="btn" style="padding:5px 10px; font-size:0.8rem">Edit</a>
                                    <a href="delete_produk.php?id=<?= $row['id_produk'] ?>" class="btn" style="padding:5px 10px; font-size:0.8rem; background:#e74c3c" onclick="return confirm('Hapus?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab User -->
            <div id="user" class="tab-content">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                    <h2>Daftar User</h2>
                    <a href="tambah_user.php" class="btn">+ Tambah User</a>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. Telp</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result_user)): ?>
                            <tr>
                                <td><?= $row['id_user'] ?></td>
                                <td><?= $row['nama'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td><?= $row['no_tlp'] ?? '-' ?></td>
                                <td>
                                    <span class="badge <?= $row['role'] == 'admin' ? 'bg-danger' : 'bg-success' ?>">
                                        <?= strtoupper($row['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit_user.php?id=<?= $row['id_user'] ?>" class="btn" style="padding:5px 10px; font-size:0.8rem">Edit</a>
                                    <?php if($row['id_user'] != $_SESSION['user']['id_user']): ?>
                                    <a href="delete_user.php?id=<?= $row['id_user'] ?>" class="btn" style="padding:5px 10px; font-size:0.8rem; background:#e74c3c" onclick="return confirm('Hapus user ini?')">Hapus</a>
                                    <?php else: ?>
                                    <span style="color:#999; font-size:0.8rem;">(Anda)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>