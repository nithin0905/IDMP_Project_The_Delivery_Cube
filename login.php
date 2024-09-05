<?php
session_start(); // Start the session

// Include the connect.php file
include('connect.php');

// Call the connectToDatabase() function to get a database connection
$conn = connectToDatabase();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Validate the login credentials
    $query = "SELECT * FROM customer WHERE name = '$username' AND customer_id = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Login successful, store user information in session variables
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['customer_id'];
        $_SESSION['user_name'] = $user['name'];
        
        // Redirect to after_login.php
        header("Location: after_login.php");
        exit();
    } else {
        // Invalid credentials, display an error message or redirect back to the login page
        echo "Invalid username or password. Please try again.";
    }
}

// Close the database connection if needed
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Redirect</title>
</head>
<body>

<h2>Login</h2>

<form action="login.php" method="post">
    <label for="username">Username (Customer Name):</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password (Customer ID):</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Login</button>
</form>

</body>
</html>
