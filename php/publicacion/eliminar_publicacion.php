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

        $statement=$conexion->prepare("UPDATE publicacion SET estado = 2 WHERE id = :id");
        $statement->bindparam("id", $idPublicacion);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }
