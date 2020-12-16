<?php
    include("../conexion_bd.php");

    $valido = true;

    $patronAlfabetico = "/^[a-zA-Z ]+$/";
    $patronAlfanumerico = "/^[a-zA-Z0-9]+$/";
    if (!isset($_REQUEST['id'])) {
        $valido = false;
        $mensajeError = "Ingrese un id";
    } elseif (!isset($_REQUEST['nombre'])) {
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
    } elseif (isset($_REQUEST['password']) && $_REQUEST['password'] != "" && (strlen($_REQUEST['password']) < 5 || strlen($_REQUEST['password']) > 20)) {
        $valido = false;
        $mensajeError = "El password debe tener entre 5 y 20 caracteres";
    } elseif (!isset($_REQUEST['email'])) {
        $valido = false;
        $mensajeError = "Ingrese un email";
    } elseif (!filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
        $valido = false;
        $mensajeError = "El email debe ser valido";
    }

    if ($valido) {
        $id = $_REQUEST['id'];
        $nombre = $_REQUEST['nombre'];
        $apellido = $_REQUEST['apellido'];
        $sexo = $_REQUEST['sexo'];
        $usuario = $_REQUEST['usuario'];
        $password = $_REQUEST['password'];
        $email = $_REQUEST['email'];
        $rol = $_REQUEST['rol'];
        $estado = $_REQUEST['estado'];

        if (existeUsuario($usuario, $id)) {
            $valido = false;
            $mensajeError = "El nombre de usuario ingresado ya existe";
        } else {
            
            $idUsuario = actualizarUsuario($id, $nombre, $apellido, $email, $usuario, $sexo, $rol, $estado);
            
            if (isset($_REQUEST["rutaImagen"])) {
                $rutaImagen = $_REQUEST["rutaImagen"];
                actualizarAvatar($id, $rutaImagen);
            }

            if (isset($password)) {
                $pass_hash=password_hash($password,PASSWORD_DEFAULT);
                actualizarPassword($id, $pass_hash);
            }
        }
    }

    if ($valido) {
        $respuesta=['correcto'=>true, 'usuario'=> $id];
        echo json_encode($respuesta);
    } else {
        $respuesta=['correcto'=>false, 'mensaje'=>$mensajeError];
        echo json_encode($respuesta);
    }


/**
 * FUNCIONES
 */

    function existeUsuario($usuario, $id_usuario)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stm=$conexion->prepare("SELECT * FROM usuario WHERE usuario=:usuario AND id != :id_usuario AND estado=1");
        $stm->bindParam('usuario', $usuario);
        $stm->bindParam('id_usuario', $id_usuario);
        $stm->execute();
        $contador=$stm->rowCount();
        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }

    function actualizarUsuario($id_usuario, $nombre, $apellido, $email, $usuario, $sexo, $rol, $estado)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("UPDATE usuario SET nombre = :nombre , apellido = :apellido, email = :email , usuario = :usuario, sexo = :sexo, id_rol = :id_rol , estado = :estado WHERE id = :id_usuario");
        $statement->bindparam("nombre", $nombre);
        $statement->bindparam("apellido", $apellido);
        $statement->bindparam("email", $email);
        $statement->bindparam("usuario", $usuario);
        $statement->bindparam("sexo", $sexo);
        $statement->bindparam("id_rol", $rol);
        $statement->bindparam("estado", $estado);
        $statement->bindparam("id_usuario", $id_usuario);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function actualizarPassword($id_usuario, $password)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("UPDATE usuario SET password = :password WHERE id = :id_usuario");
        $statement->bindparam("password", $password);
        $statement->bindparam("id_usuario", $id_usuario);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function actualizarAvatar($id_usuario, $ruta_imagen)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("UPDATE avatar SET ruta_imagen = :ruta_imagen WHERE id_usuario = :id_usuario ");
        $statement->bindparam("id_usuario", $id_usuario);
        $statement->bindparam("ruta_imagen", $ruta_imagen);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }
