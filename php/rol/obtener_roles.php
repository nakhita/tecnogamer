<?php
    include("../conexion_bd.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    $conexion = obtenerConexion();

    $statement=$conexion->prepare("SELECT r.id, r.nombre FROM rol r");
    $statement->execute();
    if (!$statement) {
        $respuesta=['correcto'=>false, 'mensaje'=> "No se ha podido obtener los roles"];
        echo json_encode($respuesta);
    } else {
        $roles = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($roles);
    }
    $conexion = null;
