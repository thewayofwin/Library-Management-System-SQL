<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Check Out</title>
</head>
<body>
<p>Please enter your Card Number and select a day to borrow.</p>

<?php
function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}


$_SESSION["SessionBook"] = test_input($_POST["Book"]);
$_SESSION["SessionBranch"]  = test_input($_POST["Branch"]);

?>


<form id="CheckOut" action="CheckOutFinish.php" method="post">
 Card Number: <input type="text" name="Card_no"  value="" required><br>
 Date:<input type="date" name="Date_out" required>
<button type="submit" form="CheckOut" value="Submit">Submit</button>
</form>

<p>Card number is required. If you don't have one card, create a new card <a href="library.html">here</a>.</p>


</body>
</html>