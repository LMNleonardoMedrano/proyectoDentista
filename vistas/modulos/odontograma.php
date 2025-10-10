<div class="wrapper">
    <div class="page-wrap">
        <div class="main-content">
            <div class="content-wrapper">

                <section class="content-header">
                    <h1>Odontograma</h1>
                    <ol class="breadcrumb">
                        <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
                        <li class="active">Odontograma</li>
                    </ol>
                </section>

                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarTratamiento">
                                Agregar Tratamiento
                            </button>
                        </div>

                        <div class="box-body">
                            <!-- Odontograma Visual -->
                            <div class="odontograma-container text-center mb-4">
                                <div class="odontograma" id="odontograma">
                                    <!-- Dientes se cargan dinámicamente con JS -->
                                    <!-- Dientes del 11 al 18 (superior derecho) -->
                                    <?php
                                    $dientes = [
                                        ['pieza' => 18, 'top' => 90, 'left' => 700],
                                        ['pieza' => 17, 'top' => 90, 'left' => 650],
                                        ['pieza' => 16, 'top' => 90, 'left' => 600],
                                        ['pieza' => 15, 'top' => 90, 'left' => 550],
                                        ['pieza' => 14, 'top' => 90, 'left' => 500],
                                        ['pieza' => 13, 'top' => 90, 'left' => 450],
                                        ['pieza' => 12, 'top' => 90, 'left' => 400],
                                        ['pieza' => 11, 'top' => 90, 'left' => 350],
                                        ['pieza' => 21, 'top' => 90, 'left' => 300],
                                        ['pieza' => 22, 'top' => 90, 'left' => 250],
                                        ['pieza' => 23, 'top' => 90, 'left' => 200],
                                        ['pieza' => 24, 'top' => 90, 'left' => 150],
                                        ['pieza' => 25, 'top' => 90, 'left' => 100],
                                        ['pieza' => 26, 'top' => 90, 'left' => 50],
                                        ['pieza' => 27, 'top' => 90, 'left' => 0],
                                        ['pieza' => 28, 'top' => 90, 'left' => -50],
                                        // Inferiores 31 al 48
                                        ['pieza' => 48, 'top' => 250, 'left' => 700],
                                        ['pieza' => 47, 'top' => 250, 'left' => 650],
                                        ['pieza' => 46, 'top' => 250, 'left' => 600],
                                        ['pieza' => 45, 'top' => 250, 'left' => 550],
                                        ['pieza' => 44, 'top' => 250, 'left' => 500],
                                        ['pieza' => 43, 'top' => 250, 'left' => 450],
                                        ['pieza' => 42, 'top' => 250, 'left' => 400],
                                        ['pieza' => 41, 'top' => 250, 'left' => 350],
                                        ['pieza' => 31, 'top' => 250, 'left' => 300],
                                        ['pieza' => 32, 'top' => 250, 'left' => 250],
                                        ['pieza' => 33, 'top' => 250, 'left' => 200],
                                        ['pieza' => 34, 'top' => 250, 'left' => 150],
                                        ['pieza' => 35, 'top' => 250, 'left' => 100],
                                        ['pieza' => 36, 'top' => 250, 'left' => 50],
                                        ['pieza' => 37, 'top' => 250, 'left' => 0],
                                        ['pieza' => 38, 'top' => 250, 'left' => -50],
                                    ];
                                    foreach ($dientes as $d) {
                                        echo "<div class='diente' data-pieza='{$d['pieza']}' style='position: absolute; top: {$d['top']}px; left: {$d['left']}px; width: 40px; height: 50px;'>
                <div class='zona oclusal' data-zona='oclusal'></div>
                <div class='zona vestibular' data-zona='vestibular'></div>
                <div class='zona lingual' data-zona='lingual'></div>
                <div class='zona mesial' data-zona='mesial'></div>
                <div class='zona distal' data-zona='distal'></div>
            </div>";
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- Tabla de Tratamientos -->
                            <table id="tablaTratamientos" class="table table-hover" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Pieza</th>
                                        <th>Tratamiento</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Se rellenará con PHP o JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

            </div>

            <!-- Modal Agregar Tratamiento -->
            <div id="modalAgregarTratamiento" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="post">
                            <div class="modal-header" style="background:#50c878; color:white;">
                                <h5 class="modal-title w-100 text-center">Agregar Tratamiento</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body px-5">
                                <input type="hidden" id="piezaSeleccionada" name="piezaSeleccionada">

                                <div class="form-group">
                                    <label for="tratamiento">Tratamiento:</label>
                                    <select class="form-control" name="tratamiento" id="tratamiento" required>
                                        <option value="">Seleccione</option>
                                        <option value="Extracción">Extracción</option>
                                        <option value="Carilla">Carilla</option>
                                        <option value="Corona">Corona</option>
                                        <option value="Implante">Implante</option>
                                        <option value="Endodoncia">Endodoncia</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="estado">Estado:</label>
                                    <select class="form-control" name="estado" id="estado" required>
                                        <option value="Previsto">Previsto</option>
                                        <option value="Realizado">Realizado</option>
                                        <option value="Cancelado">Cancelado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-center">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-outline-success" style="background-color:#50c878; color:white;">Guardar Tratamiento</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ESTILOS -->
<style>
    .odontograma {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
        max-width: 700px;
        margin: auto;
        padding: 20px;
        background-size: cover;
        background-position: center;
        position: relative;
        height: 400px;
    }

    .diente {
        width: 40px;
        height: 60px;
        border: 1px solid #ccc;
        border-radius: 4px;
        overflow: hidden;
        cursor: pointer;
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .diente:hover {
        background-color: #f0f0f0;
    }

    .diente.seleccionado {
        background-color: #50c878;
    }

    .img-diente {
        width: 100%;
        height: 100%;
        object-fit: contain;
        pointer-events: none;
        /* para que el clic llegue al div padre */
    }

    .zona {
        position: absolute;
        background-color: rgba(80, 200, 120, 0.2);
        border: 1px solid #50c878;
        border-radius: 3px;
    }

    .oclusal {
        top: 0;
        left: 5px;
        width: 30px;
        height: 10px;
    }

    .vestibular {
        top: 10px;
        left: 0;
        width: 10px;
        height: 30px;
    }

    .lingual {
        top: 10px;
        right: 0;
        width: 10px;
        height: 30px;
    }

    .mesial {
        bottom: 0;
        left: 5px;
        width: 10px;
        height: 10px;
    }

    .distal {
        bottom: 0;
        right: 5px;
        width: 10px;
        height: 10px;
    }

    .diente:hover .zona {
        background-color: rgba(80, 200, 120, 0.4);
        cursor: pointer;
    }
</style>

<!-- SCRIPT -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const odontograma = document.getElementById("odontograma");
        const dientes = [
            18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28,
            48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38
        ];

        dientes.forEach(numero => {
            const diente = document.createElement("div");
            diente.classList.add("diente");
            diente.dataset.numero = numero;

            const img = document.createElement("img");
            img.src = `vistas/img/dientes/${numero}.jpg`; // Asegúrate de tener estas imágenes
            img.alt = `Diente ${numero}`;
            img.classList.add("img-diente");

            diente.appendChild(img);

            diente.addEventListener("click", function() {
                document.querySelectorAll(".diente").forEach(d => d.classList.remove("seleccionado"));
                diente.classList.add("seleccionado");
                document.getElementById("piezaSeleccionada").value = numero;
                $("#modalAgregarTratamiento").modal("show");
            });

            odontograma.appendChild(diente);
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Al hacer clic en una zona del diente
        document.querySelectorAll('.zona').forEach(function(zona) {
            zona.addEventListener('click', function(e) {
                e.preventDefault();

                // Cambia entre sin marcar, marcada y desmarcada
                if (this.style.backgroundColor === 'rgb(255, 0, 0)') {
                    // Segundo clic: desmarca (color transparente)
                    this.style.backgroundColor = 'rgba(80, 200, 120, 0.2)';
                } else {
                    // Primer clic: marca (rojo)
                    this.style.backgroundColor = 'rgb(255, 0, 0)';
                }

                const pieza = this.closest('.diente').dataset.pieza;
                const zona = this.dataset.zona;

                console.log(`Clic en diente ${pieza}, zona ${zona}`);
                // Aquí podrías enviar una llamada AJAX para guardar el cambio
            });
        });
    });
</script>