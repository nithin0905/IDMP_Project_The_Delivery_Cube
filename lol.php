<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch Results</title>
    <style>
        .add-to-cart, .remove-from-cart {
            background-color: #4CAF50;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2>Fetch Results from XAMPP Server</h2>

<form id="checkoutForm">
    <h3>Shopping Cart</h3>
    <ul id="cartItems"></ul>
    <p>Total Bill from <span id="storeName"></span>: $<span id="totalBill">0.00</span></p>
    <button type="button" onclick="checkout()">Checkout</button>
</form>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    var cart = [];
    var totalBill = 0;
    var currentStoreName = null;

    function addToCart(storeName, itemName, itemPrice) {
        // Check if the item is already in the cart
        var existingItem = cart.find(item => item.store === storeName && item.name === itemName);

        if (existingItem) {
            // If the item is already in the cart, increase the quantity and update the total
            existingItem.quantity++;
            existingItem.totalPrice = existingItem.quantity * itemPrice;
        } else {
            // If the item is not in the cart, add it with a quantity of 1
            cart.push({ store: storeName, name: itemName, price: itemPrice, quantity: 1, totalPrice: itemPrice });
        }

        updateCartDisplay();
        updateTotalBill();
    }

    function removeFromCart(itemIndex) {
        // Remove the item from the cart
        cart.splice(itemIndex, 1);

        updateCartDisplay();
        updateTotalBill();
    }

    function updateCartDisplay() {
        // Display the items in the cart
        var cartItemsList = $("#cartItems");
        cartItemsList.empty();

        for (var i = 0; i < cart.length; i++) {
            var listItem = $("<li>").text(cart[i].name + " (Qty: " + cart[i].quantity + ") - $" + cart[i].totalPrice.toFixed(2));
            var removeButton = $("<button class='remove-from-cart'>Remove from Cart</button>");
            
            // Use an IIFE to capture the current value of 'i' in a closure
            (function(index) {
                removeButton.click(function() {
                    removeFromCart(index);
                });
            })(i);

            listItem.append(removeButton);
            cartItemsList.append(listItem);
        }

        // Display the store name in the cart
        $("#storeName").text(currentStoreName);
    }

    function updateTotalBill() {
        // Calculate the total bill
        totalBill = cart.reduce((acc, item) => acc + item.totalPrice, 0);
        $("#totalBill").text(totalBill.toFixed(2));
    }

    function checkout() {
        // Perform checkout logic (e.g., send cart data to server)
        alert("Checkout completed! Total Bill from " + currentStoreName + ": $" + totalBill.toFixed(2));
    }
</script>

<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "delivery_cube";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch data from grocery and items
$sql = "SELECT g.store_id, g.store_name, i.item_name, i.item_description, i.price
        FROM grocery g
        LEFT JOIN item i ON g.store_id = i.store_id
        ORDER BY g.store_id, i.item_id";

$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    die("Error fetching data: " . $conn->error);
}

// Check if there are any results
if ($result->num_rows > 0) {
    $currentStoreId = null;

    while ($row = $result->fetch_assoc()) {
        // Check if we are moving to a new store
        if ($currentStoreId !== $row['store_id']) {
            // Output store information
            echo "<h3>Store ID: {$row['store_id']}, Store Name: {$row['store_name']}</h3>";
            $currentStoreId = $row['store_id'];
        }

        // Output item information with an "Add to Cart" button
        echo "<ul>";
        echo "<li>Item: {$row['item_name']}, Description: {$row['item_description']}, Price: \${$row['price']}";
        echo "<button class='add-to-cart' onclick=\"addToCart('{$row['store_name']}', '{$row['item_name']}', {$row['price']})\">Add to Cart</button>";
        echo "</li>";
        echo "</ul>";
    }
} else {
    echo "No results found";
}

// Close the database connection
$conn->close();
?>

</body>
</html>
