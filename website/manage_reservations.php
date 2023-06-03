<!DOCTYPE html>
<html>
<head>
  <title>Reservations Overview</title>
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
    function confirmDeleteReservation(reservationID) {
      var result = confirm("Are you sure you want to delete this reservation?");
      if (result) {
        document.getElementById("delete_reservation_" + reservationID).submit();
      }
    }
  </script>
</head>
<body>
  <h1>Reservations Overview</h1>
  <?php
  include("connect.php");
  session_start();

  // Check if the delete form is submitted
  if (isset($_POST['reservation_id'])) {
    $reservationID = $_POST['reservation_id'];

    // Prepare a delete statement
    $sql = "DELETE FROM Reservation WHERE reservation_id = ?";

    if ($stmt = $conn->prepare($sql)) {
      // Bind variables to the prepared statement as parameters
      $stmt->bind_param("i", $reservationID);

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {
        echo "Reservation was deleted successfully.";
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }
    }
    // Close statement
    $stmt->close();
  }

  if (isset($_SESSION['Manager_ID'])) {
    $ManagerID = $_SESSION['Manager_ID'];

    // Retrieve reservation data from the database
    $reservationQuery = "SELECT R.reservation_id,R.reservation_date, u.username, b.title
      FROM Reservation R
      INNER JOIN book b ON R.book_ID = b.book_ID
      INNER JOIN students_professors sp ON R.studprof_ID = sp.studprof_ID
      INNER JOIN Users u ON u.User_ID = sp.User_ID
      WHERE R.Manager_ID = ?";

    $stmt = $conn->prepare($reservationQuery);
    $stmt->bind_param("i", $ManagerID);
    $stmt->execute();
    $reservationResult = $stmt->get_result();
  }
  ?>
  
  <table>
    <tr>
      <th>Reservation ID</th>
      <th>Book Title</th>
      <th>Username</th>
      <th>Reservation date</th>
      <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($reservationResult)): ?>
      <tr>
        <td><?php echo $row['reservation_id']; ?></td>
        <td><?php echo $row['title']; ?></td>
        <td><?php echo $row['username']; ?></td>
        <td><?php echo $row['reservation_date']; ?></td>
        <td>
          <button class="delete-button" onclick="confirmDeleteReservation(<?php echo $row['reservation_id']; ?>)">Delete</button>
          <form id="delete_reservation_<?php echo $row['reservation_id']; ?>" method="post" style="display: none;">
            <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
          </form>
          <input type="button" value="Make it a Loan" onclick="window.open('make_loan.php?reservation_id=<?php echo $row['reservation_id']; ?>', '_blank', 'width=600,height=600');" />
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
