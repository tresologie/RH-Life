<?php
require '../vendor/autoload.php'; // DOMPDF
include '../Includes/dbcon.php';
include '../Includes/session.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// ===== Infos classe =====
$query = "SELECT tblclass.className
FROM tblclassteacher
INNER JOIN tblclass ON tblclass.Id = tblclassteacher.classId
WHERE tblclassteacher.Id = '$_SESSION[userId]'";

$rs = $conn->query($query);
$rrw = $rs->fetch_assoc();

// ===== Date du jour =====
$todaysDate = date("d-m-Y");

// ===== Récupération des présences du jour =====
$dateTaken = date("Y-m-d");
$cnt = 1;

$ret = mysqli_query($conn,"SELECT tblattendance.Id,
        tblattendance.status,
        tblattendance.dateTimeTaken,
        tblclass.className,
        tblstudents.firstName,
        tblstudents.lastName,
        tblstudents.admissionNumber,
        tblstudents.poste
        FROM tblattendance
        INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
        INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
        WHERE tblattendance.dateTimeTaken = '$dateTaken' 
        AND tblattendance.classId = '$_SESSION[classId]' 
        ORDER BY tblstudents.firstName ASC");

// ===== Générer le HTML pour PDF =====
$html = '
<style>
body { font-family: Arial, sans-serif; font-size: 12px; }
.header { width: 100%; display: flex; justify-content: space-between; margin-bottom: 10px; }
.title { text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; margin-bottom: 10px; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #000; padding: 5px; text-align: left; }
th { background-color: #ccc; }
.present { background-color: #00FF00; }
.absent { background-color: #FF0000; color: #fff; }
</style>

<div class="header">
    <div>Life Campony</div>
    <div>Le '.$todaysDate.'</div>
</div>
<div class="header">
    <div>Usine: '.$rrw['className'].'</div>
</div>
<div class="title">Liste des présences</div>

<table>
<thead>
<tr>
<th>#</th>
<th>Nom & Prenom</th>
<th>Badge</th>
<th>Poste</th>
<th>Date</th>
<th>Status</th>
</tr>
</thead>
<tbody>
';

if(mysqli_num_rows($ret) > 0){
    while ($row = mysqli_fetch_array($ret)){
        $status = ($row['status'] == 1) ? "Présent" : "Absent";
        $class = ($row['status'] == 1) ? "present" : "absent";

        $html .= '<tr>
        <td>'.$cnt.'</td>
        <td>'.$row['firstName'].' '.$row['lastName'].'</td>
        <td>'.$row['admissionNumber'].'</td>
        <td>'.$row['poste'].'</td>
        <td>'.date("d/m/Y", strtotime($row['dateTimeTaken'])).'</td>
        <td class="'.$class.'">'.$status.'</td>
        </tr>';

        $cnt++;
    }
} else {
    $html .= '<tr><td colspan="6" style="text-align:center;">Aucune présence enregistrée pour aujourd\'hui</td></tr>';
}

$html .= '</tbody></table>';

// ===== Génération PDF =====
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("heures_supplementaires.pdf", ["Attachment" => true]);
exit;
?>