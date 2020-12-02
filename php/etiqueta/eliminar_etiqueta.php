<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
 
    header("Content-Type: application/json; charset=UTF-8");
 
    $idUsuario = obtenerUsuario();
    $valido = true;

    if (!isset($_REQUEST["idEtiqueta"]) || $_REQUEST["idEtiqueta"] == "") {
        $valido = false;
    } else {
        $idEtiqueta = $_REQUEST["idEtiqueta"];
    }


    if ($valido) {
        $eliminados = eliminarEtiqueta($idEtiqueta);

        if ($eliminados > 0) {
            $respuesta=['correcto'=>true, 'eliminados'=> $eliminados];
            echo json_encode($respuesta);
        } else {
            $respuesta=['correcto'=>false, 'mensajeError'=> "No se ha podido eliminar la etiqueta"];
            echo json_encode($respuesta);
        }
    } else {
        $respuesta=['correcto'=>false, 'mensajeError'=> $mensajeError];
        echo json_encode($respuesta);
    }
    
    

    function eliminarEtiqueta($idEtiqueta)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("DELETE FROM etiqueta WHERE id = :id");
        $statement->bindparam("id", $idEtiqueta);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }
