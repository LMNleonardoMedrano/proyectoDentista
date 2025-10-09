// Variables globales
let selectedStatus = 'sano';
let selectedTooth = null;
let teethData = {};

// Estados disponibles y colores clínicos
const statusColors = {
  sano: '#22c55e',
  caries: '#dc2626',
  obturado: '#3b82f6',
  corona: '#eab308',
  extraido: '#6b7280',
  endodoncia: '#a855f7',
  implante: '#0ea5e9'
};

// Cuadrantes organizados visualmente
const quadrants = {
  1: [18, 17, 16, 15, 14, 13, 12, 11],
  2: [21, 22, 23, 24, 25, 26, 27, 28],
  3: [31, 32, 33, 34, 35, 36, 37, 38],
  4: [48, 47, 46, 45, 44, 43, 42, 41]
};

// Inicializar datos clínicos
function initializeTeethData() {
  Object.values(quadrants).flat().forEach(id => {
    teethData[id] = {
      status: 'sano',
      notes: ''
    };
  });
}

// Render odontograma visual
function renderOdontogram() {
  Object.entries(quadrants).forEach(([q, teeth]) => {
    const quadrant = document.getElementById('quadrant' + q);
    quadrant.innerHTML = ''; // limpiamos cada cuadrante

    teeth.forEach(id => {
      const wrapper = document.createElement('div');
      wrapper.className = 'text-center';

      const statusClass = 'tooth-' + teethData[id].status;

      const tooth = document.createElement('div');
      tooth.id = 'tooth' + id;
      tooth.textContent = id;
      tooth.className = `tooth ${statusClass}`;
      tooth.onclick = () => selectTooth(id);

      const label = document.createElement('div');
      label.className = 'text-xs mt-1 text-gray-600 h-4';
      label.textContent = teethData[id].status !== 'sano' ? teethData[id].status : '';

      wrapper.appendChild(tooth);
      wrapper.appendChild(label);
      quadrant.appendChild(wrapper); // directamente dentro del cuadrante
    });
  });
}

function setSelectedStatus(status, button) {
  selectedStatus = status;

  document.querySelectorAll('.status-btn').forEach(btn => {
    btn.classList.remove('selected'); // quitamos selección sin alterar colores
  });

  if (button) {
    button.classList.add('selected'); // resaltamos botón seleccionado
  }
}

// Seleccionar diente
function selectTooth(id) {
  selectedTooth = id;
  teethData[id].status = selectedStatus;

  renderOdontogram();

  const panel = document.getElementById('toothNotesPanel');
  panel.classList.remove('hidden');
  document.getElementById('toothNotesTitle').textContent = `Diente #${id} - ${selectedStatus}`;
  document.getElementById('toothNotes').value = teethData[id].notes;

  document.getElementById('toothNotes').oninput = function () {
    teethData[id].notes = this.value;
  };
}

// Reset completo
function resetOdontogram() {
  initializeTeethData();
  renderOdontogram();
  selectedTooth = null;
  document.getElementById('toothNotesPanel').classList.add('hidden');

  document.querySelectorAll('.status-btn').forEach(btn => {
    btn.classList.remove('selected'); // solo removemos el resaltado
  });
  setSelectedStatus('sano');
}

function openOdontogramModal() {
  // Reseteamos contenido clínico antes de mostrar
  resetOdontogram();

  // Mostramos fecha actual si corresponde
  const fecha = new Date().toLocaleDateString('es-BO');
  document.getElementById('odontogramDate').textContent = fecha;

  // Mostramos el modal correctamente
  $('#modalAgregarOdontograma').modal({
    backdrop: 'static',
    keyboard: false
  }).modal('show');
}
// Cerrar modal
function closeOdontogram() {
  const modal = document.getElementById('modalVerOdontograma');
  modal.classList.remove('show', 'd-block');
  modal.classList.add('fade');
  document.body.classList.remove('modal-open');
  const backdrop = document.querySelector('.modal-backdrop');
  if (backdrop) backdrop.remove();
}

// Descargar imagen PNG
function downloadOdontogramImage() {
  const canvasTarget = document.getElementById('odontogramCanvas');

  html2canvas(canvasTarget, {
    backgroundColor: '#ffffff',
    scale: 3,
    useCORS: true
  }).then(canvas => {
    const link = document.createElement('a');
    link.download = 'odontograma.png';
    link.href = canvas.toDataURL('image/png');
    document.body.appendChild(link); // requerido en algunos navegadores
    link.click();
    document.body.removeChild(link);
  });
}

// Inicialización al cargar
document.addEventListener('DOMContentLoaded', function () {
  initializeTeethData();
  renderOdontogram();

  document.querySelectorAll('.btnVerOdontograma').forEach(button => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      openOdontogramModal();
    });
  });

  document.querySelectorAll('.status-btn').forEach(button => {
    button.addEventListener('click', function () {
      setSelectedStatus(this.dataset.status, this);
    });
  });
});
function saveOdontogram() {
  const canvasTarget = document.getElementById('odontogramCanvas');

  if (!canvasTarget) {
    alert('No se encontró el elemento del odontograma.');
    return;
  }

  if (canvasTarget.offsetWidth === 0 || canvasTarget.offsetHeight === 0) {
    alert('El odontograma no está visible o tiene tamaño cero. Por favor, ábrelo antes de guardar.');
    return;
  }

  html2canvas(canvasTarget, {
    backgroundColor: '#ffffff',
    scale: 3,
    useCORS: true
  }).then(canvas => {
    const imageData = canvas.toDataURL('image/png');

    // Guardar en campos ocultos
    document.getElementById('odontogramImageInput').value = imageData;
    document.getElementById('odontogramTeethInput').value = JSON.stringify(teethData);

    // Mostrar imagen en sección principal
    const preview = document.getElementById('odontogramPreview');
    const imageContainer = document.getElementById('odontogramImage');
    const summaryContainer = document.getElementById('teethSummary');

    preview.classList.remove('hidden');
    imageContainer.innerHTML = `
      <img src="${imageData}" alt="Odontograma"
           class="w-full h-auto rounded shadow-sm"
           style="max-height: 300px; object-fit: contain;">
    `;

    // Mostrar resumen de dientes alterados
    summaryContainer.innerHTML = '';
    Object.entries(teethData).forEach(([toothNumber, tooth]) => {
      if (tooth.status !== 'sano' || tooth.notes) {
        const div = document.createElement('div');
        div.className = 'p-3 bg-gray-50 rounded border text-sm';

        div.innerHTML = `
          <div class="font-semibold text-gray-800 mb-1">🦷 Diente #${toothNumber}</div>
          <div class="mb-1 text-gray-600">Estado: <span class="font-medium">${tooth.status}</span></div>
          ${tooth.notes
            ? `<div class="text-gray-700">📝 Notas: <span class="italic">${tooth.notes}</span></div>`
            : `<div class="text-gray-400 italic">Sin observaciones</div>`}
        `;

        summaryContainer.appendChild(div);
      }
    });

    // Cerrar el modal del odontograma
    $('#modalAgregarOdontograma').modal('hide');

    // Limpieza visual suave del backdrop
    setTimeout(() => {
      document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.style.opacity = '0';
        backdrop.style.transition = 'opacity 300ms ease';
        setTimeout(() => backdrop.remove(), 300);
      });

      document.body.classList.remove('modal-open');
      document.body.style.paddingRight = '';

      // Mostrar el modal de tratamiento clínico
      $('#modalAgregarTratamiento').modal('show');
      alert('Odontograma guardado exitosamente 🦷✅');
    }, 500);
  });
}
