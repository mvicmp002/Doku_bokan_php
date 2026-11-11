<?php
require_once __DIR__ . '/config/sesion.php';

// Si ya tiene la sesión iniciada, no hace falta que vea el login
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
  <title>Iniciar sesión - Doku Bokan</title>
  <link rel="stylesheet" href="css/login.css">
</head>
<body>

  <main class="card">

    <!-- Panel de color -->
    <aside class="panel">
      <h2>¡BIENVENIDO!</h2>
      <p>¿No tenés una cuenta?</p>
      <a href="registro.php" class="btn-panel">Registrate</a>
    </aside>

    <!-- Formulario -->
    <section class="form-side">
      <div class="logo">Doku Bokan</div>
      <h1>Iniciar Sesión</h1>

      <form id="auth-form" data-endpoint="api/login" data-texto="Iniciar sesión" novalidate>
        <div class="campo">
          <input type="email" name="email" placeholder="Correo electrónico" autocomplete="email" required>
          <small class="error-campo"></small>
        </div>

        <div class="campo">
          <input type="password" name="password" placeholder="Contraseña" autocomplete="current-password" required>
          <small class="error-campo"></small>
        </div>

        <div id="mensaje" class="mensaje"></div>

        <button type="submit" class="btn-enviar">Iniciar sesión</button>
      </form>

      <a href="index.php" class="volver">← Volver al inicio</a>
    </section>

  </main>

<script src="js/login.js"></script>
</body>
</html>