/*=============================================
SUBIENDO LA FOTO DEL USUARIO
=============================================*/
$(".nuevaFoto").change(function(){

	var imagen = this.files[0];

	if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){

		$(".nuevaFoto").val("");

		swal({
			title: "Error al subir la imagen",
			text: "¡La imagen debe estar en formato JPG o PNG!",
			type: "error",
			confirmButtonText: "¡Cerrar!"
		});

	}else if(imagen["size"] > 2000000){

		$(".nuevaFoto").val("");

		swal({
			title: "Error al subir la imagen",
			text: "¡La imagen no debe pesar más de 2MB!",
			type: "error",
			confirmButtonText: "¡Cerrar!"
		});

	}else{

		var datosImagen = new FileReader;
		datosImagen.readAsDataURL(imagen);

		$(datosImagen).on("load", function(event){

			var rutaImagen = event.target.result;

			$(".previsualizar").attr("src", rutaImagen);

		})

	}
})

/*=============================================
EDITAR USUARIO
=============================================*/
$(document).on("click", ".btnEditarUsuario", function() {
    var idUsuarios = $(this).attr("idUsuarios");

    var datos = new FormData();
    datos.append("idUsuarios", idUsuarios);

    console.log("Datos a enviar: ", datos);  // Revisa los valores que se envían

    $.ajax({
        url: "ajax/usuarios.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta) {
            if (respuesta) {
                // Rellenar el formulario con los datos obtenidos
				$("#editaridUsuarios").val(respuesta["idUsuarios"]);
                $("#editarNombre").val(respuesta["nombre"]);
                $("#editarApellido").val(respuesta["apellido"]);
                $("#editarCi").val(respuesta["ci"]);
                $("#editarCorreo").val(respuesta["correo"]);
                $("#editarDomicilio").val(respuesta["domicilio"]);
                $("#editarEstado").val(respuesta["estado"]);
                $("#editarFecha").val(respuesta["fecha"]);
                $("#editarUsuario").val(respuesta["usuario"]);
                $("#fotoActual").val(respuesta["foto"]);
                $("#editarPerfil").val(respuesta["idRol"]);

                $("#passwordActual").val(respuesta["password"]);
                if (respuesta["foto"] != "") {
                    $(".previsualizar").attr("src", respuesta["foto"]);
                }
            }
        }
    });
});



/*=============================================
ACTIVAR USUARIO
=============================================*/
$(document).on("click", ".btnActivar", function(){

	var idUsuarios = $(this).attr("idUsuarios");
	var estadoUsuario = $(this).attr("estadoUsuario");

	var datos = new FormData();
	datos.append("activarId", idUsuarios);
	datos.append("activarUsuario", estadoUsuario);

	$.ajax({

		url:"ajax/usuarios.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		success: function(respuesta){

			if(window.matchMedia("(max-width:767px)").matches){

				swal({
					title: "El usuario ha sido actualizado",
					type: "success",
					confirmButtonText: "¡Cerrar!"
				}).then(function(result) {

					if (result.value) {
						window.location = "usuarios";
					}

				});

			}
		}

	})

	if(estadoUsuario == 0){

		$(this).removeClass('btn-success');
		$(this).addClass('btn-danger');
		$(this).html('Desactivado');
		$(this).attr('estadoUsuario',1);

	}else{

		$(this).addClass('btn-success');
		$(this).removeClass('btn-danger');
		$(this).html('Activado');
		$(this).attr('estadoUsuario', 0);

	}

})

/*=============================================
REVISAR SI EL USUARIO YA ESTÁ REGISTRADO
=============================================*/
$("#nuevoUsuario").change(function(){

	$(".alert").remove();

	var usuario = $(this).val();

	var datos = new FormData();
	datos.append("validarUsuario", usuario);

	$.ajax({
		url:"ajax/usuarios.ajax.php",
		method:"POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success:function(respuesta){

			if(respuesta){

				$("#nuevoUsuario").parent().after('<div class="alert alert-warning">Este usuario ya existe en la base de datos</div>');

				$("#nuevoUsuario").val("");

			}

		}

	})
})

/*=============================================
REVISAR SI EL CI YA ESTÁ REGISTRADO
=============================================*/
$("#nuevoCi").change(function(){

	$(".alert").remove();

	var ci = $(this).val();

	var datos = new FormData();
	datos.append("validarCi", ci);

	$.ajax({
		url:"ajax/usuarios.ajax.php",
		method:"POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success:function(respuesta){

			if(respuesta){

				$("#nuevoCi").parent().after('<div class="alert alert-warning">Este CI ya existe en la base de datos</div>');

				$("#nuevoCi").val("");

			}

		}

	})
});

/*=============================================
ELIMINAR USUARIO
=============================================*/
$(document).on("click", ".btnEliminarUsuario", function(){

	var idUsuarios = $(this).attr("idUsuarios");
	var fotoUsuario = $(this).attr("fotoUsuario");
	var usuario = $(this).attr("usuario");

	swal({
		title: '¿Está seguro de borrar el usuario?',
		text: "¡Si no lo está puede cancelar la acción!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Si, borrar usuario!'
	}).then(function(result){

		if(result.value){

			window.location = "index.php?ruta=usuarios&idUsuarios="+idUsuarios+"&usuario="+usuario+"&fotoUsuario="+fotoUsuario;

		}

	})

});

document.addEventListener("DOMContentLoaded", function () {
    const ciInput = document.querySelector("#nuevoCi");
    const nombreInput = document.querySelector('input[name="nuevoNombre"]');
    const apellidoInput = document.querySelector('input[name="nuevoApellido"]');

    // Validación: Solo números en C.I.
    ciInput.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, ''); // Solo permite números
    });

    ciInput.addEventListener("blur", function () {
        if (this.value.length < 7 || this.value.length > 10) {
            swal({
                title: 'C.I. inválido',
                text: 'El C.I. debe tener entre 7 y 10 dígitos.',
                type: 'error',
                confirmButtonColor: '#d33'
            });
            this.value = "";
        }
    });

    // Validación: Evitar caracteres especiales en nombre y apellido
    function limpiarTexto(input) {
        input.value = input.value.replace(/[^a-zA-ZÁÉÍÓÚáéíóúñÑ\s]/g, '');
    }

    nombreInput.addEventListener("input", function () { limpiarTexto(this); });
    apellidoInput.addEventListener("input", function () { limpiarTexto(this); });
});	
/*=============================================
TOGGLE PASSWORD VISIBILITY
=============================================*/
	function togglePassword() {
  const input = document.getElementById("nuevoPassword");
  const icon = document.getElementById("toggleIcon");

  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  } else {
    input.type = "password";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  }
}
/*=============================================
GENERAR CONTRASEÑA ALEATORIA
=============================================*/
$('#modalAgregarUsuario').on('shown.bs.modal', function () {
  const campo = document.getElementById("nuevoPassword");
  const temp = Math.random().toString(36).slice(-8); // Ejemplo: "x9k2p3qz"
  campo.value = temp;
});