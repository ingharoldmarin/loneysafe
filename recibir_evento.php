<?php
// Configuración BD
$servername = "localhost";
$username   = "esp32";
$password   = "12345678";
$dbname     = "esp32";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Error conexión: " . $conn->connect_error);
}

$claveCorrecta = "1235"; // misma clave que ESP32 teclado

if (isset($_POST['evento'])) {
    $evento = $conn->real_escape_string($_POST['evento']);
    $fecha  = date("Y-m-d H:i:s");

    // Caso: Apertura por clave o botón interno (ESP con teclado)
    if ($evento === "apertura" || $evento === "apertura_interna") {
        $sql1 = "INSERT INTO eventos (evento, fecha) VALUES ('$evento', '$fecha')";
        $conn->query($sql1);

        $sql2 = "INSERT INTO aperturas (fecha, origen) VALUES ('$fecha','ESP32_TECLADO')";
        $conn->query($sql2);

        echo "✅ Apertura registrada";
    }
    // Caso: Bloqueo de sistema (clave errónea 3 veces)
    elseif ($evento === "bloqueo") {
        $sql = "INSERT INTO eventos (evento, fecha) VALUES ('Bloqueo por clave', '$fecha')";
        $conn->query($sql);
        echo "⚠ Sistema bloqueado por intentos fallidos";
    }
    // Caso: Movimiento detectado por sensores ultrasónicos
    elseif ($evento === "Alguien entro") {
        $sql1 = "INSERT INTO eventos (evento, fecha) VALUES ('Entrada detectada', '$fecha')";
        $conn->query($sql1);

        $sql2 = "INSERT INTO movimientos (tipo, fecha, origen) VALUES ('entrada','$fecha','ESP32_SENSORES')";
        $conn->query($sql2);

        echo "👤 Entrada detectada";
    }
    elseif ($evento === "Alguien salio") {
        $sql1 = "INSERT INTO eventos (evento, fecha) VALUES ('Salida detectada', '$fecha')";
        $conn->query($sql1);

        $sql2 = "INSERT INTO movimientos (tipo, fecha, origen) VALUES ('salida','$fecha','ESP32_SENSORES')";
        $conn->query($sql2);

        echo "🚶 Salida detectada";
    }
    // Otros eventos genéricos
    else {
        $sql = "INSERT INTO eventos (evento, fecha) VALUES ('$evento', '$fecha')";
        $conn->query($sql);
        echo "ℹ Evento genérico registrado";
    }

} else {
    echo "❌ No se recibió ningún evento.";
}

$conn->close();
?>
