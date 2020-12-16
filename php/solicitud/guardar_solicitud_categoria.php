<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");

    header("Content-Type: application/json; charset=UTF-8");

    $valido = true;
    $idUsuario = obtenerUsuario();
  
    // Validaciones
    if (!isset($_REQUEST['categoria']) || !isset($_REQUEST['categoria'])) {
        $valido = false;
        $mensajeError = "La categoria no ha sido ingresada";
    } elseif (!isset($_REQUEST['comentario']) || !isset($_REQUEST['comentario'])) {
        $valido = false;
        $mensajeError = "El comentario no ha sido ingresado";
    } elseif (!isset($_REQUEST['etiquetas']) || !isset($_REQUEST['etiquetas'])) {
        $valido = false;
        $mensajeError = "No se han ingresado etiquetas";
    } else {
        $categoria = $_REQUEST['categoria'];
        $comentario = $_REQUEST['comentario'];
        $etiquetas = $_REQUEST['etiquetas'];
    }

    // Inserciones
    if ($valido) {
        $idSolicitud = insertarSolicitudCategoria($idUsuario, $categoria, $comentario);
        foreach ($etiquetas as $etiqueta) {
            insertarSolicitudCategoriaEtiqueta($idSolicitud, $etiqueta);
        }
  
        if ($idSolicitud > 0) {
            $valor = "Se ha insertado la solicitud correctamente";
        } else {
            $valor = "Ha ocurrido un error al insertar la solicitud";
        }
        $respuesta = ['correcto' => true, 'mensaje' => $valor];
        echo json_encode($respuesta);
    } else {
        $respuesta = ['correcto' => false, 'mensaje' => $mensajeError];
        echo json_encode($respuesta);
    }

    function insertarSolicitudCategoria($idUsuario, $categoria, $comentario)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("INSERT INTO solicitud_categoria (comentario, categoria, id_usuario, fecha, estado) VALUES (:comentario, :categoria, :id_usuario, NOW(), 1)");
        $stm->bindParam('comentario', $comentario);
        $stm->bindParam('categoria', $categoria);
        $stm->bindParam('id_usuario', $idUsuario);
        $stm->execute();
        $insertId=$conexion->lastInsertId();

        $conexion=null;

        return $insertId;
    }

    function insertarSolicitudCategoriaEtiqueta($idSolicitud, $etiqueta)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("INSERT INTO solicitud_categoria_etiqueta (id_solicitud_categoria, etiqueta) VALUES (:id_solicitud, :etiqueta)");
        $stm->bindParam('id_solicitud', $idSolicitud);
        $stm->bindParam('etiqueta', $etiqueta);
        $stm->execute();
        $contador=$stm->rowCount();

        $conexion = null;

        return $contador;
    }
