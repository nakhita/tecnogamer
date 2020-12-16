<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");

    header("Content-Type: application/json; charset=UTF-8");

    const SOLO_CERRAR = 1;
    const CERRAR_Y_ELIMINAR = 2;
    const CERRAR_ELIMINAR_Y_BANEAR = 3;

    $idAdmin = obtenerUsuario();
    $valido = true;
  
    // Validaciones
    if (!isset($_REQUEST['idPublicacion']) && $_REQUEST['idPublicacion'] != "") {
        $valido = false;
        $mensajeError = "El id de publicacion no ha sido ingresado";
    } elseif (!isset($_REQUEST['tipoCerrarReporte'])) {
        $valido = false;
        $mensajeError = "El tipo de cerrar reporte no ha sido ingresado";
    } else {
        $idPublicacion = $_REQUEST['idPublicacion'];
        $tipoCerrarReporte = $_REQUEST['tipoCerrarReporte'];
        if (!validarPublicacionExistente($idPublicacion)) {
            $valido = false;
            $mensajeError = "El id de publicacion no existe";
        }
        if ($tipoCerrarReporte != SOLO_CERRAR && $tipoCerrarReporte != CERRAR_Y_ELIMINAR && $tipoCerrarReporte != CERRAR_ELIMINAR_Y_BANEAR) {
            $valido = false;
            $mensajeError = "El tipo de cerrar reporte no es valido";
        }
    }

    // Actualizaciones
    if ($valido) {
        if ($tipoCerrarReporte == CERRAR_ELIMINAR_Y_BANEAR) {
            // Banear autor
            banearUsuario($idPublicacion);
        }

        if ($tipoCerrarReporte == CERRAR_ELIMINAR_Y_BANEAR || $tipoCerrarReporte == CERRAR_Y_ELIMINAR) {
            // Cerrar Publicacion
            eliminarPublicacion($idPublicacion);
        }

        // Cerrar reporte
        $actualizados = cerrarReportes($idPublicacion, $idAdmin);
        if ($actualizados > 0) {
            $valor = "Se han cerrado los reportes correctamente";
        } else {
            $valor = "Ha ocurrido un error al cerrar un reporte";
        }
        $respuesta = ['correcto' => true, 'mensaje' => $valor];
        echo json_encode($respuesta);
    } else {
        $respuesta = ['correcto' => false, 'mensaje' => $mensajeError];
        echo json_encode($respuesta);
    }

    function cerrarReportes($idPublicacion, $idAdmin)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("UPDATE reporte SET estado = 2, id_admin = :id_admin, fecha_atendido = NOW() WHERE id_publicacion = :id_publicacion");
        $stm->bindParam(':id_publicacion', $idPublicacion);
        $stm->bindParam(':id_admin', $idAdmin);
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

    function eliminarPublicacion($idPublicacion)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("UPDATE publicacion SET estado = 2 WHERE id = :id_publicacion");
        $stm->bindParam(':id_publicacion', $idPublicacion);
        $stm->execute();
        $contador=$stm->rowCount();

        $conexion = null;

        return $contador;
    }

    function banearUsuario($idPublicacion)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("UPDATE usuario SET estado = 2 WHERE id = (SELECT id_usuario FROM publicacion WHERE id = :id_publicacion)");
        $stm->bindParam(':id_publicacion', $idPublicacion);
        $stm->execute();
        $contador=$stm->rowCount();

        $conexion = null;

        return $contador;
    }
