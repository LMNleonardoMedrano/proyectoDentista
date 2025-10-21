<?php
// Obtener la página actual sin query string
$paginaActual = basename($_SERVER['REQUEST_URI']);
$paginaActual = strtok($paginaActual, '?');

// ✅ Función para verificar permisos de módulo
function tienePermiso($modulo)
{
    if (!isset($_SESSION["permisos"])) return false;
    return in_array(strtolower($modulo), $_SESSION["permisos"]);
}
?>

<div class="wrapper">
  <div class="page-wrap">
    <div class="app-sidebar colored" style="position: fixed; height: 100vh; overflow-y: auto;">
      <div class="sidebar-header d-flex align-items-center justify-content-between p-3">
        <a class="header-brand d-flex align-items-center" href="inicio" aria-label="Ir al inicio">
          <div class="logo-img">
            <img src="vistas/src/img/logo.png" class="header-brand-img" alt="Dental Logo" />
          </div>
          <span class="text ml-2" style="font-family: cursive;">𝓓𝓮𝓷𝓽𝓪𝓵</span>
        </a>
        <button type="button" class="nav-toggle btn btn-light" aria-label="Expandir menú">
          <i data-toggle="expanded" class="ik ik-toggle-right toggle-icon"></i>
        </button>
        <button id="sidebarClose" class="nav-close btn btn-light" aria-label="Cerrar menú">
          <i class="ik ik-x"></i>
        </button>
      </div>

      <div class="sidebar-content">
        <?php if (isset($_SESSION["nombreRol"])) : ?>
          <div class="nav-container">
            <nav id="main-menu-navigation" class="navigation-main">

              <?php if (tienePermiso('inicio')) : ?>
                <div class="nav-item <?php echo ($paginaActual == 'inicio') ? 'active' : ''; ?>">
                  <a href="inicio"><i class="fa fa-home"></i><span>Inicio</span></a>
                </div>
              <?php endif; ?>

              <?php if (tienePermiso('paciente')) : ?>
                <div class="nav-item <?php echo ($paginaActual == 'paciente') ? 'active' : ''; ?>">
                  <a href="paciente"><i class="fa fa-user"></i><span>Pacientes</span></a>
                </div>
              <?php endif; ?>

              <?php if (tienePermiso('medicamentos')) : ?>
                <div class="nav-item <?php echo ($paginaActual == 'medicamentos') ? 'active' : ''; ?>">
                  <a href="medicamentos"><i class="fa fa-pills"></i><span>Receta Médica</span></a>
                </div>
              <?php endif; ?>

              <?php if (tienePermiso('servicios')) : ?>
                <div class="nav-item <?php echo ($paginaActual == 'servicios') ? 'active' : ''; ?>">
                  <a href="servicios"><i class="fa fa-concierge-bell"></i><span>Servicios</span></a>
                </div>
              <?php endif; ?>

              <?php if (tienePermiso('citas')) : ?>
                <div class="nav-item <?php echo ($paginaActual == 'citas') ? 'active' : ''; ?>">
                  <a href="citas"><i class="fas fa-calendar-alt fa-lg me-2"></i><span>Citas</span></a>
                </div>
              <?php endif; ?>

              <?php if (tienePermiso('tratamiento')) : ?>
                <div class="nav-item <?php echo ($paginaActual == 'tratamiento') ? 'active' : ''; ?>">
                  <a href="tratamiento"><i class="fa fa-notes-medical"></i><span>Tratamientos</span></a>
                </div>
              <?php endif; ?>

              <?php if (tienePermiso('planPagoTratamiento')) : ?>
                <div class="nav-item <?php echo ($paginaActual == 'planPagoTratamiento') ? 'active' : ''; ?>">
                  <a href="planPagoTratamiento"><i class="fa fa-credit-card"></i><span>Pagos</span></a>
                </div>
              <?php endif; ?>

              <?php if (tienePermiso('odontograma')) : ?>
                <div class="nav-item <?php echo ($paginaActual == 'odontograma') ? 'active' : ''; ?>">
                  <a href="odontograma"><i class="fa fa-file-alt"></i><span>Odontograma</span></a>
                </div>
              <?php endif; ?>

              <?php if (tienePermiso('reportesTD')) : ?>
                <div class="nav-item <?php echo ($paginaActual == 'reportesTD') ? 'active' : ''; ?>">
                  <a href="reportesTD"><i class="fa fa-file-alt"></i><span>Reportes</span></a>
                </div>
              <?php endif; ?>

              <?php if (tienePermiso('usuarios')) : ?>
                <div class="nav-item <?php echo ($paginaActual == 'usuarios') ? 'active' : ''; ?>">
                  <a href="usuarios"><i class="fas fa-users-cog"></i><span>Administrar Usuarios</span></a>
                </div>
              <?php endif; ?>

              <?php if (tienePermiso('roles')) : ?>
                <div class="nav-item <?php echo ($paginaActual == 'roles') ? 'active' : ''; ?>">
                  <a href="roles"><i class="fas fa-user-shield"></i><span>Administrar Roles</span></a>
                </div>
              <?php endif; ?>

            </nav>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
