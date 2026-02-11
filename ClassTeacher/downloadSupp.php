<?php
// Rien avant ça ! Pas d'espace ni echo
header("Content-type: application/vnd.ms-excel");
$filename = "Rapport_Heures";
$todaysDate = date("d-m-Y");
header("Content-Disposition: attachment; filename=".$filename." du ".$todaysDate.".xls");
header("Pragma: no-cache");
header("Expires: 0");

include '../Includes/dbcon.php';
include '../Includes/session.php';

// Date du jour
$todaysDate = date("d-m-Y");
echo "<h2 style='margin-left:30px; text-decoration: underline'>
Liste des heures supplementaires du ".$todaysDate."
</h2>";

// Tableau
echo "<table border='1'>
<thead>
<tr>
<th>#</th>
<th>Nom</th>
<th>Prenom</th>
<th>Badge</th>
<th>Poste</th>
<th>Usine</th>
<th>De</th>
<th>A</th>
<th>Heures</th>
<th>Montant</th>
<th>Signature</th>
</tr>
</thead>";

$dateTaken = date("Y-m-d");
$cnt = 1;			

// Initialiser les totaux
$totalHeures = 0;
$totalMontant = 0;

$ret = mysqli_query($conn,"SELECT tblsupp.Id, tblsupp.dateTimeTaken,
DATE_FORMAT(tblsupp.heureDebut, '%H:%i') AS heureDebut, 
DATE_FORMAT(tblsupp.heureFin, '%H:%i') AS heureFin,
tblsupp.heures, tblclass.className, FLOOR(tblsupp.montant / 100) * 100 AS montant,        
tblstudents.firstName, tblstudents.lastName, tblstudents.admissionNumber, tblstudents.poste
FROM tblsupp
INNER JOIN tblclass ON tblclass.Id = tblsupp.classId
INNER JOIN tblstudents ON tblstudents.admissionNumber = tblsupp.admissionNo
WHERE tblsupp.dateTimeTaken = '$dateTaken' 
AND tblsupp.classId = '".$_SESSION['classId']."'");

if(mysqli_num_rows($ret) > 0 )
{
    while ($row=mysqli_fetch_array($ret)) 
    { 
        echo "<tr>
        <td>".$cnt."</td>
        <td>".$row['firstName']."</td>
        <td>".$row['lastName']."</td>
        <td>".$row['admissionNumber']."</td>
        <td>".$row['poste']."</td>
        <td>".$row['className']."</td>
        <td>".$row['heureDebut']."</td>
        <td>".$row['heureFin']."</td>
        <td>".$row['heures']."</td>
        <td>".$row['montant']."</td>
        <td></td>
        </tr>"; 

        // Ajouter aux totaux

        $totalMontant += $row['montant'];

        $cnt++; // incrémenter après l'affichage
    }

    // Afficher la ligne total
    echo "<tr style='font-weight:bold; background-color:#f2f2f2;'>
        <td colspan='9' style='text-align:right;'>Total</td>
        <td>".number_format($totalMontant, 0, ',', ' ')."</td>
    </tr>";
}

echo "</table>";

?>
