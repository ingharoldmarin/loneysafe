<?php
session_start();
include 'conexion-BD.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['ID'])) {
    header("Location: login.php");
    exit();
}

$conexion = conectar();

// Validar datos de entrada (protección básica contra SQL injection)
$ip = mysqli_real_escape_string($conexion, $_POST['Ip']);
$nombre = mysqli_real_escape_string($conexion, $_POST['name']);

// Insertar en la tabla dispositivos
$query_dispositivo = "INSERT INTO dispositivos (IP, Nombre) VALUES ('$ip', '$nombre')";
if (!mysqli_query($conexion, $query_dispositivo)) {
    die("Error al insertar dispositivo: " . mysqli_error($conexion));
}

// Obtener el ID del dispositivo recién insertado
$dispositivo_id = mysqli_insert_id($conexion);

// Insertar en la tabla accesos (asignando el dispositivo al usuario actual)
$query_acceso = "INSERT INTO accesos (ID_users, ID_Dispositivos, ID_Estados) 
                 VALUES ('".$_SESSION['ID']."', '$dispositivo_id', 1)"; // Estado 1 por defecto

if (!mysqli_query($conexion, $query_acceso)) {
    die("Error al crear acceso: " . mysqli_error($conexion));
}

// Redireccionar con mensaje de éxito
$_SESSION['mensaje'] = "Dispositivo añadido correctamente";
header("Location: cuenta2.php");
exit();
?>