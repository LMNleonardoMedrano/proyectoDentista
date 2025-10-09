<?php
require_once "../controladores/roles.controlador.php";
require_once "../modelos/roles.modelo.php";
class AjaxRoles {

    /*=============================================
    EDITAR  ROL
    =============================================*/	

    public $idRol;

    public function ajaxEditarRoles() {

        $item = "idRol";
        $valor = $this->idRol;

        $respuesta = ControladorRoles::ctrMostrarRoles($item, $valor);

        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR MEDICAMENTO
=============================================*/	

if (isset($_POST["idRol"])) {

    $roles = new AjaxRoles();
    $roles->idRol = $_POST["idRol"];
    $roles->ajaxEditarRoles();
}



/* ===========================================================
   MOSTRAR PERMISOS POR ROL (GET)
   =========================================================== */
if (isset($_GET['idRol']) && !isset($_GET['accion'])) {
    $idRol = intval($_GET['idRol']);
    $permisos = ControladorRoles::ctrMostrarPermisosPorRol($idRol);

    $modulos = [];
    foreach ($permisos as $permiso) {
        $modulo = $permiso['modulo'];
        if (!isset($modulos[$modulo])) {
            $modulos[$modulo] = [
                'modulo' => $modulo,
                'idPermisos' => [],
                'tieneAcceso' => false
            ];
        }
        $modulos[$modulo]['idPermisos'][] = $permiso['idPermiso'];
        if ($permiso['tieneAcceso']) {
            $modulos[$modulo]['tieneAcceso'] = true;
        }
    }

    $n = 1;
    foreach ($modulos as $mod) {
        $acceso = $mod['tieneAcceso'] ? "checked" : "";
        $progreso = $mod['tieneAcceso'] ? "100%" : "0%";
        $colorProgreso = $mod['tieneAcceso'] ? "bg-success" : "bg-danger";
        $estado = $mod['tieneAcceso']
            ? "<span class='badge badge-success estado-badge'>Habilitado</span>"
            : "<span class='badge badge-danger estado-badge'>Deshabilitado</span>";

        echo "<tr>
                <td>{$n}</td>
                <td> {$mod['modulo']}</td>
                <td>
                    <div class='progress'>
                        <div class='progress-bar {$colorProgreso}' role='progressbar' style='width:{$progreso}'>{$progreso}</div>
                    </div>
                </td>
                <td class='text-center'>
                    <div class='form-check form-switch'>
                        <input class='form-check-input toggleModulo' type='checkbox'
                            data-idrol='{$idRol}'
                            data-idpermisos='" . implode(",", $mod['idPermisos']) . "'
                            {$acceso}>
                    </div>
                </td>
                <td class='text-center'>
                    <button class='btn btn-primary btn-sm btnFormularios'
                        data-idrol='{$idRol}'
                        data-modulo='{$mod['modulo']}'>
                        <i class='fas fa-folder'></i> Formularios
                    </button>
                </td>
                <td class='text-center'>{$estado}</td>
              </tr>";
        $n++;
    }
    exit;
}

/* ===========================================================
   ACTUALIZAR PERMISOS POR MÓDULO (POST)
   =========================================================== */
if (isset($_POST['accion']) && $_POST['accion'] == 'actualizarPermiso') {
    $idRol = intval($_POST['idRol']);
    $idPermisos = explode(",", $_POST['idPermisos']);
    $activo = intval($_POST['activo']);

    foreach ($idPermisos as $idPermiso) {
        if ($activo == 1) {
            ModeloRoles::mdlAsignarPermiso($idRol, $idPermiso);
        } else {
            ModeloRoles::mdlQuitarPermiso($idRol, $idPermiso);
        }
    }
    echo $activo == 1 ? "Permisos asignados correctamente." : "Permisos quitados correctamente.";
    exit;
}

/* ===========================================================
   MOSTRAR FORMULARIOS POR MÓDULO (GET)
   =========================================================== */
if (isset($_GET['accion']) && $_GET['accion'] == 'mostrarFormularios') {
    $idRol = intval($_GET['idRol']);
    $modulo = $_GET['modulo'];

    $formularios = ControladorRoles::ctrMostrarFormulariosPorModulo($idRol, $modulo);

    $n = 1;
    foreach ($formularios as $form) {
        $checked = $form['tieneAcceso'] ? 'checked' : '';
        echo "<tr>
                <td>{$n}</td>
                <td>{$form['nombrePermiso']}</td>
                <td class='text-center'>
                    <div class='form-check form-switch'>
                        <input class='form-check-input toggleFormulario'
                               type='checkbox'
                               data-idpermiso='{$form['idPermiso']}'
                               {$checked}>
                    </div>
                </td>
              </tr>";
        $n++;
    }
    exit;
}

/* ===========================================================
   ACTUALIZAR PERMISO INDIVIDUAL (POST)
   =========================================================== */
if (isset($_POST['accion']) && $_POST['accion'] == 'actualizarPermisoIndividual') {
    $idRol = intval($_POST['idRol']);
    $idPermiso = intval($_POST['idPermiso']);
    $activo = intval($_POST['activo']);

    if ($activo == 1) {
        ModeloRoles::mdlAsignarPermiso($idRol, $idPermiso);
        echo "Formulario habilitado.";
    } else {
        ModeloRoles::mdlQuitarPermiso($idRol, $idPermiso);
        echo "Formulario deshabilitado.";
    }
    exit;
}
?>
