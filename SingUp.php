<?php
session_start();
include 'conexion-BD.php';

$conexion = conectar();

$ruta_destino = "uploads/";
$imagen_predeterminada = $ruta_destino . "default.jpg";

if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
    $nombre_archivo = basename($_FILES["imagen"]["name"]);
    $ruta_temporal = $_FILES["imagen"]["tmp_name"];
    $ruta_completa = $ruta_destino . $nombre_archivo;

    if (move_uploaded_file($ruta_temporal, $ruta_completa)) {
        $imagenFinal = $ruta_completa;
    } else {
        $imagenFinal = $imagen_predeterminada;
    }
} else {
    $imagenFinal = $imagen_predeterminada;
}

$sql = "INSERT INTO users (Name, Email, Password, ID_Roles, images) 
        VALUES ('{$_POST['Nombre']}', '{$_POST['email']}', '{$_POST['password']}', '1', '$imagenFinal')";

if (mysqli_query($conexion, $sql)) {
    // Obtener el ID del nuevo usuario
    $user_id = mysqli_insert_id($conexion);

    // Crear la sesión para este usuario
    $_SESSION['ID'] = $user_id;
    $_SESSION['Email'] = $_POST['email'];
    $_SESSION['Name'] = $_POST['Nombre'];

    // Redirigir a cuenta2.php
    header("Location: cuenta2.php");
    exit();
} else {
    echo "Error en la base de datos: " . mysqli_error($conexion);
}

mysqli_close($conexion);
?>
