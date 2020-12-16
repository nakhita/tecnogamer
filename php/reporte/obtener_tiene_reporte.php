<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    $valido = true;
    $idUsuario = obtenerUsuario();
  
    if (!isset($_REQUEST['idPublicacion'])) {
        $valido = false;
        $mensajeError = "El id de la publicacion no ha sido ingresado";
    } else {
        $idPublicacion = $_REQUEST['idPublicacion'];
        if (!validarPublicacionExistente($idPublicacion)) {
            $valido = false;
            $mensajeError = "El id de la publicacion no se encuentra registrado";
        }
    }

    if ($valido) {
        if (tieneReporteRegistrado($idUsuario, $idPublicacion)) {
            $valor=true;
        } else {
            $valor=false;
        }
        $respuesta=['correcto'=>true, 'tieneReporte'=>$valor];
        echo json_encode($respuesta);
    } else {
        $respuesta=['correcto'=>false, 'mensaje'=>$mensajeError];
        echo json_encode($respuesta);
    }

    function validarPublicacionExistente($idPublicacion)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT * FROM publicacion WHERE id=:id_publicacion AND estado=1");
        $stm->bindParam('id_publicacion', $idPublicacion);
        $stm->execute();
        $contador=$stm->rowCount();
        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }

    function tieneReporteRegistrado($idUsuario, $idPublicacion)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT * FROM reporte WHERE id_reportador=:id_reportador AND id_publicacion=:id_publicacion AND estado = 1");
        $stm->bindParam('id_reportador', $idUsuario);
        $stm->bindParam('id_publicacion', $idPublicacion);
        $stm->execute();
        $contador=$stm->rowCount();
        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }
