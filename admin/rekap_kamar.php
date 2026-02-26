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
$judulPeriode = "SEMUA PERIODE";

if (!empty($_GET['periode'])) {
    if ($_GET['periode'] == 'mingguan') {
        $where[] = "k.tanggal_keluhan >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $judulPeriode = "MINGGUAN";
    } elseif ($_GET['periode'] == 'bulanan') {
        $where[] = "MONTH(k.tanggal_keluhan)=MONTH(CURDATE())
                    AND YEAR(k.tanggal_keluhan)=YEAR(CURDATE())";
        $judulPeriode = "BULANAN";
    }
}

$whereSQL = count($where) ? "WHERE " . implode(" AND ", $where) : "";

/* =========================
   QUERY REKAP
========================= */
$data = $conn->query("
    SELECT 
        k.no_kamar,
        COUNT(*) AS total_keluhan,
        SUM(CASE WHEN k.status='selesai' THEN 1 ELSE 0 END) AS selesai,
        SUM(CASE WHEN k.status!='selesai' THEN 1 ELSE 0 END) AS belum
    FROM keluhan k
    $whereSQL
    GROUP BY k.no_kamar
    ORDER BY k.no_kamar ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rekap Keluhan Per Kamar</title>

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

.content { flex:1; padding:25px; background:#f8fbff; }

table {
    border-collapse:collapse;
    width:100%;
    background:white;
    margin-top:15px;
}
th, td {
    border:1px solid #999;
    padding:6px;
    font-size:12px;
    text-align:center;
}
th { background:#0077cc; color:white; }

.print-header, .ttd { display:none; }

@media print {
    @page {
        size: 210mm 330mm; /* F4 */
        margin: 15mm;
    }

    body { background:white; }
    .header, .sidebar, form, button { display:none; }
    .content { padding:0; }

    .print-header {
        display:block;
        text-align:center;
        margin-bottom:15px;
    }

    .print-header h2 { margin:0; font-size:16px; }
    .print-header h3 { margin:5px 0; font-size:14px; }

    .ttd {
        display:flex;
        justify-content:space-between;
        margin-top:80px;
        padding:0 80px;
        font-size:12px;
    }

    .ttd div { text-align:center; width:250px; }
    .nama-ttd {
        margin-top:60px;
        font-weight:bold;
        text-decoration:underline;
    }
}
</style>
</head>

<body>

<div class="header">
    <h2>Rekap Keluhan Per Kamar</h2>
</div>

<div class="wrapper">

<div class="sidebar">
    <h3>Menu Admin</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="keluhan.php">Keluhan</a>
    <a href="laporan.php">Laporan</a>
    <a href="rekap_kamar.php">Rekap Kamar</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="content">

<div class="print-header">
    <h2>REKAP KELUHAN SARANA PER KAMAR</h2>
    <h3><?= $judulPeriode ?></h3>
    <p>Tanggal Cetak: <?= date('d-m-Y') ?></p>
</div>

<form method="get">
    <select name="periode">
        <option value="">Semua Periode</option>
        <option value="mingguan" <?= (@$_GET['periode']=='mingguan')?'selected':'' ?>>Mingguan</option>
        <option value="bulanan" <?= (@$_GET['periode']=='bulanan')?'selected':'' ?>>Bulanan</option>
    </select>
    <button type="submit">Filter</button>
    <button type="button" onclick="window.print()">Print</button>
</form>

<table>
<tr>
    <th>No</th>
    <th>No Kamar</th>
    <th>Total Keluhan</th>
    <th>Selesai</th>
    <th>Belum Selesai</th>
</tr>

<?php 
$no=1;
$total_all=0; 
while($r=$data->fetch_assoc()):
$total_all += $r['total_keluhan'];
?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $r['no_kamar'] ?></td>
    <td><?= $r['total_keluhan'] ?></td>
    <td><?= $r['selesai'] ?></td>
    <td><?= $r['belum'] ?></td>
</tr>
<?php endwhile; ?>

<tr style="font-weight:bold;background:#eef;">
    <td colspan="2">TOTAL</td>
    <td><?= $total_all ?></td>
    <td colspan="2"></td>
</tr>
</table>

<div class="ttd">
    <div>
        Mengetahui,<br>
        <strong>Mnj. Sarana</strong>
        <div class="nama-ttd">Aditya Agung N</div>
    </div>
    <div>
        Disetujui,<br>
        <strong>Direktur PSAM</strong>
        <div class="nama-ttd">Asep Abdul Halim</div>
    </div>
</div>

</div>
</div>

</body>
</html>