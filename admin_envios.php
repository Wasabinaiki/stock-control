<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

$success_message = '';
$error_message = '';

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

$estado_filtro = isset($_GET['estado_filtro']) ? $_GET['estado_filtro'] : '';
$orden_fecha_salida = isset($_GET['orden_fecha_salida']) ? $_GET['orden_fecha_salida'] : '';
$orden_fecha_llegada = isset($_GET['orden_fecha_llegada']) ? $_GET['orden_fecha_llegada'] : '';

$sql = "SELECT e.*, u.username FROM envios e 
        JOIN usuarios u ON e.usuario_id = u.id_usuario 
        WHERE 1=1";

if (!empty($estado_filtro)) {
    $sql .= " AND e.estado_envio = '" . mysqli_real_escape_string($link, $estado_filtro) . "'";
}

if (!empty($orden_fecha_salida)) {
    $sql .= " ORDER BY e.fecha_salida " . ($orden_fecha_salida == 'asc' ? 'ASC' : 'DESC');
} elseif (!empty($orden_fecha_llegada)) {
    $sql .= " ORDER BY e.fecha_llegada " . ($orden_fecha_llegada == 'asc' ? 'ASC' : 'DESC');
} else {
    $sql .= " ORDER BY e.fecha_envio DESC";
}

$result = mysqli_query($link, $sql);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($link));
}

function formatearEstado($estado)
{
    $estado = str_replace('_', ' ', $estado);
    return ucwords($estado);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Envíos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .container {
            margin-top: 30px;
            padding-bottom: 30px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
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

        .table {
            margin-bottom: 0;
        }

        .btn-warning {
            color: white;
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .btn-warning:hover {
            color: white;
            background-color: #e0a800;
            border-color: #d39e00;
        }

        .btn-success {
            background: linear-gradient(135deg, #20bf6b 0%, #0b8a45 100%);
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #0b8a45 0%, #20bf6b 100%);
        }

        .badge {
            padding: 6px 10px;
            border-radius: 12px;
            font-weight: 500;
        }

        .bg-pendiente,
        .bg-en-proceso {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .bg-completado {
            background-color: #198754 !important;
            color: #fff !important;
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
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-truck me-2"></i>Administración de Envíos</a>
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

    <div class="container">
        <?php
        if (!empty($success_message)) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo '<i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($success_message);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }

        if (!empty($error_message)) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            echo '<i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($error_message);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
        ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Gestión de Envíos</h5>
            </div>
            <div class="card-body">
                <form action="" method="GET" class="filter-form">
                    <select name="estado_filtro" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="En Proceso" <?php echo $estado_filtro == 'En Proceso' ? 'selected' : ''; ?>>En
                            Proceso</option>
                        <option value="Completado" <?php echo $estado_filtro == 'Completado' ? 'selected' : ''; ?>>
                            Completado</option>
                    </select>

                    <select name="orden_fecha_salida" class="form-select">
                        <option value="">Ordenar por fecha de salida</option>
                        <option value="desc" <?php echo $orden_fecha_salida == 'desc' ? 'selected' : ''; ?>>Más reciente
                            primero</option>
                        <option value="asc" <?php echo $orden_fecha_salida == 'asc' ? 'selected' : ''; ?>>Más antiguo
                            primero</option>
                    </select>

                    <select name="orden_fecha_llegada" class="form-select">
                        <option value="">Ordenar por fecha de llegada</option>
                        <option value="desc" <?php echo $orden_fecha_llegada == 'desc' ? 'selected' : ''; ?>>Más reciente
                            primero</option>
                        <option value="asc" <?php echo $orden_fecha_llegada == 'asc' ? 'selected' : ''; ?>>Más antiguo
                            primero</option>
                    </select>

                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID Envío</th>
                                <th>Usuario</th>
                                <th>Destino</th>
                                <th>Estado</th>
                                <th>Fecha de Salida</th>
                                <th>Fecha de Llegada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $rowClass = ($row['estado_envio'] == 'Completado') ? 'completed' : '';

                                    $badgeClass = '';
                                    if (strtolower($row['estado_envio']) == 'completado') {
                                        $badgeClass = 'bg-completado';
                                    } else {
                                        $badgeClass = 'bg-en-proceso';
                                    }

                                    echo "<tr class='" . $rowClass . "'>";
                                    echo "<td>" . htmlspecialchars($row['id_envio']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['direccion_destino']) . "</td>";
                                    echo "<td><span class='badge " . $badgeClass . "'>" . formatearEstado($row['estado_envio']) . "</span></td>";
                                    echo "<td>" . htmlspecialchars($row['fecha_salida'] ?? 'Pendiente') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['fecha_llegada'] ?? 'Pendiente') . "</td>";
                                    echo "<td class='text-nowrap'>";
                                    echo "<a href='admin_envio_editar.php?id=" . htmlspecialchars($row['id_envio']) . "' class='btn btn-sm btn-warning me-2'>";
                                    echo "<i class='fas fa-edit me-1'></i>Editar</a>";

                                    if ($row['estado_envio'] != 'Completado') {
                                        echo "<a href='admin_envio_completar.php?id=" . htmlspecialchars($row['id_envio']) . "' class='btn btn-sm btn-success'>";
                                        echo "<i class='fas fa-check-circle me-1'></i>Completar</a>";
                                    } else {
                                        echo "<button class='btn btn-sm btn-secondary' disabled>";
                                        echo "<i class='fas fa-check-circle me-1'></i>Completado</button>";
                                    }

                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No hay envíos registrados</td></tr>";
                            }
                            mysqli_free_result($result);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
mysqli_close($link);
?>