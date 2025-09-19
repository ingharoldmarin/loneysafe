<?php
session_start();
include 'conexion-BD.php';

$conexion = conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $resultados = mysqli_query($conexion, "SELECT * FROM users WHERE Email = '$email' AND Password = '$password'")
        or die("Problemas en el select: " . mysqli_error($conexion));

    if (mysqli_num_rows($resultados) > 0) {
        $fila = mysqli_fetch_assoc($resultados);
        $_SESSION['ID'] = $fila['ID'];
        header("Location: cuenta2.php");
        exit();
    } else {
        header("Location: SingIn2.php?error=" . urlencode("Correo o contraseña incorrectos."));
        exit();
    }
}
mysqli_close($conexion);
?>
