$(document).on("click", ".btnVerOdontograma", function() {
    var idTratamiento = $(this).attr("idTratamiento");
    console.log("ID Tratamiento:", idTratamiento); // Verificar el ID enviado

    $.ajax({
        url: "ajax/odontograma.ajax.php",
        method: "POST",
        data: { idTratamiento: idTratamiento },
        dataType: "json",
        success: function(respuesta) {
            console.log("Respuesta AJAX:", respuesta); // Verificar lo que devuelve el servidor
            if (respuesta) {
                $("#odontogramaContenido").html(
                    "<p><strong>Descripción:</strong> " + respuesta.descripcion + "</p>"
                );
            } else {
                $("#odontogramaContenido").html("<p>Error al cargar el odontograma</p>");
            }
        }
    });
});
function seleccionarDiente(id) {
    let diente = document.getElementById("diente-" + id);
    diente.classList.toggle("activo"); // Cambia el color cuando el usuario selecciona
}