<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");

    header("Content-Type: application/json; charset=UTF-8");

    $valido = true;
    $idUsuario = obtenerUsuario();
  
    // Validaciones
    if (!isset($_REQUEST['idPublicacion']) || !isset($_REQUEST['idPublicacion'])) {
        $valido = false;
        $mensajeError = "El id de publicacion no ha sido ingresado";
    } elseif (!isset($_REQUEST['comentario']) || !isset($_REQUEST['comentario'])) {
        $valido = false;
        $mensajeError = "El comentario no ha sido ingresado";
    } else {
        $idPublicacion = $_REQUEST['idPublicacion'];
        $comentario = $_REQUEST['comentario'];
        if (!validarPublicacionExistente($idPublicacion)) {
            $valido = false;
            $mensajeError = "El id de publicacion no existe";
        }
    }

    // Inserciones
    if ($valido) {
        $insertados = insertarReporte($idUsuario, $idPublicacion, $comentario);
        if ($insertados > 0) {
            $valor = "Se ha insertado el reporte correctamente";
        } else {
            $valor = "Ha ocurrido un error al insertar un reporte";
        }
        $respuesta = ['correcto' => true, 'mensaje' => $valor];
        echo json_encode($respuesta);
    } else {
        $respuesta = ['correcto' => false, 'mensaje' => $mensajeError];
        echo json_encode($respuesta);
    }

    function insertarReporte($idUsuario, $idPublicacion, $comentario)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("INSERT INTO reporte (id_publicacion, id_reportador, comentario, fecha, estado) VALUES (:idpublicacion, :idreportador, :comentario, NOW(), 1)");
        $stm->bindParam('idpublicacion', $idPublicacion);
        $stm->bindParam('idreportador', $idUsuario);
        $stm->bindParam('comentario', $comentario);
        $stm->execute();
        $contador=$stm->rowCount();

        $conexion = null;

        return $contador;
    }

    function validarPublicacionExistente($idPublicacion)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT * FROM publicacion WHERE id=:idpublicacion AND estado=1");
        $stm->bindParam('idpublicacion', $idPublicacion);
        $stm->execute();
        $contador=$stm->rowCount();
        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }
