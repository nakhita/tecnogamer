<?php
    include("../conexion_bd.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    if (isset($_REQUEST["titulo"])) {
        $titulo = '%'.$_REQUEST["titulo"].'%';
    } else {
        $titulo = '%%';
    }

    if (isset($_REQUEST["autor"]) && $_REQUEST["autor"] != "") {
        $autor = $_REQUEST["autor"];
        $hayAutor = true;
    } else {
        $hayAutor = false;
    }

    if (isset($_REQUEST["etiqueta"]) && $_REQUEST["etiqueta"] != "") {
        $etiqueta = $_REQUEST["etiqueta"];
        $hayEtiqueta = true;
    } else {
        $hayEtiqueta = false;
    }

    if (isset($_REQUEST["categoria"]) && $_REQUEST["categoria"] != "") {
        $categoria = $_REQUEST["categoria"];
        $hayCategoria = true;
    } else {
        $hayCategoria = false;
    }

    $conexion = obtenerConexion();

    $query = "SELECT distinct p.id, p.titulo, i.ruta_imagen, u.usuario FROM publicacion p INNER JOIN publicacion_etiqueta pe ON p.id = pe.id_publicacion INNER JOIN etiqueta e ON e.id = pe.id_etiqueta LEFT JOIN imagen i ON p.id = i.id_publicacion INNER JOIN usuario u ON p.id_usuario = u.id WHERE p.titulo LIKE :titulo ";

    if ($hayEtiqueta) {
        $query = $query . " AND pe.id_etiqueta = :id_etiqueta";
    }

    if ($hayCategoria) {
        $query = $query . " AND e.id_categoria = :id_categoria";
    }

    if ($hayAutor) {
        $query = $query . " AND u.usuario = :autor";
    }

    $statement=$conexion->prepare($query);
    $statement->bindparam("titulo", $titulo);
    
    if ($hayEtiqueta) {
        $statement->bindparam("id_etiqueta", $etiqueta);
    }

    if ($hayCategoria) {
        $statement->bindparam("id_categoria", $categoria);
    }

    if ($hayAutor) {
        $statement->bindparam("autor", $autor);
    }

    $statement->execute();
    if (!$statement) {
        echo 'Error al ejecutar la consulta';
    } else {
        $publicaciones = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($publicaciones as &$publicacion) {
            $publicacion["etiquetas"] =  obtenerEtiquetasPorPublicacion($publicacion["id"]);
        }

        echo json_encode($publicaciones);
    }
    $conexion = null;


    function obtenerEtiquetasPorPublicacion($id_publicacion)
    {
        $conexion = obtenerConexion();

        $query = "SELECT e.nombre FROM etiqueta e INNER JOIN publicacion_etiqueta pe ON e.id = pe.id_etiqueta WHERE pe.id_publicacion = :id_publicacion";

        $statement=$conexion->prepare($query);
        $statement->bindparam("id_publicacion", $id_publicacion);

        $statement->execute();

        $etiquetas = $statement->fetchAll(PDO::FETCH_COLUMN, 0);

        $conexion = null;

        return $etiquetas;
    }
