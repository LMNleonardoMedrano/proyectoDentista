<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/controladores/citas.controlador.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/controladores/paciente.controlador.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/controladores/usuarios.controlador.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/controladores/tratamiento.controlador.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/controladores/planPagoTratamiento.controlador.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/modelos/citas.modelo.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/modelos/paciente.modelo.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/modelos/usuarios.modelo.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/modelos/tratamiento.modelo.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/modelos/planPagoTratamiento.modelo.php';

$citas = ControladorCitas::ctrMostrarCitas();
$tratamientos = ControladorTratamiento::ctrMostrarTratamientosPendientes();
$pacientes = ControladorPaciente::ctrMostrarPaciente(null, null);
?>

<div class="wrapper">
  <div class="page-wrap">
    <div class="main-content">
      <div class="content-wrapper">
        <section class="content">

         <!-- ===================== -->
<!-- NAV TABS PRINCIPALES CON PERMISOS -->
<!-- ===================== -->
<ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">

    <!-- TAB CITAS -->
    <?php if (tienePermiso('verCitas')): ?>
    <li class="nav-item">
      <a class="nav-link active" id="citas-tab" data-toggle="tab" href="#citas" role="tab">
        <i class="fa fa-calendar-check-o"></i> Citas
      </a>
    </li>
    <?php endif; ?>

    <!-- TAB TRATAMIENTOS -->
    <?php if (tienePermiso('verTratamientos')): ?>
    <li class="nav-item">
      <a class="nav-link <?php echo !tienePermiso('verCitas') ? 'active' : ''; ?>" 
         id="tratamientos-tab" data-toggle="tab" href="#tratamientos" role="tab">
        Tratamientos
      </a>
    </li>
    <?php endif; ?>

    <!-- TAB PAGOS -->
    <?php if (tienePermiso('verPagos')): ?>
    <li class="nav-item">
      <a class="nav-link <?php echo (!tienePermiso('verCitas') && !tienePermiso('verTratamientos')) ? 'active' : ''; ?>" 
         id="pagos-tab" data-toggle="tab" href="#pagos" role="tab">
        Pagos
      </a>
    </li>
    <?php endif; ?>

    <!-- TAB HISTORIAL CLÍNICO -->
    <?php if (tienePermiso('verHistorialClinico')): ?>
    <li class="nav-item">
      <a class="nav-link <?php echo (!tienePermiso('verCitas') && !tienePermiso('verTratamientos') && !tienePermiso('verPagos')) ? 'active' : ''; ?>" 
         id="historial-tab" data-toggle="tab" href="#historial" role="tab">
        <i class="fa fa-file-text-o"></i> Historial Clínico
      </a>
    </li>
    <?php endif; ?>

</ul>

          <!-- ===================== -->
          <!-- CONTENIDO DE TABS -->
          <!-- ===================== -->
          <div class="tab-content" id="reportTabsContent">

          <!-- ===================== -->
