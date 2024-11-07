<?php
session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol adecuado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (!isset($_SESSION["rol"]) || ($_SESSION["rol"] !== "admin" && $_SESSION["rol"] !== "soporte")) {
    // Redirigir al dashboard si el rol no es admin o soporte
    header("location: dashboard.php");
    exit;
}

// Conexión a la base de datos
require_once "config.php";

// Obtener las solicitudes PQRS de la base de datos
$sql = "SELECT id, usuario, tipo, descripcion, estado, fecha_creacion FROM pqrs ORDER BY fecha_creacion DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de PQRS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .table-container {
            margin-top: 30px;
        }
        .table thead {
            background: #764ba2;
            color: white;
        }
        .badge {
            font-size: 0.9rem;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-warning {
            background-color: #ffc107;
        }
        .badge-danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Control de Stock - PQRS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container table-container">
        <h2 class="mb-4">Solicitudes PQRS</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Fecha de Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row["id"]; ?></td>
                                <td><?php echo htmlspecialchars($row["usuario"]); ?></td>
                                <td><?php echo htmlspecialchars($row["tipo"]); ?></td>
                                <td><?php echo htmlspecialchars($row["descripcion"]); ?></td>
                                <td>
                                    <?php 
                                        // Asignar color según estado
                                        switch ($row["estado"]) {
                                            case 'Pendiente':
                                                echo '<span class="badge badge-warning">Pendiente</span>';
                                                break;
                                            case 'Resuelto':
                                                echo '<span class="badge badge-success">Resuelto</span>';
                                                break;
                                            case 'En Proceso':
                                                echo '<span class="badge badge-danger">En Proceso</span>';
                                                break;
                                            default:
                                                echo '<span class="badge badge-secondary">Desconocido</span>';
                                                break;
                                        }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($row["fecha_creacion"]); ?></td>
                                <td>
                                    <a href="pqrs_view.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-info">Ver</a>
                                    <a href="pqrs_edit.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="pqrs_delete.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta solicitud?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay solicitudes PQRS registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
