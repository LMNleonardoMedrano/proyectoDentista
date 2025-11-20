const agendaDiv = document.getElementById('agendaOdontologos');
const rol = parseInt(agendaDiv.dataset.rol);
const idUsuario = parseInt(agendaDiv.dataset.idusuario);
const nombreUsuario = agendaDiv.dataset.nombre;

let calendar;
let idOdontologoActivo = null;

document.addEventListener('DOMContentLoaded', function () {

  // Para odontólogo, mostrar solo su tarjeta en la lista
  if (rol === 2) {
    document.querySelectorAll('#vistaOdontologos .col-md-4').forEach(card => {
      const btn = card.querySelector('.btnVerCalendario');
      if (parseInt(btn.dataset.id) !== idUsuario) {
        card.style.display = 'none'; // Ocultar otros odontólogos
      }
    });
  }

  // Botón "Ver calendario completo" (para todos)
  document.querySelectorAll('.btnVerCalendario').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = parseInt(this.dataset.id);
      const nombre = this.dataset.nombre;

      idOdontologoActivo = id;

      document.getElementById('vistaOdontologos').style.display = 'none';
      document.getElementById('vistaCalendarioOdontologo').style.display = 'block';
      document.getElementById('nombreOdontologoSeleccionado').textContent = nombre;

      // Destruir calendario previo si existía
      if (calendar) {
        calendar.destroy();
        calendar = null;
      }

      inicializarCalendarioUnico(id);
    });
  });

  // Botón "Volver a la lista"
  document.getElementById('btnVolverOdontologos').addEventListener('click', function () {
    document.getElementById('vistaCalendarioOdontologo').style.display = 'none';
    document.getElementById('vistaOdontologos').style.display = 'block';

    if (calendar) {
      calendar.destroy();
      calendar = null;
    }
    idOdontologoActivo = null;
  });

}); // DOMContentLoaded

function inicializarCalendarioUnico(idOdontologo) {
  const calendarEl = document.getElementById('calendarioUnico');

  calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    timeZone: 'local',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },
    slotLabelFormat: {
      hour: 'numeric',
      minute: '2-digit',
      hour12: true
    },
    eventTimeFormat: {
      hour: 'numeric',
      minute: '2-digit',
      meridiem: 'short',
      hour12: true
    },
    events: {
      url: "ajax/citas.ajax.php",
      method: "POST",
      extraParams: {
        accion: "listarPorOdontologo",
        idUsuarios: idOdontologo
      }
    },
    dateClick: function (info) {
      let fechaObj = new Date(info.dateStr);
      let dia = fechaObj.getDate().toString().padStart(2, '0');
      let mes = (fechaObj.getMonth() + 1).toString().padStart(2, '0');
      let anio = fechaObj.getFullYear();

      let mensajeFecha = `${dia}/${mes}/${anio}`;

      swal({
        title: `¿Qué deseas hacer el ${mensajeFecha}?`,
        text: 'Elige una opción',
        type: 'question',
        showCancelButton: true,
        confirmButtonText: 'Crear nueva cita',
        cancelButtonText: 'Ver registros'
      }).then(function (result) {
        if (result.value) {
          abrirModalAgregar(info.dateStr, info.view.type);
        } else if (result.dismiss === 'cancel') {
          let fecha = info.dateStr.split('T')[0];
          cargarCitasPorFecha(fecha);
        }
      });
    },
    eventClick: function (info) {
      abrirModalEditar(info.event);
    }
  });

  calendar.render();
}

