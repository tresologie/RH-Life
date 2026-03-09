<?php
error_reporting(E_ALL);
include '../Includes/dbcon.php';
include '../Includes/session.php';

date_default_timezone_set('Africa/Bujumbura');

// ===== Dates par défaut (7 derniers jours) =====
$fromDate = date('Y-m-d', strtotime('-6 days'));
$toDate   = date('Y-m-d');

if(isset($_POST['view']) || isset($_POST['save'])){
    $type = $_POST['type'] ?? '1';
    $fromDate = $_POST['fromDate'] ?? $fromDate;
    $toDate   = $_POST['toDate'] ?? $toDate;

    if($type == "2" && isset($_POST['singleDate'])){
        $fromDate = $toDate = $_POST['singleDate'];
    } elseif($type == "3" && isset($_POST['fromDate']) && isset($_POST['toDate'])){
        $fromDate = $_POST['fromDate'];
        $toDate   = $_POST['toDate'];
        $diff = (strtotime($toDate) - strtotime($fromDate)) / (60*60*24) + 1;
        if($diff > 7){
            $toDate = date('Y-m-d', strtotime($fromDate. ' + 6 days'));
        }
    }
}

// ===== Requête pour récupérer les données non encore enregistrées =====
$query = "SELECT 
    s.admissionNumber,
    s.firstName,
    s.lastName,
    s.identite,
    s.poste,
    c.className,
    sp.dateTimeTaken,
    FLOOR(sp.montant / 100) * 100 AS montant
FROM tblsupp sp
INNER JOIN tblclass c ON c.Id = sp.classId
INNER JOIN tblstudents s ON s.admissionNumber = sp.admissionNo
WHERE sp.dateTimeTaken BETWEEN '$fromDate' AND '$toDate'
AND NOT EXISTS (
    SELECT 1 FROM tblsupp_resume r
    WHERE r.admissionNumber = s.admissionNumber
    AND r.dateDebut = '$fromDate'
    AND r.dateFin = '$toDate'
)
ORDER BY c.className ASC, s.firstName ASC";

$rs = $conn->query($query);

// ===== Préparer le tableau et total =====
$dates = [];
$data  = [];
$totalGeneral = 0;

while($row = $rs->fetch_assoc()){
    $emp = $row['admissionNumber'];
    $date = $row['dateTimeTaken'];
    $montant = $row['montant'];

    $dates[$date] = $date;

    $data[$emp]['name']    = $row['firstName'].' '.$row['lastName'];
    $data[$emp]['identite']= $row['identite'];
    $data[$emp]['badge']   = $emp;
    $data[$emp]['usine']   = $row['className'];
    $data[$emp]['poste']   = $row['poste'];
    $data[$emp]['values'][$date] = $montant;
}

// Trier et limiter à 7 dates max
ksort($dates);
$dates = array_slice($dates, 0, 7, true);

// Calcul du total général
$totalGeneral = 0;
foreach($data as $info){
    $totalGeneral += array_sum($info['values']);
}

