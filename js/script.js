window.addEventListener('load', () => {
  const startScreen = document.getElementById('start-screen');

  // Pequeño retraso para que Doku y Bokan entren con animación al abrir la página
  setTimeout(() => {
    startScreen.classList.add('show-images');
  }, 400);
});

const startBtn = document.getElementById('start-btn');

startBtn.addEventListener('click', () => {
  startBtn.disabled = true; // evita doble clic
  document.body.classList.add('fade-out');

  setTimeout(() => {
    window.location.href = startBtn.dataset.destino;
  }, 700);
});