function cargarCitasPorFecha(fecha) {
  $.ajax({
    url: "ajax/citas.ajax.php",
    method: "POST",
    data: { accion: "listarPorFecha", fecha: fecha },
    dataType: "json",
    success: function (respuesta) {
      if (respuesta.length === 0) {
        swal('Sin citas', 'No hay registros en esta fecha.', 'info');
      } else {
        let html = '<ul style="text-align:left; max-height:300px; overflow-y:auto;">';
        respuesta.forEach(function (cita) {
          html += `<li><strong>${cita.hora}</strong> - ${cita.motivoConsulta}<br>Paciente: ${cita.nombrePaciente}<br>Odontólogo: ${cita.nombreUsuarios}</li><hr>`;
        });
        html += '</ul>';

        swal({
          title: `Citas del ${fecha}`,
          html: html,
          width: 600,
          showCloseButton: true
        });
      }
    },
    error: function () {
      swal('Error', 'No se pudieron cargar los registros.', 'error');
    }
  });
}

function abrirModalAgregar(dateStr, viewType) {
  let fechaObj = new Date(dateStr);
  let fecha = fechaObj.toISOString().slice(0, 10);
  document.getElementById('fechaCita').value = fecha;

  if (idOdontologoActivo) {
    document.getElementById('usuarioCita').value = idOdontologoActivo;
  }

  if (viewType === 'dayGridMonth') {
    document.getElementById('horaCita').value = '';
    document.getElementById('horaFinCita').value = '';
  } else {
    let horas = fechaObj.getHours().toString().padStart(2, '0');
    let minutos = fechaObj.getMinutes().toString().padStart(2, '0');
    let hora = `${horas}:${minutos}`;

    let finDate = new Date(fechaObj.getTime() + 30 * 60000);
    let finHoras = finDate.getHours().toString().padStart(2, '0');
    let finMinutos = finDate.getMinutes().toString().padStart(2, '0');
    let horaFin = `${finHoras}:${finMinutos}`;

    document.getElementById('horaCita').value = hora;
    document.getElementById('horaFinCita').value = horaFin;
  }

  $('#modalAgregarCita').modal('show');
}

// Función que abre modal desde FullCalendar
function abrirModalEditar(evento) {
  $("#modalEditarCita").modal("show");
  $("#editarIdCita").val(evento.id);
  $("#editarFecha").val(evento.startStr.split("T")[0]);
  $("#editarHora").val(evento.startStr.split("T")[1].slice(0, 5));
  $("#editarHoraFin").val(evento.endStr ? evento.endStr.split("T")[1].slice(0, 5) : "");
  $("#editarMotivo").val(evento.title);
  $("#editarPaciente").val(evento.extendedProps.idPaciente);
  $("#editarUsuarios").val(evento.extendedProps.idUsuarios);
}

// Abrir modal desde la tabla
$(document).on("click", ".btnEditarCita", function () {
  const cita = $(this).data("cita");

  // Crear objeto compatible con abrirModalEditar
  const evento = {
    id: cita.idCita,
    startStr: cita.fecha + "T" + cita.hora,
    endStr: cita.horaFin ? cita.fecha + "T" + cita.horaFin : cita.fecha + "T" + cita.hora,
    title: cita.motivo,
    extendedProps: {
      idPaciente: cita.idPaciente,
      idUsuarios: cita.idUsuario
    }
  };

  abrirModalEditar(evento);
});

// Enviar formulario por AJAX
$(document).on("submit", "#formEditarCita", function (event) {
  event.preventDefault();
  var datos = new FormData(this);
  datos.append("accion", "editar");

  $.ajax({
    url: "ajax/citas.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {
      if (respuesta.ok) {
        mostrarToast("Cita editada correctamente.", "success");
        $("#modalEditarCita").modal("hide");
        if (calendar) calendar.refetchEvents(); // Actualiza FullCalendar
        // Actualiza tabla si quieres:
        actualizarFilaTabla(respuesta.cita);
      } else {
        mostrarToast("Error al editar la cita.", "error");
      }
    },
    error: function () {
      mostrarToast("Hubo un problema al editar la cita.", "error");
    }
  });
});

