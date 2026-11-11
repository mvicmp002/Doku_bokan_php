// ===== PARTIDA ACTIVA: guardado (lo usan las 4 misiones) =====
// El PHP deja en <body> el id de la partida.
const partidaId = Number(document.body.dataset.partida);

// Manda un PUT a la API para guardar el progreso o terminar la partida
async function guardarPartida(cuerpo) {
  const respuesta = await fetch('api/partidas/' + partidaId, {
    method: 'PUT',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(cuerpo)
  });
  const data = await respuesta.json().catch(() => ({}));
  if (!respuesta.ok || !data.ok) {
    throw new Error(data.error || 'No se pudo guardar');
  }
  return data;
}