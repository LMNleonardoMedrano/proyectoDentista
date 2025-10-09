/*=============================================
EDITAR TRATAMIENTO
=============================================*/
$(document).on("click", ".btnEditarTratamiento", function () {
  var idTratamiento = $(this).attr("idTratamiento");
  var datos = new FormData();
  datos.append("idTratamiento", idTratamiento);

  $.ajax({
    url: "ajax/tratamientos.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {
      $("#idTratamientoEditar").val(respuesta["idTratamiento"]);
      $("#idPacienteEditar").val(respuesta["idPaciente"]);
      $("#idUsuariosEditar").val(respuesta["idUsuarios"]);
      $("#fechaRegistroEditar").val(respuesta["fechaRegistro"]);
      $("#saldoEditar").val(respuesta["saldo"]);
      $("#totalPagoEditar").val(respuesta["totalPago"]);
      $("#estadoEditar").val(respuesta["estado"]);
      $("#dosisEditar").val(respuesta["dosis"]);
      $("#fechaInicioEditar").val(respuesta["fechaInicio"]);
      $("#fechaFinalEditar").val(respuesta["fechaFinal"]);
      $("#tiempoEditar").val(respuesta["tiempo"]);
      $("#observacionEditar").val(respuesta["observacion"]);

      // Mostrar odontograma si existe
      if (respuesta["foto"]) {
        $("#odontogramImageEditar").html('<img src="' + respuesta["foto"] + '" class="img-fluid" />');
      }
    }
  });
});

/*=============================================
VER PAGOS Y ACTUALIZAR ESTADO/SALDO
=============================================*/
$(document).on("click", ".btnVerPagos", function () {
  const idTratamiento = $(this).data("idtratamiento");

  $.post("ajax/pagosTratamiento.ajax.php", { idTratamiento }, function (respuesta) {
    $("#contenidoPagosTratamiento").html(respuesta);

    // 🔁 Capturar valores ocultos del Ajax
    const estado = $("#estadoPagoReal").text().trim();  // "pagado", "pendiente", "parcial"
    const saldo  = $("#saldoActualizado").text().trim(); // "0.00", etc.

    // ✅ Actualizar <select> del formulario de edición
    const estadoPagoSelect = $("#estadoPago");
    estadoPagoSelect.val(estado);
    estadoPagoSelect.removeClass("bg-red-100 bg-yellow-100 bg-green-100");

    // 🔁 Estilizar visualmente el estado
    if (estado === "pendiente") {
      estadoPagoSelect.addClass("bg-red-100").prop("disabled", false);
    } else if (estado === "parcial") {
      estadoPagoSelect.addClass("bg-yellow-100").prop("disabled", false);
    } else if (estado === "pagado") {
      estadoPagoSelect.addClass("bg-green-100").prop("disabled", true);
    }

    // ✅ Actualizar campo saldo si existe
    $("#saldoEditar").val(saldo);

    // ✅ Actualizar celdas específicas en la tabla si tienen ID
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

// 🔁 Recargar tabla completa al cerrar el modal
function recargarTablaTratamientos() {
  $.get("ajax/tablaTratamientos.ajax.php", function (html) {
    $("#tablaTratamientos tbody").html(html); // Ajustá el ID si es diferente en tu tabla
  });
}

$('#modalPagosTratamiento').on('hidden.bs.modal', function () {
  recargarTablaTratamientos();
});


// 🧩 Delegación para quitar y duplicar bloques
document.addEventListener('click', function (e) {
  const container = document.getElementById('medicamentos-recetados');

  // ❌ Quitar bloque
  if (e.target.classList.contains('remove-medicamento')) {
    const bloque = e.target.closest('.medicamento-block');
    if (container.children.length > 1) {
      bloque.remove();
    }
  }

  // 📋 Duplicar bloque con datos
  if (e.target.classList.contains('duplicate-medicamento')) {
    const bloque = e.target.closest('.medicamento-block');
    const nuevoBloque = bloque.cloneNode(true);
    container.appendChild(nuevoBloque);
  }
});
// 🗓 Asignar fecha actual en formato YYYY-MM-DD
function setFechaActual(input) {
  const hoy = new Date();
  const yyyy = hoy.getFullYear();
  const mm = String(hoy.getMonth() + 1).padStart(2, '0');
  const dd = String(hoy.getDate()).padStart(2, '0');
  input.value = `${yyyy}-${mm}-${dd}`;
}

// 🧬 Al cargar la página, asignar fecha actual a campos iniciales
window.addEventListener('DOMContentLoaded', function () {
  const fechaInicio = document.querySelector('#medicamentos-recetados input[name="fechaInicio[]"]');
  const fechaRegistro = document.querySelector('input[name="fechaRegistro"]');
  if (fechaInicio) setFechaActual(fechaInicio);
  if (fechaRegistro) setFechaActual(fechaRegistro);
});

// ➕ Añadir nuevo bloque vacío con fecha actual
document.getElementById('add-medicamento').addEventListener('click', function () {
  const container = document.getElementById('medicamentos-recetados');
  const bloqueOriginal = container.querySelector('.medicamento-block');
  const nuevoBloque = bloqueOriginal.cloneNode(true);

  // Limpiar campos
  nuevoBloque.querySelectorAll('input, select, textarea').forEach(el => {
    if (el.tagName === 'SELECT') {
      el.selectedIndex = 0;
    } else {
      el.value = '';
    }
  });

  // Asignar fecha actual a fechaInicio si existe
  const fechaInicio = nuevoBloque.querySelector('input[name="fechaInicio[]"]');
  if (fechaInicio) setFechaActual(fechaInicio);

  container.appendChild(nuevoBloque);
});


