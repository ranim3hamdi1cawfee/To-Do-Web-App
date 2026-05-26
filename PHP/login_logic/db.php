<?php
$host = "127.0.0.1";
$dbname = "todo-login";
$user = "root";
$password = "";
$port = 3307;

$conn = new mysqli($host, $user, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>