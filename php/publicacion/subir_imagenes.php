<?php
  
$imageid = uniqid();

if (0 < $_FILES['file']['error']) {
    echo 'Error: ' . $_FILES['file']['error'] . '<br>';
} else {
    $path_parts = pathinfo($_FILES['file']['name']);
    $ruta = 'imagenes/' . $imageid . '.' . $path_parts['extension'];
    move_uploaded_file($_FILES['file']['tmp_name'], '../../' . $ruta);
    echo $ruta;
}
