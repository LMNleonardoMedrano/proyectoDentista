<div class="wrapper">
    <div class="page-wrap">
        <div class="main-content">
            <div class="content-wrapper">
                <section class="content-header">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h1 class="text-3xl font-bold text-gray-900">
                            <i class="fas fa-user-shield fa-lg mr-2 text-blue-600"></i>
                            Gestión de Roles y Permisos
                        </h1>
                        <button class="btn btn-primary d-flex align-items-center justify-content-center"
                            style="padding: 8px 20px; font-size: 1.1rem; height: 42px;" data-toggle="modal" data-target="#modalAgregarRol">
                            <i class="fas fa-plus mr-2"></i> Agregar Rol
                        </button>
                    </div>
                </section>

                <section class="content">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Roles de Usuario</h3>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <table class="table table-hover table-borderless">
                                        <thead style="background-color: #c3f3faff; color: #2dce89;">
                                            <tr>
                                                <th style="width: 50px; color: #00796b;">Nro</th>
                                                <th style="width: 250px; color: #00796b;">Nombre del Rol</th>
                                                <th style="width: 300px; color: #00796b;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $roles = ControladorRoles::ctrMostrarRoles(null, null);
                                            foreach ($roles as $index => $rol) {
                                                echo "<tr>";
                                                echo "<td>" . ($index + 1) . "</td>";
                                                echo "<td>{$rol['nombreRol']}</td>";
                                                echo "<td>
                                    <button class='btn btn-info mr-3 btn-sm verRol' data-id='{$rol['idRol']}' data-nombre='{$rol['nombreRol']}' title='Ver Permisos'>
                                        <i class='fas fa-eye'></i> Ver Permisos
                                    </button>
                                    <button class='icon-btn text-success mr-3 btnEditarRol' data-id='{$rol['idRol']}' data-nombre='{$rol['nombreRol']}' data-toggle='modal' data-target='#modalEditarRoles' title='Editar'>
                                        <i class='fas fa-edit fa-lg'></i>
                                    </button>
                                    <button class='icon-btn text-danger mr-3 btnEliminarRol' data-id='{$rol['idRol']}' title='Eliminar'>
                                        <i class='fas fa-trash fa-lg'></i>
                                    </button>
                                </td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="tablaPermisosContainer" style="display:none;">
                                <h5 class="mb-3"><b>Accesos y permisos de <span id="nombreRolSeleccionado"></span></b></h5>
                                <table class="table table-hover table-borderless">
                                    <thead style="background-color: #c3f3faff; color: #2dce89;">
                                        <tr>
                                            <th style="width: 50px; color: #00796b;">Nro</th>
                                            <th style="width: 250px; color: #00796b;">Módulo</th>
                                            <th style="width: 250px; color: #00796b;">Progreso permiso</th>
                                            <th style="width: 100px; color: #00796b;">Acceso</th>
                                            <th style="width: 150px; color: #00796b;">Formularios</th>
                                            <th style="width: 120px; color: #00796b;">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaPermisos">
                                        <!-- Aquí se cargarán los permisos -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ========================= MODAL FORMULARIOS ========================= -->
                <div class="modal fade" id="modalFormularios" tabindex="-1" aria-labelledby="modalFormulariosLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header" style="background:#50c878; color:white;">
                                <h5 class="modal-title" id="modalFormulariosLabel">Formularios del Módulo</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="idRolModal">
                                <table class="table table-hover table-borderless">
                                    <thead style="background-color: #c3f3faff; color: #2dce89;">
                                        <tr>
                                            <th style="width: 50px; color: #00796b;">N°</th>
                                            <th style="width: 50px; color: #00796b;">Nombre del Formulario</th>
                                            <th style="width: 50px; color: #00796b;">Acceso</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaFormularios">
                                        <!-- Cargado dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">

                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <!-- Modal Agregar Roles -->
            <div id="modalAgregarRol" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="post">
                            <div class="modal-header" style="background:#50c878; color:white;">
                                <h5 class="modal-title w-100 text-center">Agregar Roles</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body px-5">
                                <div class="form-group">
                                    <label for="nuevoRol">Nombre del Rol:</label>
                                    <input type="text" class="form-control" name="nuevoRol" required>
                                </div>

                            </div>
                            <div class="modal-footer d-flex justify-content-center">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Guardar rol</button>
                            </div>
                        </form>

                        <?php
                        $crearRoles = new ControladorRoles();
                        $crearRoles->ctrCrearRoles();
                        ?>
                    </div>
                </div>
            </div>
            <!-- Modal Editar Roles -->
            <div id="modalEditarRoles" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="post">
                            <div class="modal-header" style="background:#50c878; color:white;">
                                <h5 class="modal-title w-100 text-center">Editar Rol</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body px-5">

                                <input type="hidden" name="editaridRol" id="editaridRol">

                                <div class="form-group">
                                    <label for="editarRol">Nombre del Rol:</label>
                                    <input type="text" class="form-control" name="editarRol" id="editarRol" required>
                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-center">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Guardar Rol</button>
                            </div>
                        </form>

                        <?php
                        $editarRoles = new ControladorRoles();
                        $editarRoles->ctrEditarRoles();
                        ?>
                    </div>
                </div>
            </div>

            <?php
            $eliminarRoles = new ControladorRoles();
            $eliminarRoles->ctrEliminarRoles();
            ?>
        </div>
    </div>
</div>