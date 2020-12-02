<?php

function obtenerConexion()
{
    return new PDO('mysql:host=localhost;dbname=tecnogamerbd', "root", "");
}
     
function conectar_con()
{
    $server = "localhost";
    $user = "root";
    $pass = "";
    $db = "proyecto";
    $con = mysqli_connect($server, $user, $pass, $db);
    
    return $con;
}

function desconectar_con($con)
{
    mysqli_close($con);
}
