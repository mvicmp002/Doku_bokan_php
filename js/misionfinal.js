// (El movimiento, la cámara y irA() vienen de juego.js)
// (guardarPartida() viene de partida.js)

function temblar(elemento, clase = 'temblor', ms = 450) {
  elemento.classList.remove(clase);
  void elemento.offsetWidth; // reinicia la animación aunque se repita seguido
  elemento.classList.add(clase);
  setTimeout(() => elemento.classList.remove(clase), ms);
}

const villano = document.getElementById('villano');
const hudJugador = document.getElementById('hud-player');
const hudBoss = document.getElementById('hud-boss');

// --- ESTADOS Y SISTEMA DE VIDA ---
let playerLife = 16;
let bossLife = 100;
let muerto = false;

function actualizarVidaJugador() {
  const cells = document.querySelectorAll('#player-health-bar .cell');
  cells.forEach((cell, index) => {
    if (index < playerLife) cell.classList.remove('empty');
    else cell.classList.add('empty');
  });
}

function hacerDanoJugador() {
  if (muerto) return;
  playerLife = Math.max(0, playerLife - 2);
  actualizarVidaJugador();
  temblar(player);
  temblar(hudJugador, 'temblor-hud');

  if (playerLife === 0) {
    muerto = true;
    clearInterval(temporizadorIntervalo);
    clearInterval(temporizadorCorazonGlobal);
    setTimeout(() => irA('misionfinal.php'), 600); // reinicio rápido
  }
}

function curarMitadPerdida() {
  const perdida = 16 - playerLife;
  if (perdida > 0) {
    playerLife += Math.floor(perdida / 2);
    actualizarVidaJugador();
  }
}

function actualizarVidaBoss(porcentaje) {
  bossLife = porcentaje;
  document.getElementById('boss-life-fill').style.width = `${bossLife}%`;
}

function dañarVillano() {
  temblar(villano, 'temblor', 400);
  temblar(hudBoss, 'temblor-hud');
}


// --- MENSAJES EMERGENTES DEL VILLANO ---
const popDistraccion = document.getElementById('distraccion-pop');
const textoDistraccion = document.getElementById('distraccion-texto');
const frasesVillano = [
  "¡No vas a recuperar nada!",
  "¡Aléjate de mis tesoros!",
  "¡Ríndete ahora!",
  "¡Siente la desesperación!",
  "¡Nunca vencerás a mi corazón!"
];

function lanzarDistraccion() {
  textoDistraccion.innerText = frasesVillano[Math.floor(Math.random() * frasesVillano.length)];
  popDistraccion.classList.remove('oculto');
  setTimeout(() => popDistraccion.classList.add('oculto'), 1600);
}

setInterval(() => {
  if (!muerto && Math.random() < 0.4) lanzarDistraccion();
}, 8000);


// --- SECUENCIA DE FASES Y CLICS ---
const targetItem = document.getElementById('target-item');
const targetImg = document.getElementById('target-img');

let faseActual = 0;
let clicksEnIntervalo = 0;
let totalClicksFase = 0;
let temporizadorIntervalo = null;
let temporizadorCorazonGlobal = null;

const imagenesFases = [
  "img/anteojos.png",
  "img/gato.png",
  "img/libros.png",
  "img/corazon.png"
];

const clicksObjetivos = [15, 20, 25];

function iniciarMonitoreoExcesoClicks() {
  clearInterval(temporizadorIntervalo);
  clicksEnIntervalo = 0;

  temporizadorIntervalo = setInterval(() => {
    if (clicksEnIntervalo > 5) hacerDanoJugador();
    clicksEnIntervalo = 0;
  }, 1500);
}

iniciarMonitoreoExcesoClicks();

targetItem.addEventListener('click', (e) => {
  e.stopPropagation();
  if (muerto) return;

  temblar(targetItem, 'golpe', 150);

  if (faseActual < 3) {
    clicksEnIntervalo++;
    totalClicksFase++;

    if (totalClicksFase >= clicksObjetivos[faseActual]) {
      curarMitadPerdida();
      faseActual++;
      totalClicksFase = 0;

      if (faseActual === 1) {
        actualizarVidaBoss(66);
        targetImg.src = imagenesFases[1];
        dañarVillano();
      } else if (faseActual === 2) {
        actualizarVidaBoss(33);
        targetImg.src = imagenesFases[2];
        dañarVillano();
      } else if (faseActual === 3) {
        actualizarVidaBoss(100); // regenera la vida del boss y aparece el corazón
        targetImg.src = imagenesFases[3];
        iniciarBatallaCorazon();
      }
      temblar(targetItem, 'cambio', 500);
    }
  } else if (faseActual === 3) {
    clicksEnIntervalo++;
    actualizarVidaBoss(Math.max(0, bossLife - 0.8));
    dañarVillano();

    if (bossLife <= 0) {
      clearInterval(temporizadorCorazonGlobal);
      clearInterval(temporizadorIntervalo);
      targetItem.remove();
      terminarJuego();
    }
  }
});

function iniciarBatallaCorazon() {
  clearInterval(temporizadorIntervalo);
  clicksEnIntervalo = 0;

  temporizadorCorazonGlobal = setInterval(() => {
    if (clicksEnIntervalo < 4) hacerDanoJugador();
    clicksEnIntervalo = 0;
  }, 2500);
}

// Guarda la partida como "completada" (el servidor calcula el tiempo)
async function terminarJuego() {
  try {
    await guardarPartida({ estado: 'completada' });
    irA('final.php');
  } catch (error) {
    if (confirm('No se pudo guardar tu partida: ' + error.message + '\n¿Reintentar?')) {
      terminarJuego();
    }
  }
}