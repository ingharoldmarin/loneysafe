<?php
session_start();
include 'conexion-BD.php';
$conexion = conectar();

// Consulta para obtener los movimientos de entradas y salidas
$query = "SELECT * FROM movimientos ORDER BY fecha DESC LIMIT 50";
$resultados = mysqli_query($conexion, $query) or die("Error en la consulta: " . mysqli_error($conexion));

// Obtener estadísticas
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN tipo = 'entrada' THEN 1 ELSE 0 END) as total_entradas,
    SUM(CASE WHEN tipo = 'salida' THEN 1 ELSE 0 END) as total_salidas,
    MAX(fecha) as ultimo_registro
    FROM movimientos";
$stats_result = mysqli_query($conexion, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entradas y Salidas</title>
  <link rel="stylesheet" href="entradasalida.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

  <header class="histo">
    <h1>REPORTE DE ENTRADAS Y SALIDAS</h1>
  </header>

  <div class="sidebar">
    <a href="cuenta2.php" class="back-arrow">←</a>

    <div class="sidebar-links">
      <div class="footer-links">
        <a href="#" id="cerrarSesionBtn">Cerrar sesión</a>
        <a href="#">Configuración</a>
        <a href="#">Ayuda</a>
      </div>
    </div>
  </div>

  <script>
document.getElementById('cerrarSesionBtn').addEventListener('click', function(event) {
  event.preventDefault();

  const modal = document.createElement('div');
  modal.classList.add('custom-modal');
  modal.innerHTML = `
    <div class="custom-modal-content">
      <h3>¿Cerrar sesión?</h3>
      <p>¿Seguro que deseas salir de tu cuenta?</p>
      <div class="modal-buttons">
        <button id="confirmLogout">Confirmar</button>
        <button id="cancelLogout">Cancelar</button>
      </div>
    </div>
  `;

  document.body.appendChild(modal);

  document.getElementById('confirmLogout').addEventListener('click', () => {
    window.location.href = "logout.php";
  });

  document.getElementById('cancelLogout').addEventListener('click', () => {
    modal.remove();
  });
});
</script>

  <div class="glass-box">
    <!-- Tarjetas de resumen -->
    <div class="resumen-cards">
      <div class="resumen-card total">
        <i class="fas fa-exchange-alt"></i>
        <div class="resumen-info">
          <h3>Total de Registros</h3>
          <p><?php echo $stats['total']; ?></p>
        </div>
      </div>
      <div class="resumen-card entradas">
        <i class="fas fa-sign-in-alt"></i>
        <div class="resumen-info">
          <h3>Entradas</h3>
          <p><?php echo $stats['total_entradas']; ?></p>
        </div>
      </div>
      <div class="resumen-card salidas">
        <i class="fas fa-sign-out-alt"></i>
        <div class="resumen-info">
          <h3>Salidas</h3>
          <p><?php echo $stats['total_salidas']; ?></p>
        </div>
      </div>
      <div class="resumen-card ultimo">
        <i class="far fa-clock"></i>
        <div class="resumen-info">
          <h3>Último Registro</h3>
          <p><?php echo $stats['ultimo_registro'] ? date('d/m/Y H:i', strtotime($stats['ultimo_registro'])) : 'N/A'; ?></p>
        </div>
      </div>
    </div>

    <!-- Filtros -->
    <div class="filtros">
      <button class="filtro-btn active" data-filtro="todos">Todos</button>
      <button class="filtro-btn" data-filtro="entrada">Entradas</button>
      <button class="filtro-btn" data-filtro="salida">Salidas</button>
      <div class="fecha-filtro">
        <label for="fecha">Filtrar por fecha:</label>
        <input type="date" id="fecha-filtro">
      </div>
    </div>

    <!-- Tabla de movimientos -->
    <div class="tabla-contenedor">
      <table class="tabla-movimientos">
        <thead>
          <tr>
            <th>Fecha y Hora</th>
            <th>Tipo</th>
            <th>Origen</th>
            <th>Detalles</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($resultados) > 0): ?>
            <?php while ($movimiento = mysqli_fetch_assoc($resultados)): 
              $icono = $movimiento['tipo'] === 'entrada' ? '⬆️' : '⬇️';
              $clase_tipo = $movimiento['tipo'];
              $fecha_formateada = date('d/m/Y H:i:s', strtotime($movimiento['fecha']));
            ?>
              <tr class="movimiento-fila <?php echo $clase_tipo; ?>" data-fecha="<?php echo date('Y-m-d', strtotime($movimiento['fecha'])); ?>">
                <td><?php echo $fecha_formateada; ?></td>
                <td><span class="tipo-movimiento <?php echo $clase_tipo; ?>"><?php echo $icono . ' ' . ucfirst($clase_tipo); ?></span></td>
                <td><?php echo htmlspecialchars($movimiento['origen']); ?></td>
                <td><?php echo isset($movimiento['detalles']) ? htmlspecialchars($movimiento['detalles']) : '-'; ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="sin-datos">No hay registros de movimientos disponibles.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
document.addEventListener("DOMContentLoaded", () => {
  // Manejo de transiciones de enlaces
  document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href && !href.startsWith('#') && !href.startsWith('javascript')) {
        e.preventDefault();
        document.body.classList.add('fade-out');
        setTimeout(() => {
          window.location.href = href;
        }, 600); 
      }
    });
  });

  // Filtrado por tipo de movimiento
  const botonesFiltro = document.querySelectorAll('.filtro-btn');
  const filasMovimientos = document.querySelectorAll('.movimiento-fila');
  const fechaFiltro = document.getElementById('fecha-filtro');

  function aplicarFiltros() {
    const filtroTipo = document.querySelector('.filtro-btn.active').dataset.filtro;
    const fechaSeleccionada = fechaFiltro.value;

    filasMovimientos.forEach(fila => {
      const tipoMovimiento = fila.classList.contains('entrada') ? 'entrada' : 'salida';
      const fechaMovimiento = fila.dataset.fecha;
      
      const coincideTipo = (filtroTipo === 'todos' || tipoMovimiento === filtroTipo);
      const coincideFecha = !fechaSeleccionada || fechaMovimiento === fechaSeleccionada;

      if (coincideTipo && coincideFecha) {
        fila.style.display = '';
      } else {
        fila.style.display = 'none';
      }
    });
  }

  // Eventos para los botones de filtro
  botonesFiltro.forEach(boton => {
    boton.addEventListener('click', () => {
      botonesFiltro.forEach(btn => btn.classList.remove('active'));
      boton.classList.add('active');
      aplicarFiltros();
    });
  });

  // Evento para el filtro de fecha
  fechaFiltro.addEventListener('change', aplicarFiltros);

  // Limpiar filtro de fecha al hacer clic en el botón de filtro activo
  document.querySelectorAll('.filtro-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      if (this.dataset.filtro !== 'fecha') {
        fechaFiltro.value = '';
      }
    });
  });
});
</script>

  <script>
  window.addEventListener("load", () => {
    document.body.classList.add("loaded");
  });
</script>

</body>
</html>
<?php
    
?>