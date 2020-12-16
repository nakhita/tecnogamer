<?php
    include("../conexion_bd.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    $solicitudes = obtenerSolicitudesCategoria();

    foreach ($solicitudes as &$solicitud) {
        $solicitud["etiquetas"] = obtenerSolicitudesCategoriaEtiqueta($solicitud["id"]);
    }

    $respuesta=['correcto'=>true, 'solicitudes'=>$solicitudes];
    echo json_encode($respuesta);

    function obtenerSolicitudesCategoria()
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT sc.id, sc.fecha, u.id as id_usuario, u.usuario, sc.categoria, sc.comentario FROM solicitud_categoria sc INNER JOIN usuario u ON sc.id_usuario = u.id WHERE sc.estado = 1 AND u.estado = 1 ");
        $stm->execute();

        $solicitudes = $stm->fetchAll(PDO::FETCH_ASSOC);

        $conexion = null;

        return $solicitudes;
    }

    function obtenerSolicitudesCategoriaEtiqueta($idSolicitud)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT sce.etiqueta FROM solicitud_categoria_etiqueta sce WHERE sce.id_solicitud_categoria = :id_solicitud_categoria");
        $stm->bindparam("id_solicitud_categoria", $idSolicitud);
        $stm->execute();

        $etiquetas = $stm->fetchAll(PDO::FETCH_ASSOC);

        $conexion = null;

        return $etiquetas;
    }
