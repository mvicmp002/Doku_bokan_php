<?php
require_once __DIR__ . '/config/sesion.php';
$partida = requerirPartida(4);
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Misión Final - Batalla del Corazón</title>
  <link rel="stylesheet" href="css/juego.css">
  <link rel="stylesheet" href="css/misionfinal.css">
</head>
<body data-partida="<?= (int) $partida['id'] ?>">
  <!-- Interfaz fija (HUD de vida) -->
  <div id="hud-ui">
    <div id="hud-player" class="health-box">
      <span>PROTAGONISTA</span>
      <div id="player-health-bar" class="grid-bar">
        <div class="cell"></div><div class="cell"></div><div class="cell"></div><div class="cell"></div>
        <div class="cell"></div><div class="cell"></div><div class="cell"></div><div class="cell"></div>
        <div class="cell"></div><div class="cell"></div><div class="cell"></div><div class="cell"></div>
        <div class="cell"></div><div class="cell"></div><div class="cell"></div><div class="cell"></div>
      </div>
    </div>
    <div id="hud-boss" class="health-box">
      <span>MAGO ENOJADO</span>
      <div class="boss-bar-container">
        <div id="boss-life-fill"></div>
      </div>
    </div>
  </div>

  <!-- Mensaje emergente del villano -->
  <div id="distraccion-pop" class="oculto">
    <p id="distraccion-texto"></p>
  </div>

  <div id="game-screen">
    <div id="game">
      <div class="player frente"></div>

      <!-- Villano -->
      <div id="villano">
        <img src="img/mago_enojado.png" alt="villano">
      </div>

      <!-- Objeto interactivo actual (anteojos -> caja -> basura -> corazón) -->
      <div id="target-item" class="target-item interactivo">
        <img id="target-img" src="img/anteojos.png" alt="objeto">
      </div>

      <!-- Puerta final -->
      <button id="puerta" class="interactivo">
        <img src="img/puerta.png" alt="final">
      </button>
    </div>
  </div>

<script src="js/juego.js"></script>
<script src="js/partida.js"></script>
<script src="js/misionfinal.js"></script>
</body>
</html>