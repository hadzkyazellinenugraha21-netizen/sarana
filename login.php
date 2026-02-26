<?php
session_start();
include "config/database.php";

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = md5($_POST['password']); // hash MD5 dari input

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND password=? LIMIT 1");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if($user){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama'];

        if($user['role'] == 'wali_santri'){
            header("Location: dashboard_wali.php");
            exit;
        } elseif($user['role'] == 'admin'){
            header("Location: dashboard_admin.php");
            exit;
        } elseif($user['role'] == 'direktur'){
            header("Location: dashboard_direktur.php");
            exit;
        }
    } else {
        $error = "Email atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Aplikasi Keluhan</title>
</head>
<body>
<h2>Login Aplikasi Keluhan Wali Santri</h2>

<?php if(isset($error)){ echo "<p style='color:red;'>$error</p>"; } ?>

<form method="POST">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="login">Login</button>
</form>
</body>
</html>
