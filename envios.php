<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
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
    </style>
</head>
<body>
    <!-- Incluir la barra de navegación -->
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Gestión de Envíos</h2>
        
        <div class="mb-4">
            <a href="programar_mantenimiento.php" class="btn btn-primary">
                <i class="fas fa-calendar-plus me-2"></i>Programar Mantenimiento
            </a>
        </div>

        <!-- Aquí puedes agregar una tabla o lista de envíos -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Envío</th>
                    <th>Destino</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí irían los datos de los envíos -->
                <tr>
                    <td>1</td>
                    <td>Ciudad A</td>
                    <td>En tránsito</td>
                    <td>2023-06-15</td>
                </tr>
                <!-- Más filas... -->
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>