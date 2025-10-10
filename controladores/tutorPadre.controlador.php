<?php

class ControladorTutorPadre
{

    /*=============================================
	CREAR TutorPadre
	=============================================*/

    static public function ctrCrearTutorPadre()
    {

        if (isset($_POST["nuevoidPaciente"])) {

            if (
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoidPaciente"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoNombre"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoDomicilio"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["nuevaFechaNacimiento"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["nuevoGenero"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["nuevoCI"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["nuevoOcupacion"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["nuevoRelacion"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["nuevoTelCel"])
            ) {

                $tabla = "tutorpadre";

                $datos = array(
                    "idPaciente" => $_POST["nuevoidPaciente"],
                    "Nombre" => $_POST["nuevoNombre"],
                    "Domicilio" => $_POST["nuevoDomicilio"],
                    "FechaNac" => $_POST["nuevaFechaNacimiento"],
                    "Genero" => $_POST["nuevoGenero"],
                    "Ci" => $_POST["nuevoCI"],
                    "Ocupacion" => $_POST["nuevoOcupacion"],
                    "Relacion" => $_POST["nuevoRelacion"],
                    "TelCel" => $_POST["nuevoTelCel"]
                );

                $respuesta = ModeloTutorPadre::mdlCrearTutorPadre($tabla, $datos);

                if ($respuesta == "ok") {

                    echo '<script>

					swal({
						  type: "success",
						  title: "El tutor o Padre ha sido guardado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "tutorPadre";

									}
								})

					</script>';
                }
            } else {

                echo '<script>

					swal({
						  type: "error",
						  title: "¡El tutor o Padre no puede ir vacío o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "tutorPadre";

							}
						})

			  	</script>';
            }
        }
    }

    /*=============================================
	MOSTRAR TutorPadre
	=============================================*/

    static public function ctrMostrarTutorPadre($item, $valor)
    {

        $tabla = "tutorpadre";

        $respuesta = ModeloTutorPadre::mdlMostrarTutorPadre($tabla, $item, $valor);

        return $respuesta;
    }


    /*=============================================
EDITAR TutorPadre
=============================================*/

    static public function ctrEditarTutorPadre()
    {

        if (isset($_POST["editarIdTutorPadre"])) {

            if (
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editaridPaciente"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombre"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarDomicilio"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["editarFechaNacimiento"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["editarGenero"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["editarCI"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["editarOcupacion"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["editarRelacion"]) &&
                preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $_POST["editarTelCel"])
            ) {

                $tabla = "tutorpadre";

                $datos = array(
                    "IdTutorPadre" => $_POST["editarIdTutorPadre"], // Cambio aquí para usar el idNutricionista
                    "idPaciente" => $_POST["editaridPaciente"],
                    "Nombre" => $_POST["editarNombre"],
                    "Domicilio" => $_POST["editarDomicilio"],
                    "FechaNac" => $_POST["editarFechaNacimiento"],
                    "Genero" => $_POST["editarGenero"],
                    "Ci" => $_POST["editarCI"],
                    "Ocupacion" => $_POST["editarOcupacion"],
                    "Relacion" => $_POST["editarRelacion"],
                    "TelCel" => $_POST["editarTelCel"]
                );

                $respuesta = ModeloTutorPadre::mdlEditarTutorPadre($tabla, $datos);

                if ($respuesta == "ok") {

                    echo '<script>

                swal({
                      type: "success",
                      title: "El tutor o Padre ha sido cambiado correctamente",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                                if (result.value) {
                                    window.location = "tutorPadre";
                                }
                            })

                </script>';
                }
            } else {

                echo '<script>

                swal({
                      type: "error",
                      title: "¡El tutor o Padre no puede ir vacío o llevar caracteres especiales!",
                      showConfirmButton: true,
                      confirmButtonText: "Cerrar"
                      }).then(function(result){
                        if (result.value) {
                            window.location = "tutorPadre";
                        }
                    })

           </script>';
            }
        }
    }


    /*=============================================
	ELIMINAR TutorPadre
	=============================================*/

    static public function ctrEliminarTutorPadre()
    {

        if (isset($_GET["IdTutorPadre"])) {

            $tabla = "tutorpadre";
            $datos = $_GET["IdTutorPadre"];

            $respuesta = ModeloTutorPadre::mdlEliminarTutorPadre($tabla, $datos);

            if ($respuesta == "ok") {

                echo '<script>

				swal({
					  type: "success",
					  title: "El  tutor o Padre ha sido borrado correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar"
					  }).then(function(result){
								if (result.value) {

								window.location = "tutorPadre";

								}
							})

				</script>';
            }
        }
    }
}
