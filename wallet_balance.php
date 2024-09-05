<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet Balance</title>
    <link rel="stylesheet" href="style_wallet_balance.css">
</head>
<body>
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

    // Get the total amount to be paid from the cart
    $totalAmountToBePaid = isset($_GET['totalAmountToBePaid']) ? $_GET['totalAmountToBePaid'] : 0;

    // Display the wallet balance
    echo "<h2>Your Wallet Balance: $walletBalance</h2>";

    // Display the amount to be paid (if provided)
    if ($totalAmountToBePaid > 0) {
        echo "<p>Amount to be paid: $totalAmountToBePaid</p>";
    }

    // Check if the wallet balance is sufficient
    if ($walletBalance >= $totalAmountToBePaid) {
        // Display payment confirmation
        echo "<p>Please pay</p>";
    } else {
        // Display a message indicating insufficient balance
        echo "<p>Insufficient wallet balance. Please add funds to your wallet.</p>";
    }

    // Offer textbox and apply button
    echo "<form method='post'>";
    echo "<label for='offerId'>Enter Offer ID:</label>";
    echo "<input type='text' id='offerId' name='offerId' placeholder='Enter Offer ID'>";
    echo "<button type='submit' name='applyOfferButton' class='paymentButton'>Apply</button><br>";

    // Buttons for paying and adding funds
    echo "<button type='submit' name='payButton' class='paymentButton'>Pay</button><br>";
    echo "<button type='submit' name='addFundsButton' class='paymentButton'>Add Funds</button>";

    // Input field for adding funds
    echo "<label for='addFundsAmount'>Add Funds Amount:</label>";
    echo "<input type='text' id='addFundsAmount' name='addFundsAmount' placeholder='Enter amount to add'>";
    echo "</form>";

    // Process payment or fund addition when buttons are clicked
    if (isset($_POST['payButton'])) {
        // Check if the wallet balance is sufficient before processing payment
        if ($walletBalance >= $totalAmountToBePaid) {
            // Perform payment logic and update the wallet balance
            // You can add your payment processing logic here
            // For simplicity, I'll just deduct the amount from the wallet balance
            $newBalance = $walletBalance - $totalAmountToBePaid;

            // Update the wallet balance in the database
            $updateQuery = "UPDATE wallet SET balance = '$newBalance' WHERE customer_id = '$userId'";
            $conn->query($updateQuery);

            // Display the updated wallet balance
            echo "<p>Updated Wallet Balance: $newBalance</p>";
        } else {
            // Display a message indicating insufficient balance
            echo "<p>Insufficient wallet balance. Please add funds to your wallet.</p>";
        }
    }

    if (isset($_POST['addFundsButton'])) {
        // Check if the addFundsAmount is set and is a valid number
        if (isset($_POST['addFundsAmount']) && is_numeric($_POST['addFundsAmount'])) {
            $amountToAdd = $_POST['addFundsAmount'];

            // Perform addition of funds logic and update the wallet balance
            // You can add your logic here, for example, updating the wallet balance by adding the entered amount
            $newBalance = $walletBalance + $amountToAdd;

            // Update the wallet balance in the database
            $updateQuery = "UPDATE wallet SET balance = '$newBalance' WHERE customer_id = '$userId'";
            $conn->query($updateQuery);

            // Display the updated wallet balance
            echo "<p>Updated Wallet Balance: $newBalance</p>";
        } else {
            // Display an error message if the entered amount is not valid
            echo "<p>Error: Please enter a valid amount to add funds.</p>";
        }
    }
    if (isset($_POST['applyOfferButton'])) {
        // Offer application logic
        $offerId = $_POST['offerId'];

        // Check if the entered offer_id is valid
        $offerQuery = "SELECT * FROM offer WHERE offer_id = '$offerId'";
        $offerResult = $conn->query($offerQuery);

        if ($offerResult->num_rows > 0) {
            $offerData = $offerResult->fetch_assoc();
            $discountPercentage = $offerData['discount_percentage'];

            // Apply the offer discount to the total amount to be paid
            $discountAmount = ($discountPercentage / 100) * $totalAmountToBePaid;
            $totalAmountToBePaidWithDiscount = $totalAmountToBePaid - $discountAmount;

            echo "<p>Offer applied! Discount amount: $discountAmount</p>";

            // Update the total amount with discount in the session
            $_SESSION['totalAmountToBePaidWithDiscount'] = $totalAmountToBePaidWithDiscount;

            // Redirect to the same page to reflect the updated amount
            header("Location: wallet_balance.php?totalAmountToBePaid=$totalAmountToBePaidWithDiscount");
            exit();
        } else {
            echo "<p>Invalid offer. Please enter a valid offer ID.</p>";
        }
    }

    echo "</form>";
} else {
    // Wallet not found for the user, display an error message
    echo "<p>Wallet not found for the user.</p>";
    // You can add more content or redirect to another page as needed
}

// Close the database connection
$conn->close();
?>



</body>
</html>
