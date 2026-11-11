<?php
require_once __DIR__ . '/config/sesion.php';
requerirSesion();
require_once __DIR__ . '/models/Partida.php';

$partida = isset($_SESSION['partida_id'])
    ? (new Partida())->buscarPorId((int) $_SESSION['partida_id'])
    : null;

if (!$partida
    || (int) $partida['jugador_id'] !== (int) $_SESSION['jugador_id']
    || $partida['estado'] !== 'completada') {
    header('Location: partidas.php');
    exit;
}

$t = (int) $partida['tiempo_segundos'];
$tiempo = sprintf('%02d:%02d', intdiv($t, 60), $t % 60);
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Final - Doku Bokan</title>
  <link rel="stylesheet" href="css/final.css">
</head>
<body>

  <!-- Contenedor de la historia con máquina de escribir -->
  <div id="story-container">
    <span id="story-text"></span>
  </div>

  <!-- Contenedor de créditos finales -->
  <div id="credits-container">
    <div class="credits-scroll">
      <div class="title-game">Doku Bokan</div>
      
      <div class="section-title">Personajes</div>
      
      <div class="cast-grid">
        <div class="cast-left">
          Protagonista<br>
          Príncipe<br>
          Cleopatra<br>
          Monje<br>
          Villano
        </div>
        <div class="cast-right">
          Soma<br>
          Príncipe Azul<br>
          Cleopatra<br>
          Monje Shi<br>
          Mago Mikhail
        </div>
      </div>

      <div class="creador-curso-grid">
      <div class="tiempo-final">Tu tiempo: <?= $tiempo ?></div>
      <a href="partidas.php" class="volver-menu">Volver a mis partidas</a>
        <div>Victoria Parec</div>
        <div>3° 1° TEP</div>
      </div>
    </div>
  </div>

<script src="js/final.js"></script>
</body>
</html>