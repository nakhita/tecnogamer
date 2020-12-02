<?php
    include("../conexion_bd.php");
   
    header("Content-Type: application/json; charset=UTF-8");

    if (isset($_REQUEST["id"])) {
        $id = $_REQUEST["id"];
    } else {
        return;
    }

    $conexion = obtenerConexion();

    $statement=$conexion->prepare("SELECT e.id, e.nombre FROM etiqueta e WHERE e.id_categoria = :id_categoria");
    $statement->bindparam("id_categoria", $id);
    $statement->execute();
    if (!$statement) {
        echo 'Error al ejecutar la consulta';
    } else {
        $categorias = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($categorias);
    }
    $conexion = null;
