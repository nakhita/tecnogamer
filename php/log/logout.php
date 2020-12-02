<?php
  
    session_start();
    session_destroy();
    $respuesta=['correcto'=>true,'session'=>'false']; 
    echo json_encode($respuesta);
?>
