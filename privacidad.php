<?php
// privacidad.php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Políticas y Privacidad</title>
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
        <h1 class="text-center mb-0">POLÍTICAS Y PRIVACIDAD</h1>
        <a href="#" class="float-end">⚙️</a>
    </div>
    
    <div class="content">
        <div class="container">
            <p class="text-justify">
                Al utilizar esta aplicación, el usuario acepta cumplir con todas las normas y condiciones establecidas en los presentes términos de uso. La información proporcionada en la plataforma, incluyendo datos personales y credenciales, será tratada de manera confidencial conforme a la legislación vigente en materia de protección de datos. La plataforma se reserva el derecho de suspender o restringir el acceso en caso de uso indebido, intento de acceso no autorizado o violación de nuestras políticas. Asimismo, es responsabilidad del usuario mantener la seguridad de sus credenciales y notificar cualquier actividad sospechosa relacionada con su cuenta. Cualquier actualización o cambio en las políticas será notificado oportunamente a los usuarios.
            </p>
        </div>
    </div>
    
    <a href="javascript:history.back()" class="back-icon">⬅️</a>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>