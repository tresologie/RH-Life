<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';

date_default_timezone_set('Africa/Bujumbura');

// Définir période du 28 au 28
$today = date('Y-m-d');

if(date('d') >= 28){
    $fromDate = date('Y-m-28');
    $toDate = date('Y-m-28', strtotime('+1 month'));
} else {
    $fromDate = date('Y-m-28', strtotime('-1 month'));
    $toDate = date('Y-m-28');
}

// Récupérer toutes les classes qui ont au moins un étudiant et leur nom
$queryClasses = mysqli_query($conn, "
    SELECT DISTINCT s.classId, c.className
    FROM tblstudents s
    INNER JOIN tblclass c ON s.classId = c.Id
    ORDER BY s.classId ASC
");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="img/logo/life.jpg" rel="icon">
  <title>Tableau de bord</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">
  <?php include "Includes/sidebar.php";?>
  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
      <?php include "Includes/topbar.php";?>

      <div class="d-sm-flex align-items-center justify-content-between mb-4" >
          <h1 class="h3 mb-0 text-gray-800">Statistiques de présences du <?php echo date("d-m-Y");?></h1>
          <ol class="breadcrumb">
              <li class="breadcrumb-item">
              <a href="downloadPresences.php?fromDate=<?php echo $fromDate; ?>
              &toDate=<?php echo $toDate; ?>"> Exporter</a>(Exel)</li>
              <li class="breadcrumb-item">
              <a href="printPresences.php?fromDate=<?php echo $fromDate; ?>
              &toDate=<?php echo $toDate; ?>">Imprimer</a>(PDF)</li>
              
            </ol>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="./">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tableau</li>
          </ol>
      </div>

      <div class="container-fluid" id="container-wrapper">

        <?php while ($class = mysqli_fetch_assoc($queryClasses)) { 
            $classId = $class['classId'];
            $className = $class['className'];

            // Total étudiants dans la classe
            $queryStudents = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tblstudents WHERE classId = '$classId'");
            $studentsRow = mysqli_fetch_assoc($queryStudents);
            $students = $studentsRow['total'];

            // Étudiants présents aujourd'hui
            $queryPresent = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tblattendance WHERE classId = '$classId' AND status = '1' AND DATE(dateTimeTaken) = CURDATE()");
            $presentRow = mysqli_fetch_assoc($queryPresent);
            $totAttendance = $presentRow['total'];

            // Absents
            $absent = $students - $totAttendance;

            // Abandons (>= 5 absences)
            $queryAbandon = mysqli_query($conn, "
                SELECT admissionNo 
                FROM tblattendance 
                WHERE classId = '$classId' AND status = '0'
                GROUP BY admissionNo 
                HAVING COUNT(*) >= 5
            ");
            $abandon = mysqli_num_rows($queryAbandon);
        ?>

        <div class="row mb-4">
          <!-- Tous les employés -->
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Tous les employés - <?php echo $className;?></div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $students;?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-users fa-2x" style="color: blue;"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Présents -->
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Présents - <?php echo $className;?></div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totAttendance;?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-calendar-check fa-2x text-success"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Absents -->
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Absents - <?php echo $className;?></div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $absent;?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-user-times fa-2x text-secondary"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Abandons -->
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Abandons - <?php echo $className;?></div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $abandon;?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-cut fa-2x text-danger"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php } // fin while ?>

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
</body>
</html>