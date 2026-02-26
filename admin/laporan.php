<?php
session_start();
include "../config/database.php";

/* =========================
   CEK AKSES ADMIN
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

/* =========================
   DATA FILTER
========================= */
$walis  = $conn->query("SELECT id, nama FROM users WHERE role='wali'");
$kamars = $conn->query("SELECT DISTINCT no_kamar FROM keluhan ORDER BY no_kamar ASC");

/* =========================
   FILTER QUERY
========================= */
$where = [];
$where[] = "k.status='selesai'";
$judulPeriode = "SEMUA PERIODE";

/* Filter wali */
if (!empty($_GET['wali_id'])) {
    $where[] = "k.user_id=" . intval($_GET['wali_id']);
}

/* Filter kamar */
if (!empty($_GET['no_kamar'])) {
    $where[] = "k.no_kamar='" . $conn->real_escape_string($_GET['no_kamar']) . "'";
}

/* Filter periode */
if (!empty($_GET['periode'])) {
    if ($_GET['periode'] == 'mingguan') {
        $where[] = "k.tanggal_selesai >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $judulPeriode = "MINGGUAN";
    } elseif ($_GET['periode'] == 'bulanan') {
        $where[] = "MONTH(k.tanggal_selesai)=MONTH(CURDATE())
                    AND YEAR(k.tanggal_selesai)=YEAR(CURDATE())";
        $judulPeriode = "BULANAN";
    }
}

$whereSQL = "WHERE " . implode(" AND ", $where);

/* =========================
   QUERY DATA
========================= */
$data = $conn->query("
    SELECT 
        k.no_kamar,
        u.nama AS nama_wali,
        i.nama_item,
        k.detail_keluhan,
        k.foto_selesai,
        k.tanggal_keluhan,
        k.tanggal_selesai
    FROM keluhan k
    JOIN users u ON k.user_id=u.id
    JOIN items i ON k.item_id=i.id
    $whereSQL
    ORDER BY k.no_kamar ASC, k.tanggal_selesai DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Admin</title>

<style>
body { font-family: Arial, sans-serif; background:#e6f2ff; margin:0; }
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

.content { flex:1; padding:25px; background:#f2f8ff; }

table {
    border-collapse:collapse;
    width:100%;
    background:white;
    margin-top:15px;
}
th, td {
    border:1px solid #aaa;
    padding:4px 6px;
    font-size:11px;
    text-align:center;
}
th { background:#0077cc; color:white; }

img { max-width:70px; border-radius:4px; }

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
        margin-bottom:10px;
    }

    .print-header h2 { margin:0; font-size:16px; }
    .print-header h3 { margin:4px 0; font-size:14px; }

    table { font-size:11px; }

    .ttd {
        display:flex;
        justify-content:space-between;
        margin-top:80px;
        padding:0 70px;
        font-size:12px;
    }

    .ttd div {
        width:230px;
        text-align:center;
    }

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
    <h2>Laporan Keluhan Admin</h2>
</div>

<div class="wrapper">

<div class="sidebar">
    <h3>Menu Admin</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="keluhan.php">Keluhan</a>
    <a href="laporan.php">Laporan</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="content">

<div class="print-header">
    <h2>LAPORAN KELUHAN SARANA</h2>
    <h3><?= $judulPeriode ?></h3>
    <p>Tanggal Cetak : <?= date('d-m-Y') ?></p>
</div>

<!-- FILTER -->
<form method="get">
    <select name="wali_id">
        <option value="">Semua Wali</option>
        <?php while($w=$walis->fetch_assoc()): ?>
            <option value="<?= $w['id'] ?>" <?= (@$_GET['wali_id']==$w['id'])?'selected':'' ?>>
                <?= $w['nama'] ?>
            </option>
        <?php endwhile; ?>
    </select>

    <select name="no_kamar">
        <option value="">Semua Kamar</option>
        <?php while($k=$kamars->fetch_assoc()): ?>
            <option value="<?= $k['no_kamar'] ?>" <?= (@$_GET['no_kamar']==$k['no_kamar'])?'selected':'' ?>>
                Kamar <?= $k['no_kamar'] ?>
            </option>
        <?php endwhile; ?>
    </select>

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
    <th>Kamar</th>
    <th>Wali</th>
    <th>Item</th>
    <th>Detail</th>
    <th>Foto Selesai</th>
    <th>Tgl Keluhan</th>
    <th>Tgl Selesai</th>
</tr>

<?php $no=1; while($r=$data->fetch_assoc()): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $r['no_kamar'] ?></td>
    <td><?= $r['nama_wali'] ?></td>
    <td><?= $r['nama_item'] ?></td>
    <td><?= $r['detail_keluhan'] ?></td>
    <td>
        <?php if($r['foto_selesai']): ?>
            <img src="../<?= $r['foto_selesai'] ?>">
        <?php else: ?>-
        <?php endif; ?>
    </td>
    <td><?= $r['tanggal_keluhan'] ?></td>
    <td><?= $r['tanggal_selesai'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<div class="ttd">
    <div>
        Mengetahui,<br>
        <strong>Mnj. Sarana</strong>
        <div class="nama-ttd">Aditya Agung N</div>
    </div>
    <div>
        Disetujui,<br>
        <strong>Staf. Ketua YAB Bid.Sarana</strong>
        <div class="nama-ttd">Yoyok Mulyana</div>
    </div>
</div>

</div>
</div>
</body>
</html>