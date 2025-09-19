<?php
session_start();
include 'conexion-BD.php';
$conexion = conectar();

if (!isset($_SESSION['ID'])) {
    die("No has iniciado sesión.");
}

if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] === UPLOAD_ERR_OK) {
    $fotoTmp = $_FILES['nueva_foto']['tmp_name'];
    $nombreFoto = time() . "_" . basename($_FILES['nueva_foto']['name']);
    $rutaDestino = "uploads/" . $nombreFoto;

    // Crear carpeta si no existe
    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    // Mover la imagen a la carpeta "uploads"
    if (move_uploaded_file($fotoTmp, $rutaDestino)) {
        // Actualizar la ruta en la base de datos
        $stmt = $conexion->prepare("UPDATE users SET images = ? WHERE ID = ?");
        $stmt->bind_param("si", $rutaDestino, $_SESSION['ID']);
        if ($stmt->execute()) {
            header("Location: perfil.php"); // redirigir de nuevo al perfil
            exit;
        } else {
            echo "Error al actualizar la base de datos: " . $stmt->error;
        }
    } else {
        echo "Error al mover la foto.";
    }
} else {
    echo "No se recibió ninguna imagen o hubo un error al subirla.";
}
?>
