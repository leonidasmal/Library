<?php
session_start();
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $firstName = $_POST['firstName'];
  $lastName = $_POST['lastName'];
  $dateOfBirth = $_POST['date_of_birth']; 
  $username = $_POST['username'];
  $password = $_POST['password'];
  $email = $_POST['email'];
  $school = $_POST['school'];
  $role = $_POST['role'];



  $select = "SELECT * FROM users WHERE username='$username'";
  $result = mysqli_query($conn, $select);

  if (!$result) {
    echo "Query execution failed: " . mysqli_error($conn);
    exit;
  }

  if (mysqli_num_rows($result) > 0) {
    echo "Username already exists. Please choose a different username.";
    exit;
  } else {
    // Proceed with the registration process
    $register = "INSERT INTO users (username, user_password, Email, first_name, last_name, approved) VALUES ('$username', '$password', '$email', '$firstName', '$lastName', '0')";
    mysqli_query($conn, $register);

    // Get the newly inserted user ID
    $userId = mysqli_insert_id($conn);

    $schoolQuery = "SELECT School_ID, School_Name FROM school_unit";
    $schoolResult = mysqli_query($conn, $schoolQuery);

    if (!$schoolResult) {
      echo "Query execution failed: " . mysqli_error($conn);
      exit;
    }

    $schoolID = "";

    while ($row = mysqli_fetch_assoc($schoolResult)) {
      if ($row['School_ID'] == $school) {
        $schoolID = $row['School_ID'];
        break;
      }
    }

    if ($role == 'operator_manager') {
      $insertManager = "INSERT INTO school_unit_manager (Manager_ID, Accepted, School_ID, Admin_ID, User_ID) VALUES ('', '', '$schoolID', NULL, '$userId')";
      $insertManagerIntoSchool = "INSERT INTO students_professors (studprof_id, Is_Professor, Date_of_birth, School_ID, User_ID) VALUES ('', '1', '$dateOfBirth', '$schoolID', '$userId')";

      mysqli_query($conn, $insertManager);
      mysqli_query($conn, $insertManagerIntoSchool);
      echo '<div style="background-color: #d4edda; padding: 30px; border-radius: 10px; font-family: Arial, sans-serif; font-size: 32px; color: #155724; text-align: center; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);"> Your registration as an operator manager will be considered by an admin. Please wait for confirmation. Thank you!</div>';  
      } else {
      $isProfessor = ($role == 'professor') ? 1 : 0;
      $insertTeacherStudent = "INSERT INTO students_professors (studprof_id, first_name, last_name, email, Manager_ID, Is_Professor, Date_of_birth, School_ID, User_ID) VALUES ('', '$firstName', '$lastName', '$email', NULL, '$isProfessor', '$dateOfBirth', '$schoolID', '$userId')";
      mysqli_query($conn, $insertTeacherStudent);
      echo '<div style="background-color: #d4edda; padding: 30px; border-radius: 10px; font-family: Arial, sans-serif; font-size: 32px; color: #155724; text-align: center; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);">Registration complete! Thank you for registering. Your account request is pending for approval by the operation manager of your school. Please wait for confirmation. Thank you!</div>';
     
    }

    
    exit;
  }
}
?> 




<!DOCTYPE html>
<html>
<head>
  <title>Registration Page</title>
 
  <link rel="stylesheet" type="text/css" href="registration.css">
  <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
  <script type="text/javascript">
    function openNewWindow() {
      console.log("Opening new window...");
      window.open("success.html", "_blank");
    }
    $(document).ready(function() {
    $("#dateOfBirth").datepicker({
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      changeYear: true,
      yearRange: "1965:c",
    });
  });
  </script>
</head>
<body>
<div class="header">Library System</div>
<div class="container">
  <h2>Registration Form</h2>
  <img src="EMP.png" class="top-right-image">

  <form action="registration.php" method="POST">
    <div class="input-group">
      <label for="firstName">First Name:</label>
      <input type="text" id="firstName" name="firstName" required>
    </div>
    <div class="input-group">
      <label for="lastName">Last Name:</label>
      <input type="text" id="lastName" name="lastName" required>
    </div>
   

    <div class="input-group">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>
    </div>
    <div class="input-group">
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
    </div>
    <div class="input-group">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
    </div>
    <div class="input-group">
  <label for="date_of_birth">Date of Birth:</label>
  <input type="text" id="dateOfBirth" name="date_of_birth" required>
</div>


    <div class="input-group">
      <label for="school">School:</label>
      
      <select id="school" name="school">
          <?php
            $schoolQuery = "SELECT School_ID, School_Name FROM school_unit";
            $schoolResult = mysqli_query($conn, $schoolQuery);

            if (!$schoolResult) {
              echo "Query execution failed: " . mysqli_error($conn);
              exit;
            }

            while ($row = mysqli_fetch_assoc($schoolResult)) {
              $schoolID = $row['School_ID'];
              $schoolName = $row['School_Name'];

              echo "<option value='$schoolID'>$schoolName</option>";
            }
          ?>
        </select>
      </div>
    <div class="input-group">
      <label class="input-group-label" for="role">You are registering for:</label>
      <select id="role" name="role" class="input-group-select">
        <option value="student">Student</option>
        <option value="professor">Professor</option>
        <option value="operator_manager">Operator Manager</option>
      </select>
    </div>
    <div class="input-group">
      <input type="submit" value="Register">
    </div>
  </form>
  
  <div class="input-group">
    <p>Already registered? <a href="login.php">Log in here</a></p>
  </div>
</div>
</body>
</html>