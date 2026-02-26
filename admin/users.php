<?php
session_start();
include "../config/database.php";
if($_SESSION['role'] != 'admin'){ header("Location: ../index.php"); exit; }

// Tambah pengguna
if(isset($_POST['add'])){
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = md5($_POST['password']);
    $stmt = $conn->prepare("INSERT INTO users (nama,email,role,password) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss",$nama,$email,$role,$password);
    $stmt->execute();
    header("Location: users.php");
}

// Update password pengguna
if(isset($_POST['update_password'])){
    $id = $_POST['id'];
    $password = md5($_POST['password']);
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si",$password,$id);
    $stmt->execute();
    header("Location: users.php");
}

// Ambil daftar pengguna
$users_result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Pengguna</title>
<style>
body { font-family: Arial,sans-serif; background:#cce7ff; margin:0; }
.header, .footer { background:#0077cc; color:white; padding:15px; }
.wrapper { display:flex; min-height: calc(100vh - 60px); }
.sidebar { width:220px; background:#0077cc; color:white; padding:20px; }
.sidebar a { display:block; color:white; padding:10px; margin-bottom:5px; text-decoration:none; border-radius:5px; }
.sidebar a:hover { background:#005fa3; }
.content { flex:1; padding:20px; background:#e6f2ff; }

table { border-collapse: collapse; width:100%; background:white; border-radius:5px; margin-top:20px; }
table th, table td { padding:10px; border-bottom:1px solid #cce7ff; vertical-align:top; }
table th { background:#0077cc; color:white; }

form input, form select, form button {
    padding:6px;
    margin:2px 0;
    font-size:13px;
}
form button {
    background:#0077cc;
    color:white;
    border:none;
    border-radius:4px;
    cursor:pointer;
}
form button:hover { background:#005fa3; }

.password-form {
    background:#eef6ff;
    padding:6px;
    border-radius:5px;
}
</style>
</head>
<body>

<div class="header">
    <h1>Daftar Pengguna</h1>
</div>

<div class="wrapper">
    <div class="sidebar">
        <h3>Menu</h3>
        <a href="dashboard.php">Dashboard</a>
        <a href="keluhan.php">Daftar Keluhan</a>
        <a href="laporan.php">Laporan</a>
        <a href="users.php">Daftar Pengguna</a>
        <a href="items.php">Daftar Item</a>
        <a href="chart.php">Grafik Keluhan</a>
    </div>

    <div class="content">
        <h2>Tambah Pengguna</h2>
        <form method="post">
            <input type="text" name="nama" placeholder="Nama" required>
            <input type="email" name="email" placeholder="Email" required>
            <select name="role" required>
                <option value="">-- Pilih Role --</option>
                <option value="wali">Wali Santri</option>
                <option value="direktur">Direktur</option>
                <option value="admin">Admin</option>
            </select>
            <input type="text" name="password" placeholder="Password Default" required>
            <button type="submit" name="add">Tambah</button>
        </form>

        <h2>Daftar Pengguna</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Ganti Password</th>
            </tr>
            <?php $no=1; while($row=$users_result->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= ucfirst($row['role']) ?></td>
                <td>
                    <form method="post" class="password-form">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="password" name="password" placeholder="Password Baru" required>
                        <button type="submit" name="update_password">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<div class="footer">&copy; <?= date('Y') ?> Aplikasi Admin</div>
</body>
</html>
