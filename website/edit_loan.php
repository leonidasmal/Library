<!DOCTYPE html>
<html>
<head>
  <title>Edit Loan</title>
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
  <h1>Edit Loan</h1>
  <?php
  include("connect.php");
  session_start();

  // Check if loan_id is present in the URL
  if (isset($_GET['loan_id'])) {
    $loanID = $_GET['loan_id'];

    // Retrieve loan data from the database
    $loanQuery = "SELECT L.loan_id, L.loan_date, L.date_returned, u.username, b.title, sp.User_ID
      FROM loan L
      INNER JOIN book b ON L.book_ID = b.book_ID
      INNER JOIN students_professors sp ON L.studprof_ID = sp.studprof_ID
      INNER JOIN users u ON sp.user_ID = u.user_ID
      WHERE L.loan_id = ?";

    $stmt = $conn->prepare($loanQuery);
    $stmt->bind_param("i", $loanID);
    $stmt->execute();
    $loanResult = $stmt->get_result();
    $loan = $loanResult->fetch_assoc();

    if (!$loan) {
      echo "Loan not found.";
      exit;
    }

    // Retrieve book titles based on the school ID
    $bookQuery = "SELECT b.title
                  FROM Book b
                  INNER JOIN School_Book sb ON b.book_ID = sb.Book_ID
                  WHERE sb.School_ID = ?";

    // Retrieve usernames based on the school ID
    $userQuery = "SELECT u.username
                  FROM Users u
                  INNER JOIN students_professors sp ON u.User_ID = sp.User_ID
                  WHERE sp.School_ID = ?";

    // You need to provide the School_ID value here
    $school_id = $_SESSION['School_ID'];

    $stmt = $conn->prepare($bookQuery);
    $stmt->bind_param("i", $school_id);
    $stmt->execute();
    $bookResult = $stmt->get_result();

    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $school_id);
    $stmt->execute();
    $userResult = $stmt->get_result();

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
      $loan_date = $_POST["loan_date"];
      $date_returned = $_POST["date_returned"];

      // Check if date_returned is empty, and set it to NULL if it is
      if (empty($date_returned)) {
        $date_returned = null;
      }

      // Validate return date
      if (!empty($date_returned) && $date_returned < $loan_date) {
        $error_message = "Return date must be after the loan date.";
      } else {
        // Update loan details in the database
        $updateLoanQuery = "UPDATE loan SET loan_date = ?, date_returned = ? WHERE loan_id = ?";
        $stmt = $conn->prepare($updateLoanQuery);
        $stmt->bind_param("ssi", $loan_date, $date_returned, $loanID);
        $stmt->execute();

        // Redirect to the loan overview page
        header("Location: manage_loans.php");
        exit;
      }
    }
  } else {
    echo "Loan ID not provided.";
    exit;
  }
  ?>

  <form method="post">
    <input type="hidden" name="loan_id" value="<?php echo $loan['loan_id']; ?>">

    <label for="book_title">Book Title:</label>
    <input type="text" name="book_title" id="book_title" value="<?php echo $loan['title']; ?>" readonly>

    <label for="username">Username:</label>
    <input type="text" name="username" id="username" value="<?php echo $loan['username']; ?>" readonly>


    <label for="loan_date">Loan Date:</label>
    <input type="date" name="loan_date" id="loan_date" value="<?php echo $loan['loan_date']; ?>" required>

    <label for="date_returned">Return Date:</label>
    <input type="date" name="date_returned" id="date_returned" value="<?php echo ($loan['date_returned'] === null) ? '0000-00-00' : $loan['date_returned']; ?>">

    <?php if (isset($error_message)): ?>
      <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <input type="submit" value="Save Changes">
  </form>

</body>
</html>
