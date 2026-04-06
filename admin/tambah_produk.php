<?php
include '../includes/koneksi.php';

if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);

    // Upload Gambar
    $gambar_name = $_FILES['gambar']['name'];
    $target = "../assets/produk/" . $gambar_name;

    // Pastikan folder ada
    if (!file_exists("../assets/produk"))
        mkdir("../assets/produk", 0777, true);

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        $query = "INSERT INTO produk (nama_barang, harga, deskripsi, stok, gambar, id_kategori) 
                  VALUES ('$nama', '$harga', '$deskripsi', '$stok', '$gambar_name', '$kategori')";
        mysqli_query($koneksi, $query);
        header("Location: dashboard.php");
    } else {
        echo "<script>alert('Gagal upload gambar');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="admin-layout">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="dashboard.php">Kembali</a>
        </div>
        <div class="main-content">
            <h1>Tambah Produk Baru</h1>
            <div class="form-box" style="margin:0; max-width:100%">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_kategori" class="form-control">
                            <option value="1">Kaos</option>
                            <option value="2">Kemeja</option>
                            <option value="3">Celana</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Gambar Produk</label>
                        <input type="file" name="gambar" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" min="0" required>
                    </div>
                    <button type="submit" name="submit" class="btn">Simpan Produk</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>