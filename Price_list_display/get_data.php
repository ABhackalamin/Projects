<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Prices</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Latest Product Prices</h2>
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "product_database";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT product1_price, product2_price, product3_price, product_name FROM products ORDER BY created_at DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "<table>";
            echo "<tr><th>Product Name</th><th>Product 1 Price</th><th>Product 2 Price</th><th>Product 3 Price</th></tr>";
            echo "<tr><td>" . $row["product_name"] . "</td><td>" . $row["product1_price"] . "</td><td>" . $row["product2_price"] . "</td><td>" . $row["product3_price"] . "</td></tr>";
            echo "</table>";
        } else {
            echo "<p>No data found</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
