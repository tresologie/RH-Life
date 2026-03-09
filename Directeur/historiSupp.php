<?php
error_reporting(E_ALL);
include '../Includes/dbcon.php';
include '../Includes/session.php';

date_default_timezone_set('Africa/Bujumbura');

// ===== Récupérer toutes les dates d'enregistrement disponibles =====
$datesResult = $conn->query("
    SELECT DISTINCT DATE(dateEnregistrement) AS dateOnly 
    FROM tblsupp_resume 
    ORDER BY dateOnly DESC
");

$availableDates = [];
while($row = $datesResult->fetch_assoc()){
    $availableDates[] = $row['dateOnly'];
}

// ===== Date sélectionnée par défaut (dernière date) =====
$selectedDate = $availableDates[0] ?? date('Y-m-d');

// ===== Si l'utilisateur a filtré =====
if(isset($_POST['filter']) && !empty($_POST['selectedDate'])){
    $selectedDate = $_POST['selectedDate'];
}

// ===== Sécuriser la variable pour la requête =====
$selectedDate = $conn->real_escape_string($selectedDate);

// ===== Définir la plage complète pour le TIMESTAMP =====
$startOfDay = $selectedDate . ' 00:00:00';
$endOfDay   = $selectedDate . ' 23:59:59';

// ===== Requête pour afficher les données de la date sélectionnée =====
$query = "
    SELECT id, admissionNumber, nom, usine, poste, dateDebut, dateFin, nombreJours, totalPaye, dateEnregistrement
    FROM tblsupp_resume
    WHERE dateEnregistrement BETWEEN '$startOfDay' AND '$endOfDay'
    ORDER BY nom ASC
";

$rs = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link href="img/logo/life.jpg" rel="icon">
<title>Historique Heures Supplémentaires</title>
<link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="css/ruang-admin.min.css" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">
<?php include "Includes/sidebar.php"; ?>
<div id="content-wrapper" class="d-flex flex-column">
<div id="content">
<?php include "Includes/topbar.php"; ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
<h6 class="font-weight-bold text-primary" style="margin-left:30px">
Historique des heures supplémentaires
</h6>
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="./">Accueil</a></li>
<li class="breadcrumb-item active">Heures enregistrées</li>
</ol>
</div>

<div class="container-fluid" id="container-wrapper">

<!-- Formulaire de sélection de date -->
<div class="row mb-3">
<div class="col-lg-6">
<div class="card mb-4">
<div class="card-body">
<form method="post" class="form-inline">
<div class="form-group mb-2 mr-2">
<label for="selectedDate" class="mr-2">Sélectionner la date :</label>
<select name="selectedDate" id="selectedDate" class="form-control" required>
<?php foreach($availableDates as $date): ?>
    <option value="<?= $date ?>" <?= $date == $selectedDate ? 'selected' : '' ?>>
        <?= date("d/m/Y", strtotime($date)) ?>
    </option>
<?php endforeach; ?>
</select>
</div>
<button type="submit" name="filter" class="btn btn-primary mb-2">Afficher</button>
</form>
</div>
</div>
</div>
</div>

<!-- Tableau des données -->
<div class="row">
<div class="col-lg-12">
<div class="card mb-4">
<div class="table-responsive p-3">
<table class="table align-items-center table-flush table-hover" id="dataTableHover">
<thead class="thead-light">
<tr>
<th>#</th>
<th>Nom & Prénom</th>
<th>Badge</th>
<th>Usine</th>
<th>Poste</th>
<th>Date début</th>
<th>Date fin</th>
<th>Jours travaillés</th>
<th>Total payé</th>
<th>Date enregistrement</th>
</tr>
</thead>
<tbody>
<?php
if($rs && $rs->num_rows > 0):
    $sn = 0;
    while($row = $rs->fetch_assoc()):
        $sn++;
?>
<tr>
<td><?= $sn ?></td>
<td><b><?= htmlspecialchars($row['nom']) ?></b></td>
<td><?= htmlspecialchars($row['admissionNumber']) ?></td>
<td><?= htmlspecialchars($row['usine']) ?></td>
<td><?= htmlspecialchars($row['poste']) ?></td>
<td><?= date("d/m/Y", strtotime($row['dateDebut'])) ?></td>
<td><?= date("d/m/Y", strtotime($row['dateFin'])) ?></td>
<td><?= $row['nombreJours'] ?></td>
<td><?= number_format($row['totalPaye'],0,',',' ') ?> Fbu</td>
<td><?= date("d/m/Y H:i", strtotime($row['dateEnregistrement'])) ?></td>
</tr>
<?php
    endwhile;
else:
?>
<tr>
<td colspan="10" class="text-center">
<div class='alert alert-warning'>Aucune donnée pour cette date.</div>
</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>

</div>
</div>
</div>

<a class="scroll-to-top rounded" href="#page-top">
<i class="fas fa-angle-up"></i>
</a>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/ruang-admin.min.js"></script>
<script>
$(document).ready(function () {
    $('#dataTableHover').DataTable({
        scrollX: true,
        autoWidth: false,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
        }
    });
});
</script>
</body>
</html>