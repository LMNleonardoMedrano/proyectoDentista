$(document).ready(function(){

  // =======================
  // Autocompletar fechas con hoy
  // =======================
  const hoy = new Date().toISOString().split('T')[0];
  $('input[type="date"]').val(hoy);

  // =======================
  // Función genérica para cargar reportes
  // =======================
  function cargarReporte({formId, contenedorId, selectId, pdfBtnId, ajaxUrl, fechaDesdeId, fechaHastaId}) {
    const tipo = $(`#${selectId}`).val();
    const desde = $(`#${fechaDesdeId}`).val();
    const hasta = $(`#${fechaHastaId}`).val();

    if (!tipo) {
      $(`#${contenedorId}`).html('<div class="col-12"><div class="alert alert-info text-center">Seleccione un reporte.</div></div>');
      return;
    }

    $.ajax({
      url: ajaxUrl,
      method: 'GET',
      data: { [selectId]: tipo, [fechaDesdeId]: desde, [fechaHastaId]: hasta },
      beforeSend: function(){
        $(`#${contenedorId}`).html('<div class="col-12 text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Cargando...</div>');
      },
      success: function(data){
        $(`#${contenedorId}`).html(data);
        $(`#${pdfBtnId}`).attr('href', `${pdfBtnId.replace('btn','vistas/modulos/reportes')}.php?${selectId}=${tipo}&${fechaDesdeId}=${desde}&${fechaHastaId}=${hasta}`);
      },
      error: function(){
        $(`#${contenedorId}`).html('<div class="col-12"><div class="alert alert-danger text-center">Error al cargar los datos.</div></div>');
      }
    });
  }

  // =======================
  // Manejar submit y cambio de select para cada formulario
  // =======================
  const reportes = [
    {
      formId: 'formReportesPagos',
      contenedorId: 'contenedorPagos',
      selectId: 'tipoReporte',
      pdfBtnId: 'btnPDF',
      ajaxUrl: '/dentista/ajax/reportesPagos.ajax.php',
      fechaDesdeId: 'desde',
      fechaHastaId: 'hasta',
      inputBuscarId: 'buscarPaciente'
    },
    {
      formId: 'formReportesCitas',
      contenedorId: 'contenedorCitas',
      selectId: 'tipoReporteCitas',
      pdfBtnId: 'btnPDFCitas',
      ajaxUrl: '/dentista/ajax/reportesCitas.ajax.php',
      fechaDesdeId: 'desdeCitas',
      fechaHastaId: 'hastaCitas',
      inputBuscarId: 'buscarCita'
    },
    {
      formId: 'formReportesTratamientos',
      contenedorId: 'contenedorTratamientos',
      selectId: 'tipoReporteTratamientos',
      pdfBtnId: 'btnPDFTratamientos',
      ajaxUrl: '/dentista/ajax/reportesTratamientos.ajax.php',
      fechaDesdeId: 'desdeTrat',
      fechaHastaId: 'hastaTrat',
      inputBuscarId: 'buscarTratamiento'
    }
  ];

  reportes.forEach(rep => {
    // Submit del formulario
    $(`#${rep.formId}`).submit(function(e){
      e.preventDefault();
      cargarReporte(rep);
    });
    // Cambio de select
    $(`#${rep.selectId}`).change(function(){ cargarReporte(rep); });
    // Buscador
    $(`#${rep.inputBuscarId}`).on('keyup', function(){
      const filtro = $(this).val().toLowerCase();
      $(`#${rep.contenedorId} .col-12, #${rep.contenedorId} .col-md-6, #${rep.contenedorId} .col-lg-4`).each(function(){
        $(this).toggle($(this).text().toLowerCase().includes(filtro));
      });
    });
  });

  // =======================
  // Función global opcional
  // =======================
  window.verPagosPaciente = function(idPaciente){
    $('#contenedorPagos').html("<div class='col-12 text-center'><div class='spinner-border text-primary'></div></div>");
    fetch(`/dentista/vistas/modulos/reportesPagos.php?tipoReporte=porPaciente&idPaciente=${idPaciente}`)
      .then(res => res.text())
      .then(html => $('#contenedorPagos').html(html))
      .catch(err => $('#contenedorPagos').html("<div class='col-12'><div class='alert alert-danger text-center'>Error al cargar pagos.</div></div>"));
  };

});
