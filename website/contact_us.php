<?php

include('connect.php');
session_start();
// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

// Logout functionality
if (isset($_GET['logout'])) {
  session_destroy(); // Destroy all session data
  header("Location: login.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Raleway:400,700">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Raleway', sans-serif;
      background-color: #f3f3f3;
      margin: 0;
      padding: 0;
    }

    .header {
      background-color: #333;
      color: #fff;
      padding: 20px;
      text-align: center;
    }
    .container {
  width: 400px;
  margin: 0 auto;
  padding: 20px;
  border: 1px solid #ccc;
  text-align: center;
}


    h1 {
      font-size: 24px;
      font-weight: 700;
      text-align: center;
      margin-top: 0;
      margin-bottom: 20px;
    }

    form {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: 700;
    }

    input[type="text"],
    input[type="email"],
    textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      margin-bottom: 10px;
      font-size: 14px;
      font-family: 'Raleway', sans-serif;
    }

    textarea {
      resize: vertical;
      height: 120px;
    }

    button[type="submit"] {
      background-color: #333;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      font-size: 14px;
      font-weight: 700;
      text-transform: uppercase;
      cursor: pointer;
    }

    button[type="submit"]:hover {
      background-color: #222;
    }

    nav {
  background-color: #333;
  padding: 10px;
}

nav ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
}

nav ul li {
  margin-right: 10px;
  display: inline-block;
}

nav ul li a {
  color: #fff;
  text-decoration: none;
  padding: 5px 10px;
}

nav ul li a:hover {
  background-color: #222;
}


nav ul li a:hover {
  background-color: #222;
}


    nav ul li a:hover {
      background-color: #222;
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>Contact Us</h1>
  </div>
  <nav>
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="search_book.php">Search Books</a></li>
      <li><a href="view_account.php">My Account</a></li>
      <li><a href="#">Library Events</a></li>
      <li><a href="#">Contact Us</a></li>
      <li><a href="login.php?logout=1">Log Out</a></li>
    </ul>
  </nav>
  <div class="container">
    <form method="POST" action="">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required>
      
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
      
      <label for="message">Message:</label>
      <textarea id="message" name="message" required></textarea>
      
      <button type="submit">Submit</button>
    </form>
  </div>
</body>
</html>
