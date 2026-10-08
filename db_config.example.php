<?php
$conn = new mysqli("localhost", "root", "your_password_here", "sudoku-gh");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>