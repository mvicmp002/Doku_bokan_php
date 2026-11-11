// ===== CÓDIGO COMPARTIDO POR LAS 4 MISIONES =====
// (cámara, movimiento, diálogos, puerta y transiciones)

const gameScreen = document.getElementById('game-screen');
const player = document.querySelector('.player');

// El jugador empieza en el centro del mundo (2500x2500)
let playerX = 1250;
let playerY = 1250;
let targetX = playerX;
let targetY = playerY;
const speed = 6;
let bloqueado = false; // true mientras hay un diálogo abierto

function centrarCamara() {
  window.scrollTo({
    left: playerX - window.innerWidth / 2,
    top: playerY - window.innerHeight / 2,
    behavior: 'auto'
  });
}

function updatePlayerPosition() {
  player.style.left = `${playerX}px`;
  player.style.top = `${playerY}px`;
}

function gameLoop() {
  const dx = targetX - playerX;
  const dy = targetY - playerY;
  const distance = Math.hypot(dx, dy);

  if (distance > speed) {
    playerX += (dx / distance) * speed;
    playerY += (dy / distance) * speed;

    player.classList.remove('frente', 'espalda', 'izquierda', 'derecha');
    if (Math.abs(dx) > Math.abs(dy)) {
      player.classList.add(dx > 0 ? 'derecha' : 'izquierda');
    } else {
      player.classList.add(dy > 0 ? 'frente' : 'espalda');
    }
    player.classList.add('caminando');

    updatePlayerPosition();
    centrarCamara();
  } else {
    player.classList.remove('caminando');
  }
  requestAnimationFrame(gameLoop);
}

// Clic en el mapa para caminar (se ignora sobre elementos con clase "interactivo")
gameScreen.addEventListener('click', (e) => {
  if (bloqueado || e.target.closest('.interactivo')) return;

  const rect = gameScreen.getBoundingClientRect();
  const clickedX = e.clientX - rect.left;
  const clickedY = e.clientY - rect.top;

  targetX = Math.max(50, Math.min(2450, clickedX));
  targetY = Math.max(50, Math.min(2450, clickedY));

  const m = document.createElement('div');
  m.className = 'marcador';
  m.style.left = `${targetX}px`;
  m.style.top = `${targetY}px`;
  gameScreen.appendChild(m);
  setTimeout(() => m.remove(), 600);
});

updatePlayerPosition();
centrarCamara();
requestAnimationFrame(gameLoop);


// --- TRANSICIÓN ENTRE ESCENAS ---
function irA(url) {
  document.body.classList.add('fade-out');
  setTimeout(() => { window.location.href = url; }, 700);
}

// --- PUERTA DE SALIDA ---
const puerta = document.getElementById('puerta');

function mostrarPuerta() {
  puerta.classList.add('visible');
  document.querySelectorAll('.flecha').forEach(f => f.classList.add('oculta'));
}

function ocultarPuerta() {
  puerta.classList.remove('visible');
  document.querySelectorAll('.flecha').forEach(f => f.classList.remove('oculta'));
}

function conectarPuerta(urlDestino, misionNueva = null) {
  puerta.addEventListener('click', async (e) => {
    e.stopPropagation();
    puerta.blur();

    if (misionNueva === null) {
      irA(urlDestino);
      return;
    }

    if (puerta.dataset.guardando) return; // evita doble clic
    puerta.dataset.guardando = '1';

    try {
      await guardarPartida({ mision_alcanzada: misionNueva });
      irA(urlDestino);
    } catch (error) {
      delete puerta.dataset.guardando;
      alert('No se pudo guardar tu progreso: ' + error.message);
    }
  });
}
// --- DIÁLOGOS (letra a letra; clic, Espacio, Enter o E para avanzar) ---
class Dialogos {
  constructor(idNpc, lineas) {
    this.npc = document.getElementById(idNpc);
    this.caja = document.getElementById('caja-dialogo');
    this.nombre = document.getElementById('dialogo-nombre');
    this.texto = document.getElementById('dialogo');
    this.lineas = lineas;
    this.indice = 0;
    this.abierto = false;
    this.escribiendo = false;
    this.completo = '';
    this.timer = null;
  }

  iniciar() {
    this.npc.addEventListener('click', (e) => {
      e.stopPropagation();
      this.npc.blur();
      if (this.abierto) this.avanzar();
      else this.abrir();
    });

    this.caja.addEventListener('click', (e) => {
      e.stopPropagation();
      this.avanzar();
    });

    document.addEventListener('keydown', (e) => {
      if (this.abierto && ['Space', 'Enter', 'KeyE'].includes(e.code)) {
        e.preventDefault();
        this.avanzar();
      }
    });
  }

  abrir() {
    this.abierto = true;
    bloqueado = true;
    targetX = playerX;
    targetY = playerY;
    this.indice = 0;
    this.caja.classList.remove('oculto');
    this.mostrar();
  }

  mostrar() {
    const linea = this.lineas[this.indice];
    this.nombre.textContent = linea.name;
    this.completo = linea.text;
    this.texto.textContent = '';
    this.escribiendo = true;
    this.caja.classList.remove('listo');

    let i = 0;
    clearInterval(this.timer);
    this.timer = setInterval(() => {
      i++;
      this.texto.textContent = this.completo.slice(0, i);
      if (i >= this.completo.length) this.terminarDeEscribir();
    }, 28);
  }

  terminarDeEscribir() {
    clearInterval(this.timer);
    this.texto.textContent = this.completo;
    this.escribiendo = false;
    this.caja.classList.add('listo');
  }

  avanzar() {
    if (this.escribiendo) {
      this.terminarDeEscribir();
      return;
    }
    this.indice++;
    if (this.indice < this.lineas.length) this.mostrar();
    else this.cerrar();
  }

  cerrar() {
    this.caja.classList.add('oculto');
    this.caja.classList.remove('listo');
    this.abierto = false;
    bloqueado = false;
  }
}