/*=============================================
EDITAR MEDICAMENTO
=============================================*/
$(document).on("click", ".btnEditarMedicamento", function () {

  var codMedicamento = $(this).attr("codMedicamento");

  var datos = new FormData();
  datos.append("codMedicamento", codMedicamento);

  $.ajax({

    url: "ajax/medicamentos.ajax.php", // ✅ Actualizado
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {

      $("#editarCodMedicamento").val(respuesta["codMedicamento"]);
      $("#editarNombre").val(respuesta["nombre"]);
      $("#editarTipo").val(respuesta["tipo"]);
      $("#editarMedida").val(respuesta["medida"]);
      $("#editarTiempo").val(respuesta["tiempo"]);

    }

  });
});

/*=============================================
ELIMINAR MEDICAMENTO
=============================================*/
$(document).on("click", ".btnEliminarMedicamento", function () {

  var codMedicamento = $(this).attr("codMedicamento");

  swal({
    title: '¿Está seguro de borrar el medicamento?',
    text: "¡Si no lo está, puede cancelar la acción!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Cancelar',
    confirmButtonText: 'Sí, borrar medicamento!'
  }).then(function (result) {
    if (result.value) {
      window.location = "index.php?ruta=medicamentos&codMedicamento=" + codMedicamento;
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
