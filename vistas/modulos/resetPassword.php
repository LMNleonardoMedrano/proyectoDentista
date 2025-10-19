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
                    <h3 class="text-center">¿Olvidó su contraseña?</h3>
                    <p class="text-center">Ingrese su dirección de correo electrónico a continuación.</p>
                    <form method="post"> <!-- Cambiar acción si es necesario -->
                        <div class="form-group">
                        <input class="form-control" type="email" name="emailRecuperacion" required="" placeholder="ingrese su correo electrónico">
                        <i class="ik ik-mail"></i>
                        </div>
                        <div class="sign-btn text-center">
                            <button class="btn btn-theme" style="background-color: #6EC1E4; color: white; width: 100%; border-radius: 5px;">enviar</button>
                        </div>
                        <?php
                            $RecuperarPassword = new ControladorUsuarios();
                            $RecuperarPassword->ctrRecuperarPassword();
                            ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
