
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

  // Resaltar tratamiento seleccionado
  $(".seleccionar-tratamiento").removeClass("active bg-success text-white");
  $(this).addClass("active bg-success text-white");
});

 // 🔹 Autocompletar con la fecha actual
  window.addEventListener('DOMContentLoaded', () => {
    const fechaInput = document.getElementById('nuevoFecha');
    const hoy = new Date();
    const yyyy = hoy.getFullYear();
    const mm = String(hoy.getMonth() + 1).padStart(2, '0'); // Mes con 2 dígitos
    const dd = String(hoy.getDate()).padStart(2, '0');      // Día con 2 dígitos
    fechaInput.value = `${yyyy}-${mm}-${dd}`;
  });

  // 🔹 Filtrar por CI (tu código existente)
  document.getElementById('buscarCI').addEventListener('input', function() {
    let ci = this.value.trim();
    let lista = document.querySelectorAll('#listaTratamientosDisponibles li');

    lista.forEach(item => {
      item.style.display = item.dataset.ci.includes(ci) ? '' : 'none';
    });

    // Filtrar select de tratamiento también
    let select = document.getElementById('nuevoTratamiento');
    for (let i = 0; i < select.options.length; i++) {
      let option = select.options[i];
      if (ci === "" || option.dataset.ci.includes(ci)) {
        option.style.display = '';
      } else {
        option.style.display = 'none';
      }
    }
  });