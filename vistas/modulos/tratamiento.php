<div class="wrapper">
    <div class="page-wrap">
        <div class="main-content">
           <div class="content-wrapper">
    <section class="content-header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-clipboard-list fa-lg mr-2 text-purple-600"></i>
                Gestión de Tratamientos
            </h1>

            <?php if (tienePermiso('crearTratamiento')): ?>
            <button class="btn btn-primary d-flex align-items-center justify-content-center"
                style="padding: 8px 20px; font-size: 1.1rem; height: 42px;" data-toggle="modal" data-target="#modalAgregarTratamiento">
                <i class="fas fa-plus mr-2"></i> Agregar Tratamiento
            </button>
            <?php endif; ?>
        </div>
    </section>

    <!-- SECCIÓN PRINCIPAL -->
    <section class="content">
        <div class="box">

            <!-- NAV TABS PRINCIPALES -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabTratamientos" role="tab">🩺 Tratamientos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabMedicamentos" role="tab">💊 Medicamentos</a>
                </li>
            </ul>

            <!-- CONTENIDO DE LAS PESTAÑAS -->
            <div class="tab-content">

                <!-- TAB 1: TRATAMIENTOS -->
                <div class="tab-pane fade show active" id="tabTratamientos" role="tabpanel">
                    <?php if (tienePermiso('listarTratamiento')): ?>
                    <h4 class="text-center mb-3">Todos los tratamientos registrados</h4>
                    <div class="table-responsive">
                        <?php
                        $tratamientos = ControladorTratamiento::ctrMostrarTratamientos(null, null);
                        ?>
                        <table id="data_table" class="table table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Paciente</th>
                                    <th>Odontólogo</th>
                                    <th>Total/Saldo</th>
                                    <th>Estado</th>
                                    <th>Estado Pago</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($tratamientos)): ?>
                                    <?php foreach ($tratamientos as $i => $t): ?>
                                        <tr data-id="<?= $t['idTratamiento'] ?>">
                                            <td><?= $i + 1 ?></td>
                                            <td><?= htmlspecialchars($t['fechaRegistro']) ?></td>
                                            <td>
                                                <div>
                                                    <div style="color:#000; font-weight:bold;"><?= htmlspecialchars($t['nombrePaciente']); ?></div>
                                                    <div style="font-size:0.9rem; color:#555;">CI: <?= htmlspecialchars($t['ciPaciente']); ?></div>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($t['nombreUsuario'] . ' ' . $t['apellidoUsuario']) ?></td>
                                            <td>
                                                <div>
                                                    <div style="color:rgb(5 150 105); font-weight:bold;"><?= number_format($t['saldo']); ?> Bs</div>
                                                    <div style="font-size:0.9rem; color:rgb(220 38 38);">Saldo: <?= number_format($t['totalPago']); ?> Bs</div>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($t['estado']) ?></td>
                                            <td><?= htmlspecialchars($t['estadoPago']) ?></td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <?php if (tienePermiso('editarTratamiento')): ?>
                                                   <button class="icon-btn text-primary btnEditarTratamiento"
    data-toggle="modal"
    data-target="#modalEditarTratamiento"
    idTratamiento="<?= $t['idTratamiento'] ?>"
    title="Editar">
    <i class="fas fa-edit fa-lg"></i>
