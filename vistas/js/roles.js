/*=============================================
EDITAR ROL
=============================================*/
$(document).on("click", ".btnEditarRol", function () {
    var idRol = $(this).data("id"); // usa data-id

    var datos = new FormData();
    datos.append("idRol", idRol);

    $.ajax({
        url: "ajax/permisos.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (respuesta) {
            $("#editaridRol").val(respuesta["idRol"]);
            $("#editarRol").val(respuesta["nombreRol"]);
        }
    });
});


/*=============================================
ELIMINAR MEDICAMENTO
=============================================*/
$(document).on("click", ".btnEliminarRol", function () {

  var idRol = $(this).attr("idRol");

  swal({
    title: '¿Está seguro de borrar el rol?',
    text: "¡Si no lo está, puede cancelar la acción!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Cancelar',
    confirmButtonText: 'Sí, borrar rol!'
  }).then(function (result) {
    if (result.value) {
      window.location = "index.php?ruta=roles&idRol=" + idRol;
    }
  });

});


  $(document).on('click', '.verRol', function() {
                        const idRol = $(this).data('id');
                        const nombreRol = $(this).data('nombre');
                        const contenedor = document.getElementById('tablaPermisosContainer');
                        const tabla = document.getElementById('tablaPermisos');
                        const spanRol = document.getElementById('nombreRolSeleccionado');

                        spanRol.textContent = nombreRol;
                        contenedor.style.display = 'block';

                        fetch('ajax/permisos.ajax.php?idRol=' + idRol)
                            .then(response => response.text())
                            .then(data => {
                                tabla.innerHTML = data;

                                // Switches de módulos
                                document.querySelectorAll('.toggleModulo').forEach(toggle => {
                                    toggle.addEventListener('change', function() {
                                        const idRol = this.dataset.idrol;
                                        const idPermisos = this.dataset.idpermisos;
                                        const activo = this.checked ? 1 : 0;
                                        const fila = this.closest('tr');
                                        const barra = fila.querySelector('.progress-bar');
                                        const estado = fila.querySelector('.estado-badge');

                                        // Actualiza visualmente el módulo
                                        if (activo === 1) {
                                            barra.classList.replace('bg-danger', 'bg-success');
                                            barra.style.width = '100%';
                                            barra.textContent = '100%';
                                            estado.classList.replace('badge-danger', 'badge-success');
                                            estado.textContent = 'Habilitado';
                                            fila.querySelector('td:nth-child(2)').style.textDecoration = 'none';
                                        } else {
                                            barra.classList.replace('bg-success', 'bg-danger');
                                            barra.style.width = '0%';
                                            barra.textContent = '0%';
                                            estado.classList.replace('badge-success', 'badge-danger');
                                            estado.textContent = 'Deshabilitado';
                                            fila.querySelector('td:nth-child(2)').style.textDecoration = 'line-through';
                                        }

                                        // Actualiza en la base de datos
                                        fetch('ajax/permisos.ajax.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded'
                                                },
                                                body: `accion=actualizarPermiso&idRol=${idRol}&idPermisos=${idPermisos}&activo=${activo}`
                                            })
                                            .then(r => r.text())
                                            .then(resp => {
                                                swal({
                                                    type: "success",
                                                    title: "¡Se actualizó correctamente!",
                                                    showConfirmButton: true,
                                                    confirmButtonText: "Cerrar"
                                                }).then(function(result) {
                                                    if (result.value) {
                                                        // Si quieres redirigir a la página de roles
                                                        // window.location = "roles";

                                                        // O simplemente cerrar el alert sin recargar
                                                    }
                                                });
                                            });

                                    });
                                });

                                // Botones de formularios
                                document.querySelectorAll('.btnFormularios').forEach(btn => {
                                    btn.addEventListener('click', function() {
                                        const idRol = this.dataset.idrol;
                                        const modulo = this.dataset.modulo;
                                        document.getElementById('idRolModal').value = idRol;

                                        fetch(`ajax/permisos.ajax.php?accion=mostrarFormularios&idRol=${idRol}&modulo=${modulo}`)
                                            .then(res => res.text())
                                            .then(data => {
                                                document.getElementById('tablaFormularios').innerHTML = data;
                                                const modal = new bootstrap.Modal(document.getElementById('modalFormularios'));
                                                modal.show();

                                                document.querySelectorAll('.toggleFormulario').forEach(sw => {
                                                    sw.addEventListener('change', function() {
                                                        const idPermiso = this.dataset.idpermiso;
                                                        const activo = this.checked ? 1 : 0;
                                                        const filaForm = this.closest('tr');
                                                        const nombreForm = filaForm.querySelector('td:nth-child(2)');

                                                        // Oculta/mostrar nombre del permiso
                                                        if (activo === 1) {
                                                            nombreForm.style.textDecoration = 'none';
                                                            nombreForm.style.opacity = '1';
                                                        } else {
                                                            nombreForm.style.textDecoration = 'line-through';
                                                            nombreForm.style.opacity = '0.5';
                                                        }

                                                        fetch('ajax/permisos.ajax.php', {
                                                                method: 'POST',
                                                                headers: {
                                                                    'Content-Type': 'application/x-www-form-urlencoded'
                                                                },
                                                                body: `accion=actualizarPermisoIndividual&idRol=${idRol}&idPermiso=${idPermiso}&activo=${activo}`
                                                            })
                                                            .then(r => r.text())
                                                            .then(resp => {
                                                                // Aquí usamos tu estilo de swal clásico
                                                                swal({
                                                                    type: "success",
                                                                    title: "¡Se actualizó correctamente!",
                                                                    showConfirmButton: true,
                                                                    confirmButtonText: "Cerrar"
                                                                }).then(function(result) {
                                                                    if (result.value) {
                                                                        // Si quieres redirigir al módulo de roles:
                                                                        // window.location = "roles";

                                                                        // Si no quieres redirigir, puedes dejarlo en null
                                                                    }
                                                                });
                                                            });

                                                    });
                                                });
                                            });
                                    });
                                });
                            });
                    });