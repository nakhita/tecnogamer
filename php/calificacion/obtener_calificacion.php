<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    $conexion = obtenerConexion();

    $valido = true;

    if (!isset($_REQUEST["id"])) {
        $valido = false;
        $mensaje = "El id de publicacion debe ser ingresado";
    } else {
        $idPublicacion = $_REQUEST["id"];
        $idUsuario = obtenerUsuario();
    }


    if ($valido) {
        $puntaje = obtenerPuntaje($idPublicacion);
        $tieneCalificacion = obtenerCalificacionExistente($idUsuario, $idPublicacion);
        $respuesta=['correcto'=>true, 'puntaje'=> $puntaje, 'tieneCalificacion'=> $tieneCalificacion];
        
        echo json_encode($respuesta);
    } else {
        $respuesta=['correcto'=>false, 'mensaje'=> "No se ha podido obtener la calificacion"];
        echo json_encode($respuesta);
    }

    function obtenerPuntaje($idPublicacion)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("SELECT SUM(c.estrellas) AS estrellas, COUNT(c.id) AS total FROM calificacion c WHERE c.id_publicacion = :id_publicacion");
        $statement->bindParam("id_publicacion", $idPublicacion);
        $statement->execute();
        $calificacion = $statement->fetch(PDO::FETCH_ASSOC);

        $estrellas = $calificacion["estrellas"];
        $total = $calificacion["total"];
        if ($total > 0) {
            $puntaje = round($estrellas / $total, 2);
        } else {
            $puntaje = 0;
        }

        return $puntaje;
    }


    function obtenerCalificacionExistente($idUsuario, $idPublicacion)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("SELECT id FROM calificacion WHERE id_usuario = :id_usuario AND id_publicacion = :id_publicacion ");
        $statement->bindparam("id_usuario", $idUsuario);
        $statement->bindparam("id_publicacion", $idPublicacion);
        $statement->execute();
        $id_calificacion = $statement->fetchColumn(0);

        $conexion = null;

        return $id_calificacion;
    }
