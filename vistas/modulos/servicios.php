<div class="wrapper">
  <div class="page-wrap">
    <div class="main-content">
      <div class="content-wrapper">

       <section class="content-header">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-concierge-bell fa-lg mr-2 text-blue-600"></i>
            Gestión de Servicios
        </h1>

        <!-- Botón Agregar Servicio -->
        <?php if (tienePermiso('crearServicios')): ?>
        <button class="btn btn-primary d-flex align-items-center justify-content-center"
            style="padding: 8px 20px; font-size: 1.1rem; height: 42px;" data-toggle="modal" data-target="#modalAgregarServicio">
            <i class="fas fa-plus mr-2"></i> Agregar Servicio
        </button>
        <?php endif; ?>
    </div>
</section>

<?php if (tienePermiso('listarServicios')): ?>
<section class="content">
    <div class="box">
        <div class="box-body">
            <table id="data_table" class="table table-hover" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $item = null;
                    $valor = null;
                    $servicios = ControladorServicios::ctrMostrarServicios($item, $valor);

                    foreach ($servicios as $value):
                    ?>
                    <tr>
                        <td><?= $value["idServicio"] ?></td>
                        <td><?= htmlspecialchars($value["nombreServicio"]) ?></td>
                        <td><?= htmlspecialchars($value["descripcion"]) ?></td>
                        <td><?= number_format($value["precio"], 2) ?> Bs</td>
                        <td>
                            <div class="btn-group">
                                <!-- Botón Editar -->
                                <?php if (tienePermiso('editarServicios')): ?>
                                <button class="icon-btn text-success mr-3 btnEditarServicio"
                                    data-toggle="modal"
                                    data-target="#modalEditarServicio"
                                    idServicio="<?= $value["idServicio"] ?>"
                                    title="Editar">
                                    <i class="fas fa-edit fa-lg"></i>
                                </button>
                                <?php endif; ?>

                                <!-- Botón Eliminar -->
                                <?php if (tienePermiso('eliminarServicios')): ?>
                                <button class="icon-btn text-danger mr-3 btnEliminarServicio"
                                    idServicio="<?= $value["idServicio"] ?>"
                                    title="Eliminar">
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

      <!-- Modal Agregar Servicio -->
      <div id="modalAgregarServicio" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <form method="post">

              <div class="modal-header" style="background:#50c878; color:white;">
                <h5 class="modal-title w-100 text-center">Agregar Servicio</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
              </div>

              <div class="modal-body px-5">
                <div class="form-group">
                  <label for="nuevoNombreServicio">Nombre del Servicio:</label>
                  <input type="text" class="form-control" name="nuevoNombreServicio" required>
                </div>
                <div class="form-group">
                  <label for="nuevaDescripcion">Descripción:</label>
                  <textarea class="form-control" name="nuevaDescripcion" rows="3" required></textarea>
                </div>
                <div class="form-group">
                  <label for="nuevoPrecio">Precio (Bs):</label>
                  <input type="text" class="form-control" name="nuevoPrecio" required>
                </div>
              </div>

              <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Guardar Servicio</button>
              </div>

            </form>

            <?php
            // Llamada al controlador para registrar servicio
            $crearServicio = new ControladorServicios();
            $crearServicio->ctrCrearServicio();
            ?>
          </div>
        </div>
      </div>

      <!-- Modal Editar Servicio -->
      <div id="modalEditarServicio" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <form method="post">

              <div class="modal-header" style="background:#50c878; color:white;">
                <h5 class="modal-title w-100 text-center">Editar Servicio</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
              </div>

              <div class="modal-body px-5">

                <input type="hidden" name="editarIdServicio" id="editarIdServicio">

                <div class="form-group">
                  <label for="editarNombreServicio">Nombre del Servicio:</label>
                  <input type="text" class="form-control" name="editarNombreServicio" id="editarNombreServicio" required>
                </div>
                <div class="form-group">
                  <label for="editarDescripcion">Descripción:</label>
                  <textarea class="form-control" name="editarDescripcion" id="editarDescripcion" rows="3" required></textarea>
                </div>
                <div class="form-group">
                  <label for="editarPrecio">Precio (Bs):</label>
                  <input type="text" class="form-control" name="editarPrecio" id="editarPrecio" required>
                </div>

              </div>

              <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Guardar Cambios</button>
              </div>

            </form>

            <?php
            // Llamada al controlador para editar servicio
            $editarServicio = new ControladorServicios();
            $editarServicio->ctrEditarServicio();
            ?>
          </div>
        </div>
      </div>

      <?php
      // Llamada al controlador para eliminar servicio
      $eliminarServicio = new ControladorServicios();
      $eliminarServicio->ctrEliminarServicio();
      ?>


    </div>
  </div>
</div>