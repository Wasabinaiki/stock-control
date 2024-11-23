<?php
session_start();
require_once "includes/config.php";

// Verificar si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

// Función para registrar errores
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'error_log.txt');
}

// Obtener el número de equipos registrados hoy
$sql_equipos_hoy = "SELECT COUNT(*) as total_equipos FROM dispositivos WHERE DATE(fecha_entrega) = CURDATE()";
$result_equipos_hoy = mysqli_query($link, $sql_equipos_hoy);
if (!$result_equipos_hoy) {
    logError("Error en la consulta de equipos: " . mysqli_error($link));
}
$row_equipos_hoy = mysqli_fetch_assoc($result_equipos_hoy);
$total_equipos_hoy = $row_equipos_hoy['total_equipos'];

// Depuración: Mostrar la consulta SQL y el resultado
logError("SQL Equipos: " . $sql_equipos_hoy);
logError("Total equipos hoy: " . $total_equipos_hoy);

// Obtener todos los reportes
$sql = "SELECT r.*, d.marca, d.modelo, u.username 
        FROM reportes r 
        JOIN dispositivos d ON r.id_dispositivo = d.id_dispositivo
        JOIN usuarios u ON d.id_usuario = u.id_usuario 
        ORDER BY r.fecha_reporte DESC";

// Filtrar por tipo de mantenimiento si se proporciona
$tipo_mantenimiento = isset($_GET['tipo_mantenimiento']) ? $_GET['tipo_mantenimiento'] : '';
if ($tipo_mantenimiento) {
    $sql .= " WHERE r.tipo_mantenimiento = '" . mysqli_real_escape_string($link, $tipo_mantenimiento) . "'";
}

$result = mysqli_query($link, $sql);
if (!$result) {
    logError("Error en la consulta de reportes: " . mysqli_error($link));
}

// Depuración: Mostrar la consulta SQL de reportes
logError("SQL Reportes: " . $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-chart-bar me-2"></i>Gestión de Reportes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Gestión de Reportes</h2>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Resumen Diario</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Equipos registrados hoy:</strong> <?php echo $total_equipos_hoy; ?></p>
                        <!-- Agregar un botón para actualizar manualmente -->
                        <form method="POST">
                            <button type="submit" name="refresh" class="btn btn-sm btn-primary">Actualizar</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Filtrar por Tipo de Mantenimiento</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="GET" class="mb-3">
                            <div class="input-group">
                                <select name="tipo_mantenimiento" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="preventivo" <?php echo $tipo_mantenimiento == 'preventivo' ? 'selected' : ''; ?>>Preventivo</option>
                                    <option value="correctivo" <?php echo $tipo_mantenimiento == 'correctivo' ? 'selected' : ''; ?>>Correctivo</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Todos los Reportes</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Dispositivo</th>
                                    <th>Tipo de Mantenimiento</th>
                                    <th>Fecha de Reporte</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id_reporte']); ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['marca'] . ' ' . $row['modelo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tipo_mantenimiento']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha_reporte']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['estado_reporte'] == 'Finalizado' ? 'success' : 'warning'; ?>">
                                                <?php echo htmlspecialchars($row['estado_reporte']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="ver_reporte.php?id=<?php echo $row['id_reporte']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i>Ver
                                            </a>
                                            <a href="editar_reporte.php?id=<?php echo $row['id_reporte']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit me-1"></i>Editar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No hay reportes disponibles.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
mysqli_close($link);
?>