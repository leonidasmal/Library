<!DOCTYPE html>
<html>
<head>

</head>
<body>
  <h1>Make a Reservation</h1>
  <?php
    include("connect.php");
    session_start();   

    // Check if book_id is present in the session data
    $book_id = isset($_SESSION['Book_ID']) ? $_SESSION['Book_ID'] : null;

    // Initialize variables
    $book = null;
    $bookResult = null;
    $userResult = null;

    // Retrieve book data from the database
    if ($book_id !== null) {
      $bookQuery = "SELECT title FROM Book WHERE Book.Book_ID = ?";
      $stmt = $conn->prepare($bookQuery);
      $stmt->bind_param("i", $book_id);
      $stmt->execute();
      $bookR = $stmt->get_result();
      $book = $bookR->fetch_assoc();
    }

    // Retrieve all book titles and usernames from the database
    $bookQuery = "SELECT Book.Book_ID, title FROM Book INNER JOIN School_Book ON Book.Book_ID = School_Book.Book_ID WHERE School_Book.School_ID = ?";
    $stmt = $conn->prepare($bookQuery);
    $stmt->bind_param("i", $_SESSION['School_ID']);
    $stmt->execute();
    $bookResult = $stmt->get_result();

    $userQuery = "SELECT username FROM Users WHERE User_ID = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $_SESSION['User_ID']);
    $stmt->execute();
    $userResult = $stmt->get_result();

    // Handle form submission
    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['submit'])) {
    
        $book_id = $_POST['Book_ID'];

        $stmt = $conn->prepare("SELECT sp.studprof_id FROM students_professors sp  WHERE sp.User_ID = ?");
        $stmt->bind_param("i", $_SESSION['User_ID']);
        $stmt->execute();
        $result = $stmt->get_result();
        $studprof_id = $result->fetch_assoc()['studprof_id'];

        // Get manager_id based on school_id
        $stmt = $conn->prepare("SELECT m.Manager_ID FROM School_Unit_Manager m  WHERE m.School_ID = ?");
        $stmt->bind_param("i", $_SESSION['School_ID']);
        $stmt->execute();
        $result = $stmt->get_result();
        $manager_id = $result->fetch_assoc()['Manager_ID'];

        // Start transaction
        $conn->begin_transaction();

        try {
          // Insert reservation
          $insertReservationQuery = "INSERT INTO Reservation (book_id, Manager_ID, studprof_id, reservation_date) VALUES (?, ?, ?, NOW())";
          $stmt = $conn->prepare($insertReservationQuery);
          $stmt->bind_param("iii", $book_id, $manager_id, $studprof_id);
          $stmt->execute();

          // Commit transaction
          $conn->commit();

          // Display success message
          echo "Reservation successfully created.";


        } catch (mysqli_sql_exception $exception) {
          // Rollback transaction
          $conn->rollback();
          echo "Error: " . $exception->getMessage();
        }
    } else {
        $book_id = isset($_SESSION['Book_ID']) ? $_SESSION['Book_ID'] : null;
    }
  ?>

<?php if (isset($_SESSION['Book_ID'])): ?>
    <!-- Confirmation view when book_id is provided -->
    <table>
      <tr>
        <th>Book Title</th>
        <th>Username</th>
      </tr>
      <tr>
        <td><?php echo $book['title']; ?></td>
        <td>
        <?php while ($row = mysqli_fetch_assoc($userResult)): ?>
              <?php echo $row['username']; ?>
            <?php endwhile; ?>
            </td>
      </tr>
    </table>
    <form method="post">
      <input type="hidden" name="Book_ID" value="<?php echo $book_id; ?>">
      <button type="submit" name="submit">Confirm Reservation</button>
    </form>

    <form action="user_view_books.php" method="get">
<input type="submit" value="Back">
</form>

<?php else: ?>
    <!-- Original view when book_id is not provided -->
    <form method="post">
      <table>
        <tr>
          <th>Book Title</th>
          <th>Username</th>
        </tr>
        <tr>
          <td>
            <select name="Book_ID">
              <option value="">Select Book Title</option>
              <?php while ($row = mysqli_fetch_assoc($bookResult)): ?>
                <option value="<?php echo $row['Book_ID']; ?>" <?php echo ($row['Book_ID'] == $book_id) ? 'selected' : ''; ?>><?php echo $row['title']; ?></option>
              <?php endwhile; ?>
            </select>
          </td>
          <td>
            <?php while ($row = mysqli_fetch_assoc($userResult)): ?>
              <?php echo $row['username']; ?>
            <?php endwhile; ?>
          </td>
        </tr>
      </table>
     <button type="submit" name="submit">Make a Reservation</button>
    </form>

    <form action="user_dashboard.php" method="get">
<input type="submit" value="Back">
</form>
<?php endif; ?>


</body>
</html>
