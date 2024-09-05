<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch Results</title>
    <style>
        .add-to-cart, .remove-from-cart, .decrease-quantity {
            background-color: #4CAF50;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }

        .remove-from-cart::before {
            content: "\1F5D1"; /* Unicode for trash symbol */
            font-size: 18px;
            margin-right: 5px;
        }
    </style>
</head>
<body>

<h2>Fetch Results from XAMPP Server</h2>

<form id="checkoutForm">
    <h3>Shopping Cart</h3>
    <ul id="cartItems"></ul>
    <p>Total Bill from <span id="storeName"></span>: $<span id="totalBill">0.00</span></p>
    <p>Stores in Cart: <span id="storesInCart"></span></p>
    <button type="button" onclick="checkout('creditCard')">Checkout with Credit Card</button>
    <button type="button" onclick="checkout('wallet')">Checkout with Wallet</button>
</form>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    var cart = [];
    var totalBill = 0;
    var currentStoreName = null;
    var storesInCart = new Set(); // Set to store unique store names

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
            storesInCart.add(storeName); // Add the store name to the set
        }

        updateCartDisplay();
        updateTotalBill();
        updateStoresInCart();
    }

    function removeFromCart(itemIndex) {
        // Remove the item from the cart
        var removedStoreName = cart[itemIndex].store;
        cart.splice(itemIndex, 1);

        updateCartDisplay();
        updateTotalBill();
        updateStoresInCart(removedStoreName);
    }

    function decreaseQuantity(itemIndex) {
        // Decrease the quantity of the item
        if (cart[itemIndex].quantity > 1) {
            cart[itemIndex].quantity--;
            cart[itemIndex].totalPrice = cart[itemIndex].quantity * cart[itemIndex].price;
        } else {
            // If the quantity is 1, remove the item entirely
            var removedStoreName = cart[itemIndex].store;
            cart.splice(itemIndex, 1);
            updateStoresInCart(removedStoreName);
        }

        updateCartDisplay();
        updateTotalBill();
    }

    function updateCartDisplay() {   
        // Display the items in the cart
        var cartItemsList = $("#cartItems");
        cartItemsList.empty();

        for (var i = 0; i < cart.length; i++) {
            var listItem = $("<li>").text(cart[i].name + " (Qty: " + cart[i].quantity + ") - $" + cart[i].totalPrice.toFixed(2));
            var decreaseButton = $("<button class='decrease-quantity'>-</button>");
            var removeButton = $("<button class='remove-from-cart'>Remove from Cart</button>");
            
            // Use an IIFE to capture the current value of 'i' in a closure
            (function(index) {
                decreaseButton.click(function() {
                    decreaseQuantity(index);
                });

                removeButton.click(function() {
                    removeFromCart(index);
                });
            })(i);

            listItem.append(decreaseButton);
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

    function updateStoresInCart(removedStoreName) {
        // Update the list of stores in the cart
        var storesArray = Array.from(storesInCart);

        // If a store is removed, update the list
        if (removedStoreName) {
            storesInCart.delete(removedStoreName);
        }

        // Display the updated list of stores
        $("#storesInCart").text(storesArray.join(', '));
    }

    function checkout(paymentMethod) {
        // Perform checkout logic based on the payment method
        var totalAmountToBePaid = totalBill.toFixed(2);

        if (paymentMethod === 'creditCard') {
            window.location.href = 'checkout.html?totalAmountToBePaid=' + totalAmountToBePaid;
        } else if (paymentMethod === 'wallet') {
            window.location.href = 'wallet_balance.php?totalAmountToBePaid=' + totalAmountToBePaid;
        }
    }
</script>

<?php
// Include the connect.php file
include('connect.php');

// Call the connectToDatabase() function to get a database connection
$conn = connectToDatabase();

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

        // Output item information with "Add to Cart" buttons
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