<?php

    include("../conexion_bd.php");
    include("../obtener_usuario.php");

    $valido = true;
    $id= obtenerUsuario();
    $nombre = $_REQUEST['nombre'];
    $apellido = $_REQUEST['apellido'];
    $sexo = $_REQUEST['sexo'];
    $usuario = $_REQUEST['usuario'];
    $password = $_REQUEST['password'];
    $email = $_REQUEST['email'];
    $rutaImagen = $_REQUEST['rutaImagen'];

    if (isset($nombre)) {
      if($nombre!=""){
        $pattern = "/^[a-zA-Z ]+$/";
        if (!preg_match($pattern, $nombre)) {
            $valido = false;
            $mensajeError = "El nombre debe ser alfabetico";
        }
      } 
    } else {
        $valido = false;
        $mensajeError = "Ingrese un nombre";
    }

    if (isset($apellido)) {
      if($apellido!=""){
        $pattern = "/^[a-zA-Z ]+$/";
        if (!preg_match($pattern, $apellido)) {
            $valido = false;
            $mensajeError = "El apellido debe ser alfabetico";
        }
      }
    } else {
        $valido = false;
        $mensajeError = "Ingrese un apellido";
    }

    if (isset($sexo)) {
      if($sexo!=""){
        if ($sexo != "M" && $sexo != "F") {
            $valido = false;
            $mensajeError = "El valor sexo debe ser Masculino o Femenino";
        }
      }
    } else {
        $valido = false;
        $mensajeError = "Ingrese un valor para sexo";
    }


    if (isset($usuario)) {
      if($usuario!=""){
        $pattern = "/^[a-zA-Z0-9]+$/";
        if (!preg_match($pattern, $usuario)) {
            $valido = false;
            $mensajeError = "El usuario debe ser alfanumerico sin espacios";
        } elseif (strlen($usuario) < 5 || strlen($usuario) > 20) {
            $valido = false;
            $mensajeError = "El usuario debe tener entre 5 y 20 caracteres";
        }
        else if (existeUsuario($usuario)) {
            $valido = false;
            $mensajeError = "El usuario ingresado no ya existe";
        } 
      }
        
    } else {
        $valido = false;
        $mensajeError = "Ingrese un usuario";
    }

    if (isset($password)) {
      if($password!=""){
        if (strlen($password) < 5 || strlen($password) > 20) {
            $valido = false;
            $mensajeError = "El password debe tener entre 5 y 20 caracteres";
        }
      }
        
    } else {
        $valido = false;
        $mensajeError = "Ingrese un password";
    }

    if (isset($email)) {
        if($email!=""){
          if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $valido = false;
            $mensajeError = "El email debe ser valido";
          }
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
          if($nombre!=""){
            $query="UPDATE usuario SET nombre=:nombre WHERE id=:id";
            $variable="nombre";
            actualizarUsuario($query,$variable,$nombre,$id);  
          }
          if($apellido!=""){
            $query="UPDATE usuario SET apellido=:apellido WHERE id=:id";
            $variable="apellido";
            actualizarUsuario($query,$variable,$apellido,$id);  
          }
          if($email!=""){
            $query="UPDATE usuario SET email=:email WHERE id=:id";
            $variable="email";
            actualizarUsuario($query,$variable,$email,$id);  
          }
          if($usuario!=""){
            $query="UPDATE usuario SET usuario=:usuario WHERE id=:id";
            $variable="usuario";
            actualizarUsuario($query,$variable,$usuario,$id);  
          }
          if($password!=""){
            $pass_hash=password_hash($password,PASSWORD_DEFAULT);
            $query="UPDATE usuario SET password=:password WHERE id=:id";
            $variable="password";
            actualizarUsuario($query,$variable,$pass_hash,$id);  
          }
          if($sexo!=""){
            $query="UPDATE usuario SET sexo=:sexo WHERE id=:id";
            $variable="sexo";
            actualizarUsuario($query,$variable,$sexo,$id);  
          }
          if($rutaImagen!=""){
              insertarImagen($id,$rutaImagen);
          }
        }


    if ($valido) {
        $respuesta=['correcto'=>true, 'id'=> $id];
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
    function actualizarUsuario($query, $variable,$contenido,$id){
        
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $statement=$conexion->prepare($query);
        $statement->bindparam($variable,$contenido);
        $statement->bindparam("id", $id);
        $statement->execute();

        $conexion=null;
    }

    function insertarImagen($id_usuario, $ruta_imagen)
    {
        $id= obtenerUsuario();
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $statement=$conexion->prepare("UPDATE avatar SET ruta_imagen=:ruta_imagen WHERE id_usuario=:id_usuario");
        $statement->bindparam("id_usuario", $id);
        $statement->bindparam("ruta_imagen", $ruta_imagen);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }
