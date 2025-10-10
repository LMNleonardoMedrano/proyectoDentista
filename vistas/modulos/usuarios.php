<div class="wrapper">
  <div class="page-wrap">
    <div class="main-content">

      <div class="page-wrap">
    <section class="content-header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-users-cog fa-lg mr-2 text-purple-600"></i>
                Gestión de Usuarios
            </h1>

            <!-- Botón Agregar solo si tiene permiso crearUsuarios -->
            <?php if (tienePermiso('crearUsuarios')): ?>
            <button class="btn btn-primary d-flex align-items-center justify-content-center"
                style="padding: 8px 20px; font-size: 1.1rem; height: 42px;" 
                data-toggle="modal" data-target="#modalAgregarUsuario">
                <i class="fas fa-plus mr-2"></i> Agregar usuario
            </button>
            <?php endif; ?>
        </div>
    </section>

    <section class="content">

        <div class="box">

            <div class="box-header with-border"></div>

            <div class="box-body">
                <!-- Tabla solo si tiene permiso listarUsuarios -->
                <?php if (tienePermiso('listarUsuarios')): ?>
                <table id="data_table" class="table dataTable tablaUsuarios" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Usuario</th>
                            <th>Información Personal</th>
                            <th>Perfil</th>
                            <th>Estado</th>
                            <th>Último Login</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $item = null;
                        $valor = null;
                        $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

                        foreach ($usuarios as $key => $value):
                        ?>
                        <tr class="align-middle">
                            <!-- ID -->
                            <td><?php echo $value["idUsuarios"]; ?></td>

                            <!-- Usuario (con iniciales y correo) -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0" style="width:40px; height:40px;">
                                        <?php if (!empty($value['foto'])): ?>
                                        <img src="<?php echo $value['foto']; ?>" class="rounded-circle"
                                            style="width:40px; height:40px; object-fit:cover;" alt="Foto">
                                        <?php else: ?>
                                        <div class="rounded-circle bg-purple d-flex justify-content-center align-items-center"
                                            style="width:40px; height:40px;">
                                            <span class="text-white font-weight-bold">
                                                <?php echo substr($value['nombre'], 0, 1) . substr($value['apellido'], 0, 1); ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-2">
                                        <div><?php echo htmlspecialchars($value['usuario']); ?></div>
                                        <div class="text-muted small"><?php echo htmlspecialchars($value['correo']); ?></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Información Personal -->
                            <td>
                                <div><?php echo htmlspecialchars($value['nombre'] . ' ' . $value['apellido']); ?></div>
                                <div class="text-muted small">CI: <?php echo htmlspecialchars($value['ci']); ?></div>
                                <div class="text-muted small">Domicilio: <?php echo htmlspecialchars($value['domicilio']); ?></div>
                            </td>

                            <!-- Perfil con color -->
                            <td>
                                <?php
                                $colorPerfil = match ($value['nombreRol']) {
                                    'administrador' => 'badge bg-danger text-white',
                                    'Odontologo' => 'badge bg-primary text-white',
                                    'Recepcionista' => 'badge bg-success text-white',
                                    default => 'badge bg-secondary text-white',
                                };
                                ?>
                                <span class="<?php echo $colorPerfil; ?>"><?php echo ucfirst($value['nombreRol']); ?></span>
                            </td>

                            <!-- Estado -->
                            <td>
                                <?php if ($value['estado'] != 0): ?>
                                <button class="btn btn-success btn-xs btnActivar"
                                    idUsuarios="<?php echo $value['idUsuarios']; ?>" estadoUsuario="0">Activado</button>
                                <?php else: ?>
                                <button class="btn btn-danger btn-xs btnActivar"
                                    idUsuarios="<?php echo $value['idUsuarios']; ?>" estadoUsuario="1">Desactivado</button>
                                <?php endif; ?>
                            </td>

                            <!-- Último login -->
                            <td><?php echo $value['ultimoLogin'] ? date('d/m/Y H:i', strtotime($value['ultimoLogin'])) : 'Nunca'; ?></td>

                            <!-- Acciones con permisos -->
                            <td>
                                <div class="btn-group">

                                    <!-- Editar solo si tiene permiso editarUsuarios -->
                                    <?php if (tienePermiso('editarUsuarios')): ?>
                                    <button class="icon-btn text-success mr-3 btnEditarUsuario"
                                        idUsuarios="<?php echo $value["idUsuarios"]; ?>"
                                        data-toggle="modal"
                                        data-target="#modalEditarUsuario"
                                        title="Editar">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </button>
                                    <?php endif; ?>

                                    <!-- Eliminar solo si tiene permiso eliminarUsuarios y no es el mismo usuario -->
                                    <?php if ($value['idUsuarios'] != $_SESSION['id'] && tienePermiso('eliminarUsuarios')): ?>
                                    <button class="icon-btn text-danger mr-3 btnEliminarUsuario"
                                        idUsuarios="<?php echo $value["idUsuarios"]; ?>"
                                        fotoUsuario="<?php echo $value["foto"]; ?>"
                                        usuario="<?php echo $value["usuario"]; ?>"
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
                <?php endif; ?>
            </div>

        </div>

    </section>

