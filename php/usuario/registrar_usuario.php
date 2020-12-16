<?php
    include("../conexion_bd.php");

    $valido = true;

    $patronAlfabetico = "/^[a-zA-Z ]+$/";
    $patronAlfanumerico = "/^[a-zA-Z0-9]+$/";
    if (!isset($_REQUEST['nombre'])) {
        $valido = false;
        $mensajeError = "Ingrese un nombre";
    } elseif (!preg_match($patronAlfabetico, $_REQUEST['nombre'])) {
        $valido = false;
        $mensajeError = "El nombre debe ser alfabetico";
    } elseif (!isset($_REQUEST['apellido'])) {
        $valido = false;
        $mensajeError = "Ingrese un apellido";
    } elseif (!preg_match($patronAlfabetico, $_REQUEST['apellido'])) {
        $valido = false;
        $mensajeError = "El apellido debe ser alfabetico";
    } elseif (!isset($_REQUEST['sexo'])) {
        $valido = false;
        $mensajeError = "Ingrese un valor para sexo";
    } elseif ($_REQUEST['sexo'] != "M" && $_REQUEST['sexo'] != "F") {
        $valido = false;
        $mensajeError = "El valor sexo debe ser Masculino o Femenino";
    } elseif (!isset($_REQUEST['usuario'])) {
        $valido = false;
        $mensajeError = "Ingrese un usuario";
    } elseif (!preg_match($patronAlfanumerico, $_REQUEST['usuario'])) {
        $valido = false;
        $mensajeError = "El usuario debe ser alfanumerico sin espacios";
    } elseif (strlen($_REQUEST['usuario']) < 5 || strlen($_REQUEST['usuario']) > 20) {
        $valido = false;
        $mensajeError = "El usuario debe tener entre 5 y 20 caracteres";
    } elseif (!isset($_REQUEST['password'])) {
        $valido = false;
        $mensajeError = "Ingrese un password";
    } elseif (isset($_REQUEST['password']) && $_REQUEST['password'] != "" && (strlen($_REQUEST['password']) < 5 || strlen($_REQUEST['password']) > 20)) {
        $valido = false;
        $mensajeError = "El password debe tener entre 5 y 20 caracteres";
    } elseif (!isset($_REQUEST['email'])) {
        $valido = false;
        $mensajeError = "Ingrese un email";
    } elseif (!filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
        $valido = false;
        $mensajeError = "El email debe ser valido";
    } elseif (!isset($_REQUEST['rol'])) {
        $valido = false;
        $mensajeError = "Ingrese un rol";
    } elseif (!isset($_REQUEST['estado'])) {
        $valido = false;
        $mensajeError = "Ingrese un estado";
    }


    if ($valido) {
        $nombre = $_REQUEST['nombre'];
        $apellido = $_REQUEST['apellido'];
        $sexo = $_REQUEST['sexo'];
        $usuario = $_REQUEST['usuario'];
        $password = $_REQUEST['password'];
        $email = $_REQUEST['email'];
        $rol = $_REQUEST['rol'];
        $estado = $_REQUEST['estado'];
        $rutaImagen = $_REQUEST['rutaImagen'];

        if (existeUsuario($usuario)) {
            $valido = false;
            $mensajeError = "El usuario ingresado ya existe";
        } else {
            $pass_hash=password_hash($password,PASSWORD_DEFAULT);
            $idUsuario = insertarUsuario($nombre, $apellido, $email, $usuario, $pass_hash, $sexo, $rol, $estado);
            
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

    function insertarUsuario($nombre, $apellido, $email, $usuario, $password, $sexo, $rol, $estado)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("INSERT INTO usuario (nombre, apellido, email, usuario, password, sexo, id_rol, estado) VALUES (:nombre,:apellido,:email,:usuario,:password,:sexo,:rol,:estado)");
        $statement->bindparam("nombre", $nombre);
        $statement->bindparam("apellido", $apellido);
        $statement->bindparam("email", $email);
        $statement->bindparam("usuario", $usuario);
        $statement->bindparam("password", $password);
        $statement->bindparam("sexo", $sexo);
        $statement->bindparam("rol", $rol);
        $statement->bindparam("estado", $estado);
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
