<?php
require_once __DIR__ . '/config/sesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio - Juego</title>
  <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div id="start-screen">

  <!-- Botones de sesión (arriba a la derecha) -->
  <nav id="auth-nav">
    <?php if (estaLogueado()): ?>
      <span class="saludo">Hola, <?= htmlspecialchars($_SESSION['nombre']) ?></span>
      <a href="logout.php" class="auth-btn">Cerrar sesión</a>
    <?php else: ?>
      <a href="login.php" class="auth-btn">Iniciar sesión</a>
      <a href="registro.php" class="auth-btn auth-btn-alt">Registrarse</a>
    <?php endif; ?>
  </nav>

  <div class="center-content">
    <img id="left-image" src="img/doku.png" alt="Decoración izquierda">

    <button id="start-btn" data-destino="<?= estaLogueado() ? 'partidas.php' : 'login.php' ?>">
      <img src="img/boton.png" alt="Iniciar juego">
    </button>

    <img id="right-image" src="img/bokan.png" alt="Decoración derecha">
  </div>

  <footer>
    <p>© Grupo N°42</p>
  </footer>
</div>

<script src="js/script.js"></script>
</body>
</html>