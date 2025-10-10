/*============================================= 
EDITAR Y ELIMINAR TRATAMIENTO
=============================================*/
$(document).on("click", ".btnEditarTratamiento", function () {
  var idTratamiento = $(this).attr("idTratamiento");
  var datos = new FormData();
  datos.append("idTratamiento", idTratamiento);

  $.ajax({
    url: "ajax/tratamiento.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {
      $("#editarIdTratamiento").val(respuesta["idTratamiento"]);
      $("#editarFechaRegistro").val(respuesta["fechaRegistro"]);
      $("#editarSaldo").val(respuesta["saldo"]);
      $("#editarTotalPago").val(respuesta["totalPago"]);
      $("#editarEstado").val(respuesta["estado"]);
      $("#editarEstadoPago").val(respuesta["estadoPago"]);
      $("#editarIdPaciente").val(respuesta["idPaciente"]);
    }
  });
});

$(document).on("click", ".btnEliminarTratamiento", function () {
  var idTratamiento = $(this).attr("idTratamiento");
  swal({
    title: '¿Está seguro de borrar el tratamiento?',
    text: "¡Si no lo está, puede cancelar la acción!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Cancelar',
    confirmButtonText: 'Sí, borrar tratamiento!'
  }).then(function (result) {
    if (result.value) {
      window.location = "index.php?ruta=tratamiento&idTratamiento=" + idTratamiento;
    }
  });
});

/*============================================= 
VALIDACIONES BÁSICAS
=============================================*/
document.addEventListener("DOMContentLoaded", function () {
  const saldoInput = document.querySelector('input[name="nuevoSaldo"]');
  const totalPagoInput = document.querySelector('input[name="nuevoTotalPago"]');
  const estadoInput = document.querySelector('input[name="nuevoEstado"]');
  const estadoPagoInput = document.querySelector('input[name="nuevoEstadoPago"]');

  function limpiarTexto(input) {
    input.value = input.value.replace(/[^a-zA-ZÁÉÍÓÚáéíóúñÑ\s]/g, '');
  }

  estadoInput.addEventListener("input", () => limpiarTexto(estadoInput));
  estadoPagoInput.addEventListener("input", () => limpiarTexto(estadoPagoInput));

  saldoInput.addEventListener("input", () => {
    saldoInput.value = saldoInput.value.replace(/[^0-9.]/g, '');
  });

  totalPagoInput.addEventListener("input", () => {
    totalPagoInput.value = totalPagoInput.value.replace(/[^0-9.]/g, '');
  });

  document.querySelector("form").addEventListener("submit", function (event) {
    if (!saldoInput.value.trim() || !totalPagoInput.value.trim() || !estadoInput.value.trim() || !estadoPagoInput.value.trim()) {
      swal({
        title: 'Error en el formulario',
        text: 'Todos los campos deben estar completos y sin caracteres inválidos.',
        type: 'error',
        confirmButtonColor: '#d33'
      });
      event.preventDefault();
    }
  });
});

