// --- PUZLE DE LA CARTA (arrastrar y soltar) ---
const elementosArrastrables = document.querySelectorAll('.elemento');
const slots = document.querySelectorAll('.slot');
let piezasColocadas = 0;

elementosArrastrables.forEach(elemento => {
  elemento.addEventListener('dragstart', (e) => {
    e.dataTransfer.setData('text/plain', elemento.id);
    elemento.classList.add('arrastrando');
  });
  elemento.addEventListener('dragend', () => {
    elemento.classList.remove('arrastrando');
  });
});

slots.forEach(slot => {
  slot.addEventListener('dragover', (e) => {
    e.preventDefault();
    slot.classList.add('sobre');
  });

  slot.addEventListener('dragleave', () => {
    slot.classList.remove('sobre');
  });

  slot.addEventListener('drop', (e) => {
    e.preventDefault();
    slot.classList.remove('sobre');

    const idElemento = e.dataTransfer.getData('text/plain');
    const elemento = document.getElementById(idElemento);
    if (!elemento) return;

    const numeroElemento = idElemento.replace('elemento', '');
    const numeroSlot = slot.getAttribute('data-slot');

    // Validación estricta: cada pieza va en su casillero
    if (numeroElemento === numeroSlot && !slot.querySelector('.elemento')) {
      slot.appendChild(elemento);
      elemento.removeAttribute('draggable');
      piezasColocadas++;

      if (piezasColocadas === 6) mostrarPuerta();
    } else {
      slot.classList.add('error');
      setTimeout(() => slot.classList.remove('error'), 400);
    }
  });
});


// --- DIÁLOGO DEL PRÍNCIPE (se abre solo al empezar la misión) ---
const lineasDialogoPrincipe = [
  { name: "Príncipe Azul", text: "*sollozos*..." },
  { name: "...", text: "¿Qué le habrá pasado?" },
  { name: "Príncipe Azul", text: "No me pasó nada importante, solo no pasó lo que quería" },
  { name: "...", text: "¿Ah, me escuchó?" },
  { name: "Príncipe Azul", text: "No es difícil escucharte" },
  { name: "Príncipe Azul", text: "aunque si es complicado escuchar cosas que no queremos, e incluso hay cosas que ni se tienen que escuchar para que sean difíciles de procesar" },
  { name: "...", text: "¿Quieres o necesitas algo?" },
  { name: "Príncipe Azul", text: "lo único que quiero ahora son mis anteojos, los perdí en un momento, no recuerdo bien" },
  { name: "Príncipe Azul", text: "en una ventisca corta se volaron mis sentimientos, si los puedes recoger, me sería de mucha ayuda" },
  { name: "Príncipe Azul", text: "Por favor" },
  { name: "...", text: "Okay" }
];

const dialogoPrincipe = new Dialogos('principe', lineasDialogoPrincipe);
dialogoPrincipe.iniciar();

bloqueado = true; // no se puede caminar hasta leer el diálogo
setTimeout(() => dialogoPrincipe.abrir(), 900);

conectarPuerta('mision2.php', 2);