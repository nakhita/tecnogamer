<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");

    header("Content-Type: application/json; charset=UTF-8");

    $idAdmin = obtenerUsuario();
    $valido = true;
  
    // Validaciones
    if (!isset($_REQUEST['id']) && $_REQUEST['id'] != "") {
        $valido = false;
        $mensajeError = "El id de la notificacion no ha sido ingresado";
    } elseif (!isset($_REQUEST['visto'])) {
        $valido = false;
        $mensajeError = "El parametro visto no ha sido ingresado";
    } else {
        $idNotificacion = $_REQUEST['id'];
        $visto = $_REQUEST['visto'];
        if (!validarNotificacionExistente($idNotificacion)) {
            $valido = false;
            $mensajeError = "El id de la notificacion no existe";
        }
        if ($visto != 0 && $visto != 1) {
            $valido = false;
            $mensajeError = "El parametro visto debe ser 0 o 1";
        }
    }

    if ($valido) {
        // Actualizar notificacion
        $actualizados = actualizarNotificacion($idNotificacion, $visto);
        if ($actualizados > 0) {
            $valor = "Se ha actualizado la notificacion";
        } else {
            $valor = "Ha ocurrido un error al actualizar la notificacion";
        }
        $respuesta = ['correcto' => true, 'mensaje' => $valor];
        echo json_encode($respuesta);
    } else {
        $respuesta = ['correcto' => false, 'mensaje' => $mensajeError];
        echo json_encode($respuesta);
    }

    function actualizarNotificacion($idNotificacion, $visto)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("UPDATE notificacion SET visto = :visto WHERE id = :id_notificacion");
        $stm->bindParam('id_notificacion', $idNotificacion);
        $stm->bindParam('visto', $visto);
        $stm->execute();
        $contador=$stm->rowCount();

        $conexion = null;

        return $contador;
    }

    function validarNotificacionExistente($idNotificacion)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT * FROM notificacion WHERE id=:idNotificacion AND estado=1");
        $stm->bindParam('idNotificacion', $idNotificacion);
        $stm->execute();
        $contador=$stm->rowCount();
        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }
