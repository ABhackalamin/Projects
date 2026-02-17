<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Digital Price Display Board</title>
  <style>
    /* General Styling */
    body {
      font-family: 'Arial', sans-serif;
      background-color: #DCAE96;
      margin: 0;
      padding: 0;
    }
    #container {
      padding: 40px;
      margin: 50px auto;
      background-color: #E0BBE8;
      border-radius: 12px;
      width: 90%;
      max-width: 700px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    h1 {
      font-size: 2em;
      color: #6C4F9C;
      margin-bottom: 20px;
    }
    footer {
      font-size: 0.8em;
      margin-top: 20px;
      color: #777;
    }

    /* Form Styling */
    form {
      background-color: #F1E1F6;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
    }
    label {
      display: block;
      font-size: 1.1em;
      margin: 5px 0;
      color: #5F3A8D;
    }
    .price-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }
    .price-label {
      font-size: 1.1em;
      color: #5F3A8D;
      width: 30px; /* Adjust the width as needed */
      padding: 5px; /* Optional: add padding for better readability */

    }
    .price-input {
      width: 80%; /* Longer input fields */
      padding: 10px;
      font-size: 1em;
      border-radius: 6px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }
    .price-input:focus {
      outline: none;
      border-color: #6C4F9C;
    }

    input[type="submit"] {
      background-color: #6C4F9C;
      color: white;
      font-size: 1.2em;
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
      margin-top: 20px;
    }
    input[type="submit"]:hover {
      background-color: #57306B;
    }

    /* Footer Styling */
    footer {
      text-align: center;
      padding: 10px;
      background-color: #6C4F9C;
      color: white;
      border-radius: 8px;
      margin-top: 40px;
    }

    /* Responsive Styling */
    @media screen and (max-width: 600px) {
      #container {
        padding: 20px;
        margin: 20px;
      }
      h1 {
        font-size: 1.5em;
      }
    }
  </style>
  <script>
    function sendText(event) {
      event.preventDefault(); // Prevent form submission
      var request = new XMLHttpRequest();
      var product1 = document.getElementsByName("Product1")[0].value;
      var product2 = document.getElementsByName("Product2")[0].value;
      var product3 = document.getElementsByName("Product3")[0].value;
      var message = document.getElementById("messageInput").value;
      
      request.open("POST", "insert.php", true);
      request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      request.send("message=" + encodeURIComponent(message) + "&Product1=" + product1 + "&Product2=" + product2 + "&Product3=" + product3);
      
      request.onload = function() {
        alert(request.responseText);
      }
    }
  </script>
</head>
<body>
  <div id="container">
    <h1>Displaying Everyday Price In Bangladesh</h1>
    
    <form id="price_form" onsubmit="sendText(event)">
      <!-- "Products Name & Price" input field moved above price fields -->
      <div class="price-container">
        <label class="price-label" for="Product1">Rice Price(Tk/Kg):</label>
        <input type="number" name="Product1" class="price-input" min="0" max="9999" required>
      </div>
      
      <div class="price-container">
        <label class="price-label" for="Product2">Flour Price(Tk/Kg):</label>
        <input type="number" name="Product2" class="price-input" min="0" max="9999" required>
      </div>
      
      <div class="price-container">
        <label class="price-label" for="Product3">Potato Price(Tk/Kg):</label>
        <input type="number" name="Product3" class="price-input" min="0" max="9999" required>
      </div>

      <label>Products Name & Price:
        <input type="text" id="messageInput" name="Message" maxlength="255" class="price-input">
      </label>

      <input type="submit" value="Submit">
    </form>

    <footer><p>&copy; 2025 All rights reserved, Alamin.</p></footer> 
  </div>
</body>
</html>