$(document).on("click", "#btnEliminarCita", function () {
  var idCita = $("#editarIdCita").val();

  swal({
    title: '¿Está seguro de borrar la cita?',
    text: "¡Si no lo está, puede cancelar la acción!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Cancelar',
    confirmButtonText: 'Sí, borrar cita!'
  }).then(function (result) {
    if (result.value) {
      window.location = "index.php?ruta=citas&eliminarCita=" + idCita;
    }
  });
});

function mostrarToast(mensaje, tipo) {
  let toastEl = document.createElement("div");
  toastEl.className = `toast ${tipo}`;
  toastEl.textContent = mensaje;
  document.body.appendChild(toastEl);
  setTimeout(() => { toastEl.remove(); }, 3000);
}

document.getElementById('horaCita').addEventListener('change', function () {
  let inicio = this.value;
  if (inicio) {
    let [h, m] = inicio.split(':').map(Number);
    m += 30;
    if (m >= 60) {
      h += 1;
      m -= 60;
    }
    let horaFin = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
    document.getElementById('horaFinCita').value = horaFin;
  } else {
    document.getElementById('horaFinCita').value = '';
  }
});
function updateStatus(idCita, nuevoEstado) {
  fetch('ajax/citas.ajax.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `idCita=${idCita}&estado=${nuevoEstado}`
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Buscamos la fila de la tabla correspondiente
        const fila = document.querySelector(`tr[data-id='${idCita}']`);
        if (!fila) return;

        const badge = fila.querySelector('.estado-badge');
        if (!badge) return;

        // Actualizamos el texto
        badge.textContent = data.estado.charAt(0).toUpperCase() + data.estado.slice(1);

        // Limpiamos clases previas y aplicamos nuevas según estado
        badge.className = 'estado-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
        switch (data.estado) {
          case 'confirmada':
            badge.classList.add('bg-green-100', 'text-green-800');
            break;
          case 'cancelada':
            badge.classList.add('bg-red-100', 'text-red-800');
            break;
          case 'no_asistio':
            badge.classList.add('bg-orange-100', 'text-orange-800');
            break;
          case 'completada':
            badge.classList.add('bg-purple-100', 'text-purple-800');
            break;
          default: // programada u otros
            badge.classList.add('bg-yellow-100', 'text-yellow-800');
        }

        // Mensaje rápido temporal
        const msg = document.createElement('small');
        msg.textContent = 'Estado actualizado';
        msg.style.color = '#4caf50';
        msg.style.marginLeft = '8px';
        badge.parentElement.appendChild(msg);
        setTimeout(() => msg.remove(), 1200);

      } else {
        console.warn('No se pudo actualizar el estado en la base de datos.');
      }
    })
    .catch(err => console.error('Error en la comunicación:', err));
}
// estadisticasCitas.js

function mostrarEstadisticas(citas) {
  // Inicializamos contadores
  let stats = {
    total: citas.length,
    programada: 0,
    confirmada: 0,
    atendida: 0
  };

  // Contamos por estado
  citas.forEach(cita => {
    if (stats[cita.estado] !== undefined) {
      stats[cita.estado]++;
    }
  });

  // Mostramos en el HTML
  document.getElementById('total-citas').textContent = stats.total;
  document.getElementById('programadas').textContent = stats.programada;
  document.getElementById('confirmadas').textContent = stats.confirmada;
  document.getElementById('atendida').textContent = stats.atendida;
}
const buscarPaciente = document.getElementById('buscarPaciente');
const listaPacientes = document.getElementById('listaPacientes');
const pacienteCitaInput = document.getElementById('pacienteCita');

// Mostrar/filtrar lista al escribir
buscarPaciente.addEventListener('input', () => {
  const term = buscarPaciente.value.toLowerCase();
  const items = listaPacientes.querySelectorAll('li');
  let visible = false;

  items.forEach(item => {
    const nombre = item.dataset.nombre.toLowerCase();
    const ci = item.dataset.ci;
    if (nombre.includes(term) || ci.includes(term)) {
      item.style.display = '';
      visible = true;
    } else {
      item.style.display = 'none';
    }
  });

  listaPacientes.style.display = visible ? 'block' : 'none';
});

