<?php
session_start();
require_once "includes/config.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Verificar si id está definido en la sesión
if (!isset($_SESSION["id"])) {
    header("location: error.php?mensaje=Sesión inválida");
    exit;
}

$id_usuario = $_SESSION["id"];

// Crear la tabla si no existe
$sql_create_table = "CREATE TABLE IF NOT EXISTS reportes (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    id_dispositivo INT,
    descripcion TEXT,
    fecha_reporte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado_reporte ENUM('En Revisión', 'Finalizado') NOT NULL
)";
mysqli_query($link, $sql_create_table);

// Consulta para seleccionar los reportes relacionados con los dispositivos del usuario
$sql = "SELECT r.* FROM reportes r
        INNER JOIN dispositivos d ON r.id_dispositivo = d.id_dispositivo
        WHERE d.id_usuario = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Dispositivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
        }
        .table {
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Mis Reportes de Dispositivos</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Dispositivo</th>
                        <th>Descripción</th>
                        <th>Fecha de Reporte</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id_dispositivo']); ?></td>
                            <td><?php echo htmlspecialchars(substr($row['descripcion'], 0, 50)) . '...'; ?></td>
                            <td><?php echo htmlspecialchars($row['fecha_reporte']); ?></td>
                            <td><?php echo htmlspecialchars($row['estado_reporte']); ?></td>
                            <td>
                                <a href="ver_reporte.php?id=<?php echo $row['id_reporte']; ?>" class="btn btn-primary btn-sm">Ver</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-info">No hay reportes disponibles.</p>
        <?php endif; ?>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Volver al Dashboard</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>