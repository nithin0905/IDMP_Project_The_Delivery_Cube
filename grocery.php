<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grocery Page</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

<h2>Choose a grocery store</h2>

<!-- Form to select a store -->
<form id="storeForm">
    <label for="storeSelect">Select a Store:</label>
    <select id="storeSelect" name="store">
        <?php
        // Include the database connection file
        include 'connect.php';

        // Fetch store IDs from the 'grocery' table
        $result = $conn->query("SELECT store_id FROM grocery");

        // Check if the query was successful
        if ($result === false) {
            die("Error fetching store IDs: " . $conn->error);
        }

        // Check if there are any store IDs available
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['store_id']}'>{$row['store_id']}</option>";
            }
        } else {
            echo "<option value='' disabled>No store IDs available</option>";
        }

        // Close the database connection (optional in this case since PHP will close it when the script ends)
        // $conn->close();
        ?>
    </select>

    <!-- Button to trigger fetching and displaying items -->
    <button type="button" onclick="getItems()">Show Items</button>
</form>

<!-- Container to display items -->
<div id="itemContainer"></div>

<!-- Script to handle AJAX request and update items based on the selected store -->
<script>
    function getItems() {
        var selectedStoreId = document.getElementById("storeSelect").value;

        $.ajax({
            type: "POST",
            url: "get_items.php",
            data: { store_id: selectedStoreId },
            success: function(response) {
                $("#itemContainer").html(response);
            }
        });
    }
</script>

</body>
</html>
