<?php
require_once __DIR__ . '/config/sesion.php';
requerirSesion();
require_once __DIR__ . '/models/Jugador.php';
require_once __DIR__ . '/models/Partida.php';

$jugador = (new Jugador())->buscarPorId((int) $_SESSION['jugador_id']);
if (!$jugador) { // la cuenta ya no existe
    session_destroy();
    header('Location: login.php');
    exit;
}

// Se ordenan las partidas por número de espacio: [1 => partida, 3 => partida]
$partidas = [];
foreach ((new Partida())->deJugador((int) $jugador['id']) as $p) {
    $partidas[(int) $p['slot']] = $p;
}

$misiones = [
    1 => 'El Príncipe Triste',
    2 => 'Cleopatra',
    3 => 'Limpieza y Botes',
    4 => 'Batalla del Corazón',
];

function formatoTiempo(?int $s): string {
    if ($s === null) return '--:--';
    return sprintf('%02d:%02d', intdiv($s, 60), $s % 60);
}

$mejor = $jugador['mejor_tiempo'] !== null ? (int) $jugador['mejor_tiempo'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Elegí tu partida - Doku Bokan</title>
  <link rel="stylesheet" href="css/partidas.css">
</head>
<body>

  <main class="contenedor">

    <nav class="barra">
      <a href="index.php" class="barra-link">← Inicio</a>
      <span class="saludo">Hola, <?= htmlspecialchars($jugador['nombre']) ?></span>
      <a href="logout.php" class="barra-link">Cerrar sesión</a>
    </nav>

    <h1>Elegí tu partida</h1>

    <div class="stats">
      <div class="stat"><b><?= (int) $jugador['partidas_jugadas'] ?></b><span>Partidas jugadas</span></div>
      <div class="stat"><b><?= formatoTiempo($mejor) ?></b><span>Mejor tiempo</span></div>
    </div>

    <div id="aviso" class="aviso"></div>

    <section class="ranuras">
      <?php for ($n = 1; $n <= 3; $n++): $p = $partidas[$n] ?? null; ?>

        <?php if (!$p): ?>
          <article class="ranura vacia">
            <div class="ranura-cabecera">Partida <?= $n ?></div>
            <div class="ranura-cuerpo">
              <p class="ranura-info">Espacio vacío</p>
              <button class="btn-slot" data-accion="nueva" data-slot="<?= $n ?>">Nueva partida</button>
            </div>
          </article>

        <?php elseif ($p['estado'] === 'completada'): ?>
          <article class="ranura completa">
            <div class="ranura-cabecera">Partida <?= $n ?></div>
            <div class="ranura-cuerpo">
              <p class="ranura-info">¡Completada!</p>
              <p class="ranura-detalle">Tiempo: <?= formatoTiempo((int) $p['tiempo_segundos']) ?></p>
              <button class="btn-borrar" data-accion="borrar" data-id="<?= (int) $p['id'] ?>">Borrar</button>
            </div>
          </article>

        <?php else: ?>
          <article class="ranura en-curso">
            <div class="ranura-cabecera">Partida <?= $n ?></div>
            <div class="ranura-cuerpo">
              <p class="ranura-info">Misión <?= (int) $p['mision_alcanzada'] ?></p>
              <p class="ranura-detalle"><?= htmlspecialchars($misiones[(int) $p['mision_alcanzada']] ?? '') ?></p>
              <a class="btn-slot" href="jugar.php?id=<?= (int) $p['id'] ?>">Continuar</a>
              <button class="btn-borrar" data-accion="borrar" data-id="<?= (int) $p['id'] ?>">Borrar</button>
            </div>
          </article>
        <?php endif; ?>

      <?php endfor; ?>
    </section>

  </main>

<script src="js/partidas.js"></script>
</body>
</html>