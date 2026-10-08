<?php
session_start();
include "db_config.php";

$message = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $name, $hashed, $role);

    if ($stmt->fetch() && password_verify($password, $hashed)) {
        $_SESSION["user_id"] = $id;
        $_SESSION["name"] = $name;
        $_SESSION["role"] = $role;
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Wrong username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Log In - AdinkraDoku</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="box">
        <h1>Log in</h1>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>

        <form method="POST" action="login.php">
            <label>Username</label>
            <input type="text" name="username">

            <label>Password</label>
            <input type="password" name="password">

            <button type="submit">Log in</button>
        </form>

        <p>New here? <a href="register.php">Sign up</a></p>
    </div>
</body>
</html>