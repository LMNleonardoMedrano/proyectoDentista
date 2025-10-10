<div class="wrapper">
    <div class="page-wrap">
        <div class="main-content">
            <div class="content-wrapper">

                <section class="content-header">
                    <h1>Administrar Medicamentos</h1>
                    <ol class="breadcrumb">
                        <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
                        <li class="active">Administrar Medicamentos</li>
                    </ol>
                </section>

                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarMedicamentos">
                                Agregar Medicamentos
                            </button>
                        </div>

                        <div class="box-body">
                            <table id="data_table" class="table table-hover" width="100%">
                                <thead>
                                    <tr>
                                        <th style="width:10px">#</th>
                                        <th>Nombre</th>
                                        <th>tipo</th>
                                        <th>medida</th>
                                        <th>tiempo</th>                              
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $item = null;
                                    $valor = null;
                                    $tutorPadre = ControladorTutorPadre::ctrMostrarTutorPadre($item, $valor);

                                    foreach ($tutorPadre as $key => $value) {
                                        echo '<tr>
                                            <td>' . $value["IdTutorPadre"] . '</td>
                                            <td>' . $value["idPaciente"] . '</td>
                                            <td>' . $value["Nombre"] . '</td>
                                            <td>' . $value["Domicilio"] . '</td>
                                            <td>' . $value["FechaNac"] . '</td>
                                            <td>' . $value["Genero"] . '</td>
                                            <td>' . $value["Ci"] . '</td>
                                            <td>' . $value["Ocupacion"] . '</td>
                                            <td>' . $value["Relacion"] . '</td>
                                            <td>' . $value["TelCel"] . '</td>

                                            <td>
                                            <div class="btn-group">
                                                <button class="btn btn-warning btnEditarTutorPadre" data-toggle="modal" data-target="#modalEditarTutorPadre" IdTutorPadre="' . $value["IdTutorPadre"] . '"><i class="ik ik-edit-2"></i></button>
                                                <button class="btn btn-danger btnEliminarTutorPadre" IdTutorPadre="' . $value["IdTutorPadre"] . '"><i class="ik ik-trash-2"></i></button>
                                            </div>
                                            </td>
                                        </tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

            </div>

            <!--=====================================
      MODAL AGREGAR TUTOR PADRE
      ======================================-->
            <div id="modalAgregarTutorPadre" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form role="form" method="post">
                            <div class="modal-header" style="background:#50c878; color:white; border-bottom: none;">
                                <h5 class="modal-title w-100 text-center">Crear Nuevo Tutor Padre</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body px-5">
                                <div class="container">
                                    <!-- Entrada para el nombre -->
                                    <div class="form-group">
                                        <label for="nuevoidPaciente" class="form-label">nombre del paciente </label>
                                        <div class="input-group">
                                            <select class="form-control" name="nuevoidPaciente" required>
                                                <?php
                                                // Mostrar los Paciente disponibles
                                                $pacientes = ControladorPaciente::ctrMostrarPaciente($item, $valor);
                                                foreach ($pacientes as $paciente) {
                                                    echo '<option value="' . $paciente["idPaciente"] . '">' . $paciente["Nombre"] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- CAMPO Nombre del padre o tutor -->
                                    <div class="form-group">
                                        <label for="nuevoNombre">Nombre Completo del padre o tutor:</label>
                                        <input type="text" class="form-control" name="nuevoNombre" placeholder="Escribe el nombre del padre o tutor" required>
                                    </div>

                                    <!-- CAMPO Domicilio -->
                                    <div class="form-group">
                                        <label for="nuevoDomicilio">Domicilio:</label>
                                        <input type="text" class="form-control" name="nuevoDomicilio" placeholder="Escribe el domicilio" required>
                                    </div>

                                    <!-- CAMPO Fecha de Nacimiento -->
                                    <div class="form-group">
                                        <label for="nuevaFechaNacimiento">Fecha de Nacimiento:</label>
                                        <input type="date" class="form-control" name="nuevaFechaNacimiento" required>
                                    </div>
                                    <!-- CAMPO Género -->
                                    <div class="form-group">
                                        <label for="nuevoGenero">Selecciona un Género:</label>
                                        <select class="form-control" name="nuevoGenero" required>
                                            <option value="" disabled selected>Seleccione un Género</option>
                                            <option value="Masculino">Masculino</option>
                                            <option value="Femenino">Femenino</option>
                                        </select>
                                    </div>
                                    <!-- CAMPO CI -->
                                    <div class="form-group">
                                        <label for="nuevoCI">CI:</label>
                                        <input type="text" class="form-control" name="nuevoCI" placeholder="Escribe el Carnet de Identidad" required>
                                    </div>
                                    <!-- CAMPO Ocupación -->
                                    <div class="form-group">
                                        <label for="nuevoOcupacion">Ocupación:</label>
                                        <input type="text" class="form-control" name="nuevoOcupacion" placeholder="Escribe la ocupación" required>
                                    </div>
                                    <!-- CAMPO Relación -->
                                    <div class="form-group">
                                        <label for="nuevoRelacion">Relación:</label>
                                        <select class="form-control" name="nuevoRelacion" required>
                                            <option value="" disabled selected>Seleccione su Relacion</option>
                                            <option value="Padre">Padre</option>
                                            <option value="Tutor">Tutor</option>
                                        </select>
                                    </div>

                                    <!-- CAMPO Teléfono -->
                                    <div class="form-group">
                                        <label for="nuevoTelCel">Teléfono:</label>
                                        <input type="text" class="form-control" name="nuevoTelCel" placeholder="Escribe el Teléfono" required>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-center" style="border-top: none;">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Crear Tutor Padre</button>
                            </div>
                        </form>

                        <?php
                        $crearTutorPadre = new ControladorTutorPadre();
                        $crearTutorPadre->ctrCrearTutorPadre();
                        ?>
                    </div>
                </div>
            </div>

            <!--=====================================
      MODAL EDITAR TUTOR PADRE
      ======================================-->
            <div id="modalEditarTutorPadre" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form role="form" method="post">


                            <div class="modal-header" style="background:#50c878; color:white; border-bottom: none;">
                                <h5 class="modal-title w-100 text-center">Editar Tutor Padre</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>

                            <div class="modal-body px-5">
                                <div class="container">

                                    <!-- Entrada Oculta para el ID del Tutor Padre -->
                                    <input type="hidden" name="editarIdTutorPadre" id="editarIdTutorPadre" value="">

                                    <!-- Entrada para el nombre -->
                                    <div class="form-group">
                                        <label for="editaridPaciente" class="form-label">nombre del paciente </label>
                                        <div class="input-group">
                                            <select class="form-control" name="editaridPaciente" id="editaridPaciente" required>
                                                <?php
                                                // Mostrar los Paciente disponibles
                                                $pacientes = ControladorPaciente::ctrMostrarPaciente($item, $valor);
                                                foreach ($pacientes as $paciente) {
                                                    echo '<option value="' . $paciente["idPaciente"] . '">' . $paciente["Nombre"] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- CAMPO Nombre del padre o tutor -->
                                    <div class="form-group">
                                        <label for="editarNombre">Nombre Completo del padre o tutor:</label>
                                        <input type="text" class="form-control" name="editarNombre" id="editarNombre" placeholder="Escribe el nombre del padre o tutor" required>
                                    </div>

                                    <!-- CAMPO Domicilio -->
                                    <div class="form-group">
                                        <label for="editarDomicilio">Domicilio:</label>
                                        <input type="text" class="form-control" name="editarDomicilio" id="editarDomicilio" placeholder="Escribe el domicilio" required>
                                    </div>

                                    <!-- CAMPO Fecha de Nacimiento -->
                                    <div class="form-group">
                                        <label for="editarFechaNacimiento">Fecha de Nacimiento:</label>
                                        <input type="date" class="form-control" name="editarFechaNacimiento" id="editarFechaNacimiento" required>
                                    </div>
                                    <!-- CAMPO Género -->
                                    <div class="form-group">
                                        <label for="editarGenero">Selecciona un Género:</label>
                                        <select class="form-control" name="editarGenero" id="editarGenero" required>
                                            <option value="" disabled selected>Seleccione un Género</option>
                                            <option value="Masculino">Masculino</option>
                                            <option value="Femenino">Femenino</option>
                                        </select>
                                    </div>
                                    <!-- CAMPO CI -->
                                    <div class="form-group">
                                        <label for="editarCI">CI:</label>
                                        <input type="text" class="form-control" name="editarCI" id="editarCI" placeholder="Escribe el Carnet de Identidad" required>
                                    </div>
                                    <!-- CAMPO Ocupación -->
                                    <div class="form-group">
                                        <label for="editarOcupacion">Ocupación:</label>
                                        <input type="text" class="form-control" name="editarOcupacion" id="editarOcupacion" placeholder="Escribe la ocupación" required>
                                    </div>
                                    <!-- CAMPO Relación -->
                                    <div class="form-group">
                                        <label for="editarRelacion">Relación:</label>
                                        <select class="form-control" name="editarRelacion" id="editarRelacion" required>
                                            <option value="" disabled selected>Seleccione su Relacion</option>
                                            <option value="Padre">Padre</option>
                                            <option value="Tutor">Tutor</option>
                                        </select>
                                    </div>

                                    <!-- CAMPO Teléfono -->
                                    <div class="form-group">
                                        <label for="editarTelCel">Teléfono:</label>
                                        <input type="text" class="form-control" name="editarTelCel" id="editarTelCel" placeholder="Escribe el Teléfono" required>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-center" style="border-top: none;">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Actualizar Tutor Padre</button>
                            </div>
                            <?php
                            $EditarTutorPadre = new ControladorTutorPadre();
                            $EditarTutorPadre->ctrEditarTutorPadre();
                            ?>
                        </form>


                    </div>
                </div>
            </div>
            <?php

            $eliminartutoPadre = new ControladorTutorPadre();
            $eliminartutoPadre->ctrEliminarTutorPadre();

            ?>
        </div>
    </div>
</div>