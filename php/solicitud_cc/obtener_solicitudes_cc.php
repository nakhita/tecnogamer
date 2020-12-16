<?php
    include("../conexion_bd.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    $solicitudes = obtenerSolicitudesCC();

    $respuesta=['correcto'=>true, 'solicitudes'=>$solicitudes];
    echo json_encode($respuesta);

    function obtenerSolicitudesCC()
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT scc.id, scc.fecha, u.id as id_usuario, u.usuario, scc.tiene_experiencia as experiencia, scc.motivo FROM solicitud_creador_contenido scc INNER JOIN usuario u ON scc.id_usuario = u.id WHERE scc.estado = 1 AND u.estado = 1 ");
        $stm->execute();

        $solicitudes = $stm->fetchAll(PDO::FETCH_ASSOC);

        $conexion = null;

        return $solicitudes;
    }
