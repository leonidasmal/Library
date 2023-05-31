<?php
include("connect.php");
session_start();

$submittedSuccessfully = false;

// Check if loan_ID is present in the URL
if (isset($_GET['loan_id'])) {
    $loanID = $_GET['loan_id'];
} else {
    echo "Loan ID not provided";
    exit;
}
    $loanDetailsQuery = "SELECT date_returned FROM loan WHERE loan_id = ?";
    $loanDetailsStmt = mysqli_prepare($conn, $loanDetailsQuery);
    mysqli_stmt_bind_param($loanDetailsStmt, 'i', $loanID);
    mysqli_stmt_execute($loanDetailsStmt);
    mysqli_stmt_bind_result($loanDetailsStmt, $date_returned);
    mysqli_stmt_fetch($loanDetailsStmt);
    mysqli_stmt_close($loanDetailsStmt);

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handle the form submission
        if (isset($_POST['submit'])) {
            // Get the values from the form
            $date_returned = $_POST['date'];

            // Update date_returned
            $updateDateQuery = "UPDATE loan SET date_returned = ? WHERE loan_id = ?";
            $updateDateStmt = mysqli_prepare($conn, $updateDateQuery);
            mysqli_stmt_bind_param($updateDateStmt, 'si', $date_returned, $loanID);
            mysqli_stmt_execute($updateDateStmt);

            // Display success message
            echo '<div class="success-message">';
            echo '<div class="emoji">✨</div>';
            echo 'Update Successful!';
            echo '<div class="emoji">✨</div>';
            echo '</div>';

            $submittedSuccessfully = true;
        }
    }

?>


<!DOCTYPE html>
<html>
<head>
    <title>Update Book Information</title>
    <style>
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
            overflow: auto; /* Enable scrolling within the modal if content overflows */
            background-color: #fff;
        }
        body {
            font-family: Arial, sans-serif;
        }
        form {
            width: 400px; /* Adjust the width of the form as desired */
            margin: 0 auto;
        }
        label {
            display: block;
            margin-top: 20px;
        }
        input[type="date"] {
            width: 100%; /* Make the date input element occupy the full width */
            padding: 10px;
            margin-top: 5px;
            font-size: 16px; /* Adjust the font size as desired */
        }
        input[type="submit"] {
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .success-message {
            color: green;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            margin-top: 20px;
        }
        .emoji {    
            font-size: 40px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php 
   if (!$submittedSuccessfully): ?>
    <h1>Update Book Information</h1>
    <form method="POST" action="">
        <label for="date">Return Date of the book:</label>
        <input type="date" name="date" id="date" value="<?php echo date('Y-m-d', strtotime($date_returned)); ?>" required>


        <input type="submit" name="submit" value="Update">
    </form>
<?php endif; ?>


</body>
</html>
