<?php
    include("../conexion_bd.php");
 
    $valido = true;
    
    $usuario = $_REQUEST['usuario'];
    $password = $_REQUEST['password'];

    if (!isset($usuario)) {
        $valido = false;
        $mensajeError = "Ingrese un usuario";
    }

    if (!isset($password)) {
        $valido = false;
        $mensajeError = "Ingrese un password";
    }


    if ($valido) {
        if (!existeUsuario($usuario, $password)) {
            $valido = false;
            $mensajeError = "El usuario o contraseña ingresado no es correcto";
        }
    }
 
    if ($valido) {
        $id = obtenerId($usuario);
        session_start();
        $_SESSION['user']=$id;
        $respuesta=['correcto'=>true,'id'=>$id, 'session'=> true];
        echo json_encode($respuesta);
    } else {
        $respuesta=['correcto'=>false, 'mensaje'=>$mensajeError];
        echo json_encode($respuesta);
    }


/*
 * FUNCIONES
 */

    function existeUsuario($usuario, $password)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stm=$conexion->prepare("SELECT * FROM usuario WHERE usuario=:usuario AND password=:password AND estado=1");
        $stm->bindParam('usuario', $usuario);
        $stm->bindParam('password', $password);
        $stm->execute();
        $contador=$stm->rowCount();
        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }
    function obtenerId($usuario)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stm=$conexion->prepare("SELECT id FROM usuario WHERE usuario=:usuario AND estado=1");
        $stm->bindParam('usuario', $usuario);
        $stm->execute();
        $id=$stm->fetchColumn(0);
        $conexion=null;
        return $id;
    }
