<?php
session_start();
include '../includes/koneksi.php';

// Proteksi: Hanya admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../index.php"); exit;
}

if(isset($_POST['submit'])){
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_tlp = mysqli_real_escape_string($koneksi, $_POST['no_tlp']);
    $rekening = mysqli_real_escape_string($koneksi, $_POST['rekening']);
    $role = $_POST['role'];

    // Cek email sudah ada atau belum
    $check = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($check) > 0){
        echo "<script>alert('Email sudah terdaftar!');</script>";
    } else {
        $query = "INSERT INTO users (nama, email, password, alamat, no_tlp, rekening, role) 
                  VALUES ('$nama', '$email', '$password', '$alamat', '$no_tlp', '$rekening', '$role')";
        if(mysqli_query($koneksi, $query)){
            echo "<script>alert('User berhasil ditambahkan!'); window.location='dashboard.php';</script>";
        } else {
            echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-layout">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="dashboard.php">Kembali</a>
        </div>
        <div class="main-content">
            <h1>Tambah User Baru</h1>
            <div class="form-box" style="margin:0; max-width:100%;">
                <form method="POST">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="user">User Biasa</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_tlp" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label>No. Rekening</label>
                        <input type="text" name="rekening" class="form-control" placeholder="Untuk pembayaran">
                    </div>
                    <button type="submit" name="submit" class="btn">Simpan User</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>