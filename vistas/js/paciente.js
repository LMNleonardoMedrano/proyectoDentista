// Función para calcular edad con validación institucional
function calcularEdad(fechaNac) {
  const hoy = new Date();

  if (!fechaNac || fechaNac.trim() === "") {
    return { valido: false, mensaje: "La fecha de nacimiento está vacía." };
  }

  const nacimiento = new Date(fechaNac);

  if (isNaN(nacimiento.getTime())) {
    return { valido: false, mensaje: "Formato de fecha inválido. Use YYYY-MM-DD." };
  }

  if (nacimiento > hoy) {
    return { valido: false, mensaje: "La fecha de nacimiento no puede ser futura." };
  }

  let edad = hoy.getFullYear() - nacimiento.getFullYear();
  const mes = hoy.getMonth() - nacimiento.getMonth();
  const dia = hoy.getDate() - nacimiento.getDate();
  if (mes < 0 || (mes === 0 && dia < 0)) edad--;

  if (edad < 3) {
    return { valido: false, edad: edad, mensaje: `Edad insuficiente: ${edad} años. Se requiere mínimo 3 años.` };
  }

  return { valido: true, edad: edad };
}

/*==============================
VERIFICAR EDAD Y RESET FECHA
===============================*/
function verificarEdadConReset(selectorFecha, tipo) {
  const resultado = calcularEdad($(selectorFecha).val());

  if (!resultado.valido) {
    swal({
      title: '¡Error de edad!',
      text: resultado.mensaje,
      icon: 'error',
      confirmButtonText: 'Aceptar'
    }).then(() => {
      $(selectorFecha).val(""); // Limpiar el campo
      if (tipo === 'nueva') {
        $("#datosTutorNuevo").hide();
        $("#esMenor").val("no");
      } else {
        $("#datosTutorEditar").hide();
      }
    });
    return false;
  }

  // Mostrar/ocultar datos de tutor
  const tutorDiv = tipo === 'nueva' ? '#datosTutorNuevo' : '#datosTutorEditar';
  if (resultado.edad < 18) {
    $(tutorDiv).slideDown();
    if (tipo === 'nueva') $("#esMenor").val("si");
  } else {
    $(tutorDiv).slideUp();
    if (tipo === 'nueva') {
      $("#esMenor").val("no");
      $("#tutor_nombre").val("");
      $("#tutor_genero").val("");
      $("#tutor_ocupacion").val("");
      $("#tutor_relacion").val("");
    }
  }

  return true;
}

/*=============================================
EVENTOS CREAR PACIENTE
=============================================*/
$("#nuevaFechaNacimiento").on("change", function () {
  verificarEdadConReset("#nuevaFechaNacimiento", 'nueva');
});

$("#formNuevoPaciente").on("submit", function (e) {
  if (!verificarEdadConReset("#nuevaFechaNacimiento", 'nueva')) {
    e.preventDefault();
    return false;
  }
});

/*=============================================
EVENTOS EDITAR PACIENTE
=============================================*/
$("#editarFechaNacimiento").on("change", function () {
  verificarEdadConReset("#editarFechaNacimiento", 'editar');
});

$("#formEditarPaciente").on("submit", function (e) {
  if (!verificarEdadConReset("#editarFechaNacimiento", 'editar')) {
    e.preventDefault();
    return false;
  }
});

/*=============================================
VALIDACIONES Y FUNCIONES EXISTENTES (CI, tutor, eliminar, etc.)
=============================================*/
// Validar no repetir CI (nuevo)
$("#nuevoCi").on("change", function () {
  var ci = $(this).val().trim();
  if (!ci) return;

  $.ajax({
    url: "ajax/paciente.ajax.php",
    method: "POST",
    data: { validarCi: ci },
    dataType: "json",
    success: function (resp) {
      if (resp.existe) {
        swal({ title: '¡Error!', text: resp.mensaje, icon: 'error', confirmButtonText: 'Aceptar' });
        $("#nuevoCi").val("");
      }
    }
  });
});

// Validar no repetir CI (editar)
$("#editarCI").on("change", function () {
  var ci = $(this).val().trim();
  var idPaciente = $("#editaridPaciente").val();
  if (!ci) return;

  $.ajax({
    url: "ajax/paciente.ajax.php",
    method: "POST",
    data: { validarCi: ci },
    dataType: "json",
    success: function (resp) {
      if (resp.existe && idPaciente != resp.idPaciente) {
        swal({ title: '¡Error!', text: resp.mensaje, icon: 'error', confirmButtonText: 'Aceptar' });
        $("#editarCI").val("");
      }
    }
  });
});

