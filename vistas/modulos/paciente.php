<?php
?>

<div class="wrapper">
  <div class="page-wrap">
    <div class="main-content">
      <div class="content-wrapper">
       <section class="content-header">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-users fa-lg mr-2 text-blue-600"></i>
            Gestión de Pacientes
        </h1>

        <!-- Botón Agregar solo si tiene permiso crearPacientes -->
        <?php if (tienePermiso('crearPacientes')): ?>
        <button class="btn btn-primary d-flex align-items-center justify-content-center"
            style="padding: 8px 20px; font-size: 1.1rem; height: 42px;" 
            data-toggle="modal" data-target="#modalAgregarPaciente">
            <i class="fas fa-plus mr-2"></i> Agregar Paciente
        </button>
        <?php endif; ?>
    </div>
</section>

<section class="content-header">

    <!-- Estadísticas -->
    <?php $pacientesDesdeBD = ControladorPaciente::ctrMostrarPaciente(null, null); ?>

    <!-- Contenedor de estadísticas compacto -->
    <div class="row g-2 mb-3" id="stats-container">
        <div class="col-12 col-md-3">
            <div class="card text-center shadow-sm py-2">
                <div class="card-body p-2">
                    <h6 class="card-title mb-1">Total Pacientes</h6>
                    <p class="mb-0 fw-bold text-primary" style="font-size:1.5rem;" id="total-pacientes">0</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="card text-center shadow-sm py-2">
                <div class="card-body p-2">
                    <h6 class="card-title mb-1">Nuevos este mes</h6>
                    <p class="mb-0 fw-bold text-success" style="font-size:1.5rem;" id="nuevos-mes">0</p>
                </div>
            </div>
        </div>
    </div>

</section>