// ===== Enregistrement =====
$statusMsg = "";
if(isset($_POST['save'])){
    foreach($data as $emp => $info){
        $totalEmploye = array_sum($info['values']);
        $jours = count(array_filter($info['values'], fn($v)=>$v>0));

        // Vérifier si déjà enregistré
        $check = mysqli_query($conn,"SELECT id FROM tblsupp_resume
            WHERE admissionNumber='$emp'
            AND dateDebut='$fromDate'
            AND dateFin='$toDate'");

        if(mysqli_num_rows($check) == 0){
          $name = mysqli_real_escape_string($conn, $info['name']);
          $usine = mysqli_real_escape_string($conn, $info['usine']);
          $poste = mysqli_real_escape_string($conn, $info['poste']);
          
          mysqli_query($conn,"INSERT INTO tblsupp_resume(
              admissionNumber, nom, usine, poste, dateDebut, dateFin, nombreJours, totalPaye
          ) VALUES (
              '$emp',
              '$name',
              '$usine',
              '$poste',
              '$fromDate',
              '$toDate',
              '$jours',
              '$totalEmploye'
          )");
        }
    }
    $statusMsg = "<div class='alert alert-success mt-2'>Les données ont été enregistrées avec succès !</div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link href="img/logo/life.jpg" rel="icon">
<title>Heures supplémentaires</title>
<link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="css/ruang-admin.min.css" rel="stylesheet">

<script>
function typeDropDown(str){
    if(str==""){ document.getElementById("txtHint").innerHTML=""; return; }
    var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xmlhttp.onreadystatechange = function(){
        if(this.readyState==4 && this.status==200){
            document.getElementById("txtHint").innerHTML = this.responseText;
        }
    }
    xmlhttp.open("GET","ajaxCallTypes.php?tid="+str,true);
    xmlhttp.send();
}
</script>
</head>
<body id="page-top">
<div id="wrapper">
<?php include "Includes/sidebar.php"; ?>
<div id="content-wrapper" class="d-flex flex-column">
<div id="content">
<?php include "Includes/topbar.php"; ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
<h6 class="font-weight-bold text-primary" style="margin-left:30px">
<?php if($fromDate == $toDate): ?>
   Heures supplémentaires du <?= date("d/m/Y", strtotime($fromDate))?>
<?php else: ?>
   Heures supplémentaires du <?= date("d/m/Y", strtotime($fromDate)) ?> au <?= date("d/m/Y", strtotime($toDate)) ?>
<?php endif; ?>
</h6>
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="downloadSuppl.php?from=<?=$fromDate?>&to=<?=$toDate?>">Exporter</a>(Excel)</li>
<li class="breadcrumb-item"><a href="printSuppl.php?from=<?=$fromDate?>&to=<?=$toDate?>">Imprimer</a>(PDF)</li>
</ol>
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="./">Accueil</a></li>
<li class="breadcrumb-item active">Heures suppl</li>
</ol>
</div>

<div class="container-fluid" id="container-wrapper">
<div class="row">
<div class="col-lg-12">
<div class="card mb-4">
<div class="card-body">
<form method="post">
<div class="form-group row mb-3">
<div class="col-xl-6">
<label>Un jour/7jours/De ...à...<span class="text-danger">*</span></label>
<select required name="type" onchange="typeDropDown(this.value)" class="form-control mb-3">
<option value="">--Choisir--</option>
<option value="2" <?= (isset($_POST['type']) && $_POST['type']=="2") ? 'selected' : '' ?>>Un jour</option>
<option value="1" <?= (isset($_POST['type']) && $_POST['type']=="1") ? 'selected' : '' ?>>Cette semaine</option>
<option value="3" <?= (isset($_POST['type']) && $_POST['type']=="3") ? 'selected' : '' ?>>De ...à...</option>
</select>
</div>
<div class="col-xl-4">
<h4>Somme</h4>
<h1 class="form-control font-weight-bold" style="color:#6777EF;height:40px;font-size:20px;">
<?= number_format($totalGeneral,0,',','') ?> Fbu
</h1>
</div>
</div>
<div id="txtHint"></div>

<!-- Champs cachés pour garder les dates lors du clic sur Valider -->
<input type="hidden" name="fromDate" value="<?=$fromDate?>">
<input type="hidden" name="toDate" value="<?=$toDate?>">

<button type="submit" name="view" class="btn btn-primary">Afficher</button>

<?php if(!empty($data)){ ?>
<div class="table-responsive p-3 mt-3">
<?=$statusMsg?>
<table class="table align-items-center table-flush table-hover">
<thead class="thead-light">
<tr>
<th>Nom & Prénom</th><th>Usine</th><th>Poste</th>
<?php foreach($dates as $date): ?>
<th><?= date("d/m", strtotime($date)) ?></th>
<?php endforeach; ?>
<th>Total</th>
</tr>
</thead>
<tbody>
<?php foreach($data as $emp=>$info):
$totalEmploye = array_sum($info['values']); ?>
<tr>
<td><b><?=$info['name']?></b><br><?=$info['identite']?></td>
<td><?=$info['usine']?></td>
<td><?=$info['poste']?></td>
<?php foreach($dates as $date):
$value = $info['values'][$date] ?? 0; ?>
<td><?= number_format($value,0,',','') ?></td>
<?php endforeach; ?>
<td style="font-weight:bold;"><?= number_format($totalEmploye,0,',','') ?> Fbu</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<button type="submit" name="save" class="btn btn-success mt-2">Valider</button>
</div>
<?php } else { ?>
<div class='alert alert-danger mt-2'>Aucune donnée à afficher ou déjà validée pour cette période.</div>
<?php } ?>

</form>
</div>
</div>
</div>
</div>
</div>
</div>

<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/ruang-admin.min.js"></script>
</body>
</html>