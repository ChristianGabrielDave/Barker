<?php 
$host = 'localhost';
$db   = 'barker';
$user = 'root';
$pass = '';

$conn = new mySqli(hostname: $host, username: $user, password: $pass, database: $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}