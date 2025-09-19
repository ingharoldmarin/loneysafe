<?php
include 'conexion-BD.php';

$conexion = conectar();


$usuario_id = 1; 


$stmt = $conexion->prepare("SELECT id, nombre FROM dispositivos WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

$dispositivos = [];
while ($row = $resultado->fetch_assoc()) {
    $dispositivos[] = $row;
}
?>

<select name="dispositivo" id="dispositivo-select">
  <option value="">Selecciona un dispositivo</option>
  <?php foreach($dispositivos as $disp): ?>
    <option value="<?= htmlspecialchars($disp['id']) ?>">
      <?= htmlspecialchars($disp['nombre']) ?>
    </option>
  <?php endforeach; ?>
</select>