</div>


      <!--=====================================
MODAL AGREGAR USUARIO
======================================-->

      <div id="modalAgregarUsuario" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="max-width:85vw;">
          <div class="modal-content">

            <form role="form" method="post" enctype="multipart/form-data">

              <!--=====================================
        CABECERA DEL MODAL
        ======================================-->
              <div class="modal-header" style="background:#50c878; color:white; border-bottom: none;">
                <h5 class="modal-title w-100 text-center">Crear Nuevo Usuario</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
              </div>

              <!--=====================================
        CUERPO DEL MODAL
        ======================================-->
              <div class="modal-body px-4" style="max-height:75vh; overflow-y:auto;">
                <div class="container">

                  <fieldset class="mb-4">
                    <legend>Información Personal</legend>
                    <div class="row">
                      <div class="col-md-6 form-group">
                        <label for="nuevoNombre">Nombre Completo:</label>
                        <input type="text" class="form-control" name="nuevoNombre" placeholder="Escribe el nombre" required>
                      </div>
                      <div class="col-md-6 form-group">
                        <label for="nuevoApellido">Apellido Completo:</label>
                        <input type="text" class="form-control" name="nuevoApellido" placeholder="Escribe el apellido" required>
                      </div>
                      <div class="col-md-4 form-group">
                        <label for="nuevoCi">C.I.:</label>
                        <input type="text" class="form-control" name="nuevoCi" placeholder="Escribe el C.I." required>
                      </div>
                      <div class="col-md-4 form-group">
                        <label for="nuevoDomicilio">Domicilio:</label>
                        <input type="text" class="form-control" name="nuevoDomicilio" placeholder="Escribe el domicilio" required>
                      </div>
                      <div class="col-md-4 form-group">
                        <label for="nuevoFecha">Fecha de registro:</label>
                        <input type="date" class="form-control" name="nuevoFecha" required>
                      </div>
                    </div>
                  </fieldset>

                  <fieldset class="mb-4">
                    <legend>Datos de Acceso</legend>
                    <div class="row">
                      <div class="col-md-6 form-group">
                        <label for="nuevoUsuario">Nombre de Usuario:</label>
                        <input type="text" class="form-control" name="nuevoUsuario" placeholder="Escribe el nombre de usuario" required>
                      </div>
                      <div class="col-md-6 form-group">
                        <label for="nuevoCorreo">Correo:</label>
                        <input type="email" class="form-control" name="nuevoCorreo" placeholder="Escribe su correo" required>
                      </div>
                      <div class="col-md-6 form-group">
                        <label for="nuevoPassword">Contraseña:</label>
                        <input type="password" class="form-control" name="nuevoPassword" placeholder="Escribe la contraseña" required>
                      </div>
                      <div class="col-md-6 form-group">
                        <label for="nuevoPerfil">Selecciona un Perfil:</label>
                        <select class="form-control" name="nuevoPerfil" required>
                          <option value="" disabled selected>Seleccione un perfil</option>
                          <?php
                          // Mostrar los Paciente disponibles
                          $roles = ControladorRoles::ctrMostrarRoles($item, $valor);
                          foreach ($roles as $rol) {
                            echo '<option value="' . $rol["idRol"] . '">' . $rol["nombreRol"] . '</option>';
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                  </fieldset>

                  <fieldset class="mb-4">
                    <legend>Foto de Perfil</legend>
                    <div class="form-group text-center">
                      <input type="file" class="form-control-file nuevaFoto mt-2" name="nuevaFoto">
                      <small class="form-text text-muted mb-2">Tamaño máximo: 2 MB</small>
                      <img src="vistas/img/usuarios/default/anonymous.png" class="img-thumbnail previsualizar" width="120px">
                    </div>
                  </fieldset>

                </div>
              </div>

              <!--=====================================
        PIE DEL MODAL
        ======================================-->
              <div class="modal-footer d-flex justify-content-center" style="border-top: none;">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Crear Usuario</button>
              </div>

              <?php
              $crearUsuario = new ControladorUsuarios();
              $crearUsuario->ctrCrearUsuario();
              ?>

            </form>
          </div>
        </div>
      </div>

      <!--=====================================
MODAL EDITAR USUARIO
======================================-->

      <div id="modalEditarUsuario" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="max-width:85vw;">
          <div class="modal-content">

            <form role="form" method="post" enctype="multipart/form-data">

              <!-- CABECERA DEL MODAL -->
              <div class="modal-header" style="background:#50c878; color:white; border-bottom: none;">
                <h5 class="modal-title w-100 text-center">Editar Usuario</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
              </div>

              <!-- CUERPO DEL MODAL -->
              <div class="modal-body px-4" style="max-height:75vh; overflow-y:auto;">
                <div class="container">
                  <input type="hidden" name="editaridUsuarios" id="editaridUsuarios">

                  <fieldset class="mb-4">
                    <legend>Información Personal</legend>
                    <div class="row">
                      <div class="col-md-6 form-group">
                        <label for="editarNombre">Nombre Completo:</label>
                        <input type="text" class="form-control" id="editarNombre" name="editarNombre" required>
                      </div>
                      <div class="col-md-6 form-group">
                        <label for="editarApellido">Apellido:</label>
                        <input type="text" class="form-control" id="editarApellido" name="editarApellido" required>
                      </div>
                      <div class="col-md-4 form-group">
                        <label for="editarCi">C.I.:</label>
                        <input type="text" class="form-control" id="editarCi" name="editarCi" readonly>
                      </div>
                      <div class="col-md-4 form-group">
                        <label for="editarDomicilio">Domicilio:</label>
                        <input type="text" class="form-control" id="editarDomicilio" name="editarDomicilio" required>
                      </div>
                      <div class="col-md-4 form-group">
                        <label for="editarFecha">Fecha de Nacimiento:</label>
                        <input type="date" class="form-control" name="editarFecha" id="editarFecha" required>
                      </div>
                    </div>
                  </fieldset>

                  <fieldset class="mb-4">
                    <legend>Datos de Usuario</legend>
                    <div class="row">
                      <div class="col-md-6 form-group">
                        <label for="editarUsuario">Nombre de Usuario:</label>
                        <input type="text" class="form-control" id="editarUsuario" name="editarUsuario" readonly>
                      </div>
                      <div class="col-md-6 form-group">
                        <label for="editarCorreo">Correo:</label>
                        <input type="text" class="form-control" id="editarCorreo" name="editarCorreo" readonly>
                      </div>
                      <div class="col-md-6 form-group">
                        <label for="editarPassword">Nueva Contraseña:</label>
                        <input type="password" class="form-control" name="editarPassword" placeholder="Escriba la nueva contraseña">
                        <input type="hidden" id="passwordActual" name="passwordActual">
                      </div>
                      <div class="col-md-6 form-group">
                        <label for="editarPerfil">Perfil:</label>
                        <select class="form-control" name="editarPerfil">
                          <option value="" id="editarPerfil">Seleccione un perfil</option>
                          <?php
                          // Mostrar los Paciente disponibles
                          $roles = ControladorRoles::ctrMostrarRoles($item, $valor);
                          foreach ($roles as $rol) {
                            echo '<option value="' . $rol["idRol"] . '">' . $rol["nombreRol"] . '</option>';
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                  </fieldset>

                  <fieldset class="mb-4">
                    <legend>Foto de Perfil</legend>
                    <div class="form-group text-center">
                      <input type="file" class="form-control-file nuevaFoto mt-2" name="editarFoto">
                      <small class="form-text text-muted mb-2">Tamaño máximo: 2 MB</small>
                      <img src="vistas/img/usuarios/default/anonymous.png" class="img-thumbnail previsualizar" width="120px">
                      <input type="hidden" name="fotoActual" id="fotoActual">
                    </div>
                  </fieldset>

                </div>
              </div>

              <!-- PIE DEL MODAL -->
              <div class="modal-footer d-flex justify-content-center" style="border-top: none;">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Modificar Usuario</button>
              </div>

              <?php
              $editarUsuario = new ControladorUsuarios();
              $editarUsuario->ctrEditarUsuario();
              ?>

            </form>

          </div>
        </div>
      </div>

      <?php

      $borrarUsuario = new ControladorUsuarios();
      $borrarUsuario->ctrBorrarUsuario();

      ?>




    </div>
  </div>