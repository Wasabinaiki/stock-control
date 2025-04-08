<?php
session_start();
require_once "includes/config.php";
require_once "includes/audit_functions.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

registrar_acceso_modulo($_SESSION["id"], "Auditoría");

$registros_por_pagina = 15;
$pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$inicio = ($pagina_actual - 1) * $registros_por_pagina;

$filtro_usuario = isset($_GET['usuario']) ? $_GET['usuario'] : '';
$filtro_accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$filtro_tabla = isset($_GET['tabla']) ? $_GET['tabla'] : '';
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';

$sql_where = "WHERE 1=1";
$params = [];
$types = "";

if (!empty($filtro_usuario)) {
    $sql_where .= " AND (u.username LIKE ? OR u.id_usuario = ?)";
    $params[] = "%$filtro_usuario%";
    $params[] = $filtro_usuario;
    $types .= "si";
}

if (!empty($filtro_accion)) {
    $sql_where .= " AND a.accion LIKE ?";
    $params[] = "%$filtro_accion%";
    $types .= "s";
}

if (!empty($filtro_tabla)) {
    $sql_where .= " AND a.tabla = ?";
    $params[] = $filtro_tabla;
    $types .= "s";
}

if (!empty($filtro_fecha_desde)) {
    $sql_where .= " AND DATE(a.fecha_hora) >= ?";
    $params[] = $filtro_fecha_desde;
    $types .= "s";
}

if (!empty($filtro_fecha_hasta)) {
    $sql_where .= " AND DATE(a.fecha_hora) <= ?";
    $params[] = $filtro_fecha_hasta;
    $types .= "s";
}

$sql_count = "SELECT COUNT(*) as total FROM auditoria a 
              JOIN usuarios u ON a.id_usuario = u.id_usuario 
              $sql_where";

