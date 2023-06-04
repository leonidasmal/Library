<?php
include("connect.php");
session_start();

// Retrieve the studprof_id of the user
$studprof_id = $_SESSION['studprof_id'];

// Query to fetch the borrowed books for the user
$query = "SELECT B.title AS book_title, L.loan_date, L.date_returned FROM Loan L INNER JOIN Book B ON L.Book_ID = B.Book_ID WHERE L.studprof_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studprof_id);
$stmt->execute();
$result = $stmt->get_result();

// Prepare the HTML for the borrowed books list
$html = '<h2>Borrowed Books</h2>';
$html .= '<table>';
$html .= '<tr><th>Book Title</th><th>Loan Date</th>
<th>Returned Date</th></tr>';

while ($row = $result->fetch_assoc()) {
  $html .= '<tr>';
  $html .= '<td>' . $row['book_title'] . '</td>';
  $html .= '<td>' . $row['loan_date'] . '</td>';
  $html .= '<td>' . $row['date_returned'] . '</td>';
  $html .= '</tr>';
}

$html .= '</table>';
$html .= '<form action="user_dashboard.php" method="get">';
$html .= '<input type="submit" value="Back">';
$html .= '</form>';
// Return the HTML response
echo $html;
?>
