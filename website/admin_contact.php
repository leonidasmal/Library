<!DOCTYPE html>
<html>
<head>
  <title>Contact Us</title>
  <style>
  body {
      background-color:  #000; padding: 20px;
      font-family: Arial, sans-serif;
    }
    .container {
      max-width: 400px;
      margin: 0 auto;
      background-color:  #FFFFFF;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      text-align: left; /* Center the contents */
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
      background-position: right ;
      background-repeat: no-repeat;
      background-size: 20px; /* Adjust the size as needed */
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
  /* Add margin or padding to create space */
  margin-bottom: 10px;
}
.form-group select#role {
  /* Add margin or padding to create space */
  margin-bottom: 10px;
}

.form-group select#email {
  /* Add margin or padding to create space */
  margin-bottom: 20px;
}
.form-group textarea {
  /* Add margin or padding to create space */
  margin-top: 10px;
}
    .form-group textarea {
  width: 350px;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 3px;
  font-size: 14px;
  resize: vertical;
  height: 150px; /* Adjust the height as needed */
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
      /* Change the color to your desired color */
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
    <li><a href="#"><h3>About</h3></a></li>
    <li><a href="admin_contact.php"><h3>Contact</h3></a></li>
    </ul>

    <div class="container">
    <?php
if (isset($_POST['submit'])) {
  // Process the form submission
  $reason = $_POST['reason'];
  $role = $_POST['role'];
  $email = $_POST['email'];
  $message = $_POST['message'];

  // Display the "Thank you" message
  echo "<div class='message'>Thank you for contacting us. We will get back to you soon.</div>";
}
?>

    <h2>Contact Us</h2>
   
    <form action="" method="POST">
      <!-- Form fields omitted for brevity -->
      <div class="form-group">
        <label for="reason">Reason:</label>
        <select id="reason" name="reason">
          <option value="general">General Inquiry</option>
          <option value="support">Technical Support</option>
          <option value="billing">Billing Inquiry</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="form-group">
        <label for="role">Role:</label>
        <select id="role" name="role">
          <option value="Student">Student</option>
          <option value="Professor">Professor</option>
          <option value="Operator Manager">Operator Manager</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea>
      </div>
      
      <div class="form-group">
        <input type="submit" name="submit" value="Submit">
      </div>
    </form>
  </div>
</body>
</html>