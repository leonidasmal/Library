<!DOCTYPE html>
<html>
<head>
  <title>Loan Overview</title>
  <style>
    h1 {
      text-align: center;
    }

    table {
      width: 80%;
      margin-left: auto;
      margin-right: auto;
    }

    th, td {
      border: 1px solid #dddddd;
      text-align: left;
      padding: 8px;
      white-space: nowrap; /* Prevent table cell content from wrapping */
      overflow: hidden; /* Hide content if it exceeds cell width */
      text-overflow: ellipsis; /* Add ellipsis (...) if content overflows */
    }

    th {
      background-color: #f2f2f2;
    }

    .clickable {
      cursor: pointer;
      color: black;
      text-decoration: underline;
    }

    .edit-button,
    .delete-button {
      padding: 5px 10px;
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
    }
  </style>
  <script>
    function confirmDeleteLoan(loanID) {
      var result = confirm("Are you sure you want to delete this loan?");
      if (result) {
        document.getElementById("delete_loan_" + loanID).submit();
      }
    }
  </script>
</head>
<body>
  <h1>Loan Overview</h1>
  <a href="make_loan.php" style="position: absolute; top: 10px; right: 10px;">Make Loan</a>
  <?php
  include("connect.php");
  session_start();


  // Check if the delete form is submitted
  if (isset($_POST['delete_loan'])) {
    $loanID = $_POST['loan_id'];

    // Delete the loan record from the database
    $deleteLoanQuery = "DELETE FROM loan WHERE loan_id = ?";
    $deleteLoanStmt = mysqli_prepare($conn, $deleteLoanQuery);
    mysqli_stmt_bind_param($deleteLoanStmt, 'i', $loanID);
    mysqli_stmt_execute($deleteLoanStmt);

    // Redirect to the same page
    header("Location: manage_loans.php");
    exit;
  }

  if (isset($_SESSION['Manager_ID'])) {
    $ManagerID = $_SESSION['Manager_ID'];

  // Retrieve loan data from the database
  $loanQuery = "SELECT L.loan_id, L.loan_date, L.date_returned, u.username, b.title, sp.User_ID
    FROM loan L
    INNER JOIN book b ON L.book_ID = b.book_ID
    INNER JOIN students_professors sp ON L.studprof_ID = sp.studprof_ID
    INNER JOIN users u ON sp.user_ID = u.user_ID
    WHERE Manager_ID = ?";

    $stmt = $conn->prepare($loanQuery);
    $stmt->bind_param("i", $ManagerID);
    $stmt->execute();
    $loanResult = $stmt->get_result();;
  }
  ?>

  <table>
    <tr>
      <th>Loan ID</th>
      <th>Book Title</th>
      <th>Username</th>
      <th>Loan Date</th>
      <th>Return Date</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($loanResult)): ?>
      <tr>
        <td><?php echo $row['loan_id']; ?></td>
        <td><?php echo $row['title']; ?></td>
        <td><?php echo $row['username']; ?></td>
        <td><?php echo $row['loan_date']; ?></td>
        <td>
          <?php if ($row['date_returned'] === null): ?>
            <span class="clickable">No registered return date</span>
          <?php else: ?>
            <span class="clickable"><?php echo $row['date_returned']; ?></span>
          <?php endif; ?>
        </td>
        <td>
          <button class="edit-button" onclick="window.location.href='edit_loan.php?loan_id=<?php echo $row['loan_id']; ?>'">Edit</button>
          <button class="delete-button" onclick="confirmDeleteLoan(<?php echo $row['loan_id']; ?>)">Delete</button>
          <form id="delete_loan_<?php echo $row['loan_id']; ?>" method="post" style="display: none;">
            <input type="hidden" name="loan_id" value="<?php echo $row['loan_id']; ?>">
            <input type="hidden" name="delete_loan" value="1">
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
