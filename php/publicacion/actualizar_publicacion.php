<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
 
    header("Content-Type: application/json; charset=UTF-8");
 
    $idUsuario = obtenerUsuario();
    $valido = true;

    if (!isset($_REQUEST["idPublicacion"])) {
        $valido = false;
        $mensajeError = "La id de la publicacion es requerida para actualizar";
    } else {
        $idPublicacion = $_REQUEST["idPublicacion"];
    }

    if (!isset($_REQUEST["nombre"])) {
        $valido = false;
        $mensajeError = "El nombre es requerido";
    } else {
        $titulo = $_REQUEST["nombre"];
    }

    if (!isset($_REQUEST["etiquetas"])) {
        $valido = false;
        $mensajeError = "Al menos una etiqueta es requerida";
    } else {
        $etiquetas = $_REQUEST["etiquetas"];
    }

    if (!isset($_REQUEST["descripcion"])) {
        $valido = false;
        $mensajeError = "La descripcion es requerida";
    } else {
        $descripcion = $_REQUEST["descripcion"];
    }

    if ($valido) {
        if (validarAutorPublicacion($idPublicacion, $idUsuario) == 0) {
            $valido = false;
            $mensajeError = "Solo el autor puede actualizar sus publicaciones";
        }
    }

    if ($valido) {
        $actualizados = actualizarPublicacion($titulo, $descripcion, $idPublicacion);

        eliminarPublicacionEtiqueta($idPublicacion);
        foreach ($etiquetas as $etiqueta) {
            insertarPublicacionEtiqueta($idPublicacion, $etiqueta);
        }
    
        if (isset($_REQUEST["rutaImagen"])) {
            $rutaImagen = $_REQUEST["rutaImagen"];
            actualizarImagen($idPublicacion, $rutaImagen);
        }
    
        $respuesta=['correcto'=>true, 'actualizados'=> $actualizados];
        echo json_encode($respuesta);
    } else {
        $respuesta=['correcto'=>false, 'mensajeError'=> $mensajeError];
        echo json_encode($respuesta);
    }
    


    function validarAutorPublicacion($idPublicacion, $idUsuario)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("SELECT * FROM publicacion WHERE id=:idPublicacion AND id_usuario=:idUsuario");
        $statement->bindparam("idPublicacion", $idPublicacion);
        $statement->bindparam("idUsuario", $idUsuario);
        $statement->execute();
        $insertId=$statement->rowCount();

        $conexion=null;

        return $insertId;
    }

    function actualizarPublicacion($titulo, $descripcion, $idPublicacion)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("UPDATE publicacion SET titulo=:titulo, descripcion=:descripcion WHERE id=:idPublicacion");
        $statement->bindparam("titulo", $titulo);
        $statement->bindparam("descripcion", $descripcion);
        $statement->bindparam("idPublicacion", $idPublicacion);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function insertarPublicacionEtiqueta($id_publicacion, $id_etiqueta)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("INSERT INTO publicacion_etiqueta (id_publicacion, id_etiqueta)  values (:id_publicacion,:id_etiqueta)");
        $statement->bindparam("id_publicacion", $id_publicacion);
        $statement->bindparam("id_etiqueta", $id_etiqueta);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function actualizarImagen($id_publicacion, $ruta_imagen)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("UPDATE imagen SET ruta_imagen=:ruta_imagen WHERE id_publicacion=:id_publicacion");
        $statement->bindparam("id_publicacion", $id_publicacion);
        $statement->bindparam("ruta_imagen", $ruta_imagen);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function eliminarPublicacionEtiqueta($idPublicacion)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("DELETE FROM publicacion_etiqueta WHERE id_publicacion = :id");
        $statement->bindparam("id", $idPublicacion);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }
