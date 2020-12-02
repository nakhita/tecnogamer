<?php
    include("../conexion_bd.php");
    header("Content-Type: application/json; charset=UTF-8");
    
    $valido = true;

    if (!isset($_REQUEST['idUser']) || !isset($_REQUEST['idAutor'])) {
        $valido = false;
        $mensajeError = "El id de usuario o autor no ha sido ingresado";
    } else {
        $idusuario = $_REQUEST['idUser'];
        $idautor = $_REQUEST['idAutor'];
        if (!validarUsuarioExistente($idusuario) || !validarUsuarioExistente($idautor)) {
            $valido = false;
            $mensajeError = "El id de usuario o autor no se encuentra registrado";
        } elseif (!validarEstaSuscrito($idusuario, $idautor)) {
            $valido = false;
            $mensajeError = "El usuario no esta suscrito al autor";
        }
    }

    if ($valido) {
        $eliminados = removerSuscripcion($idusuario, $idautor);
        if ($eliminados > 0) {
            $valor="Se ha desuscrito correctamente";
        } else {
            $valor="Ha ocurrido un error al desuscribirse";
        }
        $respuesta=['correcto'=>true, 'mensaje'=>$valor];
        echo json_encode($respuesta);
    } else {
        $respuesta=['correcto'=>false, 'mensaje'=>$mensajeError];
        echo json_encode($respuesta);
    }


    function validarEstaSuscrito($idusuario, $idautor)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT * FROM suscripcion WHERE id_usuario=:idusuario AND id_autor=:idautor");
        $stm->bindParam('idusuario', $idusuario);
        $stm->bindParam('idautor', $idautor);
        $stm->execute();
        $contador=$stm->rowCount();
        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }

    function validarUsuarioExistente($idusuario)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT * FROM usuario WHERE id=:idusuario AND estado=1");
        $stm->bindParam('idusuario', $idusuario);
        $stm->execute();
        $contador=$stm->rowCount();
        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }

    function removerSuscripcion($idusuario, $idautor)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("DELETE FROM suscripcion WHERE id_usuario=:idusuario AND id_autor=:idautor");
        $stm->bindParam(':idusuario', $idusuario);
        $stm->bindParam(':idautor', $idautor);
        $stm->execute();
        $contador=$stm->rowCount();

        $conexion = null;

        return $contador;
    }
