<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
 
    header("Content-Type: application/json; charset=UTF-8");
 
    $idUsuario = obtenerUsuario();
    $valido = true;

    if (!isset($_REQUEST["id"]) || $_REQUEST["id"] == "") {
        $valido = false;
        $mensajeError = "El id de la publicacion no ha sido ingresado";
    } else {
        $idPublicacion = $_REQUEST["id"];
    }


    if ($valido) {
        eliminarMegustaPublicacion($idPublicacion);
        eliminarImagenPublicacion($idPublicacion);
        eliminarPublicacionEtiqueta($idPublicacion);
        $eliminados = eliminarPublicacion($idPublicacion);

        if ($eliminados > 0) {
            $respuesta=['correcto'=>true, 'eliminados'=> $eliminados];
            echo json_encode($respuesta);
        } else {
            $respuesta=['correcto'=>false, 'mensajeError'=> "No se ha podido eliminar la publicacion"];
            echo json_encode($respuesta);
        }
    } else {
        $respuesta=['correcto'=>false, 'mensajeError'=> $mensajeError];
        echo json_encode($respuesta);
    }
    
    

    function eliminarPublicacion($idPublicacion)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("DELETE FROM publicacion WHERE id = :id");
        $statement->bindparam("id", $idPublicacion);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function eliminarPublicacionEtiqueta($idPublicacion)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("DELETE FROM publicacion_etiqueta WHERE id_publicacion = :id");
        $statement->bindparam("id", $idPublicacion);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function eliminarImagenPublicacion($idPublicacion)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("DELETE FROM imagen WHERE id_publicacion = :id");
        $statement->bindparam("id", $idPublicacion);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function eliminarMegustaPublicacion($idPublicacion)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("DELETE FROM megusta WHERE id_publicacion = :id");
        $statement->bindparam("id", $idPublicacion);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }
