<?php
    include("../conexion_bd.php");
    include("../obtener_usuario.php");
  
    header("Content-Type: application/json; charset=UTF-8");

    $valido = true;

    $idUsuario = obtenerUsuario();

    if ($idUsuario == 0 || $idUsuario == '') {
        $valido = false;
        $mensajeError = "El usuario no se encuentra logueado";
    }

    if ($valido) {
        $notificaciones = obtenerNotificaciones($idUsuario);
        $respuesta=['correcto'=>true, 'notificaciones'=>$notificaciones];
        echo json_encode($respuesta);
    } else {
        $respuesta=['correcto'=>false, 'notificaciones'=>$mensajeError];
        echo json_encode($respuesta);
    }

    function obtenerNotificaciones($idUsuario)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT n.id, u.usuario, n.id_publicacion, p.titulo, n.visto, n.fecha FROM notificacion n INNER JOIN publicacion p ON n.id_publicacion = p.id INNER JOIN usuario u ON p.id_usuario = u.id INNER JOIN suscripcion s ON n.id_suscripcion = s.id WHERE n.estado = 1 AND n.visto = 0 AND s.estado = 1 AND s.id_usuario = :id_usuario ");
        $stm->bindParam("id_usuario", $idUsuario);
        $stm->execute();

        $notificaciones = $stm->fetchAll(PDO::FETCH_ASSOC);

        $conexion = null;

        return $notificaciones;
    }
