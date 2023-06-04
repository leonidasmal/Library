<!DOCTYPE html>
<html>
<head>
  <title>Contact Us</title>
  <style>
    body {
      background-color: #000;
      padding: 20px;
      font-family: Arial, sans-serif;
    }

    .container {
      max-width: 400px;
      margin: 0 auto;
      background-color: #FFFFFF;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      text-align: left;
    }

    .container h2 {
      text-align: center;
      margin-bottom: 10px;
      color: #333;
    }

    .form-group select {
      width: 350px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
      font-size: 14px;
      appearance: none;
      background-color: #fff;
      background-image: url('https://static.thenounproject.com/png/1666929-200.png');
      background-position: right;
      background-repeat: no-repeat;
      background-size: 20px;
      cursor: pointer;
    }

    .form-group select:focus {
      outline: none;
      border-color: #4CAF50;
    }

    .form-group option {
      padding: 10px;
    }

    .form-group select#reason {
      margin-bottom: 10px;
    }

    .form-group select#role {
      margin-bottom: 10px;
    }

    .form-group select#email {
      margin-bottom: 20px;
    }

    .form-group textarea {
      margin-top: 10px;
    }

    .form-group textarea {
      width: 350px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
      font-size: 14px;
      resize: vertical;
      height: 150px;
    }

    .form-group label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
      color: #555;
    }

    .form-group input {
      width: 350px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
      font-size: 14px;
    }

    .form-group select option {
      padding: 10px;
      background-color: #f5f5f5;
      color: #333;
    }

    .form-group select option:hover {
      background-color: #e0e0e0;
    }

    .form-group input[type="submit"] {
      background-color: #000;
      display: block;
      margin: 0 auto;
      margin-top: 20px;
      color: #fff;
      cursor: pointer;
    }

    .form-group input[type="submit"]:hover {
      background-color: #000;
    }

    .error-message {
      color: #ff0000;
      font-size: 14px;
      margin-top: 10px;
    }

    .logo {
      text-align: center;
      margin-bottom: 10px;
    }

    .logo img {
      max-width: 300px;
    }

    .nav {
      background-color: #000;
      padding: 10px;
      margin-bottom: 20px;
    }

    .nav ul {
      list-style-type: none;
      margin: 0;
      padding: 0;
      overflow: hidden;
    }

    .nav li {
      float: left;
    }

    .nav li a {
      display: block;
      color: #fff;
      text-align: center;
      padding: 14px 16px;
      text-decoration: none;
      font-size: 30px;
    }

    .nav li a:hover {
      background-color: #111;
    }
  </style>
</head>
<body>
  <div class="nav">
    <ul>
      <li><a href="front_page.php"><h3>Home</h3></a></li>
    </ul>
  </div>

  <div class="container">
    <?php
    include('connect.php');

    // Fetch the administrator emails from the database
    $sql = "SELECT u.Email
            FROM administrator AS a
            JOIN Users AS u ON a.User_ID = u.User_ID";
    $result = $conn->query($sql);

    // Display the administrator emails
    if ($result->num_rows > 0) {
        $output = "<div class='form-group'>";
        $output .= "<label for='reason'>School registration:</label>";
        $output .= "<p>For a new registration, send an email to:</p>";
        while ($row = $result->fetch_assoc()) {
          $output .= "<p>" . $row["Email"] . "</p>";
        }
        $output .= "</div>";
      }

    // Close the database connection
    $conn->close();
    ?>

    <h2>Contact Us</h2>
      <!-- Print the administrator emails -->
        <?php echo $output; ?>      
      <p>with the following information:</p>
      <ul>
        <li>School Name</li>
        <li>Address</li>
        <li>City</li>
        <li>Telephone</li>
        <li>Email</li>
        <li>Principal Full Name</li>
        </ul>
  </div>
</body>
</html>
