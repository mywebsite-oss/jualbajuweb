<?php
session_start();
include '../includes/koneksi.php';

// Proteksi: Hanya admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../index.php"); exit;
}

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id_user='$id'"));

if(!$data){
    echo "<script>alert('User tidak ditemukan!'); window.location='dashboard.php';</script>";
    exit;
}

if(isset($_POST['update'])){
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_tlp = mysqli_real_escape_string($koneksi, $_POST['no_tlp']);
    $rekening = mysqli_real_escape_string($koneksi, $_POST['rekening']);
    $role = $_POST['role'];

    // Update password jika diisi
    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "UPDATE users SET nama='$nama', email='$email', password='$password', alamat='$alamat', no_tlp='$no_tlp', rekening='$rekening', role='$role' WHERE id_user='$id'";
    } else {
        $query = "UPDATE users SET nama='$nama', email='$email', alamat='$alamat', no_tlp='$no_tlp', rekening='$rekening', role='$role' WHERE id_user='$id'";
    }

    if(mysqli_query($koneksi, $query)){
        echo "<script>alert('User berhasil diupdate!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-layout">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="dashboard.php">← Kembali</a>
        </div>
        <div class="main-content">
            <h1>Edit User</h1>
            <div class="form-box" style="margin:0; max-width:100%;">
                <form method="POST">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?= $data['nama'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $data['email'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Password Baru (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control" placeholder="********">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="user" <?= $data['role'] == 'user' ? 'selected' : '' ?>>User Biasa</option>
                            <option value="admin" <?= $data['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"><?= $data['alamat'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_tlp" class="form-control" value="<?= $data['no_tlp'] ?>">
                    </div>
                    <div class="form-group">
                        <label>No. Rekening</label>
                        <input type="text" name="rekening" class="form-control" value="<?= $data['rekening'] ?>">
                    </div>
                    <button type="submit" name="update" class="btn">Update User</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>