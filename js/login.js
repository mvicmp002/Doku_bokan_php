const form = document.getElementById('auth-form');
const mensaje = document.getElementById('mensaje');
const boton = form.querySelector('button[type="submit"]');

function limpiarErrores() {
  mensaje.textContent = '';
  mensaje.className = 'mensaje';
  form.querySelectorAll('.error-campo').forEach(e => { e.textContent = ''; });
  form.querySelectorAll('input').forEach(i => i.classList.remove('invalido'));
}

function mostrarMensaje(texto, tipo) {
  mensaje.textContent = texto;
  mensaje.className = 'mensaje ' + tipo;
}

// Pone el error debajo del campo que corresponde
function marcarCampo(nombre, texto) {
  const input = form.querySelector(`[name="${nombre}"]`);
  if (!input) return;
  input.classList.add('invalido');
  input.parentElement.querySelector('.error-campo').textContent = texto;
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  limpiarErrores();

  const datos = Object.fromEntries(new FormData(form).entries());
  boton.disabled = true;
  boton.textContent = 'Un momento...';

  let exito = false;

  try {
    const respuesta = await fetch(form.dataset.endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(datos)
    });

    const data = await respuesta.json().catch(() => ({}));

    if (respuesta.ok && data.ok) {
      exito = true;
      mostrarMensaje('¡Listo! Entrando...', 'ok');
      document.body.classList.add('fade-out');
      setTimeout(() => { window.location.href = 'index.php'; }, 700);
    } else {
      mostrarMensaje(data.error || 'Ocurrió un error, probá de nuevo', 'error');
      if (data.detalles) {
        Object.entries(data.detalles).forEach(([campo, texto]) => marcarCampo(campo, texto));
      }
    }
  } catch (err) {
    mostrarMensaje('No se pudo conectar con el servidor. ¿Está prendido Apache?', 'error');
  } finally {
    if (!exito) {
      boton.disabled = false;
      boton.textContent = form.dataset.texto;
    }
  }
});