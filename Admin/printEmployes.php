<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';
require_once '../vendor/autoload.php'; // Chemin vers Dompdf
use Dompdf\Dompdf;

date_default_timezone_set('Africa/Bujumbura');

// Récupérer tous les chefs et leurs usines
$ret = mysqli_query($conn,"SELECT t.Id, t.dateCreated, c.className, 
    t.firstName, t.lastName, t.emailAddress, t.phoneNo
    FROM tblclassteacher t
    INNER JOIN tblclass c ON c.Id = t.classId
    ORDER BY c.className, t.firstName ASC");

// Construire le HTML avec styles CSS pour PDF
$html = '<!DOCTYPE html>
<html>
<head>
<style>
body { font-family: Arial, sans-serif; font-size: 12px; }
.header { width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.logo { width: 100px; }
.title { text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; margin-top: 10px; }
table { border-collapse: collapse; width: 100%; margin-top: 10px; }
th, td { border: 1px solid #000; padding: 5px; text-align: left; }
th { background-color: #ccc; }
tr:nth-child(even) { background-color: #f2f2f2; }
</style>
</head>
<body>';

// En-tête : logo et date
$html .= '<div class="header">';
$html .= '<div><img src="../img/logo/life.jpg" class="logo"></div>'; // Logo à gauche
$html .= '<div>Le '.date("d-m-Y").'</div>';
$html .= '</div>';

// Titre
$html .= '<div class="title">Liste de tous les chefs et usines de Life Company</div>';

// Table
$html .= '<table>
<tr>
<th>#</th>
<th>Nom & Prénom</th>
<th>Email</th>
<th>Téléphone</th>
<th>Usine</th>
<th>Date Création</th>
</tr>';

$cnt = 1;
while($row = mysqli_fetch_assoc($ret)){
    $html .= '<tr>
    <td>'.$cnt.'</td>
    <td>'.$row['firstName'].' '.$row['lastName'].'</td>
    <td>'.$row['emailAddress'].'</td>
    <td>'.$row['phoneNo'].'</td>
    <td>'.$row['className'].'</td>
    <td>'.$row['dateCreated'].'</td>
    </tr>';
    $cnt++;
}

$html .= '</table>';
$html .= '</body></html>';

// Orientation paysage pour mieux voir la table
$orientation = 'landscape';

// Générer PDF
$dompdf = new Dompdf();
$dompdf->setPaper('A4', $orientation);
$dompdf->loadHtml($html);
$dompdf->render();

// Télécharger PDF
$dompdf->stream("Liste_chefs_usines.pdf", ["Attachment" => true]);
?>