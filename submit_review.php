<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $review = trim($_POST['review']);
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['name'];

    if (!empty($review)) {
        $conn = new mysqli("localhost", "root", "", "rinterio_db");
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

        $stmt = $conn->prepare("INSERT INTO reviews (user_id, user_name, review) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $user_name, $review);

        if ($stmt->execute()) {
            header("Location: index.php?msg=review_submitted");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        $conn->close();
    } else {
        header("Location: index.php?msg=empty_review");
        exit;
    }
}
?>
