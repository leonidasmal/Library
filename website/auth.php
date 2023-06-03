<?php
session_start();

function validateSessionToken($requiredRoles = []) {
  // Check if the session token exists and is valid
  if (isset($_GET['session_token']) && $_GET['session_token'] === $_SESSION['session_token']) {
    // Perform further authentication or authorization checks based on the required roles
    if (!empty($requiredRoles)) {
      // Check if the user has the required roles
      // Implement your logic here
    }
    // Access granted
    return true;
  } else {
    // Invalid or missing session token, redirect the user to the login page
    header("Location: login.php");
    exit;
  }
}
