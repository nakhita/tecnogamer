<?php    
    function obtenerUsuario(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if(!isset($_SESSION['user'])){
             $respuesta=0;
        }
        else{
            $respuesta = $_SESSION['user']; 
        } 
        return $respuesta;    
    }
?>
