<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "rinterio_db";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_id = $_SESSION['user_id'];
    $property = $_POST['property'];
    $location = $_POST['location'];
    $rent = $_POST['rent'];
    $bedrooms = $_POST['bedrooms'];

    $stmt = $conn->prepare("INSERT INTO saved_rents (user_id, property, location, rent, bedrooms) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issii", $user_id, $property, $location, $rent, $bedrooms);

    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=saved");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>
