<?php
require_once __DIR__ . '/config/sesion.php';

if (estaLogueado()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrarse - Doku Bokan</title>
  <link rel="stylesheet" href="css/login.css">
</head>
<body>

  <main class="card invertido">

    <!-- Panel de color -->
    <aside class="panel">
      <h2>¡BIENVENIDO!</h2>
      <p>¿Ya tenés una cuenta?</p>
      <a href="login.php" class="btn-panel">Iniciá sesión</a>
    </aside>

    <!-- Formulario -->
    <section class="form-side">
      <div class="logo">Doku Bokan</div>
      <h1>Registrarse</h1>

      <form id="auth-form" data-endpoint="api/jugadores" data-texto="Registrarse" novalidate>
        <div class="campo">
          <input type="text" name="nombre" placeholder="Nombre" autocomplete="given-name" maxlength="60" required>
          <small class="error-campo"></small>
        </div>

        <div class="campo">
          <input type="text" name="apellido" placeholder="Apellido" autocomplete="family-name" maxlength="60" required>
          <small class="error-campo"></small>
        </div>

        <div class="campo">
          <input type="email" name="email" placeholder="Correo electrónico" autocomplete="email" required>
          <small class="error-campo"></small>
        </div>

        <div class="campo">
          <input type="password" name="password" placeholder="Contraseña (mínimo 6 caracteres)" autocomplete="new-password" required>
          <small class="error-campo"></small>
        </div>

        <div id="mensaje" class="mensaje"></div>

        <button type="submit" class="btn-enviar">Registrarse</button>
      </form>

      <a href="index.php" class="volver">← Volver al inicio</a>
    </section>

  </main>

<script src="js/login.js"></script>
</body>
</html>