<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
    
    header("Content-Type: application/json; charset=UTF-8");

    $idUsuario = obtenerUsuario();

    $conexion = obtenerConexion();

    $statement=$conexion->prepare("SELECT p.id, p.nombre, p.url, p.menu_id FROM permiso p INNER JOIN rol_permiso rp ON p.id = rp.id_permiso INNER JOIN rol r ON rp.id_rol = r.id INNER JOIN usuario u ON r.id = u.id_rol WHERE u.id = :id");

    $statement->bindparam("id", $idUsuario);
    $statement->execute();
    
    if (!$statement) {
        $mensaje = ['error'=>true,'mensaje'=>'Error al ejecutar la consulta'];
        echo json_encode('Error al ejecutar la consulta');
    } else {
        
        $permisos = $statement->fetchAll(PDO::FETCH_ASSOC);
        $mensaje = ['error'=>false,'permisos'=>$permisos];
        echo json_encode($mensaje);
    }
    $conexion = null;
