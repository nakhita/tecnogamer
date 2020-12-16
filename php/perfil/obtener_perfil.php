<?php
    include("../conexion_bd.php");
    header("Content-Type: application/json; charset=UTF-8");

    if (isset($_REQUEST["id"])) {
        $id = $_REQUEST["id"];
    }

    $conexion = obtenerConexion();

    $query = "SELECT a.ruta_imagen as avatar,u.id , u.nombre , u.apellido , u.usuario FROM usuario u LEFT JOIN avatar a ON u.id = a.id_usuario WHERE u.id=:id";

    $statement=$conexion->prepare($query);
    $statement->bindparam("id", $id);

    $statement->execute();
    if (!$statement) {
        echo 'Error al ejecutar la consulta';
    } else {
        $perfil = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($perfil);
    }
    $conexion = null;
