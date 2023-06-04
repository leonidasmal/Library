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
  if (isset($_SESSION['Category_ID'])) {
    $categoryID = $_SESSION['Category_ID'];
    $category=$_SESSION['category'];
    $baut=true; 


    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
      $categoryname = $_POST["category"];
      
      $existingCategoryQuery = "SELECT COUNT(*) FROM category WHERE category = ?";
      $existingCategoryStmt = mysqli_prepare($conn, $existingCategoryQuery);
      mysqli_stmt_bind_param($existingCategoryStmt, 's', $categoryname);
      mysqli_stmt_execute($existingCategoryStmt);
      mysqli_stmt_store_result($existingCategoryStmt);

      // Retrieve the count
      mysqli_stmt_bind_result($existingCategoryStmt, $count);
      mysqli_stmt_fetch($existingCategoryStmt);
      // Check if the count is greater than 0
      if ($count > 0) {
        echo 'This category is already associated with this book.';
      }else {
      // Update summary
      $updateAuthorQuery = "UPDATE category SET category = ? WHERE Category_ID = ?";
      $stmt = $conn->prepare($updateAuthorQuery);
      $stmt->bind_param("si", $categoryname, $categoryID);
      $stmt->execute();
      header("Location: edit_book.php");
        exit;
      }


  }
}
  ?>

  <form method="post">
    <input type="hidden" name="category_id" value="<?php echo $categoryID; ?>">

    <label for="author_fullname">Author Fullname:</label>
    <input type="text" name="category" id="category" value="<?php echo $category; ?>" required> 

    <?php if (isset($error_message)): ?>
      <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <input type="submit" value="Save Changes">
  </form>

</body>
</html>
