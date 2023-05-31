<!DOCTYPE html>
<html>
<head>
  <title>Loan Overview</title>
  <style>
.modal {
  display: none;
  align-items: center;
  justify-content: center;
  position: fixed;
  z-index: 9999;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
} h1 {
    text-align: center;
  }

.modal-content {
      position: relative;
      margin: auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%; /* Adjust the width as desired */
      max-width: 600px; /* Set a maximum width */
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
      border-radius: 5px;
      text-align: center; /* Center the content horizontally */
      background-color: #fff;
    }



    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    .clickable {
  cursor: pointer;
  color: black;
  text-decoration: underline;
}


    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }

   /* Adjustments to table layout */
   table {
    border-collapse: collapse;
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
  </style>
  <script>
function openEditRdateModal(loanID) {
  var modal = document.getElementById("editRDate");
  var modalContent = document.getElementById("editRdateModal");
  var iframe = document.getElementById("editRdateFrame");

  var editURL = "edit_RDate.php?loan_id=" + loanID;

  // Set the iframe source to the editURL
  iframe.src = editURL;

  // Reset the height of the modal content
  modalContent.style.height = "auto";

  // Display the modal
  modal.style.display = "block";

  // Adjust the modal height based on the content inside the iframe
  iframe.onload = function() {
    var iframeBody = iframe.contentDocument.body;
    var iframeHeight = iframeBody.scrollHeight + 40; // Add extra padding

    modalContent.style.height = iframeHeight + "px";
  };
}
    function closeEditRdateModal() {
      var modal = document.getElementById("editRDate");

      // Hide the modal
      modal.style.display = "none";

      // Reset the iframe source
      var iframe = document.getElementById("editRdateFrame");
      iframe.src = "";
    }
  </script>
</head>
<body>
  <h1>Loan Overview</h1>
  <?php
  include("connect.php");
  session_start();
 

    // Retrieve loan data from the database
    $loanQuery = "SELECT L.loan_id,L.loan_date, L.date_returned, u.username, b.title, sp.User_ID
    FROM loan  L
    INNER JOIN book b ON L.book_ID = b.book_ID
    INNER JOIN students_professors sp ON L.studprof_ID = sp.studprof_ID
    INNER JOIN users u ON sp.user_ID = u.user_ID";
    
    $loanResult = mysqli_query($conn, $loanQuery);

        // Check if loan_ID is present in the URL
    
    ?>
    <?php
if (isset($_GET['loan_id'])) {
    $loanID = $_GET['loan_id'];

    if (isset($_GET['delete'])) {
        // Delete the loan record from the database
        $deleteLoanQuery = "UPDATE loan SET date_returned = NULL WHERE loan_id = ?";
        $deleteLoanStmt = mysqli_prepare($conn, $deleteLoanQuery);
        mysqli_stmt_bind_param($deleteLoanStmt, 'i', $loanID);
        mysqli_stmt_execute($deleteLoanStmt);

        // Redirect to the same page without the loan_id parameter
        header("Location: manage_loans.php");
        exit;
    }
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
      <td><?php echo $row[  'title']; ?></td>
      <td><?php echo $row['username']; ?></td>
      <td><?php echo $row['loan_date']; ?></td>
      <td>
      <?php if ($row['date_returned'] === null): ?>
          <span class="clickable" onclick="openEditRdateModal(<?php echo $row['loan_id']; ?>)">No registered return date</span>
        <?php else: ?>
          <span class="clickable" onclick="openEditRdateModal(<?php echo $row['loan_id']; ?>)"><?php echo $row['date_returned']; ?></span>
          <a href="?loan_id=<?php echo $row['loan_id']; ?>&delete=1" class="clickable" onclick="return confirm('Are you sure you want to delete this Return date loan?')">Delete</a>

        <?php endif; ?>
   
      </td>
      
    </tr>
  <?php endwhile; ?>
</table>

  <!-- Edit Return Date Modal -->
  <div id="editRDate" class="modal">
    <div class="modal-content" id="editRdateModal">
    
      <span class="close" onclick="closeEditRdateModal()">&times;</span>
      <iframe id="editRdateFrame" frameborder="0"></iframe>
    </div>
  </div>
</body>
</html>