$stmt_count = mysqli_prepare($link, $sql_count);
if (!empty($types)) {
    mysqli_stmt_bind_param($stmt_count, $types, ...$params);
}
mysqli_stmt_execute($stmt_count);
$result_count = mysqli_stmt_get_result($stmt_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_registros = $row_count['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

$sql = "SELECT a.*, u.username 
        FROM auditoria a 
        JOIN usuarios u ON a.id_usuario = u.id_usuario 
        $sql_where 
        ORDER BY a.fecha_hora DESC 
        LIMIT ?, ?";

$stmt = mysqli_prepare($link, $sql);
$params[] = $inicio;
$params[] = $registros_por_pagina;
$types .= "ii";
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$sql_tablas = "SELECT DISTINCT tabla FROM auditoria WHERE tabla IS NOT NULL ORDER BY tabla";
$result_tablas = mysqli_query($link, $sql_tablas);

$sql_acciones = "SELECT DISTINCT accion FROM auditoria ORDER BY accion";
$result_acciones = mysqli_query($link, $sql_acciones);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Auditoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-bottom: 40px;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin-bottom: 30px;
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
            padding: 15px 20px;
        }

        .card-body {
            padding: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
        }

        .pagination .page-item.active .page-link {
            background-color: #764ba2;
            border-color: #764ba2;
        }

        .pagination .page-link {
            color: #764ba2;
        }

        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-history me-2"></i>Módulo de Auditoría</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="row g-3">
                    <div class="col-md-4">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario"
                            value="<?php echo htmlspecialchars($filtro_usuario); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="accion" class="form-label">Acción</label>
                        <select class="form-select" id="accion" name="accion">
                            <option value="">Todas las acciones</option>
                            <?php while ($row_accion = mysqli_fetch_assoc($result_acciones)): ?>
                                <option value="<?php echo htmlspecialchars($row_accion['accion']); ?>" <?php echo ($filtro_accion == $row_accion['accion']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row_accion['accion']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tabla" class="form-label">Tabla</label>
                        <select class="form-select" id="tabla" name="tabla">
                            <option value="">Todas las tablas</option>
                            <?php while ($row_tabla = mysqli_fetch_assoc($result_tablas)): ?>
                                <option value="<?php echo htmlspecialchars($row_tabla['tabla']); ?>" <?php echo ($filtro_tabla == $row_tabla['tabla']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row_tabla['tabla']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_desde" class="form-label">Fecha desde</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"
                            value="<?php echo htmlspecialchars($filtro_fecha_desde); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_hasta" class="form-label">Fecha hasta</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
                            value="<?php echo htmlspecialchars($filtro_fecha_hasta); ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                        <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="btn btn-secondary">
                            <i class="fas fa-undo me-2"></i>Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Registros de Auditoría</h5>
                <div>
                    <button id="btnExportarPDF" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                    </button>
                    <button id="btnExportarCSV" class="btn btn-success btn-sm ms-2">
                        <i class="fas fa-file-csv me-2"></i>Exportar CSV
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Tabla</th>
                                <th>ID Registro</th>
                                <th>IP</th>
                                <th>Fecha y Hora</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id_auditoria']); ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['accion']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tabla'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['id_registro'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['ip_usuario']); ?></td>
                                        <td><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($row['fecha_hora']))); ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['detalles'])): ?>
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                    data-bs-target="#detallesModal<?php echo $row['id_auditoria']; ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <div class="modal fade" id="detallesModal<?php echo $row['id_auditoria']; ?>"
                                                    tabindex="-1"
                                                    aria-labelledby="detallesModalLabel<?php echo $row['id_auditoria']; ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="detallesModalLabel<?php echo $row['id_auditoria']; ?>">
                                                                    Detalles de la Acción</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><?php echo nl2br(htmlspecialchars($row['detalles'])); ?></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cerrar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No hay registros de auditoría</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_paginas > 1): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Navegación de páginas">
                            <ul class="pagination">
                                <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link"
                                        href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?pagina=1&usuario=<?php echo urlencode($filtro_usuario); ?>&accion=<?php echo urlencode($filtro_accion); ?>&tabla=<?php echo urlencode($filtro_tabla); ?>&fecha_desde=<?php echo urlencode($filtro_fecha_desde); ?>&fecha_hasta=<?php echo urlencode($filtro_fecha_hasta); ?>"
                                        aria-label="Primera">
                                        <span aria-hidden="true">&laquo;&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link"
                                        href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?pagina=<?php echo $pagina_actual - 1; ?>&usuario=<?php echo urlencode($filtro_usuario); ?>&accion=<?php echo urlencode($filtro_accion); ?>&tabla=<?php echo urlencode($filtro_tabla); ?>&fecha_desde=<?php echo urlencode($filtro_fecha_desde); ?>&fecha_hasta=<?php echo urlencode($filtro_fecha_hasta); ?>"
                                        aria-label="Anterior">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>

                                <?php
                                $rango = 2;
                                $inicio_rango = max(1, $pagina_actual - $rango);
                                $fin_rango = min($total_paginas, $pagina_actual + $rango);

                                if ($inicio_rango > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '?pagina=1&usuario=' . urlencode($filtro_usuario) . '&accion=' . urlencode($filtro_accion) . '&tabla=' . urlencode($filtro_tabla) . '&fecha_desde=' . urlencode($filtro_fecha_desde) . '&fecha_hasta=' . urlencode($filtro_fecha_hasta) . '">1</a></li>';
                                    if ($inicio_rango > 2) {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                    }
                                }

                                for ($i = $inicio_rango; $i <= $fin_rango; $i++) {
                                    echo '<li class="page-item ' . (($i == $pagina_actual) ? 'active' : '') . '"><a class="page-link" href="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '?pagina=' . $i . '&usuario=' . urlencode($filtro_usuario) . '&accion=' . urlencode($filtro_accion) . '&tabla=' . urlencode($filtro_tabla) . '&fecha_desde=' . urlencode($filtro_fecha_desde) . '&fecha_hasta=' . urlencode($filtro_fecha_hasta) . '">' . $i . '</a></li>';
                                }

                                if ($fin_rango < $total_paginas) {
                                    if ($fin_rango < $total_paginas - 1) {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '?pagina=' . $total_paginas . '&usuario=' . urlencode($filtro_usuario) . '&accion=' . urlencode($filtro_accion) . '&tabla=' . urlencode($filtro_tabla) . '&fecha_desde=' . urlencode($filtro_fecha_desde) . '&fecha_hasta=' . urlencode($filtro_fecha_hasta) . '">' . $total_paginas . '</a></li>';
                                }
                                ?>

                                <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                                    <a class="page-link"
                                        href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?pagina=<?php echo $pagina_actual + 1; ?>&usuario=<?php echo urlencode($filtro_usuario); ?>&accion=<?php echo urlencode($filtro_accion); ?>&tabla=<?php echo urlencode($filtro_tabla); ?>&fecha_desde=<?php echo urlencode($filtro_fecha_desde); ?>&fecha_hasta=<?php echo urlencode($filtro_fecha_hasta); ?>"
                                        aria-label="Siguiente">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                                <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                                    <a class="page-link"
                                        href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?pagina=<?php echo $total_paginas; ?>&usuario=<?php echo urlencode($filtro_usuario); ?>&accion=<?php echo urlencode($filtro_accion); ?>&tabla=<?php echo urlencode($filtro_tabla); ?>&fecha_desde=<?php echo urlencode($filtro_fecha_desde); ?>&fecha_hasta=<?php echo urlencode($filtro_fecha_hasta); ?>"
                                        aria-label="Última">
                                        <span aria-hidden="true">&raquo;&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('btnExportarCSV').addEventListener('click', function () {
            window.location.href = 'exportar_auditoria.php?formato=csv&usuario=<?php echo urlencode($filtro_usuario); ?>&accion=<?php echo urlencode($filtro_accion); ?>&tabla=<?php echo urlencode($filtro_tabla); ?>&fecha_desde=<?php echo urlencode($filtro_fecha_desde); ?>&fecha_hasta=<?php echo urlencode($filtro_fecha_hasta); ?>';
        });

        document.getElementById('btnExportarPDF').addEventListener('click', function () {
            window.location.href = 'exportar_auditoria.php?formato=pdf&usuario=<?php echo urlencode($filtro_usuario); ?>&accion=<?php echo urlencode($filtro_accion); ?>&tabla=<?php echo urlencode($filtro_tabla); ?>&fecha_desde=<?php echo urlencode($filtro_fecha_desde); ?>&fecha_hasta=<?php echo urlencode($filtro_fecha_hasta); ?>';
        });
    </script>
</body>

</html>