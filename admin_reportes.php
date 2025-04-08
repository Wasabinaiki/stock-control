<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

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

$asunto_contacto = isset($_GET['asunto_contacto']) ? $_GET['asunto_contacto'] : '';
$estado_contacto = isset($_GET['estado_contacto']) ? $_GET['estado_contacto'] : '';
$orden_fecha_contacto = isset($_GET['orden_fecha_contacto']) ? $_GET['orden_fecha_contacto'] : '';

$estado_bodega = isset($_GET['estado_bodega']) ? $_GET['estado_bodega'] : '';

$sql = "SELECT m.id, m.id_dispositivo, m.fecha_programada, m.descripcion, m.estado, 
               d.marca, d.modelo, d.tipo as tipo_dispositivo, u.username 
        FROM mantenimientos m 
        JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
        JOIN usuarios u ON d.id_usuario = u.id_usuario
        WHERE 1=1";

if ($estado_mantenimiento) {
    $sql .= " AND m.estado = '" . mysqli_real_escape_string($link, $estado_mantenimiento) . "'";
}

if (!empty($orden_fecha_mantenimiento)) {
    $sql .= " ORDER BY m.fecha_programada " . ($orden_fecha_mantenimiento == 'asc' ? 'ASC' : 'DESC');
} else {
    $sql .= " ORDER BY m.fecha_programada DESC";
}

$result = mysqli_query($link, $sql);
if (!$result) {
    logError("Error en la consulta de mantenimientos: " . mysqli_error($link));
}

$sql_dispositivos = "SELECT d.*, u.username FROM dispositivos d 
                    JOIN usuarios u ON d.id_usuario = u.id_usuario 
                    WHERE 1=1";

if (!empty($tipo_dispositivo)) {
    $sql_dispositivos .= " AND d.tipo = '" . mysqli_real_escape_string($link, $tipo_dispositivo) . "'";
}

if (!empty($orden_fecha_entrega)) {
    $sql_dispositivos .= " ORDER BY d.fecha_entrega " . ($orden_fecha_entrega == 'asc' ? 'ASC' : 'DESC');
} else {
    $sql_dispositivos .= " ORDER BY d.fecha_entrega DESC";
}

$result_dispositivos = mysqli_query($link, $sql_dispositivos);
if (!$result_dispositivos) {
    logError("Error en la consulta de dispositivos: " . mysqli_error($link));
}

$sql_pqrs = "SELECT p.*, u.username 
            FROM pqrs p 
            JOIN usuarios u ON p.id_usuario = u.id_usuario 
            WHERE 1=1";

if (!empty($tipo_pqr)) {
    $sql_pqrs .= " AND p.tipo = '" . mysqli_real_escape_string($link, $tipo_pqr) . "'";
}

if (!empty($estado_pqr)) {
    $sql_pqrs .= " AND p.estado = '" . mysqli_real_escape_string($link, $estado_pqr) . "'";
}

if (!empty($orden_fecha_pqr)) {
    $sql_pqrs .= " ORDER BY p.fecha_creacion " . ($orden_fecha_pqr == 'asc' ? 'ASC' : 'DESC');
} else {
    $sql_pqrs .= " ORDER BY p.fecha_creacion DESC";
}

$result_pqrs = mysqli_query($link, $sql_pqrs);
if (!$result_pqrs) {
    logError("Error en la consulta de PQRs: " . mysqli_error($link));
}

$sql_contactos = "SELECT c.* FROM contactos c WHERE 1=1";

if (!empty($asunto_contacto)) {
    $sql_contactos .= " AND c.asunto = '" . mysqli_real_escape_string($link, $asunto_contacto) . "'";
}

if (!empty($estado_contacto)) {
    $sql_contactos .= " AND c.estado = '" . mysqli_real_escape_string($link, $estado_contacto) . "'";
}

if (!empty($orden_fecha_contacto)) {
    $sql_contactos .= " ORDER BY c.fecha " . ($orden_fecha_contacto == 'asc' ? 'ASC' : 'DESC');
} else {
    $sql_contactos .= " ORDER BY c.fecha DESC";
}

$result_contactos = mysqli_query($link, $sql_contactos);
if (!$result_contactos) {
    logError("Error en la consulta de contactos: " . mysqli_error($link));
}

$sql_bodega = "SELECT d.id_dispositivo, d.tipo, d.marca, d.modelo, d.estado, u.username 
              FROM dispositivos d
              JOIN usuarios u ON d.id_usuario = u.id_usuario
              WHERE 1=1";

if (!empty($estado_bodega)) {
    $sql_bodega .= " AND d.estado = '" . mysqli_real_escape_string($link, $estado_bodega) . "'";
}

$sql_bodega .= " ORDER BY d.id_dispositivo";

