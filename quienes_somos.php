<?php
session_start();

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
    <title>¿Quiénes Somos? - Control de Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .content {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }

        h1,
        h2 {
            color: #764ba2;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .team-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-users me-2"></i>Quiénes Somos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="content">
            <h1 class="mb-4">¿Quiénes Somos?</h1>
            <p class="mb-5">
                Somos una plataforma dedicada a la gestión eficiente de equipos tecnológicos dentro de las empresas.
                Nuestro objetivo es optimizar el control y seguimiento de los dispositivos utilizados por los empleados,
                asegurando su correcto funcionamiento y facilitando la programación de mantenimientos, actualizaciones y
                licencias. Con un enfoque en la innovación y el servicio al cliente, buscamos simplificar la
                administración de recursos tecnológicos, mejorando la productividad y garantizando la seguridad de los
                activos en cada organización. Nuestro compromiso es brindar soluciones confiables, ágiles y adaptadas a
                las necesidades de cada empresa.
            </p>

            <h2 class="mb-4">Nuestro Equipo de Desarrollo</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card team-card">
                        <div class="card-body">
                            <h5 class="card-title">Andrés Camilo Hoyos</h5>
                            <p class="card-text">
                                <strong>Correo:</strong> ahoyos0124@gmail.com<br>
                                <strong>Rol:</strong> Desarrollador<br>
                                <strong>Edad:</strong> 22 años
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card team-card">
                        <div class="card-body">
                            <h5 class="card-title">Daniel Santiago Truque Martínez</h5>
                            <p class="card-text">
                                <strong>Correo:</strong> truquemdaniels@gmail.com<br>
                                <strong>Rol:</strong> Desarrollador<br>
                                <strong>Edad:</strong> 18 años
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card team-card">
                        <div class="card-body">
                            <h5 class="card-title">Juan Pablo Rubiano Quiceno</h5>
                            <p class="card-text">
                                <strong>Correo:</strong> jpamgo@gmail.com<br>
                                <strong>Rol:</strong> Desarrollador<br>
                                <strong>Edad:</strong> 20 años
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card team-card">
                        <div class="card-body">
                            <h5 class="card-title">Sebastián Arcos Valverde</h5>
                            <p class="card-text">
                                <strong>Correo:</strong> sebasvar@gmail.com<br>
                                <strong>Rol:</strong> Desarrollador<br>
                                <strong>Edad:</strong> 18 años
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-4">
            <a href="dashboard.php" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Volver al
                Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>