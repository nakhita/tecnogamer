<?php
    include("../conexion_bd.php");
    header("Content-Type: application/json; charset=UTF-8");

    if (isset($_REQUEST["id"])) {
        $id = $_REQUEST["id"];
    }

    $conexion = obtenerConexion();

    $query = "SELECT DISTINCT p.id, p.titulo, p.descripcion, i.ruta_imagen, u.id as idAutor, u.usuario FROM publicacion p LEFT JOIN imagen i ON p.id = i.id_publicacion INNER JOIN usuario u ON p.id_usuario = u.id WHERE p.id = :id AND p.estado = 1 AND u.estado = 1 ";

    $statement=$conexion->prepare($query);
    $statement->bindparam("id", $id);

    $statement->execute();
    if (!$statement) {
        echo 'Error al ejecutar la consulta';
    } else {
        $publicaciones = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($publicaciones as &$publicacion) {
            $publicacion["etiquetas"] =  obtenerEtiquetasPorPublicacion($id);
            $publicacion["megusta"] = obtenerCantidadMegusta($id);
        }

        echo json_encode($publicaciones);
    }
    $conexion = null;


    function obtenerEtiquetasPorPublicacion($id_publicacion)
    {
        $conexion = obtenerConexion();

        $query = "SELECT e.id, e.nombre FROM etiqueta e INNER JOIN publicacion_etiqueta pe ON e.id = pe.id_etiqueta WHERE pe.id_publicacion = :id_publicacion";

        $statement=$conexion->prepare($query);
        $statement->bindparam("id_publicacion", $id_publicacion);

        $statement->execute();

        $etiquetas = $statement->fetchAll(PDO::FETCH_ASSOC);

        $conexion = null;

        return $etiquetas;
    }

    function obtenerCantidadMegusta($id_publicacion)
    {
        $conexion = obtenerConexion();

        $query = "SELECT COUNT(mg.id) FROM megusta mg WHERE mg.id_publicacion = :id_publicacion";

        $statement=$conexion->prepare($query);
        $statement->bindparam("id_publicacion", $id_publicacion);

        $statement->execute();

        $contador = $statement->fetchColumn();

        $conexion = null;

        return $contador;
    }
