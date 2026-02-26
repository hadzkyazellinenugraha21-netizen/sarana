<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

/* =========================
   UPDATE STATUS KELUHAN
========================= */
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $tanggal_selesai = null;
    $foto_selesai = null;

    if ($status == 'selesai') {
        $tanggal_selesai = date('Y-m-d');

        if (!empty($_FILES['foto_selesai']['name'])) {
            $ext = pathinfo($_FILES['foto_selesai']['name'], PATHINFO_EXTENSION);
            $foto_selesai = "assets/uploads/selesai_" . $id . "_" . time() . "." . $ext;
            move_uploaded_file($_FILES['foto_selesai']['tmp_name'], "../" . $foto_selesai);
        }
    }

    $stmt = $conn->prepare("
        UPDATE keluhan 
        SET status = ?, tanggal_selesai = ?, foto_selesai = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sssi", $status, $tanggal_selesai, $foto_selesai, $id);
    $stmt->execute();

    // PINDAH HALAMAN
    if ($status == 'selesai') {
        header("Location: laporan.php");
    } else {
        header("Location: keluhan.php");
    }
    exit;
}

/* =========================
   AMBIL DATA KELUHAN
   HANYA PENDING !!!
========================= */
$keluhan_result = $conn->query("
    SELECT 
        k.id,
        k.no_kamar,
        k.tanggal_keluhan,
        k.detail_keluhan,
        k.status,
        u.nama AS nama_wali,
        i.nama_item
    FROM keluhan k
    JOIN users u ON k.user_id = u.id
    JOIN items i ON k.item_id = i.id
    WHERE k.status = 'pending'
    ORDER BY k.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Keluhan</title>
<style>
body { font-family: Arial,sans-serif; background:#cce7ff; margin:0; }
.header { background:#0077cc; color:white; padding:15px; display:flex; justify-content:space-between; }
.header a { color:white; text-decoration:none; }
.wrapper { display:flex; min-height: calc(100vh - 60px); }
.sidebar { width:220px; background:#0077cc; color:white; padding:20px; }
.sidebar a { display:block; color:white; padding:10px; margin-bottom:5px; text-decoration:none; border-radius:5px; }
.sidebar a:hover { background:#005fa3; }
.content { flex:1; padding:20px; background:#e6f2ff; }
table { border-collapse: collapse; width:100%; background:white; border-radius:5px; margin-top:20px; }
table th, table td { padding:10px; border-bottom:1px solid #cce7ff; }
table th { background:#0077cc; color:white; }
form select, form input { width:100%; padding:5px; margin:3px 0; }
button { padding:6px 10px; background:#0077cc; color:white; border:none; border-radius:4px; cursor:pointer; }
button:hover { background:#005fa3; }
.footer { text-align:center; padding:10px; background:#0077cc; color:white; }
</style>
</head>

<body>

<div class="header">
    <h1>Daftar Keluhan (Pending)</h1>
    <a href="dashboard.php">Dashboard</a>
</div>

<div class="wrapper">
    <div class="sidebar">
        <h3>Menu</h3>
        <a href="keluhan.php">Daftar Keluhan</a>
        <a href="laporan.php">Laporan</a>
        <a href="users.php">Daftar Pengguna</a>
        <a href="items.php">Daftar Item</a>
        <a href="chart.php">Grafik Keluhan</a>
    </div>

    <div class="content">
        <table>
            <tr>
                <th>No</th>
                <th>Wali</th>
                <th>Tanggal</th>
                <th>Kamar</th>
                <th>Item</th>
                <th>Detail</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

            <?php if ($keluhan_result->num_rows > 0): ?>
                <?php $no=1; while($row = $keluhan_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama_wali']) ?></td>
                    <td><?= $row['tanggal_keluhan'] ?></td>
                    <td><?= htmlspecialchars($row['no_kamar']) ?></td>
                    <td><?= htmlspecialchars($row['nama_item']) ?></td>
                    <td><?= htmlspecialchars($row['detail_keluhan']) ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td>
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <select name="status" required>
                                <option value="pending">Pending</option>
                                <option value="selesai">Selesai</option>
                            </select>
                            <input type="file" name="foto_selesai">
                            <button type="submit" name="update">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center;">Tidak ada keluhan pending</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<div class="footer">
    &copy; <?= date('Y') ?> Aplikasi Admin
</div>

</body>
</html>