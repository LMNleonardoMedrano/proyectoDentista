<?php
if (!isset($_SESSION["iniciarSesion"]) || $_SESSION["iniciarSesion"] != "ok") {
    echo '<script>window.location = "login";</script>';
    return;
}

// Obtener los datos del usuario logueado
$usuario = ControladorUsuarios::ctrMostrarPerfil();
?>
<div class="wrapper">
  <div class="page-wrap">
    <div class="main-content">
<div class="content-wrapper">

    <!-- Encabezado -->
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="text-primary"><i class="fas fa-user-circle mr-2"></i>Perfil del Usuario</h1>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="content">
        <div class="container mt-4">

            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body">
                    <div class="row">

                        <!-- Columna Izquierda: Foto y Datos -->
                        <div class="col-md-4 text-center border-right">
                            <img src="<?php echo !empty($usuario['foto']) ? $usuario['foto'] : 'vistas/img/usuarios/default/anonymous.png'; ?>" 
                                 alt="Foto de perfil" 
                                 class="img-fluid rounded-circle mb-3"
                                 style="width:150px; height:150px; object-fit:cover; border:3px solid #007bff;">

                            <h4 class="mb-0"><?php echo $usuario['nombre'] . " " . $usuario['apellido']; ?></h4>
                            <p class="text-muted mb-2"><?php echo $_SESSION["nombreRol"]; ?></p>

                            <p><i class="fas fa-envelope mr-2 text-primary"></i><?php echo $usuario['correo']; ?></p>
                            <p><i class="fas fa-home mr-2 text-primary"></i><?php echo $usuario['domicilio']; ?></p>
                            <p><i class="fas fa-calendar-alt mr-2 text-primary"></i>Registrado: <?php echo $usuario['fecha']; ?></p>
                        </div>

                        <!-- Columna Derecha: Tabs para Editar Info y Contraseña -->
                        <div class="col-md-8">

                            <!-- Tabs -->
                            <ul class="nav nav-tabs" id="perfilTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab">Editar Información</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab">Cambiar Contraseña</a>
                                </li>
                            </ul>

                            <!-- Contenido de Tabs -->
                            <div class="tab-content mt-3" id="perfilTabsContent">

                                <!-- Editar Información -->
                                <div class="tab-pane fade show active" id="info" role="tabpanel">
                                    <form method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="idUsuario" value="<?php echo $usuario['idUsuarios']; ?>">

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Nombre</label>
                                                <input type="text" name="editarNombre" class="form-control" value="<?php echo $usuario['nombre']; ?>" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Apellido</label>
                                                <input type="text" name="editarApellido" class="form-control" value="<?php echo $usuario['apellido']; ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Correo</label>
                                                <input type="email" name="editarCorreo" class="form-control" value="<?php echo $usuario['correo']; ?>" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Domicilio</label>
                                                <input type="text" name="editarDomicilio" class="form-control" value="<?php echo $usuario['domicilio']; ?>">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Foto de perfil</label>
                                            <input type="file" class="form-control-file" name="editarFoto" accept="image/*">
                                            <input type="hidden" name="fotoActual" value="<?php echo $usuario['foto']; ?>">
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>Guardar cambios
                                        </button>

                                        <?php
                                        $editarPerfil = new ControladorUsuarios();
                                        $editarPerfil->ctrEditarPerfil();
                                        ?>
                                    </form>
                                </div>

                                <!-- Cambiar Contraseña -->
                                <div class="tab-pane fade" id="password" role="tabpanel">
                                    <form method="post">
                                        <input type="hidden" name="idUsuario" value="<?php echo $usuario['idUsuarios']; ?>">

                                        <div class="form-group">
                                            <label>Contraseña actual</label>
                                            <input type="password" name="passwordActual" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Nueva contraseña</label>
                                            <input type="password" name="nuevoPassword" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Confirmar nueva contraseña</label>
                                            <input type="password" name="confirmarPassword" class="form-control" required>
                                        </div>

                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-key mr-1"></i>Actualizar Contraseña
                                        </button>

                                        <?php
                                        $cambiarPassword = new ControladorUsuarios();
                                        $cambiarPassword->ctrCambiarPassword();
                                        ?>
                                    </form>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
         </div>
            </div>

        </div>
<!-- Script para previsualizar la foto -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const inputFoto = document.querySelector('input[name="editarFoto"]');
    const imgPreview = document.querySelector('.img-fluid.rounded-circle');

    if (inputFoto && imgPreview) {
        inputFoto.addEventListener("change", function(e) {
            const archivo = e.target.files[0];
            if (archivo) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    imgPreview.src = event.target.result;
                }
                reader.readAsDataURL(archivo);
            }
        });
    }
});
</script>
