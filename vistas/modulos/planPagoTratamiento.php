<div class="wrapper">
  <div class="page-wrap">
    <div class="main-content">
      <div class="content-wrapper">

        <section class="content-header">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="text-3xl font-bold text-gray-900">
              <i class="fas fa-credit-card fa-lg mr-2 text-green-600"></i>
              Gestión de Pagos
            </h1>

            <!-- Botón Agregar Plan de Pago -->
            <?php if (tienePermiso('crearPagos')): ?>
              <button class="btn btn-primary d-flex align-items-center justify-content-center"
                style="padding: 8px 20px; font-size: 1.1rem; height: 42px;" data-toggle="modal" data-target="#modalAgregarPlanPago">
                <i class="fas fa-plus mr-2"></i> Agregar Plan de Pago
              </button>
            <?php endif; ?>
          </div>
        </section>

        <?php if (tienePermiso('listarPagos')): ?>
          <section class="content">
            <div class="box">
              <div class="box-body">
                <table id="data_table" class="table table-hover table-bordered" width="100%">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Descripción</th>
                      <th>Descuento</th>
                      <th>Fecha</th>
                      <th>Monto</th>
                      <th>Paciente</th>
                      <th>Tipo de Pago</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $claveSecreta = "TuClaveUltraPrivada2025";
                    $planesPago = ControladorPlanPago::ctrMostrarPlanesPago(null, null);

                    foreach ($planesPago as $i => $value):
                      $codPlan = $value["codPlan"];
                      $token = hash('sha256', $codPlan . $claveSecreta);

                      $tratamiento = ControladorTratamiento::ctrMostrarTratamientos("idTratamiento", $value["idTratamiento"]);
                      $paciente = ControladorPaciente::ctrMostrarPaciente("idPaciente", $tratamiento["idPaciente"] ?? null);
                      $nombrePaciente = $paciente["nombre"] ?? "Paciente no registrado";
                      $ciPaciente = $paciente["ci"] ?? "-";

                      $tipoPago = $value["nombreTipoPago"];
                      switch (strtolower($tipoPago)) {
                        case 'pago en efectivo':
                          $badgeClass = 'badge bg-success';
                          break;
                        case 'qr de recibo':
                          $badgeClass = 'badge bg-primary';
                          break;
                        default:
                          $badgeClass = 'badge bg-secondary';
                          break;
                      }
                    ?>

                      <tr class="align-middle">
                        <td><?= $i + 1 ?></td>
                        <td style="white-space: normal; word-wrap: break-word; word-break: break-word;">
                          <?= htmlspecialchars($value["descripcion"]) ?>
                        </td>

                        <td><?= htmlspecialchars($value["descuento"]) ?>%</td>
                        <td><?= date('d/m/Y', strtotime($value["fecha"])) ?></td>
                        <td><?= number_format($value["monto"]) ?> Bs</td>
                        <td>
                          <div><?= htmlspecialchars($nombrePaciente) ?></div>
                          <small class="text-muted">CI: <?= htmlspecialchars($ciPaciente) ?></small>
                        </td>
                        <td><span class="<?= $badgeClass ?>"><?= htmlspecialchars($tipoPago) ?></span></td>
                        <td>
                          <div class="btn-group">
                            <!-- Botón Editar -->
                            <?php if (tienePermiso('editarPagos')): ?>
                              <button class="icon-btn text-success mr-3 btnEditarPlanPago" data-toggle="modal" data-target="#modalEditarPlanPago" codPlan="<?= $codPlan ?>" title="Editar">
                                <i class="fas fa-edit fa-lg"></i>
                              </button>
                            <?php endif; ?>

                            <!-- Botón Ver Recibo -->
                            <?php if (tienePermiso('verRecibo') && ($tipoPago === "QR de recibo" || strtolower($tipoPago) === "pago en efectivo")): ?>
                              <a href="vistas/modulos/reciboQR.php?codPlan=<?= $codPlan ?>&token=<?= $token ?>" target="_blank" class="icon-btn text-primary" title="Ver Recibo">
                                <i class="fas fa-file-invoice fa-lg"></i>
                              </a>
                            <?php endif; ?>

                            <!-- Botón Eliminar -->
                            <?php if (tienePermiso('eliminarPagos')): ?>
                              <button class="icon-btn text-danger mr-3 btnEliminarPlanPago" codPlan="<?= $codPlan ?>" title="Eliminar">
                                <i class="fas fa-trash fa-lg"></i>
                              </button>
                            <?php endif; ?>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </section>
        <?php endif; ?>

      </div>
      <!-- Modal Agregar Plan de Pago -->
      <div id="modalAgregarPlanPago" class="modal fade" role="dialog">
        <div class="modal-dialog" role="document" style="max-width: 95vw;">
          <div class="modal-content">
            <form method="post" id="formPlanPago">
              <div class="modal-header bg-success text-white">
                <h5 class="modal-title w-100 text-center">Agregar Plan de Pago</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
              </div>

              <div class="modal-body">
                <div class="row">

                  <!-- 📝 Columna izquierda: Datos del plan -->
                  <div class="col-md-6">
                    <div class="form-row">
                      <!-- Tercera fila: Tratamiento -->
                      <div class="form-group col-md-6">

                        <label for="nuevoTratamiento">Tratamiento:</label>
                        <select class="form-control" name="nuevoTratamiento" id="nuevoTratamiento" required>
                          <option value="">Seleccionar Tratamiento</option>
                          <?php
                          $tratamientos = ControladorTratamiento::ctrMostrarTratamientos(null, null);
                          foreach ($tratamientos as $tratamiento) {
                            $paciente = ControladorPaciente::ctrMostrarPaciente("idPaciente", $tratamiento["idPaciente"]);
                            echo '<option value="' . $tratamiento["idTratamiento"] . '" 
             data-ci="' . $paciente['ci'] . '" 
             data-nombre="' . strtolower($paciente['nombre']) . '" 
             data-saldo="' . $tratamiento["saldo"] . '">' . $paciente["nombre"] . '</option>';

                          }
                          ?>
                        </select>
                      </div>

                      <!-- Cuarta fila: Tipo de Pago -->
                      <div class="form-group col-md-6">
                        <label for="nuevoTipoPago">Tipo de Pago:</label>
                        <select class="form-control" name="nuevoTipoPago" id="nuevoTipoPago" required>
                          <option value="">Seleccionar Tipo</option>
                          <?php
                          $tiposPago = ControladorPlanPago::ctrMostrarTiposPago();
                          foreach ($tiposPago as $tipoPago) {
                            if ($tipoPago["nombreTipoPago"] === "QR de recibo" || $tipoPago["nombreTipoPago"] === "Pago en efectivo") {
                              echo '<option value="' . $tipoPago["codTipoPago"] . '">' . $tipoPago["nombreTipoPago"] . '</option>';
                            }
                          }
                          ?>
                        </select>
                      </div>
                    </div>


                    <!-- Segunda fila: Descuento, Fecha y Monto  -->
                    <div class="form-row">
                      <div class="form-group col-md-3">
                        <label for="nuevoDescuento">Descuento (%):</label>
                        <input type="number" class="form-control" name="nuevoDescuento" value="0" min="0" max="100" required>
                      </div>
                      <div class="form-group col-md-5">
                        <label for="nuevoFecha">Fecha de Registro:</label>
                        <input type="date" class="form-control" name="nuevoFecha" id="nuevoFecha" required>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="nuevoMonto">Monto:</label>
                        <input type="number" class="form-control" name="nuevoMonto" id="montoPlan" required>
                      </div>

                    </div>

                    <!-- Primera fila: Descripción y Descuento -->
                    <div class="form-row">
                      <div class="form-group col-md-12">
                        <label for="nuevoDescripcion">Descripción:</label>
                        <textarea class="form-control" name="nuevoDescripcion" rows="3" required></textarea>
                      </div>


                    </div>
                    <!-- Imagen del QR, inicialmente oculta -->
                    <div id="contenedorQR" style="margin-top:10px; display:none;">
                      <img src="vistas/img/usuarios/default/anonymous.png" alt="QR de pago" style="width:150px; height:150px;">
                    </div>

                  </div>

                  <!-- 🦷 Columna derecha: Tratamientos disponibles -->
                  <div class="col-md-6">
                    <div class="card border-info">
                      <div class="card-header bg-info text-white py-1 px-2" style="font-size:0.85rem;">
                        Tratamientos disponibles
                      </div>
                      <div class="card-body p-2" style="max-height: 400px; overflow-y:auto;">

                        <!-- 🔹 Input para buscar por CI -->
                        <div class="form-group mb-2">
                          <input type="text" id="buscarCI" class="form-control form-control-sm" placeholder="Buscar paciente por CI...">
                        </div>

                        <ul class="list-group" id="listaTratamientosDisponibles">
                          <?php foreach ($tratamientos as $tratamiento): ?>
                            <?php if ($tratamiento['estado'] === 'activo'): ?>
                              <?php
                              $paciente = ControladorPaciente::ctrMostrarPaciente("idPaciente", $tratamiento["idPaciente"]);
                              $odontologo = ControladorUsuarios::ctrMostrarUsuarios("idUsuarios", $tratamiento["idUsuarios"]);

                              $detalleServicios = ModeloTratamiento::mdlMostrarDetalleServicios(
                                'detalleTratamientoServicios',
                                'idTratamiento',
                                $tratamiento['idTratamiento']
                              );

                              $serviciosTexto = "";
                              if ($detalleServicios) {
                                $nombresServicios = [];
                                foreach ($detalleServicios as $ds) {
                                  $servicio = ModeloServicios::mdlMostrarServicios('servicios', 'idServicio', $ds['idServicio']);
                                  if ($servicio) {
                                    $nombresServicios[] = $servicio['nombreServicio'] . " (Bs. " . number_format($ds['precio'], 2) . ")";
                                  }
                                }
                                $serviciosTexto = implode(", ", $nombresServicios);
                              }
                              ?>
                              <li class="list-group-item seleccionar-tratamiento"
                                data-idtratamiento="<?= $tratamiento['idTratamiento'] ?>"
                                data-monto="<?= $tratamiento['totalPago'] ?>"
                                data-saldo="<?= $tratamiento['saldo'] ?>"
                                data-ci="<?= $paciente['ci'] ?>"
                                data-nombre="<?= strtolower($paciente['nombre']) ?>">
                                <div class="d-flex justify-content-between w-100">
                                  <div>
                                    <strong>Paciente:</strong> <?= $paciente['nombre'] ?> <br>
                                    <strong>CI:</strong> <?= $paciente['ci'] ?> <br>
                                    <strong>Odontólogo:</strong> <?= $odontologo['nombre'] ?> <?= $odontologo['apellido'] ?> <br>
                                    <strong>Servicios:</strong> <?= $serviciosTexto ?>
                                  </div>
                                  <div class="text-right">
                                    <strong>Total:</strong> Bs. <?= number_format($tratamiento['totalPago'], 2) ?> <br>
                                    <strong>Saldo:</strong> Bs. <?= number_format($tratamiento['saldo'], 2) ?>
                                  </div>
                                </div>
                              </li>
                            <?php endif; ?>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

              <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Guardar Plan de Pago</button>
              </div>
            </form>
            <?php
            $crearPlanPago = new ControladorPlanPago();
            $crearPlanPago->ctrCrearPlanPago();
            ?>
          </div>

        </div>
      </div>
      <!-- Modal Editar Plan de Pago -->
      <div id="modalEditarPlanPago" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
          <div class="modal-content">
            <form method="post" id="formEditarPlanPago">
              <div class="modal-header" style="background:#50c878; color:white;">
                <h5 class="modal-title w-100 text-center">Editar Plan de Pago</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
              </div>

              <div class="modal-body">
                <input type="hidden" id="idPlanPagoEditar" name="idPlanPagoEditar">

                <div class="form-group">
                  <label>Descripción:</label>
                  <textarea class="form-control" id="editarDescripcion" name="editarDescripcion" rows="3"></textarea>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label>Descuento (%):</label>
                    <input type="number" class="form-control" id="editarDescuento" name="editarDescuento" min="0" max="100">
                  </div>
                  <div class="form-group col-md-4">
                    <label>Fecha:</label>
                    <input type="date" class="form-control" id="editarFecha" name="editarFecha">
                  </div>
                  <div class="form-group col-md-4">
                    <label>Monto:</label>
                    <input type="number" class="form-control" id="editarMonto" name="editarMonto">
                  </div>
                </div>

                <div class="form-group">
                  <label>Tipo de Pago:</label>
                  <select class="form-control" id="editarTipoPago" name="editarTipoPago">
                    <?php
                    $tiposPago = ControladorPlanPago::ctrMostrarTiposPago();
                    foreach ($tiposPago as $tipo) {
                      echo '<option value="' . $tipo["codTipoPago"] . '">' . $tipo["nombreTipoPago"] . '</option>';
                    }
                    ?>
                  </select>
                </div>

              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Actualizar Plan de Pago</button>
              </div>
            </form>
            <?php
            $editarPlan = new ControladorPlanPago();
            $editarPlan->ctrEditarPlanPago();
            ?>
          </div>
        </div>
      </div>
      <?php
      $borrarPaciente = new ControladorPlanPago();
      $borrarPaciente->ctrEliminarPlanPago();
      ?>
    </div>