
<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
 
    header("Content-Type: application/json; charset=UTF-8");
 
    $idUsuario = obtenerUsuario();
    $titulo = $_POST["nombre"];
    $etiquetas = $_POST["etiquetas"];
    $descripcion = $_POST["descripcion"];

    $idPublicacion = insertarPublicacion($titulo, $descripcion, $idUsuario);

    foreach ($etiquetas as $etiqueta) {
        insertarPublicacionEtiqueta($idPublicacion, $etiqueta);
    }

    if (isset($_POST["rutaImagen"])) {
        $rutaImagen = $_POST["rutaImagen"];
        insertarImagen($idPublicacion, $rutaImagen);
    }

    // al final enviamos las notificaciones
    insertarNotificaciones($idPublicacion, $idUsuario);

    $respuesta=['correcto'=>true, 'publicacion'=> $idPublicacion];
    echo json_encode($respuesta);

    function insertarPublicacion($titulo, $descripcion, $id_usuario)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("INSERT INTO publicacion (titulo, descripcion, estado, id_usuario, fecha) values (:titulo,:descripcion,1,:id_usuario,NOW())");
        $statement->bindparam("titulo", $titulo);
        $statement->bindparam("descripcion", $descripcion);
        $statement->bindparam("id_usuario", $id_usuario);
        $statement->execute();
        $insertId=$conexion->lastInsertId();

        $conexion=null;

        return $insertId;
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

    function insertarImagen($id_publicacion, $ruta_imagen)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("INSERT INTO imagen (id_publicacion, ruta_imagen) values (:id_publicacion,:ruta_imagen)");
        $statement->bindparam("id_publicacion", $id_publicacion);
        $statement->bindparam("ruta_imagen", $ruta_imagen);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }

    function insertarNotificaciones($id_publicacion, $id_autor)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("INSERT INTO notificacion (id_suscripcion, id_publicacion, visto, fecha, estado) SELECT s.id AS id_suscripcion, :id_publicacion, 0, NOW(), 1 FROM suscripcion s WHERE s.id_autor = :id_autor ");
        $statement->bindparam("id_publicacion", $id_publicacion);
        $statement->bindparam("id_autor", $id_autor);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        return $contador;
    }
