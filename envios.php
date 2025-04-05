<?php
session_start();
require_once "includes/config.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Obtener el ID del usuario actual
$usuario_id = $_SESSION["id"];

// Filtros
$estado_filtro = isset($_GET['estado_filtro']) ? $_GET['estado_filtro'] : '';
$orden_fecha_salida = isset($_GET['orden_fecha_salida']) ? $_GET['orden_fecha_salida'] : '';
$orden_fecha_llegada = isset($_GET['orden_fecha_llegada']) ? $_GET['orden_fecha_llegada'] : '';

// Obtener lista de envíos del usuario actual con filtros
$sql = "SELECT id_envio, estado_envio, fecha_salida, fecha_llegada FROM envios WHERE usuario_id = ?";

// Aplicar filtros
if (!empty($estado_filtro)) {
    $sql .= " AND estado_envio = '" . mysqli_real_escape_string($link, $estado_filtro) . "'";
}

// Ordenar por fecha de salida o llegada si se especifica
if (!empty($orden_fecha_salida)) {
    $sql .= " ORDER BY fecha_salida " . ($orden_fecha_salida == 'asc' ? 'ASC' : 'DESC');
} elseif (!empty($orden_fecha_llegada)) {
    $sql .= " ORDER BY fecha_llegada " . ($orden_fecha_llegada == 'asc' ? 'ASC' : 'DESC');
} else {
    $sql .= " ORDER BY fecha_envio DESC";
}

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $result_envios = mysqli_stmt_get_result($stmt);
} else {
    $error = "Error al preparar la consulta: " . mysqli_error($link);
}

// Función para formatear el estado
function formatearEstado($estado)
{
    // Primero convertir a minúsculas y reemplazar guiones bajos por espacios
    $estado = str_replace('_', ' ', $estado);
    // Capitalizar la primera letra de cada palabra
    return ucwords($estado);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Envíos</title>
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

        /* Estados estandarizados */
        .badge {
            padding: 6px 10px;
            border-radius: 12px;
            font-weight: 500;
        }

        .bg-pendiente,
        .bg-en-proceso {
            background-color: #ffc107 !important;
            /* Amarillo para en proceso */
            color: #000 !important;
        }

        .bg-completado {
            background-color: #198754 !important;
            /* Verde para completado */
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
            <a class="navbar-brand" href="#"><i class="fas fa-truck me-2"></i>Gestión de Envíos</a>
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

    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Gestión de Envíos</h5>
            </div>
            <div class="card-body">
                <!-- Botón para programar mantenimiento -->
                <div class="mb-4">
                    <a href="programar_mantenimiento.php" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i>Programar Mantenimiento
                    </a>
                </div>

                <!-- Filtros -->
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
                                <th>Estado</th>
                                <th>Fecha de Salida</th>
                                <th>Fecha de Llegada</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($result_envios) && mysqli_num_rows($result_envios) > 0) {
                                while ($row = mysqli_fetch_assoc($result_envios)) {
                                    // Determinar la clase de badge según el estado
                                    $badgeClass = '';
                                    if (strtolower($row['estado_envio']) == 'completado') {
                                        $badgeClass = 'bg-completado';
                                    } else {
                                        $badgeClass = 'bg-en-proceso';
                                    }

                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['id_envio']) . "</td>";
                                    echo "<td><span class='badge " . $badgeClass . "'>" . formatearEstado($row['estado_envio']) . "</span></td>";
                                    echo "<td>" . htmlspecialchars($row['fecha_salida'] ?? 'Pendiente') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['fecha_llegada'] ?? 'Pendiente') . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No hay envíos registrados</td></tr>";
                            }
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