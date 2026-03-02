<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';
require_once '../vendor/autoload.php'; // Chemin vers dompdf
use Dompdf\Dompdf;

// Récupérer les employés
$ret = mysqli_query($conn,"SELECT s.Id, s.dateCreated, c.className, s.salaire,
s.firstName, s.lastName, s.identite, s.admissionNumber, s.poste
FROM tblstudents s
INNER JOIN tblclass c ON c.Id = s.classId
ORDER BY s.firstName ASC");

// Construire le HTML avec styles CSS pour PDF
$html = '<!DOCTYPE html>
<html>
<head>
<style>
body { font-family: Arial, sans-serif; font-size: 12px; }
.header { width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.title { text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #000; padding: 5px; text-align: left; }
th { background-color: #ccc; }
tr:nth-child(even) { background-color: #f2f2f2; }
</style>
</head>
<body>';

// En-tête : date à droite
$html .= '<div class="header">';
$html .= '<div> Life campony </div>'; // vide à gauche
$html .= '<div>Le '.date("d-m-Y").'</div>'; // date à droite
$html .= '<div class="title">Liste de tous les employés de Life Company</div>';
$html .= '</div>';

// Table
$html .= '<table>';
$html .= '<tr>
<th>#</th><th>Nom & Prénom</th><th>Identité</th><th>Badge</th><th>Usine</th>
<th>Poste</th><th>Salaire</th><th>Date</th>
</tr>';

$cnt = 1;
while($row = mysqli_fetch_assoc($ret)){
    $html .= '<tr>
    <td>'.$cnt.'</td>
    <td>'.$row['firstName'].' '.$row['lastName'].'</td>
    <td>'.$row['identite'].'</td>
    <td>'.$row['admissionNumber'].'</td>
    <td>'.$row['className'].'</td>
    <td>'.$row['poste'].'</td>
    <td>'.number_format($row['salaire'], 0, ',', ' ').' Fbu</td>
    <td>'.$row['dateCreated'].'</td>
    </tr>';
    $cnt++;
}

$html .= '</table>';
$html .= '</body></html>';

// Détecter la largeur totale pour décider portrait ou paysage
$largeurTotal = 10 + 35 + 20 + 20 + 22 + 23 + 18 + 23; // largeur des colonnes en mm
$orientation = ($largeurTotal > 180) ? 'landscape' : 'portrait'; // A4 portrait = 210mm largeur

// Générer PDF
$dompdf = new Dompdf();
$dompdf->setPaper('A4', $orientation);
$dompdf->loadHtml($html);
$dompdf->render();

// Télécharger PDF
$dompdf->stream("Liste_employes.pdf", ["Attachment" => true]);