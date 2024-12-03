<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the action (add or delete)
    $action = $_POST['action'] ?? '';

    // Add New Menu Item
    if ($action == 'add') {
        $image_url = $_POST['menu-image'];
        $name = $_POST['menu-name'];
        $description = $_POST['menu-description'];
        $price = $_POST['menu-price'];

        // Input sanitization and validation
        if (empty($image_url) || empty($name) || empty($description) || !is_numeric($price)) {
            echo "<script>alert('Invalid input. Please ensure all fields are filled correctly.');</script>";
        } else {
            // Prepared statement to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO menu_items (image_url, name, description, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssd", $image_url, $name, $description, $price);  // "sssd" means: string, string, string, double

            if ($stmt->execute()) {
                // On success, redirect
                echo "<script>
                        alert('New menu item added successfully!');
                        window.location.href='dashboard.php'; // Redirect after success
                      </script>";
            } else {
                echo "<script>
                        alert('Error adding menu item: " . $stmt->error . "');
                      </script>";
            }

            $stmt->close();
        }
    }

    // Delete Menu Item
    elseif ($action == 'delete') {
        $name = $_POST['menu-name'];
    
        // Check if name is provided
        if (!empty($name)) {
            // Prepared statement to delete the menu item by name
            $stmt = $conn->prepare("DELETE FROM menu_items WHERE name = ?");
            $stmt->bind_param("s", $name);  // "s" means: string
    
            if ($stmt->execute()) {
                // Check if any rows were actually deleted
                if ($stmt->affected_rows > 0) {
                    echo "<script>
                            alert('Menu item deleted successfully!');
                            window.location.href='dashboard.php'; // Redirect after success
                          </script>";
                } else {
                    echo "<script>
                            alert('No menu item found with the given name.');
                          </script>";
                }
            } else {
                echo "<script>
                        alert('Error deleting menu item: " . $stmt->error . "');
                      </script>";
            }
    
            $stmt->close();
        } else {
            echo "<script>
                    alert('Please provide the menu item name to delete.');
                  </script>";
        }
    }    
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body style="margin: 40px;">
<h2 class="section-title" style="text-align: center; color: #ff6f61;">Admin Dashboard</h2>

<!-- Add Menu Item Form -->
<h3 style="color: #ff6f61;">Add New Menu Item</h3>
<form id="add-menu-form" method="POST" action="dashboard.php" style="margin-bottom: 40px;">
    <label for="menu-image">Image URL:</label>
    <input type="text" id="menu-image" name="menu-image" required style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd;">

    <label for="menu-name">Name:</label>
    <input type="text" id="menu-name" name="menu-name" required style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd;">

    <label for="menu-description">Description:</label>
    <textarea id="menu-description" name="menu-description" required style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd;"></textarea>

    <label for="menu-price">Price:</label>
    <input type="number" id="menu-price" name="menu-price" required style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd;">

    <button type="submit" style="background-color: #ff6f61; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Add Menu Item</button>
</form>

<h3 style="color: #ff6f61;">Delete Menu Item</h3>
<form method="POST" action="dashboard.php" style="margin-bottom: 40px;">
    <label for="menu-name" style="color: #333; margin-top: 15px;">Menu Item Name:</label>
    <input type="text" id="menu-name" name="menu-name" placeholder="Enter the name of the menu item to delete" required style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 14px;">

    <input type="hidden" name="action" value="delete">

    <button type="submit" style="background-color: #ff6f61; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Delete Menu Item</button>
</form>

<!-- Order History Table -->
<h3 style="color: #ff6f61;">Order History</h3>
<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr style="background-color: #ff6f61; color: white;">
            <th style="padding: 10px; border: 1px solid #ddd;">Order ID</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Customer Name</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Menu Item</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Price</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Date</th>
        </tr>
    </thead>
    <tbody id="order-history">
        <!-- Order history will be added here -->
    </tbody>
</table>

<script>
    window.onload = function() {
        // Fetch order history data from the PHP script
        fetch('fetch_order_history.php')
            .then(response => response.json())
            .then(data => {
                const orderHistoryContainer = document.getElementById('order-history');
                if (data.length === 0) {
                    orderHistoryContainer.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 10px;">No orders found</td></tr>';
                    return;
                }
                data.forEach(order => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td style="padding: 10px; border: 1px solid #ddd;">${order.order_id}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">${order.customer_name}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">${order.item_name}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">$${order.item_price}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">${new Date(order.order_date).toLocaleDateString()}</td>
                    `;
                    orderHistoryContainer.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error fetching order history:', error);
            });
    };
</script>
</body>
</html>
