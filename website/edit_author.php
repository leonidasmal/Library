<!DOCTYPE html>
<html>
<head>
  <title>Edit Author</title>
  <style>
    h1 {
      text-align: center;
    }

    form {
      width: 400px;
      margin: auto;
    }

    label {
      display: block;
      margin-bottom: 10px;
    }

    input[type="text"],
    input[type="date"] {
      width: 100%;
      padding: 5px;
      margin-bottom: 10px;
    }

    input[type="submit"] {
      display: block;
      width: 100%;
      padding: 10px;
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
    }

    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <h1>Edit Author</h1>
  <?php
  include("connect.php");
  session_start();

  // Check if loan_id is present in the URL
  if (isset($_SESSION['Author_ID'])) {
    $authorID = $_SESSION['Author_ID'];
    $authorname=$_SESSION['author_fullname'];
    $baut=true; 


    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
      $authorname = $_POST["author_fullname"];
      
      $existingAuthorQuery = "SELECT COUNT(*) FROM author WHERE author_fullname = ?";
      $existingAuthorStmt = mysqli_prepare($conn, $existingAuthorQuery);
      mysqli_stmt_bind_param($existingAuthorStmt, 's', $authorname);
      mysqli_stmt_execute($existingAuthorStmt);
      mysqli_stmt_store_result($existingAuthorStmt);

      // Retrieve the count
      mysqli_stmt_bind_result($existingAuthorStmt, $count);
      mysqli_stmt_fetch($existingAuthorStmt);
      // Check if the count is greater than 0
      if ($count > 0) {
        echo 'This author is already associated with this book.';
      }else {
      // Update summary
      $updateAuthorQuery = "UPDATE author SET author_fullname = ? WHERE Author_ID = ?";
      $stmt = $conn->prepare($updateAuthorQuery);
      $stmt->bind_param("si", $authorname, $authorID);
      $stmt->execute();
      header("Location: edit_book.php");
        exit;
      }


  }
}
  ?>

  <form method="post">
    <input type="hidden" name="author_id" value="<?php echo $authorID; ?>">

    <label for="author_fullname">Author Fullname:</label>
    <input type="text" name="author_fullname" id="author_fullname" value="<?php echo $authorname; ?>" required> 

    <?php if (isset($error_message)): ?>
      <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <input type="submit" value="Save Changes">
  </form>

</body>
</html>
