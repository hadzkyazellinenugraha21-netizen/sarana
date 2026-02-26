<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'wali') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ambil item kerusakan
$item_result = $conn->query("SELECT * FROM items ORDER BY nama_item ASC");

if (isset($_POST['submit'])) {

    $no_kamar = trim($_POST['no_kamar']);
    $item_id  = $_POST['item_id'];
    $tanggal  = $_POST['tanggal_keluhan'];
    $detail   = trim($_POST['detail_keluhan']);
    $foto     = null;

    /* ================= UPLOAD FOTO ================= */
    if (!empty($_FILES['foto_keluhan']['name']) && $_FILES['foto_keluhan']['error'] == 0) {

        $upload_dir = "../assets/uploads/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['foto_keluhan']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];

        if (in_array($ext, $allowed)) {
            $nama_file = time() . "_" . $user_id . "." . $ext;
            if (move_uploaded_file($_FILES['foto_keluhan']['tmp_name'], $upload_dir . $nama_file)) {
                $foto = "assets/uploads/" . $nama_file;
            } else {
                $error = "Upload foto gagal.";
            }
        } else {
            $error = "Format foto harus JPG / PNG.";
        }
    }

    /* ================= INSERT DATABASE ================= */
    if (!isset($error)) {

        $stmt = $conn->prepare("
            INSERT INTO keluhan
            (user_id, no_kamar, kamar_id, item_id, tanggal_keluhan, detail_keluhan, foto_keluhan, status)
            VALUES (?, ?, NULL, ?, ?, ?, ?, 'pending')
        ");

        $stmt->bind_param(
            "isisss",
            $user_id,
            $no_kamar,
            $item_id,
            $tanggal,
            $detail,
            $foto
        );

        if ($stmt->execute()) {
            $success = "Keluhan berhasil dikirim.";
        } else {
            // tampilkan error asli MySQL (FINAL DEBUG)
            echo "<pre>".$stmt->error."</pre>";
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Keluhan</title>

<link rel="stylesheet" href="../assets/css/style.css">

<style>
* { pointer-events:auto !important; }
body::before, body::after { content:none !important; }

body {
    margin:0;
    font-family: Arial, sans-serif;
    background:#cce7ff;
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

.container {
    max-width:600px;
    margin:30px auto;
    padding:20px;
}

form {
    background:white;
    padding:20px;
    border-radius:5px;
    box-shadow:0 0 5px #999;
}

label {
    display:block;
    margin-top:10px;
    font-weight:bold;
}

input, select, textarea {
    width:100%;
    padding:8px;
    margin-top:5px;
    border-radius:5px;
    border:1px solid #ccc;
}

textarea {
    height:80px;
    resize:vertical;
}

button {
    margin-top:15px;
    padding:10px 20px;
    background:#0077cc;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

button:hover { background:#005fa3; }

.success {
    background:#d4edda;
    color:#155724;
    padding:10px;
    border-radius:5px;
    margin-bottom:10px;
}

.error {
    background:#f8d7da;
    color:#721c24;
    padding:10px;
    border-radius:5px;
    margin-bottom:10px;
}

.footer {
    text-align:center;
    padding:10px;
    background:#0077cc;
    color:white;
    margin-top:20px;
}
</style>
</head>
<body>

<div class="header">
    <h2>Tambah Keluhan</h2>
    <a href="dashboard.php">Dashboard</a>
</div>

<div class="container">

<?php if (isset($success)) echo "<div class='success'>$success</div>"; ?>
<?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

<form method="post" enctype="multipart/form-data">

    <label>Nomor Kamar</label>
    <input type="text" name="no_kamar" placeholder="Contoh: 1111" required>

    <label>Tanggal Keluhan</label>
    <input type="date" name="tanggal_keluhan" required>

    <label>Jenis Kerusakan</label>
    <select name="item_id" required>
        <option value="">-- Pilih Kerusakan --</option>
        <?php while ($i = $item_result->fetch_assoc()): ?>
            <option value="<?= $i['id'] ?>"><?= $i['nama_item'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Detail Kerusakan</label>
    <textarea name="detail_keluhan" required></textarea>

    <label>Upload Foto (Opsional)</label>
    <input type="file" name="foto_keluhan" accept="image/*">

    <button type="submit" name="submit">Kirim Keluhan</button>

</form>
</div>

<div class="footer">
    &copy; <?= date('Y') ?> Aplikasi Keluhan Wali Santri
</div>

</body>
</html>
