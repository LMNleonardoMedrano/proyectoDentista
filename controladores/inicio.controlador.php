<?php
require_once "modelos/inicio.modelo.php";

class ControladorInicio {

    static public function ctrObtenerEstadisticas() {
        return ModeloInicio::mdlObtenerEstadisticas();
    }
// Método para obtener las citas de hoy
    public static function ctrCitasHoy() {
        // Llamar al modelo para obtener las citas de hoy
        return ModeloInicio::mdlCitasHoy(); // Llama al método del modelo que devuelve las citas
    }
}
