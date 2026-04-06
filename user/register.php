<?php
session_start();
include '../includes/koneksi.php';

if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $alamat = $_POST['alamat'];
    $no_tlp = $_POST['no_tlp'];
    $role = 'user'; // Default role untuk registrasi

    // Gunakan Prepared Statements untuk keamanan
    $stmt = $koneksi->prepare("INSERT INTO users (nama, email, password, alamat, no_tlp, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nama, $email, $password, $alamat, $no_tlp, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='login.php';</script>";
    }
    else {
        echo "<script>alert('Registrasi Gagal: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Jual Baju</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2 style="text-align:center; margin-bottom:20px;">Daftar Akun Baru</h2>
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
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="no_tlp" class="form-control">
                </div>
                <button type="submit" name="register" class="btn" style="width:100%">Daftar</button>
            </form>
            <p style="text-align:center; margin-top:15px;">Sudah punya akun? <a href="login.php" style="color:#3498db">Login</a></p>
        </div>
    </div>
</body>
</html>