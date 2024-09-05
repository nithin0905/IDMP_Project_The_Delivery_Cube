<?php
session_start(); // Start the session

// Check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include the connect.php file
include('connect.php');

// Call the connectToDatabase() function to get a database connection
$conn = connectToDatabase();

// Get the user ID from the session
$userId = $_SESSION['user_id'];

// Fetch the wallet balance for the user
$query = "SELECT * FROM wallet WHERE customer_id = '$userId'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $walletData = $result->fetch_assoc();
    $walletBalance = $walletData['balance'];

    // Display the current wallet balance
    echo "<h2>Your Current Wallet Balance: $walletBalance</h2>";

    // Get the initially due amount from the URL parameter
    $initialDueAmount = isset($_GET['initialDueAmount']) ? $_GET['initialDueAmount'] : 0;

    // Display the initially due amount
    echo "<p>Initially Due Amount: $initialDueAmount</p>";

    // Automatically pay the initially due amount
    if ($walletBalance >= $initialDueAmount) {
        // Deduct the amount from the wallet balance
        $newBalance = $walletBalance - $initialDueAmount;

        // Update the wallet balance in the database
        $updateQuery = "UPDATE wallet SET balance = '$newBalance' WHERE customer_id = '$userId'";
        $conn->query($updateQuery);

        // Display the updated wallet balance
        echo "<p>Payment successful! Updated Wallet Balance: $newBalance</p>";
    } else {
        // Display a message indicating insufficient balance
        echo "<p>Insufficient wallet balance. Please add funds to your wallet.</p>";
    }

    // Display a form to add funds
    echo "<form method='post'>";
    echo "<label for='amount'>Enter Amount to Add:</label>";
    echo "<input type='number' id='amount' name='amount' required>";
    echo "<button type='submit' name='addFundsButton'>Add Funds</button>";
    echo "</form>";

    // Process fund addition when the "Add Funds" button is clicked
    if (isset($_POST['addFundsButton'])) {
        $amountToAdd = $_POST['amount'];

        // Validate the amount (you can add more validation as needed)
        if ($amountToAdd > 0) {
            // Update the wallet balance in the database
            $newBalance = $walletBalance + $amountToAdd;
            $updateQuery = "UPDATE wallet SET balance = '$newBalance' WHERE customer_id = '$userId'";
            $conn->query($updateQuery);

            // Redirect to wallet_balance.php after updating the balance
            header("Location: wallet_balance.php");
            exit();
        } else {
            echo "<p>Please enter a valid amount to add funds.</p>";
        }
    }
} else {
    // Wallet not found for the user, display an error message
    echo "<p>Wallet not found for the user.</p>";
    // You can add more content or redirect to another page as needed
}

// Close the database connection
$conn->close();
?>
