<?php
session_start();
include 'conexion-BD.php';
$conexion = conectar();

$resultados = mysqli_query($conexion, "SELECT * FROM users WHERE ID = ".$_SESSION['ID'])
    or die("Problemas en el select" . mysqli_error($conexion));

if (mysqli_num_rows($resultados) > 0) {
    while ($fila = mysqli_fetch_assoc($resultados)) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Perfil de Usuario</title>
  <link rel="stylesheet" href="perfil.css" />


  <style>
.custom-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.modal-content {
  background: #fff;
  padding: 20px 30px;
  border-radius: 8px;
  text-align: center;
  max-width: 320px;
  box-shadow: 0 0 10px rgba(0,0,0,0.3);
}

.modal-content p {
  font-size: 16px;
  margin-bottom: 15px;
  color: #333;
}

.modal-buttons {
  display: flex;
  justify-content: space-between;
}

.modal-buttons button {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.confirm-btn {
  background-color: #d9534f;
  color: white;
}

.cancel-btn {
  background-color: #6c757d;
  color: white;
}
</style>

</head>
<body>
  <div class="sidebar">
    <a href="cuenta2.php" class="back-arrow">←</a>
    <div class="sidebar-links">
       <a href="#" id="cerrarSesionBtn">Cerrar sesión</a>
      <a href="#">Configuración</a>
      <a href="#">Ayuda</a>
    </div>
  </div>

  <div class="profile-container">
    <div class="profile-card">
      <div class="profile-header">
        <div class="avatar">
          <img src="<?php echo $fila['images']; ?>" alt="Avatar" />
        </div>
        <div class="username">
    <?php echo $fila['Name']; ?>
    <div class="user-id">ID: <?php echo $fila['ID']; ?></div>
</div>

        
        <form id="form-foto" action="actualizar_foto.php" method="POST" enctype="multipart/form-data">
          <input type="file" id="nueva_foto" name="nueva_foto" accept="image/*" style="display:none;">
          <button type="button" class="edit-button" onclick="document.getElementById('nueva_foto').click();">
            Cambiar foto
          </button>
          <button type="button" class="edit-button" onclick="eliminarFoto()">
            Eliminar foto
          </button>
        </form>

        <script>
        document.getElementById('nueva_foto').addEventListener('change', function() {
          if (this.files.length > 0) {
            document.getElementById('form-foto').submit();
          }
        });

        function eliminarFoto() {
  // Crear modal personalizado
  const modal = document.createElement('div');
  modal.classList.add('custom-modal');
  modal.innerHTML = `
    <div class="modal-content">
      <p>¿Seguro que quieres eliminar tu foto y usar la predeterminada?</p>
      <div class="modal-buttons">
        <button class="confirm-btn">Sí, eliminar</button>
        <button class="cancel-btn">Cancelar</button>
      </div>
    </div>
  `;
  document.body.appendChild(modal);

  // Confirmar eliminación
  modal.querySelector('.confirm-btn').addEventListener('click', () => {
    window.location.href = "eliminar_foto.php";
  });

  // Cancelar y cerrar modal
  modal.querySelector('.cancel-btn').addEventListener('click', () => {
    modal.remove();
  });
}
        </script>
      </div>

      <div class="form-group">
        <label class="label">Gmail</label>
        <div class="input-container">
          <input type="email" value="<?php echo $fila['Email']; ?>" readonly/>
          <span class="close">×</span>
        </div>
      </div>

      <div class="form-group">
        <label class="label">Password</label>
        <div class="input-container">
          <input type="password" value="<?php echo $fila['Password']; ?>" readonly/>
          <span class="close">×</span>
        </div>
      </div>
    </div>
  </div>
  <script>
document.getElementById('cerrarSesionBtn').addEventListener('click', function(event) {
  event.preventDefault();

  // Crear modal
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

  // Acciones
  document.getElementById('confirmLogout').addEventListener('click', () => {
    window.location.href = "logout.php";
  });

  document.getElementById('cancelLogout').addEventListener('click', () => {
    modal.remove();
  });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  document.body.classList.add('fade-in');
});

// Animación al salir
document.querySelectorAll("a").forEach(link => {
  link.addEventListener("click", function (e) {
    const href = this.getAttribute("href");
    if (href && !href.startsWith("#") && !href.startsWith("javascript")) {
      e.preventDefault();
      document.body.classList.add("fade-out");
      setTimeout(() => {
        window.location.href = href;
      }, 500);
    }
  });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const container = document.querySelector('.container');
  container.classList.add('animate-in');
});
</script>
<script>
  // Animación de entrada al cargar
  window.addEventListener("load", () => {
    document.body.classList.add("loaded");
  });

  // Animación de salida al hacer clic en enlaces
  document.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", function (e) {
      const href = this.getAttribute("href");
      if (href && !href.startsWith("#") && !href.startsWith("javascript")) {
        e.preventDefault();
        document.body.classList.add("fade-out");
        setTimeout(() => {
          window.location.href = href;
        }, 600);
      }
    });
  });
</script>

</body>
</html>
<?php
    }
}
?>
