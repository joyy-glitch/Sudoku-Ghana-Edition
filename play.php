<?php
session_start();
include "db_config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// work out the child's current level
$stmt = $conn->prepare("SELECT MAX(level) FROM games WHERE user_id = ? AND completed = 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($max_level);
$stmt->fetch();
$stmt->close();

if ($max_level == null) {
    $max_level = 0;
}
$current_level = $max_level + 1;

// pick the level to play
$level = 1;
if (isset($_GET["level"])) {
    $level = (int)$_GET["level"];
}
if ($level < 1 || $level > 2 || $level > $current_level) {
    $level = min($current_level, 2);
}

if ($level == 1) {
    $size = 4;
} else {
    $size = 6;
}

// get the symbols for this level
$stmt = $conn->prepare("SELECT name, image FROM symbols WHERE level <= ? ORDER BY id LIMIT ?");
$stmt->bind_param("ii", $level, $size);
$stmt->execute();
$stmt->bind_result($name, $image);

$symbols = [];
while ($stmt->fetch()) {
    $symbols[] = ["name" => $name, "image" => $image];
}
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Play - AdinkraDoku</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="box wide">
        <h1>Level <?php echo $level; ?></h1>
        <p>Pick a symbol, then click an empty square. Each row, column and box must have every symbol once.</p>
        <label class="toggle">
            <input type="checkbox" id="showNumbers"> Show numbers
       </label>
        <div id="board"></div>
        <div id="palette"></div>
        <p id="message"></p>

        <a href="learn.php">My Symbols</a> | <a href="dashboard.php">Dashboard</a>
    </div>

    <script>
        let size = <?php echo $size; ?>;
        let level = <?php echo $level; ?>;
        let symbols = <?php echo json_encode($symbols); ?>;
    </script>
    <script src="js/sudoku.js"></script>
</body>
</html>