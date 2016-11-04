<?php
// Start the session
session_start();
?>



<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Check Out Finish</title>
<style>
table {
    width: auto;
    border-collapse: collapse;
    border: 3px solid black;
    margin: 50px;
    padding: 5px;
}

td, th {    
    border: 1px solid black;
    padding: 5px;
}

th {text-align: left;}

</style>
</head>
<body>
<p>Check Out Finish...</p>


<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library";

// Create connection
$con = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
} 


function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

$Book = $_SESSION["SessionBook"];
$Branch = $_SESSION["SessionBranch"];
$Card = test_input($_POST["Card_no"]);

$Date_out = $_POST["Date_out"];
$Due_date = date('Y-m-d', strtotime($Date_out.'+14 days'));


//need a card exits
$reject = "SELECT B.Card_no 
          FROM borrower B 
          WHERE B.Card_no = '$Card' ";
$exits = mysqli_query($con, $reject);

if(mysqli_num_rows($exits)>0){		
  	// no_of_copies equals loan book number, can't borrow
	$loanbook = "SELECT BL.Book_id, BL.Branch_id, BC.Book_id, BC.Branch_id, BC.No_of_copies, COUNT(*)
				FROM book_loans BL, book_copies BC
				WHERE BL.Book_id = BC.Book_id AND BL.Branch_id = BC.Branch_id AND BL.Book_id ='$Book' AND BL.Branch_id='$Branch' AND Date_in IS NULL
				HAVING COUNT(*) >= BC.No_of_copies";
	$equal = mysqli_query($con, $loanbook);

	if(mysqli_num_rows($equal)>0){			
		echo "There are no more book copies available at your select branch.";
	}
	else{
		//max 3 books
		$loannumber = "SELECT BL.Card_no, COUNT(*)
				FROM book_loans BL
				WHERE BL.Card_no = '$Card' AND (Date_in IS NULL)
				HAVING COUNT(*)>2";
		$max = mysqli_query($con, $loannumber);
		if(mysqli_num_rows($max)>0){      
			echo "Sorry, each card is permitted to borrow a maximum of 3 books, please return any book before checking out this time.";
		}
		else{
			$sql = "INSERT INTO book_loans (Book_id, Branch_id, Card_no, Date_out, Due_date) 
				VALUES ('$Book', '$Branch', '$Card', '$Date_out', '$Due_date')";		
			
			if(mysqli_query($con,$sql)){
	        
	        $new = mysqli_query($con, "SELECT *	FROM book_loans WHERE Card_no = '$Card' ORDER BY Date_out");

	        echo "<h2>Borrow books Information</h2>";
	        echo "<table>
	        <tr>
	        <th>Card_no</th>
	        <th>Book_id</th>
	        <th>Branch_id</th>
	        <th>Date_out</th>  
	        <th>Due_date</th>
	        <th>Date_in</th>
	        <th>Loan_id</th>
	        </tr>";        
	        while($row = mysqli_fetch_array($new)) {
	            echo "<tr>";
	            echo "<td>" . $row['Card_no'] . "</td>";
	            echo "<td>" . $row['Book_id'] . "</td>";
	            echo "<td>" . $row['Branch_id'] . "</td>";
	            echo "<td>" . $row['Date_out'] . "</td>";
	            echo "<td><mark>" . $row['Due_date'] . "</mark></td>";
	            echo "<td>" . $row['Date_in'] . "</td>";
	            echo "<td>" . $row['Loan_id'] . "</td>";
	            echo "</tr>";           
	        }
	         echo "</table>";
	         echo "Please remember return the book before Due_date!</p>"; 
			}else {
			    echo "Error borrowing";
			}


		}


	}




}
else{
 echo "Sorry, you need a card to process. If you don't have one card, create a new card <a href='library.html'>here</a>.";
}

mysqli_close($con);
?>

</body>
</html>