<!-- TAB CITAS -->
<!-- ===================== -->
<?php if (tienePermiso('verCitas')): ?>
<div class="tab-pane fade show active" id="citas" role="tabpanel">
  <h2 class="text-center mb-4">Reporte de Citas</h2>

  <!-- FILA 1: Desde y Hasta -->
  <form id="formReportesCitas" class="mb-3">
    <div class="row g-3 align-items-end">
      <div class="col-md-3">
        <label for="desdeCitas" class="form-label fw-bold">Desde</label>
        <input type="date" name="desdeCitas" id="desdeCitas" class="form-control">
      </div>
      <div class="col-md-3">
        <label for="hastaCitas" class="form-label fw-bold">Hasta</label>
        <input type="date" name="hastaCitas" id="hastaCitas" class="form-control">
      </div>
    </div>

    <!-- FILA 2: Tipo de reporte, Buscador y Botones -->
    <div class="row g-3 align-items-end mt-2">
      <div class="col-md-3">
        <label for="tipoReporteCitas" class="form-label fw-bold">Tipo de reporte</label>
        <select name="tipoReporteCitas" id="tipoReporteCitas" class="form-control" required>
          <option value="">-- Seleccionar Reporte --</option>
          <option value="programadas">Citas Programadas</option>
          <option value="confirmadas">Pacientes Confirmados</option>
          <option value="atendidos">Pacientes Atendidos por Odontólogo</option>
          <option value="canceladas">Citas Canceladas</option>
          <option value="porDia">Citas por Día</option>
          <option value="porOdontologo">Citas por Odontólogo</option>
          <option value="porServicio">Citas por Servicio</option>
          <option value="mensual">Citas Mensuales</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-bold d-none d-md-block">&nbsp;</label>
        <input type="text" id="buscarCita" class="form-control" placeholder="🔍 Buscar por paciente...">
      </div>
      <div class="col-md-2">
        <label class="form-label fw-bold d-none d-md-block">&nbsp;</label>
        <button type="submit" class="btn btn-primary w-100">
          <i class="fa fa-search"></i> Buscar
        </button>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-bold d-none d-md-block">&nbsp;</label>
        <button id="btnExportarPDF" class="btn btn-danger w-100">
          <i class="fa fa-file-pdf-o"></i> Exportar PDF
        </button>
      </div>
    </div>
  </form>

  <div id="contenedorCitas" class="row bg-light p-3 rounded shadow-sm" style="max-height:500px; overflow-y:auto;">
    <div class="col-12">
      <div class="alert alert-info text-center mb-0">
        Seleccione un reporte para mostrar los resultados.
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
            <script>
              document.getElementById('btnExportarPDF').addEventListener('click', function(e) {
                e.preventDefault();

                const tipo = document.getElementById('tipoReporteCitas').value;
                const desde = document.getElementById('desdeCitas').value;
                const hasta = document.getElementById('hastaCitas').value;

                if (!tipo) {
                  alert("Seleccione un tipo de reporte primero.");
                  return;
                }

                const url = `vistas/modulos/reportesCitas.php?tipoReporteCitas=${tipo}&desdeCitas=${desde}&hastaCitas=${hasta}&exportarPDF=1`;
                window.open(url, "_blank");
              });
            </script>

          <!-- ===================== -->
<!-- TAB TRATAMIENTOS -->
<!-- ===================== -->
<?php if (tienePermiso('verTratamientos')): ?>
<div class="tab-pane fade" id="tratamientos" role="tabpanel">
  <h2 class="text-center mb-4">Reporte de Tratamientos</h2>

  <form id="formReportesTratamientos" class="mb-3">
    <!-- FILA 1: Desde y Hasta -->
    <div class="row g-3 align-items-end">
      <div class="col-md-3">
        <label for="desdeTrat" class="form-label fw-bold">Desde</label>
        <input type="date" name="desdeTrat" id="desdeTrat" class="form-control">
      </div>
      <div class="col-md-3">
        <label for="hastaTrat" class="form-label fw-bold">Hasta</label>
        <input type="date" name="hastaTrat" id="hastaTrat" class="form-control">
      </div>
    </div>

    <!-- FILA 2: Tipo de reporte, Buscador y Botones -->
    <div class="row g-3 align-items-end mt-2">
      <div class="col-md-3">
        <label for="tipoReporteTratamientos" class="form-label fw-bold">Tipo de reporte</label>
        <select name="tipoReporteTratamientos" id="tipoReporteTratamientos" class="form-control" required>
          <option value="">-- Seleccionar Reporte --</option>
          <option value="completados">Tratamientos Completados</option>
          <option value="parciales">Tratamientos Parciales</option>
          <option value="activos">Tratamientos Activos</option>
          <option value="noCancelados">Tratamientos No Cancelados</option>
          <option value="porOdontologo">Tratamientos por Odontólogo</option>
          <option value="porServicio">Tratamientos por Servicio</option>
          <option value="porEstado">Tratamientos por Estado</option>
          <option value="mensual">Tratamientos Mensuales</option>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label fw-bold d-none d-md-block">&nbsp;</label>
        <input type="text" id="buscarTratamiento" class="form-control" placeholder="🔍 Buscar por paciente...">
      </div>

      <div class="col-md-2">
        <label class="form-label fw-bold d-none d-md-block">&nbsp;</label>
        <button type="submit" class="btn btn-primary w-100">
          <i class="fa fa-search"></i> Aceptar
        </button>
      </div>

      <div class="col-md-3">
        <label class="form-label fw-bold d-none d-md-block">&nbsp;</label>
        <button id="btnExportarTratamientosPDF" class="btn btn-danger w-100">
          <i class="fa fa-file-pdf-o"></i> Exportar PDF
        </button>
      </div>
    </div>
  </form>

  <div id="contenedorTratamientos" class="row bg-light p-3 rounded shadow-sm" style="max-height:500px; overflow-y:auto;">
    <div class="col-12">
      <div class="alert alert-info text-center mb-0">
        Seleccione un reporte para mostrar los resultados.
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
            <script>
              document.getElementById('btnExportarTratamientosPDF').addEventListener('click', function(e) {
                e.preventDefault();

                const tipo = document.getElementById('tipoReporteTratamientos').value;
                const desde = document.getElementById('desdeTrat').value;
                const hasta = document.getElementById('hastaTrat').value;

                if (!tipo) {
                  alert("Seleccione un tipo de reporte primero.");
                  return;
                }

                const url = `vistas/modulos/reportesTratamientos.php?tipoReporteTratamientos=${tipo}&desdeTrat=${desde}&hastaTrat=${hasta}&exportarPDF=1`;
                window.open(url, "_blank");
              });
            </script>

            <!-- ===================== -->
