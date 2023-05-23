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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  // Retrieve the selected search option
  $searchOption = $_POST['search_option'];

  // Perform search and retrieve matching books based on the selected option
  // Replace this with your own code to fetch matching books from the database
  // You'll need to modify the query based on the selected search option and its corresponding value
  $searchResults = [];
  if ($searchOption === 'category') {
    $category = $_POST['category_name'];
    // Perform the search using the selected category
    $query = "SELECT * FROM book_category WHERE category_name = '$category'";
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if ($result) {
      // Fetch the search results as an associative array
      $searchResults = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
      // Query execution failed
      echo "Error executing query: " . mysqli_error($conn);
    }
  } elseif ($searchOption === 'keywords') {
    // Handle multiple keyword search option
    if (isset($_POST['keywords'])) {
      // Retrieve the selected keywords as an array
      $keywords = $_POST['keywords'];
  
      // Create a placeholder for each selected keyword
      $placeholders = implode(', ', array_fill(0, count($keywords), '?'));
  
      // Prepare the query with the placeholders
      $query = "SELECT * FROM book_keyword WHERE keyword IN ($placeholders)";
      $stmt = mysqli_prepare($conn, $query);
  
      // Bind the keyword values to the prepared statement
      mysqli_stmt_bind_param($stmt, str_repeat('s', count($keywords)), ...$keywords);
  
      // Execute the prepared statement
      mysqli_stmt_execute($stmt);
  
      // Get the search results
      $result = mysqli_stmt_get_result($stmt);
  
      // Check if the query was successful
      if ($result) {
        // Fetch the search results as an associative array
        $searchResults = mysqli_fetch_all($result, MYSQLI_ASSOC);
      } else {
        // Query execution failed
        echo "Error executing query: " . mysqli_error($conn);
      }
  
      // Close the prepared statement
      mysqli_stmt_close($stmt);
    } else {
      // Keywords not set or empty
      echo "Please select one or more keywords.";
    }
  }elseif ($searchOption === 'author') {
    // Handle author search option
    if (isset($_POST['author_fullname'])) {
      // Retrieve the selected author
      $author = $_POST['author_fullname'];
      // Modify the query according to your database schema
      $query = "SELECT * FROM books WHERE author = '$author'";
      $result = mysqli_query($conn, $query);

      // Check if the query was successful
      if ($result) {
        // Fetch the search results as an associative array
        $searchResults = mysqli_fetch_all($result, MYSQLI_ASSOC);
      } else {
        // Query execution failed
        echo "Error executing query: " . mysqli_error($conn);
      }
    } else {
      // Author fullname not set or empty
      echo "Please select an author.";
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Catalog</title>
  <link rel="stylesheet" type="text/css" href="user_dash.css">
  <style>
    .search-form {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }
    
    .search-form label {
      margin-right: 10px;
    }
    
    .search-form select,
    .search-form input[type="text"],
    .search-form input[type="submit"] {
      font-size: 16px;
      padding: 8px 12px;
    }
    
    .search-form select {
      margin-right: 10px;
    }
    
    .search-form input[type="submit"] {
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
    }
    
    .search-form input[type="submit"]:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
<header>
  <div class="logo">Library</div>
  <nav>
    <ul>
      <li><a href="user_dashboard.php">Home</a></li>
      <li><a href="search_book.php">Search Books</a></li>
      <li><a href="view_account.php">My Account</a></li> <!-- Updated link -->
      <li><a href="#">Library Events</a></li>
      <li><a href="#">Contact Us</a></li>
      <li><a href="user_dashboard.php?logout">Log Out</a></li> <!-- Update the logout link -->     
    </ul>
  </nav>
</header>

<section class="main-content">
  <div class="container">

    <!-- Search Form -->
    <form method="POST" class="search-form">
      <label for="search_option">Search by:</label>
      <select name="search_option" id="search_option">
        <option value="category">Category</option>
        <option value="keywords">Keywords</option>
        <option value="author">Author</option>
      </select>

      <!-- Dynamic Input Fields -->
      <?php if (isset($_POST['search_option']) && $_POST['search_option'] === 'author') : ?>
        <?php
        // Fetch authors' full names from the database
        $query = "SELECT author_fullname FROM book_author";
        $result = mysqli_query($conn, $query);
        $authors = mysqli_fetch_all($result, MYSQLI_ASSOC);
        ?>
        <select name="author_fullname" id="author_fullname">
          <?php foreach ($authors as $author) : ?>
            <option value="<?php echo $author['author_fullname']; ?>"><?php echo $author['author_fullname']; ?></option>
          <?php endforeach; ?>
        </select>
     <!-- Dynamic Input Fields -->
<?php if (isset($_POST['search_option']) && $_POST['search_option'] === 'keywords') : ?>
  <?php
  // Fetch keywords from the database
  $query = "SELECT keyword FROM book_keyword";
  $result = mysqli_query($conn, $query);
  $keywords = mysqli_fetch_all($result, MYSQLI_ASSOC);
  ?>
  <select name="keywords[]" id="keywords" multiple>
    <?php foreach ($keywords as $keyword) : ?>
      <option value="<?php echo $keyword['keyword']; ?>"><?php echo $keyword['keyword']; ?></option>
    <?php endforeach; ?>
  </select>
<?php endif; ?>
<?php 
        // Fetch categories from the database
        $query = "SELECT category_name FROM book_category";
        $result = mysqli_query($conn, $query);
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
        ?>
        <select name="category_name" id="category_name">
          <?php foreach ($categories as $category) : ?>
            <option value="<?php echo $category['category_name']; ?>"><?php echo $category['category_name']; ?></option>
          <?php endforeach; ?>
        </select>
      <?php endif; ?>
      <input type="submit" value="Search">
    </form>

    <!-- Display Search Results -->
    <?php if (isset($searchResults)) : ?>
      <div class="search-results">
        <h3>Here are the books based on your search:</h3>
        <?php if (count($searchResults) > 0) : ?>
          <ul>
            <?php foreach ($searchResults as $result) : ?>
              <li><?php echo $result; ?></li>
            <?php endforeach; ?>
          </ul>
        <?php else : ?>
          <p>No results found.</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>

<script>
  // Show/hide author search input based on selected option
  const searchOption = document.getElementById('search_option');
  const authorSearch = document.getElementById('author_fullname');

  searchOption.addEventListener('change', function() {
    if (searchOption.value === 'author') {
      authorSearch.style.display = 'block';
    } else {
      authorSearch.style.display = 'none';
    }
  });
</script>

</body>
</html>
