<div class="wrapper">
<header class="header-top" header-theme="green">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <!-- Lado izquierdo: búsqueda y fullscreen -->
            <div class="top-menu d-flex align-items-center">
                <button type="button" class="btn-icon mobile-nav-toggle d-lg-none"><span></span></button>
                <div class="header-search">
                    <div class="input-group">
                        <span class="input-group-addon search-close"><i class="ik ik-x"></i></span>
                        <input type="text" class="form-control">
                        <span class="input-group-addon search-btn"><i class="ik ik-search"></i></span>
                    </div>
                </div>
                <button type="button" id="navbar-fullscreen" class="nav-link"><i class="ik ik-maximize"></i></button>
            </div>

            <!-- Lado derecho: usuario -->
            <div class="top-menu d-flex align-items-center">
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <!-- Foto de usuario -->
                        <img class="avatar" src="<?php echo !empty($_SESSION['foto']) ? $_SESSION['foto'] : 'vistas/img/user.jpg'; ?>" alt="Usuario">
                        <!-- Nombre y rol -->
                        <div class="ml-2 text-left">
                            <span class="font-weight-bold">
                                <?php echo isset($_SESSION['nombre'], $_SESSION['apellido']) ? $_SESSION['nombre'].' '.$_SESSION['apellido'] : 'Invitado'; ?>
                            </span>
                            <br>
                            <small class="text-muted">
                                <?php echo isset($_SESSION['nombreRol']) ? $_SESSION['nombreRol'] : ''; ?>
                            </small>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="perfil"><i class="ik ik-user dropdown-icon"></i> Profile</a>
                        <a class="dropdown-item" href="#"><i class="ik ik-settings dropdown-icon"></i> Settings</a>
                        <a class="dropdown-item" href="#"><span class="float-right"><span class="badge badge-primary">6</span></span><i class="ik ik-mail dropdown-icon"></i> Inbox</a>
                        <a class="dropdown-item" href="#"><i class="ik ik-navigation dropdown-icon"></i> Message</a>
                        <a class="dropdown-item" href="salir"><i class="ik ik-power dropdown-icon"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
</div>
