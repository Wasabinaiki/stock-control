<?php
// privacidad.php
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
    <title>Políticas de Seguridad - Control de Stock</title>
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
            <a class="navbar-brand" href="#"><i class="fas fa-shield-alt me-2"></i>Políticas de Seguridad</a>
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
            <h1 class="mb-4">Políticas de Stock Control</h1>

            <h2>1. Política de Privacidad</h2>
            <h3>Recopilación de datos:</h3>
            <ul>
                <li>Recopilamos información personal como nombre, correo electrónico, teléfono y datos relacionados con los dispositivos registrados.</li>
                <li>También recolectamos datos técnicos, como direcciones IP y estadísticas de uso, para mejorar la experiencia del usuario.</li>
            </ul>
            <h3>Uso de los datos:</h3>
            <ul>
                <li>Los datos personales serán utilizados exclusivamente para la gestión de dispositivos, programación de mantenimientos y facturación.</li>
                <li>No compartimos tu información con terceros, excepto cuando sea necesario para procesar pagos o cumplir con la ley.</li>
            </ul>
            <h3>Almacenamiento de datos:</h3>
            <ul>
                <li>Los datos son almacenados en servidores seguros. Implementamos medidas para proteger la información contra accesos no autorizados.</li>
            </ul>
            <h3>Cookies:</h3>
            <ul>
                <li>Utilizamos cookies para personalizar la experiencia del usuario y analizar el tráfico del sitio.</li>
                <li>Puedes deshabilitar las cookies desde la configuración de tu navegador.</li>
            </ul>

            <h2>2. Política de Devoluciones y Reembolsos</h2>
            <h3>Servicios reembolsables:</h3>
            <ul>
                <li>Los pagos por mantenimientos programados pueden ser reembolsados únicamente si el servicio no ha sido prestado.</li>
            </ul>
            <h3>Plazos:</h3>
            <ul>
                <li>Las solicitudes de reembolso deben realizarse dentro de los 30 días posteriores al pago.</li>
                <li>Después de este periodo, no se realizarán devoluciones.</li>
            </ul>
            <h3>Proceso de reembolso:</h3>
            <ul>
                <li>Para solicitar un reembolso, envía un correo a truquemdaniels@gmail.com con el número de la factura y el motivo de la solicitud.</li>
                <li>Los reembolsos se procesarán en un plazo de 5 días hábiles y se devolverán al método de pago original.</li>
            </ul>
            <h3>Excepciones:</h3>
            <ul>
                <li>No se reembolsarán pagos por servicios ya prestados o cancelaciones tardías.</li>
            </ul>

            <h2>3. Política de Seguridad</h2>
            <h3>Protección de datos:</h3>
            <ul>
                <li>Utilizamos cifrado SSL para proteger la transmisión de datos sensibles, como información de pago.</li>
                <li>Accedemos únicamente a la información necesaria para ofrecer nuestros servicios.</li>
            </ul>
            <h3>Responsabilidad del usuario:</h3>
            <ul>
                <li>Los usuarios son responsables de mantener la confidencialidad de su cuenta.</li>
                <li>Recomendamos usar contraseñas seguras y no compartirlas con terceros.</li>
            </ul>
            <h3>Notificación de incidentes:</h3>
            <ul>
                <li>En caso de una brecha de seguridad que comprometa tus datos, serás notificado de inmediato junto con las acciones correctivas implementadas.</li>
            </ul>

            <h2>4. Política de Uso Aceptable</h2>
            <h3>Propósito del servicio:</h3>
            <ul>
                <li>La plataforma debe ser utilizada exclusivamente para la gestión de dispositivos personales o empresariales.</li>
                <li>Queda prohibido registrar información falsa, inexacta o dispositivos que no sean de tu propiedad.</li>
            </ul>
            <h3>Prohibiciones:</h3>
            <ul>
                <li>No se permite usar la plataforma para actividades fraudulentas, acceder sin autorización a otras cuentas o alterar el sistema.</li>
                <li>El incumplimiento de esta política puede resultar en la suspensión o eliminación de tu cuenta.</li>
            </ul>
            <h3>Cumplimiento legal:</h3>
            <ul>
                <li>Los usuarios deben respetar las leyes locales al utilizar el servicio.</li>
            </ul>

            <h2>5. Política de Mantenimientos</h2>
            <h3>Programación:</h3>
            <ul>
                <li>Los usuarios deben completar correctamente los datos del dispositivo para programar un mantenimiento.</li>
                <li>La programación está sujeta a confirmación una vez recibido el pago correspondiente.</li>
            </ul>
            <h3>Cancelaciones:</h3>
            <ul>
                <li>Las cancelaciones deben realizarse al menos 24 horas antes de la fecha programada.</li>
                <li>Las cancelaciones fuera de plazo pueden generar cargos adicionales.</li>
            </ul>
            <h3>Reagendación:</h3>
            <ul>
                <li>Si deseas cambiar la fecha de tu mantenimiento, puedes hacerlo sin costo si notificas con al menos 3 horas de antelación.</li>
            </ul>

            <h2>6. Política de Pagos</h2>
            <h3>Métodos aceptados:</h3>
            <ul>
                <li>Aceptamos pagos mediante tarjeta de crédito, débito y plataformas electrónicas (PayPal, Stripe).</li>
            </ul>
            <h3>Facturación:</h3>
            <ul>
                <li>Cada pago realizado generará automáticamente una factura, que estará disponible en tu cuenta.</li>
                <li>La factura incluirá el detalle del servicio contratado.</li>
            </ul>
            <h3>Impuestos:</h3>
            <ul>
                <li>Los precios mostrados incluyen impuestos aplicables (si los hay).</li>
            </ul>

            <h2>7. Política de Modificaciones</h2>
            <h3>Cambios en las políticas:</h3>
            <ul>
                <li>Nos reservamos el derecho de modificar estas políticas en cualquier momento.</li>
                <li>Las actualizaciones serán notificadas en la plataforma con 30 días de antelación antes de que entren en vigor.</li>
            </ul>
            <h3>Aceptación de cambios:</h3>
            <ul>
                <li>Al continuar utilizando el servicio después de los cambios, aceptas las políticas actualizadas.</li>
            </ul>

            <h2>8. Política de Contacto</h2>
            <h3>Soporte al usuario:</h3>
            <ul>
                <li>Para dudas, quejas o sugerencias, puedes contactarnos mediante:</li>
                <li>Correo electrónico: truquemdaniels@gmail.com</li>
                <li>Teléfono: 3246044420</li>
                <li>Horarios de atención: Lunes – Viernes 08:00 AM – 05:00 PM</li>
            </ul>
            <h3>Resolución de conflictos:</h3>
            <ul>
                <li>Hacemos nuestro mejor esfuerzo para resolver cualquier problema dentro de los 15 días hábiles desde el momento en que se recibe tu solicitud.</li>
            </ul>
        </div>
        
        <div class="mt-4 mb-4">
            <a href="dashboard.php" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Volver al Dashboard</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>