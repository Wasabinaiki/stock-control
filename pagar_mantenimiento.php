<?php
session_start();
require_once "includes/config.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$id_mantenimiento = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mensaje = '';
$error = '';

// Obtener detalles del mantenimiento
$sql = "SELECT m.*, d.marca, d.modelo FROM mantenimientos m 
        JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo 
        WHERE m.id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id_mantenimiento);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $mantenimiento = mysqli_fetch_assoc($result);
    } else {
        $error = "Error al obtener los detalles del mantenimiento.";
    }
    mysqli_stmt_close($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pagar'])) {
    // Aquí iría la lógica de procesamiento del pago
    // Por ahora, simplemente actualizaremos el estado del mantenimiento
    $sql_update = "UPDATE mantenimientos SET estado = 'pagado' WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql_update)) {
        mysqli_stmt_bind_param($stmt, "i", $id_mantenimiento);
        if (mysqli_stmt_execute($stmt)) {
            // Generar factura
            $numero_factura = 'FACT-' . date('YmdHis') . '-' . $id_mantenimiento;
            $monto = 19900.00; // Monto fijo de $19.900 COP
            $fecha_emision = date('Y-m-d');
            $estado = 'Pagada';
            
            $sql_factura = "INSERT INTO facturas (id_usuario, numero_factura, monto, fecha_emision, estado) VALUES (?, ?, ?, ?, ?)";
            if ($stmt_factura = mysqli_prepare($link, $sql_factura)) {
                mysqli_stmt_bind_param($stmt_factura, "isdss", $_SESSION["id"], $numero_factura, $monto, $fecha_emision, $estado);
                if (mysqli_stmt_execute($stmt_factura)) {
                    $mensaje = "Pago procesado con éxito. El mantenimiento ha sido confirmado y se ha generado la factura.";
                } else {
                    $error = "Error al generar la factura. Por favor, contacte al administrador.";
                }
                mysqli_stmt_close($stmt_factura);
            } else {
                $error = "Error al preparar la consulta de factura. Por favor, contacte al administrador.";
            }
        } else {
            $error = "Error al procesar el pago. Por favor, inténtelo de nuevo.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar Mantenimiento</title>
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
            max-width: 600px;
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
            <a class="navbar-brand" href="#"><i class="fas fa-credit-card me-2"></i>Pago de Mantenimiento</a>
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
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">Pagar Mantenimiento</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-success"><?php echo $mensaje; ?></div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (isset($mantenimiento)): ?>
                    <h4>Detalles del Mantenimiento</h4>
                    <p><strong>Dispositivo:</strong> <?php echo htmlspecialchars($mantenimiento['marca'] . ' ' . $mantenimiento['modelo']); ?></p>
                    <p><strong>Fecha Programada:</strong> <?php echo htmlspecialchars($mantenimiento['fecha_programada']); ?></p>
                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($mantenimiento['descripcion']); ?></p>
                    <p><strong>Monto a Pagar:</strong> $19.900 COP</p>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_mantenimiento); ?>" method="post">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre en la Tarjeta</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="numero_tarjeta" class="form-label">Número de Tarjeta</label>
                            <input type="text" class="form-control" id="numero_tarjeta" name="numero_tarjeta" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                                <input type="text" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" placeholder="MM/AA" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control" id="cvv" name="cvv" required>
                            </div>
                        </div>
                        <button type="submit" name="pagar" class="btn btn-primary btn-lg w-100">Pagar $19.900 COP</button>
                    </form>
                <?php else: ?>
                    <p>No se encontró información del mantenimiento.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>