<?php
require "config.php";

if (isset($_SESSION["user"])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if (isset($_POST["login"])) {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if ($username == "admin" && $password == "admin123") {
        $_SESSION["user"] = "Admin";
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Wrong username or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>HMS Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
<div class="login-box">
    <div class="login-logo">💚</div>
    <h1>HMS</h1>
    <p>Hospital Management System</p>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <?php if ($message != "") { ?>
            <p class="error"><?php echo $message; ?></p>
        <?php } ?>
        <button type="submit" name="login">Login</button>
    </form>

    <small>Demo login: admin / admin123</small>
</div>
</body>
</html>