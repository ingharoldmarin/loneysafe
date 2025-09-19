<?php
session_start();
include 'conexion-BD.php';
$conexion = conectar();

// Verificar si el usuario tiene permisos
if (!isset($_SESSION['ID'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Acceso denegado");
}

// Validar y sanitizar el ID del dispositivo
$dispositivo_id = filter_input(INPUT_POST, 'dispositivo_id', FILTER_VALIDATE_INT);

if (!$dispositivo_id) {
    header("HTTP/1.1 400 Bad Request");
    exit("ID de dispositivo no válido");
}

// Verificar que el dispositivo pertenece al usuario actual
$query = "SELECT d.ID 
          FROM dispositivos d
          INNER JOIN accesos a ON d.ID = a.ID_Dispositivos
          WHERE d.ID = ? AND a.ID_users = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "ii", $dispositivo_id, $_SESSION['ID']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    // Primero eliminamos de la tabla accesos por las restricciones de clave foránea
    $delete_accesos = "DELETE FROM accesos WHERE ID_Dispositivos = ?";
    $stmt1 = mysqli_prepare($conexion, $delete_accesos);
    mysqli_stmt_bind_param($stmt1, "i", $dispositivo_id);
    mysqli_stmt_execute($stmt1);
    
    // Luego eliminamos el dispositivo
    $delete_dispositivo = "DELETE FROM dispositivos WHERE ID = ?";
    $stmt2 = mysqli_prepare($conexion, $delete_dispositivo);
    mysqli_stmt_bind_param($stmt2, "i", $dispositivo_id);
    
    if (mysqli_stmt_execute($stmt2)) {
        echo json_encode(['success' => true, 'message' => 'Dispositivo eliminado correctamente']);
    } else {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el dispositivo']);
    }
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['success' => false, 'message' => 'Dispositivo no encontrado o no tienes permisos']);
}

mysqli_close($conexion);
?>