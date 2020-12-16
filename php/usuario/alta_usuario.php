<?php
    include("../conexion_bd.php");

    $valido = true;
     
    $nombre = $_REQUEST['nombre'];
    $apellido = $_REQUEST['apellido'];
    $sexo = $_REQUEST['sexo'];
    $usuario = $_REQUEST['usuario'];
    $password = $_REQUEST['password'];
    $email = $_REQUEST['email'];
    $rutaImagen = $_REQUEST['rutaImagen'];

    if (isset($nombre)) {
        $pattern = "/^[a-zA-Z ]+$/";
        if (!preg_match($pattern, $nombre)) {
            $valido = false;
            $mensajeError = "El nombre debe ser alfabetico";
        }
    } else {
        $valido = false;
        $mensajeError = "Ingrese un nombre";
    }

    if (isset($apellido)) {
        $pattern = "/^[a-zA-Z ]+$/";
        if (!preg_match($pattern, $apellido)) {
            $valido = false;
            $mensajeError = "El apellido debe ser alfabetico";
        }
    } else {
        $valido = false;
        $mensajeError = "Ingrese un apellido";
    }

    if (isset($sexo)) {
        if ($sexo != "M" && $sexo != "F") {
            $valido = false;
            $mensajeError = "El valor sexo debe ser Masculino o Femenino";
        }
    } else {
        $valido = false;
        $mensajeError = "Ingrese un valor para sexo";
    }


    if (isset($usuario)) {
        $pattern = "/^[a-zA-Z0-9]+$/";
        if (!preg_match($pattern, $usuario)) {
            $valido = false;
            $mensajeError = "El usuario debe ser alfanumerico sin espacios";
        } elseif (strlen($usuario) < 5 || strlen($usuario) > 20) {
            $valido = false;
            $mensajeError = "El usuario debe tener entre 5 y 20 caracteres";
        }
    } else {
        $valido = false;
        $mensajeError = "Ingrese un usuario";
    }

    if (isset($password)) {
        if (strlen($password) < 5 || strlen($password) > 20) {
            $valido = false;
            $mensajeError = "El password debe tener entre 5 y 20 caracteres";
        }
    } else {
        $valido = false;
        $mensajeError = "Ingrese un password";
    }

    if (isset($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $valido = false;
            $mensajeError = "El email debe ser valido";
        }
    } else {
        $valido = false;
        $mensajeError = "Ingrese un email";
    }

    if (!isset($rutaImagen)) {
        $valido = false;
        $mensajeError = "Ingrese una imagen";
    }


    if ($valido) {
        if (existeUsuario($usuario)) {
            $valido = false;
            $mensajeError = "El usuario ingresado ya existe";
        } else {
            $pass_hash=password_hash($password,PASSWORD_DEFAULT);
            $idUsuario = insertarUsuario($nombre, $apellido, $email, $usuario, $pass_hash, $sexo);
            
            insertarImagen($idUsuario, $rutaImagen);
        }
    }

    if ($valido) {
        $respuesta=['correcto'=>true, 'usuario'=> $idUsuario];
        echo json_encode($respuesta);
    } else {
        $respuesta=['correcto'=>false, 'mensaje'=>$mensajeError];
        echo json_encode($respuesta);
    }


/**
 * FUNCIONES
 */

    function existeUsuario($usuario)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stm=$conexion->prepare("SELECT * FROM usuario WHERE usuario=:usuario AND estado=1");
        $stm->bindParam('usuario', $usuario);
        $stm->execute();
        $contador=$stm->rowCount();
        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }

    function insertarUsuario($nombre, $apellido, $email, $usuario, $password, $sexo)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("INSERT INTO usuario (nombre, apellido, email, usuario, password, sexo, id_rol, estado) VALUES (:nombre,:apellido,:email,:usuario,:password,:sexo,1,1)");
        $statement->bindparam("nombre", $nombre);
        $statement->bindparam("apellido", $apellido);
        $statement->bindparam("email", $email);
        $statement->bindparam("usuario", $usuario);
        $statement->bindparam("password", $password);
        $statement->bindparam("sexo", $sexo);
        $statement->execute();
        $insertId=$conexion->lastInsertId();

        $conexion=null;

        return $insertId;
    }

    function insertarImagen($id_usuario, $ruta_imagen)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("INSERT INTO avatar (id_usuario, ruta_imagen) values (:id_usuario,:ruta_imagen)");
        $statement->bindparam("id_usuario", $id_usuario);
        $statement->bindparam("ruta_imagen", $ruta_imagen);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }
