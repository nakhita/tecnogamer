<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
 
    header("Content-Type: application/json; charset=UTF-8");
 
    $idUsuario = obtenerUsuario();
    $valido = true;

    if (!isset($_REQUEST["idCategoria"]) || $_REQUEST["idCategoria"] == "") {
        $valido = false;
    } else {
        $idCategoria = $_REQUEST["idCategoria"];
    }


    if ($valido) {
        eliminarEtiquetasDeCategoria($idCategoria);
        $eliminados = eliminarCategoria($idCategoria);

        if ($eliminados > 0) {
            $respuesta=['correcto'=>true, 'eliminados'=> $eliminados];
            echo json_encode($respuesta);
        } else {
            $respuesta=['correcto'=>false, 'mensajeError'=> "No se ha podido eliminar la categoria"];
            echo json_encode($respuesta);
        }
    } else {
        $respuesta=['correcto'=>false, 'mensajeError'=> $mensajeError];
        echo json_encode($respuesta);
    }
    
    

    function eliminarCategoria($idCategoria)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("DELETE FROM categoria WHERE id = :id");
        $statement->bindparam("id", $idCategoria);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function eliminarEtiquetasDeCategoria($idCategoria)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("DELETE FROM etiqueta WHERE id_categoria = :id");
        $statement->bindparam("id", $idCategoria);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }
