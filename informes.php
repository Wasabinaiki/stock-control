<?php
session_start();
require_once "includes/config.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Verificar si id_usuario está definido en la sesión
if (!isset($_SESSION["id"])) {
    header("location: error.php?mensaje=Sesión inválida");
    exit;
}

$id_usuario = $_SESSION["id"];

// Obtener información del usuario
$sql_usuario = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt_usuario = mysqli_prepare($link, $sql_usuario);
mysqli_stmt_bind_param($stmt_usuario, "i", $id_usuario);
mysqli_stmt_execute($stmt_usuario);
$result_usuario = mysqli_stmt_get_result($stmt_usuario);
$usuario = mysqli_fetch_assoc($result_usuario);

// Obtener dispositivos del usuario
$sql_dispositivos = "SELECT * FROM dispositivos WHERE id_usuario = ?";
$stmt_dispositivos = mysqli_prepare($link, $sql_dispositivos);
mysqli_stmt_bind_param($stmt_dispositivos, "i", $id_usuario);
mysqli_stmt_execute($stmt_dispositivos);
$result_dispositivos = mysqli_stmt_get_result($stmt_dispositivos);

// Contar tipos de dispositivos para el nuevo gráfico
$sql_tipos = "SELECT tipo, COUNT(*) as total FROM dispositivos WHERE id_usuario = ? GROUP BY tipo";
$stmt_tipos = mysqli_prepare($link, $sql_tipos);
mysqli_stmt_bind_param($stmt_tipos, "i", $id_usuario);
mysqli_stmt_execute($stmt_tipos);
$result_tipos = mysqli_stmt_get_result($stmt_tipos);
$tipos_labels = [];
$tipos_data = [];
while ($row = mysqli_fetch_assoc($result_tipos)) {
    $tipos_labels[] = ucfirst($row['tipo']); // Capitalizar primera letra
    $tipos_data[] = $row['total'];
}

// Obtener mantenimientos del usuario
$sql_mantenimientos = "SELECT m.*, d.tipo, d.marca, d.modelo 
                       FROM mantenimientos m 
                       JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo 
                       WHERE d.id_usuario = ? 
                       ORDER BY m.fecha_programada DESC";
$stmt_mantenimientos = mysqli_prepare($link, $sql_mantenimientos);
mysqli_stmt_bind_param($stmt_mantenimientos, "i", $id_usuario);
mysqli_stmt_execute($stmt_mantenimientos);
$result_mantenimientos = mysqli_stmt_get_result($stmt_mantenimientos);

