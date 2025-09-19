<?php
session_start();
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>LonelySafe</title>
  <link rel="stylesheet" href="sing_in.css" />
</head>
<body>

  <header class="navbar">
    <div class="logo-container">
      <img src="imag/ola.png" alt="Logo" class="logo" />
      <span class="brand">LonelySafe</span>
    </div>
    <nav class="nav-links">
        <a href="inicio.html">Home</a>
        <a href="contacto.html">About</a>
      <a href="SignUp.html">Sign Up</a>
      <a href="signIn2.php">Sign In</a>
    </nav>
  </header>

  <div id="signin-container">
    <form action="SingIn.php" method="POST">
      <h2 style="color: white; text-align: center;">Sign In</h2>

      <?php if (!empty($error)) : ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <input type="text" name="email" placeholder="Email or Username" class="input-field" required> 
      <input type="password" name="password" placeholder="Password" class="input-field" required>

      <div class="social-login">
        <button type="button" class="social-btn">
          <img src="imag/pildora9-6-22google-digitalizatec-fondo-azul.png" alt="Google" />
        </button>
        <button type="button" class="social-btn">
          <img src="imag/facebook.png" alt="Facebook" />
        </button>
        <button type="button" class="social-btn">
          <img src="imag/x.png" alt="X" />
        </button>
      </div>

      <button type="submit" class="submit-btn">Sign In</button>
    </form>
  </div>

  <footer class="footer">
    <p><strong>Copyright (©) 2025 - The Lonely Boys</strong></p>
    <p>El contenido de este sitio, incluyendo textos, diseños y código, es propiedad del equipo desarrollador y no puede ser reproducido sin autorización previa.</p>
  </footer>

<script>
document.addEventListener("DOMContentLoaded", () => {
  document.body.classList.add('fade-in');

  document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href && !href.startsWith('#') && !href.startsWith('javascript')) {
        e.preventDefault();
        document.body.classList.remove('fade-in');
        document.body.classList.add('fade-out');
        setTimeout(() => {
          window.location.href = href;
        }, 600);
      }
    });
  });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  document.body.classList.add('fade-in');

  setTimeout(() => {
    document.getElementById('signin-container').classList.add('show');
  }, 300);

  document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href && !href.startsWith('#') && !href.startsWith('javascript')) {
        e.preventDefault();
        document.body.classList.remove('fade-in');
        document.body.classList.add('fade-out');
        setTimeout(() => {
          window.location.href = href;
        }, 600);
      }
    });
  });
});

document.querySelector('form').addEventListener('submit', function (e) {
  e.preventDefault();
  document.body.classList.add('fade-out');
  setTimeout(() => {
    this.submit();
  }, 600);
});
</script>

</body>
</html>