<section class="content">
    <div class="box">
        <div class="box-header with-border d-flex justify-content-between align-items-center">
            <button id="btnToggleMenores" class="btn btn-info">
                <i class="ik ik-filter"></i> Ver menores
            </button>
        </div>

        <div class="box-body">
            <!-- Tabla solo si tiene permiso listarPacientes -->
            <?php if (tienePermiso('listarPacientes')): ?>
            <table id="data_table" class="table table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th>CI</th>
                        <th>Edad</th>
                        <th>Teléfono</th>
                        <th>Género</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $item = null;
                    $valor = null;
                    $pacientes = ControladorPaciente::ctrMostrarPaciente($item, $valor);

                    foreach ($pacientes as $key => $value) {
                        $edad = (new DateTime($value["fechaNac"]))->diff(new DateTime('today'))->y;
                        $esMenor = $edad < 18 ? "Sí" : "No";
                    ?>
                    <tr class="<?php echo $esMenor === "Sí" ? "table-warning menor" : ""; ?>">
                        <td><?php echo $value["idPaciente"]; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"
                                    style="width:40px; height:40px; font-weight:bold;">
                                    <?php echo substr($value['nombre'], 0, 1); ?>
                                </div>
                                <div class="ml-2">
                                    <div class="font-weight-bold"><?php echo htmlspecialchars($value["nombre"]); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($value["domicilio"]); ?></small>
                                    <?php if ($esMenor === "Sí"): ?>
                                    <div><span class="badge badge-warning">Menor de edad</span></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($value["ci"]); ?></td>
                        <td><?php echo $edad; ?> años</td>
                        <td>
                            <i class="ik ik-phone text-muted"></i>
                            <?php echo htmlspecialchars($value["telCel"] ?: "N/A"); ?>
                        </td>
                        <td><?php echo htmlspecialchars($value["genero"]); ?></td>
                        <td>
                            <div class="d-flex">

                                <!-- Editar solo si tiene permiso editarPacientes -->
                                <?php if (tienePermiso('editarPacientes')): ?>
                                <button class="icon-btn text-success mr-3 btnEditarPaciente"
                                    data-toggle="modal"
                                    data-target="#modalEditarPaciente"
                                    idPaciente="<?= $value['idPaciente'] ?>" title="Editar">
                                    <i class="fas fa-edit fa-lg"></i>
                                </button>
                                <?php endif; ?>

                                <!-- Eliminar solo si tiene permiso eliminarPacientes -->
                                <?php if (tienePermiso('eliminarPacientes')): ?>
                                <button class="icon-btn text-danger mr-3 btnEliminarPaciente"
                                    idPaciente="<?= $value['idPaciente'] ?>" title="Eliminar">
                                    <i class="fas fa-trash fa-lg"></i>
                                </button>
                                <?php endif; ?>

                                <!-- Ver Tutor (solo si es menor) -->
                                <?php if ($esMenor === "Sí"): ?>
                                <button class="icon-btn text-info btnVerTutor"
                                    data-id="<?= $value['idPaciente'] ?>"
                                    data-toggle="modal"
                                    data-target="#modalVerTutor" title="Ver Datos del Tutor">
                                    <i class="fas fa-user fa-lg"></i>
                                </button>
                                <?php endif; ?>

                            </div>
                        </td>

                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</section>



        <style>
          .menor {
            background-color: #ffdddd;
          }
        </style>

        <!-- Modal para ver tutor -->
        <div class="modal fade" id="modalVerTutor" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Datos del Tutor</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body p-2">
                <div id="detalleTutor">
                  <em>Cargando...</em>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- MODAL AGREGAR PACIENTE -->
        <div id="modalAgregarPaciente" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <form role="form" method="post" id="formAgregarPaciente">
                <div class="modal-header" style="background:#50c878; color:white; border-bottom: none;">
                  <h5 class="modal-title w-100 text-center">Crear Nuevo Paciente</h5>
                  <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body px-5">
                  <div class="container">

                    <div class="form-group">
                      <label for="nuevoCi">C.I.:</label>
                      <input type="text" class="form-control" name="nuevoCi" placeholder="Escribe el C.I." id="nuevoCi" required>
                    </div>

                    <div class="form-group">
                      <label for="nuevoDomicilio">Domicilio:</label>
                      <input type="text" class="form-control" name="nuevoDomicilio" placeholder="Escribe el Domicilio" required>
                    </div>

                    <div class="form-group">
                      <label for="nuevaFechaNacimiento">Fecha de Nacimiento:</label>
                      <input type="date" class="form-control" name="nuevaFechaNacimiento" id="nuevaFechaNacimiento" required onchange="verificarEdad('nueva')">
                    </div>

                    <div class="form-group">
                      <label for="nuevoNombre">Nombre Completo:</label>
                      <input type="text" class="form-control" name="nuevoNombre" placeholder="Escribe el Nombre Completo" required>
                    </div>

                    <div class="form-group">
                      <label for="nuevoGenero">Género:</label>
                      <select class="form-control" name="nuevoGenero" required>
                        <option value="" disabled selected>Seleccione un Género</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="nuevaTelefono">Teléfono:</label>
                      <input type="text" class="form-control" name="nuevaTelefono" required>
                    </div>

                    <!-- BLOQUE DE TUTOR -->
                    <div id="datosTutorNuevo" style="display:none; border:1px solid #ccc; padding:10px; border-radius:10px; margin-top:20px;">
                      <h6>Datos del Tutor (solo si es menor de edad)</h6>

                      <input type="hidden" name="esMenor" id="esMenor" value="no">

                      <div class="form-group">
                        <label for="tutor_nombre">Nombre del Tutor:</label>
                        <input type="text" class="form-control" name="tutor_nombre" id="tutor_nombre">
                      </div>

                      <div class="form-group">
                        <label for="tutor_genero">Género del Tutor:</label>
                        <select class="form-control" name="tutor_genero" id="tutor_genero">
                          <option value="">Seleccione un Género</option>
                          <option value="Masculino">Masculino</option>
                          <option value="Femenino">Femenino</option>
                        </select>
                      </div>

                      <div class="form-group">
                        <label for="tutor_ocupacion">Ocupación del Tutor:</label>
                        <input type="text" class="form-control" name="tutor_ocupacion" id="tutor_ocupacion">
                      </div>

                      <div class="form-group">
                        <label for="tutor_relacion">Relación con el Paciente:</label>
                        <input type="text" class="form-control" name="tutor_relacion" id="tutor_relacion">
                      </div>
                    </div>

                  </div>
                </div>
                <div class="modal-footer d-flex justify-content-center" style="border-top: none;">
                  <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                  <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Crear Paciente</button>
                </div>
              </form>

              <?php
              $crearPaciente = new ControladorPaciente();
              $crearPaciente->ctrCrearPaciente();
              ?>
            </div>
          </div>
        </div>

        <!-- MODAL EDITAR PACIENTE -->
        <div id="modalEditarPaciente" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <form role="form" method="post" id="formEditarPaciente">

                <div class="modal-header" style="background:#50c878; color:white; border-bottom: none;">
                  <h5 class="modal-title w-100 text-center">Editar Paciente</h5>
                  <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body px-5">
                  <div class="container">

                    <input type="hidden" name="editaridPaciente" id="editaridPaciente">

                    <div class="form-group">
                      <label for="editarCI">CI:</label>
                      <input type="text" class="form-control" name="editarCI" id="editarCI" required>
                    </div>

                    <div class="form-group">
                      <label for="editarDomicilio">Domicilio:</label>
                      <input type="text" class="form-control" name="editarDomicilio" id="editarDomicilio" required>
                    </div>

                    <div class="form-group">
                      <label for="editarFechaNacimiento">Fecha de Nacimiento:</label>
                      <input type="date" class="form-control" name="editarFechaNacimiento" id="editarFechaNacimiento" required onchange="verificarEdadEditar()">
                    </div>

                    <div class="form-group">
                      <label for="editarNombre">Nombre Completo:</label>
                      <input type="text" class="form-control" name="editarNombre" id="editarNombre" required>
                    </div>

                    <div class="form-group">
                      <label for="editarGenero">Género:</label>
                      <select class="form-control" name="editarGenero" id="editarGenero" required>
                        <option value="" disabled selected>Seleccione un Género</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="editarTelefono">Teléfono:</label>
                      <input type="text" class="form-control" name="editarTelefono" id="editarTelefono" required>
                    </div>

                    <!-- BLOQUE TUTOR EDITAR -->
                    <div id="datosTutorEditar" style="display:none; border:1px solid #ccc; padding:10px; border-radius:10px; margin-top:20px;">
                      <h6>Datos del Tutor (solo si es menor de edad)</h6>

                      <div class="form-group">
                        <label for="editarNombrePT">Nombre del Tutor:</label>
                        <input type="text" class="form-control" name="editarNombrePT" id="editarNombrePT">
                      </div>

                      <div class="form-group">
                        <label for="editarGeneroPT">Género del Tutor:</label>
                        <select class="form-control" name="editarGeneroPT" id="editarGeneroPT">
                          <option value="">Seleccione un Género</option>
                          <option value="Masculino">Masculino</option>
                          <option value="Femenino">Femenino</option>
                        </select>
                      </div>

                      <div class="form-group">
                        <label for="editarOcupacionPT">Ocupación del Tutor:</label>
                        <input type="text" class="form-control" name="editarOcupacionPT" id="editarOcupacionPT">
                      </div>

                      <div class="form-group">
                        <label for="editarRelacionPT">Relación con el Paciente:</label>
                        <input type="text" class="form-control" name="editarRelacionPT" id="editarRelacionPT">
                      </div>
                    </div>

                  </div>
                </div>

                <div class="modal-footer d-flex justify-content-center" style="border-top: none;">
                  <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                  <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Guardar Cambios</button>
                </div>

                <?php
                $editarPaciente = new ControladorPaciente();
                $editarPaciente->ctrEditarPaciente();
                ?>
              </form>
            </div>
          </div>
        </div>

        <?php
        $borrarPaciente = new ControladorPaciente();
        $borrarPaciente->ctrEliminarPaciente();
        ?>

      </div>
    </div>
  </div>
</div>