<?php
    include("../conexion_bd.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    $usuarios = obtenerUsuarios();
    $respuesta=['correcto'=>true, 'usuarios'=>$usuarios];
    echo json_encode($respuesta);

    function obtenerUsuarios()
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT u.id, u.nombre, u.apellido, u.email, u.usuario, u.sexo, u.id_rol, r.nombre AS rol_usuario, u.estado FROM usuario u INNER JOIN rol r ON u.id_rol = r.id ");
        $stm->execute();

        $reportes = $stm->fetchAll(PDO::FETCH_ASSOC);

        $conexion = null;

        return $reportes;
    }
