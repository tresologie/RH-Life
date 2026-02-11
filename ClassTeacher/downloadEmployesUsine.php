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
<h2 style="margin-left:30px;  text-decoration: underline">Liste de tous les employes de <b><?php echo $rrw['className'];?></b></h2>
        <table border="1">
        <thead>
            <tr>
            <th>#</th>
            <th>Nom</th>
            <th>Prenom</th>
            <th>Identite</th>
            <th>Badge</th>
            <th>Poste</th>
            <th>Date</th>
            </tr>
        </thead>

<?php 
$filename="Liste de tous les employes de l'usine";
$dateCreated = date("Y-m-d");

$cnt=1;			
$ret = mysqli_query($conn,"SELECT tblstudents.Id,tblstudents.dateCreated,
tblstudents.firstName,tblstudents.lastName,tblstudents.identite,tblstudents.admissionNumber,tblstudents.poste
 FROM tblstudents 
 INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
 where tblstudents.classId = '$_SESSION[classId]'");

if(mysqli_num_rows($ret) > 0 )
{
 while ($row=mysqli_fetch_array($ret)) 
        { 
echo '  
<tr>  
<td>'.$cnt.'</td> 
<td>'.$firstName= $row['firstName'].'</td> 
<td>'.$lastName= $row['lastName'].'</td> 
<td>'.$identite= $row['identite'].'</td> 
<td>'.$admissionNumber= $row['admissionNumber'].'</td> 
<td>'.$poste=$row['poste'].'</td>		 
<td>'.$dateCreated=$row['dateCreated'].'</td>	 					
</tr>  
';
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename.".xls");
header("Pragma: no-cache");
header("Expires: 0");
			$cnt++;
			
	}
}
?>
</table>