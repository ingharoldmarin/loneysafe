<?php
session_start();
include 'conexion-BD.php';
$conexion = conectar();

// Consulta para la información de accesos
$resultados = mysqli_query($conexion, "
SELECT *
FROM accesos
INNER JOIN dispositivos ON accesos.ID_Dispositivos = dispositivos.ID
INNER JOIN users ON accesos.ID_users = users.ID
INNER JOIN estados ON accesos.ID_Estados = estados.ID
WHERE users.ID = ".$_SESSION['ID']) 
or die("Problemas en el select: " . mysqli_error($conexion));

// Consulta para obtener los movimientos (entradas/salidas)
$movimientos = mysqli_query($conexion, "
    SELECT 'Movimiento' as tipo, 
           tipo as descripcion, 
           fecha, 
           origen 
    FROM movimientos 
    ORDER BY fecha DESC
    LIMIT 30") or die("Error en consulta de movimientos: " . mysqli_error($conexion));

// Consulta para obtener los eventos
$eventos = mysqli_query($conexion, "
    SELECT 'Evento' as tipo, 
           evento as descripcion, 
           fecha, 
           'Sistema' as origen 
    FROM eventos 
    WHERE evento IN ('Entrada detectada', 'Salida detectada', 'Bloqueo por clave')
    ORDER BY fecha DESC
    LIMIT 30") or die("Error en consulta de eventos: " . mysqli_error($conexion));

// Consulta para obtener las aperturas
$aperturas = mysqli_query($conexion, "
    SELECT 'Apertura' as tipo, 
           'Apertura de puerta' as descripcion, 
           fecha, 
           origen 
    FROM aperturas 
    ORDER BY fecha DESC
    LIMIT 20") or die("Error en consulta de aperturas: " . mysqli_error($conexion));

// Combinar todos los resultados
$todos_eventos = [];

// Función para agregar resultados al array
function agregarResultados($query_result, &$array) {
    if ($query_result && mysqli_num_rows($query_result) > 0) {
        while ($fila = mysqli_fetch_assoc($query_result)) {
            $array[] = $fila;
        }
    }
}

// Agregar resultados de cada consulta
agregarResultados($movimientos, $todos_eventos);
agregarResultados($eventos, $todos_eventos);
agregarResultados($aperturas, $todos_eventos);

// Ordenar por fecha (más reciente primero)
usort($todos_eventos, function($a, $b) {
    return strtotime($b['fecha']) - strtotime($a['fecha']);
});

// Limitar a los 50 eventos más recientes
$todos_eventos = array_slice($todos_eventos, 0, 50);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Historial</title>
  <link rel="stylesheet" href="historial.css" />
</head>
<body>

  <header class="histo">
    <h1>HISTORIAL</h1>
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
    <h2>Reporte de Actividad</h2>
    
    <div class="filtros">
        <button class="filtro-btn active" data-filtro="todos">Todos</button>
        <button class="filtro-btn" data-filtro="movimiento">Movimientos</button>
        <button class="filtro-btn" data-filtro="evento">Eventos</button>
        <button class="filtro-btn" data-filtro="apertura">Aperturas</button>
    </div>
    
    <div class="tabla-contenedor">
        <table class="tabla-eventos">
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Origen</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($todos_eventos)): ?>
                    <?php foreach ($todos_eventos as $evento): 
                        $clase_tipo = strtolower($evento['tipo']);
                        $icono = '';
                        
                        // Asignar iconos según el tipo de evento
                        if ($clase_tipo === 'movimiento') {
                            $icono = $evento['descripcion'] === 'entrada' ? '⬆️' : '⬇️';
                        } elseif ($clase_tipo === 'apertura') {
                            $icono = '🔓';
                        } else {
                            if (strpos(strtolower($evento['descripcion']), 'bloqueo') !== false) {
                                $icono = '⚠️';
                            } else {
                                $icono = 'ℹ️';
                            }
                        }
                    ?>
                        <tr class="evento-fila <?php echo $clase_tipo; ?>">
                            <td><?php echo date('d/m/Y H:i:s', strtotime($evento['fecha'])); ?></td>
                            <td><span class="evento-tipo"><?php echo $icono . ' ' . $evento['tipo']; ?></span></td>
                            <td><?php echo $evento['descripcion']; ?></td>
                            <td><?php echo $evento['origen']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: white;">No hay registros de actividad disponibles.</td>
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

  // Filtrado de eventos
  const botonesFiltro = document.querySelectorAll('.filtro-btn');
  const filasEventos = document.querySelectorAll('.evento-fila');

  botonesFiltro.forEach(boton => {
    boton.addEventListener('click', () => {
      // Actualizar botones activos
      botonesFiltro.forEach(btn => btn.classList.remove('active'));
      boton.classList.add('active');
      
      const filtro = boton.getAttribute('data-filtro');
      
      // Mostrar/ocultar filas según el filtro
      filasEventos.forEach(fila => {
        if (filtro === 'todos' || fila.classList.contains(filtro)) {
          fila.style.display = '';
        } else {
          fila.style.display = 'none';
        }
      });
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
