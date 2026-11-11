// --- ENTREGA DE LA CAJA A CLEOPATRA (arrastrar y soltar) ---
const caja = document.getElementById('caja');
const cleopatraBtn = document.getElementById('cleopatra');
let cajaEntregada = false;

caja.addEventListener('dragstart', (e) => {
  e.dataTransfer.setData('text/plain', 'caja');
});

cleopatraBtn.addEventListener('dragover', (e) => {
  e.preventDefault(); // permite soltar encima
  cleopatraBtn.classList.add('sobre');
});

cleopatraBtn.addEventListener('dragleave', () => {
  cleopatraBtn.classList.remove('sobre');
});

cleopatraBtn.addEventListener('drop', (e) => {
  e.preventDefault();
  cleopatraBtn.classList.remove('sobre');

  if (!cajaEntregada) {
    cajaEntregada = true;
    caja.remove(); // la caja desaparece: Cleopatra la agarró
    cleopatraBtn.classList.add('feliz');
    setTimeout(() => cleopatraBtn.classList.remove('feliz'), 700);
    mostrarPuerta();
  }
});


// --- DIÁLOGO DE CLEOPATRA (se abre solo al empezar la misión) ---
const lineasDialogoCleopatra = [
  { name: "...", text: "¿Hola?" },
  { name: "Cleopatra", text: "...." },
  { name: "...", text: "¿Necesitas algo?" },
  { name: "...", text: "¿Pasó algo?" },
  { name: "Cleopatra", text: "Sí... se me perdió..." },
  { name: "Cleopatra", text: "...mi caja" },
  { name: "...", text: "¿Quieres que te ayude a buscarla?" },
  { name: "Cleopatra", text: "¿Podrías?" },
  { name: "...", text: "Sí, ¿dónde la viste la última vez?" },
  { name: "Cleopatra", text: "Estaba por aquí cerca, pero desapareció." },
  { name: "...", text: "Entiendo, voy a intentar encontrarla." },
  { name: "Cleopatra", text: "Muchas gracias." },
  { name: "...", text: "*Espero que no sea lo que estoy pensando...*" }
];

const dialogoCleopatra = new Dialogos('cleopatra', lineasDialogoCleopatra);
dialogoCleopatra.iniciar();

bloqueado = true;
setTimeout(() => dialogoCleopatra.abrir(), 900);

conectarPuerta('mision3.php', 3);