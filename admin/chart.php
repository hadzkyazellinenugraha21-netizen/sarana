<?php
session_start();
include "../config/database.php";
if($_SESSION['role'] != 'admin'){ header("Location: ../index.php"); exit; }

// Ambil data jumlah keluhan per status
$status_result = $conn->query("SELECT status, COUNT(*) as jumlah FROM keluhan GROUP BY status");
$data = [];
while($row = $status_result->fetch_assoc()){
    $data[$row['status']] = $row['jumlah'];
}
$pending = $data['pending'] ?? 0;
$selesai = $data['selesai'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Grafik Keluhan</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body { font-family: Arial,sans-serif; background:#cce7ff; margin:0; }
.header, .footer { background:#0077cc; color:white; padding:15px; }
.wrapper { display:flex; min-height: calc(100vh - 60px); }
.sidebar { width:220px; background:#0077cc; color:white; padding:20px; }
.sidebar a { display:block; color:white; padding:10px; margin-bottom:5px; text-decoration:none; border-radius:5px; }
.sidebar a:hover { background:#005fa3; }
.content { flex:1; padding:20px; background:#e6f2ff; }
</style>
</head>
<body>
<div class="header"><h1>Grafik Keluhan</h1></div>

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
        <canvas id="chartStatus" width="400" height="200"></canvas>
        <script>
        const ctx = document.getElementById('chartStatus').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Pending', 'Selesai'],
                datasets: [{
                    label: 'Jumlah Keluhan',
                    data: [<?= $pending ?>, <?= $selesai ?>],
                    backgroundColor: ['orange','green']
                }]
            },
            options: { responsive:true, plugins:{ legend:{ display:false } } }
        });
        </script>
    </div>
</div>

<div class="footer">&copy; <?= date('Y') ?> Aplikasi Admin</div>
</body>
</html>
