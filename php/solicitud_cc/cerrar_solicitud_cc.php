<?php
    include("../conexion_bd.php");

    header("Content-Type: application/json; charset=UTF-8");

    const APROBAR_Y_CERRAR = 1;
    const SOLO_CERRAR = 2;

    const ROL_CREADOR_CONTENIDO = 2;

    $valido = true;
  
    // Validaciones
    if (!isset($_REQUEST['idSolicitud']) || $_REQUEST['idSolicitud'] == "") {
        $valido = false;
        $mensajeError = "El id de la solicitud no ha sido ingresado";
    } elseif (!isset($_REQUEST['tipoCerrarSolicitud'])) {
        $valido = false;
        $mensajeError = "El tipo de cerrar solicitud no ha sido ingresado";
    } else {
        $idSolicitud = $_REQUEST['idSolicitud'];
        $tipoCerrarSolicitud = $_REQUEST['tipoCerrarSolicitud'];
        if (!validarSolicitudExistente($idSolicitud)) {
            $valido = false;
            $mensajeError = "El id de solicitud no existe";
        }
        if ($tipoCerrarSolicitud != SOLO_CERRAR && $tipoCerrarSolicitud != APROBAR_Y_CERRAR) {
            $valido = false;
            $mensajeError = "El tipo de cerrar solicitud no es valido";
        }
    }

    // Actualizaciones
    if ($valido) {
        if ($tipoCerrarSolicitud == APROBAR_Y_CERRAR) {
            $idUsuario = obtenerUsuarioSolicitud($idSolicitud);
            actualizarRolUsuario($idUsuario, ROL_CREADOR_CONTENIDO);
        }

        // Cerrar reporte
        $actualizados = cerrarSolicitud($idSolicitud);
        if ($actualizados > 0) {
            $valor = "Se ha cerrado la solicitud correctamente";
        } else {
            $valor = "Ha ocurrido un error al cerrar una solicitud";
        }
        $respuesta = ['correcto' => true, 'mensaje' => $valor];
        echo json_encode($respuesta);
    } else {
        $respuesta = ['correcto' => false, 'mensaje' => $mensajeError];
        echo json_encode($respuesta);
    }

    
    function validarSolicitudExistente($idSolicitud)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT * FROM solicitud_creador_contenido WHERE id=:idSolicitud AND estado=1");
        $stm->bindParam('idSolicitud', $idSolicitud);
        $stm->execute();
        $contador=$stm->rowCount();
        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }

    function cerrarSolicitud($idSolicitud)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("UPDATE solicitud_creador_contenido SET estado = 2 WHERE id = :id_solicitud");
        $stm->bindParam('id_solicitud', $idSolicitud);
        $stm->execute();
        $contador=$stm->rowCount();

        $conexion = null;

        return $contador;
    }

    function obtenerUsuarioSolicitud($idSolicitud)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("SELECT scc.id_usuario FROM solicitud_creador_contenido scc WHERE id = :id_solicitud");
        $statement->bindparam("id_solicitud", $idSolicitud);
        $statement->execute();
        
        $idUsuario = $statement->fetchColumn(0);

        $conexion=null;

        return $idUsuario;
    }

    function actualizarRolUsuario($idSolicitud, $rol)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("UPDATE usuario SET id_rol = :rol WHERE id = :id_usuario ");
        $statement->bindparam("rol", $rol);
        $statement->bindparam("id_usuario", $idSolicitud);
        $statement->execute();
        
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }
