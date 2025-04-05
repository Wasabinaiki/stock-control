<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

$tipo_filtro = isset($_GET['tipo_filtro']) ? $_GET['tipo_filtro'] : '';
$estado_filtro = isset($_GET['estado_filtro']) ? $_GET['estado_filtro'] : '';
$orden_fecha = isset($_GET['orden_fecha']) ? $_GET['orden_fecha'] : 'desc';

$sql = "SELECT p.*, u.username FROM pqrs p JOIN usuarios u ON p.id_usuario = u.id_usuario WHERE 1=1";

if (!empty($tipo_filtro)) {
    $sql .= " AND p.tipo = '" . mysqli_real_escape_string($link, $tipo_filtro) . "'";
}

if (!empty($estado_filtro)) {
    $sql .= " AND p.estado = '" . mysqli_real_escape_string($link, $estado_filtro) . "'";
}

$sql .= " ORDER BY p.fecha_creacion " . ($orden_fecha == 'asc' ? 'ASC' : 'DESC');

$result_pqrs = mysqli_query($link, $sql);

function debug_to_console($data)
{
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug: " . addslashes($output) . "');</script>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PQRs Registrados</title>
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

        .bg-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .bg-info {
            background-color: #0d6efd !important;
            color: #fff !important;
        }

        .bg-success {
            background-color: #198754 !important;
            color: #fff !important;
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
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-clipboard-list me-2"></i>PQRs Registrados</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard Administrador
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>PQRs Registrados</h5>
            </div>
            <div class="card-body">
                <form action="" method="GET" class="filter-form">
                    <select name="tipo_filtro" class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="peticion" <?php echo $tipo_filtro == 'peticion' ? 'selected' : ''; ?>>Petición
                        </option>
                        <option value="queja" <?php echo $tipo_filtro == 'queja' ? 'selected' : ''; ?>>Queja</option>
                        <option value="reclamo" <?php echo $tipo_filtro == 'reclamo' ? 'selected' : ''; ?>>Reclamo
                        </option>
                        <option value="sugerencia" <?php echo $tipo_filtro == 'sugerencia' ? 'selected' : ''; ?>>
                            Sugerencia</option>
                    </select>

                    <select name="estado_filtro" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" <?php echo $estado_filtro == 'pendiente' ? 'selected' : ''; ?>>Pendiente
                        </option>
                        <option value="en_proceso" <?php echo $estado_filtro == 'en_proceso' ? 'selected' : ''; ?>>En
                            proceso</option>
                        <option value="resuelto" <?php echo $estado_filtro == 'resuelto' ? 'selected' : ''; ?>>Resuelto
                        </option>
                    </select>

                    <select name="orden_fecha" class="form-select">
                        <option value="desc" <?php echo $orden_fecha == 'desc' ? 'selected' : ''; ?>>Más reciente primero
                        </option>
                        <option value="asc" <?php echo $orden_fecha == 'asc' ? 'selected' : ''; ?>>Más antiguo primero
                        </option>
                    </select>

                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result_pqrs) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result_pqrs)): ?>
                                    <?php
                                    debug_to_console("Estado para ID " . $row['id'] . ": '" . $row['estado'] . "'");
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                                        <td>
                                            <?php
                                            $estado = isset($row['estado']) ? trim(strtolower($row['estado'])) : '';

                                            $badgeClass = 'bg-secondary';
                                            $estadoTexto = 'Desconocido';

                                            if ($estado == 'pendiente') {
                                                $badgeClass = 'bg-warning';
                                                $estadoTexto = 'Pendiente';
                                            } elseif ($estado == 'en_proceso' || $estado == 'en proceso' || $estado == 'enproceso') {
                                                $badgeClass = 'bg-info';
                                                $estadoTexto = 'En proceso';
                                            } elseif ($estado == 'resuelto') {
                                                $badgeClass = 'bg-success';
                                                $estadoTexto = 'Resuelto';
                                            } else {
                                                $estadoTexto = empty($estado) ? 'Pendiente' : ucfirst($estado);
                                                $badgeClass = 'bg-warning';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo $estadoTexto; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['fecha_creacion']))); ?>
                                        </td>
                                        <td>
                                            <a href="editar_pqr.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay PQRs registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>