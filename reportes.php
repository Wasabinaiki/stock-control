<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$id_usuario = $_SESSION["id"];

function logError($message)
{
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'error_log.txt');
}

function formatearEstado($estado)
{
    $estado = strtolower(str_replace('_', ' ', $estado));
    return ucwords($estado);
}

$estado_mantenimiento = isset($_GET['estado_mantenimiento']) ? $_GET['estado_mantenimiento'] : '';
$orden_fecha_mantenimiento = isset($_GET['orden_fecha_mantenimiento']) ? $_GET['orden_fecha_mantenimiento'] : '';

$tipo_dispositivo = isset($_GET['tipo_dispositivo']) ? $_GET['tipo_dispositivo'] : '';
$orden_fecha_entrega = isset($_GET['orden_fecha_entrega']) ? $_GET['orden_fecha_entrega'] : '';

$tipo_pqr = isset($_GET['tipo_pqr']) ? $_GET['tipo_pqr'] : '';
$estado_pqr = isset($_GET['estado_pqr']) ? $_GET['estado_pqr'] : '';
$orden_fecha_pqr = isset($_GET['orden_fecha_pqr']) ? $_GET['orden_fecha_pqr'] : '';

$estado_bodega = isset($_GET['estado_bodega']) ? $_GET['estado_bodega'] : '';

// Consulta para mantenimientos del usuario
$sql = "SELECT m.id, m.id_dispositivo, m.fecha_programada, m.descripcion, m.estado, 
               d.marca, d.modelo, d.tipo as tipo_dispositivo
        FROM mantenimientos m 
        JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
        WHERE d.id_usuario = ?";

if ($estado_mantenimiento) {
    $sql .= " AND m.estado = ?";
}

if (!empty($orden_fecha_mantenimiento)) {
    $sql .= " ORDER BY m.fecha_programada " . ($orden_fecha_mantenimiento == 'asc' ? 'ASC' : 'DESC');
} else {
    $sql .= " ORDER BY m.fecha_programada DESC";
}

$stmt = mysqli_prepare($link, $sql);

if ($estado_mantenimiento) {
    mysqli_stmt_bind_param($stmt, "is", $id_usuario, $estado_mantenimiento);
} else {
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    logError("Error en la consulta de mantenimientos: " . mysqli_error($link));
}

// Consulta para dispositivos del usuario
$sql_dispositivos = "SELECT * FROM dispositivos WHERE id_usuario = ?";

if (!empty($tipo_dispositivo)) {
    $sql_dispositivos .= " AND tipo = ?";
}

if (!empty($orden_fecha_entrega)) {
    $sql_dispositivos .= " ORDER BY fecha_entrega " . ($orden_fecha_entrega == 'asc' ? 'ASC' : 'DESC');
} else {
    $sql_dispositivos .= " ORDER BY fecha_entrega DESC";
}

$stmt_dispositivos = mysqli_prepare($link, $sql_dispositivos);

if (!empty($tipo_dispositivo)) {
    mysqli_stmt_bind_param($stmt_dispositivos, "is", $id_usuario, $tipo_dispositivo);
} else {
    mysqli_stmt_bind_param($stmt_dispositivos, "i", $id_usuario);
}

mysqli_stmt_execute($stmt_dispositivos);
$result_dispositivos = mysqli_stmt_get_result($stmt_dispositivos);

if (!$result_dispositivos) {
    logError("Error en la consulta de dispositivos: " . mysqli_error($link));
}

// Consulta para PQRs del usuario
$sql_pqrs = "SELECT * FROM pqrs WHERE id_usuario = ?";

if (!empty($tipo_pqr)) {
    $sql_pqrs .= " AND tipo = ?";
}

if (!empty($estado_pqr)) {
    $sql_pqrs .= " AND estado = ?";
}

if (!empty($orden_fecha_pqr)) {
    $sql_pqrs .= " ORDER BY fecha_creacion " . ($orden_fecha_pqr == 'asc' ? 'ASC' : 'DESC');
} else {
    $sql_pqrs .= " ORDER BY fecha_creacion DESC";
}

$stmt_pqrs = mysqli_prepare($link, $sql_pqrs);

