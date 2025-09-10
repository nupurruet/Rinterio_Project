<?php 
session_start();

// Default property data
$properties = [
    ["name"=>"Modern Studio","location"=>"Dhaka","rent"=>15000,"bedrooms"=>1,"image"=>"assets/img2.png"],
    ["name"=>"Family Apartment","location"=>"Chittagong","rent"=>25000,"bedrooms"=>3,"image"=>"assets/img3.png"],
    ["name"=>"Luxury Villa","location"=>"Sylhet","rent"=>50000,"bedrooms"=>5,"image"=>"assets/img4.png"],
];

// Connect to DB
$conn = new mysqli("localhost", "root", "", "rinterio_db");

// Fetch user-added properties
$user_properties = [];
if (!$conn->connect_error) {
    $res = $conn->query("SELECT name, location, rent, bedrooms, image FROM user_properties ORDER BY id DESC");
    if ($res) {
        while($row = $res->fetch_assoc()) {
            $user_properties[] = $row;
        }
    }

    // Fetch reviews
    $reviews = [];
    $res2 = $conn->query("SELECT user_name, review, created_at FROM reviews ORDER BY created_at DESC");
    if ($res2) {
        while($row = $res2->fetch_assoc()) {
            $reviews[] = $row;
        }
    }
}

// Handle property submission
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $rent = $_POST['rent'];
    $bedrooms = $_POST['bedrooms'];
    $image = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    $target = "assets/".$image;
    move_uploaded_file($tmp_name, $target);

    $stmt = $conn->prepare("INSERT INTO user_properties (user_id, name, location, rent, bedrooms, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issiis", $user_id, $name, $location, $rent, $bedrooms, $image);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php?property_added=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Rinterio - Home</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">

<!-- Notifications -->
<?php if(isset($_GET['success'])): ?>
<div class="max-w-6xl mx-auto mt-4 px-4 py-3 bg-green-500 text-white rounded text-center">
    Rental saved successfully!
</div>
<?php endif; ?>
<?php if(isset($_GET['review_submitted'])): ?>
<div class="max-w-6xl mx-auto mt-4 px-4 py-3 bg-blue-500 text-white rounded text-center">
    Review submitted successfully!
</div>
<?php endif; ?>
<?php if(isset($_GET['property_added'])): ?>
<div class="max-w-6xl mx-auto mt-4 px-4 py-3 bg-green-500 text-white rounded text-center">
    Property added successfully!
</div>
<?php endif; ?>

<!-- Navbar -->
<nav class="bg-white shadow-lg p-4 flex justify-between items-center sticky top-0 z-50">
<h1 class="text-2xl font-bold text-green-600">🏠 Rinterio</h1>
<div>
<?php if (isset($_SESSION['user_id'])): ?>
    <span class="mr-4">Hi, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
    <a href="dashboard.php" class="px-3 py-2 bg-green-500 text-white rounded">Dashboard</a>
    <a href="logout.php" class="px-3 py-2 bg-red-500 text-white rounded">Logout</a>
<?php else: ?>
    <a href="login.php" class="px-3 py-2 bg-blue-500 text-white rounded">Login</a>
    <a href="register.php" class="px-3 py-2 bg-gray-700 text-white rounded">Register</a>
<?php endif; ?>
</div>
</nav>

<!-- Hero Section -->
<section class="relative bg-cover bg-center h-[400px]" style="background-image: url('assets/img1.png');">
<div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-center text-white">
<h2 class="text-5xl font-bold mb-4">Find Your Dream Home</h2>
<p class="text-lg mb-6">Affordable rents, luxury apartments & more</p>
<a href="#properties" class="px-6 py-3 bg-green-500 rounded-lg text-white text-lg font-semibold hover:bg-green-600">Explore Now</a>
</div>
</section>

<!-- Services Section -->
<section class="max-w-6xl mx-auto py-12 px-4 text-center">
<h3 class="text-3xl font-bold mb-8">Our Services</h3>
<div class="grid md:grid-cols-3 gap-8">
<div class="bg-white shadow-lg rounded-xl p-6">
<img src="assets/article1.png" class="w-full h-40 object-cover rounded-lg mb-4" alt="Property Listing">
<h4 class="text-xl font-semibold mb-2">Property Listing</h4>
<p class="text-gray-600">Find verified homes, apartments & villas at best locations.</p>
</div>
<div class="bg-white shadow-lg rounded-xl p-6">
<img src="assets/article2.png" class="w-full h-40 object-cover rounded-lg mb-4" alt="Easy Booking">
<h4 class="text-xl font-semibold mb-2">Easy Booking</h4>
<p class="text-gray-600">Book your desired property online with just one click.</p>
</div>
<div class="bg-white shadow-lg rounded-xl p-6">
<img src="assets/article3.png" class="w-full h-40 object-cover rounded-lg mb-4" alt="Tenant Dashboard">
<h4 class="text-xl font-semibold mb-2">Tenant & Owner Dashboard</h4>
<p class="text-gray-600">Manage your rentals & add new properties securely.</p>
<?php if(isset($_SESSION['user_id'])): ?>
<button onclick="document.getElementById('propertyModal').classList.remove('hidden')" class="mt-3 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Add Property</button>
<?php else: ?>
<p class="text-red-500 mt-2">Login to access your dashboard.</p>
<?php endif; ?>
</div>
</div>
</section>

