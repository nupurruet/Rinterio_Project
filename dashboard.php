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

$user_id = $_SESSION['user_id'];

// Fetch saved rents
$stmt = $conn->prepare("SELECT property, location, rent, bedrooms, saved_at FROM saved_rents WHERE user_id = ? ORDER BY saved_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">

<!-- Navbar -->
<nav class="bg-white shadow-lg p-4 flex justify-between items-center sticky top-0 z-50">
  <h1 class="text-2xl font-bold text-green-600">🏠 Rinterio</h1>
  <div class="flex items-center gap-2">
    <a href="index.php" class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Back to Home</a>
    <span class="mr-4">Hi, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
    <a href="logout.php" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">Logout</a>
  </div>
</nav>

<!-- Saved Rents Section -->
<section class="max-w-6xl mx-auto py-12 px-4">
  <h2 class="text-3xl font-bold mb-8 text-center">My Saved Rents</h2>
  <div class="grid md:grid-cols-2 gap-6">
    <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="bg-white shadow-md rounded-xl p-4">
          <h3 class="text-xl font-bold"><?php echo htmlspecialchars($row['property']); ?></h3>
          <p class="text-gray-600">📍 <?php echo htmlspecialchars($row['location']); ?></p>
          <p>Rent: <span class="font-semibold"><?php echo number_format($row['rent']); ?> BDT</span></p>
          <p>Bedrooms: <?php echo $row['bedrooms']; ?></p>
          <p class="text-gray-400 text-sm mt-2">Saved on: <?php echo date("d M Y, h:i A", strtotime($row['saved_at'])); ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="col-span-2 text-center text-red-500">You haven't saved any properties yet.</p>
    <?php endif; ?>
  </div>
</section>

</body>
</html>
<?php $stmt->close(); $conn->close(); ?>
