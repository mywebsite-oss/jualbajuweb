<?php
session_start();
include '../includes/koneksi.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php"); exit;
}

$id_user = $_SESSION['user_id'];

// Ambil data keranjang
$query = "SELECT k.*, p.nama_barang, p.harga, p.stok 
          FROM keranjang k 
          JOIN produk p ON k.id_produk = p.id_produk 
          WHERE k.id_user = '$id_user'";
$result = mysqli_query($koneksi, $query);

if(mysqli_num_rows($result) == 0){
    header("Location: chart.php"); exit;
}

// VALIDASI STOK SEBELUM PROSES
$stok_error = false;
$error_message = "";

mysqli_data_seek($result, 0);
while($item = mysqli_fetch_assoc($result)){ 
    if($item['stok'] < $item['qty']){
        $stok_error = true;
        $error_message .= "Stok '".$item['nama_barang']."' tidak mencukupi (Tersedia: ".$item['stok'].")\n";
    }
    if($item['stok'] <= 0){
        $stok_error = true;
        $error_message .= "Produk '".$item['nama_barang']."' sudah habis!\n";
    }
}

if($stok_error){
    echo "<script>alert('".$error_message."'); window.location='chart.php';</script>";
    exit;
}

if(isset($_POST['checkout'])){
    $method_bayar = $_POST['method_bayar'];
    
    // Reset pointer query
    mysqli_data_seek($result, 0);
    
    // Proses setiap item di keranjang
    while($item = mysqli_fetch_assoc($result)){
        $id_produk = $item['id_produk'];
        $qty = $item['qty'];
        $subtotal = $item['harga'] * $qty;
        
        // DOUBLE CHECK stok lagi sebelum insert
        $check_stok = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT stok FROM produk WHERE id_produk='$id_produk'"));
        if($check_stok['stok'] < $qty){
            echo "<script>alert('Stok berubah! Silakan coba lagi.'); window.location='chart.php';</script>";
            exit;
        }
        
        // 1. Buat Pesanan
        $query_pesanan = "INSERT INTO pesanan (id_user, id_produk, qty, total, method_bayar, status_bayar, status) 
                          VALUES ('$id_user', '$id_produk', '$qty', '$subtotal', '$method_bayar', 'Belum Bayar', 'Pending')";
        mysqli_query($koneksi, $query_pesanan);
        $id_pesanan_baru = mysqli_insert_id($koneksi);

        // 2. Catat Pembayaran
        $query_bayar = "INSERT INTO pembayaran (id_pesanan, jumlah_bayar, status_bayar) VALUES ('$id_pesanan_baru', '$subtotal', 'Belum Bayar')";
        mysqli_query($koneksi, $query_bayar);
        
        // 3. Kurangi Stok (Pakai query langsung untuk hindari minus)
        mysqli_query($koneksi, "UPDATE produk SET stok = stok - $qty WHERE id_produk='$id_produk' AND stok >= $qty");
    }

    // 4. Kosongkan Keranjang
    mysqli_query($koneksi, "DELETE FROM keranjang WHERE id_user='$id_user'");
    
    echo "<script>alert('Pesanan berhasil dibuat! Silakan lakukan pembayaran.'); window.location='dashboard.php';</script>";
}

// Hitung total
$total_bayar = 0;
mysqli_data_seek($result, 0);
while($item = mysqli_fetch_assoc($result)){ 
    $total_bayar += ($item['harga'] * $item['qty']); 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container" style="padding: 40px 20px;">
        <div class="form-box" style="max-width: 600px;">
            <h2>Checkout</h2>
            
            <h3 style="margin:20px 0;">Ringkasan Pesanan</h3>
            <table style="margin-bottom:20px;">
                <?php 
                mysqli_data_seek($result, 0);
                while($item = mysqli_fetch_assoc($result)): 
                ?>
                <tr>
                    <td><?= $item['nama_barang'] ?> x<?= $item['qty'] ?></td>
                    <td style="text-align:right;">Rp <?= number_format($item['harga'] * $item['qty']) ?></td>
                </tr>
                <?php endwhile; ?>
                <tr style="font-weight:bold; font-size:1.1rem;">
                    <td>Total</td>
                    <td style="text-align:right; color:#e74c3c;">Rp <?= number_format($total_bayar) ?></td>
                </tr>
            </table>
            
            <form method="POST">
                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <select name="method_bayar" class="form-control" required>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="E-Wallet">E-Wallet (GoPay/OVO/DANA)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Total Pembayaran</label>
                    <input type="text" class="form-control" value="Rp <?= number_format($total_bayar) ?>" readonly>
                    <input type="hidden" name="total_bayar" value="<?= $total_bayar ?>">
                </div>
                <button type="submit" name="checkout" class="btn" style="width:100%">Buat Pesanan</button>
            </form>
        </div>
    </div>
</body>
</html>