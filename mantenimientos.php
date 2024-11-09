<?php
session_start();
require_once "includes/config.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Determinar si el usuario es administrador
$is_admin = isset($_SESSION["rol"]) && $_SESSION["rol"] === "administrador";

// Consulta SQL base
$sql = "SELECT m.*, d.tipo as tipo_dispositivo, d.marca, d.modelo 
        FROM mantenimientos m 
        JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo";

// Si no es administrador, filtrar solo los mantenimientos del usuario actual
if (!$is_admin) {
    $sql .= " WHERE m.id_usuario = " . $_SESSION['id_usuario'];
}

$sql .= " ORDER BY m.fecha_programada DESC";

$result_mantenimientos = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimientos</title>
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-tools me-2"></i>Mantenimientos</a>
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

    <div class="container mt-5">
        <h2 class="mb-4">Mantenimientos <?php echo $is_admin ? 'de todos los usuarios' : 'programados'; ?></h2>
        
        <div class="mb-4">
            <a href="programar_mantenimiento.php" class="btn btn-primary">
                <i class="fas fa-calendar-plus me-2"></i>Programar Mantenimiento
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Dispositivo</th>
                        <th>Fecha Programada</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result_mantenimientos) > 0) {
                        while($row = mysqli_fetch_assoc($result_mantenimientos)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tipo_dispositivo'] . ' ' . $row['marca'] . ' ' . $row['modelo']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['fecha_programada']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['descripcion']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
                            echo "<td>";
                            echo "<a href='ver_mantenimiento.php?id=" . $row['id'] . "' class='btn btn-sm btn-info me-2'><i class='fas fa-eye'></i></a>";
                            if ($is_admin || $row['estado'] !== 'completado') {
                                echo "<a href='editar_mantenimiento.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning me-2'><i class='fas fa-edit'></i></a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No hay mantenimientos programados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>