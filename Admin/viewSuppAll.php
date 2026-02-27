
<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

$totalGeneral = 0;



if(isset($_POST['view'])){

  $type = $_POST['type'];

  if($type == "2"){
      $singleDate = $_POST['singleDate'];
      $fromDate = $singleDate;
      $toDate = $singleDate;

  } elseif($type == "3"){
      $fromDate = $_POST['fromDate'];
      $toDate = $_POST['toDate'];

      $diff = (strtotime($toDate) - strtotime($fromDate)) / (60*60*24) + 1;
      if($diff > 7){
          $toDate = date('Y-m-d', strtotime($fromDate. ' + 6 days'));
      }

  } else {
      $toDate = date('Y-m-d');
      $fromDate = date('Y-m-d', strtotime('-6 days'));
  }

  $queryTotal = "SELECT SUM(FLOOR(tblsupp.montant / 100) * 100) as total
                 FROM tblsupp
                 WHERE tblsupp.dateTimeTaken BETWEEN '$fromDate' AND '$toDate'";

  $resultTotal = $conn->query($queryTotal);
  $rowTotal = $resultTotal->fetch_assoc();
  $totalGeneral = $rowTotal['total'] ?? 0;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="img/logo/life.jpg" rel="icon">
  <title>Heures supplémentaires</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">

<script>
    function typeDropDown(str) {
    if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","ajaxCallTypes.php?tid="+str,true);
        xmlhttp.send();
    }
}
</script>

</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
      <?php include "Includes/sidebar.php";?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
       <?php include "Includes/topbar.php";?>
        <!-- Topbar -->
        
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Heures supplémentaires jusqu'à 7 jours</h1>

            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Exporter</a>(Exel)</li>
              <li class="breadcrumb-item"><a href="#">Imprimer</a>(PDF)</li>
              
            </ol>

            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Accueil</a></li>
              <li class="breadcrumb-item active" aria-current="page">Heures suppl</li>
            </ol>
          </div>
        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
         

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Un jour/7jours/De ...à...<span class="text-danger ml-2">*</span></label>
                          <select required name="type" onchange="typeDropDown(this.value)" class="form-control mb-3">
                          <option value="">--Choisir--</option>
                          <option value="2" >Un jour</option>
                          <option value="1" >7 jours</option>
                          <option value="3" >De ...à...</option>
                        </select>
                        </div>
                        <div class="col-xl-4">
                          <h4 class="form-control-label">Somme</h6>

                          <?php if(isset($_POST['view']) && $totalGeneral > 0){ ?>
                             <h1 class="form-control font-weight-bold" style="color:#6777EF;height:40px;font-size:20px;">
                                 <?php echo number_format($totalGeneral, 0, ',', ' ') . " Fbu"; ?>
                              </h1>
                         <?php } else { ?>
                             <h4 class="text-danger form-control font-weight-bold" style="height:40px;font-size:20px;"> 0 Fbu</h4><?php } 
                         ?>

                      </div>
                    </div>
    
                      <?php
                        echo"<div id='txtHint'></div>";
                      ?>

                    <button type="submit" name="view" class="btn btn-primary">Afficher</button>
                  </form>
                </div>
              </div>

              <!-- Input Group -->
                 <div class="row">
              <div class="col-lg-12">
              <div class="card mb-4">
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">

                  <?php

  // Requête SQL
  $query = "SELECT 
      tblstudents.admissionNumber,
      tblstudents.firstName,
      tblstudents.lastName,
      tblstudents.identite,
      tblstudents.poste,
      tblclass.className,
      tblsupp.dateTimeTaken,
      FLOOR(tblsupp.montant / 100) * 100 AS montant
      FROM tblsupp
      INNER JOIN tblclass ON tblclass.Id = tblsupp.classId
      INNER JOIN tblstudents ON tblstudents.admissionNumber = tblsupp.admissionNo
      WHERE tblsupp.dateTimeTaken BETWEEN '$fromDate' AND '$toDate'
      ORDER BY tblstudents.firstName ASC";

  $rs = $conn->query($query);

  // Préparer le tableau
  $dates = [];
  $data = [];
  $totalGeneral = 0;

  while ($row = $rs->fetch_assoc()) {

      $emp = $row['admissionNumber'];
      $date = $row['dateTimeTaken'];
      $montant = $row['montant'];
      $totalGeneral += $montant;

      // Stocker dates uniques
      $dates[$date] = $date;

      // Stocker données par employé
      $data[$emp]['name'] = $row['firstName'].' '.$row['lastName'];
      $data[$emp]['badge'] = $row['admissionNumber'];
      $data[$emp]['usine'] = $row['className'];
      $data[$emp]['poste'] = $row['poste'];

      $data[$emp]['values'][$date] = $montant;
  }

  // Trier et limiter dates à 7 max
  ksort($dates);
  $dates = array_slice($dates, 0, 7, true);

  // Affichage du tableau
  if(!empty($data)){

      echo "<thead class='thead-light'>";
      echo "<tr>";
      echo "<th>Nom & Prenom</th>";
      echo "<th>Badge</th>";
      echo "<th>Usine</th>";
      echo "<th>Poste</th>";

      foreach($dates as $date){
          echo "<th>".date("d/m", strtotime($date))."</th>";
      }

      echo "<th>TOTAL</th>"; // colonne Total
      echo "</tr></thead><tbody>";

      foreach($data as $emp => $info){

          $totalEmploye = 0;

          echo "<tr>";
          echo "<td style= 'width:400px;' >".$info['name']." ".$row['identite']."</td>";
          echo "<td>".$info['badge']."</td>";
          echo "<td>".$info['usine']."</td>";
          echo "<td>".$info['poste']."</td>";

          foreach($dates as $date){

              $value = isset($info['values'][$date]) ? $info['values'][$date] : 0;
              $totalEmploye += $value;

              echo "<td>".number_format($value, 0, ',', ' ')."</td>";
          }

          echo "<td style='font-weight:bold; width:140px;'>".number_format($totalEmploye, 0, ',', ' ')." Fbu</td>";

          echo "</tr>";
      }

      echo "</tbody>";

  } else {

      echo "<tr>
              <td colspan='".(count($dates)+5)."'>
                  <div class='alert alert-danger'>
                      Entrez la date valide SVP!
                  </div>
              </td>
            </tr>";
  }

                    
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            </div>
          </div>


        </div>
        <!---Container Fluid-->
      </div>
    </div>
  </div>

  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
   <!-- Page level plugins -->
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
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