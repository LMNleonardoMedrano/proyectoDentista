$(document).ready(function() {
  var table = $('#data_table').DataTable({
    scrollX: true,         // Permite scroll horizontal
    responsive: false,     // Desactivamos responsive para evitar ocultar columnas
    select: true,
    columnDefs: [{
      targets: 'nosort',   // Aplica esta clase para columnas no ordenables si las tienes
      orderable: false
    }]
  });

  $('#data_table tbody').on('click', 'tr', function() {
    if ($(this).hasClass('selected')) {
      $(this).removeClass('selected');
    } else {
      table.$('tr.selected').removeClass('selected');
      $(this).addClass('selected');
    }
  });

  // Opcional: inicialización de otra tabla advanced_table si la usas
  $("#advanced_table").DataTable({
    responsive: true,
    select: true,
    columnDefs: [{
      targets: 'nosort',
      orderable: false
    }]
  });

  // Filtros (si los tienes)
  $('input.global_filter').on('keyup click', function() {
    filterGlobal();
  });

  $('input.column_filter').on('keyup click', function() {
    filterColumn($(this).attr('data-column'));
  });
});
