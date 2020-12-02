<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    $valido = true;
  
    if (!isset($_REQUEST['idUser']) || !isset($_REQUEST['idPublicacion'])) {
        $valido = false;
        $mensajeError = "El id del usuario o publicacion no ha sido ingresado";
    } else {
        $idusuario = $_REQUEST['idUser'];
        $idpublicacion = $_REQUEST['idPublicacion'];
        if (!validarUsuarioExistente($idusuario) || !validarPublicacionExistente($idpublicacion)) {
            $valido = false;
            $mensajeError = "El id del usuario o de la publicacion no se encuentran registrado";
        }
    }


    if ($valido) {
        if (estaMegusta($idusuario, $idpublicacion)) {
            $valor=true;
        } else {
            $valor=false;
        }
        $respuesta=['correcto'=>true, 'megusta'=>$valor];
        echo json_encode($respuesta);
    } else {
        $respuesta=['correcto'=>false, 'mensaje'=>$mensajeError];
        echo json_encode($respuesta);
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

    function estaMegusta($idusuario, $idpublicacion)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT * FROM megusta WHERE id_usuario=:id_usuario AND id_publicacion=:id_publicacion");
        $stm->bindParam('id_usuario', $idusuario);
        $stm->bindParam('id_publicacion', $idpublicacion);
        $stm->execute();
        $contador=$stm->rowCount();
        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }
