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

// Obtener lista de envíos del usuario actual
$sql = "SELECT id_envio, estado_envio, fecha_salida, fecha_llegada FROM envios WHERE usuario_id = ? ORDER BY fecha_envio DESC";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $result_envios = mysqli_stmt_get_result($stmt);
} else {
    $error = "Error al preparar la consulta: " . mysqli_error($link);
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
            <a class="navbar-brand" href="#"><i class="fas fa-truck me-2"></i>Gestión de Envíos</a>
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
        <h2 class="mb-4">Gestión de Envíos</h2>
        
        <div class="mb-4">
            <a href="programar_mantenimiento.php" class="btn btn-primary">
                <i class="fas fa-calendar-plus me-2"></i>Programar Mantenimiento
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
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
                        while($row = mysqli_fetch_assoc($result_envios)) {
                            $estadoClass = ($row['estado_envio'] == 'Completado') ? 'estado-completado' : 'estado-proceso';
                            
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id_envio']) . "</td>";
                            echo "<td class='" . $estadoClass . "'>" . htmlspecialchars($row['estado_envio']) . "</td>";
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

