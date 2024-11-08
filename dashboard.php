<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Determinar si el usuario es administrador
$is_admin = isset($_SESSION["rol"]) && $_SESSION["rol"] === "administrador";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-title {
            color: #764ba2;
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
            <a class="navbar-brand" href="#"><i class="fas fa-laptop me-2"></i>Control de Stock</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php"><i class="fas fa-user me-2"></i>Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION["username"]); ?></h2>
        <div class="row">
            <!-- Existing cards -->

            <!-- New Envíos card -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-truck fa-3x mb-3" style="color: #764ba2;"></i>
                        <h5 class="card-title">Envíos</h5>
                        <a href="envios.php" class="btn btn-primary">Gestionar Envíos</a>
                    </div>
                </div>
            </div>

            <!-- New Informes card -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-bar fa-3x mb-3" style="color: #764ba2;"></i>
                        <h5 class="card-title">Informes</h5>
                        <a href="informes.php" class="btn btn-primary">Ver Informes</a>
                    </div>
                </div>
            </div>

            <!-- New Reportes card -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-file-alt fa-3x mb-3" style="color: #764ba2;"></i>
                        <h5 class="card-title">Reportes</h5>
                        <a href="reportes.php" class="btn btn-primary">Ver Reportes</a>
                    </div>
                </div>
            </div>

            <!-- New Factura card -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-file-invoice fa-3x mb-3" style="color: #764ba2;"></i>
                        <h5 class="card-title">Factura</h5>
                        <a href="factura.php" class="btn btn-primary">Gestionar Facturas</a>
                    </div>
                </div>
            </div>

            <?php if ($is_admin): ?>
            <!-- Admin only: Mantenimientos card -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-tools fa-3x mb-3" style="color: #764ba2;"></i>
                        <h5 class="card-title">Mantenimientos</h5>
                        <a href="mantenimientos.php" class="btn btn-primary">Ver Mantenimientos</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Existing cards and content -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>