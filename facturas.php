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
$sql_create_table = "CREATE TABLE IF NOT EXISTS facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    numero_factura VARCHAR(50),
    monto DECIMAL(10, 2),
    fecha_emision DATE,
    estado VARCHAR(20)
)";
mysqli_query($link, $sql_create_table);

// Consulta para seleccionar las facturas del usuario
$sql = "SELECT * FROM facturas WHERE id_usuario = ?";
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
    <title>Facturas de Dispositivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 900px;
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
        <h2 class="mb-4"><i class="fas fa-file-invoice-dollar me-2"></i>Mis Facturas de Dispositivos</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Número de Factura</th>
                        <th>Monto</th>
                        <th>Fecha de Emisión</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['numero_factura']); ?></td>
                            <td>$<?php echo number_format($row['monto'], 2); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['fecha_emision'])); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $row['estado'] == 'Pagada' ? 'success' : 'warning'; ?>">
                                    <?php echo htmlspecialchars($row['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="ver_factura.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>Ver
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay facturas disponibles.</p>
        <?php endif; ?>
        <a href="dashboard.php" class="btn btn-secondary mt-3">
            <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
        </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>