<?php
session_start();
include '../includes/koneksi.php';

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    $data = mysqli_fetch_assoc($query);

    if($data){
        if(password_verify($password, $data['password'])){
            // Simpan semua data user ke session
            $_SESSION['user'] = $data;
            $_SESSION['user_id'] = $data['id_user'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['nama'] = $data['nama'];
            
            // Redirect berdasarkan role
            if($data['role'] == 'admin'){
                echo "<script>alert('Login Admin Berhasil!'); window.location='../admin/dashboard.php';</script>";
            } else {
                echo "<script>alert('Login Berhasil!'); window.location='index.php';</script>";
            }
        } else {
            echo "<script>alert('Password salah!');</script>";
        }
    } else {
        echo "<script>alert('Email tidak ditemukan!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Jual Baju</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2 style="text-align:center; margin-bottom:20px;">Login Akun</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn" style="width:100%">Login</button>
            </form>
            <p style="text-align:center; margin-top:15px;">Belum punya akun? <a href="register.php" style="color:#3498db">Daftar</a></p>
        </div>
    </div>
</body>
</html>