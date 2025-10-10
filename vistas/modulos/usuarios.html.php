<div class="wrapper">
  <div class="page-wrap">
    <div class="main-content">
    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#modalAgregarUsuario">Registro Usuario</button>
      
      <div class="card">

        <div class="card-header d-block">
            <h3>REGISTRO USUARIOS</h3>
        </div>
        <div class="card-body p-0 table-border-style">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                          <th style="width:10px">ID</th>
                          <th>NOMBRE</th>
                          <th>USUARIO</th>
                          <th>FOTO</th>
                          <th>PERFIL</th>
                          <th>ESTADO</th>
                          <th>FECHA LOGIN</th>
                          <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>san Francisco</td>
                        <td>administrador</td>
                        <td><img src="vistas/img/usuarios/default/anonymous.png" class="img-thumbnail" width="40px"></td>
                        <td>Admi</td>                      
                        <td><button class="btn btn-success btn-xs">Activado</button></td>
                        <td>12/12/12</td>
                        <td>
                        <div class="btn-group">
                          <button type="button" class="btn btn-dark"><i class="ik ik-edit-2"></i>Editar</button>
                          <button type="button" class="btn btn-danger"><i class="ik ik-trash-2"></i>Eliminar</button>
                        </div>
                        </td>  
                      </tr>
                      <tr>
                        <td>1</td>
                        <td>san Francisco</td>
                        <td>administrador</td>
                        <td><img src="vistas/img/usuarios/default/anonymous.png" class="img-thumbnail" width="40px"></td>
                        <td>Admi</td>                      
                        <td><button class="btn btn-success btn-xs">Activado</button></td>
                        <td>12/12/12</td>
                        <td>
                        <div class="btn-group">
                          <button type="button" class="btn btn-dark"><i class="ik ik-edit-2"></i>Editar</button>
                          <button type="button" class="btn btn-danger"><i class="ik ik-trash-2"></i>Eliminar</button>
                        </div>
                        </td>  
                      </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="page-wrap">

    <!--=====================================
MODAL AGREGAR USUARIO
======================================-->

<!-- Modal para agregar usuario -->
<div id="modalAgregarUsuario" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background-color: #3c8dbc; color: white;">
          <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
          <h4 class="modal-title">Agregar usuario</h4>
        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->
        <div class="modal-body">
          <div class="box-body">
            <!-- Entrada para el Nombre -->
            <div class="form-group">
              <label for="nuevoNombre">Nombre</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control input-lg" id="nuevoNombre" name="nuevoNombre" placeholder="Ingresar nombre" required />
              </div>
            </div>

            <!-- Entrada para el Usuario -->
            <div class="form-group">
              <label for="nuevoUsuario">Usuario</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                <input type="text" class="form-control input-lg" id="nuevoUsuario" name="nuevoUsuario" placeholder="Ingresar usuario" required />
              </div>
            </div>

            <!-- Entrada para la Contraseña -->
            <div class="form-group">
              <label for="nuevoPassword">Contraseña</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                <input type="password" class="form-control input-lg" id="nuevoPassword" name="nuevoPassword" placeholder="Ingresar contraseña" required />
              </div>
            </div>

            <!-- Entrada para Seleccionar Perfil -->
            <div class="form-group">
              <label for="nuevoPerfil">Perfil</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-users"></i></span>
                <select class="form-control input-lg" id="nuevoPerfil" name="nuevoPerfil">
                  <option value="">Seleccionar perfil</option>
                  <option value="Administrador">Administrador</option>
                  <option value="Especial">Especial</option>
                  <option value="Vendedor">Vendedor</option>
                </select>
              </div>
            </div>

            <!-- Entrada para Subir Foto -->
            <div class="form-group">
              <label for="nuevaFoto">Foto de perfil</label>
              <input type="file" id="nuevaFoto" name="nuevaFoto" />
              <p class="help-block">Peso máximo de la foto 200 MB</p>
              <img src="vistas/img/usuarios/default/anonymous.png" class="img-thumbnail" width="100px" alt="Imagen por defecto" />
            </div>
          </div>
        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar usuario</button>
        </div>
      </form>
    </div>
  </div>
</div>

  </div>
</div>