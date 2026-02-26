<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

/* =========================
   GRAFIK STATUS KELUHAN
========================= */
$keluhan_status = $conn->query("
    SELECT status, COUNT(*) as jumlah 
    FROM keluhan 
    GROUP BY status
");

$status_data = [];
while ($row = $keluhan_status->fetch_assoc()) {
    $status_data[$row['status']] = $row['jumlah'];
}

$pending = $status_data['pending'] ?? 0;
$selesai = $status_data['selesai'] ?? 0;

/* =========================
   GRAFIK & REKAP PER KAMAR
========================= */
$rekap_kamar = $conn->query("
    SELECT 
        no_kamar,
        COUNT(*) AS total,
        SUM(CASE WHEN status='selesai' THEN 1 ELSE 0 END) AS selesai
    FROM keluhan
    GROUP BY no_kamar
    ORDER BY no_kamar ASC
");

$label_kamar = [];
$data_kamar  = [];

$rekap_data = [];
while ($r = $rekap_kamar->fetch_assoc()) {
    $label_kamar[] = "Kamar " . $r['no_kamar'];
    $data_kamar[]  = $r['total'];
    $rekap_data[]  = $r;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body { margin:0; font-family: Arial,sans-serif; background:#cce7ff; }
.header {
    background:#0077cc;
    color:white;
    padding:15px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.header a {
    color:white;
    text-decoration:none;
    background:#005fa3;
    padding:8px 12px;
    border-radius:5px;
}
.header a:hover { background:#003f73; }

.wrapper { display:flex; min-height: calc(100vh - 60px); }

.sidebar {
    width:220px;
    background:#0077cc;
    color:white;
    padding:20px;
}
.sidebar a {
    display:block;
    color:white;
    padding:10px 15px;
    text-decoration:none;
    margin-bottom:5px;
    border-radius:5px;
}
.sidebar a:hover { background:#005fa3; }

.content {
    flex:1;
    padding:20px;
    background:#e6f2ff;
}

.card {
    background:white;
    padding:15px;
    border-radius:8px;
    margin-bottom:20px;
}

table {
    border-collapse: collapse;
    width:100%;
    font-size:12px;
}
table th, table td {
    border:1px solid #ccc;
    padding:6px;
    text-align:center;
}
table th {
    background:#0077cc;
    color:white;
}

.footer {
    text-align:center;
    padding:10px;
    background:#0077cc;
    color:white;
}
</style>
</head>

<body>

<div class="header">
    <h1>Dashboard Admin</h1>
    <a href="../logout.php">Logout</a>
</div>

<div class="wrapper">

<div class="sidebar">
    <h3>Menu</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="keluhan.php">Daftar Keluhan</a>
    <a href="laporan.php">Laporan</a>
    <a href="rekap_kamar.php">Rekap Kamar</a>
    <a href="grafik_kamar.php">Grafik Kamar</a>
</div>

<div class="content">

<h2>Ringkasan Keluhan</h2>

<!-- RINGKASAN STATUS -->
<div class="card">
    <ul>
        <li><strong>Pending:</strong> <?= $pending ?></li>
        <li><strong>Selesai:</strong> <?= $selesai ?></li>
    </ul>

    <canvas id="chartStatus" height="120"></canvas>
</div>

<!-- GRAFIK KELUHAN PER KAMAR -->
<div class="card">
    <h3>Grafik Keluhan Per Kamar</h3>
    <canvas id="chartKamar" height="120"></canvas>
</div>

<!-- REKAP SINGKAT PER KAMAR -->
<div class="card">
    <h3>Rekap Singkat Keluhan Per Kamar</h3>
    <table>
        <tr>
            <th>No</th>
            <th>No Kamar</th>
            <th>Total Keluhan</th>
            <th>Selesai</th>
        </tr>
        <?php $no=1; foreach ($rekap_data as $r): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $r['no_kamar'] ?></td>
            <td><?= $r['total'] ?></td>
            <td><?= $r['selesai'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

</div>
</div>

<div class="footer">
    &copy; <?= date('Y') ?> Aplikasi Keluhan Admin
</div>

<script>
// GRAFIK STATUS
new Chart(document.getElementById('chartStatus'), {
    type: 'bar',
    data: {
        labels: ['Pending', 'Selesai'],
        datasets: [{
            data: [<?= $pending ?>, <?= $selesai ?>],
            backgroundColor: ['orange','green']
        }]
    },
    options: {
        plugins: { legend: { display:false } },
        scales: { y: { beginAtZero:true } }
    }
});

// GRAFIK PER KAMAR
new Chart(document.getElementById('chartKamar'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($label_kamar) ?>,
        datasets: [{
            label: 'Jumlah Keluhan',
            data: <?= json_encode($data_kamar) ?>,
            backgroundColor: '#0077cc'
        }]
    },
    options: {
        scales: { y: { beginAtZero:true } }
    }
});
</script>

</body>
</html>