<!-- Property Modal -->
<div id="propertyModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
<div class="bg-white rounded-lg p-6 w-96 relative">
<button onclick="document.getElementById('propertyModal').classList.add('hidden')" class="absolute top-2 right-2 text-red-500 font-bold">X</button>
<h3 class="text-xl font-bold mb-4">Add New Property</h3>
<form method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
<input type="text" name="name" placeholder="Property Name" required class="p-2 border rounded">
<input type="text" name="location" placeholder="Location" required class="p-2 border rounded">
<input type="number" name="rent" placeholder="Rent" required class="p-2 border rounded">
<input type="number" name="bedrooms" placeholder="Bedrooms" required class="p-2 border rounded">
<input type="file" name="image" accept="image/*" required class="p-2 border rounded">
<button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Submit</button>
</form>
</div>
</div>

<!-- Properties Section -->
<section id="properties" class="max-w-6xl mx-auto py-12 px-4">
<h3 class="text-3xl font-bold mb-8 text-center">Available Properties</h3>
<div class="grid md:grid-cols-3 gap-6">

<?php
// Merge default and user-added properties
$all_properties = array_merge($properties, $user_properties);
foreach ($all_properties as $prop): ?>
<div class="bg-white shadow-md rounded-xl overflow-hidden">
<img src="<?php echo $prop['image']; ?>" alt="<?php echo htmlspecialchars($prop['name']); ?>" class="w-full h-48 object-cover">
<div class="p-4">
<h4 class="text-xl font-bold"><?php echo htmlspecialchars($prop['name']); ?></h4>
<p class="text-gray-600">📍 <?php echo htmlspecialchars($prop['location']); ?></p>
<p class="mt-2">Rent: <span class="font-semibold"><?php echo number_format($prop['rent']); ?> BDT</span></p>
<p>Bedrooms: <?php echo $prop['bedrooms']; ?></p>
<?php if (isset($_SESSION['user_id'])): ?>
<form action="save_rent.php" method="POST" class="mt-3">
<input type="hidden" name="property" value="<?php echo htmlspecialchars($prop['name']); ?>">
<input type="hidden" name="location" value="<?php echo htmlspecialchars($prop['location']); ?>">
<input type="hidden" name="rent" value="<?php echo $prop['rent']; ?>">
<input type="hidden" name="bedrooms" value="<?php echo $prop['bedrooms']; ?>">
<button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Save Rent</button>
</form>
<?php else: ?>
<p class="text-red-500 mt-2">Login to save this property.</p>
<?php endif; ?>
</div>
</div>
<?php endforeach; ?>

</div>
</section>

<!-- Reviews Section -->
<section class="bg-gray-100 py-12">
<div class="max-w-6xl mx-auto text-center">
<h3 class="text-3xl font-bold mb-8">What Our Clients Say</h3>
<div class="grid md:grid-cols-3 gap-6 mb-8">
<?php if(!empty($reviews)): ?>
<?php foreach($reviews as $r): ?>
<div class="bg-white shadow-md p-6 rounded-lg">
<p class="italic">"<?php echo htmlspecialchars($r['review']); ?>"</p>
<h4 class="mt-4 font-bold">— <?php echo htmlspecialchars($r['user_name']); ?></h4>
<p class="text-gray-400 text-sm"><?php echo date("d M Y, h:i A", strtotime($r['created_at'])); ?></p>
</div>
<?php endforeach; ?>
<?php else: ?>
<p class="col-span-3 text-red-500">No reviews yet.</p>
<?php endif; ?>
</div>

<?php if (isset($_SESSION['user_id'])): ?>
<h3 class="text-2xl font-bold mt-12 mb-4">Submit Your Review</h3>
<form action="submit_review.php" method="POST" class="max-w-xl mx-auto">
<textarea name="review" rows="4" class="w-full p-3 border rounded mb-4" placeholder="Write your review..." required></textarea>
<button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">Submit Review</button>
</form>
<?php else: ?>
<p class="text-red-500 mt-8">Login to submit a review.</p>
<?php endif; ?>
</div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white text-center py-4">
<p>&copy; <?php echo date("Y"); ?> Rinterio. All rights reserved.</p>
</footer>

</body>
</html>
<?php $conn->close(); ?>
