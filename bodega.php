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

$sql = "SELECT id_dispositivo, tipo, marca, modelo, estado FROM dispositivos WHERE 1=1";
if (!empty($estado_filtro)) {
    $sql .= " AND estado = '" . mysqli_real_escape_string($link, $estado_filtro) . "'";
}
$sql .= " ORDER BY id_dispositivo DESC";

$result = mysqli_query($link, $sql);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($link));
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bodega</title>
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

        .table {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, .02);
        }

        .completed {
            background-color: #d4edda !important;
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

        .bg-completado,
        .bg-activo {
            background-color: #198754 !important;
            color: #fff !important;
        }

        .bg-inactivo {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .bg-reparacion {
            background-color: #0d6efd !important;
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
            <a class="navbar-brand" href="#"><i class="fas fa-warehouse me-2"></i>Gestión de Bodega</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php"><i
                                class="fas fa-tachometer-alt me-2"></i>Dashboard Administrador</a>
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
                <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Gestión de Bodega</h5>
            </div>
            <div class="card-body">
                <form action="" method="GET" class="filter-form">
                    <select name="estado_filtro" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="Activo" <?php echo $estado_filtro == 'Activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="Inactivo" <?php echo $estado_filtro == 'Inactivo' ? 'selected' : ''; ?>>Inactivo
                        </option>
                        <option value="En reparación" <?php echo $estado_filtro == 'En reparación' ? 'selected' : ''; ?>>
                            En reparación</option>
                        <option value="Completado" <?php echo $estado_filtro == 'Completado' ? 'selected' : ''; ?>>
                            Completado</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $rowClass = ($row['estado'] == 'Completado') ? 'completed' : '';

                                    $badgeClass = '';
                                    if (strtolower($row['estado']) == 'completado') {
                                        $badgeClass = 'bg-completado';
                                    } elseif (strtolower($row['estado']) == 'activo') {
                                        $badgeClass = 'bg-activo';
                                    } elseif (strtolower($row['estado']) == 'en reparación') {
                                        $badgeClass = 'bg-reparacion';
                                    } else {
                                        $badgeClass = 'bg-inactivo';
                                    }

                                    echo "<tr class='" . $rowClass . "'>";
                                    echo "<td>" . htmlspecialchars($row['id_dispositivo']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['marca']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
                                    echo "<td><span class='badge " . $badgeClass . "'>" . htmlspecialchars($row['estado']) . "</span></td>";
                                    echo "<td class='text-nowrap'>";
                                    echo "<a href='bodega_editar.php?id=" . htmlspecialchars($row['id_dispositivo']) . "' class='btn btn-sm btn-warning me-2'>";
                                    echo "<i class='fas fa-edit me-1'></i>Editar</a>";

                                    if ($row['estado'] != 'Completado') {
                                        echo "<a href='bodega_eliminar.php?id=" . htmlspecialchars($row['id_dispositivo']) . "' class='btn btn-sm btn-success'>";
                                        echo "<i class='fas fa-check-circle me-1'></i>Completar</a>";
                                    } else {
                                        echo "<button class='btn btn-sm btn-secondary' disabled>";
                                        echo "<i class='fas fa-check-circle me-1'></i>Completado</button>";
                                    }

                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No hay dispositivos en la bodega</td></tr>";
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