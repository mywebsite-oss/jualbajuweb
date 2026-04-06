<?php
session_start();
include '../includes/koneksi.php';

if(!isset($_SESSION['user'])){ 
    header("Location: login.php"); 
    exit; 
}

$id_user = $_SESSION['user']['id_user'];

// Tambah ke keranjang
if(isset($_POST['add_to_cart'])){
    $id_produk = $_POST['id_produk'];
    
    $produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT stok, nama_barang FROM produk WHERE id_produk='$id_produk'"));
    
    if($produk['stok'] <= 0){
        echo "<script>alert('Maaf, produk \"".$produk['nama_barang']."\" sudah habis!'); window.location='index.php';</script>";
        exit;
    }
    
    $check = mysqli_query($koneksi, "SELECT * FROM keranjang WHERE id_user='$id_user' AND id_produk='$id_produk'");
    
    if(mysqli_num_rows($check) > 0){
        $cart_item = mysqli_fetch_assoc($check);
        $new_qty = $cart_item['qty'] + 1;
        
        if($new_qty > $produk['stok']){
            echo "<script>alert('Qty melebihi stok tersedia! (Maksimal: {$produk['stok']})'); window.location='chart.php';</script>";
            exit;
        }
        
        mysqli_query($koneksi, "UPDATE keranjang SET qty = qty + 1 WHERE id_user='$id_user' AND id_produk='$id_produk'");
    } else {
        $qty = 1;
        mysqli_query($koneksi, "INSERT INTO keranjang (id_user, id_produk, qty) VALUES ('$id_user', '$id_produk', '$qty')");
    }
    header("Location: chart.php");
}

// Update qty - DIPERBAIKI
if(isset($_POST['update_qty'])){
    $id_keranjang = $_POST['id_keranjang']; // Sekarang dari form terpisah
    $qty = $_POST['qty']; // Sekarang single value, bukan array
    
    $cart_item = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT k.*, p.stok, p.nama_barang FROM keranjang k JOIN produk p ON k.id_produk = p.id_produk WHERE k.id_keranjang='$id_keranjang' AND k.id_user='$id_user'"));
    
    if($qty > $cart_item['stok']){
        echo "<script>alert('Qty melebihi stok tersedia untuk ".$cart_item['nama_barang']."! (Maksimal: {$cart_item['stok']})'); window.location='chart.php';</script>";
        exit;
    }
    
    if($qty < 1){
        $qty = 1;
    }
    
    mysqli_query($koneksi, "UPDATE keranjang SET qty='$qty' WHERE id_keranjang='$id_keranjang' AND id_user='$id_user'");
    header("Location: chart.php");
}

// Hapus item
if(isset($_GET['hapus'])){
    $id_keranjang = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM keranjang WHERE id_keranjang='$id_keranjang' AND id_user='$id_user'");
    header("Location: chart.php");
}

// Kosongkan keranjang
if(isset($_GET['kosongkan'])){
    mysqli_query($koneksi, "DELETE FROM keranjang WHERE id_user='$id_user'");
    header("Location: chart.php");
}

// Ambil data keranjang
$query = "SELECT k.*, p.nama_barang, p.harga, p.gambar, p.stok 
          FROM keranjang k 
          JOIN produk p ON k.id_produk = p.id_produk 
          WHERE k.id_user = '$id_user'
          ORDER BY k.id_keranjang DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .cart-item-form { display: flex; align-items: center; gap: 10px; }
        .cart-item-form input { width: 60px; padding: 5px; }
        .cart-item-form button { padding: 5px 10px; font-size: 0.8rem; }
    </style>
</head>
<body>
    <header>
        <div class="container navbar">
            <div class="logo">FashionStore</div>
            <nav class="nav-links">
                <a href="index.php">Kembali Belanja</a>
                <a href="checkout.php">Checkout</a>
            </nav>
        </div>
    </header>
    <div class="container" style="padding: 40px 20px;">
        <h2>Keranjang Belanja</h2>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Qty (Max: Stok)</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_bayar = 0;
                while($row = mysqli_fetch_assoc($result)): 
                    $subtotal = $row['harga'] * $row['qty'];
                    $total_bayar += $subtotal;
                ?>
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <img src="../assets/produk/<?= $row['gambar'] ?>" alt="<?= $row['nama_barang'] ?>" style="width:50px; height:50px; object-fit:cover; border-radius:5px;" onerror="this.src='https://via.placeholder.com/50'">
                            <span><?= $row['nama_barang'] ?></span>
                        </div>
                    </td>
                    <td>Rp <?= number_format($row['harga']) ?></td>
                    <td>
                        <!-- ✅ SETIAP BARIS JADI FORM TERPISAH -->
                        <form method="POST" class="cart-item-form">
                            <input type="number" name="qty" value="<?= $row['qty'] ?>" min="1" max="<?= $row['stok'] ?>">
                            <input type="hidden" name="id_keranjang" value="<?= $row['id_keranjang'] ?>">
                            <button type="submit" name="update_qty" class="btn">Update</button>
                        </form>
                        <br><small style="color:#777;">Stok tersedia: <?= $row['stok'] ?></small>
                    </td>
                    <td>Rp <?= number_format($subtotal) ?></td>
                    <td>
                        <a href="?hapus=<?= $row['id_keranjang'] ?>" style="color:red;">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right; font-weight:bold">Total:</td>
                    <td colspan="2" style="font-weight:bold; color:#e74c3c">Rp <?= number_format($total_bayar) ?></td>
                </tr>
            </tfoot>
        </table>
        <div style="margin-top:20px; display:flex; gap:10px;">
            <a href="?kosongkan=1" class="btn" style="background:#e74c3c;">Kosongkan Keranjang</a>
            <a href="checkout.php" class="btn">Lanjut ke Pembayaran</a>
        </div>
        <?php else: ?>
        <div style="text-align:center; padding:50px;">
            <p style="font-size:1.2rem; color:#777;">Keranjang belanja Anda kosong</p>
            <a href="index.php" class="btn" style="margin-top:20px;">Mulai Belanja</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>