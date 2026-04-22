<?php
declare(strict_types=1);

$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "auraedu";

$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
