<?php
session_start();
require_once "includes/config.php";

// Verificar si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

// Función para formatear el estado
function formatearEstado($estado)
{
    // Primero convertir a minúsculas y reemplazar guiones bajos por espacios
    $estado = strtolower(str_replace('_', ' ', $estado));
    // Capitalizar la primera letra de cada palabra
    return ucwords($estado);
}

// Obtener estadísticas generales
$sql_usuarios = "SELECT COUNT(*) as total FROM usuarios";
$result_usuarios = mysqli_query($link, $sql_usuarios);
$total_usuarios = mysqli_fetch_assoc($result_usuarios)['total'];

$sql_dispositivos = "SELECT COUNT(*) as total FROM dispositivos";
$result_dispositivos = mysqli_query($link, $sql_dispositivos);
$total_dispositivos = mysqli_fetch_assoc($result_dispositivos)['total'];

$sql_mantenimientos = "SELECT COUNT(*) as total FROM mantenimientos";
$result_mantenimientos = mysqli_query($link, $sql_mantenimientos);
$total_mantenimientos = mysqli_fetch_assoc($result_mantenimientos)['total'];

// Obtener estadísticas de dispositivos por tipo
$sql_tipos = "SELECT tipo, COUNT(*) as total FROM dispositivos GROUP BY tipo";
$result_tipos = mysqli_query($link, $sql_tipos);
$tipos_labels = [];
$tipos_data = [];
while ($row = mysqli_fetch_assoc($result_tipos)) {
    $tipos_labels[] = $row['tipo'];
    $tipos_data[] = $row['total'];
}

// Obtener estadísticas de mantenimientos por estado
$sql_estados = "SELECT estado, COUNT(*) as total FROM mantenimientos GROUP BY estado";
$result_estados = mysqli_query($link, $sql_estados);
$estados_labels = [];
$estados_data = [];
$estados_colors = [];

// Colores para los estados
$color_map = [
    'programado' => '#ffc107', // Amarillo
    'en_proceso' => '#0d6efd', // Azul
    'completado' => '#198754'  // Verde
];

while ($row = mysqli_fetch_assoc($result_estados)) {
    $estados_labels[] = formatearEstado($row['estado']);
    $estados_data[] = $row['total'];
    // Asignar color según el estado
    $estados_colors[] = isset($color_map[$row['estado']]) ? $color_map[$row['estado']] : '#6c757d';
}

// Obtener los últimos 5 dispositivos registrados
$sql_ultimos = "SELECT d.*, u.username FROM dispositivos d 
                JOIN usuarios u ON d.id_usuario = u.id_usuario 
                ORDER BY d.fecha_entrega DESC LIMIT 5";
$result_ultimos = mysqli_query($link, $sql_ultimos);

// Obtener los próximos 5 mantenimientos programados
$sql_proximos = "SELECT m.*, d.tipo, d.marca, d.modelo, u.username 
                FROM mantenimientos m 
                JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo 
                JOIN usuarios u ON d.id_usuario = u.id_usuario 
                WHERE m.estado = 'programado' 
                ORDER BY m.fecha_programada ASC LIMIT 5";
$result_proximos = mysqli_query($link, $sql_proximos);

// Obtener estadísticas adicionales para métricas de rendimiento
// 1. Dispositivos por marca
$sql_marcas = "SELECT marca, COUNT(*) as total FROM dispositivos GROUP BY marca ORDER BY total DESC LIMIT 5";
$result_marcas = mysqli_query($link, $sql_marcas);
$marcas_data = [];
while ($row = mysqli_fetch_assoc($result_marcas)) {
    $marcas_data[$row['marca']] = $row['total'];
}

// 2. Usuarios más activos (con más dispositivos)
$sql_usuarios_activos = "SELECT u.username, COUNT(d.id_dispositivo) as total 
                        FROM usuarios u 
                        LEFT JOIN dispositivos d ON u.id_usuario = d.id_usuario 
                        GROUP BY u.id_usuario 
                        ORDER BY total DESC 
                        LIMIT 5";
$result_usuarios_activos = mysqli_query($link, $sql_usuarios_activos);
$usuarios_activos = [];
while ($row = mysqli_fetch_assoc($result_usuarios_activos)) {
    $usuarios_activos[$row['username']] = $row['total'];
}

// 3. Eficiencia de mantenimientos (% completados vs programados)
$sql_eficiencia = "SELECT 
                    SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as completados,
                    COUNT(*) as total
                    FROM mantenimientos";
$result_eficiencia = mysqli_query($link, $sql_eficiencia);
$eficiencia_data = mysqli_fetch_assoc($result_eficiencia);
$eficiencia = ($eficiencia_data['total'] > 0) ?
    round(($eficiencia_data['completados'] / $eficiencia_data['total']) * 100, 1) : 0;

// 4. Tasa de dispositivos activos
$sql_activos = "SELECT COUNT(*) as total FROM dispositivos WHERE estado = 'activo' OR estado = 'disponible'";
$result_activos = mysqli_query($link, $sql_activos);
$dispositivos_activos = mysqli_fetch_assoc($result_activos)['total'];
$tasa_activos = $total_dispositivos > 0 ? round(($dispositivos_activos / $total_dispositivos) * 100, 1) : 0;

