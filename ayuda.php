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

        .help-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .help-card:hover {
            transform: translateY(-5px);
        }

        .help-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            border-radius: 10px 10px 0 0 !important;
        }

        .help-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #667eea;
        }

        .contact-info {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
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
            <h1 class="mb-4">Centro de Ayuda</h1>
            <p class="text-justify">
                Si necesitas asistencia en el uso de nuestra plataforma, estás en el lugar correcto. En esta sección
                encontrarás recursos útiles para aprovechar al máximo todas las funcionalidades de nuestro sistema de
                Control de Stock. Si no encuentras lo que buscas, no dudes en ponerte en contacto con nuestro equipo de
                soporte, disponible para resolver cualquier inquietud que tengas. Estamos comprometidos en ofrecerte una
                experiencia sin complicaciones, asegurando que siempre puedas sacar el máximo provecho de nuestra
                plataforma.
            </p>

            <div class="row mt-5">
                <div class="col-md-4 mb-4">
                    <div class="card help-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Guías de Uso</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="help-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <h5>Manuales y Tutoriales</h5>
                            <p>Accede a nuestros manuales detallados y tutoriales paso a paso para aprender a utilizar
                                todas las funciones de la plataforma.</p>
                            <a href="faq.php" class="btn btn-primary">Ver Guías</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card help-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Soporte Técnico</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="help-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h5>Asistencia Personalizada</h5>
                            <p>Nuestro equipo de soporte técnico está disponible para ayudarte con cualquier problema o
                                duda que puedas tener.</p>
                            <a href="#contacto" class="btn btn-primary">Contactar Soporte</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card help-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Preguntas Frecuentes</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="help-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <h5>Respuestas Rápidas</h5>
                            <p>Consulta nuestra sección de preguntas frecuentes para encontrar respuestas a las dudas
                                más comunes sobre la plataforma.</p>
                            <a href="faq.php" class="btn btn-primary">Ver FAQs</a>
                        </div>
                    </div>
                </div>
            </div>



            <h2 class="mt-5" id="contacto">Contacto de Soporte</h2>
            <div class="contact-info">
                <p>Si necesitas ayuda adicional, no dudes en contactarnos a través de cualquiera de estos canales:</p>
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-envelope me-2"></i>Correo Electrónico</h5>
                        <p>truquemdaniels.cla@gmail.com</p>
                        <p class="text-muted small">Tiempo de respuesta: 24-48 horas hábiles</p>

                        <h5 class="mt-4"><i class="fas fa-phone me-2"></i>Teléfono</h5>
                        <p>(+57) 3246044420</p>
                        <p class="text-muted small">Lunes a Viernes, 8:00 AM - 6:00 PM</p>
                    </div>

                    <div class="col-md-6">
                        <h5><i class="fas fa-comment-dots me-2"></i>Sistema de Contactos</h5>
                        <p>Disponible desde tu panel de usuario</p>
                        <p class="text-muted small">Lunes a Viernes, 9:00 AM - 6:00 PM</p>

                        <h5 class="mt-4"><i class="fas fa-ticket-alt me-2"></i>Sistema de Tickets</h5>
                        <p>Para seguimiento detallado de incidencias</p>
                        <p class="text-muted small">Accede desde la sección "Soporte" en tu panel</p>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h5>Horario de Atención</h5>
                <p>Nuestro equipo de soporte está disponible de Lunes a Viernes, de 8:00 AM a 6:00 PM (hora local). Para
                    asistencia fuera de este horario, por favor envía un correo electrónico y te responderemos en el
                    siguiente día hábil.</p>
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