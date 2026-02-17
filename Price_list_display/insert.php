<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "product_database";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    // Select the database
    $conn->select_db($dbname);
} else {
    die("Error creating database: " . $conn->error);
}

// Create table if not exists
$table_sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product1_price INT DEFAULT NULL,
    product2_price INT DEFAULT NULL,
    product3_price INT DEFAULT NULL,
    product_name VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($table_sql)) {
    die("Error creating table: " . $conn->error);
}

// Insert data based on request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product1 = isset($_POST['Product1']) ? $_POST['Product1'] : NULL;
    $product2 = isset($_POST['Product2']) ? $_POST['Product2'] : NULL;
    $product3 = isset($_POST['Product3']) ? $_POST['Product3'] : NULL;
    $message = isset($_POST['message']) ? $_POST['message'] : NULL;

    $sql = "INSERT INTO products (product1_price, product2_price, product3_price, product_name) 
            VALUES ('$product1', '$product2', '$product3', '$message')";

    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
