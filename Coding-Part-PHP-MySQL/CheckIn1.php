<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Check In</title>
<style>
table {
    width: auto;
    border-collapse: collapse;
    border: 3px solid black;
    margin:20px;
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


$rbookID = test_input($_POST["rISBN10"]);
$card = test_input($_POST["Card_no"]);
global $returnQ;
	

	//use bookid and card number to return
	if($rbookID=="" && $card ==""){
		echo "You need input something to return the book!";
	}
	else{
		echo "<p>Please select the day to check in.</p>";
		if($card ==""){
			$returnQ = "SELECT BL.Book_id, BL.Branch_id, BL.Card_no, Date_out, Due_date, Date_in, BL.Loan_id, B.Fname, B.Lname
					FROM book_loans BL, borrower B
					WHERE (BL.Book_id LIKE '%$rbookID%') AND BL.Card_no = B.Card_no
					ORDER BY BL.Card_no, BL.Loan_id";
		}
		
		if($rbookID==""){
			$returnQ = "SELECT BL.Book_id, BL.Branch_id, BL.Card_no, Date_out, Due_date, Date_in, BL.Loan_id, B.Fname, B.Lname
					FROM book_loans BL, borrower B
					WHERE BL.Card_no = '$card' AND BL.Card_no = B.Card_no
					ORDER BY BL.Card_no, BL.Loan_id";
		}
		if( ($rbookID !="") && ($card !="")){
			$returnQ = "SELECT BL.Book_id, BL.Branch_id, BL.Card_no, Date_out, Due_date, Date_in, BL.Loan_id, B.Fname, B.Lname
					FROM book_loans BL, borrower B
					WHERE (BL.Book_id LIKE '%$rbookID%') AND BL.Card_no = B.Card_no AND (BL.Card_no = '$card')
					ORDER BY BL.Card_no, BL.Loan_id";
		}
		
		$result = mysqli_query($con,$returnQ);
		$rowcount = mysqli_num_rows($result);

		if($rowcount == 0){
    		echo "<p><b>Search Result: ".$rowcount." result</b></p>";
		}
		else{
			echo "<p><b>Search Result: ".$rowcount." results</b></p>";
			
			echo "<table>
					<tr>
					<th>Card_no</th>
					<th>Fname</th>
					<th>Lname</th>
					<th>Book_id</th>
					<th>Branch_id</th>			
					<th>Loan_id</th>
					<th>Date_out</th>
					<th>Due_date</th>
					<th>Date_in</th>
					<th>Return</th>
					</tr>";
		while($row = mysqli_fetch_array($result)) {
		    echo "<tr>";
		    echo "<td>" . $row['Card_no'] . "</td>";
		    echo "<td>" . $row['Fname'] . "</td>";
		    echo "<td>" . $row['Lname'] . "</td>";
		    echo "<td>" . $row['Book_id'] . "</td>";
		    echo "<td>" . $row['Branch_id'] . "</td>";
		    echo "<td>" . $row['Loan_id'] . "</td>";
		    echo "<td>" . $row['Date_out'] . "</td>";
		    echo "<td><mark>" . $row['Due_date'] . "</mark></td>";
		    echo "<td>" . $row['Date_in'] . "</td>";
		    
		    if($row['Date_in'] == NULL){
				echo '<td>
			        <form  action="CheckInFinish.php" method="post">
					 <input type="hidden" name="LoanID" value="'.$row['Loan_id'].'">
       				 <input type="hidden" name="Cardno" value="'.$row['Card_no'].'">
       				 Date:<input type="date" name="Date_in" required>					 
					<button type="submit"  >Return</button>
					</form>
			      </td>';
		    }else{
		    	echo "<td></td>";

		    }
		    

		    echo "</tr>";
		}

		echo "</table>";
		}




	}




//mysqli_free_result($result);

mysqli_close($con);

?>






</body>
</html>