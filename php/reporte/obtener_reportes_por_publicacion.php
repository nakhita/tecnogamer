<?php
    include("../conexion_bd.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    if (!isset($_REQUEST["idPublicacion"]) || $_REQUEST["idPublicacion"] == "") {
        $valido = false;
        $mensajeError = "El id de la publicacion no ha sido ingresado";
    } else {
        $idPublicacion = $_REQUEST["idPublicacion"];
    }

    $reportes = obtenerReportes($idPublicacion);
    $respuesta=['correcto'=>true, 'reportes'=>$reportes];
    echo json_encode($respuesta);

    function obtenerReportes($id_publicacion)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT r.fecha, r.id_reportador, u.usuario, r.comentario FROM reporte r INNER JOIN usuario u ON r.id_reportador = u.id  WHERE r.id_publicacion = :id_publicacion AND r.estado = 1 ");
        $stm->bindparam("id_publicacion", $id_publicacion);
        $stm->execute();

        $reportes = $stm->fetchAll(PDO::FETCH_ASSOC);

        $conexion = null;

        return $reportes;
    }
