<div class="wrapper">
  <div class="page-wrap">
    <div class="main-content">
      <div class="content-wrapper">

      <section class="content-header">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 fw-bold text-primary">
            <i class="fas fa-calendar-alt fa-lg me-2"></i>
            Gestión de Citas
        </h1>

        <!-- Botón Agregar Cita -->
        <?php if (tienePermiso('crearCitas')): ?>
        <button class="btn btn-primary d-flex align-items-center justify-content-center"
            style="padding: 8px 20px; font-size: 1.1rem; height: 42px;" data-toggle="modal" data-target="#modalAgregarCita">
            <i class="fas fa-plus mr-2"></i> Agregar Cita
        </button>
        <?php endif; ?>
    </div>
</section>

<!-- Filtros -->
<?php if (tienePermiso('listarCitas')): ?>
<div id="filtrosTablaCitas" class="mb-4 sticky-top" style="top: 70px; z-index: 1000;">
    <div class="d-flex justify-content-start gap-2 ps-3">
        <button type="button" class="btn btn-primary btn-rounded btn-filter active" data-filter="all">Todas</button>
        <button type="button" class="btn btn-outline-secondary btn-rounded btn-filter" data-filter="programada">Programadas</button>
        <button type="button" class="btn btn-outline-secondary btn-rounded btn-filter" data-filter="confirmada">Confirmadas</button>
        <button type="button" class="btn btn-outline-secondary btn-rounded btn-filter" data-filter="atendida">Atendidas</button>
    </div>
</div>

<style>
    .btn-filter {
        border-radius: 20px;
        padding: 6px 16px;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }
    .btn-filter.active {
        background-color: #0d6efd;
        color: #fff;
        border: none;
    }
    .btn-filter:not(.active) {
        background-color: #f0f0f0;
        color: #333;
        border: none;
    }
    .btn-filter+.btn-filter { margin-left: 8px; }
</style>
<?php endif; ?>

<?php
$odontologos = ControladorUsuarios::ctrMostrarSoloOdontologos();
if (!is_array($odontologos)) $odontologos = [];
$coloresOdontologos = [2=>'#4caf50',3=>'#2196f3',4=>'#ff9800',6=>'#e91e63',7=>'#dce91eff',8=>'#290630ff'];
?>

