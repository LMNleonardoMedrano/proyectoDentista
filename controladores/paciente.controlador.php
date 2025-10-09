<?php

class ControladorPaciente {

  /*=============================================
  Mostrar Pacientes
  =============================================*/
  static public function ctrMostrarPaciente($item, $valor) {
    return ModeloPaciente::mdlMostrarPaciente("pacientes", $item, $valor);
  }

  /*=============================================
  Crear Paciente (y tutor si menor)
  =============================================*/
  public function ctrCrearPaciente() {
    if (isset($_POST["nuevoCi"])) {
          // Generar fecha de registro institucional
    $fechaRegistro = date("Y-m-d H:i:s");

      // Recoger datos paciente
      $datosPaciente = [
        "ci" => trim($_POST["nuevoCi"]),
        "domicilio" => trim($_POST["nuevoDomicilio"]),
        "fechaNac" => $_POST["nuevaFechaNacimiento"],
        "nombre" => trim($_POST["nuevoNombre"]),
        "genero" => $_POST["nuevoGenero"],
        "telCel" => trim($_POST["nuevaTelefono"]),
         "fechaRegistro" => $fechaRegistro

      ];

      // Datos tutor (solo si es menor)
      $esMenor = isset($_POST["esMenor"]) && $_POST["esMenor"] === "si";

      $datosTutor = null;
      if ($esMenor) {
        $datosTutor = [
          "nombrePT" => trim($_POST["tutor_nombre"]),
          "generoPT" => $_POST["tutor_genero"],
          "ocupacion" => trim($_POST["tutor_ocupacion"]),
          "relacion" => trim($_POST["tutor_relacion"])
        ];
      }

      $respuesta = ModeloPaciente::mdlCrearPacienteCompleto("pacientes", "pacienteMenor", $datosPaciente, $datosTutor);

      if ($respuesta === "ok") {
        echo '<script>
          swal({
            type: "success",
            title: "¡El paciente ha sido creado correctamente!",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
          }).then(function(result){
            if(result.value){
              window.location = "paciente";
            }
          });
        </script>';
      } else {
        echo '<script>
          swal({
            type: "error",
            title: "Error al crear paciente",
            text: "' . $respuesta . '",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
          });
        </script>';
      }
    }
  }

  /*=============================================
  Editar Paciente (y tutor si menor)
  =============================================*/
  public function ctrEditarPaciente() {
    if (isset($_POST["editaridPaciente"])) {
      $idPaciente = $_POST["editaridPaciente"];
$fechaRegistro = date("Y-m-d H:i:s");
      $datosPaciente = [
        "idPaciente" => $idPaciente,
        "ci" => trim($_POST["editarCI"]),
        "domicilio" => trim($_POST["editarDomicilio"]),
        "fechaNac" => $_POST["editarFechaNacimiento"],
        "nombre" => trim($_POST["editarNombre"]),
        "genero" => $_POST["editarGenero"],
        "telCel" => trim($_POST["editarTelefono"]),
        "fechaRegistro" => $fechaRegistro

      ];

      // Calcular si menor para guardar tutor
      $fechaNac = new DateTime($_POST["editarFechaNacimiento"]);
      $edad = $fechaNac->diff(new DateTime())->y;
      $esMenor = $edad < 18;

      $datosTutor = null;
      if ($esMenor) {
        $datosTutor = [
          "idPaciente" => $idPaciente,
          "nombrePT" => trim($_POST["editarNombrePT"]),
          "generoPT" => $_POST["editarGeneroPT"],
          "ocupacion" => trim($_POST["editarOcupacionPT"]),
          "relacion" => trim($_POST["editarRelacionPT"])
        ];
      }

      $respuesta = ModeloPaciente::mdlEditarPacienteCompleto("pacientes", "pacienteMenor", $datosPaciente, $datosTutor, $esMenor);

      if ($respuesta === "ok") {
        echo '<script>
          swal({
            type: "success",
            title: "¡El paciente ha sido editado correctamente!",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
          }).then(function(result){
            if(result.value){
              window.location = "paciente";
            }
          });
        </script>';
      } else {
        echo '<script>
          swal({
            type: "error",
            title: "Error al editar paciente",
            text: "' . $respuesta . '",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
          });
        </script>';
      }
    }
  }

  /*=============================================
  Eliminar Paciente (y tutor)
  =============================================*/
  public function ctrEliminarPaciente() {
    if (isset($_GET["idPaciente"])) {
        $idPaciente = $_GET["idPaciente"];
        $respuesta = ModeloPaciente::mdlEliminarPacienteCompleto("pacientes", "pacienteMenor", $idPaciente);

        if ($respuesta == "ok") {
            echo "<script>
                swal({
                    type: 'success',
                    title: '¡El paciente ha sido eliminado correctamente!',
                    showConfirmButton: true,
                    confirmButtonText: 'Cerrar'
                }).then(function() {
                    window.location = 'paciente';
                });
            </script>";
        } else if ($respuesta == "error_tratamiento") {
            echo "<script>
                swal({
                    type: 'error',
                    title: 'No se puede eliminar el paciente',
                    text: 'Este paciente tiene tratamientos asociados y no puede ser eliminado.',
                    showConfirmButton: true,
                    confirmButtonText: 'Cerrar'
                });
            </script>";
        } else {
            echo "<script>
                swal({
                    type: 'error',
                    title: 'Error al eliminar el paciente',
                    text: 'Por favor intente de nuevo o contacte al administrador.',
                    showConfirmButton: true,
                    confirmButtonText: 'Cerrar'
                });
            </script>";
        }
    }
}

}