<!-- TAB PAGOS -->
<!-- ===================== -->
<?php if (tienePermiso('verPagos')): ?>
<div class="tab-pane fade" id="pagos" role="tabpanel">
  <h2 class="text-center mb-4">Reporte de Pagos</h2>

  <form id="formReportesPagos" class="mb-3">
    <!-- FILA 1: Desde y Hasta -->
    <div class="row g-3 align-items-end">
      <div class="col-md-3">
        <label for="desde" class="form-label fw-bold">Desde</label>
        <input type="date" name="desde" id="desde" class="form-control">
      </div>
      <div class="col-md-3">
        <label for="hasta" class="form-label fw-bold">Hasta</label>
        <input type="date" name="hasta" id="hasta" class="form-control">
      </div>
    </div>

    <!-- FILA 2: Tipo de reporte, Buscador y Botones -->
    <div class="row g-3 align-items-end mt-2">
      <div class="col-md-3">
        <label for="tipoReporte" class="form-label fw-bold">Tipo de reporte</label>
        <select name="tipoReporte" id="tipoReporte" class="form-control" required>
          <option value="">-- Seleccionar Reporte --</option>
          <option value="totales">Pagos Totales (entre fechas)</option>
          <option value="saldoPacientes">Saldos por Paciente</option>
          <option value="pendientes">Pagos Pendientes</option>
          <option value="diario">Pagos por Día</option>
          <option value="servicios">Servicios más solicitados</option>
          <option value="porOdontologo">Pagos por Odontólogo</option>
          <option value="porTipoPago">Pagos por Tipo de Pago</option>
          <option value="porEstadoTratamiento">Pagos por Estado de Tratamiento</option>
          <option value="descuentos">Descuentos Aplicados</option>
          <option value="porServicio">Pagos por Servicio</option>
          <option value="mensual">Pagos Mensuales</option>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label fw-bold d-none d-md-block">&nbsp;</label>
        <input type="text" id="buscarPaciente" class="form-control" placeholder="🔍 Buscar por paciente...">
      </div>

      <div class="col-md-2">
        <label class="form-label fw-bold d-none d-md-block">&nbsp;</label>
        <button type="submit" class="btn btn-primary w-100">
          <i class="fa fa-search"></i> Aceptar
        </button>
      </div>

      <div class="col-md-3">
        <label class="form-label fw-bold d-none d-md-block">&nbsp;</label>
        <button id="btnExportarPDFPagos" class="btn btn-danger w-100">
          <i class="fa fa-file-pdf-o"></i> Exportar PDF
        </button>
      </div>
    </div>
  </form>

  <div id="contenedorPagos" class="row bg-light p-3 rounded shadow-sm" style="max-height:500px; overflow-y:auto;">
    <div class="col-12">
      <div class="alert alert-info text-center mb-0">
        Seleccione un reporte para mostrar los resultados.
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
            <script>
              document.getElementById('btnExportarPDFPagos').addEventListener('click', function(e) {
                e.preventDefault();

                const tipo = document.getElementById('tipoReporte').value;
                const desde = document.getElementById('desde').value;
                const hasta = document.getElementById('hasta').value;

                if (!tipo) {
                  alert("Seleccione un tipo de reporte primero.");
                  return;
                }

                const url = `vistas/modulos/reportesPagos.php?tipoReporte=${tipo}&desde=${desde}&hasta=${hasta}`;
                window.open(url, "_blank");
              });
            </script>
