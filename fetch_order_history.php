<?php

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "restaurant_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch order history
$sql = "SELECT o.order_id, o.customer_name, oi.menu_item_name AS item_name, oi.price AS item_price, o.order_date 
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        ORDER BY o.order_date DESC";

$result = $conn->query($sql);

$orderHistory = [];

if ($result->num_rows > 0) {
    // Fetch each row and store in the $orderHistory array
    while ($row = $result->fetch_assoc()) {
        $orderHistory[] = $row;
    }

    echo json_encode($orderHistory);
} else {
    echo json_encode([]);
}

$conn->close();
?>
