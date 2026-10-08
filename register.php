<?php
session_start();
include "db_config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    if ($name == "" || $username == "" || $password == "") {
        $message = "Please fill in all fields.";
    } else if (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
    } else if ($role != "child" && $role != "teacher") {
        $message = "Please choose a valid role.";
    } else {
        // check if the username is already taken
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "That username is already taken.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $username, $hashed, $role);

            if ($stmt->execute()) {
                // log the new user in straight away
                $_SESSION["user_id"] = $conn->insert_id;
                $_SESSION["name"] = $name;
                $_SESSION["role"] = $role;
                header("Location: dashboard.php");
                exit();
            } else {
                $message = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up - AdinkraDoku</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="box">
        <h1>Create an account</h1>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>

        <form method="POST" action="register.php">
            <label>Full name</label>
            <input type="text" name="name">

            <label>Username</label>
            <input type="text" name="username">

            <label>Password</label>
            <input type="password" name="password">

            <label>I am a</label>
            <select name="role">
                <option value="child">Student</option>
                <option value="teacher">Teacher</option>
            </select>

            <button type="submit">Sign up</button>
        </form>

        <p>Already have an account? <a href="login.php">Log in</a></p>
    </div>
</body>
</html>