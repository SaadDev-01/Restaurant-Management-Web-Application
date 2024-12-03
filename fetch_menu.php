<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'restaurant_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);

// If there are menu items, loop through and display them
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div style="background: #fff; border: 1px solid #ddd; padding: 20px; width: 250px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 10px; text-align: center; transition: transform 0.3s ease; margin: 10px; flex-shrink: 0; cursor: pointer;">';
        echo '<img src="' . $row['image_url'] . '" alt="' . $row['name'] . '" style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px;">';
        echo '<h3 style="font-size: 1.5rem; margin: 10px 0; color: #333;">' . $row['name'] . '</h3>';
        echo '<p style="font-size: 1rem; color: #555; margin-bottom: 10px;">' . $row['description'] . '</p>';
        echo '<span style="font-size: 1.2rem; color: #000; font-weight: bold;">$' . $row['price'] . '</span>';
        
        // Add a quantity selector for each item
        echo '<div style="margin-top: 15px;">';
        echo '<label for="quantity_' . $row['id'] . '" style="font-size: 1rem; color: #333;">Quantity: </label>';
        echo '<input type="number" id="quantity_' . $row['id'] . '" value="1" min="1" style="width: 50px; padding: 5px; margin-left: 5px; border-radius: 5px; border: 1px solid #ccc;">';
        echo '</div>';

        // Add an "Add to Order" button with the correct ID
        echo '<div style="margin-top: 15px;">';
        echo '<button onclick="addToCart(' . $row['id'] . ', \'' . $row['name'] . '\', ' . $row['price'] . ')" style="background: #FF5733; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Add to Order</button>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo "<p>No menu items available at the moment.</p>";
}

$conn->close();
?>
