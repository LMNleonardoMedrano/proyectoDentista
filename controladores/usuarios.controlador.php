<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
class ControladorUsuarios
{
    /*=============================================
INGRESO DE USUARIO
=============================================*/
    static public function ctrIngresoUsuario()
    {
        if (isset($_POST["ingUsuario"])) {

            // Validar entradas
            if (
                preg_match('/^[a-zA-Z0-9@._]+$/', $_POST["ingUsuario"]) &&
                preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingPassword"])
            ) {

                $tabla = "usuarios";
                $valor = $_POST["ingUsuario"];
                $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, "usuario", $valor);

                // Si no se encontró por nombre de usuario, intenta buscar por correo
                if (!$respuesta) {
                    $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, "correo", $valor);
                }

                if ($respuesta) {
                    if (password_verify($_POST["ingPassword"], $respuesta["password"])) {

                        if ($respuesta["estado"] == "1") {

                            // Iniciar sesión
                            $_SESSION["iniciarSesion"] = "ok";
                            $_SESSION["id"] = $respuesta["idUsuarios"];
                            $_SESSION["nombre"] = $respuesta["nombre"];
                            $_SESSION["apellido"] = $respuesta["apellido"];
                            $_SESSION["usuario"] = $respuesta["usuario"];
                            $_SESSION["foto"] = $respuesta["foto"];
                            $_SESSION["idRol"] = $respuesta["idRol"];

                            // Obtener nombre del rol
                            $tablaRoles = "roles";
                            $rol = ModeloUsuarios::mdlMostrarUsuarios($tablaRoles, "idRol", $respuesta["idRol"]);
                            if ($rol) {
                                $_SESSION["nombreRol"] = $rol["nombreRol"];
                            }

                            /* =====================================================
                           ✅ Cargar permisos del rol en la sesión
                           ===================================================== */
                            require_once "controladores/roles.controlador.php";

                            $permisos = ControladorRoles::ctrMostrarPermisosPorRol($_SESSION["idRol"]);

                            $_SESSION["permisos"] = []; // reiniciar lista

                            foreach ($permisos as $permiso) {
                                if ($permiso["tieneAcceso"]) {
                                    // Guardar módulo
                                    $_SESSION["permisos"][] = strtolower($permiso["modulo"]);

                                    // Guardar permiso individual si existe
                                    if (!empty($permiso["nombrePermiso"])) {
                                        $_SESSION["permisos"][] = strtolower($permiso["nombrePermiso"]);
                                    }
                                }
                            }
                            /* ===================================================== */

                            // Registrar fecha para el último login
                            date_default_timezone_set('America/Bogota');
                            $fechaActual = date('Y-m-d H:i:s');
                            ModeloUsuarios::mdlActualizarUsuario(
                                $tabla,
                                "ultimoLogin",
                                $fechaActual,
                                "idUsuarios",
                                $respuesta["idUsuarios"]
                            );

                            // Redirigir al inicio
                            echo '<script>window.location = "inicio";</script>';
                        } else {
                            echo '<br><div class="alert alert-danger">El usuario aún no está activado</div>';
                        }
                    } else {
                        echo '<br><div class="alert alert-danger">Contraseña incorrecta. Intenta de nuevo.</div>';
                    }
                } else {
                    echo '<br><div class="alert alert-danger">Usuario o correo no encontrado. Intenta de nuevo.</div>';
                }
            } else {
                echo '<br><div class="alert alert-danger">Los campos no son válidos.</div>';
            }
        }
    }






    /*=============================================
	REGISTRO DE USUARIO
=============================================*/

    static public function ctrCrearUsuario()
    {

        if (isset($_POST["nuevoUsuario"])) {

            if (
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoNombre"]) &&
                preg_match('/^[a-zA-Z0-9]+$/', $_POST["nuevoUsuario"]) &&
                preg_match('/^[a-zA-Z0-9]+$/', $_POST["nuevoPassword"])
            ) {


                /*=============================================
            VALIDAR IMAGEN
            =============================================*/
                $ruta = "";

                if (isset($_FILES["nuevaFoto"]) && !empty($_FILES["nuevaFoto"]["tmp_name"]) && $_FILES["nuevaFoto"]["error"] == 0) {

                    $tipoArchivo = $_FILES["nuevaFoto"]["type"];

                    if ($tipoArchivo == "image/jpeg" || $tipoArchivo == "image/png") {

                        $size = @getimagesize($_FILES["nuevaFoto"]["tmp_name"]);
                        if ($size !== false) {
                            list($ancho, $alto) = $size;
                        } else {
                            echo '<script>
                            swal({
                                type: "error",
                                title: "¡La imagen no es válida!",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                            }).then(function(result){
                                if(result.value){
                                    window.location = "usuarios";
                                }
                            });
                        </script>';
                            return;
                        }

                        $nuevoAncho = 500;
                        $nuevoAlto = 500;

                        /*=============================================
                    CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DEL USUARIO
                    =============================================*/
                        $directorio = "vistas/img/usuarios/" . $_POST["nuevoUsuario"];

                        if (!is_dir($directorio)) {
                            if (!mkdir($directorio, 0755, true)) {
                                echo '<script>
                                swal({
                                    type: "error",
                                    title: "¡No se pudo crear el directorio para guardar la imagen!",
                                    showConfirmButton: true,
                                    confirmButtonText: "Cerrar"
                                }).then(function(result){
                                    if(result.value){
                                        window.location = "usuarios";
                                    }
                                });
                            </script>';
                                return;
                            }
                        }

                        /*=============================================
                    DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
                    =============================================*/
                        $aleatorio = mt_rand(100, 999);
                        $ruta = $directorio . "/" . $aleatorio;

                        if ($tipoArchivo == "image/jpeg") {
                            $ruta .= ".jpg";
                            $origen = imagecreatefromjpeg($_FILES["nuevaFoto"]["tmp_name"]);
                        } else {
                            $ruta .= ".png";
                            $origen = imagecreatefrompng($_FILES["nuevaFoto"]["tmp_name"]);
                        }

                        // Verificación de si la imagen fue creada correctamente
                        if ($origen === false) {
                            echo '<script>
                            swal({
                                type: "error",
                                title: "¡Error al procesar la imagen, el archivo no es válido!",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                            }).then(function(result){
                                if(result.value){
                                    window.location = "usuarios";
                                }
                            });
                        </script>';
                            return;
                        }

                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

                        if ($tipoArchivo == "image/jpeg") {
                            imagejpeg($destino, $ruta);
                        } else {
                            imagepng($destino, $ruta);
                        }
                    } else {
                        echo '<script>
                        swal({
                            type: "error",
                            title: "¡Solo se permiten imágenes en formato JPG o PNG!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "usuarios";
                            }
                        });
                    </script>';
                        return;
                    }
                }

                /*=============================================
            GUARDAR USUARIO EN LA BASE DE DATOS
            =============================================*/
                $tabla = "usuarios";

                $temporal = $_POST["nuevoPassword"]; // lo que vino del formulario
                $encriptar = password_hash($temporal, PASSWORD_BCRYPT);

                // Preparar los datos para la inserción
                $datos = array(
                    "nombre" => $_POST["nuevoNombre"],
                    "apellido" => $_POST["nuevoApellido"], // Añadir apellido a la base de datos
                    "ci" => $_POST["nuevoCi"], // Agregar CI
                    "correo" => $_POST["nuevoCorreo"],
                    "password" => $encriptar,
                    "idRol" => $_POST["nuevoPerfil"],
                    "foto" => $ruta,
                    "usuario" => $_POST["nuevoUsuario"],
                    "estado" => "1", // Asumí que el estado será 1 por defecto
                    "fecha" => date('Y-m-d'), // Fecha de registro
                    "domicilio" => $_POST["nuevoDomicilio"], // Agregar domicilio
                    "ultimoLogin" => NULL, // Asignar NULL al primer login
                );

                $respuesta = ModeloUsuarios::mdlIngresarUsuario($tabla, $datos);

                if ($respuesta == "ok") {

                    // Enviar correo con la contraseña temporal
                    $mail = new PHPMailer(true);

                    try {
                       $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'lmnleo4318@gmail.com';
                        $mail->Password   = 'qwdl ztbq lzvm rwdf';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        // Establece los destinatarios
                        $mail->setFrom('lmnleo4318@gmail.com', 'clinica dental Dentani');
                        $mail->addAddress($_POST["nuevoCorreo"], $_POST["nuevoNombre"]);

                        $mail->isHTML(true);
                        $mail->Subject = 'Acceso al sistema';
                        $mail->Body = '
            <h3>Hola ' . $_POST["nuevoNombre"] . ',</h3>
            <p>Tu usuario ha sido creado correctamente.</p>
            <p><strong>Usuario:</strong> ' . $_POST["nuevoUsuario"] . '</p>
            <p><strong>Contraseña temporal:</strong> ' . $temporal . '</p>
            <p>Por favor, cambia tu contraseña al iniciar sesión.</p>
        ';

                        $mail->send();

                        echo '<script>
            swal({
                type: "success",
                title: "¡Usuario registrado y contraseña enviada al correo!",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
            }).then(function(result){
                if(result.value){
                    window.location = "usuarios";
                }
            });
        </script>';
                    } catch (Exception $e) {
                        echo '<script>
            swal({
                type: "error",
                title: "¡Usuario registrado, pero error al enviar el correo!",
                text: "' . htmlspecialchars($mail->ErrorInfo) . '",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
            }).then(function(result){
                if(result.value){
                    window.location = "usuarios";
                }
            });
        </script>';
                    }
                }
            } else {
                echo '<script>
                swal({
                    type: "error",
                    title: "¡El usuario no puede ir vacío o llevar caracteres especiales!",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result){
                    if(result.value){
                        window.location = "usuarios";
                    }
                });
            </script>';
            }
        }
    }




    /*=============================================
	MOSTRAR USUARIO
	=============================================*/

    static public function ctrMostrarUsuarios($item, $valor)
    {

        $tabla = "usuarios";

        $respuesta = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor);

        return $respuesta;
    }

    /*=============================================
EDITAR USUARIO
=============================================*/
    static public function ctrEditarUsuario()
    {
        if (isset($_POST["editarUsuario"])) {

            // Validar que el nombre no contenga caracteres especiales
            if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombre"])) {

                /*=============================================
            VALIDAR IMAGEN
            =============================================*/
                $ruta = $_POST["fotoActual"];

                if (isset($_FILES["editarFoto"]["tmp_name"]) && !empty($_FILES["editarFoto"]["tmp_name"])) {

                    $tipoArchivo = $_FILES["editarFoto"]["type"];

                    if ($tipoArchivo == "image/jpeg" || $tipoArchivo == "image/png") {

                        $size = @getimagesize($_FILES["editarFoto"]["tmp_name"]);
                        if ($size !== false) {
                            list($ancho, $alto) = $size;
                        } else {
                            echo '<script>
                        swal({
                            type: "error",
                            title: "¡La imagen no es válida!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "usuarios";
                            }
                        });
                    </script>';
                            return;
                        }

                        $nuevoAncho = 500;
                        $nuevoAlto = 500;

                        /*=============================================
                    CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DEL USUARIO
                    =============================================*/
                        $directorio = "vistas/img/usuarios/" . $_POST["editarUsuario"];

                        /*=============================================
                    PRIMERO PREGUNTAMOS SI EXISTE OTRA IMAGEN EN LA BD
                    =============================================*/
                        if (!empty($_POST["fotoActual"])) {
                            if (file_exists($_POST["fotoActual"])) {
                                unlink($_POST["fotoActual"]);
                            }
                        }

                        // Si el directorio no existe, lo creamos
                        if (!is_dir($directorio)) {
                            if (!mkdir($directorio, 0755, true)) {
                                echo '<script>
                            swal({
                                type: "error",
                                title: "¡No se pudo crear el directorio para guardar la imagen!",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                            }).then(function(result){
                                if(result.value){
                                    window.location = "usuarios";
                                }
                            });
                        </script>';
                                return;
                            }
                        }

                        /*=============================================
                    DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
                    =============================================*/

                        $aleatorio = mt_rand(100, 999);
                        $ruta = $directorio . "/" . $aleatorio;

                        if ($tipoArchivo == "image/jpeg") {
                            $ruta .= ".jpg";
                            $origen = imagecreatefromjpeg($_FILES["editarFoto"]["tmp_name"]);
                        } else {
                            $ruta .= ".png";
                            $origen = imagecreatefrompng($_FILES["editarFoto"]["tmp_name"]);
                        }

                        // Verificación de si la imagen fue creada correctamente
                        if ($origen === false) {
                            echo '<script>
                        swal({
                            type: "error",
                            title: "¡Error al procesar la imagen, el archivo no es válido!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "usuarios";
                            }
                        });
                    </script>';
                            return;
                        }

                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

                        if ($tipoArchivo == "image/jpeg") {
                            imagejpeg($destino, $ruta);
                        } else {
                            imagepng($destino, $ruta);
                        }
                    } else {
                        echo '<script>
                    swal({
                        type: "error",
                        title: "¡Solo se permiten imágenes en formato JPG o PNG!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "usuarios";
                        }
                    });
                </script>';
                        return;
                    }
                }

                /*=============================================
            VALIDAR CONTRASEÑA
            =============================================*/
                $tabla = "usuarios";
                if ($_POST["editarPassword"] != "") {
                    if (preg_match('/^[a-zA-Z0-9]+$/', $_POST["editarPassword"])) {
                        $encriptar = password_hash($_POST["editarPassword"], PASSWORD_BCRYPT);
                    } else {
                        echo '<script>
                    swal({
                        type: "error",
                        title: "¡La contraseña no puede ir vacía o llevar caracteres especiales!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "usuarios";
                        }
                    });
                </script>';
                        return;
                    }
                } else {
                    $encriptar = $_POST["passwordActual"];
                }

                /*=============================================
            ACTUALIZAR USUARIO EN LA BASE DE DATOS
            =============================================*/
                $datos = array(
                    "idUsuarios" => $_POST["editaridUsuarios"],  // Utilizamos el ID real del usuario
                    "nombre" => $_POST["editarNombre"],
                    "apellido" => $_POST["editarApellido"],
                    "ci" => $_POST["editarCi"],
                    "correo" => $_POST["editarCorreo"],
                    "domicilio" => $_POST["editarDomicilio"],
                    "estado" => $_POST["editarEstado"],
                    "idRol" => $_POST["editarPerfil"],
                    "password" => $encriptar,
                    "foto" => $ruta
                );


                $respuesta = ModeloUsuarios::mdlEditarUsuario($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                swal({
                    type: "success",
                    title: "El usuario ha sido editado correctamente",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result){
                    if(result.value){
                        window.location = "usuarios";
                    }
                });
            </script>';
                } else {
                    echo '<script>
                swal({
                    type: "error",
                    title: "Hubo un error al editar el usuario",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result){
                    if(result.value){
                        window.location = "usuarios";
                    }
                });
            </script>';
                }
            } else {
                echo '<script>
            swal({
                type: "error",
                title: "¡El nombre no puede ir vacío o llevar caracteres especiales!",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
            }).then(function(result){
                if(result.value){
                    window.location = "usuarios";
                }
            });
        </script>';
            }
        }
    }

    static public function ctrValidarCI($item, $valor)
    {

        $tabla = "usuarios";
        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);

        return $respuesta;
    }



    /*=============================================
	BORRAR USUARIO
=============================================*/

    static public function ctrBorrarUsuario()
    {

        if (isset($_GET["idUsuarios"])) {

            $tabla = "usuarios";
            $datos = $_GET["idUsuarios"];

            // Verificar si la foto existe y eliminarla
            if ($_GET["fotoUsuario"] != "") {
                if (file_exists($_GET["fotoUsuario"])) {
                    unlink($_GET["fotoUsuario"]);
                }
                // Verificar si el directorio existe y eliminarlo
                $directorio = 'vistas/img/usuarios/' . $_GET["usuario"];
                if (is_dir($directorio)) {
                    rmdir($directorio);
                }
            }

            // Llamar al modelo para eliminar el usuario
            $respuesta = ModeloUsuarios::mdlEliminarUsuario($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                swal({
                    type: "success",
                    title: "El usuario ha sido borrado correctamente",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result){
                    if(result.value){
                        window.location = "usuarios";
                    }
                });
            </script>';
            }
        }
    }




    /*=============================================
RECUPERAR CONTRASEÑA
=============================================*/
    static public function ctrRecuperarPassword()
    {

        if (isset($_POST["emailRecuperacion"])) {
            if (preg_match('/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/', $_POST["emailRecuperacion"])) {
                $email = $_POST["emailRecuperacion"];
                $tabla = "usuarios";


                $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, "correo", $email);


                if ($respuesta) {

                    $mail = new PHPMailer(true);

                    $mail->SMTPDebug = 0;

                    try {

                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'lmnleo4318@gmail.com';
                        $mail->Password   = 'qwdl ztbq lzvm rwdf';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        // Establece los destinatarios
                        $mail->setFrom('lmnleo4318@gmail.com', 'clinica dental Dentani');
                        $mail->addAddress($email);

                        // Contenido del correo
                        $mail->isHTML(true);
                        $mail->Subject = 'Recuperacion de contrasena';
                        $mail->Body = 'Hola ' . htmlspecialchars($respuesta["nombre"]) . ',<br><br>'
                            . 'Para restablecer tu contraseña, haz clic en el siguiente enlace:<br>'
                            . '<a href="http://localhost/dentista/index.php?ruta=password">Recuperar contraseña</a><br><br>'
                            . 'Si no solicitaste este cambio, simplemente ignora este correo.';




                        $mail->send();


                        echo '<script>
				swal({
					type: "success",
					title: "¡Correo de recuperación enviado!",
					showConfirmButton: true,
					confirmButtonText: "Cerrar"
				}).then(function(result){
					if(result.value){
						window.location = "index.php";
					}
				});
			</script>';
                    } catch (Exception $e) {

                        echo '<script>
				swal({
					type: "error",
					title: "Error al enviar el correo: ' . htmlspecialchars($mail->ErrorInfo) . '",
					showConfirmButton: true,
					confirmButtonText: "Cerrar"
				});
			</script>';
                    }
                } else {

                    echo '<script>
			swal({
				type: "error",
				title: "¡El correo no está registrado o el usuario está inactivo!",
				showConfirmButton: true,
				confirmButtonText: "Cerrar"
			});
		</script>';
                }
            } else {

                echo '<script>
		swal({
			type: "error",
			title: "¡El correo no es válido!",
			showConfirmButton: true,
			confirmButtonText: "Cerrar"
		});
	</script>';
            }
        }
    }


    /*=============================================
ACTUALIZAR CONTRASEÑA
=============================================*/
    static public function ctrActualizarPassword()
    {
        if (!empty($_POST["usuario"]) && !empty($_POST["nuevaPassword"])) {
            $usuario = $_POST["usuario"];
            $nuevaContrasena = crypt($_POST["nuevaPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
            $tabla = "usuarios";

            $datos = array("usuario" => $usuario, "password" => $nuevaContrasena);

            // Actualizar en base de datos
            $respuesta = ModeloUsuarios::mdlActualizarPassword($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script> /* Mensaje de éxito */ </script>';
            } else {
                echo '<script> /* Error al actualizar */ </script>';
            }
        }
    }
    static public function ctrMostrarSoloOdontologos()
    {
        return ModeloUsuarios::mdlMostrarSoloOdontologos("usuarios");
    }









    // Mostrar perfil
    static public function ctrMostrarPerfil()
    {
        $idUsuario = $_SESSION["id"];
        return ModeloUsuarios::mdlMostrarPerfil($idUsuario);
    }
    /*=============================================
EDITAR PERFIL DE USUARIO LOGUEADO
=============================================*/
    static public function ctrEditarPerfil()
    {
        if (isset($_POST["editarNombre"])) {

            $tabla = "usuarios";

            // Procesar imagen
            $ruta = $_POST["fotoActual"];
            if (isset($_FILES["editarFoto"]["tmp_name"]) && !empty($_FILES["editarFoto"]["tmp_name"])) {

                $directorio = "vistas/img/usuarios/" . $_SESSION["usuario"];

                if (!file_exists($directorio)) {
                    mkdir($directorio, 0755, true);
                }

                $aleatorio = mt_rand(100, 999);
                $ruta = $directorio . "/" . $aleatorio . ".jpg";

                move_uploaded_file($_FILES["editarFoto"]["tmp_name"], $ruta);
            }

            $datos = array(
                "idUsuarios" => $_POST["idUsuario"],
                "nombre" => $_POST["editarNombre"],
                "apellido" => $_POST["editarApellido"],
                "correo" => $_POST["editarCorreo"],
                "domicilio" => $_POST["editarDomicilio"],
                "foto" => $ruta
            );

            $respuesta = ModeloUsuarios::mdlEditarPerfil($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
        swal({
            type: "success",
            title: "Perfil actualizado correctamente",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
        }).then(function(result){
            if(result.value){
                window.location = "perfil";
            }
        });
    </script>';
            } else {
                echo '<script>
        swal({
            type: "error",
            title: "Error al actualizar el perfil",
            text: "Intenta nuevamente",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
        });
    </script>';
            }
        }
    }

    /*=============================================
CAMBIAR CONTRASEÑA DEL PERFIL
=============================================*/
    static public function ctrCambiarPassword()
    {
        if (isset($_POST["passwordActual"])) {

            $tabla = "usuarios";
            $id = $_POST["idUsuario"];
            $passwordActual = $_POST["passwordActual"];
            $nuevoPassword = $_POST["nuevoPassword"];
            $confirmarPassword = $_POST["confirmarPassword"];

            $usuario = ModeloUsuarios::mdlMostrarUsuarioPorId($tabla, $id);

            if ($nuevoPassword !== $confirmarPassword) {
                echo '<script>
    swal({
        type: "warning",
        title: "Las contraseñas no coinciden",
        showConfirmButton: true,
        confirmButtonText: "Cerrar"
    }).then(function(result){
        if(result.value){
            window.location = "perfil"; // redirige a perfil si quieres
        }
    });
</script>';
                return;
            }

            if (password_verify($passwordActual, $usuario["password"])) {
                $hash = password_hash($nuevoPassword, PASSWORD_DEFAULT);
                $respuesta = ModeloUsuarios::mdlCambiarPassword($tabla, $id, $hash);

                if ($respuesta == "ok") {
                    echo '<script>
        swal({
            type: "success",
            title: "Contraseña actualizada correctamente",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
        }).then(function(result){
            if(result.value){
                window.location = "perfil";
            }
        });
    </script>';
                } else {
                    echo '<script>
        swal({
            type: "error",
            title: "Error al cambiar la contraseña",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
        });
    </script>';
                }
            } else {
                echo '<script>
        swal({
            type: "error",
            title: "Contraseña actual incorrecta",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
        });
    </script>';
            }
        }
    }
}
