/*=============================================
EDITAR SERVICIO
=============================================*/
$(document).on("click", ".btnEditarServicio", function () {

  var idServicio = $(this).attr("idServicio");

  var datos = new FormData();
  datos.append("idServicio", idServicio);

  $.ajax({

    url: "ajax/servicios.ajax.php", // Archivo AJAX de servicios
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {

      $("#editarIdServicio").val(respuesta["idServicio"]);
      $("#editarNombreServicio").val(respuesta["nombreServicio"]);
      $("#editarDescripcion").val(respuesta["descripcion"]);
      $("#editarPrecio").val(respuesta["precio"]);

    }

  });
});

/*=============================================
ELIMINAR SERVICIO
=============================================*/
$(document).on("click", ".btnEliminarServicio", function () {

  var idServicio = $(this).attr("idServicio");

  swal({
    title: '¿Está seguro de borrar el servicio?',
    text: "¡Si no lo está, puede cancelar la acción!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Cancelar',
    confirmButtonText: 'Sí, borrar servicio!'
  }).then(function (result) {
    if (result.value) {
      window.location = "index.php?ruta=servicios&idServicio=" + idServicio;
    }
  });

});


document.addEventListener("DOMContentLoaded", function () {

    const nombreInput = document.querySelector('input[name="nuevoNombreServicio"]');
    const descripcionInput = document.querySelector('textarea[name="nuevaDescripcion"]');
    const precioInput = document.querySelector('input[name="nuevoPrecio"]');

    // Validación: Evitar caracteres especiales en Nombre
    function limpiarTexto(input) {
        input.value = input.value.replace(/[^a-zA-ZÁÉÍÓÚáéíóúñÑ0-9\s]/g, '');
    }

    nombreInput.addEventListener("input", function () { limpiarTexto(this); });
    descripcionInput.addEventListener("input", function () { limpiarTexto(this); });

    // Validación: Solo números y punto en Precio
    precioInput.addEventListener("input", function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });

    // Validación en envío del formulario
    document.querySelector("form").addEventListener("submit", function (event) {
        if (!nombreInput.value.trim() || !descripcionInput.value.trim() || !precioInput.value.trim()) {
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
