<?php
require_once __DIR__ . '/config/sesion.php';
$partida = requerirPartida(3);
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Misión 3 - Limpieza y Botes</title>
  <link rel="stylesheet" href="css/juego.css">
  <link rel="stylesheet" href="css/mision3.css">
</head>
<body data-partida="<?= (int) $partida['id'] ?>">
  <div id="game-screen">
    <div id="game">
      <div class="player frente"></div>

      <!-- Botes de basura -->
      <div id="bote1" class="basurero"></div>
      <div id="bote2" class="basurero"></div>
      <div id="bote3" class="basurero"></div>

      <!-- Basuras dispersas -->
      <div id="basura1" class="basura interactivo" draggable="true"><img src="img/basura1.png" alt="basura 1"></div>
      <div id="basura2" class="basura interactivo" draggable="true"><img src="img/basura2.png" alt="basura 2"></div>
      <div id="basura3" class="basura interactivo" draggable="true"><img src="img/basura3.png" alt="basura 3"></div>

      <!-- Monje -->
      <button id="monje" class="npc interactivo">
        <span class="flecha">▼</span>
        <img src="img/monje.png" alt="monje">
      </button>

      <!-- Caja de diálogo -->
      <div id="caja-dialogo" class="oculto interactivo">
        <div id="dialogo-nombre"></div>
        <p id="dialogo"></p>
        <span id="dialogo-sig">▶</span>
      </div>

      <!-- Puerta de salida (aparece únicamente con la combinación correcta) -->
      <button id="puerta" class="interactivo">
        <img src="img/puerta.png" alt="mision siguiente">
      </button>
    </div>
  </div>

<script src="js/juego.js"></script>
<script src="js/partida.js"></script>
<script src="js/mision3.js"></script>
</body>
</html>