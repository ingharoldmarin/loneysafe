<?php
session_start();
include 'conexion-BD.php';
$conexion = conectar();

// Verificar si el usuario está logueado
if (!isset($_SESSION['ID'])) {
    header("Location: login.php");
    exit();
}

// Obtener información del usuario
$query_usuario = mysqli_query($conexion, "SELECT * FROM users WHERE ID = ".$_SESSION['ID'])                     
    or die("Error al obtener datos del usuario: " . mysqli_error($conexion));

// Obtener dispositivos del usuario
$query_dispositivos = mysqli_query($conexion, "
    SELECT dispositivos.ID as dispositivo_id, dispositivos.*, accesos.*, estados.*, users.*
    FROM accesos
    INNER JOIN dispositivos ON accesos.ID_Dispositivos = dispositivos.ID
    INNER JOIN users ON accesos.ID_users = users.ID
    INNER JOIN estados ON accesos.ID_Estados = estados.ID
    WHERE users.ID = ".$_SESSION['ID']) 
    or die("Error al obtener dispositivos: " . mysqli_error($conexion));

// Obtener datos del usuario para el perfil
$usuario = mysqli_fetch_assoc($query_usuario);
$total_dispositivos = mysqli_num_rows($query_dispositivos);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Dispositivos</title>
    <link rel="stylesheet" href="cuentas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

.icono-caminar {
    position: fixed; /* Se queda fijo en la pantalla */
    top: 50px;       /* Distancia desde arriba */
    left: 290px;     /* Distancia desde la izquierda */
    z-index: 9999;   /* Muy alto para estar encima de todo */
    display: inline-block;
    color: white !important;
}

.icono-caminar img {
    width: 50px;
    height: auto;
    cursor: pointer;
}


        .modal-content {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            text-align: center;
            max-width: 300px;
            color: black;
        }

        .modal-buttons {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .modal-buttons button {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
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

        .context-menu {
            display: none;
            position: absolute;
            background: black;
            border: 1px solid #ccc;
            padding: 5px;
            z-index: 999;
            min-width: 120px;
        }
        
        .delete-form {
            margin: 0;
            padding: 0;
        }

        .device-info {
            display: flex;
            flex-direction: column;
            justify-content: center;    /* centra verticalmente */
            align-items: center;        /* centra horizontalmente */
            height: 100%;
            text-align: center;
            transform: translateY(20%); /* baja un poco todo el bloque */
        }

        .device-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px; /* separa el nombre del estado */
        }

        .device-status {
            font-size: 20px;
            font-weight: bold;
        }

        
    .icon-button.disabled {
        pointer-events: none;
        opacity: 0.4;
        cursor: not-allowed;
    }



    </style>
</head>
<body>
<a href="entradasalida.php" class="icono-caminar">
    <img src=imag/image.png>
</a>





    <div class="sidebar">
        <div class="profile">
            <img class="avatar-img" src="<?php echo htmlspecialchars($usuario['images']); ?>" alt="Avatar" />
            <a href="Perfil.php" class="profile-link"><strong><?php echo htmlspecialchars($usuario['Name']); ?></strong></a>
            <div class="user-id">ID: <?php echo htmlspecialchars($usuario['ID']); ?></div>
        </div>

        <nav class="menu">
            <ul>
                <li><strong>Dispositivos</strong></li>
                
            </ul>
        </nav>
        

        
        <div class="footer-links">
            <a href="#" id="cerrarSesionBtn">Cerrar sesión</a>
            <a href="#">Configuración</a>
            <a href="#">Ayuda</a>
        </div>
    </div>

    <div class="main">
        <header class="top-bar">
            <div class="top-icons">
                <a href="historial.php">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
            <div class="actions">
    <a href="dispo.html" 
       id="addDeviceBtn" 
       class="icon-button <?php echo ($total_dispositivos >= 3) ? 'disabled' : ''; ?>" 
       <?php echo ($total_dispositivos >= 3) ? 'onclick="return false;"' : ''; ?>>
        <i class="fas fa-plus"></i>
    </a>

       <a href="historial.php">
                </a>
   
</div>
        </header>

        <div class="content">
            <?php
            
            if (mysqli_num_rows($query_dispositivos) > 0) {
                while ($dispositivo = mysqli_fetch_assoc($query_dispositivos)) {
            ?>
                <div class="device-card" data-device-id="<?php echo $dispositivo['dispositivo_id']; ?>">
    <div class="device-header">
        <div class="device-name"><?php echo htmlspecialchars($dispositivo['Nombre']); ?></div>
      <div class="dots">
    <button class="dots-btn">
        <i class="fas fa-ellipsis-v"></i>
    </button>
    <div class="context-menu">
        <form class="delete-form" action="eliminar_dispo.php" method="post">
            <input type="hidden" name="dispositivo_id" value="<?php echo $dispositivo['dispositivo_id']; ?>">
            <button type="submit" class="delete-option">Eliminar</button>
        </form>
    </div>
</div>

    </div>
    <div class="device-status">
        <?php echo htmlspecialchars($dispositivo['Estado']); ?>
    </div>
</div>

            <?php
                }
            } else {
    echo "
    <div class='no-devices'>
        <i class='fas fa-box-open'></i>
        <p>No hay dispositivos registrados</p>
        <small>Agrega uno usando el botón +</small>
    </div>";
}

            
            mysqli_close($conexion);
            ?>
        </div>
    </div>

    <script>
        // Cerrar sesión
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

        // Efectos de transición
        document.addEventListener("DOMContentLoaded", () => {
            document.body.classList.add('fade-in');
            
            document.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", function(e) {
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
        });

        window.addEventListener("pageshow", () => {
            document.body.classList.add("page-loaded");
        });

        // Menú contextual
        document.addEventListener('DOMContentLoaded', () => {
           document.querySelectorAll('.dots-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        document.querySelectorAll('.context-menu').forEach(menu => menu.style.display = 'none');
        const menu = btn.parentElement.querySelector('.context-menu');
        if (menu) menu.style.display = 'block';
    });
});


            document.addEventListener('click', () => {
                document.querySelectorAll('.context-menu').forEach(menu => menu.style.display = 'none');
            });

            // Eliminación con AJAX
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const dispositivoId = this.querySelector('input[name="dispositivo_id"]').value;
                    const card = this.closest('.device-card');
                    
                    const modal = document.createElement('div');
                    modal.classList.add('custom-modal');
                    modal.innerHTML = `
                        <div class="modal-content">
                            <p>¿Estás seguro de que quieres eliminar este dispositivo?</p>
                            <div class="modal-buttons">
                                <button class="confirm-btn">Eliminar</button>
                                <button class="cancel-btn">Cancelar</button>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);

                    modal.querySelector('.confirm-btn').addEventListener('click', () => {
                        fetch('eliminar_dispo.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `dispositivo_id=${dispositivoId}`
                        })
                        .then(response => response.text())
                        .then(data => {
                            card.classList.add('fade-out');
                           setTimeout(() => {
    card.remove();

    // Verificar si ya no quedan dispositivos
    if (document.querySelectorAll('.device-card').length === 0) {
        const noDevices = document.createElement('div');
        noDevices.className = 'no-devices';
        noDevices.innerHTML = `
            <i class="fas fa-box-open"></i>
            <p>No hay dispositivos registrados</p>
            <small>Agrega uno usando el botón +</small>
        `;
        document.querySelector('.content').appendChild(noDevices);
    }
}, 400);

                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Ocurrió un error al eliminar el dispositivo');
                        })
                        .finally(() => {
                            modal.remove();
                        });
                    });

                    modal.querySelector('.cancel-btn').addEventListener('click', () => {
                        modal.remove();
                    });
                });
            });
        });

        
        
    </script>
</body>
</html>
