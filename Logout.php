<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cerrando sesión</title>
  <script>
    alert("Has cerrado sesión correctamente.");
    window.location.href = "SingIn2.php";
  </script>
</head>
<body>
</body>
</html>
