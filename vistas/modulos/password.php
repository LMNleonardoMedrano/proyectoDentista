<div class="auth-wrapper">
    <div class="container-fluid h-100">
        <div class="row flex-row h-100 bg-light">
            <div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
                <div class="lavalite-bg" style="background-image: url('vistas/img/auth/dentista.jpg'); background-size: cover; background-position: center;">
                    <div class="lavalite-overlay"></div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-7 my-auto p-0">
                <div class="authentication-form mx-auto" style="padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); background-color: #ffffff;">
                    <h3 class="text-center">Actualizar Contraseña</h3>
                    <p class="text-center">Actualiza tu contraseña a continuación.</p>
                    <form method="post">
                        <div class="form-group">
                        <input class="form-control" type="text" name="usuario" id="usuario" required placeholder="Ingrese el usuario">
                        <i class="ik ik-user"></i>
                        </div>
                        <div class="form-group">
                        <input class="form-control" type="password" name="nuevaPassword" id="nuevaPassword" required placeholder="Nueva Contraseña">
                        <i class="ik ik-lock"></i>
                        </div>
                        <div class="row mb-3">
                           
                            <div class="col text-right">
                                <a href="login">Regresar </a> 
                            </div>
                        </div>
                        <div class="sign-btn text-center">
                            <button class="btn btn-theme" style="background-color: #6EC1E4; color: white; width: 100%; border-radius: 5px;">Actualizar Contraseña</button>
                        </div>
                        <?php
                            // Ejecuta el método del controlador para actualizar la contraseña
                            $actualizarPassword = new ControladorUsuarios();
                            $actualizarPassword->ctrActualizarPassword();
                            ?>
                    </form>
                    <div class="register text-center mt-3">
                        <p>New to our clinic? <a href="register.html">Create a New Account</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
