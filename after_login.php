<?php
session_start(); // Start the session

// Check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>After Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="greeting-container">
    <p>Hello <?php echo $userName; ?>!</p>
    <p>What would you like to go through today?</p>
</div>

<div class="button-container">
    <button onclick="redirectToPage('restaurant.html')">Restaurant</button>
    <button onclick="redirectToPage('remove_add_cart.php')">Grocery</button>
    <!-- Add other buttons as needed -->
</div>

<script>
    function redirectToPage(page) {
        // Redirect to the specified page
        window.location.href = page;
    }
</script>

</body>
</html>
