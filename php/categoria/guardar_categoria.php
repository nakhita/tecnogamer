<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
 
    header("Content-Type: application/json; charset=UTF-8");
 
    $idUsuario = obtenerUsuario();
    $valido = true;
    $mensajeError = "Error al guardar o actualizar la categoria";

    if (!isset($_REQUEST["idCategoria"]) || $_REQUEST["idCategoria"] == "") {
        $idCategoria = 0;
    } else {
        $idCategoria = $_REQUEST["idCategoria"];
    }

    if (!isset($_REQUEST["categoria"]) || $_REQUEST["categoria"] == "") {
        $valido = false;
        $mensajeError = "La categoria es requerida";
    } else {
        $categoria = $_REQUEST["categoria"];
    }


    if ($valido) {
        if ($idCategoria == 0) {
            if (validarExisteCategoria($categoria)) {
                $valido = false;
                $mensajeError = "No se puede insertar porque ya existe una categoria con ese nombre";
            } else {
                $insertados = insertarCategoria($categoria);
            }
        } else {
            $insertados = actualizarCategoria($idCategoria, $categoria);
        }

        if ($valido && $insertados > 0) {
            $respuesta=['correcto'=>true, 'insertados'=> $insertados];
            echo json_encode($respuesta);
        } else {
            $respuesta=['correcto'=>false, 'mensajeError'=> $mensajeError];
            echo json_encode($respuesta);
        }
    } else {
        $respuesta=['correcto'=>false, 'mensajeError'=> $mensajeError];
        echo json_encode($respuesta);
    }



    function insertarCategoria($categoria)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("INSERT INTO categoria (nombre) values (:nombre)");
        $statement->bindparam("nombre", $categoria);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function actualizarCategoria($idCategoria, $categoria)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("UPDATE categoria SET nombre=:categoria WHERE id=:id_categoria");
        $statement->bindparam("categoria", $categoria);
        $statement->bindparam("id_categoria", $idCategoria);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function validarExisteCategoria($categoria)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("SELECT * FROM categoria WHERE nombre = :nombre");
        $statement->bindparam("nombre", $categoria);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }
