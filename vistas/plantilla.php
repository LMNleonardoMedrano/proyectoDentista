<?php
session_start();
setlocale(LC_ALL, 'es_ES.UTF-8');
date_default_timezone_set('America/Lima'); // Cambia a tu país si deseas

// Evitar undefined index
if (!isset($_SESSION['id'])) {
    $_SESSION['id'] = 0; // ID del usuario logueado
}

if (!isset($_SESSION['nombreRol'])) {
    $_SESSION['nombreRol'] = ''; // Rol del usuario
}

if (!isset($_SESSION['permisos'])) {
    $_SESSION['permisos'] = []; // Array de rutas permitidas para el usuario
}
?>

<!doctype html>
<html class="no-js" lang="es">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="x-ua-compatible" content="ie=edge" />
  <title>DENTISTA</title>
  <meta name="description" content="" />
  <meta name="keywords" content="" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="icon" href="vistas/src/img/down-arrow.svg" type="image/x-icon" />

  <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet" />

  <!-- Bootstrap 4 CSS -->
  <link rel="stylesheet" href="vistas/plugins/bootstrap/dist/css/bootstrap.min.css" />

  <!-- FontAwesome y otros íconos -->
  <link rel="stylesheet" href="vistas/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="vistas/plugins/icon-kit/dist/css/iconkit.min.css" />
  <link rel="stylesheet" href="vistas/plugins/ionicons/dist/css/ionicons.min.css" />

  <!-- Plugins CSS -->
  <link rel="stylesheet" href="vistas/plugins/perfect-scrollbar/css/perfect-scrollbar.css" />
  <link rel="stylesheet" href="vistas/plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="vistas/plugins/jvectormap/jquery-jvectormap.css" />
  <link rel="stylesheet" href="vistas/plugins/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css" />
  <link rel="stylesheet" href="vistas/plugins/weather-icons/css/weather-icons.min.css" />
  <link rel="stylesheet" href="vistas/plugins/c3/c3.min.css" />
  <link rel="stylesheet" href="vistas/plugins/owl.carousel/dist/assets/owl.carousel.min.css" />
  <link rel="stylesheet" href="vistas/plugins/owl.carousel/dist/assets/owl.theme.default.min.css" />

  <!-- Tema principal -->
  <link rel="stylesheet" href="vistas/dist/css/theme.min.css" />
  <link rel="stylesheet" href="vistas/src/css/inicio.css" />
  <link rel="stylesheet" href="vistas/src/css/odontograma2.css" />

  <!-- FullCalendar CSS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />

  <!-- Estilos personalizados para DataTables -->
  <style>
    /* Tu CSS personalizado aquí, sin cambios */
    table.dataTable {
      width: 100%;
      border-collapse: collapse;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      font-size: 15px;
      background-color: #ffffff;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      border-radius: 12px;
      overflow: hidden;
    }

    table.dataTable thead {
      background-color: #e0f7fa;
    }

    table.dataTable thead th {
      padding: 12px 15px;
      color: #00796b;
      text-align: left;
      border-bottom: 2px solid #b2dfdb;
    }

    table.dataTable tbody td {
      padding: 10px 15px;
      color: #333;
      border-bottom: 1px solid #f0f0f0;
    }

    table.dataTable tbody tr:hover {
      background-color: rgb(217, 235, 235);
    }

    table.dataTable tbody tr.selected {
      background-color: #b2ebf2 !important;
    }

    table.dataTable,
    table.dataTable th,
    table.dataTable td {
      border: none !important;
    }

    table.dataTable.dtr-inline.collapsed>tbody>tr>td:first-child:before {
      background-color: #4dd0e1;
    }
  </style>

  <style>
    /* CSS personalizado para calendario y navegación */
    .fc-daygrid-event {
      background-color: #2dce89 !important;
      color: white !important;
      border: none !important;
    }

    .fc-button {
      background-color: #0288d1 !important;
      color: white !important;
      border: none !important;
    }

    .fc-toolbar-title {
      color: #01579b;
      font-weight: bold;
    }

    .navigation-main .nav-item.active>a {
      background-color: #3b475cff;
      color: white !important;
      font-weight: bold;
      border-radius: 4px;
    }

    .navigation-main .nav-item>a:hover {
      background-color: #3b475cff;
      color: white !important;
    }

    .icon-btn {
    background: none;
    border: none;
    padding: 0.4rem;
    cursor: pointer;
    transition: filter 0.2s;
  }

  .icon-btn:focus {
    outline: none; /* Evita el borde al hacer clic */
  }

  .icon-btn:hover i {
    filter: brightness(1.5); /* Hace que "alumbre" el icono */
  }
    
  </style>

  <!-- Modernizr -->
  <script src="vistas/src/js/vendor/modernizr-2.8.3.min.js"></script>

  <!-- jQuery 3.6.0 -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Popper.js 1.x -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

  <!-- Bootstrap 4 JS -->
  <script src="vistas/plugins/bootstrap/dist/js/bootstrap.min.js"></script>

  <!-- Plugins JS -->
  <script src="vistas/plugins/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
  <script src="vistas/plugins/screenfull/dist/screenfull.js"></script>

  <!-- Moment.js -->
  <script src="vistas/plugins/moment/moment.js"></script>

  <!-- Tempusdominus Bootstrap 4 -->
  <script src="vistas/plugins/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js"></script>

  <!-- D3 y C3 -->
  <script src="vistas/plugins/d3/dist/d3.min.js"></script>
  <script src="vistas/plugins/c3/c3.min.js"></script>

  <!-- jVectorMap -->
  <script src="vistas/plugins/jvectormap/jquery-jvectormap.min.js"></script>
  <!-- MAPA de jVectorMap: descomenta si tienes el archivo o usa CDN -->
  <!-- <script src="vistas/plugins/jvectormap/tests/assets/jquery-jvectormap-world-mill-en.js"></script> -->
  <!-- O CDN alternativa: -->
  <script src="https://cdn.jsdelivr.net/npm/jvectormap@2.0.5/jquery-jvectormap-world-mill-en.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
  <script src="vistas/plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
  <script src="vistas/plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="vistas/plugins/sweetalert2/sweetalert2.all.js"></script>

  <!-- FullCalendar JS -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

  <!-- html2canvas -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

  <!-- Archivos JS personalizados -->
  <script src="vistas/js/tables.js"></script>
  <script src="vistas/js/widgets.js"></script>
  <script src="vistas/js/charts.js"></script>
  <script src="vistas/dist/js/theme.min.js"></script>

  <!-- Google Analytics -->
  <script>
    (function(b, o, i, l, e, r) {
      b.GoogleAnalyticsObject = l;
      b[l] ||
        (b[l] = function() {
          (b[l].q = b[l].q || []).push(arguments);
        });
      b[l].l = +new Date();
      e = o.createElement(i);
      r = o.getElementsByTagName(i)[0];
      e.src = "https://www.google-analytics.com/analytics.js";
      r.parentNode.insertBefore(e, r);
    })(window, document, "script", "ga");
    ga("create", "UA-XXXXX-X", "auto");
    ga("send", "pageview");
  </script>

</head>



<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">

<?php
if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok") {

    echo '<div class="wrapper">';

    /* CABEZERA */
    include "modulos/cabecera.php";

    /* MENU */
    include "modulos/menu.php";

    /* CONTENIDO */
    if (isset($_GET["ruta"])) {

        $ruta = $_GET["ruta"];
        $rutaLower = strtolower($ruta); // Para comparar permisos en minúsculas

        if (
            $ruta == "inicio" ||
            $ruta == "perfil" ||
            $ruta == "paciente" ||
            $ruta == "medicamentos" ||
            $ruta == "servicios" ||
            $ruta == "tratamiento" ||
            $ruta == "planPagoTratamiento" ||
            $ruta == "citas" ||
            $ruta == "odontograma" ||
            $ruta == "usuarios" ||
            $ruta == "roles" ||
            $ruta == "reportesTD" ||
            $ruta == "salir" ||
            $ruta == "editPassword"
        ) {

            // Rutas que siempre son accesibles
            $rutasPublicas = ["inicio", "salir", "editpassword"];

            if (in_array($rutaLower, $rutasPublicas) || in_array($rutaLower, $_SESSION["permisos"])) {
                include "modulos/" . $ruta . ".php";
            } else {
                echo '<div class="alert alert-danger text-center mt-4">No tienes permiso para acceder a este módulo.</div>';
            }

        } else {
            include "modulos/404.php";
        }

    } else {
        include "modulos/inicio.php";
    }

    /* FOOTER */
    include "modulos/footer.php";

    echo '</div>';

} else {

    if (isset($_GET["ruta"])) {
        if ($_GET["ruta"] == "resetPassword") {
            include "modulos/resetPassword.php";
        } elseif ($_GET["ruta"] == "password") {
            include "modulos/password.php";
        } else {
            include "modulos/login.php";
        }
    } else {
        include "modulos/login.php";
    }

}
?>
<script>
    // Pasar los pacientes desde PHP a JS
    const pacientesData = <?php echo json_encode($pacientesDesdeBD); ?>;
</script>
  <script src="vistas/js/plantilla.js"></script>
  <script src="vistas/js/usuarios.js"></script>
  <script src="vistas/js/paciente.js"></script>
  <script src="vistas/js/tutorPadre.js"></script>
  <script src="vistas/js/medicamentos.js"></script>
  <script src="vistas/js/servicios.js"></script>
  <script src="vistas/js/citas.js"></script>
  <script src="vistas/js/odontograma.js"></script>
  <script src="vistas/js/tratamiento.js"></script>
  <script src="vistas/js/planPagoTratamiento.js"></script>
  <script src="vistas/js/reportes.js"></script>
  <script src="vistas/js/roles.js"></script>






  <script>
    // Convertimos el array de PHP a JS
    const citas = <?php echo json_encode($citasDesdeBD); ?>;

    // Llamamos a la función de nuestro JS
    mostrarEstadisticas(citas);
    
  </script>



</body>

</html>