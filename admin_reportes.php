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

// Función para formatear el estado
function formatearEstado($estado) {
    // Primero convertir a minúsculas y reemplazar guiones bajos por espacios
    $estado = strtolower(str_replace('_', ' ', $estado));
    // Capitalizar la primera letra de cada palabra
    return ucwords($estado);
}

// Filtrar mantenimientos por estado si se proporciona
$estado_mantenimiento = isset($_GET['estado_mantenimiento']) ? $_GET['estado_mantenimiento'] : '';

// Obtener todos los mantenimientos con información de dispositivos y usuarios
$sql = "SELECT m.id, m.id_dispositivo, m.fecha_programada, m.descripcion, m.estado, 
               d.marca, d.modelo, d.tipo as tipo_dispositivo, u.username 
        FROM mantenimientos m 
        JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
        JOIN usuarios u ON d.id_usuario = u.id_usuario";

// Aplicar filtro si se seleccionó un estado
if ($estado_mantenimiento) {
    $sql .= " WHERE m.estado = '" . mysqli_real_escape_string($link, $estado_mantenimiento) . "'";
}

$sql .= " ORDER BY m.fecha_programada DESC";

$result = mysqli_query($link, $sql);
if (!$result) {
    logError("Error en la consulta de mantenimientos: " . mysqli_error($link));
}

// Obtener todos los dispositivos
$sql_dispositivos = "SELECT d.*, u.username FROM dispositivos d 
                    JOIN usuarios u ON d.id_usuario = u.id_usuario 
                    ORDER BY d.fecha_entrega DESC";
$result_dispositivos = mysqli_query($link, $sql_dispositivos);
if (!$result_dispositivos) {
    logError("Error en la consulta de dispositivos: " . mysqli_error($link));
}

// NUEVA SECCIÓN: Obtener todos los PQRs
$sql_pqrs = "SELECT p.*, u.username 
            FROM pqrs p 
            JOIN usuarios u ON p.id_usuario = u.id_usuario 
            ORDER BY p.fecha_creacion DESC";
$result_pqrs = mysqli_query($link, $sql_pqrs);
if (!$result_pqrs) {
    logError("Error en la consulta de PQRs: " . mysqli_error($link));
}

// NUEVA SECCIÓN: Obtener todos los formularios de contacto
$sql_contactos = "SELECT c.* FROM contactos c ORDER BY c.fecha DESC";
$result_contactos = mysqli_query($link, $sql_contactos);
if (!$result_contactos) {
    logError("Error en la consulta de contactos: " . mysqli_error($link));
}

// NUEVA SECCIÓN: Obtener todos los dispositivos en Bodega (como sustituto de bodega)
$sql_envios = "SELECT e.*, d.marca, d.modelo, d.tipo, u.username 
              FROM envios e
              JOIN dispositivos d ON e.usuario_id = d.id_usuario
              JOIN usuarios u ON e.usuario_id = u.id_usuario
              ORDER BY e.fecha_envio DESC";
$result_envios = mysqli_query($link, $sql_envios);
if (!$result_envios) {
    logError("Error en la consulta de envíos: " . mysqli_error($link));
}
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
        .dashboard-link {
            color: white !important;
            border-radius: 5px;
            padding: 8px 15px !important;
            margin-right: 10px;
        }
        .filter-form {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .filter-form select {
            max-width: 200px;
            margin-right: 10px;
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
        <h2 class="mb-4">Gestión de Reportes</h2>
        
        <!-- Mantenimientos Programados con filtro por estado -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Mantenimientos Programados</h5>
                <form action="" method="GET" class="filter-form">
                    <select name="estado_mantenimiento" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="completado" <?php echo $estado_mantenimiento == 'completado' ? 'selected' : ''; ?>>Completado</option>
                        <option value="en_proceso" <?php echo $estado_mantenimiento == 'en_proceso' ? 'selected' : ''; ?>>En proceso</option>
                        <option value="programado" <?php echo $estado_mantenimiento == 'programado' ? 'selected' : ''; ?>>Programado</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </form>
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
                                    <th>Tipo</th>
                                    <th>Fecha Programada</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['marca'] . ' ' . $row['modelo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tipo_dispositivo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha_programada']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                if ($row['estado'] == 'completado') {
                                                    echo 'success';
                                                } elseif ($row['estado'] == 'en_proceso') {
                                                    echo 'warning';
                                                } else {
                                                    echo 'info';
                                                }
                                            ?>">
                                                <?php echo formatearEstado($row['estado']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No hay mantenimientos disponibles.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Dispositivos Registrados -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Dispositivos Registrados</h5>
            </div>
            <div class="card-body">
                <?php if ($result_dispositivos && mysqli_num_rows($result_dispositivos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Tipo</th>
                                    <th>Marca/Modelo</th>
                                    <th>Fecha de Entrega</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_dispositivos)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id_dispositivo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['marca'] . ' ' . $row['modelo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha_entrega']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['estado'] == 'Activo' ? 'success' : 'warning'; ?>">
                                                <?php echo htmlspecialchars($row['estado']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No hay dispositivos disponibles.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- NUEVA SECCIÓN: PQRs Registrados -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">PQRs Registrados</h5>
            </div>
            <div class="card-body">
                <?php if ($result_pqrs && mysqli_num_rows($result_pqrs) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_pqrs)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($row['descripcion'], 0, 50)) . (strlen($row['descripcion']) > 50 ? '...' : ''); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha_creacion']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                if ($row['estado'] == 'resuelto') {
                                                    echo 'success';
                                                } elseif ($row['estado'] == 'en_proceso' || $row['estado'] == 'en proceso') {
                                                    echo 'warning';
                                                } else {
                                                    echo 'info';
                                                }
                                            ?>">
                                                <?php echo formatearEstado($row['estado'] ? $row['estado'] : 'pendiente'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No hay PQRs registrados.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- NUEVA SECCIÓN: Formularios de Contacto -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Formularios de Contacto</h5>
            </div>
            <div class="card-body">
                <?php if ($result_contactos && mysqli_num_rows($result_contactos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Asunto</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_contactos)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['asunto']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                if ($row['estado'] == 'Resuelto') {
                                                    echo 'success';
                                                } elseif ($row['estado'] == 'En proceso') {
                                                    echo 'warning';
                                                } else {
                                                    echo 'info';
                                                }
                                            ?>">
                                                <?php echo formatearEstado($row['estado']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No hay formularios de contacto registrados.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- NUEVA SECCIÓN: Dispositivos en Bodega (sustituto de bodega) -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Dispositivos en Bodega</h5>
            </div>
            <div class="card-body">
                <?php if ($result_envios && mysqli_num_rows($result_envios) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Destino</th>
                                    <th>Fecha de Envío</th>
                                    <th>Fecha de Salida</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_envios)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id_envio']); ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['direccion_destino']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha_envio']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha_salida']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['estado_envio'] == 'Completado' ? 'success' : 'warning'; ?>">
                                                <?php echo formatearEstado($row['estado_envio']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No hay dispositivos en Bodega.</p>
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