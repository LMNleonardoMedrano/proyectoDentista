/*=============================================
EDITAR CLIENTE
=============================================*/
$(document).on("click", ".btnEditarTutorPadre", function () {

  var IdTutorPadre = $(this).attr("IdTutorPadre");

  var datos = new FormData();
  datos.append("IdTutorPadre", IdTutorPadre);

  $.ajax({

    url: "ajax/tutorPadre.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {

      $("#editarIdTutorPadre").val(respuesta["IdTutorPadre"]);
      $("#editaridPaciente").val(respuesta["idPaciente"]);
      $("#editarNombre").val(respuesta["Nombre"]);
      $("#editarDomicilio").val(respuesta["Domicilio"]);
      $("#editarFechaNacimiento").val(respuesta["FechaNac"]);
      $("#editarGenero").val(respuesta["Genero"]);
      $("#editarCI").val(respuesta["Ci"]);
      $("#editarOcupacion").val(respuesta["Ocupacion"]);
      $("#editarRelacion").val(respuesta["Relacion"]);
      $("#editarTelCel").val(respuesta["TelCel"]);
    }

  })

})

/*=============================================
ELIMINAR CLIENTE
=============================================*/
$(document).on("click", ".btnEliminarTutorPadre", function () {

  var IdTutorPadre = $(this).attr("IdTutorPadre");

  swal({
    title: '¿Está seguro de borrar el tutor o padre?',
    text: "¡Si no lo está puede cancelar la acción!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Cancelar',
    confirmButtonText: 'Si, borrar tutor o padre!'
  }).then(function (result) {
    if (result.value) {

      window.location = "index.php?ruta=tutorPadre&IdTutorPadre=" + IdTutorPadre;
    }

  })

})