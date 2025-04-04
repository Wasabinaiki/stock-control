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

$sql_usuario = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt_usuario = mysqli_prepare($link, $sql_usuario);
mysqli_stmt_bind_param($stmt_usuario, "i", $id_usuario);
mysqli_stmt_execute($stmt_usuario);
$result_usuario = mysqli_stmt_get_result($stmt_usuario);
$usuario = mysqli_fetch_assoc($result_usuario);

$sql_dispositivos = "SELECT * FROM dispositivos WHERE id_usuario = ? ORDER BY fecha_entrega DESC";
$stmt_dispositivos = mysqli_prepare($link, $sql_dispositivos);
mysqli_stmt_bind_param($stmt_dispositivos, "i", $id_usuario);
mysqli_stmt_execute($stmt_dispositivos);
$result_dispositivos = mysqli_stmt_get_result($stmt_dispositivos);

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

if (!$stats) {
    $stats = [
        'total_mantenimientos' => 0,
        'mantenimientos_completados' => 0,
        'mantenimientos_en_proceso' => 0,
        'mantenimientos_programados' => 0
    ];
}

$sql_mantenimientos = "SELECT m.*, d.marca, d.modelo, d.tipo as tipo_dispositivo 
                      FROM mantenimientos m
                      INNER JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
                      WHERE d.id_usuario = ?
                      ORDER BY m.fecha_programada DESC";
$stmt_mantenimientos = mysqli_prepare($link, $sql_mantenimientos);
mysqli_stmt_bind_param($stmt_mantenimientos, "i", $id_usuario);
mysqli_stmt_execute($stmt_mantenimientos);
$result_mantenimientos = mysqli_stmt_get_result($stmt_mantenimientos);

$sql_pqrs = "SELECT * FROM pqrs WHERE id_usuario = ? ORDER BY fecha_creacion DESC";
$stmt_pqrs = mysqli_prepare($link, $sql_pqrs);
mysqli_stmt_bind_param($stmt_pqrs, "i", $id_usuario);
mysqli_stmt_execute($stmt_pqrs);
$result_pqrs = mysqli_stmt_get_result($stmt_pqrs);

$sql_contactos = "SELECT * FROM contactos WHERE email = ? ORDER BY fecha DESC";
$stmt_contactos = mysqli_prepare($link, $sql_contactos);
mysqli_stmt_bind_param($stmt_contactos, "s", $usuario['email']);
mysqli_stmt_execute($stmt_contactos);
$result_contactos = mysqli_stmt_get_result($stmt_contactos);

$sql_bodega = "SELECT d.* 
              FROM dispositivos d 
              WHERE d.id_usuario = ?
              ORDER BY d.fecha_entrega DESC";
$stmt_bodega = mysqli_prepare($link, $sql_bodega);
mysqli_stmt_bind_param($stmt_bodega, "i", $id_usuario);
mysqli_stmt_execute($stmt_bodega);
$result_bodega = mysqli_stmt_get_result($stmt_bodega);

