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

// Ahora solo actualizamos el estado a "pendiente de pago" en lugar de procesarlo directamente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmar'])) {
    $sql_update = "UPDATE mantenimientos SET estado = 'pendiente de pago' WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql_update)) {
        mysqli_stmt_bind_param($stmt, "i", $id_mantenimiento);
        if (mysqli_stmt_execute($stmt)) {
            $mensaje = "Solicitud registrada con éxito. Un administrador verificará tu pago y se pondrá en contacto contigo para confirmar el mantenimiento.";
        } else {
            $error = "Error al registrar la solicitud. Por favor, contacte al administrador.";
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
        .qr-container {
            text-align: center;
            margin: 20px auto;
            max-width: 300px;
        }
        .qr-code {
            width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .payment-instructions {
            background-color: #f0f8ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
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

                    <div class="payment-instructions">
                        <h5><i class="fas fa-info-circle me-2"></i>Instrucciones de Pago</h5>
                        <p>Realiza el pago usando este código QR de Nequi y deja tus datos en la descripción del pago. Una vez se registre la transacción un administrador registrará tu mantenimiento y se pondrá en contacto contigo para proceder con el mantenimiento de tu dispositivo.</p>
                    </div>

                    <!-- Código QR de Nequi -->
                    <div class="qr-container">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/QR-UdE147canGWl1Z7XbuOIASjdYdUqPw.jpeg" alt="Código QR de Nequi - DANIEL TRUQUE" class="qr-code">
                        <p class="mt-3 text-muted">Escanea este código con la app de Nequi</p>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Importante:</strong> En la descripción del pago incluye tu nombre completo y el ID de mantenimiento: <strong><?php echo $id_mantenimiento; ?></strong>
                    </div>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id_mantenimiento); ?>" method="post">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="confirmacion" name="confirmacion" required>
                            <label class="form-check-label" for="confirmacion">
                                He realizado el pago o lo realizaré en breve
                            </label>
                        </div>
                        <button type="submit" name="confirmar" class="btn btn-primary btn-lg w-100">Confirmar Solicitud</button>
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