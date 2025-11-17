$(document).ready(function() {
  var table = $('#data_table').DataTable({
    scrollX: true,         // Permite scroll horizontal
    responsive: false,     // Desactivamos responsive para evitar ocultar columnas
    select: true,
    pageLength: 25, // 👈 Esta línea define que se muestren 25 registros por página
    lengthMenu: [10, 25, 50, 100],
    columnDefs: [{
      targets: 'nosort',   // Aplica esta clase para columnas no ordenables si las tienes
      orderable: false
    }],
    language: {
      decimal: ",",
      thousands: ".",
      processing: "Procesando...",
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ registros",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoEmpty: "Mostrando 0 a 0 de 0 registros",
      infoFiltered: "(filtrado de _MAX_ registros totales)",
      loadingRecords: "Cargando...",
      zeroRecords: "No se encontraron resultados",
      emptyTable: "No hay datos disponibles en la tabla",
      paginate: {
        first: "Primero",
        previous: "Anterior",
        next: "Siguiente",
        last: "Último"
      },
      aria: {
        sortAscending: ": activar para ordenar ascendente",
        sortDescending: ": activar para ordenar descendente"
      }
    }
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