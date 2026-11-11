<?php
require_once __DIR__ . '/config/sesion.php';
$partida = requerirPartida(2);
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Misión 2 - Cleopatra</title>
  <link rel="stylesheet" href="css/juego.css">
  <link rel="stylesheet" href="css/mision2.css">
</head>
<body data-partida="<?= (int) $partida['id'] ?>">
  <div id="game-screen">
    <div id="game">
      <div class="player frente"></div>

      <!-- Caja para entregar -->
      <div id="caja" class="caja interactivo" draggable="true">
        <img src="img/caja.png" alt="caja">
      </div>

      <!-- Cleopatra -->
      <button id="cleopatra" class="npc interactivo">
        <span class="flecha">▼</span>
        <img src="img/cleo.png" alt="cleopatra">
      </button>

      <!-- Caja de diálogo -->
      <div id="caja-dialogo" class="oculto interactivo">
        <div id="dialogo-nombre"></div>
        <p id="dialogo"></p>
        <span id="dialogo-sig">▶</span>
      </div>

      <!-- Puerta de salida (aparece al entregar la caja) -->
      <button id="puerta" class="interactivo">
        <img src="img/puerta.png" alt="mision siguiente">
      </button>
    </div>
  </div>

<script src="js/juego.js"></script>
<script src="js/partida.js"></script>
<script src="js/mision2.js"></script>
</body>
</html>