/*============================================= 
GESTIÓN DE MEDICAMENTOS (MODAL INDIVIDUAL)
=============================================*/
document.addEventListener('DOMContentLoaded', function() {

  // Variables modal
  const btnAgregar = document.getElementById('btnAgregarMedicamento');
  const selectMedicamento = document.getElementById('selectMedicamentoForm');
  const dosisInput = document.getElementById('dosisMedicamento');
  const inicioInput = document.getElementById('fechaInicioMedicamento');
  const finInput = document.getElementById('fechaFinalMedicamento');
  const tiempoInput = document.getElementById('tiempoMedicamento');
  const observacionInput = document.getElementById('observacionMedicamento');
  const listaTemporal = document.getElementById('listaTemporalMedicamentos');
  const form = document.getElementById('formMedicamentos');
  const idTratamientoInput = document.getElementById('idTratamientoMedicamentos');
  const msIdTitle = document.getElementById('ms_id_title');

  let medicamentosSeleccionados = [];

  // Abrir modal desde el botón
document.querySelectorAll('.btnMedicamentosServicios').forEach(btn=>{
  btn.addEventListener('click', function(){
    const idTratamiento = this.getAttribute('data-id');
    idTratamientoInput.value = idTratamiento;
    msIdTitle.textContent = idTratamiento;

    // Limpiar lista de medicamentos seleccionados
    medicamentosSeleccionados = [];
    renderLista();

    // ================= Fecha actual =================
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2,'0');
    const dd = String(today.getDate()).padStart(2,'0');
    const fechaActual = `${yyyy}-${mm}-${dd}`;
    
    inicioInput.value = fechaActual;
    finInput.value = fechaActual;

    // Mostrar modal
    $('#modalMedicamentos').modal('show');
  });
});

  // Renderizar lista
  function renderLista(){
    listaTemporal.innerHTML = '';
    medicamentosSeleccionados.forEach((m, idx)=>{
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.innerHTML = `
        <div>
          <strong>${m.nombre}</strong>
          <div class="small">Dosis: ${m.dosis} | Inicio: ${m.inicio} | Final: ${m.fin} | Tiempo: ${m.tiempo}${m.observacion ? ' | Obs: ' + m.observacion : ''}</div>
        </div>
        <button type="button" class="btn btn-danger btn-sm" data-idx="${idx}">&times;</button>
      `;
      listaTemporal.appendChild(li);
    });

    listaTemporal.querySelectorAll('button.btn-danger').forEach(btn=>{
      btn.addEventListener('click', function(){
        medicamentosSeleccionados.splice(parseInt(this.dataset.idx),1);
        renderLista();
      });
    });
  }

  // Agregar medicamento
  btnAgregar.addEventListener('click', function(){
    const id = selectMedicamento.value;
    const nombre = selectMedicamento.options[selectMedicamento.selectedIndex].text;
    const dosis = dosisInput.value.trim();
    const inicio = inicioInput.value;
    const fin = finInput.value;
    const tiempo = tiempoInput.value.trim();
    const obs = observacionInput.value.trim();

    if(!id || !dosis || !inicio || !fin || !tiempo){
      alert('Completa todos los campos');
      return;
    }

    medicamentosSeleccionados.push({id,nombre,dosis,inicio,fin,tiempo,observacion: obs});
    renderLista();

    // Limpiar campos
    selectMedicamento.value = '';
    dosisInput.value = '';
    inicioInput.value = '';
    finInput.value = '';
    tiempoInput.value = '';
    observacionInput.value = '';
  });

  // Enviar formulario
  form.addEventListener('submit', function(e){
    if(medicamentosSeleccionados.length === 0){
      alert('Agrega al menos un medicamento');
      e.preventDefault();
      return;
    }

    form.querySelectorAll('.tmp-input').forEach(el=>el.remove());

    medicamentosSeleccionados.forEach(m=>{
      ['codMedicamento','dosis','fechaInicio','fechaFinal','tiempo','observacion'].forEach(k=>{
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = k+'[]';
        input.value = ({
          codMedicamento: m.id,
          dosis: m.dosis,
          fechaInicio: m.inicio,
          fechaFinal: m.fin,
          tiempo: m.tiempo,
          observacion: m.observacion
        })[k];
        input.className = 'tmp-input';
        form.appendChild(input);
      });
    });
  });

});

/*============================================= 
VER Y ACTUALIZAR PAGOS DE TRATAMIENTO
=============================================*/
$(document).on("click", ".btnVerPagos", function () {
  const idTratamiento = $(this).data("idtratamiento");

  $.post("ajax/pagosTratamiento.ajax.php", { idTratamiento }, function (respuesta) {
    $("#contenidoPagosTratamiento").html(respuesta);

    const estado = $("#estadoPagoReal").text().trim();
    const saldo  = $("#saldoActualizado").text().trim();

    const estadoPagoSelect = $("#estadoPago");
    estadoPagoSelect.val(estado);
    estadoPagoSelect.removeClass("bg-red-100 bg-yellow-100 bg-green-100");

    if (estado === "pendiente") {
      estadoPagoSelect.addClass("bg-red-100").prop("disabled", false);
    } else if (estado === "parcial") {
      estadoPagoSelect.addClass("bg-yellow-100").prop("disabled", false);
    } else if (estado === "pagado") {
      estadoPagoSelect.addClass("bg-green-100").prop("disabled", true);
    }

    $("#saldoEditar").val(saldo);

    const celdaEstado = $("#estado-" + idTratamiento);
    if (celdaEstado.length) {
      celdaEstado.text(estado.charAt(0).toUpperCase() + estado.slice(1));
    }

    const celdaSaldo = $("#saldo-" + idTratamiento);
    if (celdaSaldo.length) {
      celdaSaldo.text(saldo + " Bs");
    }
  });
});

