<?php
session_start();
include "../config/database.php";
if($_SESSION['role'] != 'admin'){ header("Location: ../index.php"); exit; }

// Tambah item
if(isset($_POST['add'])){
    $nama_item = $_POST['nama_item'];
    $stmt = $conn->prepare("INSERT INTO items (nama_item) VALUES (?)");
    $stmt->bind_param("s",$nama_item);
    $stmt->execute();
    header("Location: items.php"); // refresh
}

// Update item
if(isset($_POST['update'])){
    $id = $_POST['id'];
    $nama_item = $_POST['nama_item'];
    $stmt = $conn->prepare("UPDATE items SET nama_item=? WHERE id=?");
    $stmt->bind_param("si",$nama_item,$id);
    $stmt->execute();
    header("Location: items.php");
}

$items_result = $conn->query("SELECT * FROM items ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Item</title>
<style>
body { font-family: Arial,sans-serif; background:#cce7ff; margin:0; }
.header, .footer { background:#0077cc; color:white; padding:15px; }
.wrapper { display:flex; min-height: calc(100vh - 60px); }
.sidebar { width:220px; background:#0077cc; color:white; padding:20px; }
.sidebar a { display:block; color:white; padding:10px; margin-bottom:5px; text-decoration:none; border-radius:5px; }
.sidebar a:hover { background:#005fa3; }
.content { flex:1; padding:20px; background:#e6f2ff; }
form input, form button { padding:5px; margin:2px 0; }
form button { background:#0077cc; color:white; border:none; border-radius:4px; cursor:pointer; }
form button:hover { background:#005fa3; }
table { border-collapse: collapse; width:100%; background:white; border-radius:5px; margin-top:20px; }
table th, table td { padding:10px; border-bottom:1px solid #cce7ff; }
table th { background:#0077cc; color:white; }
.edit-form { background:#e0f0ff; padding:10px; margin-top:5px; border-radius:5px; }
</style>
</head>
<body>

<div class="header">
    <h1>Daftar Item Kerusakan</h1>
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
        <h2>Tambah Item Kerusakan</h2>
        <form method="post">
            <input type="text" name="nama_item" placeholder="Nama Item" required>
            <button type="submit" name="add">Tambah</button>
        </form>

        <h2>Daftar Item</h2>
        <table>
            <tr>
                <th>No</th><th>Nama Item</th><th>Aksi</th>
            </tr>
            <?php $no=1; while($row=$items_result->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['nama_item'] ?></td>
                <td>
                    <form method="post" class="edit-form">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="text" name="nama_item" value="<?= $row['nama_item'] ?>" required>
                        <button type="submit" name="update">Update</button>
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
