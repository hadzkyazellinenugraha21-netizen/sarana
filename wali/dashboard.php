<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'wali') {
    header("Location: ../index.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

/* ==========================
   AMBIL NAMA USER
========================== */
$user_query = $conn->query("SELECT nama FROM users WHERE id = $user_id");
$user_data  = $user_query->fetch_assoc();
$nama_user  = $user_data ? $user_data['nama'] : 'User';

/* ==========================
   STATISTIK
========================== */
$total = $conn->query("SELECT COUNT(*) as jml FROM keluhan WHERE user_id=$user_id")->fetch_assoc()['jml'];
$pending = $conn->query("SELECT COUNT(*) as jml FROM keluhan WHERE user_id=$user_id AND status='pending'")->fetch_assoc()['jml'];
$selesai = $conn->query("SELECT COUNT(*) as jml FROM keluhan WHERE user_id=$user_id AND status='selesai'")->fetch_assoc()['jml'];

/* ==========================
   DATA TABEL
========================== */
$keluhan_result = $conn->query("
    SELECT k.*, i.nama_item 
    FROM keluhan k
    LEFT JOIN items i ON k.item_id = i.id
    WHERE k.user_id = $user_id
    ORDER BY k.id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Wali</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:Arial;background:#cce7ff;}

/* HEADER */
.header{
    background:#0077cc;
    color:white;
    padding:15px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.header-right{
    display:flex;
    align-items:center;
    gap:15px;
}
.header-right span{
    font-weight:bold;
}
.header a{
    background:#005fa3;
    color:white;
    padding:8px 12px;
    border-radius:5px;
    text-decoration:none;
}
.header a:hover{background:#003f73;}

/* LAYOUT */
.wrapper{display:flex;}
.sidebar{
    width:220px;
    background:#0077cc;
    padding:20px;
    color:white;
}
.sidebar h3{margin-bottom:10px;}
.sidebar a{
    display:block;
    padding:10px;
    color:white;
    text-decoration:none;
    margin-bottom:5px;
    border-radius:5px;
}
.sidebar a:hover{background:#005fa3;}

.content{
    flex:1;
    padding:20px;
    background:#e6f2ff;
}

/* CARD */
.cards{
    display:flex;
    gap:20px;
    margin-bottom:20px;
}
.card{
    flex:1;
    background:white;
    padding:20px;
    border-radius:8px;
    box-shadow:0 2px 5px rgba(0,0,0,0.1);
    text-align:center;
}
.card h3{margin-bottom:10px;}
.card span{
    font-size:28px;
    font-weight:bold;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    background:white;
    margin-top:20px;
}
table th, table td{
    padding:10px;
    border-bottom:1px solid #ddd;
}
table th{
    background:#0077cc;
    color:white;
}

.status-pending{color:orange;font-weight:bold;}
.status-selesai{color:green;font-weight:bold;}

/* FOOTER */
.footer{
    text-align:center;
    padding:10px;
    background:#0077cc;
    color:white;
    margin-top:20px;
}
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <h1>Aplikasi Keluhan Pesantren Siswa Al Ma'soem</h1>
    <div class="header-right">
        <span>👤 <?= htmlspecialchars($nama_user) ?></span>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h3>Menu</h3>
        <a href="keluhan.php">Tambah Keluhan</a>
        <a href="daftar_keluhan.php">Daftar Keluhan</a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <h2>Dashboard Wali Santri</h2>

        <!-- STATISTIK -->
        <div class="cards">
            <div class="card">
                <h3>Total Keluhan</h3>
                <span><?= $total ?></span>
            </div>
            <div class="card">
                <h3>Pending</h3>
                <span style="color:orange"><?= $pending ?></span>
            </div>
            <div class="card">
                <h3>Selesai</h3>
                <span style="color:green"><?= $selesai ?></span>
            </div>
        </div>

        <!-- GRAFIK -->
        <div style="background:white;padding:20px;border-radius:8px;">
            <h3>Grafik Status Keluhan</h3>
            <canvas id="chartKeluhan"></canvas>
        </div>

        <!-- TABEL -->
        <h3 style="margin-top:20px;">Daftar Keluhan Anda</h3>
        <table>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kerusakan</th>
                <th>Detail</th>
                <th>Status</th>
            </tr>
            <?php $no=1; while($row=$keluhan_result->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['tanggal_keluhan'] ?></td>
                <td><?= htmlspecialchars($row['nama_item']) ?></td>
                <td><?= htmlspecialchars($row['detail_keluhan']) ?></td>
                <td class="status-<?= $row['status'] ?>">
                    <?= ucfirst($row['status']) ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

    </div>
</div>

<div class="footer">
    &copy; <?= date('Y') ?> Aplikasi Keluhan Pesantren Siswa Al Ma'soem
</div>

<script>
const ctx = document.getElementById('chartKeluhan');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Pending', 'Selesai'],
        datasets: [{
            label: 'Jumlah Keluhan',
            data: [<?= $pending ?>, <?= $selesai ?>],
            borderWidth: 1
        }]
    }
});
</script>

</body>
</html>