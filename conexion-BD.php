<?php

function conectar()
{
$conexion = mysqli_connect("Localhost", "root", "","esp32") or 
die ("problemas de conexion");
    
return $conexion;
}
?>
