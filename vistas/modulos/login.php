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
                    <h3 class="text-center">Inicie sesión en su portal dental</h3>
                    <p class="text-center">¡Bienvenido de nuevo, nos alegra verte!</p>
                    <form method="post">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="nombre de usuario" required name="ingUsuario" value="">
                            <i class="ik ik-user"></i>
                        </div>
                        <div class="form-group position-relative">
                            <input type="password" class="form-control" placeholder="Contraseña" required name="ingPassword" id="ingPassword">
                            <i class="ik ik-lock" style="position: absolute; top: 50%; left: 10px; transform: translateY(-50%);"></i>
                            <button type="button" onclick="toggleLoginPassword()" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); background: none; border: none;">
                                <i class="fas fa-eye-slash" id="toggleLoginIcon"></i>
                            </button>
                        </div>
                        <script>
                            function toggleLoginPassword() {
                                const input = document.getElementById("ingPassword");
                                const icon = document.getElementById("toggleLoginIcon");

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
                        </script>
                        <div class="row mb-3">
                            <div class="col text-left">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="item_checkbox" name="item_checkbox" value="option1">
                                    <span class="custom-control-label">&nbsp;Permanecer conectado</span>
                                </label>
                            </div>
                            <div class="col text-right">
                                <a href="resetPassword">¿Olvidó su contraseña?</a> <!-- Asegúrate de que sea editPassword.php -->
                            </div>
                        </div>
                        <div class="sign-btn text-center">
                            <button class="btn btn-theme" style="background-color: #6EC1E4; color: white; width: 100%; border-radius: 5px;">Iniciar sesión</button>
                        </div>
                        <?php
                        // Instancias...
                        $login = new ControladorUsuarios();
                        $login->ctrIngresoUsuario();
                        ?>
                    </form>
                    <div class="register text-center mt-3">
                        <p>¿Nuevo en nuestra clínica? <a href="register.html">Crear una nueva cuenta</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>