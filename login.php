<?php
session_start();
$host = "localhost"; $user = "root"; $pass = ""; $db = "rinterio_db";
$conn = new mysqli($host, $user, $pass, $db);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "❌ Invalid Login.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    input { display: block; margin: 10px 0; padding: 8px; width: 250px; }
    button { padding: 8px 16px; margin-top: 10px; }
    a { display: inline-block; margin-top: 15px; text-decoration: none; color: #fff; background: #4CAF50; padding: 8px 16px; border-radius: 4px; }
    a:hover { background: #45a049; }
  </style>
</head>
<body>
<h2>Login</h2>
<form method="POST">
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit">Login</button>
</form>

<!-- Back to Home Button -->
<a href="index.php">⬅ Back to Home</a>

</body>
</html>
