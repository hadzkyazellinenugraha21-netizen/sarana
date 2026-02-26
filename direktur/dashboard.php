<?php
session_start();
include "../config/database.php";

/* =========================
   AKSES DIREKTUR
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'direktur') {
    header("Location: ../index.php");
    exit;
}

/* =========================
   DATA RINGKASAN
========================= */
$total_keluhan   = $conn->query("SELECT COUNT(*) total FROM keluhan")->fetch_assoc()['total'];
$total_selesai   = $conn->query("SELECT COUNT(*) total FROM keluhan WHERE status='selesai'")->fetch_assoc()['total'];
$total_pending   = $conn->query("SELECT COUNT(*) total FROM keluhan WHERE status!='selesai'")->fetch_assoc()['total'];

/* =========================
   GRAFIK STATUS
========================= */
$statusData = [
    'Pending' => $total_pending,
    'Selesai' => $total_selesai
];

/* =========================
   GRAFIK & REKAP PER KAMAR
========================= */
$q = $conn->query("
    SELECT 
        no_kamar,
        COUNT(*) total,
        SUM(CASE WHEN status='selesai' THEN 1 ELSE 0 END) selesai
    FROM keluhan
    GROUP BY no_kamar
    ORDER BY no_kamar ASC
");

$label_kamar = [];
$data_kamar  = [];
$rekap       = [];

while ($r = $q->fetch_assoc()) {
    $label_kamar[] = "Kamar " . $r['no_kamar'];
    $data_kamar[]  = $r['total'];
    $rekap[]       = $r;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Direktur</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body { margin:0; font-family: Arial,sans-serif; background:#eef5ff; }

.header {
    background:#003f73;
    color:white;
    padding:18px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.header a {
    color:white;
    text-decoration:none;
    background:#005fa3;
    padding:8px 14px;
    border-radius:6px;
}

.wrapper { display:flex; min-height: calc(100vh - 70px); }

.sidebar {
    width:240px;
    background:#004a86;
    color:white;
    padding:20px;
}
.sidebar h3 { margin-top:0; }
.sidebar a {
    display:block;
    color:white;
    padding:12px;
    text-decoration:none;
    border-radius:6px;
    margin-bottom:6px;
}
.sidebar a:hover { background:#006bb3; }

.content {
    flex:1;
    padding:25px;
}

.card {
    background:white;
    border-radius:10px;
    padding:18px;
    margin-bottom:20px;
    box-shadow:0 2px 6px rgba(0,0,0,0.08);
}

.stat {
    display:flex;
    gap:20px;
}
.stat div {
    flex:1;
    background:#f4f8ff;
    border-radius:8px;
    padding:15px;
    text-align:center;
}
.stat h2 { margin:5px 0; color:#003f73; }

table {
    width:100%;
    border-collapse:collapse;
    font-size:13px;
}
th, td {
    border:1px solid #ccc;
    padding:6px;
    text-align:center;
}
th {
    background:#003f73;
    color:white;
}
</style>
</head>

<body>

<div class="header">
    <h1>Dashboard Direktur</h1>
    <a href="../logout.php">Logout</a>
</div>

<div class="wrapper">

<div class="sidebar">
    <h3>Menu Direktur</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="laporan.php">Laporan Keluhan</a>
    <a href="rekap_kamar.php">Rekap Per Kamar</a>
</div>

<div class="content">

<!-- STATISTIK -->
<div class="card stat">
    <div>
        <small>Total Keluhan</small>
        <h2><?= $total_keluhan ?></h2>
    </div>
    <div>
        <small>Selesai</small>
        <h2><?= $total_selesai ?></h2>
    </div>
    <div>
        <small>Pending</small>
        <h2><?= $total_pending ?></h2>
    </div>
</div>

<!-- GRAFIK STATUS -->
<div class="card">
    <h3>Grafik Status Keluhan</h3>
    <canvas id="chartStatus" height="120"></canvas>
</div>

<!-- GRAFIK PER KAMAR -->
<div class="card">
    <h3>Grafik Keluhan Per Kamar</h3>
    <canvas id="chartKamar" height="120"></canvas>
</div>

<!-- REKAP RINGKAS -->
<div class="card">
    <h3>Rekap Singkat Per Kamar</h3>
    <table>
        <tr>
            <th>No</th>
            <th>No Kamar</th>
            <th>Total</th>
            <th>Selesai</th>
        </tr>
        <?php $no=1; foreach($rekap as $r): ?>
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

<script>
// STATUS
new Chart(document.getElementById('chartStatus'), {
    type: 'bar',
    data: {
        labels: ['Pending','Selesai'],
        datasets: [{
            data: [<?= $total_pending ?>, <?= $total_selesai ?>],
            backgroundColor: ['orange','green']
        }]
    },
    options: {
        plugins:{ legend:{ display:false }},
        scales:{ y:{ beginAtZero:true }}
    }
});

// PER KAMAR
new Chart(document.getElementById('chartKamar'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($label_kamar) ?>,
        datasets: [{
            label:'Jumlah Keluhan',
            data: <?= json_encode($data_kamar) ?>,
            backgroundColor:'#003f73'
        }]
    },
    options: {
        scales:{ y:{ beginAtZero:true }}
    }
});
</script>

</body>
</html>