</button>

                                                    <?php endif; ?>

                                                    <button class="icon-btn text-info btnMedicamentosServicios"
                                                        data-id="<?= $t['idTratamiento'] ?>"
                                                        title="Agregar medicamentos">
                                                        <i class="ik ik-plus-circle fa-lg"></i>
                                                    </button>

                                                    <button class="icon-btn text-secondary btnVerPagos"
                                                        data-toggle="modal"
                                                        data-target="#modalPagosTratamiento"
                                                        title="ver pagos realizados"
                                                        data-idtratamiento="<?= $t["idTratamiento"] ?>">
                                                        <i class="ik ik-file-text fa-lg"></i> Ver
                                                    </button>

                                                    <?php if (tienePermiso('eliminarTratamiento')): ?>
                                                    <button class="icon-btn text-danger btnEliminarTratamiento"
                                                        title="Eliminar"
                                                        idTratamiento="<?= $t['idTratamiento'] ?>">
                                                        <i class="fas fa-trash fa-lg"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No hay tratamientos registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <p class="text-center text-muted mt-3">No tienes permiso para ver los tratamientos.</p>
                    <?php endif; ?>
                </div>

                <!-- TAB 2: MEDICAMENTOS -->
                <div class="tab-pane fade" id="tabMedicamentos" role="tabpanel">
                    <h4 class="text-center mb-3">Medicamentos asociados a los tratamientos</h4>
                    <div class="table-responsive">
                        <?php
                        $detalles = ControladorTratamiento::ctrMostrarDetalleMedicamento();
                        $claveSecreta = "TuClaveUltraPrivada2025";
                        ?>
                        <table id="data_table_medicamentos" class="table table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tratamiento</th>
                                    <th>Medicamento</th>
                                    <th>Dosis</th>
                                    <th>Inicio</th>
                                    <th>Final</th>
                                    <th>Tiempo</th>
                                    <th>Observación</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($detalles)): ?>
                                    <?php foreach ($detalles as $i => $d):
                                        $token = hash('sha256', $d["idTratamiento"] . $claveSecreta);
                                    ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= htmlspecialchars($d['nombrePaciente']) ?></td>
                                            <td><?= htmlspecialchars($d['nombreMedicamento']) ?></td>
                                            <td><?= htmlspecialchars($d['dosis']) ?></td>
                                            <td><?= htmlspecialchars($d['fechaInicio']) ?></td>
                                            <td><?= htmlspecialchars($d['fechaFinal']) ?></td>
                                            <td><?= htmlspecialchars($d['tiempo']) ?></td>
                                            <td><?= htmlspecialchars($d['observacion']) ?></td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <?php if (tienePermiso('editarTratamiento')): ?>
                                                   <button class="icon-btn text-primary btnEditarMedicamento"
        data-toggle="modal"
        data-target="#modalMedicamentos"
        idTratamiento="<?= $d['idTratamiento'] ?>" title="Editar">
    <i class="fas fa-edit fa-lg"></i>
</button>

                                                    <?php endif; ?>

                                                    <a href="vistas/modulos/reciboMedicamento.php?idTratamiento=<?= $d['idTratamiento'] ?>&token=<?= $token ?>"
                                                        target="_blank"
                                                        class="icon-btn text-info"
                                                        title="Ver Recibo">🧾
                                                    </a>

                                                    <?php if (tienePermiso('eliminarTratamiento')): ?>
                                                    <button class="icon-btn text-danger btnEliminarTratamiento"
                                                        idTratamiento="<?= $d['idTratamiento'] ?>">
                                                        <i class="fas fa-trash fa-lg"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No hay medicamentos registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div><!-- /.tab-content -->
        </div><!-- /.box -->
    </section>
