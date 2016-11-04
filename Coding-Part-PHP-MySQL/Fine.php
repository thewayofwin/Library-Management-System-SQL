<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Fine Page</title>
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


 $Date = date("Y-m-d");


	

		//late books have been returned
		$sql2 = "SELECT Card_no, Loan_id, Date_out, Due_date, Date_in, DATEDIFF(Date_in,Due_date) AS DIF
				FROM book_loans
				WHERE DATEDIFF(Date_in,Due_date)>0 AND Date_in IS NOT NULL";	
		$result2 = mysqli_query($con,$sql2);
		if(mysqli_num_rows($result2)>0){
			while($row = mysqli_fetch_array($result2)) {
				
				$fineid = $row['Loan_id'];
				$fines = ($row['DIF'])*0.25;			// fine $0.25/day
				

				$sql1 = "SELECT Loan_id
							FROM fines
							WHERE Loan_id = '$fineid'";
				$result1 = mysqli_query($con,$sql1);
				if(mysqli_num_rows($result1)>0){
					$nopaid = mysqli_query($con,"SELECT Loan_id
												FROM fines
												WHERE Loan_id = '$fineid' AND Paid = 0");
					if(mysqli_num_rows($nopaid)>0){
						$update = mysqli_query($con, "UPDATE fines 
													SET Fine_amt = '$fines'
													WHERE Loan_id = '$fineid'");
					}
				}
				else{

					$add = mysqli_query($con, "INSERT INTO fines(Loan_id, Fine_amt) 
											VALUES ('$fineid','$fines')");
				}



			}

		}

		//late books have NOT been returned, estimated fee

		$sql3 = "SELECT Card_no, Loan_id, Date_out, Due_date, Date_in, DATEDIFF('$Date',Due_date) AS DIF
				FROM book_loans
				WHERE DATEDIFF('$Date',Due_date)>0 AND Date_in IS NULL";	
		$result3 = mysqli_query($con,$sql3);
		if(mysqli_num_rows($result3)>0){
			while($rowE = mysqli_fetch_array($result3)) {
				
				$fineidE = $rowE['Loan_id'];
				$finesE = ($rowE['DIF'])*0.25;			// fine $0.25/day
				

				$sql1E = "SELECT Loan_id
							FROM fines
							WHERE Loan_id = '$fineidE'";
				$result1E = mysqli_query($con,$sql1E);
				if(mysqli_num_rows($result1E)>0){
					$nopaidE = mysqli_query($con,"SELECT Loan_id
												FROM fines
												WHERE Loan_id = '$fineidE' AND Paid = 0");
					if(mysqli_num_rows($nopaidE)>0){
						$updateE = mysqli_query($con, "UPDATE fines 
													SET Fine_amt = '$finesE'
													WHERE Loan_id = '$fineidE'");
					}
				}
				else{

					$addE = mysqli_query($con, "INSERT INTO fines(Loan_id, Fine_amt) 
											VALUES ('$fineidE','$finesE')");
				}

				
			}

		}



	// show every fine info no matter paid or not
	$show = mysqli_query($con,"SELECT Card_no, F.Loan_id, Date_out, Due_date, Date_in, Fine_amt, Paid
								FROM book_loans BL, fines F
								WHERE BL.Loan_id = F.Loan_id AND Fine_amt >0
								ORDER BY Card_no");

	if(mysqli_num_rows($show)>0){
		echo "<p><b>Fine Result: </b></p>";

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
		while($Fineinfo = mysqli_fetch_array($show)) {
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
		echo "</table>";


	}else{
		echo "Not available!";
	}

	

mysqli_close($con);
?>

</body>
</html>