// Función para formatear el estado
function formatearEstado($estado) {
    switch ($estado) {
        case 'programado':
            return 'Programado';
        case 'en_proceso':
            return 'En proceso';
        case 'completado':
            return 'Completado';
        default:
            return ucfirst($estado);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Informes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            border-radius: 10px 10px 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg,rgb(234, 102, 102) 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }
        .estado-programado {
            color: #ffc107;
            font-weight: bold;
        }
        .estado-en_proceso {
            color: #0d6efd;
            font-weight: bold;
        }
        .estado-completado {
            color: #198754;
            font-weight: bold;
        }
        .nav-tabs .nav-link {
            color: #000000 !important; /* Cambiado a negro */
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            color: #000000 !important; /* Cambiado a negro */
            font-weight: bold;
            border-color: #667eea #667eea #fff;
        }
        /* Cambiamos el color del texto en los encabezados de tabla */
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333 !important; /* Cambiado a gris oscuro */
            border: none;
            font-weight: bold;
        }
        .refresh-btn {
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .refresh-btn:hover {
            transform: rotate(180deg);
        }
        /* Aseguramos que los íconos en las pestañas sean visibles */
        .nav-tabs .nav-link i {
            color: #000000 !important; /* Cambiado a negro */
        }
        .nav-tabs .nav-link.active i {
            color: #000000 !important; /* Cambiado a negro */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-file-alt me-2"></i>Mis Informes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-chart-line me-2"></i>Mis Informes</h2>
            <span class="refresh-btn" id="refreshData" title="Actualizar datos">
                <i class="fas fa-sync-alt fa-2x text-primary"></i>
            </span>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tipos de Dispositivos</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="tiposDispositivos"></canvas>
                        </div>
                        <div class="mt-3">
                            <p class="text-muted small">
                                <strong>Nota:</strong> Este gráfico muestra la distribución de tus dispositivos por tipo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Resumen de Mantenimientos</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="mantenimientosChart"></canvas>
                        </div>
                        <div class="mt-3">
                            <p class="text-muted small">
                                <strong>Nota:</strong> Este gráfico muestra el estado de los mantenimientos de tus dispositivos.
                                <ul class="small">
                                    <li><span class="estado-programado">Programado</span>: Mantenimiento agendado</li>
                                    <li><span class="estado-en_proceso">En Proceso</span>: Mantenimiento en ejecución</li>
                                    <li><span class="estado-completado">Completado</span>: Mantenimiento finalizado</li>
                                </ul>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs mt-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="dispositivos-tab" data-bs-toggle="tab" data-bs-target="#dispositivos" type="button" role="tab" aria-controls="dispositivos" aria-selected="true">
                    <i class="fas fa-laptop me-2"></i>Mis Dispositivos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mantenimientos-tab" data-bs-toggle="tab" data-bs-target="#mantenimientos" type="button" role="tab" aria-controls="mantenimientos" aria-selected="false">
                    <i class="fas fa-tools me-2"></i>Historial de Mantenimientos
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="dispositivos" role="tabpanel" aria-labelledby="dispositivos-tab">
                <div class="card border-top-0 rounded-0 rounded-bottom">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="dispositivosTable">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Fecha Entrega</th>
                                        <th>Última Actualización</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    mysqli_data_seek($result_dispositivos, 0);
                                    if (mysqli_num_rows($result_dispositivos) > 0) {
                                        while ($dispositivo = mysqli_fetch_assoc($result_dispositivos)) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($dispositivo['tipo']) . "</td>";
                                            echo "<td>" . htmlspecialchars($dispositivo['marca']) . "</td>";
                                            echo "<td>" . htmlspecialchars($dispositivo['modelo']) . "</td>";
                                            echo "<td>" . htmlspecialchars($dispositivo['fecha_entrega']) . "</td>";
                                            echo "<td>" . date('Y-m-d', strtotime($dispositivo['fecha_entrega'])) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>No tienes dispositivos registrados.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="mantenimientos" role="tabpanel" aria-labelledby="mantenimientos-tab">
                <div class="card border-top-0 rounded-0 rounded-bottom">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="filtroEstado" class="form-label">Filtrar por estado:</label>
                            <select class="form-select" id="filtroEstado">
                                <option value="">Todos</option>
                                <option value="programado">Programado</option>
                                <option value="en_proceso">En Proceso</option>
                                <option value="completado">Completado</option>
                            </select>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="mantenimientosTable">
                                <thead>
                                    <tr>
                                        <th>Dispositivo</th>
                                        <th>Descripción</th>
                                        <th>Fecha Programada</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result_mantenimientos) > 0) {
                                        while ($mantenimiento = mysqli_fetch_assoc($result_mantenimientos)) {
                                            echo "<tr class='fila-mantenimiento' data-estado='" . $mantenimiento['estado'] . "'>";
                                            echo "<td>" . htmlspecialchars($mantenimiento['tipo'] . ' ' . $mantenimiento['marca'] . ' ' . $mantenimiento['modelo']) . "</td>";
                                            echo "<td>" . htmlspecialchars($mantenimiento['descripcion']) . "</td>";
                                            echo "<td>" . htmlspecialchars($mantenimiento['fecha_programada']) . "</td>";
                                            echo "<td><span class='estado-" . $mantenimiento['estado'] . "'>" . formatearEstado($mantenimiento['estado']) . "</span></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center'>No tienes mantenimientos registrados.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Nuevo gráfico de tipos de dispositivos
        const ctxTipos = document.getElementById('tiposDispositivos').getContext('2d');
        const tiposChart = new Chart(ctxTipos, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($tipos_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($tipos_data); ?>,
                    backgroundColor: [
                        '#004dff',
                        '#ff006c',
                        '#b7f83c',
                        '#f742ee',
                        '#283bb3',
                        '#20c997',
                        '#fd7e14'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Distribución por Tipo'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de mantenimientos
        const ctxMantenimientos = document.getElementById('mantenimientosChart').getContext('2d');
        
        // Contar mantenimientos por estado
        let programados = 0;
        let enProceso = 0;
        let completados = 0;
        
        document.querySelectorAll('.fila-mantenimiento').forEach(fila => {
            const estado = fila.getAttribute('data-estado');
            if (estado === 'programado') programados++;
            else if (estado === 'en_proceso') enProceso++;
            else if (estado === 'completado') completados++;
        });
        
        const mantenimientosChart = new Chart(ctxMantenimientos, {
            type: 'bar',
            data: {
                labels: ['Programados', 'En Proceso', 'Completados'],
                datasets: [{
                    label: 'Cantidad',
                    data: [programados, enProceso, completados],
                    backgroundColor: [
                        '#ffc107',
                        '#0d6efd',
                        '#198754'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Mantenimientos por Estado'
                    }
                }
            }
        });

        // Filtro de mantenimientos
        document.getElementById('filtroEstado').addEventListener('change', function() {
            const filtro = this.value;
            const filas = document.querySelectorAll('.fila-mantenimiento');
            
            filas.forEach(fila => {
                if (filtro === '' || fila.getAttribute('data-estado') === filtro) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });

        // Actualización en tiempo real
        document.getElementById('refreshData').addEventListener('click', function() {
            this.classList.add('fa-spin');
            
            // Simular actualización con AJAX
            setTimeout(() => {
                // En producción, aquí iría una llamada AJAX real
                $.ajax({
                    url: 'get_informes_data.php',
                    type: 'GET',
                    data: { user_id: <?php echo $id_usuario; ?> },
                    success: function(response) {
                        // Actualizar los datos con la respuesta
                        // En este ejemplo solo detenemos la animación
                        document.getElementById('refreshData').classList.remove('fa-spin');
                        
                        // Mostrar notificación de actualización
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show';
                        alertDiv.innerHTML = `
                            <i class="fas fa-check-circle me-2"></i>Datos actualizados correctamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.container').firstChild);
                    },
                    error: function() {
                        document.getElementById('refreshData').classList.remove('fa-spin');
                        
                        // Mostrar error
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                        alertDiv.innerHTML = `
                            <i class="fas fa-exclamation-circle me-2"></i>Error al actualizar los datos.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.container').firstChild);
                    }
                });
            }, 1000);
        });
    </script>
</body>
</html>

