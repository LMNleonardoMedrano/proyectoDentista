<?php

require_once "conexion.php";

class ModeloTutorPadre
{

	/*=============================================
	CREAR TutorPadre
	=============================================*/

	static public function mdlCrearTutorPadre($tabla, $datos)
	{
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(idPaciente, Nombre, Domicilio, FechaNac, Genero, Ci, Ocupacion, Relacion, TelCel) VALUES (:idPaciente, :Nombre, :Domicilio, :FechaNac, :Genero, :Ci, :Ocupacion, :Relacion, :TelCel)");

		$stmt->bindParam(":idPaciente", $datos["idPaciente"], PDO::PARAM_STR);
		$stmt->bindParam(":Nombre", $datos["Nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":Domicilio", $datos["Domicilio"], PDO::PARAM_STR);
		$stmt->bindParam(":FechaNac", $datos["FechaNac"], PDO::PARAM_STR);
		$stmt->bindParam(":Genero", $datos["Genero"], PDO::PARAM_STR);
		$stmt->bindParam(":Ci", $datos["Ci"], PDO::PARAM_STR);
		$stmt->bindParam(":Ocupacion", $datos["Ocupacion"], PDO::PARAM_STR);
		$stmt->bindParam(":Relacion", $datos["Relacion"], PDO::PARAM_STR);
		$stmt->bindParam(":TelCel", $datos["TelCel"], PDO::PARAM_STR);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}

		$stmt->close();
		$stmt = null;
	}

	/*=============================================
	MOSTRAR TutorPadre
	=============================================*/

	static public function mdlMostrarTutorPadre($tabla, $item, $valor)
	{

		if ($item != null) {
			// Si se pasa un filtro (por ejemplo, un ID específico)
			$stmt = Conexion::conectar()->prepare("SELECT t.IdTutorPadre, p.nombre AS idPaciente, t.Nombre, t.Domicilio, t.FechaNac, t.Genero, t.Ci, t.Ocupacion, t.Relacion, t.TelCel 
												   FROM $tabla t
												   JOIN paciente p ON t.idPaciente = p.idPaciente
												   WHERE $item = :$item");

			$stmt->bindParam(":$item", $valor, PDO::PARAM_STR);  // Enlazar el valor del parámetro con el tipo adecuado
			$stmt->execute();

			return $stmt->fetch();
		} else {
			// Si no se pasa un filtro, traer todos los TutorPadre con sus nombres de idPaciente
			$stmt = Conexion::conectar()->prepare("SELECT t.IdTutorPadre, p.nombre AS idPaciente, t.Nombre, t.Domicilio, t.FechaNac, t.Genero, t.Ci, t.Ocupacion, t.Relacion, t.TelCel 
												   FROM $tabla t
												   JOIN paciente p ON t.idPaciente = p.idPaciente");

			$stmt->execute();

			return $stmt->fetchAll();
		}

		$stmt->close();
		$stmt = null;
	}



	/*=============================================
EDITAR TutorPadre
=============================================*/

	static public function mdlEditarTutorPadre($tabla, $datos)
	{

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET idPaciente = :idPaciente, Nombre = :Nombre, Domicilio = :Domicilio, FechaNac = :FechaNac, Genero = :Genero, Ci = :Ci, Ocupacion = :Ocupacion, Relacion = :Relacion, TelCel = :TelCel WHERE IdTutorPadre = :IdTutorPadre");

		$stmt->bindParam(":IdTutorPadre", $datos["IdTutorPadre"], PDO::PARAM_INT); // Asegurando el bind del TutorPadre
		$stmt->bindParam(":idPaciente", $datos["idPaciente"], PDO::PARAM_INT);
		$stmt->bindParam(":Nombre", $datos["Nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":Domicilio", $datos["Domicilio"], PDO::PARAM_STR);
		$stmt->bindParam(":FechaNac", $datos["FechaNac"], PDO::PARAM_STR);
		$stmt->bindParam(":Genero", $datos["Genero"], PDO::PARAM_STR);
		$stmt->bindParam(":Ci", $datos["Ci"], PDO::PARAM_INT);
		$stmt->bindParam(":Ocupacion", $datos["Ocupacion"], PDO::PARAM_STR);
		$stmt->bindParam(":Relacion", $datos["Relacion"], PDO::PARAM_STR);
		$stmt->bindParam(":TelCel", $datos["TelCel"], PDO::PARAM_STR);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}

		$stmt->close();
		$stmt = null;
	}


	/*=============================================
	ELIMINAR TutorPadre
	=============================================*/

	static public function mdlEliminarTutorPadre($tabla, $datos)
	{

		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE IdTutorPadre = :IdTutorPadre");

		$stmt->bindParam(":IdTutorPadre", $datos, PDO::PARAM_INT);

		if ($stmt->execute()) {

			return "ok";
		} else {

			return "error";
		}

		$stmt->close();

		$stmt = null;
	}
}
