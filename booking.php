<?php
$host = "localhost";
$user = "root";   // XAMPP default user
$pass = "";       // XAMPP default has no password
$db   = "rinterio_db";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$name     = $_POST['name'] ?? '';
$email    = $_POST['email'] ?? '';
$phone    = $_POST['phone'] ?? '';
$property = $_POST['property'] ?? '';

// Insert into database
if (!empty($name) && !empty($email) && !empty($phone) && !empty($property)) {
    $stmt = $conn->prepare("INSERT INTO bookings (name, email, phone, property) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $property);

    if ($stmt->execute()) {
        echo "<h2>✅ Booking Successful!</h2>";
        echo "<a href='index.php'>Back to Home</a>";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "⚠️ Please fill all fields.";
}

$conn->close();
?>