// Llenar formulario editar paciente
$(document).on("click", ".btnEditarPaciente", function () {
  var idPaciente = $(this).attr("idPaciente");
  $.ajax({
    url: "ajax/paciente.ajax.php",
    method: "POST",
    data: { idPaciente: idPaciente },
    dataType: "json",
    success: function (resp) {
      $("#editaridPaciente").val(resp.idPaciente);
      $("#editarCI").val(resp.ci);
      $("#editarDomicilio").val(resp.domicilio);
      $("#editarFechaNacimiento").val(resp.fechaNac);
      $("#editarNombre").val(resp.nombre);
      $("#editarGenero").val(resp.genero);
      $("#editarTelefono").val(resp.telCel);

      if (resp.fechaNac) {
        verificarEdadConReset("#editarFechaNacimiento", 'editar');
        if (resp.tutor) {
          $("#editarNombrePT").val(resp.tutor.nombrePT);
          $("#editarGeneroPT").val(resp.tutor.generoPT);
          $("#editarOcupacionPT").val(resp.tutor.ocupacion);
          $("#editarRelacionPT").val(resp.tutor.relacion);
        }
      }
    }
  });
});

// Eliminar paciente
$(document).on("click", ".btnEliminarPaciente", function () {
  var idPaciente = $(this).attr("idPaciente");
  swal({
    title: '¿Está seguro de borrar el Paciente?',
    text: "¡Si no lo está, puede cancelar la acción!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Cancelar',
    confirmButtonText: 'Sí, borrar Paciente!'
  }).then(function (result) {
    if (result.value) {
      window.location = "index.php?ruta=paciente&idPaciente=" + idPaciente;
    }
  });
});
/*=============================================
FILTRAR TABLA: Mostrar solo menores
=============================================*/
$(document).ready(function () {
  let filtroActivo = false;

  $("#btnToggleMenores").click(function () {
    filtroActivo = !filtroActivo;

    if (filtroActivo) {
      $(this)
        .removeClass("btn-info")
        .addClass("btn-success")
        .html('<i class="ik ik-filter"></i> Menores activados');

      $(".menor").show();
    } else {
      $(this)
        .removeClass("btn-success")
        .addClass("btn-info")
        .html('<i class="ik ik-filter"></i> Ver menores');

      $(".menor").hide();
    }
  });

  $(".menor").hide();
});
/*=============================================
VER DATOS TUTOR EN MODAL
=============================================*/
$(document).on("click", ".btnVerTutor", function () {
  var idPaciente = $(this).data("id");

  $("#detalleTutor").html("<em>Cargando...</em>");

  $.ajax({
    url: "ajax/paciente.ajax.php",
    method: "GET",
    data: { accion: "getTutor", idPaciente: idPaciente },
    dataType: "json",
    success: function (respuesta) {
      if (respuesta && Object.keys(respuesta).length > 0) {
        var html = `
          <p><strong>Nombre:</strong> ${respuesta.nombrePT || "N/A"}</p>
          <p><strong>Género:</strong> ${respuesta.generoPT || "N/A"}</p>
          <p><strong>Ocupación:</strong> ${respuesta.ocupacion || "N/A"}</p>
          <p><strong>Relación:</strong> ${respuesta.relacion || "N/A"}</p>
        `;
        $("#detalleTutor").html(html);
      } else {
        $("#detalleTutor").html("<p>No hay datos del tutor.</p>");
      }
    },
    error: function () {
      $("#detalleTutor").html("<p>Error al cargar datos del tutor.</p>");
    }
  });
});
document.addEventListener("DOMContentLoaded", function() {
    const pacientes = pacientesData; // ya vienen de PHP
    const totalPacientes = pacientes.length;

    let nuevosMes = 0;
    const mesActual = new Date().getMonth() + 1;
    const anioActual = new Date().getFullYear();

    pacientes.forEach(p => {
        const fecha = new Date(p.fechaRegistro); // Cambia 'fecha' por tu campo real de registro
        if ((fecha.getMonth() + 1) === mesActual && fecha.getFullYear() === anioActual) {
            nuevosMes++;
        }
    });

    document.getElementById("total-pacientes").textContent = totalPacientes;
    document.getElementById("nuevos-mes").textContent = nuevosMes;
});