// Seleccionar paciente de la lista
listaPacientes.addEventListener('click', e => {
  if (e.target.tagName === 'LI') {
    const id = e.target.dataset.id;
    const nombre = e.target.dataset.nombre;
    const ci = e.target.dataset.ci;

    buscarPaciente.value = `${nombre} – CI: ${ci}`;
    pacienteCitaInput.value = id; // set hidden input
    listaPacientes.style.display = 'none';
  }
});

// Ocultar lista al hacer clic fuera
document.addEventListener('click', e => {
  if (!buscarPaciente.contains(e.target) && !listaPacientes.contains(e.target)) {
    listaPacientes.style.display = 'none';
  }
});

document.addEventListener('DOMContentLoaded', function () {

  // =========================
  // Filtrado de citas por estado
  // =========================
  const filterButtons = document.querySelectorAll('.btn-filter');
  const tableRows = document.querySelectorAll('#data_table tbody tr');

  filterButtons.forEach(button => {
    button.addEventListener('click', function () {
      const filter = this.getAttribute('data-filter');

      // Resalta el botón activo
      filterButtons.forEach(btn => btn.classList.remove('btn-primary'));
      filterButtons.forEach(btn => btn.classList.add('btn-outline-secondary'));
      this.classList.remove('btn-outline-secondary');
      this.classList.add('btn-primary');

      // Filtrar filas
      tableRows.forEach(row => {
        if (filter === 'all' || row.getAttribute('data-estado') === filter) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  });

  // =========================
  // Manejo de botones de acciones
  // =========================
  const table = document.querySelector('#data_table tbody');

  table.addEventListener('click', function (e) {
    const btn = e.target.closest('button');
    if (!btn) return;

    // Editar cita
    if (btn.classList.contains('btnEditarCita')) {
      const cita = JSON.parse(btn.dataset.cita);
      document.querySelector("#modalEditarCita input[name='idCita']").value = cita.idCita;
      document.querySelector("#modalEditarCita input[name='paciente']").value = cita.paciente;
      document.querySelector("#modalEditarCita input[name='fecha']").value = cita.fecha;
      document.querySelector("#modalEditarCita input[name='hora']").value = cita.hora;
      // Abrir modal
      $('#modalEditarCita').modal('show');
    }

    // Confirmar cita
    if (btn.classList.contains('btnConfirmarCita')) {
      updateStatus(btn.dataset.id, 'confirmada');
    }

    // Cancelar cita
    if (btn.classList.contains('btnCancelarCita')) {
      updateStatus(btn.dataset.id, 'cancelada');
    }

    // No asistió
    if (btn.classList.contains('btnNoAsistioCita')) {
      updateStatus(btn.dataset.id, 'no_asistio');
    }

    // Eliminar cita
    if (btn.classList.contains('btnEliminarCita')) {
      deleteAppointment(btn.dataset.id);
    }
  });

});
// Filtros de tabla
document.querySelectorAll('.btn-filter').forEach(btn => {
  btn.addEventListener('click', () => {
    // Limpiar todos los botones
    document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
    // Activar el clickeado
    btn.classList.add('active');

    const filtro = btn.getAttribute('data-filter');
    console.log('Filtro aplicado:', filtro);
  });
});
/*=============================================
ELIMINAR cita
=============================================*/
$(document).on("click", ".btnEliminarCita", function () {

  var idCita = $(this).attr("idCita");

  swal({
    title: '¿Está seguro de borrar el citas?',
    text: "¡Si no lo está, puede cancelar la acción!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Cancelar',
    confirmButtonText: 'Sí, borrar citas!'
  }).then(function (result) {
    if (result.value) {
      window.location = "index.php?ruta=citas&idCita=" + idCita;
    }
  });

});
