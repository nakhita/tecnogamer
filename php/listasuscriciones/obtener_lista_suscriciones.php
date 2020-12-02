<?php 
  include("../conexion_bd.php");
  include("../obtener_usuario.php");
  
  header("Content-Type: application/json; charset=UTF-8");

  $conexion = obtenerConexion();
  $id= obtenerUsuario();
  $statement=$conexion->prepare("SELECT  a.ruta_imagen as ruta, u.usuario as autor,u.id as idAutor, s.id as idSus FROM suscripcion s INNER JOIN usuario u on s.id_autor = u.id INNER JOIN avatar a on u.id = a.id_usuario where s.id_usuario= :id");
  $statement->bindparam("id", $id);  
  $statement->execute();
  if (!$statement) {
      echo 'Error al ejecutar la consulta';
  } else {
      $suscriciones = $statement->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode($suscriciones);
  }
  $conexion = null;
