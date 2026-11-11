// --- PRUEBA Y ERROR: cualquier basura en cualquier bote ---
const basuras = document.querySelectorAll('.basura');
const basureros = document.querySelectorAll('.basurero');

// Combinación correcta
const combinacionGanadora = {
  "bote1": "basura3",
  "bote2": "basura1",
  "bote3": "basura2"
};

basuras.forEach(basura => {
  basura.addEventListener('dragstart', (e) => {
    e.dataTransfer.setData('text/plain', basura.id);
    basura.classList.add('arrastrando');
  });
  basura.addEventListener('dragend', () => {
    basura.classList.remove('arrastrando');
  });
});

basureros.forEach(bote => {
  bote.addEventListener('dragover', (e) => {
    e.preventDefault();
    bote.classList.add('sobre');
  });

  bote.addEventListener('dragleave', () => {
    bote.classList.remove('sobre');
  });

  bote.addEventListener('drop', (e) => {
    e.preventDefault();
    bote.classList.remove('sobre');

    const idBasura = e.dataTransfer.getData('text/plain');
    const elementoBasura = document.getElementById(idBasura);

    if (elementoBasura) {
      bote.appendChild(elementoBasura); // se pueden apilar varias y volver a sacar
      verificarVictoria();
    }
  });
});

function verificarVictoria() {
  let aciertos = 0;

  basureros.forEach(bote => {
    const dentro = bote.querySelectorAll('.basura');
    // Solo cuenta si el bote tiene exactamente la basura correcta
    if (dentro.length === 1 && dentro[0].id === combinacionGanadora[bote.id]) {
      aciertos++;
    }
  });

  if (aciertos === 3) mostrarPuerta();
  else ocultarPuerta();
}


// --- DIÁLOGO DEL MONJE (se abre solo al empezar la misión) ---
const lineasDialogoMonje = [
  { name: "Monje", text: "Saludos, viajero. Veo que has avanzado mucho." },
  { name: "...", text: "¿Qué haces por aquí?" },
  { name: "Monje", text: "El camino se ha llenado de desorden y basura dispersa." },
  { name: "Monje", text: "Si me ayudas a ordenar los residuos en los contenedores correctos, te abriré el paso final." },
  { name: "...", text: "De acuerdo, a buscar el orden." }
];

const dialogoMonje = new Dialogos('monje', lineasDialogoMonje);
dialogoMonje.iniciar();

bloqueado = true;
setTimeout(() => dialogoMonje.abrir(), 900);

conectarPuerta('misionfinal.php', 4);