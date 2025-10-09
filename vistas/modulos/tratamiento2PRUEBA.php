<div class="wrapper">
  <div class="page-wrap">
    <div class="main-content">
      <div class="content-wrapper">

        <section class="content-header">
          <h1>Administrar Tratamientos</h1>
          <ol class="breadcrumb">
            <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Administrar Tratamientos</li>
          </ol>
        </section>

        <div class="container mt-5">
          <h4 class="text-center mb-4">Gestión Clínica</h4>
          <div class="box-header with-border">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarTratamiento">Agregar Tratamiento</button>
          </div>
          <!-- Pestañas -->
          <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" data-toggle="tab" href="#tabTratamiento" role="tab">🩺 Tratamientos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#tabMedicamentos" role="tab">💊 Detalle Medicamento</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#tabOdontograma" role="tab">🦷 Odontograma</a>
            </li>
          </ul>

          <!-- Contenido pestañas -->
          <div class="tab-content">
            <!-- Tratamientos -->
            <div class="tab-pane fade show active" id="tabTratamiento" role="tabpanel">
              <table id="data_table_tratamiento" class="table table-hover">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Paciente</th>
                    <th>Odontólogo</th>
                    <th>Saldo</th>
                    <th>Total Pago</th>
                    <th>Estado</th>
                    <th>EstadoPago</th>
                    <th>accion</th>

                  </tr>
                </thead>
                <tbody>
                  <?php
                  $tratamientos = ControladorTratamiento::ctrMostrarTratamientos(); // Debes crear este método
                  foreach ($tratamientos as $i => $t) {
                    echo "<tr>
                                            <td>{$t['idTratamiento']}</td>

                      <td>{$t['fechaRegistro']}</td>
                      <td>{$t['nombrePaciente']}</td>
                      <td>{$t['nombreUsuario']}</td>
                      <td>" . number_format($t['saldo'], 2) . "</td>
                      <td>" . number_format($t['totalPago'], 2) . "</td>
                      <td>{$t['estado']}</td>
                      <td>{$t['estadoPago']}</td>
                      <td>
                        <div class='btn-group'>
                          <button class='btn btn-info btnVerPagos' 
                                  data-toggle='modal' 
                                  data-target='#modalPagosTratamiento' 
                                  data-idtratamiento='{$t["idTratamiento"]}'>
                            <i class='ik ik-file-text'></i> Ver
                          </button>
                            <button class='btn btn-warning btnEditarTratamiento' 
                                    data-toggle='modal' 
                                    data-target='#modalEditarTratamiento' 
                                    idTratamiento='{$t["idTratamiento"]}'>
                              <i class='ik ik-edit-2'></i>
                            </button>
                            <button class='btn btn-danger btnEliminarTratamiento' 
                                    idTratamiento='{$t["idTratamiento"]}'>
                              <i class='ik ik-trash-2'></i>
                            </button>
                        </div>
                      </td>
                    </tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>

            <!-- Detalle Medicamento -->
            <div class="tab-pane fade" id="tabMedicamentos" role="tabpanel">
              <table class="table table-bordered table-hover">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Tratamiento</th>
                    <th>Medicamento</th>
                    <th>Dosis</th>
                    <th>Inicio</th>
                    <th>Final</th>
                    <th>Tiempo</th>
                    <th>Observación</th>
                    <th>accion</th>

                  </tr>
                </thead>
                <tbody>
                  <?php
                  $detalles = ControladorTratamiento::ctrMostrarDetalleMedicamento();
                  $claveSecreta = "TuClaveUltraPrivada2025";

                  foreach ($detalles as $i => $d) {
                    $token = hash('sha256', $d["idTratamiento"] . $claveSecreta);
                    echo "<tr>
                      <td>" . ($i + 1) . "</td>
                      <td>{$d['idTratamiento']}</td>
                      <td>{$d['nombreMedicamento']}</td>
                      <td>{$d['dosis']}</td>
                      <td>{$d['fechaInicio']}</td>
                      <td>{$d['fechaFinal']}</td>
                      <td>{$d['tiempo']}</td>
                      <td>{$d['observacion']}</td>
                      <td>
                        <div class='btn-group'>
                          <button class='btn btn-warning btnEditarTratamiento' 
                                  data-toggle='modal' 
                                  data-target='#modalEditarTratamiento' 
                                  idTratamiento='{$d["idTratamiento"]}'>
                            <i class='ik ik-edit-2'></i>
                          </button>
                          <button class='btn btn-danger btnEliminarTratamiento' 
                                  idTratamiento='{$d["idTratamiento"]}'>
                            <i class='ik ik-trash-2'></i>
                          </button>
                          <a href='vistas/modulos/reciboMedicamento.php?idTratamiento={$d["idTratamiento"]}&token=$token' 
                            target='_blank' 
                            class='btn btn-info' 
                            title='Ver Recibo'>
                            🧾
                          </a>
                        </div>
                      </td>
                    </tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>

            <!-- Odontograma -->
            <div class="tab-pane fade" id="tabOdontograma" role="tabpanel">
              <table class="table table-bordered table-hover">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Tratamiento</th>
                    <th>Fecha</th>
                    <th>Posición</th>
                    <th>Estado</th>
                    <th>Descripción</th>
                    <th>Imagen</th>
                    <th>accion</th>

                  </tr>
                </thead>
                <tbody>
                  <?php
                  $odontogramas = ControladorTratamiento::ctrMostrarOdontogramas(); // Método personalizado
                  foreach ($odontogramas as $i => $o) {
                    echo "<tr>
              <td>" . ($i + 1) . "</td>
              <td>{$o['idTratamiento']}</td>
              <td>{$o['fechaRegistro']}</td>
              <td>{$o['posicion']}</td>
              <td>{$o['estado']}</td>
              <td>{$o['descripcion']}</td>
              <td><img src='{$o['foto']}' alt='Odontograma' style='max-height: 60px;'></td>
              <td>
                        <div class='btn-group'>
                          <button class='btn btn-warning btnEditarTratamiento' 
                                  data-toggle='modal' 
                                  data-target='#modalEditarTratamiento' 
                                  idTratamiento='{$t["idTratamiento"]}'>
                            <i class='ik ik-edit-2'></i>
                          </button>
                          <button class='btn btn-danger btnEliminarTratamiento' 
                                  idTratamiento='{$t["idTratamiento"]}'>
                            <i class='ik ik-trash-2'></i>
                          </button>
                        </div>
                      </td>
            </tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
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

        <!-- MODAL AGREGAR TRATAMIENTO -->
        <div id="modalAgregarTratamiento" class="modal fade" role="dialog">
          <div class="modal-dialog modal-xl" style="max-width:85vw;">
            <div class="modal-content">
              <form method="post">
                <!-- Cabecera -->
                <div class="modal-header" style="background:#50c878; color:white;">
                  <h5 class="modal-title w-100 text-center">Registrar Tratamiento Completo</h5>
                  <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <!-- Cuerpo -->
                <div class="modal-body px-4" style="max-height:75vh; overflow-y:auto;">
                  <div class="container">

                    <!-- ✅ Primera fila: Tratamiento + Medicamentos -->
                    <div class="row mb-4">
                      <!-- 🦷 Datos del Tratamiento -->
                      <div class="col-md-12">
                        <fieldset class="mb-4">
                          <legend>Datos del Tratamiento</legend>
                          <div class="row">

                            <div class="col-md-6 form-group">
                              <label>Odontólogo:</label>
                              <select name="idUsuarios" class="form-control" required>
                                <option value="">Seleccionar odontólogo</option>
                                <?php
                                $usuarios = ControladorUsuarios::ctrMostrarUsuarios(null, null);
                                foreach ($usuarios as $u) {
                                  echo "<option value='{$u['idUsuarios']}'>{$u['nombre']}</option>";
                                }
                                ?>
                              </select>
                            </div>
                            <div class="col-md-6 form-group">
                              <label>Paciente:</label>
                              <select name="idPaciente" class="form-control" required>
                                <option value="">Seleccionar paciente</option>
                                <?php
                                $pacientes = ControladorPaciente::ctrMostrarPaciente(null, null);
                                foreach ($pacientes as $p) {
                                  echo "<option value='{$p['idPaciente']}'>{$p['nombre']}</option>";
                                }
                                ?>
                              </select>
                            </div>
                            <div class="col-md-6 form-group">
                              <label>Fecha Registro:</label>
                              <input type="date" name="fechaRegistro" class="form-control" required>
                            </div>
                            <div class="col-md-3 form-group">
                              <label>Saldo:</label>
                              <input type="number" name="saldo" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-md-3 form-group">
                              <label>Total Pago:</label>
                              <input type="number" name="totalPago" class="form-control" step="0.01" required>
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Estado</label>
                              <select name="estado" id="estado"
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                <option value="activo">Activo</option>
                                <option value="completado">Completado</option>
                                <option value="cancelado">Cancelado</option>
                              </select>
                            </div>
                            <!-- Estado de pago -->
                            <div>
                              <label class="block text-sm font-medium text-gray-700">Estado de Pago</label>
                              <select name="estadoPago" id="estadoPago"
                                class="estadoPagoVisual mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-green-500 focus:border-green-500"
                                readonly>
                                <option value="pendiente">Pendiente</option>
                                <option value="parcial">Parcial</option>
                                <option value="pagado">Pagado</option>
                              </select>
                            </div>


                          </div>
                        </fieldset>
                      </div>

                      <!-- 💊 Medicamentos -->
                       <!-- 💊 Medicamentos -->
                      <div class="col-md-12">
                        <fieldset class="mb-4">
                          <legend>Medicamentos Recetados</legend>

                          <div id="medicamentos-recetados">
                            <div class="medicamento-block border p-3 mb-3">
                              <div class="row">
                                <div class="col-md-6 form-group">
                                  <label>Dosis:</label>
                                  <input type="text" name="dosis[]" class="form-control">
                                </div>
                                <div class="col-md-3 form-group">
                                  <label>Inicio:</label>
                                  <input type="date" name="fechaInicio[]" class="form-control">
                                </div>
                                <div class="col-md-3 form-group">
                                  <label>Final:</label>
                                  <input type="date" name="fechaFinal[]" class="form-control">
                                </div>
                                <div class="col-md-6 form-group">
                                  <label>Tiempo de aplicación:</label>
                                  <input type="text" name="tiempo[]" class="form-control">
                                </div>
                                <div class="col-md-6 form-group">
                                  <label>Medicamento:</label>
                                  <select name="codMedicamento[]" class="form-control" required>
                                    <option value="">Seleccionar medicamento</option>
                                    <?php
                                    $medicamentos = ControladorMedicamento::ctrMostrarMedicamentos(null, null);
                                    foreach ($medicamentos as $m) {
                                      echo "<option value='{$m['codMedicamento']}'>{$m['nombre']}</option>";
                                    }
                                    ?>
                                  </select>
                                </div>
                                <div class="col-md-12 form-group">
                                  <label>Observación:</label>
                                  <textarea name="observacion[]" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-md-12 text-right">
                                  <button type="button" class="btn btn-secondary duplicate-medicamento">📋 Duplicar este medicamento</button>
                                  <button type="button" class="btn btn-danger remove-medicamento">✖ Quitar</button>
                                </div>
                              </div>
                            </div>
                          </div>

                          <button type="button" class="btn btn-primary mt-2" id="add-medicamento">➕ Añadir otro medicamento</button>
                        </fieldset>
                      </div>
                    </div>

                    <!-- 🧠 Segunda fila: Odontograma horizontal -->
                    <div class="row">
                      <div class="col-md-12">
                        <!-- Botón Agregar Odontograma -->
                        <div class="col-md-3 form-group">
                          <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#modalAgregarOdontograma">
                            Agregar Odontograma
                          </button>
                        </div>
                        <section class="bg-gray-50 rounded-lg p-4">
                          <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">🦷</div>
                            <h3 class="text-xl font-semibold text-gray-800">Odontograma Registrado</h3>
                          </div>

                          <div id="odontogramPreview" class="hidden bg-white rounded-lg p-4 border border-gray-200">
                            <div class="row">
                              <div class="col-md-12">
                                <div id="odontogramImage" class="border border-gray-200 rounded-lg p-3 bg-white text-center">
                                  <!-- Imagen vía JS -->
                                </div>
                              </div>
                              <div class="col-md-12">
                                <h5 class="text-md font-semibold text-gray-700 mb-3">Resumen clínico:</h5>
                                <div id="teethSummary" class="d-flex flex-wrap gap-3">
                                  <!-- Piezas dentales vía JS -->
                                </div>
                              </div>
                            </div>
                          </div>
                        </section>
                      </div>
                    </div>

                  </div>
                </div>

                <!-- Campos ocultos -->
                <input type="hidden" name="odontogramImage" id="odontogramImageInput">
                <input type="hidden" name="odontogramTeeth" id="odontogramTeethInput">

                <!-- Footer -->
                <div class="modal-footer d-flex justify-content-center">
                  <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                  <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Guardar Tratamiento</button>
                </div>
              </form>

              <?php
              $crearTratamiento = new ControladorTratamiento();
              $crearTratamiento->ctrCrearTratamientoCompleto();
              ?>
            </div>
          </div>
        </div>


        <!-- MODAL VER ODONTOGRAMA -->
        <!-- Modal Agregar Odontograma -->
        <div id="modalAgregarOdontograma" class="modal fade" role="dialog">
          <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document" style="max-width: 95vw;">
            <div class="modal-content">
              <form method="post">
                <!-- Cabecera -->
                <div class="modal-header" style="background:#50c878; color:white;">
                  <h5 class="modal-title w-100 text-center">Registrar Odontograma</h5>
                  <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <!-- Cuerpo -->
                <div class="modal-body">
                  <!-- Herramientas -->
                  <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Herramientas</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-2 mb-3">
                      <?php
                      $estados = [
                        'sano' => 'Sano',
                        'caries' => 'Caries',
                        'obturado' => 'Obturado',
                        'corona' => 'Corona',
                        'extraido' => 'Extraído',
                        'endodoncia' => 'Endodoncia',
                        'implante' => 'Implante'
                      ];
                      foreach ($estados as $key => $label) {
                        echo "<button type=\"button\" class=\"status-btn p-3 rounded-lg border-2 text-sm font-medium transition-all\" data-status=\"$key\" onclick=\"setSelectedStatus('$key', this)\">$label</button>";
                      }
                      ?>
                    </div>
                    <div class="flex gap-2">
                      <button type="button" onclick="resetOdontogram()" class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        🔄 Limpiar Todo
                      </button>
                      <button type="button" onclick="downloadOdontogramImage()" class="px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                        📥 Descargar PNG
                      </button>
                    </div>
                  </div>

                  <!-- Odontograma -->
                  <div id="odontogramCanvas" class="bg-white p-8 rounded-lg border-2 border-gray-200">
                    <div class="text-center mb-8">
                      <h3 class="text-2xl font-bold text-gray-800 mb-2">Odontograma Dental</h3>
                      <p class="text-gray-600">Fecha: <span id="odontogramDate"></span></p>
                    </div>

                    <!-- Leyenda horizontal en cinta -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                      <h4 class="text-sm font-semibold text-gray-700 mb-2">Leyenda:</h4>
                      <div class="leyenda-cinta">
                        <div class="estado">
                          <div class="cuadro tooth-sano"></div><span>Sano</span>
                        </div>
                        <div class="estado">
                          <div class="cuadro tooth-caries"></div><span>Caries</span>
                        </div>
                        <div class="estado">
                          <div class="cuadro tooth-obturado"></div><span>Obturado</span>
                        </div>
                        <div class="estado">
                          <div class="cuadro tooth-corona"></div><span>Corona</span>
                        </div>
                        <div class="estado">
                          <div class="cuadro tooth-extraido"></div><span>Extraído</span>
                        </div>
                        <div class="estado">
                          <div class="cuadro tooth-endodoncia"></div><span>Endodoncia</span>
                        </div>
                        <div class="estado">
                          <div class="cuadro tooth-implante"></div><span>Implante</span>
                        </div>
                      </div>
                    </div>
                    <div class="odontograma-layout">
                      <!-- Columna derecha -->
                      <div class="columna">
                        <div class="text-center">
                          <h4>Superior Derecho</h4>
                          <div id="quadrant1" class="grid"></div>
                        </div>
                        <div class="text-center">
                          <h4>Inferior Derecho</h4>
                          <div id="quadrant4" class="grid"></div>
                        </div>
                      </div>

                      <!-- Columna izquierda -->
                      <div class="columna">
                        <div class="text-center">
                          <h4>Superior Izquierdo</h4>
                          <div id="quadrant2" class="grid"></div>
                        </div>
                        <div class="text-center">
                          <h4>Inferior Izquierdo</h4>
                          <div id="quadrant3" class="grid"></div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Panel de notas -->
                  <div id="toothNotesPanel" class="mt-6 bg-blue-50 rounded-lg p-4 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3" id="toothNotesTitle">Diente #</h4>
                    <textarea id="toothNotes" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Agregue observaciones para este diente..."></textarea>
                  </div>

                  <!-- Observaciones generales -->
                  <div class="form-group mt-6">
                    <label>Observaciones generales:</label>
                    <textarea name="odontogramaObservacion" class="form-control" rows="3"></textarea>
                  </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer d-flex justify-content-center">
                  <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                  <button type="button" onclick="saveOdontogram()" class="btn btn-success">
                    💾 Guardar Odontograma
                  </button>
                </div>
            </div>
            </form>

            <?php
            // Aquí iría el controlador para guardar el odontograma si es necesario
            ?>
          </div>
        </div>
      </div>
      <!-- MODAL EDITAR TRATAMIENTO -->
      <div id="modalEditarTratamiento" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl" style="max-width:85vw;">
          <div class="modal-content">
            <form method="post" enctype="multipart/form-data">
              <!-- Cabecera -->
              <div class="modal-header" style="background:#f0ad4e; color:white;">
                <h5 class="modal-title w-100 text-center">Editar Tratamiento</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
              </div>

              <!-- Cuerpo -->
              <div class="modal-body px-4" style="max-height:75vh; overflow-y:auto;">
                <div class="container">

                  <!-- Campo oculto para ID -->
                  <input type="hidden" name="idTratamientoEditar" id="idTratamientoEditar">

                  <!-- Primera fila: Tratamiento + Medicamentos -->
                  <div class="row mb-4">
                    <!-- Tratamiento -->
                    <div class="col-md-6">
                      <fieldset class="mb-4">
                        <legend>Datos del Tratamiento</legend>
                        <div class="row">
                          <div class="col-md-6 form-group">
                            <label>Odontólogo:</label>
                            <select name="idUsuariosEditar" id="idUsuariosEditar" class="form-control" required>
                              <option value="">Seleccionar odontólogo</option>
                              <?php
                              $usuarios = ControladorUsuarios::ctrMostrarUsuarios(null, null);
                              foreach ($usuarios as $u) {
                                echo "<option value='{$u['idUsuarios']}'>{$u['nombre']}</option>";
                              }
                              ?>
                            </select>
                          </div>
                          <div class="col-md-6 form-group">
                            <label>Paciente:</label>
                            <select name="idPacienteEditar" id="idPacienteEditar" class="form-control" required>
                              <option value="">Seleccionar paciente</option>
                              <?php
                              $pacientes = ControladorPaciente::ctrMostrarPaciente(null, null);
                              foreach ($pacientes as $p) {
                                echo "<option value='{$p['idPaciente']}'>{$p['nombre']}</option>";
                              }
                              ?>
                            </select>
                          </div>
                          <div class="col-md-6 form-group">
                            <label>Fecha Registro:</label>
                            <input type="date" name="fechaRegistroEditar" id="fechaRegistroEditar" class="form-control" required>
                          </div>
                          <div class="col-md-3 form-group">
                            <label>Saldo:</label>
                            <input type="number" name="saldoEditar" id="saldoEditar" class="form-control" step="0.01" required>
                          </div>
                          <div class="col-md-3 form-group">
                            <label>Total Pago:</label>
                            <input type="number" name="totalPagoEditar" id="totalPagoEditar" class="form-control" step="0.01" required>
                          </div>
                          <div class="col-md-6 form-group">
                            <label>Estado:</label>
                            <select name="estadoEditar" id="estadoEditar" class="form-control">
                              <option value="activo">Activo</option>
                              <option value="completado">Completado</option>
                              <option value="cancelado">Cancelado</option>
                            </select>
                          </div>
                        </div>
                      </fieldset>
                    </div>

                    <!-- Medicamentos -->
                    <div class="col-md-6">
                      <fieldset class="mb-4">
                        <legend>Medicamentos Recetados</legend>
                        <div class="row">
                          <div class="col-md-6 form-group">
                            <label>Dosis:</label>
                            <input type="text" name="dosisEditar" id="dosisEditar" class="form-control">
                          </div>
                          <div class="col-md-3 form-group">
                            <label>Inicio:</label>
                            <input type="date" name="fechaInicioEditar" id="fechaInicioEditar" class="form-control">
                          </div>
                          <div class="col-md-3 form-group">
                            <label>Final:</label>
                            <input type="date" name="fechaFinalEditar" id="fechaFinalEditar" class="form-control">
                          </div>
                          <div class="col-md-6 form-group">
                            <label>Tiempo de aplicación:</label>
                            <input type="text" name="tiempoEditar" id="tiempoEditar" class="form-control">
                          </div>
                          <div class="col-md-6 form-group">
                            <label>Medicamento:</label>
                            <select name="codMedicamentoEditar" id="codMedicamentoEditar" class="form-control">
                              <option value="">Seleccionar medicamento</option>
                              <?php
                              $medicamentos = ControladorMedicamento::ctrMostrarMedicamentos(null, null);
                              foreach ($medicamentos as $m) {
                                echo "<option value='{$m['codMedicamento']}'>{$m['nombre']}</option>";
                              }
                              ?>
                            </select>
                          </div>
                          <div class="col-md-12 form-group">
                            <label>Observación:</label>
                            <textarea name="observacionEditar" id="observacionEditar" class="form-control" rows="2"></textarea>
                          </div>
                        </div>
                      </fieldset>
                    </div>
                  </div>

                  <!-- Segunda fila: Odontograma -->
                  <div class="row">
                    <div class="col-md-12">
                      <section class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-4">
                          <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">🦷</div>
                          <h3 class="text-xl font-semibold text-gray-800">Odontograma Registrado</h3>
                        </div>

                        <div id="odontogramPreviewEditar" class="bg-white rounded-lg p-4 border border-gray-200">
                          <div class="row">
                            <div class="col-md-12">
                              <div id="odontogramImageEditar" class="border border-gray-200 rounded-lg p-3 bg-white text-center">
                                <!-- Imagen cargada vía JS -->
                              </div>
                            </div>
                            <div class="col-md-12">
                              <h5 class="text-md font-semibold text-gray-700 mb-3">Resumen clínico:</h5>
                              <div id="teethSummaryEditar" class="d-flex flex-wrap gap-3">
                                <!-- Piezas dentales vía JS -->
                              </div>
                            </div>
                          </div>
                        </div>
                      </section>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Ocultos para odontograma -->
              <input type="hidden" name="odontogramImageEditar" id="odontogramImageInputEditar">
              <input type="hidden" name="odontogramTeethEditar" id="odontogramTeethInputEditar">

              <!-- Footer -->
              <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning">Actualizar Tratamiento</button>
              </div>
            </form>

            <?php
            $editarTratamiento = new ControladorTratamiento();
            $editarTratamiento->ctrEditarTratamientoCompleto(); // función que actualiza en la BD
            ?>
          </div>
        </div>
      </div>



    </div>
  </div>
</div>