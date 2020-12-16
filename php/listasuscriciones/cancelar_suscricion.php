<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");

    header("Content-Type: application/json; charset=UTF-8");
    
    $valido = true;
    $iduser = obtenerUsuario();
    if (!isset($_REQUEST['idSus'])) {
        $valido = false;
        $mensajeError = "El id de suscricion no existe";
    } else {
        $idsus = $_REQUEST['idSus'];
        $valido = validarSuscricion($idsus, $iduser);
    }

    if ($valido) {
        $desuscricion = removerSuscripcion($idsus);
        if ($desuscricion > 0) {
            $mensaje="Se ha desuscrito correctamente";
            $respuesta=['error'=>false,'correcto'=>true, 'mensaje'=>$mensaje];
        } else {
            $mensaje="Ha ocurrido un error al desuscribirse";
            $respuesta=['error'=>false,'correcto'=>false, 'mensaje'=>$mensaje];
        }
        echo json_encode($respuesta);
    } else {
        $respuesta=['error'=>true, 'mensaje'=>$mensajeError];
        echo json_encode($respuesta);
    }

    function validarSuscricion($idsus, $iduser)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT * FROM suscripcion WHERE id=:idsus and id_usuario=:iduser");
        $stm->bindParam('idsus', $idsus);
        $stm->bindParam('iduser', $iduser);
        $stm->execute();
        $sus=$stm->rowCount();
        $conexion=null;
        if ($sus > 0) {
            return true;
        } else {
            return false;
        }
    }

    function removerSuscripcion($idsus)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("UPDATE suscripcion SET estado = 2 WHERE id=:idsus");
        $stm->bindParam(':idsus', $idsus);
        $stm->execute();
        $contador=$stm->rowCount();

        $conexion = null;

        return $contador;
    }
