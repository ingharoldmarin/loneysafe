<?php
session_start();
include 'conexion-BD.php';
$conexion = conectar();

if (!isset($_SESSION['ID'])) {
    die("No has iniciado sesión.");
}

$defaultImage = "uploads/default.jpg";

$stmt = $conexion->prepare("UPDATE users SET images = ? WHERE ID = ?");
$stmt->bind_param("si", $defaultImage, $_SESSION['ID']);

if ($stmt->execute()) {
    header("Location: perfil.php");
    exit;
} else {
    echo "Error al actualizar la foto: " . $stmt->error;
}
?>
