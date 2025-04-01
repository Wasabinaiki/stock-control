<?php
// ayuda.php
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
    <title>Ayuda - Control de Stock</title>
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
        .content {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }
        h1, h2 {
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
            <a class="navbar-brand" href="#"><i class="fas fa-question-circle me-2"></i>Ayuda</a>
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
    
    <div class="container mt-4">
        <div class="content">
            <h1 class="mb-4">Centro de Ayuda</h1>
            <p class="text-justify">
                Si necesitas asistencia en el uso de nuestra plataforma, estás en el lugar correcto. En esta sección encontrarás respuestas a las preguntas más frecuentes, guías paso a paso para la gestión de equipos, recuperación de contraseñas, y la administración de licencias. Si no encuentras lo que buscas, no dudes en ponerte en contacto con nuestro equipo de soporte, disponible para resolver cualquier inquietud que tengas. Estamos comprometidos en ofrecerte una experiencia sin complicaciones, asegurando que siempre puedas sacar el máximo provecho de nuestra plataforma.
            </p>
            
            <h2 class="mt-4">Preguntas Frecuentes</h2>
            <div class="accordion mt-3" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            ¿Cómo puedo registrar un nuevo dispositivo?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Para registrar un nuevo dispositivo, dirígete a la sección correspondiente (Computadoras, Tablets o Celulares) desde el Dashboard. Luego, haz clic en el botón "Agregar Nuevo" y completa el formulario con los datos del dispositivo.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            ¿Cómo programo un mantenimiento?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Para programar un mantenimiento, selecciona el dispositivo desde tu lista de dispositivos y haz clic en "Programar Mantenimiento". Selecciona la fecha deseada, describe el tipo de mantenimiento necesario y confirma la solicitud.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            ¿Cómo puedo recuperar mi contraseña?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Si olvidaste tu contraseña, haz clic en el enlace "¿Olvidaste tu contraseña?" en la página de inicio de sesión. Ingresa tu correo electrónico registrado y sigue las instrucciones que recibirás para crear una nueva contraseña.
                        </div>
                    </div>
                </div>
            </div>
            
            <h2 class="mt-4">Contacto de Soporte</h2>
            <p>Si necesitas ayuda adicional, no dudes en contactarnos:</p>
            <ul>
                <li><i class="fas fa-envelope me-2"></i>Correo electrónico: soporte@stockcontrol.com</li>
                <li><i class="fas fa-phone me-2"></i>Teléfono: (123) 456-7890</li>
                <li><i class="fas fa-clock me-2"></i>Horario de atención: Lunes a Viernes, 8:00 AM - 6:00 PM</li>
            </ul>
        </div>
        
        <div class="mt-4 mb-4">
            <a href="dashboard.php" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Volver al Dashboard</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>