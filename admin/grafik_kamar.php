<?php
session_start();
include "../config/database.php";

/* =========================
   AKSES ADMIN
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

/* =========================
   FILTER PERIODE
========================= */
$where = [];

if (!empty($_GET['periode'])) {
    if ($_GET['periode'] == 'mingguan') {
        $where[] = "tanggal_keluhan >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    } elseif ($_GET['periode'] == 'bulanan') {
        $where[] = "MONTH(tanggal_keluhan)=MONTH(CURDATE())
                    AND YEAR(tanggal_keluhan)=YEAR(CURDATE())";
    }
}

$whereSQL = count($where) ? "WHERE " . implode(" AND ", $where) : "";

/* =========================
   QUERY DATA GRAFIK
========================= */
$q = $conn->query("
    SELECT 
        no_kamar,
        COUNT(*) AS total
    FROM keluhan
    $whereSQL
    GROUP BY no_kamar
    ORDER BY no_kamar ASC
");

$kamar = [];
$total = [];

while ($r = $q->fetch_assoc()) {
    $kamar[] = "Kamar " . $r['no_kamar'];
    $total[] = $r['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Grafik Keluhan Per Kamar</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body { font-family: Arial, sans-serif; background:#eef5ff; margin:0; }
.header { background:#0077cc; color:white; padding:15px; }
.wrapper { display:flex; min-height:calc(100vh - 60px); }

.sidebar {
    width:220px;
    background:#0077cc;
    color:white;
    padding:20px;
}
.sidebar a {
    display:block;
    color:white;
    padding:10px;
    text-decoration:none;
    border-radius:5px;
    margin-bottom:5px;
}
.sidebar a:hover { background:#005fa3; }

.content {
    flex:1;
    padding:20px;
    background:white;
}

select, button {
    padding:6px;
    margin-bottom:15px;
}

canvas {
    max-width:100%;
    background:#f9fbff;
    padding:15px;
    border-radius:8px;
}
</style>
</head>

<body>

<div class="header">
    <h2>Grafik Keluhan Per Kamar</h2>
</div>

<div class="wrapper">

<div class="sidebar">
    <h3>Menu Admin</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="keluhan.php">Keluhan</a>
    <a href="laporan.php">Laporan</a>
    <a href="rekap_kamar.php">Rekap Kamar</a>
    <a href="grafik_kamar.php">Grafik Kamar</a>
</div>

<div class="content">

<form method="get">
    <select name="periode">
        <option value="">Semua Periode</option>
        <option value="mingguan" <?= (@$_GET['periode']=='mingguan')?'selected':'' ?>>Mingguan</option>
        <option value="bulanan" <?= (@$_GET['periode']=='bulanan')?'selected':'' ?>>Bulanan</option>
    </select>
    <button type="submit">Tampilkan</button>
</form>

<canvas id="chartKamar"></canvas>

</div>
</div>

<script>
const ctx = document.getElementById('chartKamar').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($kamar) ?>,
        datasets: [{
            label: 'Jumlah Keluhan',
            data: <?= json_encode($total) ?>,
            backgroundColor: '#0077cc'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});
</script>

</body>
</html>