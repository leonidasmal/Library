<?php
session_start();
include("connect.php");

if (isset($_GET["School_ID"])) {
  $selectedSchoolID = $_GET["School_ID"];
  $_SESSION["School_ID"] = $selectedSchoolID;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION["School_ID"])) {
  $selectedSchoolID = $_SESSION["School_ID"];
  $username = $_POST["username"];
  $password = $_POST["password"];

  $userQuery = "SELECT u.User_ID, sp.studprof_ID, sm.Manager_ID, sp.School_ID, sm.School_ID
                FROM users u
                LEFT JOIN students_professors sp ON u.User_ID = sp.User_ID
                LEFT JOIN school_unit_manager sm ON u.User_ID = sm.User_ID
                WHERE u.username = ? AND u.user_password = ?";
  $stmt = $conn->prepare($userQuery);
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  $userResult = $stmt->get_result();

  if ($userResult->num_rows == 1) {
    $userRow = $userResult->fetch_assoc();
    $userID = $userRow["User_ID"];
    $studprofID = $userRow["studprof_ID"];
    $managerID = $userRow["Manager_ID"];
    $studentProfessorSchoolID = $userRow["School_ID"];
    $managerSchoolID = $userRow["School_ID"];

    if (($managerID && $managerSchoolID == $selectedSchoolID) || ($studprofID && $studentProfessorSchoolID == $selectedSchoolID)) {
      $_SESSION["User_ID"] = $userID;
      $_SESSION["username"] = $username;
      if ($managerID) {
        $_SESSION["Manager_ID"] = $managerID;
        header("Location: operator_manager_dashboard.php");
        exit;
      } elseif ($studprofID) {
        $_SESSION["studprof_ID"] = $studprofID;
        header("Location: user_dashboard.php");
        exit;
      }
    } else {
      echo "<h1 style='text-align: center; color: white;'>You are not authorized to access this school.</h1>";
    }
  } else {
    echo "<h1 style='text-align: center; color: white;'>Invalid username or password.</h1>";
  }
} elseif (!isset($_SESSION["School_ID"])) {
  echo "<h1 style='text-align: center; color: black;'>Please select your school on the front page.</h1>";
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login Page</title>
  <link rel="stylesheet" type="text/css" href="registration.css">
</head>
<body>
<div class="header">Library System</div>
<div class="container">
<img src="https://t4.ftcdn.net/jpg/02/29/75/83/360_F_229758328_7x8jwCwjtBMmC6rgFzLFhZoEpLobB6L8.jpg" alt="Image Description">

  <h2>Login</h2>
  <img src="EMP.png" class="top-right-image">

  <form action="<?php echo $_SERVER['PHP_SELF'] . '?School_ID=' . $_SESSION['School_ID']; ?>" method="POST">
    <div class="input-group">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>
    </div>
    <div class="input-group">
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
    </div>
    <div class="input-group">
      <input type="submit" value="Log in">
    </div>
  </form>
  
  <div class="input-group">
    <p>New user? <a href="registration.php">Register here</a></p>
  </div>
</div>
</body>
</html>
