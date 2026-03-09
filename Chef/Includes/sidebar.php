 <ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center bg-gradient-primary justify-content-center" href="index.php">
        <div class="sidebar-brand-icon" >
          <img src="img/logo/life.jpg">
        </div>
        <div class="sidebar-brand-text mx-3">HRMS-Life</div>
      </a>
      <hr class="sidebar-divider my-0">
      <li class="nav-item active">
        <a class="nav-link" href="index.php">
          <i class="fas fa-fw fa-tachometer-alt" style="color: blue;"></i>
          <span>Tableau de bord</span></a>
      </li> 
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Appel
      </div>
      </li>
       <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrap2"
          aria-expanded="true" aria-controls="collapseBootstrap2">
          <i class="fa fa-calendar-alt" style="color: blue;"></i>
          <span>Gérer les employés</span>
        </a>
        <div id="collapseBootstrap2" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Gérer les employés</h6>
            <a class="collapse-item" href="takeAttendance.php">Faire l'appel</a>
            <a class="collapse-item" href="refaireAppel.php">Refaire l'appel</a>
            <a class="collapse-item" href="viewAttendance.php">Voir la liste d'appel</a>
            <a class="collapse-item" href="viewStudentAttendance.php">Voir l'appel d'un employé</a>
            <a class="collapse-item" href="viewStudents.php">Liste des employés</a>
            
          </div>
        </div>
      </li>
 
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Supplémentaires
      </div>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePage" aria-expanded="true"
          aria-controls="collapsePage">
          <i class="fas fa-clock fa-2x"  style="color: blue;"></i>
          <span>Heures supplémentaires</span>
        </a>
        <div id="collapsePage" class="collapse" aria-labelledby="headingPage" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Supplémentaires</h6>
            <a class="collapse-item" href="takeHeuresSupp.php">Ajouter les heures supp</a>
            <a class="collapse-item" href="viewSupp.php">Voir les heures supp</a>
            <a class="collapse-item" href="viewSuppSelect.php">Heures suppl(un employé)</a>

          </div>
        </div>
      </li>
   
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
      Rendement
      </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTable" aria-expanded="true"
          aria-controls="collapseTable">
          <i class="fab fa-fw fa-wpforms" style="color: blue;"></i>
          <span> Rapport des rendements</span>
        </a>
        <div id="collapseTable" class="collapse" aria-labelledby="headingTable" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Rapport</h6>
            
            <a class="collapse-item" href="#">Donner le rapport</a>
            <a class="collapse-item" href="#">Voir le rendement</a>
            <a class="collapse-item" href="#">Rendement(Excel)</a>
          </div>
        </div>
      </li>

     

      <hr class="sidebar-divider">
      <div class="version" id="version-ruangadmin"></div>
    </ul>