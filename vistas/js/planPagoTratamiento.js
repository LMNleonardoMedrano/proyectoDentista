
/*=============================================
EDITAR PLAN DE PAGO
=============================================*/
$(document).on("click", ".btnEditarPlanPago", function () {

  var codPlan = $(this).attr("codPlan");

  var datos = new FormData();
  datos.append("codPlan", codPlan);

  $.ajax({

    url: "ajax/planPago.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {

      $("#idPlanPagoEditar").val(respuesta["codPlan"]);
      $("#editarDescripcion").val(respuesta["descripcion"]);
      $("#editarDescuento").val(respuesta["descuento"]);
      $("#editarFecha").val(respuesta["fecha"]);
      $("#editarMonto").val(respuesta["monto"]);
      $("#editarTipoPago").val(respuesta["codTipoPago"]);

      $("#modalEditarPlanPago").modal("show");

    }

  });

});

/*=============================================
ELIMINAR MEDICAMENTO
=============================================*/
$(document).on("click", ".btnEliminarPlanPago", function () {

  var codPlan = $(this).attr("codPlan");

  swal({
    title: '¿Está seguro de borrar el Pago?',
    text: "¡Si no lo está, puede cancelar la acción!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Cancelar',
    confirmButtonText: 'Sí, borrar Pago!'
  }).then(function (result) {
    if (result.value) {
      window.location = "index.php?ruta=planPagoTratamiento&codPlan=" + codPlan;
    }
  });

});