</div>


               
                <!-- MODAL DE PAGOS -->

                <div class="modal fade" id="modalPagosTratamiento" tabindex="-1" role="dialog" aria-labelledby="modalPagosLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Pagos realizados</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="contenidoPagosTratamiento">
                                <!-- Aquí se cargará la tabla vía Ajax -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Agregar Tratamiento -->
                <div id="modalAgregarTratamiento" class="modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document" style="max-width: 95vw;">
                        <div class="modal-content">
                            <form method="post">

                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title w-100 text-center">Agregar Tratamiento</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                </div>

                                <input type="hidden" name="nuevoFechaRegistro" value="<?php echo date('Y-m-d'); ?>">

                                <div class="modal-body">
                                    <div class="row">
                                        <!-- 🦷 Columna izquierda: Formulario -->
                                        <div class="col-md-6">
                                            <!-- Paciente y Odontólogo -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label>Paciente:</label>
                                                    <select class="form-control" name="nuevoIdPaciente" required>
                                                        <option value="">-- Seleccione Paciente --</option>
                                                        <?php
                                                        $pacientes = ControladorPaciente::ctrMostrarPaciente(null, null);
                                                        foreach ($pacientes as $paciente) {
                                                            echo "<option value='{$paciente['idPaciente']}'>{$paciente['nombre']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Odontólogo:</label>
                                                    <select class="form-control" name="nuevoIdUsuarios" required>
                                                        <option value="">-- Seleccione Odontólogo --</option>
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
                                            </div>

                                            <!-- Agregar Servicios -->
                                            <div class="card mb-3 border-primary">
                                                <div class="card-header bg-primary text-white py-1 px-2" style="font-size:0.8rem;">Agregar Servicio</div>
                                                <div class="card-body p-2">
                                                    <div class="form-row align-items-center">
                                                        <div class="col-md-7 mb-2">
                                                            <select class="form-control" id="selectServicioForm">
                                                                <option value="">-- Seleccione un servicio --</option>
                                                                <?php
                                                                $servicios = ControladorServicios::ctrMostrarServicios(null, null);
                                                                foreach ($servicios as $servicio) {
                                                                    echo "<option value='{$servicio['idServicio']}' data-precio='{$servicio['precio']}'>{$servicio['nombreServicio']} - Bs. {$servicio['precio']}</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2 mb-2">
                                                            <button type="button" class="btn btn-success btn-block btn-sm" id="btnAgregarServicioForm">Añadir</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Lista de servicios -->
                                            <div class="card mb-3 border-secondary">
                                                <div class="card-header bg-secondary text-white py-1 px-2" style="font-size:0.8rem;">Servicios Seleccionados</div>
                                                <ul id="listaServicios" class="list-group list-group-flush p-2"></ul>
                                            </div>

                                            <!-- Total y Saldo -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label>Total Pago:</label>
                                                    <input type="number" step="0.01" class="form-control" name="nuevoTotalPago" id="totalPago" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Saldo:</label>
                                                    <input type="number" step="0.01" class="form-control" name="nuevoSaldo" id="saldo" readonly>
                                                </div>
                                            </div>

                                            <!-- Estado y Estado Pago -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label>Estado:</label>
                                                    <select class="form-control" name="nuevoEstado" required>
                                                        <option value="activo">Activo</option>
                                                        <option value="completado">Completado</option>
                                                        <option value="cancelado">Cancelado</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Estado Pago:</label>
                                                    <select class="form-control" name="nuevoEstadoPago" required>
                                                        <option value="pendiente">Pendiente</option>
                                                        <option value="parcial">Parcial</option>
                                                        <option value="pagado">Pagado</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 📅 Columna derecha: Citas programadas -->
                                        <div class="col-md-6">
                                            <div class="card border-info mb-3">
                                                <div class="card-header bg-info text-white py-1 px-2" style="font-size:0.8rem;">
                                                    Citas confirmadas
                                                </div>
                                                <div class="card-body p-2" style="max-height: 400px; overflow-y: auto;">

                                                    <!-- 🔹 Input para buscar por CI -->
                                                    <div class="form-group mb-2">
                                                        <input type="text" id="buscarCICitas" class="form-control form-control-sm" placeholder="Buscar paciente por CI...">
                                                    </div>

                                                    <ol class="list-group list-group-numbered list-group-flush p-2" id="listaCitasConfirmadas">
                                                        <?php
                                                        $citas = ControladorCitas::ctrMostrarCitas(null, null);

                                                        foreach ($citas as $cita) {
                                                            if ($cita['estado'] === 'confirmada') {
                                                                $paciente = ControladorPaciente::ctrMostrarPaciente("idPaciente", $cita['idPaciente']);
                                                                $odontologo = ControladorUsuarios::ctrMostrarUsuarios("idUsuarios", $cita['idUsuarios']);

                                                                echo "
            <li class='list-group-item seleccionar-cita px-3 py-2'  
    data-idpaciente='{$paciente['idPaciente']}'  
    data-idusuario='{$odontologo['idUsuarios']}'  
    data-idcita='{$cita['idCita']}'
    data-ci='{$paciente['ci']}'
    data-nombre='{$paciente['nombre']}'
    style='background-color:#F8F9FA; color:#000000; border-radius:5px; margin-bottom:5px;'>

    <div style='font-size:0.9rem; font-weight:bold; margin-bottom:3px;'>
        ID {$paciente['idPaciente']} – {$paciente['nombre']} | CI: {$paciente['ci']}
    </div>
    <div style='font-size:0.85rem; margin-bottom:2px;'>
        Odontólogo: {$odontologo['nombre']} {$odontologo['apellido']}
    </div>
    <div style='font-size:0.85rem; margin-bottom:2px;'>
        Motivo: {$cita['motivoConsulta']}
    </div>
    <div style='font-size:0.85rem; color:#555;'>
        Fecha: {$cita['fecha']} | Hora: {$cita['hora']} - {$cita['horaFin']}
    </div>
</li>
            ";
                                                            }
                                                        }
                                                        ?>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer justify-content-center">
                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-success">Guardar Tratamiento</button>
                                </div>
                                <input type="hidden" name="idCitaSeleccionada" id="idCitaSeleccionada">

                            </form>
                            <?php
                            $crearTratamiento = new ControladorTratamiento();
                            $crearTratamiento->ctrCrearTratamiento();
                            ?>
                        </div>
                    </div>
                </div>


                <div id="modalMedicamentos" class="modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <form method="post" id="formMedicamentos">

                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title">Medicamentos del Tratamiento #<span id="ms_id_title"></span></h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                </div>

                                <input type="hidden" name="idTratamientoMedicamentos" id="idTratamientoMedicamentos">

                                <div class="modal-body">

                                    <!-- Sección Medicamentos -->
                                    <div class="card mb-3 border-primary">
                                        <div class="card-header bg-primary text-white py-1 px-2" style="font-size:0.8rem;">Agregar Medicamento</div>
                                        <div class="card-body p-2">
                                            <div class="form-row align-items-center">
                                                <div class="col-md-3 mb-2">
                                                    <select class="form-control" id="selectMedicamentoForm">
                                                        <option value="">-- Seleccione medicamento --</option>
                                                        <?php
                                                        $medicamentos = ControladorMedicamento::ctrMostrarMedicamentos(null, null);
                                                        foreach ($medicamentos as $m) {
                                                            echo "<option value='{$m['codMedicamento']}'>{$m['nombre']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <input type="text" class="form-control" id="dosisMedicamento" placeholder="Dosis">
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <input type="date" class="form-control" id="fechaInicioMedicamento">
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <input type="date" class="form-control" id="fechaFinalMedicamento">
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <input type="text" class="form-control" id="tiempoMedicamento" placeholder="Tiempo">
                                                </div>
                                                <div class="col-md-1 mb-2">
                                                    <button type="button" class="btn btn-success btn-block btn-sm" id="btnAgregarMedicamento">+</button>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-12">
                                                    <textarea class="form-control" id="observacionMedicamento" rows="1" placeholder="Observación"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lista temporal Medicamentos -->
                                    <div class="card mb-2 border-secondary">
                                        <div class="card-header bg-secondary text-white py-1 px-2" style="font-size:0.8rem;">Medicamentos a guardar</div>
                                        <ul id="listaTemporalMedicamentos" class="list-group list-group-flush p-2"></ul>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Guardar Medicamentos</button>
                                </div>
                            </form>

                            <?php
                            $guardarMedicamentos = new ControladorTratamiento();
                            $guardarMedicamentos->ctrGuardarMedicamentos();
                            ?>

                        </div>
                    </div>
                </div>


<!-- Modal Editar Medicamentos -->
<div id="modalEditarMedicamentos" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" id="formEditarMedicamentos">

                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Medicamentos del Tratamiento #<span id="ms_edit_id_title"></span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <input type="hidden" name="editarIdTratamientoMedicamentos" id="editarIdTratamientoMedicamentos">

                <div class="modal-body">

                    <!-- Sección Medicamentos -->
                    <div class="card mb-3 border-primary">
                        <div class="card-header bg-primary text-white py-1 px-2" style="font-size:0.8rem;">Editar Medicamento</div>
                        <div class="card-body p-2">
                            <div class="form-row align-items-center">
                                <div class="col-md-3 mb-2">
                                    <select class="form-control" id="editarSelectMedicamentoForm">
                                        <option value="">-- Seleccione medicamento --</option>
                                        <?php
                                        $medicamentos = ControladorMedicamento::ctrMostrarMedicamentos(null, null);
                                        foreach ($medicamentos as $m) {
                                            echo "<option value='{$m['codMedicamento']}'>{$m['nombre']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <input type="text" class="form-control" id="editarDosisMedicamento" placeholder="Dosis">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <input type="date" class="form-control" id="editarFechaInicioMedicamento">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <input type="date" class="form-control" id="editarFechaFinalMedicamento">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <input type="text" class="form-control" id="editarTiempoMedicamento" placeholder="Tiempo">
                                </div>
                                <div class="col-md-1 mb-2">
                                    <button type="button" class="btn btn-success btn-block btn-sm" id="btnEditarAgregarMedicamento">+</button>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12">
                                    <textarea class="form-control" id="editarObservacionMedicamento" rows="1" placeholder="Observación"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista temporal Medicamentos -->
                    <div class="card mb-2 border-secondary">
                        <div class="card-header bg-secondary text-white py-1 px-2" style="font-size:0.8rem;">Medicamentos a guardar</div>
                        <ul id="listaTemporalEditarMedicamentos" class="list-group list-group-flush p-2"></ul>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>

            <?php
            $editarMedicamentos = new ControladorTratamiento();
            $editarMedicamentos->ctrEditarMedicamentos();
            ?>

        </div>
    </div>
</div>


<!-- Modal Editar Tratamiento -->
<div id="modalEditarTratamiento" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header" style="background:#50c878; color:white;">
          <h5 class="modal-title w-100 text-center">Editar Tratamiento</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body px-5">

          <input type="hidden" name="editarIdTratamiento" id="editarIdTratamiento">

          <div class="form-group">
            <label for="editarIdPaciente">Paciente:</label>
            <select class="form-control" name="editarIdPaciente" id="editarIdPaciente" required>
              <?php
              $pacientes = ControladorPaciente::ctrMostrarPaciente(null, null);
              foreach ($pacientes as $paciente) {
                  echo "<option value='{$paciente['idPaciente']}'>{$paciente['nombre']}</option>";
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label for="editarIdUsuarios">Odontólogo:</label>
            <select class="form-control" name="editarIdUsuarios" id="editarIdUsuarios" required>
              <?php
              $usuarios = ControladorUsuarios::ctrMostrarUsuarios(null, null);
              foreach ($usuarios as $usuario) {
                  echo "<option value='{$usuario['idUsuarios']}'>{$usuario['nombre']}</option>";
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label for="editarSaldo">Saldo:</label>
            <input type="number" step="0.01" class="form-control" name="editarSaldo" id="editarSaldo" required>
          </div>

          <div class="form-group">
            <label for="editarTotalPago">Total Pago:</label>
            <input type="number" step="0.01" class="form-control" name="editarTotalPago" id="editarTotalPago" required>
          </div>

          <div class="form-group">
            <label for="editarEstado">Estado:</label>
            <select class="form-control" name="editarEstado" id="editarEstado" required>
                <option value="activo">Activo</option>
                <option value="completado">Completado</option>
                <option value="cancelado">Cancelado</option>
            </select>
          </div>

          <div class="form-group">
            <label for="editarEstadoPago">Estado Pago:</label>
            <select class="form-control" name="editarEstadoPago" id="editarEstadoPago" required>
              <option value="pendiente">Pendiente</option>
              <option value="parcial">Parcial</option>
              <option value="pagado">Pagado</option>
            </select>
          </div>

        </div>

        <div class="modal-footer d-flex justify-content-center">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Guardar Cambios</button>
        </div>
      </form>

      <?php
      $editarTratamiento = new ControladorTratamiento();
      $editarTratamiento->ctrEditarTratamiento();
      ?>
    </div>
  </div>
</div>



              
            </div>
        </div>
    </div>
      <?php
                $eliminarTratamiento = new ControladorTratamiento();
                $eliminarTratamiento->ctrEliminarTratamiento();
                ?>