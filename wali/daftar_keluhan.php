<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "../config/database.php";

/* =====================
   CEK LOGIN
===================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'wali') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    die("SESSION user_id tidak ditemukan.");
}

$user_id = (int) $_SESSION['user_id'];

/* =====================
   QUERY DATA KELUHAN
===================== */
$stmt = $conn->prepare("
    SELECT 
        k.id,
        k.no_kamar,
        k.tanggal_keluhan,
        k.detail_keluhan,
        k.foto_keluhan,
        k.status,
        k.tanggal_selesai,
        k.foto_selesai,
        i.nama_item
    FROM keluhan k
    LEFT JOIN items i ON k.item_id = i.id
    WHERE k.user_id = ?
    ORDER BY k.id DESC
");

if (!$stmt) {
    die("Prepare gagal: " . $conn->error);
}

$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    die("Execute gagal: " . $stmt->error);
}

$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Keluhan</title>

<style>
body {
    font-family: Arial, sans-serif;
    background:#cce7ff;
    margin:0;
}

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

.content {
    padding:20px;
}

table {
    width:100%;
    border-collapse: collapse;
    background:white;
}

table th, table td {
    padding:10px;
    border-bottom:1px solid #ddd;
    text-align:left;
}

table th {
    background:#0077cc;
    color:white;
}

img {
    border-radius:5px;
}

.status-pending {
    color:orange;
    font-weight:bold;
}

.status-proses {
    color:blue;
    font-weight:bold;
}

.status-selesai {
    color:green;
    font-weight:bold;
}

.empty {
    text-align:center;
    padding:20px;
    font-weight:bold;
    color:#555;
}
</style>
</head>
<body>

<div class="header">
    <h2>Daftar Keluhan Saya</h2>
    <a href="dashboard.php">Dashboard</a>
</div>

<div class="content">

<table>
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>No Kamar</th>
    <th>Jenis Kerusakan</th>
    <th>Detail</th>
    <th>Foto</th>
    <th>Status</th>
</tr>

<?php
if ($result->num_rows == 0) {
    echo "<tr><td colspan='7' class='empty'>Belum ada keluhan yang dikirim.</td></tr>";
}

$no = 1;
while ($row = $result->fetch_assoc()):
    
    $status_class = "status-pending";
    if ($row['status'] == "proses") {
        $status_class = "status-proses";
    } elseif ($row['status'] == "selesai") {
        $status_class = "status-selesai";
    }
?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['tanggal_keluhan']) ?></td>
    <td><?= htmlspecialchars($row['no_kamar']) ?></td>
    <td><?= htmlspecialchars($row['nama_item']) ?></td>
    <td><?= htmlspecialchars($row['detail_keluhan']) ?></td>

    <td>
        <?php if (!empty($row['foto_keluhan'])): ?>
            <img src="../<?= $row['foto_keluhan'] ?>" width="70">
        <?php else: ?>
            -
        <?php endif; ?>
    </td>

    <td class="<?= $status_class ?>">
        <?= ucfirst($row['status']) ?>
        <?php if ($row['status'] == "selesai" && !empty($row['foto_selesai'])): ?>
            <br><small>Selesai: <?= $row['tanggal_selesai'] ?></small>
            <br>
            <img src="../<?= $row['foto_selesai'] ?>" width="70">
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</table>

</div>

</body>
</html>