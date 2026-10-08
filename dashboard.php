<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - AdinkraDoku</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="box">
        <h1>Akwaaba, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</h1>

        <?php if ($_SESSION["role"] == "child") { ?>
            <p>Your puzzles and symbols will appear here.</p>
        <?php } else { ?>
            <p>Your classes will appear here.</p>
        <?php } ?>

        <a href="logout.php">Log out</a>
    </div>
</body>
</html>