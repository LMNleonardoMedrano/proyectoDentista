<div class="wrapper">
  <div class="page-wrap">
    <div class="main-content">
      <div class="content-wrapper">

        <section class="content-header">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-pills fa-lg mr-2 text-green-600"></i>
            Gestión de Medicamentos
        </h1>

        <!-- Botón Agregar solo si tiene permiso crearMedicamentos -->
        <?php if (tienePermiso('crearMedicamentos')): ?>
        <button class="btn btn-primary d-flex align-items-center justify-content-center"
            style="padding: 8px 20px; font-size: 1.1rem; height: 42px;" 
            data-toggle="modal" data-target="#modalAgregarMedicamento">
            <i class="fas fa-plus mr-2"></i> Agregar Medicamento
        </button>
        <?php endif; ?>
    </div>
</section>

<section class="content">
    <div class="box">

        <div class="box-body">
            <!-- Mostrar tabla solo si tiene permiso listarMedicamentos -->
            <?php if (tienePermiso('listarMedicamentos')): ?>
            <table id="data_table" class="table table-hover" width="100%">
                <thead>
                    <tr>
                        <th>Medicamento</th>
                        <th>Tipo</th>
                        <th>Medida</th>
                        <th>Frecuencia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $item = null;
                    $valor = null;
                    $medicamentos = ControladorMedicamento::ctrMostrarMedicamentos($item, $valor);

                    foreach ($medicamentos as $key => $value): ?>
                    <tr>
                        <!-- Medicamento con icono -->
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center"
                                    style="width:40px; height:40px;">
                                    <i class="fas fa-pills text-white"></i>
                                </div>
                                <div class="ml-3">
                                    <span class="font-weight-bold text-dark">
                                        <?= htmlspecialchars($value['nombre']) ?>
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Tipo con badge -->
                        <td>
                            <?php
                            $tipo = strtolower($value['tipo']);
                            $badgeClass = 'badge-default';
                            if ($tipo == 'antiinflamatorio') $badgeClass = 'badge-antiinflamatorio';
                            elseif ($tipo == 'analgésico') $badgeClass = 'badge-analgésico';
                            elseif ($tipo == 'antibiótico') $badgeClass = 'badge-antibiótico';
                            ?>
                            <span class="badge-pill <?= $badgeClass ?>">
                                <?= htmlspecialchars($value['tipo'] ?: 'Sin clasificar') ?>
                            </span>
                        </td>

                        <!-- Medida -->
                        <td class="text-dark">
                            <?= htmlspecialchars($value['medida'] ?: 'N/A') ?>
                        </td>

                        <!-- Frecuencia -->
                        <td>
                            <div class="d-flex align-items-center text-muted">
                                <i class="fas fa-clock mr-2"></i>
                                <?= htmlspecialchars($value['tiempo'] ?: 'N/A') ?>
                            </div>
                        </td>

                        <!-- Acciones -->
                        <td>
                            <div class="d-flex">
                                <!-- Editar solo si tiene permiso editarMedicamentos -->
                                <?php if (tienePermiso('editarMedicamentos')): ?>
                                <button class="icon-btn text-success mr-3 btnEditarMedicamento"
                                    data-toggle="modal"
                                    data-target="#modalEditarMedicamento"
                                    codMedicamento="<?= $value['codMedicamento'] ?>" title="Editar">
                                    <i class="fas fa-edit fa-lg"></i>
                                </button>
                                <?php endif; ?>

                                <!-- Eliminar solo si tiene permiso eliminarMedicamentos -->
                                <?php if (tienePermiso('eliminarMedicamentos')): ?>
                                <button class="icon-btn text-danger mr-3 btnEliminarMedicamento"
                                    codMedicamento="<?= $value['codMedicamento'] ?>" title="Eliminar">
                                    <i class="fas fa-trash fa-lg"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</section>

      </div>

      <!-- Modal Agregar Medicamento -->
      <div id="modalAgregarMedicamento" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <form method="post">
              <div class="modal-header" style="background:#50c878; color:white;">
                <h5 class="modal-title w-100 text-center">Agregar Medicamento</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body px-5">
                <div class="form-group">
                  <label for="nuevoNombre">Nombre:</label>
                  <input type="text" class="form-control" name="nuevoNombre" placeholder="Ej: Ibuprofeno" required>
                </div>
                <div class="form-group">
                  <label for="nuevoTipo">Tipo:</label>
                  <input type="text" class="form-control" name="nuevoTipo" required>
                </div>
                <div class="form-group">
                  <label for="nuevaMedida">Medida:</label>
                  <input type="text" class="form-control" name="nuevaMedida" placeholder="Ej: 400mg, 500ml" required>
                </div>
                <div class="form-group">
                  <label for="nuevoTiempo">Tiempo:</label>
                  <input type="text" class="form-control" name="nuevoTiempo" placeholder="Ej: Cada 8 horas, 3 veces al día" required>
                </div>
              </div>
              <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Guardar Medicamento</button>
              </div>
            </form>

            <?php
            $crearMedicamento = new ControladorMedicamento();
            $crearMedicamento->ctrCrearMedicamento();
            ?>
          </div>
        </div>
      </div>

      <!-- Modal Editar Medicamento -->
      <div id="modalEditarMedicamento" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <form method="post">
              <div class="modal-header" style="background:#50c878; color:white;">
                <h5 class="modal-title w-100 text-center">Editar Medicamento</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body px-5">

                <input type="hidden" name="editarCodMedicamento" id="editarCodMedicamento">

                <div class="form-group">
                  <label for="editarNombre">Nombre:</label>
                  <input type="text" class="form-control" name="editarNombre" id="editarNombre" required>
                </div>
                <div class="form-group">
                  <label for="editarTipo">Tipo:</label>
                  <input type="text" class="form-control" name="editarTipo" id="editarTipo" required>
                </div>
                <div class="form-group">
                  <label for="editarMedida">Medida:</label>
                  <input type="text" class="form-control" name="editarMedida" id="editarMedida" required>
                </div>
                <div class="form-group">
                  <label for="editarTiempo">Tiempo:</label>
                  <input type="text" class="form-control" name="editarTiempo" id="editarTiempo" required>
                </div>

              </div>
              <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Guardar Cambios</button>
              </div>
            </form>

            <?php
            $editarMedicamento = new ControladorMedicamento();
            $editarMedicamento->ctrEditarMedicamento();
            ?>
          </div>
        </div>
      </div>

      <?php
      $eliminarMedicamento = new ControladorMedicamento();
      $eliminarMedicamento->ctrEliminarMedicamento();
      ?>
    </div>
  </div>
</div>