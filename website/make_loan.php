<!DOCTYPE html>
<html>
<head>
 <style>
    .back-button {
      position: fixed;
      bottom: 20px;
      right: 20px;
      padding: 10px 20px;
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <h1>Make a Loan</h1>
  <?php
    include("connect.php");
    session_start();   

    // Check if reservation_id is present in the URL
    $reservation_id = isset($_GET['reservation_id']) ? $_GET['reservation_id'] : null;
    
    // Initialize variables
    $reservation = null;
    $bookResult = null;
    $userResult = null;

    // Retrieve reservation data from the database
    if ($reservation_id !== null) {
      $reservationQuery = "SELECT R.reservation_date, u.username, b.title
        FROM Reservation R
        INNER JOIN book b ON R.book_ID = b.book_ID
        INNER JOIN students_professors sp ON R.studprof_ID = sp.studprof_ID
        INNER JOIN Users u ON u.User_ID = sp.User_ID
        WHERE R.reservation_id = ?";

      $stmt = $conn->prepare($reservationQuery);
      $stmt->bind_param("i", $reservation_id);
      $stmt->execute();
      $reservationResult = $stmt->get_result();
      $reservation = $reservationResult->fetch_assoc();
    }

    // Retrieve all book titles and usernames from the database
    $bookQuery = "SELECT b.title
                  FROM Book b
                  INNER JOIN School_Book sb ON b.book_ID = sb.Book_ID
                  WHERE sb.School_ID = ?";

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
      $book_title = $_POST["book_title"];
      $username = $_POST["username"];
      $reservation_date = $_POST["reservation_date"];

      // Get studprof_id based on username
      $stmt = $conn->prepare("SELECT sp.studprof_id FROM students_professors sp INNER JOIN Users u ON sp.User_ID = u.User_ID WHERE u.username = ?");
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $result = $stmt->get_result();
      $studprof_id = $result->fetch_assoc()['studprof_id'];

      // Get book_id based on book title
      $stmt = $conn->prepare("SELECT b.book_id FROM Book b WHERE b.title = ?");
      $stmt->bind_param("s", $book_title);
      $stmt->execute();
      $result = $stmt->get_result();
      $book_id = $result->fetch_assoc()['book_id'];

      // Get Manager_ID from session
      $manager_id = $_SESSION['Manager_ID'];

      // Start transaction
      $conn->begin_transaction();

      try {
        // Insert loan
        $insertLoanQuery = "INSERT INTO Loan (studprof_id, book_id, Manager_ID, loan_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertLoanQuery);
        $stmt->bind_param("iiis", $studprof_id, $book_id, $manager_id, $reservation_date);
        $stmt->execute();

        // Delete reservation if reservation_id is provided
        if ($reservation_id !== null) {
          $deleteReservationQuery = "DELETE FROM Reservation WHERE reservation_id = ?";
          $stmt = $conn->prepare($deleteReservationQuery);
          $stmt->bind_param("i", $reservation_id);
          $stmt->execute();
        }

        // Commit transaction
        $conn->commit();

        // Display success message
        echo "Loan successfully created.";
      } catch (mysqli_sql_exception $exception) {
        // Rollback transaction
        $conn->rollback();
        echo "Error: " . $exception->getMessage();
      }
    }
  ?>

  <?php if ($reservation_id !== null): ?>
    <!-- View when reservation_id is provided -->
    <table>
      <tr>
        <th>Book Title</th>
        <th>Username</th>
        <th>Reservation Date</th>
      </tr>
      <tr>
        <td><?php echo $reservation['title']; ?></td>
        <td><?php echo $reservation['username']; ?></td>
        <td><?php echo $reservation['reservation_date']; ?></td>
      </tr>
    </table>
    <form method="post">
      <input type="hidden" name="book_title" value="<?php echo $reservation['title']; ?>">
      <input type="hidden" name="username" value="<?php echo $reservation['username']; ?>">
      <input type="hidden" name="reservation_date" value="<?php echo $reservation['reservation_date']; ?>">
      <input type="submit" value="Confirm">
    </form>
  <?php else: ?>
    <!-- View when reservation_id is not provided -->
    <form method="post">
      <table>
        <tr>
          <th>Book Title</th>
          <th>Username</th>
          <th>Reservation Date</th>
        </tr>
        <tr>
          <td>
            <select name="book_title">
              <option value="">Select Book Title</option>
              <?php while ($row = mysqli_fetch_assoc($bookResult)): ?>
                <option value="<?php echo $row['title']; ?>"><?php echo $row['title']; ?></option>
              <?php endwhile; ?>
            </select>
          </td>
          <td>
            <select name="username">
              <option value="">Select Username</option>
              <?php while ($row = mysqli_fetch_assoc($userResult)): ?>
                <option value="<?php echo $row['username']; ?>"><?php echo $row['username']; ?></option>
              <?php endwhile; ?>
            </select>
          </td>
          <td>
            <input type="date" name="reservation_date" required>
          </td>
        </tr>
      </table>
      <input type="submit" value="Confirm">
    </form>
  <?php endif; ?>

  <button class="back-button" onclick="window.location.href = 'manage_loans.php'">Back</button>

</body>
</html>
