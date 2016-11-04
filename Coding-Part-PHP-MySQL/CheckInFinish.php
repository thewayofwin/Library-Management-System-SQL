<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Check In Finish</title>
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
<p>Check In Finish...</p>

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


$Loan = test_input($_POST["LoanID"]);
$card = test_input($_POST["Cardno"]);
//$DateIn = date("Y/m/d");
$DateIn = $_POST["Date_in"];

$sql = "UPDATE book_loans 
		SET Date_In ='$DateIn'
		WHERE Loan_id ='$Loan' AND Card_no = '$card' AND Date_in IS NULL";	

			if(mysqli_query($con,$sql)){
	        
	        $new = mysqli_query($con, "SELECT *	FROM book_loans 
	        						WHERE Loan_id ='$Loan' AND Card_no = '$card' 
	        						ORDER BY Due_date");

	        echo "<h2>Return books Information</h2>";
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
	            echo "<td>" . $row['Due_date'] . "</td>";
	            echo "<td><mark>" . $row['Date_in'] . "</mark></td>";
	            echo "<td>" . $row['Loan_id'] . "</td>";
	            echo "</tr>";           
	        }
	         echo "</table>";
	         echo "Return seccessfully!</p>"; 
			}else {
			    echo "Error return";
			}

mysqli_close($con);
?>

</body>
</html>