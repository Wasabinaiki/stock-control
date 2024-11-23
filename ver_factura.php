<?php
session_start();
require_once "includes/config.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Verificar si se proporcionó un ID de factura
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: error.php?mensaje=ID de factura inválido");
    exit;
}

$id_factura = intval($_GET['id']);
$id_usuario = $_SESSION["id"];

// Obtener los detalles de la factura
$sql = "SELECT f.*, m.descripcion, d.marca, d.modelo 
        FROM facturas f
        LEFT JOIN mantenimientos m ON f.id_mantenimiento = m.id
        LEFT JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
        WHERE f.id = ? AND f.id_usuario = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id_factura, $id_usuario);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("location: error.php?mensaje=Factura no encontrada");
    exit;
}

$factura = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Factura</title>
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
        .container {
            max-width: 800px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            border-radius: 10px 10px 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-file-invoice-dollar me-2"></i>Ver Factura</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="facturas.php"><i class="fas fa-file-invoice me-2"></i>Mis Facturas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">Factura #<?php echo htmlspecialchars($factura['numero_factura']); ?></h2>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Detalles de la Factura</h5>
                        <p><strong>Número de Factura:</strong> <?php echo htmlspecialchars($factura['numero_factura']); ?></p>
                        <p><strong>Fecha de Emisión:</strong> <?php echo date('d/m/Y', strtotime($factura['fecha_emision'])); ?></p>
                        <p><strong>Estado:</strong> 
                            <span class="badge bg-<?php echo $factura['estado'] == 'Pagada' ? 'success' : 'warning'; ?>">
                                <?php echo htmlspecialchars($factura['estado']); ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5>Detalles del Mantenimiento</h5>
                        <?php if (!empty($factura['descripcion'])): ?>
                            <p><strong>Dispositivo:</strong> <?php echo htmlspecialchars($factura['marca'] . ' ' . $factura['modelo']); ?></p>
                            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($factura['descripcion']); ?></p>
                        <?php else: ?>
                            <p>No hay detalles de mantenimiento asociados a esta factura.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Resumen de Pago</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Servicio de Mantenimiento</td>
                                    <td class="text-end">$<?php echo number_format($factura['monto'], 2); ?></td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <th class="text-end">$<?php echo number_format($factura['monto'], 2); ?></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="facturas.php" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Volver a Mis Facturas</a>
                <button class="btn btn-secondary ms-2" onclick="window.print()"><i class="fas fa-print me-2"></i>Imprimir Factura</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>