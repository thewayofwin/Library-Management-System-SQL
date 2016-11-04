<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Fine finish Page</title>
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



$finishcard = $_POST["cardnumber"];
$finishpayfee = $_POST["fee"];


$sql = "SELECT BL.Loan_id AS Loan_id, Card_no, Fine_amt, Paid
		FROM fines F, book_loans BL
		WHERE Card_no = '$finishcard' AND BL.Loan_id= F.Loan_id AND  (Date_in IS NOT NULL) 
		AND Paid = 0";
$result = mysqli_query($con,$sql);
while($Fine = mysqli_fetch_array($result)){

	$finishpay = "UPDATE fines 
				  SET Paid = 1
				  WHERE Loan_id = '".$Fine['Loan_id']."'";
	$done =	mysqli_query($con,$finishpay);


}


$finishinfo = "SELECT BL.Loan_id AS Loan_id, Card_no, Paid
				FROM fines F, book_loans BL
				WHERE Card_no = '$finishcard' AND BL.Loan_id= F.Loan_id AND  (Date_in IS NOT NULL) 
				AND Paid = 1";


if(mysqli_query($con,$finishinfo)){

echo "Pay successfully!";
$info = "SELECT Card_no, Paid, sum(Fine_amt)
		FROM fines F, book_loans BL
		WHERE Card_no = '$finishcard' AND BL.Loan_id= F.Loan_id AND Paid = 1
		GROUP BY Card_no";
$r = mysqli_query($con,$info);
	 	echo "<table>
				<tr>
				<th>Card_no</th>				
				<th>Paid fee</th>
				<th>Paid</th>
				<th>Fine Fees Total(Include history) </th>
				</tr>";
	while($show = mysqli_fetch_array($r)){
 		echo "<tr>";
		echo "<td>" . $show['Card_no'] . "</td>";
		echo "<td>" . $finishpayfee . "</td>";
		if($show['Paid']==0){
			echo "<td>No</td>";
		}else{
			echo "<td>Yes</td>";
		}

		
		echo "<td>" . $show['sum(Fine_amt)'] . "</td>";
		echo "</tr>";
	}


}else{
	echo "Pay error.";
}

		
	

mysqli_close($con);
?>

</body>
</html>