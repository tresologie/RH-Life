<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

date_default_timezone_set('Africa/Bujumbura');

$todaysDate = date("d-m-Y");
$dateSQL    = date("Y-m-d");


//--------------------EDIT------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {

  $Id = $_GET['Id'];

  // Récupérer les données existantes pour pré-remplir le formulaire
  $query = mysqli_query($conn, "SELECT * FROM tbladmin WHERE Id ='$Id'");
  $row = mysqli_fetch_array($query);

  if (isset($_POST['update'])) {

      $firstName = trim($_POST['firstName']);
      $lastName  = trim($_POST['lastName']);
      $emailAddress = trim($_POST['emailAddress']);
      $password = trim($_POST['password']);
      $cpassword = trim($_POST['cpassword']);

      // Flag pour savoir si on peut mettre à jour
      $canUpdate = true;

      // Vérification du mot de passe seulement si rempli
      if (!empty($password)) {
          if ($password !== $cpassword) {
              $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>
                  Les mots de passe ne correspondent pas !
              </div>";
              $canUpdate = false; // empêche la mise à jour
          } else {
              $md5Password = md5($password);
          }
      }

      // Effectuer la mise à jour uniquement si $canUpdate = true
      if ($canUpdate) {
          if (!empty($password)) {
              $query = mysqli_query($conn, "UPDATE tbladmin SET 
                  firstName='$firstName',
                  lastName='$lastName',
                  emailAddress='$emailAddress',
                  password='$md5Password'
                  WHERE Id='$Id'");
          } else {
              $query = mysqli_query($conn, "UPDATE tbladmin SET 
                  firstName='$firstName',
                  lastName='$lastName',
                  emailAddress='$emailAddress',
                  WHERE Id='$Id'");
          }

          if ($query) {
              echo "<script type = \"text/javascript\">
          window.location = (\"index.php\")
          </script>";
          } else {
              $statusMsg = "<div class='alert alert-danger'>
                  Erreur lors de la mise à jour !
              </div>";
          }
      }
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="img/logo/life.jpg" rel="icon">
  <title>Profile</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

<?php include "Includes/sidebar.php";?>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<?php include "Includes/topbar.php";?>

<div class="d-sm-flex align-items-center justify-content-between mb-0">
<h6 class="font-weight-bold text-primary" style="margin-left:30px">
Directeur Général - Le <?php echo date("d-m-Y"); ?>
</h6>

<ol class="breadcrumb">
  <li class="breadcrumb-item"><a href="./">Accueil</a></li>
  <li class="breadcrumb-item active" aria-current="page">Tableau</li>
</ol>
</div>
<div id="content-wrapper" class="d-flex flex-column">

<div class="row justify-content-center">
      <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form">
                <h6 class="font-weight-bold text-primary" align ="center" ><b> Modifier le profile</b> </h6>
                  <div class="text-center">
                  <div class="card mb-4">
                <div class="card-body">
                  <form method="post">
                   <div class="form-group row mb-3">
                        <div class="col-xl-4">
                        <label class="form-control-label">Nom<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" required name="firstName" value="<?php echo $row['firstName'];?>" id="exampleInputFirstName">
                        </div>
                        <div class="col-xl-4">
                        <label class="form-control-label">Prénom<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" required name="lastName" value="<?php echo $row['lastName'];?>" id="exampleInputFirstName" >
                        </div>
                        <div class="col-xl-4">
                        <label class="form-control-label">Email<span class="text-danger ml-2">*</span></label>
                        <input type="email" class="form-control" required name="emailAddress" value="<?php echo $row['emailAddress'];?>" id="exampleInputFirstName" >
                        </div>
                    </div>
                     <div class="form-group row mb-3">
                        
                        <div class="col-xl-4">
                        <label class="form-control-label">Directeur<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" name="poste" value="<?php echo $row['poste'];?>" id="exampleInputFirstName" >
                        </div>
                        <div class="col-xl-4">
                        <label class="form-control-label">Mot de passe<span class="text-danger ml-2">*</span></label>
                        <input type="password" class="form-control" required name="password"  id="exampleInputFirstName" >
                        </div>
                        <div class="col-xl-4">
                        <label class="form-control-label">Comfirmer le mot de passe<span class="text-danger ml-2">*</span></label>
                        <input type="password" class="form-control" name="cpassword"  id="exampleInputFirstName" >
                        </div>
                    </div>
                    <button type="submit" name="update" class="btn btn-warning">Modifier</button>
                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  </div>
                
                  <div class="text-center">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Login Content -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
</body>
</html>