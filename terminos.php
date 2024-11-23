<?php
// terminos.php
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
    <title>Términos y Condiciones - Control de Stock</title>
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
            <a class="navbar-brand" href="#"><i class="fas fa-file-contract me-2"></i>Términos y Condiciones</a>
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
            <h1 class="mb-4">Términos y Condiciones de Uso</h1>
            <p>Bienvenido a Stock Control. Al acceder y utilizar nuestros servicios, aceptas cumplir con los términos y condiciones descritos a continuación. Si no estás de acuerdo con ellos, no deberías usar nuestra plataforma.</p>
            
            <h2>1. Introducción</h2>
            <p>Stock Control es una plataforma dedicada a la gestión de dispositivos electrónicos como PCs, celulares y tablets. Ofrecemos herramientas para el control organizado de tu inventario, programación de mantenimientos, generación de facturas y otros servicios relacionados.</p>
            <p>Estos términos regulan el uso de nuestros servicios, por lo que te recomendamos leerlos atentamente.</p>
            
            <h2>2. Definiciones</h2>
            <ul>
                <li><strong>Usuario:</strong> Persona que accede y utiliza los servicios de la plataforma. Puede ser un usuario regular o un administrador, según los permisos otorgados.</li>
                <li><strong>Dispositivo:</strong> Equipo electrónico registrado en la plataforma para ser gestionado.</li>
                <li><strong>Servicio:</strong> Funciones ofrecidas por la plataforma, como gestión de dispositivos, programación de mantenimientos y pagos.</li>
            </ul>
            
            <h2>3. Registro de cuenta</h2>
            <ol>
                <li><strong>Requisitos:</strong>
                    <ul>
                        <li>Para usar nuestra plataforma, debes crear una cuenta con información real y actualizada.</li>
                    </ul>
                </li>
                <li><strong>Responsabilidades del usuario:</strong>
                    <ul>
                        <li>Mantener la confidencialidad de tu nombre de usuario y contraseña.</li>
                        <li>Notificar de inmediato cualquier uso no autorizado de tu cuenta.</li>
                    </ul>
                </li>
                <li><strong>Roles:</strong>
                    <ul>
                        <li>La plataforma incluye roles de usuario (gestión básica de dispositivos) y administrador (gestión avanzada, como ver el inventario completo y aprobar mantenimientos).</li>
                    </ul>
                </li>
            </ol>
            
            <h2>4. Uso del servicio</h2>
            <ol>
                <li><strong>Propósito:</strong>
                    <ul>
                        <li>Este servicio es para uso personal o empresarial, según el plan elegido.</li>
                        <li>Los usuarios se comprometen a utilizar la plataforma únicamente para fines legales.</li>
                    </ul>
                </li>
                <li><strong>Restricciones:</strong>
                    <p>Está prohibido:</p>
                    <ul>
                        <li>Manipular el sistema para obtener acceso no autorizado.</li>
                        <li>Registrar dispositivos que no sean de tu propiedad.</li>
                        <li>Usar la plataforma para actividades ilícitas o fraudulentas.</li>
                    </ul>
                </li>
            </ol>
            
            <h2>5. Programación de mantenimientos</h2>
            <ol>
                <li><strong>Condiciones de programación:</strong>
                    <ul>
                        <li>Puedes programar mantenimientos desde la plataforma ingresando los datos del dispositivo.</li>
                        <li>La confirmación del mantenimiento está sujeta al pago de la tarifa correspondiente.</li>
                    </ul>
                </li>
                <li><strong>Política de cancelación:</strong>
                    <ul>
                        <li>Los mantenimientos pueden cancelarse sin costo si se realiza antes de 3 horas de la fecha programada.</li>
                        <li>Cancelaciones posteriores podrían generar cargos.</li>
                    </ul>
                </li>
            </ol>
            
            <h2>6. Pagos</h2>
            <ol>
                <li><strong>Métodos aceptados:</strong>
                    <ul>
                        <li>Pagos en línea mediante tarjeta de crédito, débito o plataformas electrónicas (como PayPal o Stripe).</li>
                    </ul>
                </li>
                <li><strong>Política de reembolsos:</strong>
                    <ul>
                        <li>Solo se otorgarán reembolsos si el servicio no ha sido prestado y la solicitud se realiza dentro de 15 días.</li>
                        <li>Los costos de transacción no son reembolsables.</li>
                    </ul>
                </li>
                <li><strong>Facturación:</strong>
                    <ul>
                        <li>Una vez realizado el pago, se generará automáticamente una factura que estará disponible en tu cuenta.</li>
                    </ul>
                </li>
            </ol>
            
            <h2>7. Propiedad Intelectual</h2>
            <ol>
                <li><strong>Derechos sobre el contenido:</strong>
                    <p>Todo el contenido, diseño y código de la plataforma son propiedad de Stock Control. Está prohibida su reproducción, distribución o modificación sin permiso.</p>
                </li>
                <li><strong>Datos del usuario:</strong>
                    <ul>
                        <li>La información de los dispositivos registrada por los usuarios seguirá siendo de su propiedad.</li>
                        <li>Garantizamos que esta información será tratada con confidencialidad y según nuestra Política de Privacidad.</li>
                    </ul>
                </li>
            </ol>
            
            <h2>8. Limitación de responsabilidad</h2>
            <ol>
                <li><strong>Disponibilidad del servicio:</strong>
                    <p>Stock Control no garantiza que la plataforma esté disponible las 24 horas del día, aunque haremos nuestro mejor esfuerzo por mantener la continuidad del servicio.</p>
                </li>
                <li><strong>Errores o fallos:</strong>
                    <p>No nos hacemos responsables de:</p>
                    <ul>
                        <li>Daños ocasionados por el uso incorrecto del servicio.</li>
                        <li>Pérdida de datos debido a fallos técnicos externos a nuestra plataforma.</li>
                    </ul>
                </li>
                <li><strong>Decisiones comerciales:</strong>
                    <p>Las decisiones tomadas por los usuarios basadas en los datos del sistema son de su exclusiva responsabilidad.</p>
                </li>
            </ol>
            
            <h2>9. Modificaciones de los términos</h2>
            <p>Nos reservamos el derecho de actualizar estos términos en cualquier momento. Los cambios serán efectivos una vez publicados en la plataforma. Te recomendamos revisar esta sección periódicamente.</p>
            
            <h2>10. Ley aplicable</h2>
            <p>Estos términos se rigen por las leyes de Colombia. Cualquier disputa relacionada con el uso del servicio será resuelta ante los tribunales competentes de Santiago de Cali.</p>
            
            <h2>11. Contacto</h2>
            <p>Si tienes preguntas sobre estos términos, puedes contactarnos a través de:</p>
            <ul>
                <li>Correo electrónico: truquemdaniels.cla@gmail.com</li>
                <li>Teléfono: 3246044420</li>
            </ul>
        </div>
        
        <div class="mt-4 mb-4">
            <a href="dashboard.php" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Volver al Dashboard</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>