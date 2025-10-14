
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

            <!-- Lado derecho: notificaciones y usuario -->
            <div class="top-menu d-flex align-items-center">

                <!-- 🔔 NOTIFICACIONES -->
                <div class="dropdown mr-3">
                    <a class="nav-link dropdown-toggle position-relative" href="#" id="notiDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ik ik-bell"></i>
                        <span class="badge bg-danger position-absolute" id="cantidadNotificaciones" style="top:-5px; right:-8px;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right notification-dropdown" aria-labelledby="notiDropdown" style="width:300px;">
                        <h4 class="header px-3">Notificaciones</h4>
                        <div class="notifications-wrap" id="contenedorNotificaciones">
                            <a href="#" class="dropdown-item text-center text-muted">Cargando...</a>
                        </div>
                        <div class="footer text-center py-2"><a href="citas">Ver todas las citas</a></div>
                    </div>
                </div>

                <!-- 👤 USUARIO -->
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="avatar" src="<?php echo !empty($_SESSION['foto']) ? $_SESSION['foto'] : 'vistas/img/user.jpg'; ?>" alt="Usuario">
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
                        <a class="dropdown-item" href="perfil"><i class="ik ik-user dropdown-icon"></i> Perfil</a>
                        <a class="dropdown-item" href="#"><i class="ik ik-settings dropdown-icon"></i> Configuración</a>
                        <a class="dropdown-item" href="#"><span class="float-right"><span class="badge badge-primary">6</span></span><i class="ik ik-mail dropdown-icon"></i> Inbox</a>
                        <a class="dropdown-item" href="#"><i class="ik ik-navigation dropdown-icon"></i> Mensaje</a>
                        <a class="dropdown-item" href="salir"><i class="ik ik-power dropdown-icon"></i> Cerrar sesión</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</header>
<input type="hidden" id="idUsuario" value="<?php echo $_SESSION['id']; ?>">
<input type="hidden" id="idRol" value="<?php echo $_SESSION['idRol']; ?>">


<style>
.dropdown-item.confirmada { background-color: #69e2baff; color: white; border-left: 4px solid #2ecc71; }
.dropdown-item.programada { background-color: #f8f5d7ff; color: black; border-left: 4px solid #f1c40f; }
.dropdown-item.atendida { background-color: #87ceebff; color: white; border-left: 4px solid #1e90ff; }
.notifications-wrap { max-height: 300px; overflow-y: auto; }
.notifications-wrap::-webkit-scrollbar { width: 6px; }
.notifications-wrap::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,0.2); border-radius: 3px; }
.dropdown-item { border-radius: 5px; margin-bottom: 5px; padding: 10px; transition: all 0.3s ease; }
.dropdown-item:hover { opacity: 0.9; }
</style>

<script>
$(document).ready(function(){
    const idUsuario = parseInt($("#idUsuario").val()) || 0;
    const idRol = parseInt($("#idRol").val()) || 0;

    function cargarNotificaciones(){
        var datos = new FormData();
        datos.append("accion", "notificaciones");
        datos.append("idUsuario", idUsuario);
        datos.append("idRol", idRol);
        datos.append("fecha", new Date().toISOString().split('T')[0]);

        $.ajax({
            url: "ajax/citas.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(respuesta){
                let html = "";
                let contadorProgramadas = 0;

                if(Array.isArray(respuesta) && respuesta.length > 0){
                    respuesta.forEach(n => {
                        let estado = (n.estado || '').toLowerCase().trim();
                        let claseEstado = '';
                        switch(estado){
                            case 'confirmada': claseEstado='confirmada'; break;
                            case 'atendida': claseEstado='atendida'; break;
                            default: claseEstado='programada';
                        }
                        if(estado==='programada') contadorProgramadas++;

                        html += `
                        <a href="citas" class="dropdown-item ${claseEstado}">
                            <div class="d-flex justify-content-between">
                                <strong>${n.paciente}</strong>
                                <small>${n.hora}</small>
                            </div>
                            <div>${n.motivoConsulta}</div>
                            <small>Fecha: ${n.fecha} | Estado: ${n.estado}</small>
                            <div class="text-right"><small><strong>Odontólogo:</strong> ${n.odontologo}</small></div>
                        </a>`;
                    });
                } else {
                    html = `<span class="dropdown-item text-center text-muted">Sin notificaciones</span>`;
                }

                $("#contenedorNotificaciones").html(html);
                $("#cantidadNotificaciones").text(contadorProgramadas);
            }
        });
    }

    cargarNotificaciones();
    setInterval(cargarNotificaciones, 60000);
});

</script>
