<?php
session_start();
include "db_config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// find the highest level this child has completed
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

$result = $conn->query("SELECT name, meaning, image, level FROM symbols ORDER BY level, id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Symbols - AdinkraDoku</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="box wide">
        <h1>My Symbols</h1>
        <p>You are on level <?php echo $current_level; ?>.</p>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <?php if ($row["level"] <= $current_level) { ?>
                <div class="symbol-card">
                    <img src="images/symbols/<?php echo htmlspecialchars($row["image"]); ?>" alt="<?php echo htmlspecialchars($row["name"]); ?>">
                    <div>
                        <h3><?php echo htmlspecialchars($row["name"]); ?></h3>
                        <p><?php echo htmlspecialchars($row["meaning"]); ?></p>
                    </div>
                </div>
            <?php } else { ?>
                <div class="symbol-card locked">
                    <p>Locked - finish level <?php echo $row["level"] - 1; ?> to unlock</p>
                </div>
            <?php } ?>
        <?php } ?>

        <p>
            <a href="play.php?level=1">Play level 1 (4x4)</a>
            <?php if ($current_level >= 2) { ?>
                | <a href="play.php?level=2">Play level 2 (6x6)</a>
            <?php } ?>
        </p>
        <a href="dashboard.php">Back to dashboard</a>
    </div>
</body>
</html>