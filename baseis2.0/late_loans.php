<?php
// Assuming you have a database connection established
session_start();
include("connect.php");

$searchName = isset($_POST['search_name']) ? $_POST['search_name'] : '';
$searchSurname = isset($_POST['search_surname']) ? $_POST['search_surname'] : '';
$searchDaysLate = isset($_POST['search_quantity']) && $_POST['search_quantity'] !== '' ? intval($_POST['search_quantity']) : null;

// Check if the user is logged in as a manager
if (!isset($_SESSION['Manager_ID'])) {
  echo "Access denied";
  exit;
}

// Retrieve the late loans data from the database
$lateLoansQuery = "SELECT First_name, Last_name, Days_Late FROM lateloans AS ll";

// Add search conditions based on the provided criteria
if (!empty($searchName)) {
  $lateLoansQuery .= " WHERE ll.First_name LIKE '%$searchName%'";
}

if (!empty($searchSurname)) {
  if (!empty($searchName)) {
    $lateLoansQuery .= " AND ll.Last_name LIKE '%$searchSurname%'";
  } else {
    $lateLoansQuery .= " WHERE ll.Last_name LIKE '%$searchSurname%'";
  }
}

if (isset($searchDaysLate)) {
  if (!empty($searchName) || !empty($searchSurname)) {
    $lateLoansQuery .= " AND";
  } else {
    $lateLoansQuery .= " WHERE";
  }
  $lateLoansQuery .= " ll.Days_Late = $searchDaysLate";
}

$lateLoansResult = mysqli_query($conn, $lateLoansQuery);

if (!$lateLoansResult) {
  echo "Query execution failed: " . mysqli_error($conn);
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Late Loans</title>
  <style>
    table {
      border-collapse: collapse;
      margin: 0 auto;
      width: 50%;
    }

    th, td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f2f2f2;
    }
  </style>
</head>
<body>
  <h2>Late Loans</h2>

  <form method="POST">
    <input type="text" name="search_name" placeholder="Search by Name" value="<?php echo $searchName; ?>">
    <input type="text" name="search_surname" placeholder="Search by Surname" value="<?php echo $searchSurname; ?>">
    <input type="number" name="search_quantity" placeholder="Search by Days late" value="<?php echo $searchDaysLate; ?>">
    <button type="submit">Search</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Surname</th>
        <th>Days Late</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Display late loans in the table
      while ($loan = mysqli_fetch_assoc($lateLoansResult)) {
        ?>
        <tr>
          <td><?php echo $loan['First_name']; ?></td>
          <td><?php echo $loan['Last_name']; ?></td>
          <td><?php echo $loan['Days_Late']; ?></td>
        </tr>
        <?php
      }
      ?>
    </tbody>
  </table>
  <form action="operator_manager_dashboard.php" method="get">
    <input type="submit" value="Back">
</form>
</body>
</html>
