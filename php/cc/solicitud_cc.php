<?php 
    include("../conexion_bd.php");
    include("../obtener_usuario.php");

    $valido = true;
    $id= obtenerUsuario();
    $motivo = $_REQUEST['motivo'];
    $experiencia = $_REQUEST['experiencia'];
    if (isset($motivo)) {
      if($motivo==""){
        $valido = false;
        $mensajeError = "No hay motivo escrito";
      }
    }else{
      $valido = false;
      $mensajeError = "No hay motivo";
    }

    if (!isset($experiencia)) {
        $valido = false;
        $mensajeError = "El apellido debe ser alfabetico";
    }

    if ($valido) {
          $insercion = insertarSolicitud($experiencia, $motivo,$id);
      if(!$insercion){
        $valido = false;
        $mensajeError = "Error al insertar";
      }
    }


    if ($valido) {
        $respuesta=['correcto'=>true];
        echo json_encode($respuesta);
    } else {
        $respuesta=['correcto'=>false, 'mensaje'=>$mensajeError];
        echo json_encode($respuesta);
    }


/**
 * FUNCIONES
 */
  function insertarSolicitud($experiencia, $motivo, $id)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("INSERT INTO solicitud_creador_contenido (id_usuario, tiene_experiencia, motivo, fecha, estado) values (:id,:experiencia,:motivo,NOW(),1)");
        $statement->bindparam("id", $id);
        $statement->bindparam("experiencia", $experiencia);
        $statement->bindparam("motivo", $motivo);
        $statement->execute();
        $contador=$statement->rowCount();
    
        $conexion=null;
        if($contador>0){
          return true;
        }
        else{
          return false;
        }
    }


?>
