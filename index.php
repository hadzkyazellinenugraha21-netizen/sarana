<?php
session_start();
include "config/database.php";

if (isset($_POST['login'])) {

    $email    = trim($_POST['email']);
    $password = md5($_POST['password']); // menyesuaikan DB lama

    $query = $conn->prepare("SELECT id, role FROM users WHERE email=? AND password=? LIMIT 1");
    $query->bind_param("ss", $email, $password);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];

        /* =========================
           REDIRECT BERDASARKAN ROLE
        ========================= */
        if ($user['role'] == 'wali') {

            header("Location: wali/dashboard.php");
            exit;

        } elseif ($user['role'] == 'admin') {

            header("Location: admin/dashboard.php");
            exit;

        } elseif ($user['role'] == 'direktur') {

            header("Location: direktur/dashboard.php");
            exit;

        } else {

            // role tidak dikenal
            session_destroy();
            header("Location: index.php");
            exit;
        }

    } else {
        $error = "Email atau password salah";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Sistem Keluhan</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<div class="login-box">
    <h2>Login Sistem Keluhan</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>
</body>
</html>