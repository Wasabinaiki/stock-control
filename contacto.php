<?php
// contacto.php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contáctenos</title>
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
        .pqrs-button {
            background-color: #ffd700;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="dashboard.php" class="home-icon">🏠</a>
        <h1 class="text-center mb-0">CONTÁCTENOS</h1>
        <a href="#" class="float-end">⚙️</a>
    </div>
    
    <div class="content">
        <div class="container">
            <p class="text-justify">
                Si tiene alguna pregunta o inquietud sobre esta Política de Privacidad, contáctenos a:
            </p>
            <div class="mt-4">
                <p><strong>Control de Stock</strong></p>
                <p>juanpablorubiano1977@gmail.com</p>
                <p>3195536738</p>
                <p>Cra. 100 #5-169 Local 172</p>
            </div>
            <button class="pqrs-button" onclick="window.location.href='pqrs.php'">¿Generar PQRS?</button>
        </div>
    </div>
    
    <a href="javascript:history.back()" class="back-icon">⬅️</a>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>