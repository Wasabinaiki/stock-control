<?php
session_start();
require_once "includes/config.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Verificar si id está definido en la sesión
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

// Obtener los dispositivos del usuario
$sql_dispositivos = "SELECT * FROM dispositivos WHERE id_usuario = ? ORDER BY fecha_entrega DESC";
$stmt_dispositivos = mysqli_prepare($link, $sql_dispositivos);
mysqli_stmt_bind_param($stmt_dispositivos, "i", $id_usuario);
mysqli_stmt_execute($stmt_dispositivos);
$result_dispositivos = mysqli_stmt_get_result($stmt_dispositivos);

// Obtener estadísticas de mantenimiento
$sql_stats = "SELECT 
                COUNT(*) as total_mantenimientos,
                SUM(CASE WHEN m.estado = 'completado' THEN 1 ELSE 0 END) as mantenimientos_completados,
                SUM(CASE WHEN m.estado = 'en_proceso' THEN 1 ELSE 0 END) as mantenimientos_en_proceso,
                SUM(CASE WHEN m.estado = 'programado' THEN 1 ELSE 0 END) as mantenimientos_programados
              FROM mantenimientos m
              INNER JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
              WHERE d.id_usuario = ?";
$stmt_stats = mysqli_prepare($link, $sql_stats);
mysqli_stmt_bind_param($stmt_stats, "i", $id_usuario);
mysqli_stmt_execute($stmt_stats);
$result_stats = mysqli_stmt_get_result($stmt_stats);
$stats = mysqli_fetch_assoc($result_stats);

// Si no hay estadísticas, inicializar con valores predeterminados
if (!$stats) {
    $stats = [
        'total_mantenimientos' => 0,
        'mantenimientos_completados' => 0,
        'mantenimientos_en_proceso' => 0,
        'mantenimientos_programados' => 0
    ];
}

// Obtener los mantenimientos programados para los dispositivos del usuario
$sql_mantenimientos = "SELECT m.*, d.marca, d.modelo, d.tipo as tipo_dispositivo 
                      FROM mantenimientos m
                      INNER JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
                      WHERE d.id_usuario = ?
                      ORDER BY m.fecha_programada DESC";
$stmt_mantenimientos = mysqli_prepare($link, $sql_mantenimientos);
mysqli_stmt_bind_param($stmt_mantenimientos, "i", $id_usuario);
mysqli_stmt_execute($stmt_mantenimientos);
$result_mantenimientos = mysqli_stmt_get_result($stmt_mantenimientos);

// NUEVA SECCIÓN: Obtener PQRs registrados por el usuario
$sql_pqrs = "SELECT * FROM pqrs WHERE id_usuario = ? ORDER BY fecha_creacion DESC";
$stmt_pqrs = mysqli_prepare($link, $sql_pqrs);
mysqli_stmt_bind_param($stmt_pqrs, "i", $id_usuario);
mysqli_stmt_execute($stmt_pqrs);
$result_pqrs = mysqli_stmt_get_result($stmt_pqrs);

// NUEVA SECCIÓN: Obtener formularios de contacto enviados por el usuario
// Nota: Asumiendo que hay una relación entre contactos y usuarios por email
$sql_contactos = "SELECT * FROM contactos WHERE email = ? ORDER BY fecha DESC";
$stmt_contactos = mysqli_prepare($link, $sql_contactos);
mysqli_stmt_bind_param($stmt_contactos, "s", $usuario['email']);
mysqli_stmt_execute($stmt_contactos);
$result_contactos = mysqli_stmt_get_result($stmt_contactos);

// NUEVA SECCIÓN: Obtener dispositivos del usuario en envíos (como sustituto de bodega)
$sql_envios = "SELECT e.*, d.marca, d.modelo, d.tipo 
              FROM envios e
              INNER JOIN dispositivos d ON e.usuario_id = d.id_usuario
              WHERE e.usuario_id = ? AND e.estado_envio = 'En Proceso'
              ORDER BY e.fecha_envio DESC";