$result_bodega = mysqli_query($link, $sql_bodega);
if (!$result_bodega) {
    logError("Error en la consulta de dispositivos en bodega: " . mysqli_error($link));
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestión de Reportes</h2>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download me-2"></i>Descargar Reportes
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li class="dropdown-header">Todos los reportes</li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=todos&formato=pdf" target="_blank"><i
                                class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=todos&formato=excel"><i
                                class="fas fa-file-excel me-2"></i>Excel</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li class="dropdown-header">Mantenimientos</li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=mantenimientos&formato=pdf"
                            target="_blank"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=mantenimientos&formato=excel"><i
                                class="fas fa-file-excel me-2"></i>Excel</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li class="dropdown-header">Dispositivos</li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=dispositivos&formato=pdf"
                            target="_blank"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=dispositivos&formato=excel"><i
                                class="fas fa-file-excel me-2"></i>Excel</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li class="dropdown-header">PQRs</li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=pqrs&formato=pdf" target="_blank"><i
                                class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=pqrs&formato=excel"><i
                                class="fas fa-file-excel me-2"></i>Excel</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li class="dropdown-header">Contactos</li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=contactos&formato=pdf"
                            target="_blank"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=contactos&formato=excel"><i
                                class="fas fa-file-excel me-2"></i>Excel</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li class="dropdown-header">Bodega</li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=bodega&formato=pdf" target="_blank"><i
                                class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="exportar_reportes.php?tipo=bodega&formato=excel"><i
                                class="fas fa-file-excel me-2"></i>Excel</a></li>
                </ul>
            </div>
        </div>

        <div class="section-header">
            <i class="fas fa-tools me-2"></i>Mantenimientos Programados
        </div>
        <div class="section-filters">
            <form action="" method="GET" class="filter-form">
                <input type="hidden" name="tipo_dispositivo" value="<?php echo htmlspecialchars($tipo_dispositivo); ?>">
                <input type="hidden" name="orden_fecha_entrega"
                    value="<?php echo htmlspecialchars($orden_fecha_entrega); ?>">
                <input type="hidden" name="tipo_pqr" value="<?php echo htmlspecialchars($tipo_pqr); ?>">
                <input type="hidden" name="estado_pqr" value="<?php echo htmlspecialchars($estado_pqr); ?>">
                <input type="hidden" name="orden_fecha_pqr" value="<?php echo htmlspecialchars($orden_fecha_pqr); ?>">
                <input type="hidden" name="asunto_contacto" value="<?php echo htmlspecialchars($asunto_contacto); ?>">
                <input type="hidden" name="estado_contacto" value="<?php echo htmlspecialchars($estado_contacto); ?>">
                <input type="hidden" name="orden_fecha_contacto"
                    value="<?php echo htmlspecialchars($orden_fecha_contacto); ?>">
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

        <div class="section-header">
            <i class="fas fa-laptop me-2"></i>Dispositivos Registrados
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
                <input type="hidden" name="asunto_contacto" value="<?php echo htmlspecialchars($asunto_contacto); ?>">
                <input type="hidden" name="estado_contacto" value="<?php echo htmlspecialchars($estado_contacto); ?>">
                <input type="hidden" name="orden_fecha_contacto"
                    value="<?php echo htmlspecialchars($orden_fecha_contacto); ?>">
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
                                    <th>Usuario</th>
                                    <th>Tipo</th>
                                    <th>Marca/Modelo</th>
                                    <th>Fecha de Entrega</th>
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

        <div class="section-header">
            <i class="fas fa-question-circle me-2"></i>PQRs Registrados
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
                <input type="hidden" name="asunto_contacto" value="<?php echo htmlspecialchars($asunto_contacto); ?>">
                <input type="hidden" name="estado_contacto" value="<?php echo htmlspecialchars($estado_contacto); ?>">
                <input type="hidden" name="orden_fecha_contacto"
                    value="<?php echo htmlspecialchars($orden_fecha_contacto); ?>">
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
                    <p class="text-center">No hay PQRs registrados.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="section-header">
            <i class="fas fa-envelope me-2"></i>Formularios de Contacto
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
                <input type="hidden" name="estado_bodega" value="<?php echo htmlspecialchars($estado_bodega); ?>">

                <select name="asunto_contacto" class="form-select">
                    <option value="">Todos los asuntos</option>
                    <option value="soporte" <?php echo $asunto_contacto == 'soporte' ? 'selected' : ''; ?>>Soporte
                    </option>
                    <option value="quejas" <?php echo $asunto_contacto == 'quejas' ? 'selected' : ''; ?>>Quejas</option>
                    <option value="otros" <?php echo $asunto_contacto == 'otros' ? 'selected' : ''; ?>>Otros</option>
                </select>

                <select name="estado_contacto" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente" <?php echo $estado_contacto == 'Pendiente' ? 'selected' : ''; ?>>Pendiente
                    </option>
                    <option value="En proceso" <?php echo $estado_contacto == 'En proceso' ? 'selected' : ''; ?>>En
                        proceso</option>
                    <option value="Resuelto" <?php echo $estado_contacto == 'Resuelto' ? 'selected' : ''; ?>>Resuelto
                    </option>
                </select>

                <select name="orden_fecha_contacto" class="form-select">
                    <option value="">Ordenar por fecha</option>
                    <option value="desc" <?php echo $orden_fecha_contacto == 'desc' ? 'selected' : ''; ?>>Más reciente
                        primero</option>
                    <option value="asc" <?php echo $orden_fecha_contacto == 'asc' ? 'selected' : ''; ?>>Más antiguo
                        primero</option>
                </select>

                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>

        <div class="card mb-4">
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

        <div class="section-header">
            <i class="fas fa-warehouse me-2"></i>Dispositivos en Bodega
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
                <input type="hidden" name="asunto_contacto" value="<?php echo htmlspecialchars($asunto_contacto); ?>">
                <input type="hidden" name="estado_contacto" value="<?php echo htmlspecialchars($estado_contacto); ?>">
                <input type="hidden" name="orden_fecha_contacto"
                    value="<?php echo htmlspecialchars($orden_fecha_contacto); ?>">

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
                                    <th>Usuario</th>
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
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
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
                    <p class="text-center">No hay dispositivos en Bodega.</p>
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