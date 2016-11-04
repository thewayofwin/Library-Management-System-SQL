<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>PAY Fine</title>
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


function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

$card = test_input($_POST["card"]);
$Date = date("Y-m-d");

	
	//fine or not
	$sql = "SELECT BL.Loan_id , Card_no
			FROM fines F, book_loans BL
			WHERE Card_no = '$card' AND BL.Loan_id= F.Loan_id AND Paid = 0";
	$result = mysqli_query($con,$sql);

	if(mysqli_num_rows($result)>0){

		//don't allow payment of a fine for books that are not yet returned
				$sql1 = "SELECT BL.Loan_id AS Loan_id, Card_no, Date_out, Due_date, Date_in, Fine_amt, Paid
						FROM fines F, book_loans BL
						WHERE Card_no = '$card' AND BL.Loan_id= F.Loan_id  AND (Date_in IS NULL) 
						AND Paid = 0
						ORDER BY BL.Loan_id";
				$result1 = mysqli_query($con,$sql1);
				if(mysqli_num_rows($result1)>0){
				   echo "<p><mark>You don't allow payment of a fine for books that are not yet returned.</mark></p>";

				   echo "<table>
					<tr>
					<th>Card_no</th>
					<th>Loan_id</th>
					<th>Date_out</th>
					<th>Due_date</th>
					<th>Date_in</th>
					<th>Fine_amt</th>
					<th>Paid</th>
					</tr>";
					while($FineinfoN = mysqli_fetch_array($result1)) {
				    echo "<tr>";
				    echo "<td>" . $FineinfoN['Card_no'] . "</td>";
				   
				    echo "<td>" . $FineinfoN['Loan_id'] . "</td>";
				    echo "<td>" . $FineinfoN['Date_out'] . "</td>";
				    echo "<td>" . $FineinfoN['Due_date'] . "</td>";
				    echo "<td>" . $FineinfoN['Date_in'] . "</td>";
				    echo "<td>" . $FineinfoN['Fine_amt'] . "</td>";
					if($FineinfoN['Paid']==0){
 					echo "<td>No</td>";
					}else{
					echo "<td>Yes</td>";
					}				    

				   
				    echo "</tr>";
				 	}
				    echo "<tr>";
				    echo "<td>SUM estimated</td>";
				    echo "<td></td>";
				    echo "<td></td>";
				    echo "<td></td>";
				    echo "<td></td>";
				    echo "<td></td>";
				    $sumN = mysqli_query($con,"SELECT Card_no, sum(Fine_amt)
				    					FROM fines F, book_loans BL
				    					WHERE Card_no = '$card' AND BL.Loan_id= F.Loan_id AND Paid = 0");
				    $FineN = mysqli_fetch_array($sumN);

				    echo "<td><mark>" . $FineN['sum(Fine_amt)'] . "</mark></td>";
				    echo "</tr>";
					echo "</table>";

				}
				else{

					$sql2 = "SELECT BL.Loan_id AS Loan_id, Card_no, Date_out, Due_date, Date_in, Fine_amt, Paid
						FROM fines F, book_loans BL
						WHERE Card_no = '$card' AND BL.Loan_id= F.Loan_id AND  (Date_in IS NOT NULL) 
						AND Paid = 0
						ORDER BY BL.Loan_id";
					$result2 = mysqli_query($con,$sql2);

					echo "<p><mark>You allow pay the fine.</mark></p>";
					echo "<table>
					<tr>
					<th>Card_no</th>
					<th>Loan_id</th>
					<th>Date_out</th>
					<th>Due_date</th>
					<th>Date_in</th>
					<th>Fine_amt</th>
					<th>Paid</th>
					</tr>";
					while($Fineinfo = mysqli_fetch_array($result2)) {
				    echo "<tr>";
				    echo "<td>" . $Fineinfo['Card_no'] . "</td>";				    
				    echo "<td>" . $Fineinfo['Loan_id'] . "</td>";
				    echo "<td>" . $Fineinfo['Date_out'] . "</td>";
				    echo "<td>" . $Fineinfo['Due_date'] . "</td>";
				    echo "<td>" . $Fineinfo['Date_in'] . "</td>";
				    echo "<td>" . $Fineinfo['Fine_amt'] . "</td>";
				    if($Fineinfo['Paid']==0){
				    	echo "<td>No</td>";
				    }else{
				    	echo "<td>Yes</td>";
				    }				    
				    echo "</tr>";
					}					
				    echo "<tr>";
				    echo "<td>SUM </td>";
				    echo "<td></td>";
				    echo "<td></td>";
				    echo "<td></td>";
				    echo "<td></td>";
				    echo "<td></td>";
				    $sum = mysqli_query($con,"SELECT Card_no, sum(Fine_amt)
				    					FROM fines F, book_loans BL
				    					WHERE Card_no = '$card' AND BL.Loan_id= F.Loan_id AND Paid = 0");
				    $Fine = mysqli_fetch_array($sum);

				    echo "<td><mark>" . $Fine['sum(Fine_amt)'] . "</mark></td>";
				    echo "</tr>";
					echo "</table>";


					echo '<form  action="payfinish.php" method="post">
						<input type="hidden" name="cardnumber" value="'.$card.'">
		       			<input type="hidden" name="fee" value="'.$Fine['sum(Fine_amt)'].'">		       									 
						<button type="submit" >Pay all fee</button>
						</form>';

				}


	}
	else
	{
		echo "<p><mark>You don't have fines.</mark></p>";

	}




				

	

mysqli_close($con);
?>

</body>
</html>