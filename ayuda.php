<?php
// ayuda.php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda</title>
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
        <h1 class="text-center mb-0">AYUDA</h1>
        <a href="#" class="float-end">⚙️</a>
    </div>
    
    <div class="content">
        <div class="container">
            <p class="text-justify">
                Si necesitas asistencia en el uso de nuestra plataforma, estás en el lugar correcto. En esta sección encontrarás respuestas a las preguntas más frecuentes, guías paso a paso para la gestión de equipos, recuperación de contraseñas, y la administración de licencias. Si no encuentras lo que buscas, no dudes en ponerte en contacto con nuestro equipo de soporte, disponible para resolver cualquier inquietud que tengas. Estamos comprometidos en ofrecerte una experiencia sin complicaciones, asegurando que siempre puedas sacar el máximo provecho de nuestra plataforma.
            </p>
        </div>
    </div>
    
    <a href="javascript:history.back()" class="back-icon">⬅️</a>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>