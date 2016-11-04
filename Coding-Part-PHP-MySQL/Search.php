<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Search Book</title>
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
<script>
function myFunction(){
    alert("Sorry, there are no more book copies available at your selected branch.");
}

</script>
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


$bookID = test_input($_POST["ISBN10"]);
$title = test_input($_POST["Title"]);
$author = test_input($_POST["Author"]);
global $sql, $availableNumber;


if(($bookID == "") && ($title == "") && ($author == "")){
    echo "0 result.";
    echo "You need input something!";
    
}
else{
if(($title == "") && ($author == "")){
    $sql = "SELECT B.Book_id, Title, BA.Book_id, Author_name, BC.Book_id, BC.Branch_id, No_of_copies 
        FROM book B,  book_authors BA, book_copies BC 
        WHERE (B.Book_id LIKE '%$bookID%')
        AND (B.Book_id = BA.Book_id) AND (B.Book_id = BC.Book_id) AND (BA.Book_id = BC.Book_id)
        ORDER BY Title, BC.Branch_id";
}
if(($bookID == "") && ($title == "")){
    $sql = "SELECT B.Book_id, Title, BA.Book_id, Author_name, BC.Book_id, BC.Branch_id, No_of_copies 
        FROM book B,  book_authors BA, book_copies BC 
        WHERE (Author_name LIKE '%$author%')
        AND (B.Book_id = BA.Book_id) AND (B.Book_id = BC.Book_id) AND (BA.Book_id = BC.Book_id)
        ORDER BY Title, BC.Branch_id";
}
if(($bookID == "") && ($author == "")){
    $sql = "SELECT B.Book_id, Title, BA.Book_id, Author_name, BC.Book_id, BC.Branch_id, No_of_copies 
        FROM book B,  book_authors BA, book_copies BC 
        WHERE (Title LIKE '%$title%')
        AND (B.Book_id = BA.Book_id) AND (B.Book_id = BC.Book_id) AND (BA.Book_id = BC.Book_id)
        ORDER BY Title, BC.Branch_id";
}
if($author == ""){
    $sql = "SELECT B.Book_id, Title, BA.Book_id, Author_name, BC.Book_id, BC.Branch_id, No_of_copies 
        FROM book B,  book_authors BA, book_copies BC 
        WHERE (B.Book_id LIKE '%$bookID%') AND (Title LIKE '%$title%')
        AND (B.Book_id = BA.Book_id) AND (B.Book_id = BC.Book_id) AND (BA.Book_id = BC.Book_id)
        ORDER BY Title, BC.Branch_id";
}
if($title == ""){
     $sql = "SELECT B.Book_id, Title, BA.Book_id, Author_name, BC.Book_id, BC.Branch_id, No_of_copies 
        FROM book B,  book_authors BA, book_copies BC 
        WHERE (B.Book_id LIKE '%$bookID%') AND (Author_name LIKE '%$author%')
        AND (B.Book_id = BA.Book_id) AND (B.Book_id = BC.Book_id) AND (BA.Book_id = BC.Book_id)
        ORDER BY Title, BC.Branch_id";
}
if($bookID == ""){
    $sql = "SELECT B.Book_id, Title, BA.Book_id, Author_name, BC.Book_id, BC.Branch_id, No_of_copies 
        FROM book B,  book_authors BA, book_copies BC 
        WHERE (Title LIKE '%$title%') AND (Author_name LIKE '%$author%')
        AND (B.Book_id = BA.Book_id) AND (B.Book_id = BC.Book_id) AND (BA.Book_id = BC.Book_id)
        ORDER BY Title, BC.Branch_id";
}
if( ($bookID != "") && ($title != "") && ($author != "")){
    $sql = "SELECT B.Book_id, Title, BA.Book_id, Author_name, BC.Book_id, BC.Branch_id, No_of_copies 
            FROM book B,  book_authors BA, book_copies BC 
            WHERE (B.Book_id LIKE '%$bookID%') AND (Title LIKE '%$title%') AND (Author_name LIKE '%$author%')
            AND (B.Book_id = BA.Book_id) AND (B.Book_id = BC.Book_id) AND (BA.Book_id = BC.Book_id)
            ORDER BY Title, BC.Branch_id";
}

$result = mysqli_query($con,$sql);
$rowcount = mysqli_num_rows($result);


if($rowcount == 0){
    echo "<p><b>Search Result: ".$rowcount." result</b></p>";
}
else{
echo "<p><b>Search Result: ".$rowcount." results</b></p>";

echo "<table>
<tr>
<th>Book_id</th>
<th>Title</th>
<th>Author</th>
<th>Branch_id</th>
<th>Number_of_copies</th>
<th>Available</th>
<th>CheckOut</th>
</tr>";
while($row = mysqli_fetch_array($result)) {
    echo "<tr>";
    echo "<td>" . $row['Book_id'] . "</td>";
    echo "<td>" . $row['Title'] . "</td>";
    echo "<td>" . $row['Author_name'] . "</td>";
    echo "<td>" . $row['Branch_id'] . "</td>";
    echo "<td>" . $row['No_of_copies'] . "</td>";
    
        
    //book available 
    $bookavailable = "SELECT BL.Book_id, BL.Branch_id, BC.Book_id, BC.Branch_id, BC.No_of_copies, COUNT(*)
                FROM book_loans BL, book_copies BC
                WHERE BL.Book_id = BC.Book_id AND BL.Branch_id = BC.Branch_id AND BL.Book_id ='".$row['Book_id']."' AND BL.Branch_id='".$row['Branch_id']."' AND Date_in IS NULL
                HAVING COUNT(*) <= BC.No_of_copies";
    $available = mysqli_query($con, $bookavailable);   

    $copies = mysqli_query($con, "SELECT No_of_copies FROM book_copies BC WHERE BC.Book_id ='".$row['Book_id']."' AND BC.Branch_id = '".$row['Branch_id']."'");
    $copiesNumber = mysqli_fetch_array($copies);


    if(mysqli_num_rows($available)>0){
        $loanBookNumber = mysqli_fetch_array($available);
        $availableNumber = $copiesNumber['No_of_copies'] - $loanBookNumber['COUNT(*)'];
    }else{
        $availableNumber = $copiesNumber['No_of_copies'];
    }

    echo "<td>".$availableNumber."</td>";
    
    if($availableNumber>0){
    echo '<td>
        <form action="CheckOut.php" method="post">
        <input type="hidden" name="Book" value="'.$row['Book_id'].'">
        <input type="hidden" name="Branch" value="'.$row['Branch_id'].'">
        <button type="submit" value="Submit">CheckOut</button>
        </form>
        </td>';
    }else{
        echo "<td><button type='button' onclick='myFunction()'>Not Available</button></td>";
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