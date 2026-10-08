<?php
$conn = new mysqli("localhost", "root", "mysql", "sudoku-gh");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>