if (!empty($tipo_pqr) && !empty($estado_pqr)) {
    mysqli_stmt_bind_param($stmt_pqrs, "iss", $id_usuario, $tipo_pqr, $estado_pqr);
} elseif (!empty($tipo_pqr)) {
    mysqli_stmt_bind_param($stmt_pqrs, "is", $id_usuario, $tipo_pqr);
} elseif (!empty($estado_pqr)) {
    mysqli_stmt_bind_param($stmt_pqrs, "is", $id_usuario, $estado_pqr);
} else {
    mysqli_stmt_bind_param($stmt_pqrs, "i", $id_usuario);
}

mysqli_stmt_execute($stmt_pqrs);
$result_pqrs = mysqli_stmt_get_result($stmt_pqrs);

if (!$result_pqrs) {
    logError("Error en la consulta de PQRs: " . mysqli_error($link));
}

// Consulta para dispositivos en bodega del usuario
$sql_bodega = "SELECT * FROM dispositivos WHERE id_usuario = ?";

if (!empty($estado_bodega)) {
    $sql_bodega .= " AND estado = ?";
}

$sql_bodega .= " ORDER BY id_dispositivo";

$stmt_bodega = mysqli_prepare($link, $sql_bodega);

if (!empty($estado_bodega)) {
    mysqli_stmt_bind_param($stmt_bodega, "is", $id_usuario, $estado_bodega);
} else {
    mysqli_stmt_bind_param($stmt_bodega, "i", $id_usuario);
}

mysqli_stmt_execute($stmt_bodega);
$result_bodega = mysqli_stmt_get_result($stmt_bodega);

