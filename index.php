<?php 
// 🕒 Configurar zona horaria Bolivia
date_default_timezone_set('America/La_Paz');

require_once "controladores/plantilla.controlador.php";
require_once "controladores/inicio.controlador.php";
require_once "controladores/usuarios.controlador.php";
require_once "controladores/roles.controlador.php";
require_once "controladores/paciente.controlador.php";
require_once "controladores/tutorPadre.controlador.php";
require_once "controladores/medicamentos.controlador.php";
require_once "controladores/servicios.controlador.php";
require_once "controladores/citas.controlador.php";
require_once "controladores/tratamiento.controlador.php";
require_once "controladores/planPagoTratamiento.controlador.php";
require_once "controladores/odontograma.controlador.php";

require_once "modelos/inicio.modelo.php";
require_once "modelos/usuarios.modelo.php";
require_once "modelos/roles.modelo.php";

require_once "modelos/paciente.modelo.php";
require_once "modelos/tutorPadre.modelo.php";
require_once "modelos/medicamentos.modelo.php";
require_once "modelos/servicios.modelo.php";
require_once "modelos/citas.modelo.php";
require_once "modelos/tratamiento.modelo.php";
require_once "modelos/planPagoTratamiento.modelo.php";
require_once "modelos/odontograma.modelo.php";





 
 $plantilla=new ControladorPlantilla();
 $plantilla->crtPlantilla();

 ?>