<section class="content">
    <div class="box">

        <!-- Estadísticas -->
        <?php if (tienePermiso('listarCitas')): 
            $citasDesdeBD = ControladorCitas::ctrMostrarCitas();
        ?>
        <div class="row g-2 mb-3" id="stats-container">
            <div class="col-12 col-md-3">
                <div class="card text-center shadow-sm py-2">
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1">Total Citas</h6>
                        <p class="mb-0 fw-bold text-primary" style="font-size:1.5rem;" id="total-citas">0</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card text-center shadow-sm py-2">
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1">Programadas</h6>
                        <p class="mb-0 fw-bold text-warning" style="font-size:1.5rem;" id="programadas">0</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card text-center shadow-sm py-2">
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1">Confirmadas</h6>
                        <p class="mb-0 fw-bold text-success" style="font-size:1.5rem;" id="confirmadas">0</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card text-center shadow-sm py-2">
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1">Atendidas</h6>
                        <p class="mb-0 fw-bold" style="font-size:1.5rem; color:#6f42c1;" id="atendida">0</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Exportar PDF -->
        <?php if (tienePermiso('listarCitas')): ?>
        <div class="box-header with-border d-flex justify-content-between mb-3">
            <a href='vistas/modulos/reportesCitas.php' class='btn btn-danger' target='_blank'>
                <i class='fa fa-file-pdf-o'></i> Exportar PDF
            </a>
        </div>
        <?php endif; ?>

        <!-- Nav Tabs -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <?php if (tienePermiso('listarCitas')): ?>
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#todasCitas" role="tab">Todas las citas registradas</a></li>
            <?php endif; ?>
            <?php if (tienePermiso('verAgenda')): ?>
            <li class="nav-item"><a class="nav-link <?= !tienePermiso('listarCitas') ? 'active' : '' ?>" data-toggle="tab" href="#agendaOdontologos" role="tab">Agenda de Odontólogos</a></li>
            <?php endif; ?>
        </ul>

        <div class="tab-content">
            <!-- Tab 1: Todas las citas -->
            <?php if (tienePermiso('listarCitas')): ?>
            <div class="tab-pane fade show active" id="todasCitas" role="tabpanel">
                <h4 class="text-center mb-3">Todas las citas registradas</h4>
                <div class="table-responsive">
                    <?php $todasCitas = ControladorCitas::ctrMostrarCitasCompletas(); ?>
                    <table id="data_table" class="table table-hover" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha Y hora</th>
                                <th>Paciente</th>
                                <th>Odontólogo</th>
                                <th>Motivo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($todasCitas)): ?>
                            <?php foreach ($todasCitas as $i => $cita):
                                $colorOdontologo = $coloresOdontologos[$cita['idUsuarios']] ?? '#607d8b';
                            ?>
                            <tr data-id="<?= $cita['idCita'] ?>" data-estado="<?= $cita['estado'] ?>">
                                <td><?= $i+1 ?></td>
                                <td>
                                    <div><div style="color:#000; font-weight:bold;"><?= htmlspecialchars($cita['fecha']) ?></div>
                                    <div style="font-size:0.9rem; color:#555;"><?= htmlspecialchars($cita['hora'].' - '.$cita['horaFin']) ?></div></div>
                                </td>
                                <td><?= $cita["nombrePaciente"] ?></td>
                                <td><span style="color:<?= $colorOdontologo ?>; font-weight:bold;"><?= $cita["nombreOdontologo"] ?></span></td>
                                <td><?= $cita["motivoConsulta"] ?></td>
                                <td>
                                    <span class="estado-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php switch ($cita['estado']) {
                                        case 'programada': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'confirmada': echo 'bg-green-100 text-green-800'; break;
                                        case 'atendida': echo 'bg-purple-100 text-purple-800'; break;
                                        case 'cancelada': echo 'bg-red-100 text-red-800'; break;
                                        case 'no_asistio': echo 'bg-orange-100 text-orange-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }?>"><?= ucfirst($cita['estado']); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <?php if (tienePermiso('editarCitas')): ?>
                                        <button class="icon-btn text-primary btnEditarCita" data-cita='<?= json_encode($cita) ?>' data-toggle="modal" data-target="#modalEditarCita" title="Editar"><i class="fas fa-edit fa-lg"></i></button>
                                        <?php endif; ?>
                                        <?php if (tienePermiso('confirmarCitas')): ?>
                                        <button class="icon-btn text-success btnConfirmarCita" data-id="<?= $cita['idCita'] ?>" title="Confirmar"><i class="fas fa-check fa-lg"></i></button>
                                        <?php endif; ?>
                                        <?php if (tienePermiso('cancelarCitas')): ?>
                                        <button class="icon-btn text-danger btnCancelarCita" data-id="<?= $cita['idCita'] ?>" title="Cancelar"><i class="fas fa-times fa-lg"></i></button>
                                        <?php endif; ?>
                                        <?php if (tienePermiso('noAsistioCitas')): ?>
                                        <button class="icon-btn text-warning btnNoAsistioCita" data-id="<?= $cita['idCita'] ?>" title="No Asistió"><i class="fas fa-user-slash fa-lg"></i></button>
                                        <?php endif; ?>
                                        <?php if (tienePermiso('eliminarCitas')): ?>
                                        <button class="icon-btn text-secondary btnEliminarCita" data-id="<?= $cita['idCita'] ?>" title="Eliminar"><i class="fas fa-trash fa-lg"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center text-muted">No hay citas registradas</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tab 2: Agenda de Odontólogos -->
            <?php if (tienePermiso('verAgenda')): ?>
            <div class="tab-pane fade <?= !tienePermiso('listarCitas') ? 'show active' : '' ?>" id="agendaOdontologos" role="tabpanel"
                data-rol="<?= $_SESSION['idRol'] ?>"
                data-idusuario="<?= $_SESSION['id'] ?>"
                data-nombre="<?= $_SESSION['nombre'].' '.$_SESSION['apellido'] ?>">

                <h2 class="text-center mt-3 mb-4">Agenda de Odontólogos</h2>

                <div id="vistaOdontologos">
                    <div class="row px-4">
                        <?php if (!empty($odontologos)): ?>
                            <?php foreach ($odontologos as $od):
                                $color = $coloresOdontologos[$od['idUsuarios']] ?? '#607d8b';
                            ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card shadow-sm">
                                        <div class="card-header text-white text-center" style="background-color: <?= $color ?>;">
                                            <strong><?= $od['nombre'].' '.$od['apellido'] ?></strong>
                                        </div>
                                        <div class="card-body text-center" style="padding: 20px;">
                                            <img src="<?= !empty($od["foto"]) && file_exists($od["foto"]) ? $od["foto"] : 'vistas/img/usuarios/default/anonymous.png' ?>"
                                                 class="img-circle mx-auto d-block mb-3" width="90"
                                                 style="border-radius: 50%; border: 2px solid #ccc; object-fit: cover;">
                                            <button class="btn btn-sm btnVerCalendario" style="background-color: <?= $color ?>; color: #fff;"
                                                    data-id="<?= $od['idUsuarios'] ?>"
                                                    data-nombre="<?= $od['nombre'].' '.$od['apellido'] ?>"
                                                    data-color="<?= $color ?>">
                                                Ver calendario completo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center text-muted">No hay odontólogos para mostrar.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="vistaCalendarioOdontologo" style="display:none; margin-top:20px;">
                    <div class="mb-3 px-4">
                        <button class="btn btn-secondary btn-sm" id="btnVolverOdontologos">← Volver a la lista</button>
                    </div>
                    <div class="text-center mb-3">
                        <h3 id="nombreOdontologoSeleccionado" class="font-weight-bold"></h3>
                    </div>
                    <div class="px-4">
                        <div id="calendarioUnico"></div>
                    </div>
                </div>

            </div>
            <?php endif; ?>

        </div>
    </div>
