<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (!isset($_SESSION["id"])) {
    header("location: error.php?mensaje=Sesión inválida");
    exit;
}

$id_usuario = $_SESSION["id"];
$is_admin = isset($_SESSION["rol"]) && $_SESSION["rol"] === "administrador";

$sql = "SELECT m.*, d.tipo as tipo_dispositivo, d.marca, d.modelo 
        FROM mantenimientos m 
        JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
        WHERE d.id_usuario = ?
        ORDER BY m.fecha_programada DESC";

$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$result_mantenimientos = mysqli_stmt_get_result($stmt);

$sql_stats = "SELECT m.estado, COUNT(*) as total 
             FROM mantenimientos m 
             JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
             WHERE d.id_usuario = ?
             GROUP BY m.estado";

$stmt_stats = mysqli_prepare($link, $sql_stats);
mysqli_stmt_bind_param($stmt_stats, "i", $id_usuario);
mysqli_stmt_execute($stmt_stats);
$result_stats = mysqli_stmt_get_result($stmt_stats);

$stats = [
    'programado' => 0,
    'en_proceso' => 0,
    'completado' => 0
];

while ($row = mysqli_fetch_assoc($result_stats)) {
    $stats[$row['estado']] = $row['total'];
}

function formatearEstado($estado)
{
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
    <title>Mis Mantenimientos</title>
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

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .table {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333 !important;
            border: none;
            font-weight: bold;
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

        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }

        .estado-programado,
        .programado {
            color: #ffc107;
            font-weight: bold;
        }

        .estado-en_proceso,
        .en_proceso {
            color: #0d6efd;
            font-weight: bold;
        }

        .estado-completado,
        .completado {
            color: #198754;
            font-weight: bold;
        }

        .refresh-btn {
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .refresh-btn:hover {
            transform: rotate(180deg);
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table th,
        .table td {
            vertical-align: middle;
            padding: 12px 15px;
        }

        .table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .filter-container {
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .filter-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
        }

        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-tools me-2"></i>Mantenimientos</a>
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
            <h2><i class="fas fa-tools me-2"></i>Mis Mantenimientos Programados</h2>
            <span class="refresh-btn" id="refreshData" title="Actualizar datos">
                <i class="fas fa-sync-alt fa-2x text-primary"></i>
            </span>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Resumen de Mantenimientos</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="mantenimientosChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <a href="programar_mantenimiento.php" class="btn btn-primary">
                <i class="fas fa-calendar-plus me-2"></i>Programar Mantenimiento
            </a>
        </div>

        <div class="filter-container">
            <div class="row">
                <div class="col-md-4">
                    <label for="filtroEstado" class="filter-label">Filtrar por estado:</label>
                    <select class="form-select" id="filtroEstado">
                        <option value="">Todos</option>
                        <option value="programado">Programado</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="completado">Completado</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="section-header">
            <i class="fas fa-list me-2"></i> Lista de Mantenimientos
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Dispositivo</th>
                        <th>Fecha Programada</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result_mantenimientos) > 0) {
                        while ($row = mysqli_fetch_assoc($result_mantenimientos)) {
                            echo "<tr class='fila-mantenimiento' data-estado='" . $row['estado'] . "'>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tipo_dispositivo'] . ' ' . $row['marca'] . ' ' . $row['modelo']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['fecha_programada']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['descripcion']) . "</td>";
                            echo "<td><span class='" . $row['estado'] . "'>" . formatearEstado($row['estado']) . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No tienes mantenimientos programados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const ctxMantenimientos = document.getElementById('mantenimientosChart').getContext('2d');
        const mantenimientosChart = new Chart(ctxMantenimientos, {
            type: 'doughnut',
            data: {
                labels: ['Programados', 'En Proceso', 'Completados'],
                datasets: [{
                    data: [
                        <?php echo $stats['programado']; ?>,
                        <?php echo $stats['en_proceso']; ?>,
                        <?php echo $stats['completado']; ?>
                    ],
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

        document.getElementById('filtroEstado').addEventListener('change', function () {
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

        document.getElementById('refreshData').addEventListener('click', function () {
            this.classList.add('fa-spin');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
    </script>
</body>

</html>