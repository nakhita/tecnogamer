<?php
    include("../conexion_bd.php");

    header("Content-Type: application/json; charset=UTF-8");

    const APROBAR_Y_CERRAR = 1;
    const SOLO_CERRAR = 2;

    $valido = true;
  
    // Validaciones
    if (!isset($_REQUEST['idSolicitud']) || $_REQUEST['idSolicitud'] == "") {
        $valido = false;
        $mensajeError = "El id de la solicitud no ha sido ingresado";
    } elseif (!isset($_REQUEST['tipoCerrarSolicitud'])) {
        $valido = false;
        $mensajeError = "El tipo de cerrar solicitud no ha sido ingresado";
    } else {
        $idSolicitud = $_REQUEST['idSolicitud'];
        $tipoCerrarSolicitud = $_REQUEST['tipoCerrarSolicitud'];
        if (!validarSolicitudExistente($idSolicitud)) {
            $valido = false;
            $mensajeError = "El id de solicitud no existe";
        }
        if ($tipoCerrarSolicitud != SOLO_CERRAR && $tipoCerrarSolicitud != APROBAR_Y_CERRAR) {
            $valido = false;
            $mensajeError = "El tipo de cerrar solicitud no es valido";
        }
        // adicional validacion cuando se requiera aprobar
        if ($tipoCerrarSolicitud == APROBAR_Y_CERRAR) {
            if (validarExisteCategoria($idSolicitud)) {
                $valido = false;
                $mensajeError = "Ya existe una categoria con el mismo nombre";
            }
            if (validarExisteEtiqueta($idSolicitud)) {
                $valido = false;
                $mensajeError = "Ya existe una etiqueta con el mismo nombre de una de la solicitud";
            }
        }
    }

    // Actualizaciones
    if ($valido) {
        if ($tipoCerrarSolicitud == APROBAR_Y_CERRAR) {
            $idCategoria = insertarCategoria($idSolicitud);
            insertarEtiquetas($idSolicitud, $idCategoria);
        }

        // Cerrar reporte
        $actualizados = cerrarSolicitud($idSolicitud);
        if ($actualizados > 0) {
            $valor = "Se ha cerrado la solicitud correctamente";
        } else {
            $valor = "Ha ocurrido un error al cerrar una solicitud";
        }
        $respuesta = ['correcto' => true, 'mensaje' => $valor];
        echo json_encode($respuesta);
    } else {
        $respuesta = ['correcto' => false, 'mensaje' => $mensajeError];
        echo json_encode($respuesta);
    }

    
    function validarSolicitudExistente($idSolicitud)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("SELECT * FROM solicitud_categoria WHERE id=:idSolicitud AND estado=1");
        $stm->bindParam('idSolicitud', $idSolicitud);
        $stm->execute();
        $contador=$stm->rowCount();
        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }

    function cerrarSolicitud($idSolicitud)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm=$conexion->prepare("UPDATE solicitud_categoria SET estado = 2 WHERE id = :id_solicitud");
        $stm->bindParam('id_solicitud', $idSolicitud);
        $stm->execute();
        $contador=$stm->rowCount();

        $conexion = null;

        return $contador;
    }

    function insertarEtiquetas($idSolicitud, $idCategoria)
    {
        $conexion=obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $statement=$conexion->prepare("INSERT INTO etiqueta (nombre, id_categoria, estado) SELECT sce.etiqueta, :id_categoria, 1 FROM solicitud_categoria_etiqueta sce WHERE sce.id_solicitud_categoria = :id_solicitud");
        $statement->bindparam("id_categoria", $idCategoria);
        $statement->bindparam("id_solicitud", $idSolicitud);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion = null;

        return $contador;
    }

    function insertarCategoria($idSolicitud)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("INSERT INTO categoria (nombre) SELECT sc.categoria FROM solicitud_categoria sc WHERE sc.id = :id_solicitud");
        $statement->bindparam("id_solicitud", $idSolicitud);
        $statement->execute();
        
        $insertId=$conexion->lastInsertId();

        $conexion=null;

        return $insertId;
    }

    function validarExisteCategoria($idSolicitud)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("SELECT * FROM categoria c where c.nombre = (SELECT sc.categoria FROM solicitud_categoria sc WHERE sc.id = :id_solicitud_categoria);");
        $statement->bindparam("id_solicitud_categoria", $idSolicitud);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }

    function validarExisteEtiqueta($idSolicitud)
    {
        $conexion = obtenerConexion();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement=$conexion->prepare("SELECT * FROM etiqueta e where e.nombre IN (SELECT sce.etiqueta FROM solicitud_categoria_etiqueta sce WHERE sce.id_solicitud_categoria = :id_solicitud_categoria);");
        $statement->bindparam("id_solicitud_categoria", $idSolicitud);
        $statement->execute();
        $contador=$statement->rowCount();

        $conexion=null;

        if ($contador > 0) {
            return true;
        } else {
            return false;
        }
    }
