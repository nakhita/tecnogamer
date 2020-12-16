<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
    $id =obtenerUsuario();

    $conexion = obtenerConexion();

    $query = "SELECT tiene_experiencia as experiencia, motivo, fecha FROM solicitud_creador_contenido WHERE id_usuario = :id AND estado = 1 ";

    $statement=$conexion->prepare($query);
    $statement->bindparam("id", $id);

    $statement->execute();
    if (!$statement) {
        echo 'Error al ejecutar la consulta';
    } else {
        $solicitud = $statement->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($solicitud);
    }
    $conexion = null;
?>
