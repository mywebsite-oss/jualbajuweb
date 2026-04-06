<?php
include '../includes/koneksi.php';
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk='$id'"));

if(isset($_POST['update'])){
    $nama = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    // Logic update gambar jika ada file baru
    if(!empty($_FILES['gambar']['name'])){
        $gambar_name = $_FILES['gambar']['name'];
        $target = "../assets/produk/" . $gambar_name;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target);
        $query = "UPDATE produk SET nama_barang='$nama', harga='$harga', stok='$stok', gambar='$gambar_name' WHERE id_produk='$id'";
    } else {
        $query = "UPDATE produk SET nama_barang='$nama', harga='$harga', stok='$stok' WHERE id_produk='$id'";
    }
    
    mysqli_query($koneksi, $query);
    header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-layout">
        <div class="sidebar"><h2>Admin</h2><a href="dashboard.php">Kembali</a></div>
        <div class="main-content">
            <h1>Edit Produk</h1>
            <div class="form-box" style="margin:0; max-width:100%">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" value="<?= $data['nama_barang'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" class="form-control" value="<?= $data['harga'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" value="<?= $data['stok'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Gambar Baru (Opsional)</label>
                        <input type="file" name="gambar" class="form-control">
                        <small>Gambar saat ini: <?= $data['gambar'] ?></small>
                    </div>
                    <button type="submit" name="update" class="btn">Update Produk</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>