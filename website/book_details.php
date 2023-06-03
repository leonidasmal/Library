<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .bookview {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin: 10px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.1);
        }
        .bookview-flex1 {
            flex: 1;
            max-width: 300px; /* Adjust this value to the maximum size you want for your image */
            padding-right: 20px; /* Add right padding */
        }
        .bookview-flex2 {
            flex: 2;
            padding-left: 20px; /* Add left padding */
            border: 1px solid #dddddd; /* Add border */
            padding: 20px; /* Add padding */
            background-color: #f9f9f9; /* Add background color */
            border-radius: 5px; /* Add border radius */
        }
        img {
            width: 100%;
            max-width: 100%;
            border-radius: 5px;
        }
        table {
            width: 100%;
        }
        td:first-child {
            width: 150px; /* Make first column of a fixed width */
        }
        label {
            font-weight: bold;
            color: #333333;
        }
    </style>
</head>
<body>

<?php
include("connect.php");

if(isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

$stmt = $conn->prepare('SELECT * FROM Book WHERE Book_ID = ?');
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
  $book = $result->fetch_assoc();

  // Fetch authors for the book
  $stmt_authors = $conn->prepare('SELECT Author.author_fullname FROM Author JOIN Book_Author ON Author.Author_ID = Book_Author.Author_ID WHERE Book_Author.Book_ID = ?');
  $stmt_authors->bind_param("i", $book_id);
  $stmt_authors->execute();
  $result_authors = $stmt_authors->get_result();

  $authors = [];
  while($row = $result_authors->fetch_assoc()) {
    $authors[] = htmlspecialchars($row['author_fullname']);
  }

?>

<div class="bookview">
	<div class="bookview-flex1">
		<img src="<?= htmlspecialchars($book['image_URL']) ?>" onerror="this.src='images/default.png'">
		<p id="rankBook" title="Δείτε τις αξιολογήσεις"><a href="#listComments"></a></p>
	</div>
	<div class="bookview-flex2">
		<table class="noborder">
			<tr><td><label>ISBN (Barcode):</label></td><td> <?= htmlspecialchars($book['ISBN']) ?></td></tr>
			<tr><td><label>Τίτλος:</label></td><td><?= htmlspecialchars($book['title']) ?></td></tr>
			<tr><td><label><?= (count($authors) > 1) ? "Συγγραφείς:" : "Συγγραφέας:" ?></label></td><td><?= implode(', ', $authors) ?></td></tr>
			<tr><td><label>Εκδόσεις:</label></td><td><?= htmlspecialchars($book['publisher']) ?>, <?= htmlspecialchars($book['pg_numbers']) ?> σελ</td></tr>
			<!-- Remaining fields to be filled as per your HTML structure -->
		</table>
	</div>
</div>

<?php
  } else {
    echo "No book found with id $book_id";
  }

$stmt->close();
$stmt_authors->close();
$conn->close();

} else {
    echo "Please enter a book id!";
}
?>


</body>
</html>


