<?php
// Obtiene las estadísticas usando el controlador
$estadisticas = ControladorInicio::ctrObtenerEstadisticas();

// Asigna las estadísticas a variables
$stats_pacientes = $estadisticas['pacientes'] ?? ['total' => 0, 'nuevos' => 0];
$stats_citas = $estadisticas['citas'] ?? ['confirmada' => 0];
$stats_tratamientos = $estadisticas['tratamiento'] ?? ['activo' => 0, 'completado' => 0, 'ingresos' => 0];

// Obtiene las citas de hoy
$citas_hoy = ControladorInicio::ctrCitasHoy(); // Llama al controlador que trae las citas de hoy
?>

<div class="wrapper">
  <div class="page-wrap">
    <div class="main-content">
      <div class="content-wrapper">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-home mr-2 text-green-600"></i>
            Inicio
          </h1>
          <p class="mt-2 text-gray-700">
            Bienvenido al sistema de gestión de la clínica dental
          </p>
        </div>

        <!-- Primera fila KPIs -->
        <section class="kpi-grid-section mb-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-3">

            <!-- Pacientes Activos -->
            <div class="bg-white shadow-sm rounded-lg p-3 transition hover:shadow-md">
              <div class="flex items-center">
                <div class="bg-blue-500 p-2 rounded-md text-white">
                  <i class="fas fa-users icon-xl"></i>
                </div>
                <div class="ml-3 flex-1">
                  <div class="text-base font-medium text-gray-500">Pacientes Activos</div>
                  <div class="flex items-center mt-1">
                    <div class="text-xl font-semibold text-gray-900"><?php echo $stats_pacientes['total']; ?></div>
                    <div class="ml-2 text-xs font-semibold text-green-600">
                      <i class="fa fa-arrow-up mr-1"></i>+<?php echo $stats_pacientes['nuevos']; ?> este mes
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Citas Hoy -->
            <div class="bg-white shadow-sm rounded-lg p-3 transition hover:shadow-md">
              <div class="flex items-center">
                <div class="bg-green-500 p-2 rounded-md text-white">
                  <i class="fa fa-calendar icon-xl"></i>
                </div>
                <div class="ml-3 flex-1">
                  <div class="text-base font-medium text-gray-500">Citas Hoy</div>
                  <div class="flex items-center mt-1">
                    <div class="text-xl font-semibold text-gray-900"><?php echo is_array($citas_hoy) ? count($citas_hoy) : 0; ?></div>
                    <div class="ml-2 text-xs font-semibold text-blue-600">
                      <i class="fa fa-calendar-check mr-1"></i><?php echo $stats_citas['confirmada']; ?> confirmadas
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tratamientos Activos -->
            <div class="bg-white shadow-sm rounded-lg p-3 transition hover:shadow-md">
              <div class="flex items-center">
                <div class="bg-purple-500 p-2 rounded-md text-white">
                  <i class="fas fa-clipboard-list icon-xl"></i>
                </div>
                <div class="ml-3 flex-1">
                  <div class="text-base font-medium text-gray-500">Tratamientos Activos</div>
                  <div class="flex items-center mt-1">
                    <div class="text-xl font-semibold text-gray-900"><?php echo $stats_tratamientos['activo']; ?></div>
                    <div class="ml-2 text-xs font-semibold text-purple-600">
                      <i class="fa fa-check mr-1"></i><?php echo $stats_tratamientos['completado']; ?> completados
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Ingresos Totales -->
            <div class="bg-white shadow-sm rounded-lg p-3 transition hover:shadow-md">
              <div class="flex items-center">
                <div class="bg-yellow-500 p-2 rounded-md text-white">
                  <i class="fa fa-dollar-sign icon-xl"></i>
                </div>
                <div class="ml-3 flex-1">
                  <div class="text-base font-medium text-gray-500">Ingresos Totales</div>
                  <div class="flex items-center mt-1">
                    <div class="text-xl font-semibold text-gray-900">Bs. <?php echo number_format($stats_tratamientos['ingresos'], 2, ',', '.'); ?></div>
                    <div class="ml-2 text-xs font-semibold text-green-600">
                      <i class="fa fa-arrow-up mr-1"></i>+15%
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </section>

        <!-- Segunda fila Citas y Acciones -->
        <section class="dashboard-actions-section">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Citas de Hoy -->
            <div class="bg-white shadow-sm rounded-lg p-5 transition hover:shadow-md">
              <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                Citas de Hoy
              </h3>

              <?php if (count($citas_hoy) > 0): ?>
                <div class="space-y-3">
                  <?php foreach ($citas_hoy as $cita_item): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                      <div class="flex items-center space-x-3">
                        <i class="fas fa-clock icon-xl text-gray-400"></i>
                        <div>
                          <p class="text-sm font-medium text-gray-900"><?php echo $cita_item["paciente_nombre"]; ?></p>
                          <p class="text-sm text-gray-600"><?php echo $cita_item["hora"]; ?> - <?php echo $cita_item["motivoConsulta"] ?? 'Consulta general'; ?></p>
                        </div>
                      </div>
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        <?php echo $cita_item["estado"] === 'confirmada' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                        <?php echo ucfirst($cita_item["estado"]); ?>
                      </span>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="text-center py-6">
                  <i class="fas fa-calendar-times text-gray-400 text-2xl mb-2"></i>
                  <p class="text-sm text-gray-500">No hay citas programadas para hoy</p>
                </div>
              <?php endif; ?>
            </div>

            <!-- Acciones Rápidas -->
            <div class="bg-white shadow-sm rounded-lg p-5 transition hover:shadow-md">
              <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-bolt mr-2 text-yellow-600"></i>
                Acciones Rápidas
              </h3>

              <div class="space-y-3">
                <a href="paciente" class="w-full flex items-center justify-between p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                  <div class="flex items-center space-x-3">
                    <i class="fas fa-user-plus text-blue-600"></i>
                    <span class="text-sm font-medium text-blue-900">Registrar Paciente</span>
                  </div>
                </a>

                <a href="citas" class="w-full flex items-center justify-between p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                  <div class="flex items-center space-x-3">
                    <i class="fas fa-calendar-plus text-green-600"></i>
                    <span class="text-sm font-medium text-green-900">Agendar Cita</span>
                  </div>
                </a>

                <a href="tratamiento" class="w-full flex items-center justify-between p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                  <div class="flex items-center space-x-3">
                    <i class="fas fa-notes-medical text-purple-600"></i>
                    <span class="text-sm font-medium text-purple-900">Iniciar Tratamiento</span>
                  </div>
                </a>

                <a href="citas/pendientes" class="w-full flex items-center justify-between p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                  <div class="flex items-center space-x-3">
                    <i class="fas fa-tasks text-yellow-600"></i>
                    <span class="text-sm font-medium text-yellow-900">Ver Citas Pendientes</span>
                  </div>
                </a>
              </div>
            </div>

          </div>
        </section>
      </div>
    </div>
  </div>
</div>