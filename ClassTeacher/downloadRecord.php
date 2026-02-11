<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';


?>
<h2 style="margin-left:30px;  text-decoration: underline">
Liste de presences du <?php echo $todaysDate = date("d-m-Y");?> </h2>
        <table border="1">
        <thead>
            <tr>
            <th>#</th>
            <th>Nom</th>
            <th>Prenom</th>
            <th>Badge</th>
            <th>Poste</th>
            <th>Usine</th>
            <th>Status</th>
            <th>Date</th>
            </tr>
        </thead>

<?php 
$filename="Liste de Présences";
$dateTaken = date("Y-m-d");

$cnt=1;			
$ret = mysqli_query($conn,"SELECT tblattendance.Id,tblattendance.status,tblattendance.dateTimeTaken,tblclass.className,
        tblstudents.firstName,tblstudents.lastName,tblstudents.admissionNumber,tblstudents.poste
        FROM tblattendance
        INNER JOIN tblclass ON tblclass.Id = tblattendance.classId

        INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
        where tblattendance.dateTimeTaken = '$dateTaken' and tblattendance.classId = '$_SESSION[classId]' ");

if(mysqli_num_rows($ret) > 0 )
{
while ($row=mysqli_fetch_array($ret)) 
{ 
    
    if($row['status'] == '1'){$status = "Present"; $colour="#00FF00";}else{$status = "Absent";$colour="#FF0000";}

echo '  
<tr>  
<td>'.$cnt.'</td> 
<td>'.$firstName= $row['firstName'].'</td> 
<td>'.$lastName= $row['lastName'].'</td> 
 
<td>'.$admissionNumber= $row['admissionNumber'].'</td> 
<td>'.$poste= $row['poste'].'</td> 
<td>'.$className= $row['className'].'</td> 
 

<td>'.$status=$status.'</td>	 	
<td>'.$dateTimeTaken=$row['dateTimeTaken'].'</td>	 					
</tr>  
';
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename." du ".$todaysDate.".xls");
header("Pragma: no-cache");
header("Expires: 0");
			$cnt++;
			}
	}
?>
</table>