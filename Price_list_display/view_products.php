<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "product_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the last inserted data
$sql = "SELECT * FROM products ORDER BY created_at DESC LIMIT 1";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Last Product Inserted</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #4CAF50;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }

        .header img {
            width: 150px; /* Increased image size */
            height: 150px; /* Increased image size */
            margin-right: 20px; /* Increased space between the image and text */
        }

        .product-info {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            font-size: 18px;
        }

        .product-info div {
            width: 30%;
        }

        .marquee {
            margin-top: 20px;
            font-size: 20px;
            color: #333;
            font-weight: bold;
            background-color: #e7f7e7;
            padding: 10px;
            text-align: center;
        }

        marquee {
            font-size: 22px;
            color: #4CAF50;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        footer p {
            margin: 0;
        }

    </style>
</head>
<body>

<div class="header">
    <img src="DNCRP.jpg" alt="DNCRP logo">
    <h1>National Consumer Rights Protection Directorate, People's Republic of Bangladesh</h1>
</div>

<div class="container">
    <h2><marquee style="color: red; font-size: 20px;">
      Welcome to the National Consumer Rights Protection Directorate, People's Republic of Bangladesh. Here you can see the product and its last updated price. If retailer does not follow this price please call on 16121. Thank you.
    </marquee></h2>
    <h2>Last Updated Product & Its Price</h2>

    <?php
    if ($result->num_rows > 0) {
        // Output the last inserted record
        $row = $result->fetch_assoc();
    ?>
        <div class="product-info">
            <div><strong>Rice Price(TK/KG):</strong> <?php echo $row['product1_price']; ?></div><br>
            <div><strong>Flour Price(TK/KG):</strong> <?php echo $row['product2_price']; ?></div><br>
            <div><strong>Potato Price(TK/KG):</strong> <?php echo $row['product3_price']; ?></div><br>
        </div>

        <div class="marquee">
            <marquee behavior="scroll" direction="left"><?php echo $row['product_name']; ?></marquee>
        </div>

    <?php
    } else {
        echo "<p>No data available.</p>";
    }

    $conn->close();
    ?>
</div>

<footer>
    <p>&copy; 2025 All rights reserved, Alamin.</p>
</footer>

</body>
</html>
