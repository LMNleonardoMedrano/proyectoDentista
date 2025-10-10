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
                    <h3 class="text-center">Sign In to Your Dental Portal</h3>
                    <p class="text-center">Welcome back, we’re glad to see you!</p>
                    <form method="post">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Username" required name="ingUsuario" value="">
                            <i class="ik ik-user"></i>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="Password" required name="ingPassword" value="">
                            <i class="ik ik-lock"></i>
                        </div>
                        <div class="row mb-3">
                            <div class="col text-left">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="item_checkbox" name="item_checkbox" value="option1">
                                    <span class="custom-control-label">&nbsp;Stay Signed In</span>
                                </label>
                            </div>
                            <div class="col text-right">
                                <a href="resetPassword">Forgot Password?</a> <!-- Asegúrate de que sea editPassword.php -->
                            </div>
                        </div>
                        <div class="sign-btn text-center">
                            <button class="btn btn-theme" style="background-color: #6EC1E4; color: white; width: 100%; border-radius: 5px;">Log In</button>
                        </div>
                        <?php 
                        // Instancias...
                        $login = new ControladorUsuarios();
                        $login->ctrIngresoUsuario();
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