$stmt_envios = mysqli_prepare($link, $sql_envios);
mysqli_stmt_bind_param($stmt_envios, "i", $id_usuario);
mysqli_stmt_execute($stmt_envios);
$result_envios = mysqli_stmt_get_result($stmt_envios);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reportes de Dispositivos</title>
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
        .stats-card {
            text-align: center;
            padding: 15px;
        }
        .stats-card .number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
            color: #667eea;
        }
        .stats-card .label {
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-clipboard-list me-2"></i>Mis Reportes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Mis Reportes de Dispositivos</h2>
        
        <div class="row">
            <!-- Información Personal -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Información Personal</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
                        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['telefono']); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Estadísticas de Mantenimiento -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Estadísticas de Mantenimiento</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="stats-card">
                                    <div class="number"><?php echo $stats['total_mantenimientos']; ?></div>
                                    <div class="label">Total mantenimientos</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stats-card">
                                    <div class="number"><?php echo $stats['mantenimientos_completados']; ?></div>
                                    <div class="label">Completados</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stats-card">
                                    <div class="number"><?php echo $stats['mantenimientos_en_proceso']; ?></div>
                                    <div class="label">En proceso</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stats-card">
                                    <div class="number"><?php echo $stats['mantenimientos_programados']; ?></div>
                                    <div class="label">Programados</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mis Dispositivos -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Mis Dispositivos</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result_dispositivos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Fecha de Entrega</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_dispositivos)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['marca']); ?></td>
                                        <td><?php echo htmlspecialchars($row['modelo']); ?></td>
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
                    <p class="alert alert-info">No tienes dispositivos registrados.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mantenimientos Programados -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Mantenimientos Programados</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result_mantenimientos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Dispositivo</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Fecha Programada</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_mantenimientos)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['marca'] . ' ' . $row['modelo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tipo_dispositivo']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($row['descripcion'], 0, 50)) . (strlen($row['descripcion']) > 50 ? '...' : ''); ?></td>
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
                                                <?php 
                                                if ($row['estado'] == 'completado') {
                                                    echo 'Completado';
                                                } elseif ($row['estado'] == 'en_proceso') {
                                                    echo 'En proceso';
                                                } elseif ($row['estado'] == 'programado') {
                                                    echo 'Programado';
                                                } else {
                                                    echo htmlspecialchars($row['estado']);
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="ver_mantenimiento.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Ver
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="alert alert-info">No hay mantenimientos programados.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- NUEVA SECCIÓN: PQRs Registrados -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">PQRs Registrados</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result_pqrs) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
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
                                        <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($row['descripcion'], 0, 50)) . (strlen($row['descripcion']) > 50 ? '...' : ''); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha_creacion']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                if ($row['estado'] == 'resuelto') {
                                                    echo 'success';
                                                } elseif ($row['estado'] == 'en_proceso') {
                                                    echo 'warning';
                                                } else {
                                                    echo 'info';
                                                }
                                            ?>">
                                                <?php echo htmlspecialchars($row['estado'] ? $row['estado'] : 'Pendiente'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="alert alert-info">No has registrado PQRs.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- NUEVA SECCIÓN: Formularios de Contacto -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Formularios de Contacto Enviados</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result_contactos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Asunto</th>
                                    <th>Mensaje</th>
                                    <th>Fecha de Envío</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_contactos)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['asunto']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($row['mensaje'], 0, 50)) . (strlen($row['mensaje']) > 50 ? '...' : ''); ?></td>
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
                                                <?php echo htmlspecialchars($row['estado']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="alert alert-info">No has enviado formularios de contacto.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- NUEVA SECCIÓN: Dispositivos en Envío (sustituto de bodega) -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Mis Dispositivos en Bodega</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result_envios) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID Envío</th>
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
                                        <td><?php echo htmlspecialchars($row['direccion_destino']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha_envio']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha_salida']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['estado_envio'] == 'Completado' ? 'success' : 'warning'; ?>">
                                                <?php echo htmlspecialchars($row['estado_envio']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="alert alert-info">No tienes dispositivos en Bodega.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Cerrar todas las declaraciones y la conexión
mysqli_stmt_close($stmt_usuario);
mysqli_stmt_close($stmt_dispositivos);
mysqli_stmt_close($stmt_stats);
mysqli_stmt_close($stmt_mantenimientos);
mysqli_stmt_close($stmt_pqrs);
mysqli_stmt_close($stmt_contactos);
mysqli_stmt_close($stmt_envios);
mysqli_close($link);
?>