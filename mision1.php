<?php
require_once __DIR__ . '/config/sesion.php';
$partida = requerirPartida(1);
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Misión 1 - El Príncipe Triste</title>
  <link rel="stylesheet" href="css/juego.css">
  <link rel="stylesheet" href="css/mision1.css">
</head>
<body data-partida="<?= (int) $partida['id'] ?>">
  <div id="game-screen">
    <div id="game">
      <div class="player frente"></div>

      <!-- Casilleros (Slots) ordenados para la carta -->
      <div class="slots-container">
        <div class="fila-carta">
          <div class="slot" data-slot="1"></div>
          <div class="slot" data-slot="2"></div>
          <div class="slot" data-slot="3"></div>
        </div>
        <div class="fila-carta">
          <div class="slot" data-slot="4"></div>
          <div class="slot" data-slot="5"></div>
          <div class="slot" data-slot="6"></div>
        </div>
      </div>

      <!-- Piezas de la carta -->
      <div id="elemento1" class="elemento interactivo" draggable="true"><img src="img/cartaprincipe1.png" alt="pedazo1"></div>
      <div id="elemento2" class="elemento interactivo" draggable="true"><img src="img/cartaprincipe2.png" alt="pedazo2"></div>
      <div id="elemento3" class="elemento interactivo" draggable="true"><img src="img/cartaprincipe3.png" alt="pedazo3"></div>
      <div id="elemento4" class="elemento interactivo" draggable="true"><img src="img/cartaprincipe4.png" alt="pedazo4"></div>
      <div id="elemento5" class="elemento interactivo" draggable="true"><img src="img/cartaprincipe5.png" alt="pedazo5"></div>
      <div id="elemento6" class="elemento interactivo" draggable="true"><img src="img/cartaprincipe6.png" alt="pedazo6"></div>

      <!-- Príncipe -->
      <button id="principe" class="npc interactivo">
        <span class="flecha">▼</span>
        <img src="img/principe_triste.png" alt="principe">
      </button>

      <!-- Caja de diálogo -->
      <div id="caja-dialogo" class="oculto interactivo">
        <div id="dialogo-nombre"></div>
        <p id="dialogo"></p>
        <span id="dialogo-sig">▶</span>
      </div>

      <!-- Puerta de salida (aparece al completar) -->
      <button id="puerta" class="interactivo">
        <img src="img/puerta.png" alt="mision siguiente">
      </button>
    </div>
  </div>

<script src="js/juego.js"></script>
<script src="js/partida.js"></script>
<script src="js/mision1.js"></script>
</body>
</html>