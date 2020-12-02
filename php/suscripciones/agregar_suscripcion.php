<?php
    include("../conexion_bd.php");

    header("Content-Type: application/json; charset=UTF-8");

    $valido = true;
  
    // Validaciones
    if (!isset($_REQUEST['idUser']) || !isset($_REQUEST['idAutor'])) {
        $valido = false;
        $mensajeError = "El id de usuario o autor no ha sido ingresado";
    } else {
        $idusuario = $_REQUEST['idUser'];
        $idautor = $_REQUEST['idAutor'];
        if (!validarUsuarioExistente($idusuario) || !validarUsuarioExistente($idautor)) {
            $valido = false;
            $mensajeError = "El id de usuario o autor no se encuentra registrado";
        } elseif (validarEstaSuscrito($idusuario, $idautor)) {
            $valido = false;
            $mensajeError = "El usuario ya esta suscrito al autor";
        }
    }

    // Inserciones
    if ($valido) {
        $insertados = insertarSuscripcion($idusuario, $idautor);
        if ($insertados > 0) {
            $valor="Se ha suscrito correctamente";
        } else {
            $valor="Ha ocurrido un error al suscribirse";
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

    function insertarSuscripcion($idusuario, $idautor)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("INSERT INTO suscripcion (id_usuario, id_autor, fecha) VALUES (:idusuario, :idautor, NOW())");
        $stm->bindParam(':idusuario', $idusuario);
        $stm->bindParam(':idautor', $idautor);
        $stm->execute();
        $contador=$stm->rowCount();

        $conexion = null;

        return $contador;
    }
