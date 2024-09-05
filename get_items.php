<?php
// get_items.php

// Include the database connection file
include 'connect.php';

$selectedStoreId = $_POST['store_id'];

$result = $conn->query("SELECT * FROM item WHERE store_id = $selectedStoreId");

echo "<h3>Items from the selected store:</h3>";
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    echo "<li>{$row['item_name']} - {$row['item_description']} - \${$row['price']}</li>";
}
echo "</ul>";
?>