function formatearEstado($estado)
{
    $estado = strtolower(str_replace('_', ' ', $estado));
    return ucwords($estado);
}
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

        .estado-pendiente,
        .badge-pendiente {
            color: #ffc107 !important;
            font-weight: bold;
        }

        .estado-en-proceso,
        .badge-en-proceso {
            color: #0d6efd !important;
            font-weight: bold;
        }

        .estado-resuelto,
        .estado-completado,
        .badge-resuelto,
        .badge-completado {
            color: #198754 !important;
            font-weight: bold;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 12px;
            font-weight: 500;
        }

        .bg-pendiente {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .bg-en-proceso {
            background-color: #0d6efd !important;
            color: #fff !important;
        }

        .bg-resuelto,
        .bg-completado {
            background-color: #198754 !important;
            color: #fff !important;
        }

        .bg-inactivo,
        .bg-cancelado {
            background-color: #dc3545 !important;
            color: #fff !important;
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
        <h2 class="mb-4">Mis Reportes de Dispositivos</h2>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Información Personal</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong>
                            <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
                        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['telefono']); ?></p>
                    </div>
                </div>
            </div>

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
                                            <?php
                                            $estadoClass = 'badge-pendiente';
                                            if (strtolower($row['estado']) == 'activo' || strtolower($row['estado']) == 'completado') {
                                                $estadoClass = 'badge-completado';
                                            } elseif (strtolower($row['estado']) == 'en proceso') {
                                                $estadoClass = 'badge-en-proceso';
                                            } elseif (strtolower($row['estado']) == 'inactivo') {
                                                $estadoClass = 'bg-inactivo';
                                            }
                                            ?>
                                            <span
                                                class="badge <?php echo (strpos($estadoClass, 'bg-') === 0) ? $estadoClass : 'bg-' . str_replace('badge-', '', $estadoClass); ?>">
                                                <?php echo formatearEstado($row['estado']); ?>
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_mantenimientos)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['marca'] . ' ' . $row['modelo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tipo_dispositivo']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($row['descripcion'], 0, 50)) . (strlen($row['descripcion']) > 50 ? '...' : ''); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['fecha_programada']); ?></td>
                                        <td>
                                            <?php
                                            $estado = strtolower($row['estado']);
                                            $badgeClass = '';

                                            if ($estado == 'completado') {
                                                $badgeClass = 'bg-completado';
                                            } elseif ($estado == 'en_proceso' || $estado == 'en proceso') {
                                                $badgeClass = 'bg-en-proceso';
                                            } else {
                                                $badgeClass = 'bg-pendiente';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo formatearEstado($row['estado']); ?>
                                            </span>
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
                                        <td><?php echo htmlspecialchars(substr($row['descripcion'], 0, 50)) . (strlen($row['descripcion']) > 50 ? '...' : ''); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['fecha_creacion']); ?></td>
                                        <td>
                                            <?php
                                            $estado = strtolower($row['estado'] ?: 'pendiente');
                                            $badgeClass = '';

                                            if ($estado == 'resuelto') {
                                                $badgeClass = 'bg-resuelto';
                                            } elseif ($estado == 'en_proceso' || $estado == 'en proceso') {
                                                $badgeClass = 'bg-en-proceso';
                                            } else {
                                                $badgeClass = 'bg-pendiente';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo formatearEstado($row['estado'] ?: 'pendiente'); ?>
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
                                        <td><?php echo htmlspecialchars(substr($row['mensaje'], 0, 50)) . (strlen($row['mensaje']) > 50 ? '...' : ''); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                                        <td>
                                            <?php
                                            $estado = strtolower($row['estado']);
                                            $badgeClass = '';

                                            if ($estado == 'resuelto') {
                                                $badgeClass = 'bg-resuelto';
                                            } elseif ($estado == 'en proceso') {
                                                $badgeClass = 'bg-en-proceso';
                                            } else {
                                                $badgeClass = 'bg-pendiente';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo formatearEstado($row['estado']); ?>
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

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Mis Dispositivos en Bodega</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result_bodega) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Fecha de Entrega</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_bodega)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id_dispositivo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['marca']); ?></td>
                                        <td><?php echo htmlspecialchars($row['modelo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha_entrega']); ?></td>
                                        <td>
                                            <?php
                                            $estado = strtolower($row['estado']);
                                            $badgeClass = '';

                                            if ($estado == 'inactivo') {
                                                $badgeClass = 'bg-inactivo';
                                            } elseif ($estado == 'completado' || $estado == 'activo') {
                                                $badgeClass = 'bg-completado';
                                            } elseif ($estado == 'en_proceso' || $estado == 'en proceso') {
                                                $badgeClass = 'bg-en-proceso';
                                            } else {
                                                $badgeClass = 'bg-pendiente';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo formatearEstado($row['estado']); ?>
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
mysqli_stmt_close($stmt_usuario);
mysqli_stmt_close($stmt_dispositivos);
mysqli_stmt_close($stmt_stats);
mysqli_stmt_close($stmt_mantenimientos);
mysqli_stmt_close($stmt_pqrs);
mysqli_stmt_close($stmt_contactos);
mysqli_stmt_close($stmt_bodega);
mysqli_close($link);
?>