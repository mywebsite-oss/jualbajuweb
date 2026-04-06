<?php
session_start();
include '../includes/koneksi.php';

// Ambil produk
$query = "SELECT * FROM produk ORDER BY id_produk DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jual Baju Web - Home</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container navbar">
            <div class="logo">FashionStore</div>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <?php if(isset($_SESSION['user'])): ?>
                    <a href="chart.php">Keranjang</a>
                    <a href="dashboard.php">Akun Saya</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="register.php">Register</a>
                    <a href="login.php">Login</a> 
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="hero">
        <div class="hero-content">
            <h1>Temukan Gaya Terbaikmu</h1>
            <p>Koleksi baju terbaru dengan harga terjangkau</p>
            <br>
            <a href="#produk" class="btn">Belanja Sekarang</a>
        </div>
    </div>

    <div class="container" id="produk">
        <h2 style="margin: 40px 0 20px;">Produk Terbaru</h2>
        <div class="product-grid">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="product-card">
                <!-- Gunakan gambar placeholder jika tidak ada gambar -->
                <img src="../assets/produk/<?= $row['gambar'] ?? 'default.jpg' ?>" alt="<?= $row['nama_barang'] ?>" class="product-img" onerror="this.src='https://image.qwenlm.ai/public_source/3206b955-692a-4d22-9b50-96026ab7e4a0/12961fae4-f282-4ad4-8d70-4a2144291483.png'">
                <div class="product-info">
                    <h3 class="product-title"><?= $row['nama_barang'] ?></h3>
                    <p class="product-price">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                    <p class="product-desc"><?= substr($row['deskripsi'], 0, 50) ?>...</p>
                    <?php if(isset($_SESSION['user'])): ?>
                        <form action="chart.php" method="POST">
                            <input type="hidden" name="id_produk" value="<?= $row['id_produk'] ?>">
                            <input type="hidden" name="nama_barang" value="<?= $row['nama_barang'] ?>">
                            <input type="hidden" name="harga" value="<?= $row['harga'] ?>">
                            <button type="submit" name="add_to_cart" class="btn" style="width:100%">Tambah ke Keranjang</button>
                        </form>
                    <?php else: ?>
                        <a href="register.php" class="btn" style="width:100%; display:block; text-align:center;">Login untuk Beli</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>