<!-- ===================== -->
<!-- TAB HISTORIAL CLÍNICO -->
<!-- ===================== -->
<?php if (tienePermiso('verHistorialClinico')): ?>
<div class="tab-pane fade" id="historial" role="tabpanel">
  <h2 class="text-center mb-4">Historial Clínico del Paciente</h2>

  <!-- Fila principal con buscador y botones alineados -->
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    
    <!-- Buscador -->
    <div class="flex-grow-1 me-3 position-relative" style="max-width: 500px;">
      <input type="text" id="buscarHistorial" class="form-control" placeholder="🔍 Buscar paciente por nombre o CI..." autocomplete="off">
      <!-- Contenedor para sugerencias -->
      <div id="sugerenciasHistorial" class="list-group position-absolute w-100" style="z-index:1000; max-height:200px; overflow-y:auto;"></div>
    </div>

    <!-- Botones -->
    <div class="text-end">
      <button id="btnVerHistorial" class="btn btn-primary me-2">
        <i class="fa fa-eye"></i> Ver Historial
      </button>
      <button id="btnExportarHistorialPDF" class="btn btn-danger">
        <i class="fa fa-file-pdf-o"></i> Exportar PDF
      </button>
    </div>

  </div>

  <!-- Contenedor del historial -->
  <div id="contenedorHistorial" class="bg-light p-3 rounded shadow-sm" style="max-height:500px; overflow-y:auto;">
    <div class="alert alert-info text-center mb-0">
      Haga clic en "Ver Historial" para mostrar los resultados.
    </div>
  </div>
</div>
<?php endif; ?>
<script>
const inputBusqueda = document.getElementById('buscarHistorial');
const contenedorSugerencias = document.createElement('div');
contenedorSugerencias.id = 'sugerenciasHistorial';
contenedorSugerencias.className = 'list-group position-absolute w-100';
contenedorSugerencias.style.zIndex = '1000';
contenedorSugerencias.style.maxHeight = '200px';
contenedorSugerencias.style.overflowY = 'auto';
inputBusqueda.parentNode.appendChild(contenedorSugerencias);

// Guardamos el CI real seleccionado
let ciSeleccionado = '';

inputBusqueda.addEventListener('input', function() {
    const query = this.value.trim();
    ciSeleccionado = ''; // resetear al escribir
    if (!query) {
        contenedorSugerencias.innerHTML = '';
        return;
    }

    fetch(`ajax/historialClinico.ajax.php?autocompletar=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            contenedorSugerencias.innerHTML = '';
            if (data.length === 0) return;

            data.forEach(paciente => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action';
                item.textContent = `${paciente.nombre} | ${paciente.ci}`;
                item.dataset.ci = paciente.ci; // guardamos el CI real
                item.addEventListener('click', function() {
                    inputBusqueda.value = `${paciente.nombre} | ${paciente.ci}`; // mostrar nombre | CI
                    ciSeleccionado = this.dataset.ci; // guardamos CI para buscar
                    contenedorSugerencias.innerHTML = '';
                });
                contenedorSugerencias.appendChild(item);
            });
        });
});

// Cerrar sugerencias si hace click fuera del input
document.addEventListener('click', function(e) {
    if (!inputBusqueda.contains(e.target) && !contenedorSugerencias.contains(e.target)) {
        contenedorSugerencias.innerHTML = '';
    }
});

// Modificar los botones para usar el CI real
document.getElementById('btnVerHistorial').addEventListener('click', function() {
    const pacienteID = ciSeleccionado || inputBusqueda.value.trim();
    if (!pacienteID) return alert('Ingrese un nombre o CI para buscar');

    fetch(`ajax/historialClinico.ajax.php?busqueda=${encodeURIComponent(pacienteID)}`)
      .then(res => res.text())
      .then(html => { 
          document.getElementById('contenedorHistorial').innerHTML = html; 
      });
});

document.getElementById('btnExportarHistorialPDF').addEventListener('click', function() {
    const pacienteID = ciSeleccionado || inputBusqueda.value.trim();
    if (!pacienteID) return alert('Ingrese un nombre o CI antes de exportar el PDF.');

    window.open(`vistas/modulos/historialClinico.pdf.php?idPaciente=${encodeURIComponent(pacienteID)}`, '_blank');
});
</script>


          </div> <!-- FIN TAB-CONTENT -->

      </div>
      </section>
    </div>
  </div>
</div>
