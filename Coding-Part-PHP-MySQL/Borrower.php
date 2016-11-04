<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Borrower Information</title>
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
p {
  color: red;
}
</style>
</head>
<body>
<h1>Borrower Information is here...</h1>

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

$Fname = test_input($_POST["Fname"]);
$Lname = test_input($_POST["Lname"]);
$Address = test_input($_POST["Address"]);
$Phone = test_input($_POST["Phone"]);
global $sql;

//check if borrower info exist
$reject = "SELECT Fname, Lname, B.Address 
          FROM borrower B 
          WHERE Fname = '$Fname' AND Lname = '$Lname' AND Address = '$Address'";
 $exist = mysqli_query($con, $reject);
if( mysqli_num_rows($exist)>0){
    
    echo "<p>Sorry, the borrower info already exist. Please try again.</p>";
    echo "<script>          
              alert('Sorry, the borrower info already exist.');
          </script>";
  }
else{
      if($Phone == ""){
          $sql = "INSERT INTO borrower (Fname, Lname, Address) VALUES ('$Fname', '$Lname', '$Address')";          
          }
      else{
        $sql = "INSERT INTO borrower (Fname, Lname, Address, Phone) 
                VALUES ('$Fname', '$Lname', '$Address', '$Phone')"; 
      }

  if(mysqli_query($con,$sql)){
        
        $new = mysqli_query($con, "SELECT Card_no, Fname, Lname, B.Address, Phone
                              FROM borrower B 
                              WHERE Fname = '$Fname' AND Lname = '$Lname' AND Address = '$Address'");

        echo "<h2>Your Information</h2>";
        echo "<table>
        <tr>
        <th>Card_no</th>
        <th>Fname</th>
        <th>Lname</th>
        <th>Address</th>  
        <th>Phone</th>
        </tr>";        
        while($row = mysqli_fetch_array($new)) {
            echo "<tr>";
            echo "<td>" . $row['Card_no'] . "</td>";
            echo "<td>" . $row['Fname'] . "</td>";
            echo "<td>" . $row['Lname'] . "</td>";
            echo "<td>" . $row['Address'] . "</td>";
            echo "<td>" . $row['Phone'] . "</td>";
            echo "</tr>";
            echo "</table>";
            echo "<p><b>Congrulations!<b>";
            echo "Please remember your Card Number is <mark>".$row['Card_no']."</mark> !</p>";
        }
        
  }else {
    echo "Error Insertion";
  }

}


mysqli_close($con);
?>

</body>
</html>