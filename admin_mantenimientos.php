<?php
session_start();
require_once "includes/config.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador"){
    header("location: login.php");
    exit;
}

// Función para formatear el estado
function formatearEstado($estado) {
    // Primero convertir a minúsculas y reemplazar guiones bajos por espacios
    $estado = strtolower(str_replace('_', ' ', $estado));
    // Capitalizar la primera letra de cada palabra
    return ucwords($estado);
}

// Obtener lista de mantenimientos
$sql = "SELECT m.*, d.marca, d.modelo, u.username 
        FROM mantenimientos m 
        JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
        JOIN usuarios u ON d.id_usuario = u.id_usuario
        ORDER BY m.fecha_programada DESC";
$result_mantenimientos = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimientos Programados</title>
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
        .navbar-brand, .nav-link {
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
        /* Estados estandarizados */
        .bg-pendiente {
            background-color: #ffc107 !important; /* Amarillo para pendiente/programado */
            color: #000 !important;
        }
        .bg-en-proceso {
            background-color: #0d6efd !important; /* Azul para en proceso */
            color: #fff !important;
        }
        .bg-completado {
            background-color: #198754 !important; /* Verde para completado */
            color: #fff !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-tools me-2"></i>Mantenimientos Programados</a>
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
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Mantenimientos Programados</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Dispositivo</th>
                                <th>Usuario</th>
                                <th>Fecha Programada</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result_mantenimientos) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result_mantenimientos)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['marca'] . ' ' . $row['modelo']); ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['fecha_programada']))); ?></td>
                                    <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
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
                                    <td>
                                        <a href="editar_mantenimiento.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No hay mantenimientos programados</td>
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