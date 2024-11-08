<?php
// quienes_somos.php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¿Quiénes Somos?</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header {
            background-color: #dc3545;
            color: white;
            padding: 15px;
            position: relative;
        }
        .home-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
        }
        .back-icon {
            position: absolute;
            left: 15px;
            bottom: 15px;
        }
        .content {
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: calc(100vh - 60px);
        }
        .icon {
            width: 30px;
            height: 30px;
        }
        a {
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="dashboard.php" class="home-icon">🏠</a>
        <h1 class="text-center mb-0">¿QUIENES SOMOS?</h1>
        <a href="#" class="float-end">⚙️</a>
    </div>
    
    <div class="content">
        <div class="container">
            <p class="text-justify">
                Somos una plataforma dedicada a la gestión eficiente de equipos tecnológicos dentro de las empresas. Nuestro objetivo es optimizar el control y seguimiento de los dispositivos utilizados por los empleados, asegurando su correcto funcionamiento y facilitando la programación de mantenimientos, actualizaciones y licencias. Con un enfoque en la innovación y el servicio al cliente, buscamos simplificar la administración de recursos tecnológicos, mejorando la productividad y garantizando la seguridad de los activos en cada organización. Nuestro compromiso es brindar soluciones confiables, ágiles y adaptadas a las necesidades de cada empresa.
            </p>
        </div>
    </div>
    
    <a href="javascript:history.back()" class="back-icon">⬅️</a>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>