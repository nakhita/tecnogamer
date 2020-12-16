<?php
    include("../conexion_bd.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    $reportes = obtenerReportes();
    $respuesta=['correcto'=>true, 'reportes'=>$reportes];
    echo json_encode($respuesta);

    function obtenerReportes()
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT DISTINCT r.id_publicacion, p.fecha, p.titulo, u.id AS id_autor, u.usuario FROM reporte r INNER JOIN publicacion p ON r.id_publicacion = p.id INNER JOIN usuario u ON p.id_usuario = u.id  WHERE r.estado = 1");
        $stm->execute();

        $reportes = $stm->fetchAll(PDO::FETCH_ASSOC);

        $conexion = null;

        return $reportes;
    }