/*============================================= 
SELECCIONAR CITA PARA TRATAMIENTO
=============================================*/
$(document).on("click", ".seleccionar-cita", function () {
    let idCita = $(this).data("idcita");
    let idPaciente = $(this).data("idpaciente");
    let idUsuario = $(this).data("idusuario");

    $(".seleccionar-cita").removeClass("active bg-success text-white");
    $(this).addClass("active bg-success text-white");

    $("select[name='nuevoIdPaciente']").val(idPaciente);
    $("select[name='nuevoIdUsuarios']").val(idUsuario);
    $("#idCitaSeleccionada").val(idCita);

    Swal.fire({
        icon: "success",
        title: "Datos cargados",
        text: "Paciente y odontólogo asignados al tratamiento",
        timer: 1500,
        showConfirmButton: false
    });

    fetch("modelo/citas/actualizarEstadoCita.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ idCita: idCita, nuevoEstado: "atendida" })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) { $(this).remove(); }
        else {
            Swal.fire({icon:"error",title:"Error",text:"No se pudo actualizar el estado de la cita."});
        }
    })
    .catch(err => {
        Swal.fire({icon:"error",title:"Error",text:"Error en la comunicación con el servidor."});
        console.error("Error fetch:", err);
    });
});

// Filtrar citas por CI
document.getElementById('buscarCICitas').addEventListener('input', function() {
  let ci = this.value.trim();
  let lista = document.querySelectorAll('#listaCitasConfirmadas li');
  lista.forEach(item => {
    item.style.display = item.dataset.ci.includes(ci) ? '' : 'none';
  });
});

// JS para agregar servicios ====================== -->

document.addEventListener('DOMContentLoaded', function() {
    const selectServicioForm = document.getElementById('selectServicioForm');
    const btnAgregarServicioForm = document.getElementById('btnAgregarServicioForm');
    const listaServicios = document.getElementById('listaServicios');
    const totalPagoInput = document.getElementById('totalPago');
    const saldoInput = document.getElementById('saldo');

    let serviciosSeleccionados = [];

    function actualizarLista() {
        listaServicios.innerHTML = '';
        let total = 0;

        serviciosSeleccionados.forEach((s,index)=>{
            total += parseFloat(s.precio);
            const li = document.createElement('li');
            li.classList.add('list-group-item','d-flex','justify-content-between','align-items-center');
            li.innerHTML = `
                ${s.nombre} - Bs. ${parseFloat(s.precio).toFixed(2)}
                <button type="button" class="btn btn-danger btn-sm" data-index="${index}">&times;</button>
                <input type="hidden" name="servicios[]" value="${s.id}">
            `;
            listaServicios.appendChild(li);
        });

        totalPagoInput.value = total.toFixed(2);
        saldoInput.value = total.toFixed(2);

        document.querySelectorAll('#listaServicios button.btn-danger').forEach(btn=>{
            btn.addEventListener('click', function(){
                const i = parseInt(this.dataset.index);
                serviciosSeleccionados.splice(i,1);
                actualizarLista();
            });
        });
    }

    btnAgregarServicioForm.addEventListener('click', function(){
        const option = selectServicioForm.selectedOptions[0];
        if(option && option.value != ''){
            const id = option.value;
            const nombre = option.text.split(' - Bs.')[0];
            const precio = parseFloat(option.dataset.precio);

            if(!serviciosSeleccionados.find(s=>s.id==id)){
                serviciosSeleccionados.push({id,nombre,precio});
                actualizarLista();
            }

            selectServicioForm.value = '';
        }
    });
});
// DataTables initialization
$(document).ready(function() {
    $('#data_table').DataTable(); // Tratamientos
    $('#data_table_medicamentos').DataTable(); // Medicamentos
});