document.addEventListener("DOMContentLoaded", function () {
    const nombreInput = document.querySelector('input[name="nuevoNombre"]');
    const tipoInput = document.querySelector('input[name="nuevoTipo"]');
    

    // Validación: Evitar caracteres especiales en Nombre y Tipo
    function limpiarTexto(input) {
        input.value = input.value.replace(/[^a-zA-ZÁÉÍÓÚáéíóúñÑ\s]/g, '');
    }

    nombreInput.addEventListener("input", function () { limpiarTexto(this); });
    tipoInput.addEventListener("input", function () { limpiarTexto(this); });

    // Validación: Solo números en Medida y Tiempo
    medidaInput.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, '');
    });

    tiempoInput.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, '');
    });

    // Validación en envío del formulario
    document.querySelector("form").addEventListener("submit", function (event) {
        if (!nombreInput.value.trim() || !tipoInput.value.trim() || !medidaInput.value.trim() || !tiempoInput.value.trim()) {
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
MOSTRAR U OCULTAR EL CONTENEDOR DEL CÓDIGO QR
=============================================*/
const selectTipoPago = document.getElementById('nuevoTipoPago');
  const contenedorQR = document.getElementById('contenedorQR');

  selectTipoPago.addEventListener('change', function() {
    const opcionSeleccionada = selectTipoPago.options[selectTipoPago.selectedIndex].text;
    if(opcionSeleccionada === 'QR de recibo') {
      contenedorQR.style.display = 'block';
    } else {
      contenedorQR.style.display = 'none';
    }
  });
/*=============================================
SELECCIONAR TRATAMIENTO Y AUTOCOMPLETAR MONTO
=============================================*/
$(document).on("click", ".seleccionar-tratamiento", function(){
  let idTratamiento = $(this).data("idtratamiento");
  let saldoTratamiento = parseFloat($(this).data("saldo")); // saldo del tratamiento

  // Llenar el select del tratamiento
  $("#nuevoTratamiento").val(idTratamiento);

  // Llenar automáticamente el monto del plan con el saldo del tratamiento
  $("#montoPlan").val(saldoTratamiento.toFixed(2));
  $("#montoPlan").attr("max", saldoTratamiento); // Limita el máximo permitido

  // Resaltar tratamiento seleccionado
 $(".seleccionar-tratamiento").removeClass("active bg-success text-white")
    .css({ "background-color": "", "color": "" }); // quitar estilos previos

$(this).addClass("active") // mantenemos la clase active
       .css({
           "background-color": "#befafaff", // verde suave
           "color": "#000000"             // texto negro
       });

  // Verificar tipo de pago
  let tipoPagoTexto = $("#nuevoTipoPago option:selected").text().trim().toLowerCase();
  let app = tipoPagoTexto === "yape" ? "yape" : "qr";

  if ((tipoPagoTexto === "qr de recibo" || tipoPagoTexto === "yape") && saldoTratamiento > 0) {
    let qrURL = `vistas/modulos/qrPagoDirecto.php?idTratamiento=${idTratamiento}&monto=${saldoTratamiento.toFixed(2)}&app=${app}`;
    $("#imagenQRPago").attr("src", qrURL);
    $("#contenedorQR").show();
  } else {
    $("#contenedorQR").hide();
  }
});

$("#montoPlan, #nuevoTipoPago").on("input change", function () {
  let idTratamiento = $("#nuevoTratamiento").val();
  let monto = parseFloat($("#montoPlan").val());
  let tipoPagoTexto = $("#nuevoTipoPago option:selected").text().trim().toLowerCase();
  let app = tipoPagoTexto === "yape" ? "yape" : "qr";

  if (!idTratamiento || isNaN(monto) || monto <= 0 || (tipoPagoTexto !== "qr de recibo" && tipoPagoTexto !== "yape")) {
    $("#contenedorQR").hide();
    return;
  }

  let qrURL = `vistas/modulos/qrPagoDirecto.php?idTratamiento=${idTratamiento}&monto=${monto.toFixed(2)}&app=${app}`;
  $("#imagenQRPago").attr("src", qrURL);
  $("#contenedorQR").show();
});
 // 🔹 Autocompletar con la fecha actual
  window.addEventListener('DOMContentLoaded', () => {
  const fechaInput = document.getElementById('nuevoFecha');
  if (!fechaInput.value) {
    const hoy = new Date();
    const yyyy = hoy.getFullYear();
    const mm = String(hoy.getMonth() + 1).padStart(2, '0');
    const dd = String(hoy.getDate()).padStart(2, '0');
    fechaInput.value = `${yyyy}-${mm}-${dd}`;
  }
});

  // 🔹 Filtrar por CI (tu código existente)
// 🔹 Filtrar por CI o nombre
document.getElementById('buscarCI').addEventListener('input', function() {
    let filtro = this.value.trim().toLowerCase();

    // 🔍 Filtrar lista de tratamientos disponibles
    let lista = document.querySelectorAll('#listaTratamientosDisponibles li');
    lista.forEach(item => {
        let ci = item.dataset.ci?.toLowerCase() || '';
        let nombre = item.dataset.nombre?.toLowerCase() || '';
        item.style.display = (ci.includes(filtro) || nombre.includes(filtro)) ? '' : 'none';
    });

    // 🔍 Filtrar opciones del select de tratamiento
    let select = document.getElementById('nuevoTratamiento');
    for (let i = 0; i < select.options.length; i++) {
        let option = select.options[i];
        let ci = option.dataset.ci?.toLowerCase() || '';
        let nombre = option.dataset.nombre?.toLowerCase() || '';
        option.style.display = (filtro === "" || ci.includes(filtro) || nombre.includes(filtro)) ? '' : 'none';
    }
});
/*=============================================
VALIDAR QUE EL MONTO NO EXCEDA EL SALDO DEL TRATAMIENTO
=============================================*/
  $("#formPlanPago").on("submit", function(e) {
  const monto = parseFloat($("#montoPlan").val());
  const saldo = parseFloat($("#nuevoTratamiento option:selected").data("saldo"));

  if (!isNaN(monto) && !isNaN(saldo) && monto > saldo) {
    e.preventDefault();
    Swal.fire({
      icon: "warning",
      title: "Monto excedido",
      text: "El monto no puede ser mayor al saldo disponible (Bs. " + saldo.toFixed(2) + ").",
      confirmButtonText: "Entendido"
    });
  }
});