<?php
include '../Includes/dbcon.php';
require_once '../vendor/autoload.php'; // si installé via composer

date_default_timezone_set('Africa/Bujumbura');

use Dompdf\Dompdf;
use Dompdf\Options;



if(isset($_GET['fromDate']) && isset($_GET['toDate'])){
    $fromDate = $_GET['fromDate'];
    $toDate   = $_GET['toDate'];
} else {
    die("Periode invalide !");
}

$query = "SELECT 
    tblstudents.admissionNumber,
    tblstudents.firstName,
    tblstudents.lastName,
    tblstudents.poste,
    tblclass.className,
    DATE(tblattendance.dateTimeTaken) as dateTaken,
    tblattendance.status
FROM tblattendance
INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
WHERE DATE(tblattendance.dateTimeTaken) 
BETWEEN '$fromDate' AND '$toDate'
ORDER BY tblclass.className, tblstudents.firstName ASC";

$rs = $conn->query($query);

$dates = [];
$data  = [];

while ($row = $rs->fetch_assoc()) {

    $emp   = $row['admissionNumber'];
    $date  = $row['dateTaken'];
    $status= $row['status'];

    $dates[$date] = $date;

    $data[$emp]['name']  = $row['firstName'].' '.$row['lastName'];
    $data[$emp]['badge'] = $row['admissionNumber'];
    $data[$emp]['usine'] = $row['className'];
    $data[$emp]['poste'] = $row['poste'];

    $data[$emp]['values'][$date] = ($status == 1) ? 'P' : 'A';
}

ksort($dates);

// ===== HTML POUR PDF =====
$html = '
<style>
body { font-family: Arial, sans-serif; font-size: 12px; }
.header { width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.title { text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #000; padding: 3px; text-align: left; }
th { background-color: #ccc; }
tr:nth-child(even) { background-color: #f2f2f2; }
</style>
<div class="header">
<div> Life campony </div>
<div>Le '.date("d-m-Y").'</div>
<div class="title">Liste de Presences du '.$fromDate.' au '.$toDate.'</div>
</div>

<table>
<tr>
<th>#</th>
<th>Nom & Prenom</th>
<th>Badge</th>
<th>Usine</th>
<th>Poste</th>';

foreach($dates as $date){
    $html .= '<th>'.date("d/m", strtotime($date)).'</th>';
}

$html .= '<th>TOTAL</th></tr>';

$cnt = 1;

foreach($data as $emp => $info){

    $totalP = 0;

    $html .= '<tr>';
    $html .= '<td>'.$cnt.'</td>';
    $html .= '<td>'.$info['name'].'</td>';
    $html .= '<td>'.$info['badge'].'</td>';
    $html .= '<td>'.$info['usine'].'</td>';
    $html .= '<td>'.$info['poste'].'</td>';

    foreach($dates as $date){

        $value = isset($info['values'][$date]) ? $info['values'][$date] : '';

        if($value == 'P'){
            $totalP++;
        }

        $html .= '<td>'.$value.'</td>';
    }

    $html .= '<td><b>'.$totalP.'</b></td>';
    $html .= '</tr>';
    $cnt++;
}

$html .= '</table>';

// ===== GENERATION PDF =====
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // paysage
$dompdf->render();
$dompdf->stream("Liste de presence.pdf", array("Attachment" => true));
exit;
?>