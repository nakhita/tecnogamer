<?php
    include("../conexion_bd.php");

    header("Content-Type: application/json; charset=UTF-8");

    $valido = true;
  
    // Validaciones
    if (!isset($_REQUEST['idUser']) || !isset($_REQUEST['idPublicacion'])) {
        $valido = false;
        $mensajeError = "El id de usuario o publicacion no ha sido ingresado";
    } else {
        $idusuario = $_REQUEST['idUser'];
        $idpublicacion = $_REQUEST['idPublicacion'];
        if (!validarUsuarioExistente($idusuario) || !validarPublicacionExistente($idpublicacion)) {
            $valido = false;
            $mensajeError = "El id del usuario o de la publicacion no se encuentra registrado";
        } elseif (validarEstaMegusta($idusuario, $idpublicacion)) {
            $valido = false;
            $mensajeError = "El usuario ya le ha dado me gusta a esta publicacion";
        }
    }

    // Inserciones
    if ($valido) {
        $insertados = insertarMegusta($idusuario, $idpublicacion);
        if ($insertados > 0) {
            $valor="Se ha agregado correctamente";
        } else {
            $valor="Ha ocurrido un error al agregar me gusta";
        }
        $respuesta=['correcto'=>true, 'mensaje'=>$valor];
        echo json_encode($respuesta);
    } else {
        $respuesta=['correcto'=>false, 'mensaje'=>$mensajeError];
        echo json_encode($respuesta);
    }


    function validarPublicacionExistente($idpublicacion)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT * FROM publicacion WHERE id=:idpublicacion AND estado=1");
        $stm->bindParam('idpublicacion', $idpublicacion);
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

    function validarEstaMegusta($idusuario, $idpublicacion)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT * FROM megusta WHERE id_usuario=:idusuario AND id_publicacion=:idpublicacion");
        $stm->bindParam('idusuario', $idusuario);
        $stm->bindParam('idpublicacion', $idpublicacion);
        $stm->execute();
        $contador=$stm->rowCount();
        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }

    function insertarMegusta($idusuario, $idpublicacion)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("INSERT INTO megusta (id_usuario, id_publicacion, fecha) VALUES (:idusuario, :idpublicacion, NOW())");
        $stm->bindParam(':idusuario', $idusuario);
        $stm->bindParam(':idpublicacion', $idpublicacion);
        $stm->execute();
        $contador=$stm->rowCount();

        $conexion = null;

        return $contador;
    }
