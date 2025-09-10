<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rinterio_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$email = $_POST['email'];
$pass = $_POST['password'];
$role = $_POST['role'];

$sql = "INSERT INTO users (email, password, role) VALUES ('$email', '$pass', '$role')";
if ($conn->query($sql) === TRUE) { echo "Success"; } else { echo "Error"; }
$conn->close();
?>