if (!$result_bodega) {
    logError("Error en la consulta de dispositivos en bodega: " . mysqli_error($link));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reportes</title>
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

        .dashboard-link {
            color: white !important;
            border-radius: 5px;
            padding: 8px 15px !important;
            margin-right: 10px;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .filter-form select,
        .filter-form button {
            margin-right: 10px;
        }

        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
            }

            .filter-form select,
            .filter-form button {
                width: 100%;
                margin-right: 0;
                margin-bottom: 10px;
            }
        }

        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
            font-weight: bold;
        }

        .section-filters {
            background-color: #f0f2f5;
            padding: 15px;
            border-radius: 0 0 10px 10px;
            margin-bottom: 20px;
        }

        .table thead th {
            color: #333;
            background-color: #f8f9fa;
        }

        .tab-header {
            color: #000;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-chart-bar me-2"></i>Mis Reportes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link dashboard-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mis Reportes</h2>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download me-2"></i>Descargar Reportes
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li class="dropdown-header">Todos mis reportes</li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=todos&formato=pdf&usuario=<?php echo $id_usuario; ?>" target="_blank"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=todos&formato=excel&usuario=<?php echo $id_usuario; ?>"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="dropdown-header">Mis Mantenimientos</li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=mantenimientos&formato=pdf&usuario=<?php echo $id_usuario; ?>" target="_blank"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=mantenimientos&formato=excel&usuario=<?php echo $id_usuario; ?>"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="dropdown-header">Mis Dispositivos</li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=dispositivos&formato=pdf&usuario=<?php echo $id_usuario; ?>" target="_blank"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=dispositivos&formato=excel&usuario=<?php echo $id_usuario; ?>"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="dropdown-header">Mis PQRs</li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=pqrs&formato=pdf&usuario=<?php echo $id_usuario; ?>" target="_blank"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=pqrs&formato=excel&usuario=<?php echo $id_usuario; ?>"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="dropdown-header">Mis Dispositivos en Bodega</li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=bodega&formato=pdf&usuario=<?php echo $id_usuario; ?>" target="_blank"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=bodega&formato=excel&usuario=<?php echo $id_usuario; ?>"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                </ul>
            </div>
        </div>

        <div class="section-header">
            <i class="fas fa-tools me-2"></i>Mis Mantenimientos Programados
        </div>
        <div class="section-filters">
            <form action="" method="GET" class="filter-form">
                <input type="hidden" name="tipo_dispositivo" value="<?php echo htmlspecialchars($tipo_dispositivo); ?>">
                <input type="hidden" name="orden_fecha_entrega"
                    value="<?php echo htmlspecialchars($orden_fecha_entrega); ?>">
                <input type="hidden" name="tipo_pqr" value="<?php echo htmlspecialchars($tipo_pqr); ?>">
                <input type="hidden" name="estado_pqr" value="<?php echo htmlspecialchars($estado_pqr); ?>">
                <input type="hidden" name="orden_fecha_pqr" value="<?php echo htmlspecialchars($orden_fecha_pqr); ?>">
                <input type="hidden" name="estado_bodega" value="<?php echo htmlspecialchars($estado_bodega); ?>">

                <select name="estado_mantenimiento" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="completado" <?php echo $estado_mantenimiento == 'completado' ? 'selected' : ''; ?>>
                        Completado</option>
                    <option value="en_proceso" <?php echo $estado_mantenimiento == 'en_proceso' ? 'selected' : ''; ?>>En
                        proceso</option>
                    <option value="programado" <?php echo $estado_mantenimiento == 'programado' ? 'selected' : ''; ?>>
                        Programado</option>
                </select>

                <select name="orden_fecha_mantenimiento" class="form-select">
                    <option value="">Ordenar por fecha</option>
                    <option value="desc" <?php echo $orden_fecha_mantenimiento == 'desc' ? 'selected' : ''; ?>>Más
                        reciente primero</option>
                    <option value="asc" <?php echo $orden_fecha_mantenimiento == 'asc' ? 'selected' : ''; ?>>Más antiguo
                        primero</option>
                </select>

                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
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
                    <p class="text-center">No tienes mantenimientos programados.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="section-header">
            <i class="fas fa-laptop me-2"></i>Mis Dispositivos Registrados
        </div>
        <div class="section-filters">
            <form action="" method="GET" class="filter-form">
                <input type="hidden" name="estado_mantenimiento"
                    value="<?php echo htmlspecialchars($estado_mantenimiento); ?>">
                <input type="hidden" name="orden_fecha_mantenimiento"
                    value="<?php echo htmlspecialchars($orden_fecha_mantenimiento); ?>">
                <input type="hidden" name="tipo_pqr" value="<?php echo htmlspecialchars($tipo_pqr); ?>">
                <input type="hidden" name="estado_pqr" value="<?php echo htmlspecialchars($estado_pqr); ?>">
                <input type="hidden" name="orden_fecha_pqr" value="<?php echo htmlspecialchars($orden_fecha_pqr); ?>">
                <input type="hidden" name="estado_bodega" value="<?php echo htmlspecialchars($estado_bodega); ?>">

                <select name="tipo_dispositivo" class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="computadora" <?php echo $tipo_dispositivo == 'computadora' ? 'selected' : ''; ?>>
                        Computadora</option>
                    <option value="tablet" <?php echo $tipo_dispositivo == 'tablet' ? 'selected' : ''; ?>>Tablet</option>
                    <option value="celular" <?php echo $tipo_dispositivo == 'celular' ? 'selected' : ''; ?>>Celular
                    </option>
                </select>

                <select name="orden_fecha_entrega" class="form-select">
                    <option value="">Ordenar por fecha</option>
                    <option value="desc" <?php echo $orden_fecha_entrega == 'desc' ? 'selected' : ''; ?>>Más reciente
                        primero</option>
                    <option value="asc" <?php echo $orden_fecha_entrega == 'asc' ? 'selected' : ''; ?>>Más antiguo primero
                    </option>
                </select>

                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <?php if ($result_dispositivos && mysqli_num_rows($result_dispositivos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Marca/Modelo</th>
                                    <th>Fecha de Entrega</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_dispositivos)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id_dispositivo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['marca'] . ' ' . $row['modelo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha_entrega']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No tienes dispositivos registrados.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="section-header">
            <i class="fas fa-question-circle me-2"></i>Mis PQRs Registrados
        </div>
        <div class="section-filters">
            <form action="" method="GET" class="filter-form">
                <input type="hidden" name="estado_mantenimiento"
                    value="<?php echo htmlspecialchars($estado_mantenimiento); ?>">
                <input type="hidden" name="orden_fecha_mantenimiento"
                    value="<?php echo htmlspecialchars($orden_fecha_mantenimiento); ?>">
                <input type="hidden" name="tipo_dispositivo" value="<?php echo htmlspecialchars($tipo_dispositivo); ?>">
                <input type="hidden" name="orden_fecha_entrega"
                    value="<?php echo htmlspecialchars($orden_fecha_entrega); ?>">
                <input type="hidden" name="estado_bodega" value="<?php echo htmlspecialchars($estado_bodega); ?>">

                <select name="tipo_pqr" class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="peticion" <?php echo $tipo_pqr == 'peticion' ? 'selected' : ''; ?>>Petición</option>
                    <option value="queja" <?php echo $tipo_pqr == 'queja' ? 'selected' : ''; ?>>Queja</option>
                    <option value="reclamo" <?php echo $tipo_pqr == 'reclamo' ? 'selected' : ''; ?>>Reclamo</option>
                    <option value="sugerencia" <?php echo $tipo_pqr == 'sugerencia' ? 'selected' : ''; ?>>Sugerencia
                    </option>
                </select>

                <select name="estado_pqr" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" <?php echo $estado_pqr == 'pendiente' ? 'selected' : ''; ?>>Pendiente
                    </option>
                    <option value="en_proceso" <?php echo $estado_pqr == 'en_proceso' ? 'selected' : ''; ?>>En proceso
                    </option>
                    <option value="resuelto" <?php echo $estado_pqr == 'resuelto' ? 'selected' : ''; ?>>Resuelto</option>
                </select>

                <select name="orden_fecha_pqr" class="form-select">
                    <option value="">Ordenar por fecha</option>
                    <option value="desc" <?php echo $orden_fecha_pqr == 'desc' ? 'selected' : ''; ?>>Más reciente primero
                    </option>
                    <option value="asc" <?php echo $orden_fecha_pqr == 'asc' ? 'selected' : ''; ?>>Más antiguo primero
                    </option>
                </select>

                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <?php if ($result_pqrs && mysqli_num_rows($result_pqrs) > 0): ?>
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
                    <p class="text-center">No tienes PQRs registrados.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="section-header">
            <i class="fas fa-warehouse me-2"></i>Mis Dispositivos en Bodega
        </div>
        <div class="section-filters">
            <form action="" method="GET" class="filter-form">
                <input type="hidden" name="estado_mantenimiento"
                    value="<?php echo htmlspecialchars($estado_mantenimiento); ?>">
                <input type="hidden" name="orden_fecha_mantenimiento"
                    value="<?php echo htmlspecialchars($orden_fecha_mantenimiento); ?>">
                <input type="hidden" name="tipo_dispositivo" value="<?php echo htmlspecialchars($tipo_dispositivo); ?>">
                <input type="hidden" name="orden_fecha_entrega"
                    value="<?php echo htmlspecialchars($orden_fecha_entrega); ?>">
                <input type="hidden" name="tipo_pqr" value="<?php echo htmlspecialchars($tipo_pqr); ?>">
                <input type="hidden" name="estado_pqr" value="<?php echo htmlspecialchars($estado_pqr); ?>">
                <input type="hidden" name="orden_fecha_pqr" value="<?php echo htmlspecialchars($orden_fecha_pqr); ?>">

                <select name="estado_bodega" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="Activo" <?php echo $estado_bodega == 'Activo' ? 'selected' : ''; ?>>Activo</option>
                    <option value="Inactivo" <?php echo $estado_bodega == 'Inactivo' ? 'selected' : ''; ?>>Inactivo
                    </option>
                    <option value="En Reparación" <?php echo $estado_bodega == 'En Reparación' ? 'selected' : ''; ?>>En
                        Reparación</option>
                    <option value="Completado" <?php echo $estado_bodega == 'Completado' ? 'selected' : ''; ?>>Completado
                    </option>
                </select>

                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <?php if ($result_bodega && mysqli_num_rows($result_bodega) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
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
                                        <td>
                                            <span class="badge bg-<?php
                                            if ($row['estado'] == 'Completado' || $row['estado'] == 'Activo') {
                                                echo 'success';
                                            } elseif ($row['estado'] == 'En Reparación') {
                                                echo 'warning';
                                            } else {
                                                echo 'danger';
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
                    <p class="text-center">No tienes dispositivos en Bodega.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
mysqli_close($link);
?>