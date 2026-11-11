const frases = [
  "terminé mi misión",
  "pude encontrar los objetos de los chicos",
  "los voy a extrañar",
  "ahora creo que puedo descansar",
  "...",
  "o más bien"
];

const storyContainer = document.getElementById('story-container');
const storyText = document.getElementById('story-text');
const creditsContainer = document.getElementById('credits-container');

let fraseIndex = 0;
let charIndex = 0;

function escribirFrase() {
  storyContainer.classList.add('fade-in');
  if (fraseIndex < frases.length) {
    if (charIndex < frases[fraseIndex].length) {
      storyText.textContent += frases[fraseIndex].charAt(charIndex);
      charIndex++;
      setTimeout(escribirFrase, 70); // Velocidad de escritura
    } else {
      // Pausa antes de borrar y pasar a la siguiente frase
      setTimeout(borrarFrase, 1800);
    }
  } else {
    // Terminaron las frases, ocultar contenedor y mostrar créditos
    storyContainer.classList.remove('fade-in');
    setTimeout(() => {
      storyContainer.style.display = 'none';
      creditsContainer.classList.add('fade-in');
    }, 1000);
  }
}

function borrarFrase() {
  if (charIndex > 0) {
    storyText.textContent = frases[fraseIndex].substring(0, charIndex - 1);
    charIndex--;
    setTimeout(borrarFrase, 40);
  } else {
    fraseIndex++;
    setTimeout(escribirFrase, 400);
  }
}

// Iniciar secuencia al cargar la página
window.addEventListener('load', () => {
  setTimeout(escribirFrase, 1000);
});