<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    $conexion = obtenerConexion();

    $valido = true;

    if (!isset($_REQUEST["id_publicacion"])) {
        $valido = false;
        $mensaje = "Debe ingresar una id de publicacion";
    } elseif (!isset($_REQUEST["estrellas"])) {
        $valido = false;
        $mensaje = "Debe ingresar una cantidad de estrellas";
    } else {
        $idPublicacion = $_REQUEST["id_publicacion"];
        $idUsuario = obtenerUsuario();
        $estrellas = $_REQUEST["estrellas"];
    }

    if ($valido) {
        $idCalificacion = obtenerCalificacionExistente($idUsuario, $idPublicacion);
        if ($idCalificacion > 0) {
            $insertados = actualizarCalificacion($idCalificacion, $estrellas);
        } else {
            $insertados = insertarCalificacion($idUsuario, $idPublicacion, $estrellas);
        }
        
        $respuesta=['correcto'=>true, 'mensaje'=> "Se pudo ingresar la calificacion correctamente"];
        
        echo json_encode($respuesta);
    } else {
        $respuesta=['correcto'=>false, 'mensaje'=> "No se ha podido ingresar la calificacion"];
        echo json_encode($respuesta);
    }

    function insertarCalificacion($id_usuario, $id_publicacion, $estrellas)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("INSERT INTO calificacion (id_usuario, id_publicacion, estrellas) values (:id_usuario, :id_publicacion, :estrellas)");
        $statement->bindparam("id_usuario", $id_usuario);
        $statement->bindparam("id_publicacion", $id_publicacion);
        $statement->bindparam("estrellas", $estrellas);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion = null;

        return $contador;
    }

    function actualizarCalificacion($id_calificacion, $estrellas)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("UPDATE calificacion SET estrellas = :estrellas WHERE id = :id_calificacion");
        $statement->bindparam("id_calificacion", $id_calificacion);
        $statement->bindparam("estrellas", $estrellas);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion = null;

        return $contador;
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