</section>






        <!-- MODAL AGREGAR CITA -->
        <div id="modalAgregarCita" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <form method="post">
                <div class="modal-header" style="background:#50c878; color:white; border-bottom: none;">
                  <h5 class="modal-title w-100 text-center">Crear Nueva Cita</h5>
                  <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body px-5">
                  <input type="hidden" name="accionCita" value="registrar">

                  <div class="container">
                    <!-- CAMPOS FORMULARIO -->
                    <div class="form-group">
                      <label for="usuarioCita">Seleccionar odontólogo:</label>
                      <select class="form-control" name="usuarioCita" id="usuarioCita" required>
                        <option value="">Seleccionar odontólogo</option>
                        <?php
                        $usuarios = ControladorUsuarios::ctrMostrarUsuarios(null, null);
                        foreach ($usuarios as $usuario) {
                          // Solo mostrar usuarios cuyo perfil sea "Odontólogo"
                          if ($usuario['nombreRol'] === 'Odontologo') {
                            echo "<option value='" . $usuario['idUsuarios'] . "' data-nombre='" . $usuario['nombre'] . " " . $usuario['apellido'] . "'>" . $usuario['nombre'] . " " . $usuario['apellido'] . "</option>";
                          }
                        }
                        ?>
                      </select>
                    </div>

                    <div class="form-group position-relative">
                      <label for="buscarPaciente">Buscar Paciente:</label>
                      <input type="text" id="buscarPaciente" class="form-control" placeholder="Escribe nombre o CI del paciente..." autocomplete="off" required>
                      <ul class="list-group" id="listaPacientes" style="max-height:200px; overflow-y:auto; position:absolute; width:100%; z-index:1000; display:none;">
                        <?php
                        $pacientes = ControladorPaciente::ctrMostrarPaciente(null, null);
                        foreach ($pacientes as $paciente) {
                          echo "
                              <li class='list-group-item list-group-item-action'
                                  data-id='{$paciente['idPaciente']}'
                                  data-ci='{$paciente['ci']}'
                                  data-nombre='{$paciente['nombre']}'>
                                  {$paciente['nombre']} – CI: {$paciente['ci']}
                              </li>";
                        }
                        ?>
                      </ul>
                      <input type="hidden" name="pacienteCita" id="pacienteCita">
                    </div>


                    <div class="form-group">
                      <label for="fechaCita">Fecha:</label>
                      <input type="date" class="form-control" name="fechaCita" id="fechaCita" required>
                    </div>

                    <div class="form-group">
                      <label for="horaCita">Hora Inicio:</label>
                      <input type="time" class="form-control" name="horaCita" id="horaCita" required>
                    </div>

                    <div class="form-group">
                      <label for="horaFinCita">Hora de fin:</label>
                      <input type="time" class="form-control" name="horaFinCita" id="horaFinCita" readonly>
                    </div>

                    <div class="form-group">
                      <label for="motivoCita">Motivo de la Cita:</label>
                      <select class="form-control" name="motivoCita" id="motivoCita" required>
                        <option value="">Seleccione un motivo</option>
                        <option value="Dolor dental o sensibilidad">Dolor dental o sensibilidad</option>
                        <option value="Limpieza dental / Profilaxis">Limpieza dental / Profilaxis</option>
                        <option value="Revisión de control">Revisión de control</option>
                        <option value="Caries / Obturación">Caries / Obturación</option>
                        <option value="Extracción dental">Extracción dental</option>
                        <option value="Muelas del juicio">Muelas del juicio</option>
                        <option value="Endodoncia (tratamiento de conducto)">Endodoncia (tratamiento de conducto)</option>
                        <option value="Urgencia odontológica">Urgencia odontológica</option>
                        <option value="Sangrado o inflamación de encías">Sangrado o inflamación de encías</option>
                        <option value="Ortodoncia / Brackets">Ortodoncia / Brackets</option>
                        <option value="Blanqueamiento dental">Blanqueamiento dental</option>
                        <option value="Halitosis (mal aliento)">Halitosis (mal aliento)</option>
                        <option value="Prótesis dentales">Prótesis dentales</option>
                        <option value="Implantes dentales">Implantes dentales</option>
                        <option value="Traumatismo / Golpe dental">Traumatismo / Golpe dental</option>
                        <option value="Bruxismo / Dolor en mandíbula">Bruxismo / Dolor en mandíbula</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="estado">Estado:</label>
                      <select class="form-control" name="estado" id="estado">
                        <option value="programada">🗓️ Programada</option> <!-- Cita registrada, sin confirmación -->
                        <option value="confirmada">✅ Confirmada</option> <!-- Paciente confirmó asistencia -->
                        <option value="atendida">🦷 Atendida</option> <!-- Cita realizada, se puede generar tratamiento -->
                        <option value="cancelada">❌ Cancelada</option> <!-- Cancelada antes de la atención -->
                        <option value="no_asistio">⚠️ No asistió</option> <!-- El paciente no se presentó -->
                        <option value="reprogramada">🔁 Reprogramada</option> <!-- Movida a otra fecha -->
                      </select>
                    </div>


                    <!-- 👇 Mostrará el nombre elegido aquí -->
                    <div class="mb-3 text-center">
                      <span id="odontologoSeleccionado" style="font-weight:bold; color:#50c878;"></span>
                    </div>
                  </div>
                </div>

                <div class="modal-footer d-flex justify-content-center" style="border-top: none;">
                  <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                  <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Guardar Cita</button>
                </div>
              </form>
              <?php
              $crearCitas = new ControladorCitas();
              $crearCitas->ctrCrearCita();
              ?>
            </div>
          </div>
        </div>
        <!-- MODAL EDITAR CITA -->
        <div id="modalEditarCita" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <form method="post">
                <div class="modal-header" style="background:#50c878; color:white;">
                  <h5 class="modal-title w-100 text-center">Editar Cita</h5>
                  <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body px-5">

                  <input type="hidden" name="editarIdCita" id="editarIdCita">

                  <div class="form-group">
                    <label for="editarUsuarios">Seleccionar odontólogo:</label>
                    <select class="form-control" name="editarUsuarios" id="editarUsuarios" required>
                      <option value="">Seleccionar odontólogo</option>
                      <?php
                      $usuarios = ControladorUsuarios::ctrMostrarUsuarios(null, null);
                      foreach ($usuarios as $usuario) {
                        // Solo mostrar usuarios cuyo perfil sea "Odontólogo"
                        if ($usuario['nombreRol'] === 'Odontologo') {
                          echo "<option value='" . $usuario['idUsuarios'] . "' data-nombre='" . $usuario['nombre'] . " " . $usuario['apellido'] . "'>" . $usuario['nombre'] . " " . $usuario['apellido'] . "</option>";
                        }
                      }
                      ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="editarPaciente">Seleccionar Paciente:</label>
                    <select class="form-control" name="editarPaciente" id="editarPaciente" required>
                      <option value="">Seleccionar Paciente</option>
                      <?php
                      $pacientes = ControladorPaciente::ctrMostrarPaciente(null, null);
                      foreach ($pacientes as $paciente) {
                        echo "<option value='" . $paciente['idPaciente'] . "'>" . $paciente['nombre'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>


                  <div class="form-group">
                    <label for="editarFecha">Fecha:</label>
                    <input type="date" class="form-control" name="editarFecha" id="editarFecha" required>
                  </div>

                  <div class="form-group">
                    <label for="editarHora">Hora:</label>
                    <input type="time" class="form-control" name="editarHora" id="editarHora" required>
                  </div>

                  <div class="form-group">
                    <label for="editarHoraFin">Hora de fin:</label>
                    <input type="time" class="form-control" name="editarHoraFin" id="editarHoraFin">
                  </div>

                  <div class="form-group">
                    <label for="editarMotivo">Motivo de la Cita:</label>
                    <input type="text" class="form-control" name="editarMotivo" id="editarMotivo" required>
                  </div>
                  <div class="form-group">
                    <label for="editarEstado">Estado:</label>
                    <select class="form-control" name="editarEstado" id="editarEstado">
                      <option value="programada">🗓️ Programada</option> <!-- Cita registrada, sin confirmación -->
                      <option value="confirmada">✅ Confirmada</option> <!-- Paciente confirmó asistencia -->
                      <option value="atendida">🦷 Atendida</option> <!-- Cita realizada, se puede generar tratamiento -->
                      <option value="cancelada">❌ Cancelada</option> <!-- Cancelada antes de la atención -->
                      <option value="no_asistio">⚠️ No asistió</option> <!-- El paciente no se presentó -->
                      <option value="reprogramada">🔁 Reprogramada</option> <!-- Movida a otra fecha -->
                    </select>
                  </div>

                </div>

                <div class="modal-footer d-flex justify-content-center">
                  <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                  <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Guardar Cambios</button>
                  <button type="button" class="btn btn-outline-danger" id="btnEliminarCita" style="margin-left:10px;">Eliminar</button>

                </div>
              </form>

              <?php
              $editarCita = new ControladorCitas();
              $editarCita->ctrEditarCita();
              ?>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$eliminarCita = new ControladorCitas();
$eliminarCita->ctrEliminarCita();
?>