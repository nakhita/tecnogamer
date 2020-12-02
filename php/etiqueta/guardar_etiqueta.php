<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
 
    header("Content-Type: application/json; charset=UTF-8");
 
    $idUsuario = obtenerUsuario();
    $valido = true;
    $mensajeError = "Error al guardar o actualizar la etiqueta";

    if (!isset($_REQUEST["idEtiqueta"]) || $_REQUEST["idEtiqueta"] == "") {
        $idEtiqueta = 0;
    } else {
        $idEtiqueta = $_REQUEST["idEtiqueta"];
    }

    if (!isset($_REQUEST["etiqueta"]) || $_REQUEST["etiqueta"] == "") {
        $valido = false;
        $mensajeError = "La etiqueta es requerida";
    } else {
        $etiqueta = $_REQUEST["etiqueta"];
    }

    if (!isset($_REQUEST["idCategoria"]) || $_REQUEST["idCategoria"] == "") {
        $valido = false;
        $mensajeError = "La categoria es requerida";
    } else {
        $idCategoria = $_REQUEST["idCategoria"];
    }


    if ($valido) {
        if ($idEtiqueta == 0) {
            if (validarExisteEtiqueta($etiqueta)) {
                $valido = false;
                $mensajeError = "No se puede insertar porque ya existe una etiqueta con ese nombre";
            } else {
                $insertados = insertarEtiqueta($etiqueta, $idCategoria);
            }
        } else {
            $insertados = actualizarEtiqueta($idEtiqueta, $etiqueta, $idCategoria);
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



    function insertarEtiqueta($etiqueta, $idCategoria)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("INSERT INTO etiqueta (nombre, id_categoria, estado) values (:nombre,:id_categoria,1)");
        $statement->bindparam("nombre", $etiqueta);
        $statement->bindparam("id_categoria", $idCategoria);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function actualizarEtiqueta($idEtiqueta, $etiqueta, $idCategoria)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("UPDATE etiqueta SET nombre=:etiqueta , id_categoria=:id_categoria WHERE id=:id_etiqueta");
        $statement->bindparam("id_etiqueta", $idEtiqueta);
        $statement->bindparam("etiqueta", $etiqueta);
        $statement->bindparam("id_categoria", $idCategoria);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function validarExisteEtiqueta($etiqueta)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("SELECT * FROM etiqueta WHERE nombre = :nombre");
        $statement->bindparam("nombre", $etiqueta);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }
