const aviso = document.getElementById('aviso');

async function pedir(url, opciones) {
  const respuesta = await fetch(url, {
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json' },
    ...opciones
  });
  const data = await respuesta.json().catch(() => ({}));
  if (!respuesta.ok || !data.ok) {
    throw new Error(data.error || 'Ocurrió un error, probá de nuevo');
  }
  return data;
}

function irA(url) {
  document.body.classList.add('fade-out');
  setTimeout(() => { window.location.href = url; }, 700);
}

document.querySelectorAll('[data-accion]').forEach(boton => {
  boton.addEventListener('click', async () => {
    aviso.textContent = '';

    try {
      if (boton.dataset.accion === 'nueva') {
        boton.disabled = true;
        const data = await pedir('api/partidas', {
          method: 'POST',
          body: JSON.stringify({ slot: Number(boton.dataset.slot) })
        });
        irA('jugar.php?id=' + data.partida.id);

      } else if (boton.dataset.accion === 'borrar') {
        if (!confirm('¿Seguro que querés borrar esta partida? No se puede deshacer.')) return;
        boton.disabled = true;
        await pedir('api/partidas/' + boton.dataset.id, { method: 'DELETE' });
        window.location.reload();
      }
    } catch (error) {
      aviso.textContent = error.message;
      boton.disabled = false;
    }
  });
});