// 5. Mantenimientos pendientes
$sql_pendientes = "SELECT COUNT(*) as total FROM mantenimientos WHERE estado = 'programado'";
$result_pendientes = mysqli_query($link, $sql_pendientes);
$mantenimientos_pendientes = mysqli_fetch_assoc($result_pendientes)['total'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informes Administrativos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .navbar-brand,
        .nav-link {
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333 !important;
            /* Cambiado a gris oscuro para mejor legibilidad */
            border: none;
        }

        .stat-card {
            text-align: center;
            padding: 20px;
        }

        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #667eea;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-card .stat-label {
            font-size: 1rem;
            color: #6c757d;
        }

        .metric-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .metric-card .metric-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .metric-card .metric-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .metric-card .metric-label {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .progress {
            height: 10px;
            margin-top: 10px;
        }

        .dashboard-link {
            color: white !important;
            border-radius: 5px;
            padding: 8px 15px !important;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-chart-line me-2"></i>Informes Administrativos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link dashboard-link" href="admin_dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard Administrador
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4"><i class="fas fa-chart-line me-2"></i>Informes y Estadísticas</h2>

        <!-- Estadísticas Generales -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-value"><?php echo $total_usuarios; ?></div>
                    <div class="stat-label">Usuarios Registrados</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <i class="fas fa-laptop"></i>
                    <div class="stat-value"><?php echo $total_dispositivos; ?></div>
                    <div class="stat-label">Dispositivos Registrados</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <i class="fas fa-tools"></i>
                    <div class="stat-value"><?php echo $total_mantenimientos; ?></div>
                    <div class="stat-label">Mantenimientos Programados</div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Dispositivos por Tipo</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="tiposChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Mantenimientos por Estado</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="estadosChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métricas de Rendimiento -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Métricas de Rendimiento</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="metric-card">
                                    <div class="metric-icon text-primary">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="metric-value"><?php echo $eficiencia; ?>%</div>
                                    <div class="metric-label">Eficiencia de Mantenimientos</div>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            style="width: <?php echo $eficiencia; ?>%"
                                            aria-valuenow="<?php echo $eficiencia; ?>" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- MÉTRICA ACTUALIZADA 1: Dispositivos Activos en lugar de Dispositivos por Usuario -->
                            <div class="col-md-3 mb-3">
                                <div class="metric-card">
                                    <div class="metric-icon text-success">
                                        <i class="fas fa-laptop-code"></i>
                                    </div>
                                    <div class="metric-value"><?php echo $tasa_activos; ?>%</div>
                                    <div class="metric-label">Dispositivos Activos</div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: <?php echo $tasa_activos; ?>%"
                                            aria-valuenow="<?php echo $tasa_activos; ?>" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="metric-card">
                                    <div class="metric-icon text-warning">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="metric-value">
                                        <?php
                                        $sql_mant_mes = "SELECT COUNT(*) as total FROM mantenimientos WHERE MONTH(fecha_programada) = MONTH(CURRENT_DATE())";
                                        $result_mant_mes = mysqli_query($link, $sql_mant_mes);
                                        echo mysqli_fetch_assoc($result_mant_mes)['total'];
                                        ?>
                                    </div>
                                    <div class="metric-label">Mantenimientos este Mes</div>
                                </div>
                            </div>

                            <!-- MÉTRICA ACTUALIZADA 2: Mantenimientos Pendientes en lugar de Tiempo Promedio de Mantenimiento -->
                            <div class="col-md-3 mb-3">
                                <div class="metric-card">
                                    <div class="metric-icon text-info">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    <div class="metric-value"><?php echo $mantenimientos_pendientes; ?></div>
                                    <div class="metric-label">Mantenimientos Pendientes</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="metric-card">
                                    <h6 class="mb-3">Marcas más Populares</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Marca</th>
                                                    <th>Cantidad</th>
                                                    <th>Porcentaje</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($marcas_data as $marca => $cantidad): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($marca); ?></td>
                                                        <td><?php echo $cantidad; ?></td>
                                                        <td><?php echo round(($cantidad / $total_dispositivos) * 100, 1); ?>%
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="metric-card">
                                    <h6 class="mb-3">Usuarios con más Dispositivos</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Usuario</th>
                                                    <th>Dispositivos</th>
                                                    <th>Porcentaje</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($usuarios_activos as $username => $cantidad): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($username); ?></td>
                                                        <td><?php echo $cantidad; ?></td>
                                                        <td><?php echo round(($cantidad / $total_dispositivos) * 100, 1); ?>%
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tablas de Datos -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Últimos Dispositivos Registrados</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Marca/Modelo</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result_ultimos) > 0) {
                                        while ($row = mysqli_fetch_assoc($result_ultimos)) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['marca'] . ' ' . $row['modelo']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['fecha_entrega']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center'>No hay dispositivos registrados</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Próximos Mantenimientos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Dispositivo</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result_proximos) > 0) {
                                        while ($row = mysqli_fetch_assoc($result_proximos)) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['tipo'] . ' ' . $row['marca'] . ' ' . $row['modelo']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['fecha_programada']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='text-center'>No hay mantenimientos programados</td></tr>";
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
        // Gráfico de tipos de dispositivos
        const ctxTipos = document.getElementById('tiposChart').getContext('2d');
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
                            label: function (context) {
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

        // Gráfico de estados de mantenimientos
        const ctxEstados = document.getElementById('estadosChart').getContext('2d');
        const estadosChart = new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($estados_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($estados_data); ?>,
                    backgroundColor: <?php echo json_encode($estados_colors); ?>,
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
                        text: 'Mantenimientos por Estado'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
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
    </script>
</body>

</html>