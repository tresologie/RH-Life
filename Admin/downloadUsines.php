<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';


$query = "SELECT tblclass.className
FROM tblclassteacher
INNER JOIN tblclass ON tblclass.Id = tblclassteacher.classId
Where tblclassteacher.Id = '$_SESSION[userId]'";

$rs = $conn->query($query);
$num = $rs->num_rows;
$rrw = $rs->fetch_assoc();
?>
<h2 style="margin-left:30px;  text-decoration: underline">Liste de tous les usines et chefs de Life campony </h2>
        <table border="1">
        <thead>
            <tr>
            <th>#</th>
            <th>Nom</th>
            <th>Prenom</th>
            <th>Email</th>
            <th>Tel</th>
            <th>Usine</th>
            <th>Date</th>
            </tr>
        </thead>

<?php 
$filename="Liste des usines et chefs de Life campony";
$dateCreated = date("Y-m-d");

$cnt=1;			
$ret = mysqli_query($conn,"SELECT tblclassteacher.firstName,tblclassteacher.lastName,
tblclassteacher.emailAddress,tblclassteacher.phoneNo,tblclassteacher.dateCreated,tblclass.className
 FROM tblclassteacher
 INNER JOIN tblclass ON tblclass.Id = tblclassteacher.classId ");

if(mysqli_num_rows($ret) > 0 )
{
 while ($row=mysqli_fetch_array($ret)) 
        { 
echo '  
<tr>  
<td>'.$cnt.'</td> 
<td>'.$firstName= $row['firstName'].'</td> 
<td>'.$lastName= $row['lastName'].'</td> 
<td>'.$emailAddress= $row['emailAddress'].'</td> 
<td>'.$phoneNo= $row['phoneNo'].'</td>
<td>'.$className= $row['className'].'</td>  
<td>'.$dateCreated=$row['dateCreated'].'</td>	 					
</tr>  
';
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename.".xls");
header("Pragma: no-cache");
header("Expires: 0");
			$cnt++;
			
	}
}
?>
</table>