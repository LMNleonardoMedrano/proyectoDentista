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
?>

<div class="wrapper">
  <div class="page-wrap">
    <div class="main-content">
      <div class="content-wrapper">
        <section class="content">

          <!-- ===================== -->
          <!-- NAV TABS PRINCIPALES -->
          <!-- ===================== -->
          <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="citas-tab" data-toggle="tab" href="#citas" role="tab">
                <i class="fa fa-calendar-check-o"></i> Citas
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="tratamientos-tab" data-toggle="tab" href="#tratamientos" role="tab">
                Tratamientos
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="pagos-tab" data-toggle="tab" href="#pagos" role="tab">
                Pagos
              </a>
            </li>
          </ul>

          <!-- ===================== -->
          <!-- CONTENIDO DE TABS -->
          <!-- ===================== -->
          <div class="tab-content" id="reportTabsContent">

            <!-- ============ TAB CITAS ============ -->
            <div class="tab-pane fade show active" id="citas" role="tabpanel">
              <h2 class="text-center mb-4">Reporte de Citas</h2>

              <form id="formReportesCitas" class="row g-3 mb-3 align-items-end">
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
                <div class="col-md-3">
                  <label for="desdeCitas" class="form-label fw-bold">Desde</label>
                  <input type="date" name="desdeCitas" id="desdeCitas" class="form-control">
                </div>
                <div class="col-md-3">
                  <label for="hastaCitas" class="form-label fw-bold">Hasta</label>
                  <input type="date" name="hastaCitas" id="hastaCitas" class="form-control">
                </div>
                <div class="col-md-3">
                  <button type="submit" class="btn btn-primary w-100">
                    <i class="fa fa-search"></i> Aceptar
                  </button>
                </div>
              </form>

              <div class="row mb-3">
                <div class="col-md-6 offset-md-3">
                  <input type="text" id="buscarCita" class="form-control" placeholder="🔍 Buscar por paciente...">
                </div>
              </div>

              <div id="contenedorCitas" class="row bg-light p-3 rounded shadow-sm" style="max-height:500px; overflow-y:auto;">
                <div class="col-12">
                  <div class="alert alert-info text-center mb-0">
                    Seleccione un reporte para mostrar los resultados.
                  </div>
                </div>
              </div>

              <div class="text-center mt-3">
                <button id="btnExportarPDF" class="btn btn-danger btn-sm">
                  <i class="fa fa-file-pdf-o"></i> Exportar PDF
                </button>
              </div>
            </div>
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

                window.open(url, "_blank"); // abre el PDF en otra pestaña
              });
            </script>

            <!-- ============ TAB TRATAMIENTOS ============ -->
            <div class="tab-pane fade" id="tratamientos" role="tabpanel">
              <h2 class="text-center mb-4">Reporte de Tratamientos</h2>

              <form id="formReportesTratamientos" class="row g-3 mb-3 align-items-end">
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
                <div class="col-md-3">
                  <label for="desdeTrat" class="form-label fw-bold">Desde</label>
                  <input type="date" name="desdeTrat" id="desdeTrat" class="form-control">
                </div>
                <div class="col-md-3">
                  <label for="hastaTrat" class="form-label fw-bold">Hasta</label>
                  <input type="date" name="hastaTrat" id="hastaTrat" class="form-control">
                </div>
                <div class="col-md-3">
                  <button type="submit" class="btn btn-primary w-100">
                    <i class="fa fa-search"></i> Aceptar
                  </button>
                </div>
              </form>

              <div class="row mb-3">
                <div class="col-md-6 offset-md-3">
                  <input type="text" id="buscarTratamiento" class="form-control" placeholder="🔍 Buscar por paciente...">
                </div>
              </div>

              <div id="contenedorTratamientos" class="row bg-light p-3 rounded shadow-sm" style="max-height:500px; overflow-y:auto;">
                <div class="col-12">
                  <div class="alert alert-info text-center mb-0">
                    Seleccione un reporte para mostrar los resultados.
                  </div>
                </div>
              </div>

              <div class="text-center mt-3">
                <button id="btnExportarTratamientosPDF" class="btn btn-danger btn-sm">
                  <i class="fa fa-file-pdf-o"></i> Exportar PDF
                </button>
              </div>

            </div>
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

                window.open(url, "_blank"); // abre el PDF en otra pestaña
              });
            </script>
            <!-- ============ TAB PAGOS ============ -->
            <div class="tab-pane fade" id="pagos" role="tabpanel">
              <h2 class="text-center mb-4">Reporte de Pagos</h2>

              <form id="formReportesPagos" class="row g-3 mb-3 align-items-end">
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
                <div class="col-md-3">
                  <label for="desde" class="form-label fw-bold">Desde</label>
                  <input type="date" name="desde" id="desde" class="form-control">
                </div>
                <div class="col-md-3">
                  <label for="hasta" class="form-label fw-bold">Hasta</label>
                  <input type="date" name="hasta" id="hasta" class="form-control">
                </div>
                <div class="col-md-3">
                  <button type="submit" class="btn btn-primary w-100">
                    <i class="fa fa-search"></i> Aceptar
                  </button>
                </div>
              </form>

              <div class="row mb-3">
                <div class="col-md-6 offset-md-3">
                  <input type="text" id="buscarPaciente" class="form-control" placeholder="🔍 Buscar por paciente...">
                </div>
              </div>

              <div id="contenedorPagos" class="row bg-light p-3 rounded shadow-sm" style="max-height:500px; overflow-y:auto;">
                <div class="col-12">
                  <div class="alert alert-info text-center mb-0">
                    Seleccione un reporte para mostrar los resultados.
                  </div>
                </div>
              </div>
              <div class="text-center mt-3">
                <button id="btnExportarPDFPagos" class="btn btn-danger btn-sm">
                  <i class="fa fa-file-pdf-o"></i> Exportar PDF
                </button>
              </div>
            </div>
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

                window.open(url, "_blank"); // abre el PDF en otra pestaña
              });
            </script>
          </div> <!-- FIN TAB-CONTENT -->

      </div>
      </section>
    </div>
  </div>
</div>
</div>