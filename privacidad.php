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
    <title>Política de Privacidad - Control de Stock</title>
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
            padding: 30px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        h1, h2, h3 {
            color: #764ba2;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        .section:last-child {
            border-bottom: none;
        }
        .last-updated {
            font-style: italic;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .privacy-item {
            margin-bottom: 20px;
        }
        .privacy-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }
        .table {
            margin-top: 15px;
            margin-bottom: 25px;
        }
        .table th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-shield-alt me-2"></i>Política de Privacidad</a>
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
            <h1 class="mb-3">Política de Privacidad</h1>
            <p class="last-updated">Última actualización: 3 de abril de 2025</p>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Esta Política de Privacidad describe cómo Stock Control S.A.S. recopila, utiliza, almacena y protege su información personal. Por favor, léala detenidamente para entender nuestras prácticas con respecto a sus datos personales.
            </div>
            
            <div class="section">
                <h2>1. Introducción</h2>
                <p>En Stock Control S.A.S. ("nosotros", "nuestro", "la Compañía"), respetamos su privacidad y nos comprometemos a proteger sus datos personales. Esta Política de Privacidad explica cómo recopilamos, utilizamos, divulgamos, transferimos y almacenamos su información cuando utiliza nuestra plataforma de Control de Stock ("la Plataforma").</p>
                
                <p>Nos adherimos a los principios de protección de datos, incluyendo transparencia, limitación de propósito, minimización de datos, precisión, limitación de almacenamiento, integridad y confidencialidad. Esta política se aplica a todos los usuarios de nuestra Plataforma, ya sean clientes, administradores, o visitantes.</p>
            </div>
            
            <div class="section">
                <h2>2. Información que Recopilamos</h2>
                <div class="privacy-item">
                    <p class="privacy-title">2.1. Información que usted nos proporciona</p>
                    <p>Recopilamos la información que usted nos proporciona directamente cuando:</p>
                    <ul>
                        <li>Crea o modifica su cuenta (nombre, dirección de correo electrónico, contraseña, etc.)</li>
                        <li>Completa formularios en nuestra Plataforma (información de contacto, detalles de la empresa)</li>
                        <li>Registra dispositivos o inventario (detalles del producto, números de serie, ubicaciones)</li>
                        <li>Programa mantenimientos o envíos (fechas, descripciones, destinatarios)</li>
                        <li>Se comunica con nuestro equipo de soporte (consultas, problemas técnicos)</li>
                        <li>Responde a encuestas o proporciona comentarios</li>
                    </ul>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">2.2. Información recopilada automáticamente</p>
                    <p>Cuando utiliza nuestra Plataforma, recopilamos automáticamente cierta información, incluyendo:</p>
                    <ul>
                        <li>Información técnica: dirección IP, tipo de dispositivo, tipo y versión del navegador, sistema operativo, resolución de pantalla</li>
                        <li>Información de uso: páginas visitadas, tiempo pasado en la Plataforma, patrones de navegación, interacciones con la interfaz</li>
                        <li>Información de registro: actividades realizadas, errores encontrados, tiempos de acceso</li>
                        <li>Información de ubicación: ubicación geográfica general basada en la dirección IP</li>
                    </ul>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">2.3. Cookies y tecnologías similares</p>
                    <p>Utilizamos cookies y tecnologías similares para recopilar información sobre su actividad, navegador y dispositivo. Las cookies son pequeños archivos de texto que se almacenan en su dispositivo cuando visita nuestra Plataforma. Utilizamos los siguientes tipos de cookies:</p>
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tipo de Cookie</th>
                                <th>Propósito</th>
                                <th>Duración</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Cookies esenciales</td>
                                <td>Necesarias para el funcionamiento de la Plataforma, autenticación y seguridad</td>
                                <td>Sesión / Persistentes</td>
                            </tr>
                            <tr>
                                <td>Cookies de preferencias</td>
                                <td>Recuerdan sus preferencias y configuraciones</td>
                                <td>1 año</td>
                            </tr>
                            <tr>
                                <td>Cookies analíticas</td>
                                <td>Nos ayudan a entender cómo interactúan los usuarios con la Plataforma</td>
                                <td>2 años</td>
                            </tr>
                            <tr>
                                <td>Cookies de funcionalidad</td>
                                <td>Permiten características avanzadas y personalizaciones</td>
                                <td>6 meses</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <p>Puede gestionar sus preferencias de cookies a través de la configuración de su navegador. Sin embargo, tenga en cuenta que deshabilitar ciertas cookies puede afectar la funcionalidad de nuestra Plataforma.</p>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">2.4. Información de terceros</p>
                    <p>En algunos casos, podemos recibir información sobre usted de terceros, como:</p>
                    <ul>
                        <li>Socios comerciales con los que ofrecemos servicios co-branded o realizamos actividades de marketing conjuntas</li>
                        <li>Proveedores de servicios que nos ayudan a verificar cierta información (por ejemplo, para verificar direcciones)</li>
                        <li>Plataformas de redes sociales, si elige conectar su cuenta con estos servicios</li>
                    </ul>
                </div>
            </div>
            
            <div class="section">
                <h2>3. Cómo Utilizamos Su Información</h2>
                <div class="privacy-item">
                    <p class="privacy-title">3.1. Propósitos principales</p>
                    <p>Utilizamos la información que recopilamos para los siguientes propósitos:</p>
                    <ul>
                        <li>Proporcionar, mantener y mejorar nuestra Plataforma</li>
                        <li>Procesar y completar transacciones</li>
                        <li>Enviar información técnica, actualizaciones, alertas de seguridad y mensajes de soporte</li>
                        <li>Responder a sus comentarios, preguntas y solicitudes</li>
                        <li>Monitorear y analizar tendencias, uso y actividades relacionadas con nuestra Plataforma</li>
                        <li>Detectar, investigar y prevenir actividades fraudulentas y accesos no autorizados</li>
                        <li>Personalizar y mejorar su experiencia</li>
                    </ul>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">3.2. Base legal para el procesamiento</p>
                    <p>Procesamos su información personal basándonos en las siguientes bases legales:</p>
                    <ul>
                        <li><strong>Ejecución de un contrato:</strong> Cuando el procesamiento es necesario para cumplir con nuestras obligaciones contractuales con usted.</li>
                        <li><strong>Consentimiento:</strong> Cuando ha dado su consentimiento explícito para el procesamiento con un propósito específico.</li>
                        <li><strong>Intereses legítimos:</strong> Cuando el procesamiento es necesario para nuestros intereses legítimos o los de un tercero, siempre que estos intereses no sean anulados por sus derechos y libertades.</li>
                        <li><strong>Obligación legal:</strong> Cuando el procesamiento es necesario para cumplir con una obligación legal a la que estamos sujetos.</li>
                    </ul>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">3.3. Comunicaciones de marketing</p>
                    <p>Podemos utilizar su información para enviarle comunicaciones de marketing sobre nuestros productos y servicios, actualizaciones y promociones. Puede optar por no recibir estas comunicaciones en cualquier momento siguiendo las instrucciones de cancelación de suscripción incluidas en nuestros correos electrónicos o contactándonos directamente.</p>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">3.4. Toma de decisiones automatizada</p>
                    <p>No utilizamos procesos automatizados de toma de decisiones, incluida la elaboración de perfiles, que produzcan efectos legales o que le afecten significativamente de manera similar.</p>
                </div>
            </div>
            
            <div class="section">
                <h2>4. Compartir y Divulgar Información</h2>
                <div class="privacy-item">
                    <p class="privacy-title">4.1. Compartir con terceros</p>
                    <p>Podemos compartir su información personal con los siguientes tipos de terceros:</p>
                    <ul>
                        <li><strong>Proveedores de servicios:</strong> Empresas que realizan servicios en nuestro nombre, como procesamiento de pagos, análisis de datos, entrega de correo electrónico, servicios de alojamiento, atención al cliente y marketing.</li>
                        <li><strong>Socios comerciales:</strong> Terceros con los que podemos ofrecer productos o servicios conjuntamente.</li>
                        <li><strong>Afiliados:</strong> Empresas que están bajo control común con nosotros, que pueden ayudarnos a proporcionar y mejorar nuestros servicios.</li>
                        <li><strong>Asesores profesionales:</strong> Contadores, abogados, auditores y aseguradores que proporcionan servicios de consultoría, legales, de seguros y contables.</li>
                    </ul>
                    <p>Exigimos a todos los terceros que respeten la seguridad de sus datos personales y los traten de acuerdo con la ley. No permitimos que nuestros proveedores de servicios utilicen sus datos personales para sus propios fines y solo les permitimos procesar sus datos personales para fines específicos y de acuerdo con nuestras instrucciones.</p>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">4.2. Divulgación legal</p>
                    <p>Podemos divulgar su información personal si creemos de buena fe que dicha divulgación es necesaria para:</p>
                    <ul>
                        <li>Cumplir con una obligación legal, proceso judicial o solicitud gubernamental</li>
                        <li>Hacer cumplir nuestros términos y condiciones u otros acuerdos</li>
                        <li>Proteger la seguridad, derechos o propiedad de nuestra empresa, nuestros usuarios o el público</li>
                        <li>Detectar, prevenir o abordar fraude, problemas de seguridad o técnicos</li>
                    </ul>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">4.3. Transferencias de negocios</p>
                    <p>Si participamos en una fusión, adquisición, reorganización, venta de activos o quiebra, su información puede ser vendida o transferida como parte de esa transacción. Tomaremos medidas razonables para asegurar que sus datos personales sigan siendo protegidos de acuerdo con esta Política de Privacidad.</p>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">4.4. Transferencias internacionales de datos</p>
                    <p>Operamos a nivel nacional y podemos transferir, almacenar y procesar su información en países distintos a su país de residencia, donde tenemos instalaciones o donde contratamos proveedores de servicios. Nos aseguramos de que estas transferencias cumplan con las leyes de protección de datos aplicables y que se implementen medidas de seguridad adecuadas.</p>
                </div>
            </div>
            
            <div class="section">
                <h2>5. Retención de Datos</h2>
                <div class="privacy-item">
                    <p class="privacy-title">5.1. Período de retención</p>
                    <p>Conservamos su información personal solo durante el tiempo necesario para cumplir con los fines para los que la recopilamos, incluido el cumplimiento de requisitos legales, contables o de informes. Para determinar el período de retención apropiado, consideramos:</p>
                    <ul>
                        <li>La cantidad, naturaleza y sensibilidad de los datos personales</li>
                        <li>El riesgo potencial de daño por uso o divulgación no autorizados</li>
                        <li>Los fines para los que procesamos sus datos y si podemos lograr esos fines por otros medios</li>
                        <li>Los requisitos legales aplicables</li>
                    </ul>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">5.2. Datos de cuenta</p>
                    <p>Conservamos la información de su cuenta mientras su cuenta esté activa. Si solicita la eliminación de su cuenta, eliminaremos o anonimizaremos su información personal dentro de los 30 días posteriores a su solicitud, a menos que estemos legalmente obligados o permitidos a mantener cierta información.</p>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">5.3. Datos de uso</p>
                    <p>Los datos de uso y análisis se conservan generalmente por un período más corto, normalmente no más de 24 meses, después de lo cual se agregan o anonimizan.</p>
                </div>
            </div>
            
            <div class="section">
                <h2>6. Seguridad de los Datos</h2>
                <div class="privacy-item">
                    <p class="privacy-title">6.1. Medidas de seguridad</p>
                    <p>Implementamos medidas de seguridad técnicas, administrativas y físicas diseñadas para proteger sus datos personales contra pérdida accidental, uso no autorizado, acceso, divulgación, alteración o destrucción. Estas medidas incluyen:</p>
                    <ul>
                        <li>Encriptación de datos sensibles en tránsito y en reposo</li>
                        <li>Firewalls y sistemas de detección de intrusiones</li>
                        <li>Controles de acceso basados en roles</li>
                        <li>Monitoreo y registro de actividades</li>
                        <li>Copias de seguridad regulares</li>
                        <li>Evaluaciones de vulnerabilidad y pruebas de penetración</li>
                        <li>Capacitación en seguridad para empleados</li>
                    </ul>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">6.2. Notificación de violación de datos</p>
                    <p>En caso de una violación de datos que afecte su información personal, tomaremos medidas para contener y mitigar el incidente, y le notificaremos de acuerdo con las leyes aplicables.</p>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">6.3. Limitaciones</p>
                    <p>Aunque nos esforzamos por proteger su información personal, ningún método de transmisión por Internet o método de almacenamiento electrónico es 100% seguro. No podemos garantizar la seguridad absoluta de sus datos. Es importante que también tome precauciones para proteger su información, como mantener la confidencialidad de sus credenciales de inicio de sesión.</p>
                </div>
            </div>
            
            <div class="section">
                <h2>7. Sus Derechos y Opciones</h2>
                <div class="privacy-item">
                    <p class="privacy-title">7.1. Derechos de protección de datos</p>
                    <p>Dependiendo de su ubicación, puede tener los siguientes derechos con respecto a sus datos personales:</p>
                    <ul>
                        <li><strong>Derecho de acceso:</strong> Solicitar una copia de la información personal que tenemos sobre usted.</li>
                        <li><strong>Derecho de rectificación:</strong> Solicitar la corrección de información inexacta o incompleta.</li>
                        <li><strong>Derecho de supresión:</strong> Solicitar la eliminación de su información personal en ciertas circunstancias.</li>
                        <li><strong>Derecho a restringir el procesamiento:</strong> Solicitar la limitación del procesamiento de su información personal en ciertas circunstancias.</li>
                        <li><strong>Derecho a la portabilidad de datos:</strong> Recibir sus datos personales en un formato estructurado, de uso común y legible por máquina.</li>
                        <li><strong>Derecho de oposición:</strong> Oponerse al procesamiento de sus datos personales en ciertas circunstancias.</li>
                        <li><strong>Derecho a retirar el consentimiento:</strong> Retirar el consentimiento en cualquier momento cuando el procesamiento se base en su consentimiento.</li>
                    </ul>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">7.2. Cómo ejercer sus derechos</p>
                    <p>Para ejercer cualquiera de estos derechos, por favor contáctenos utilizando la información proporcionada en la sección "Contacto" de esta política. Responderemos a su solicitud dentro de los plazos establecidos por las leyes aplicables (generalmente dentro de los 30 días).</p>
                    <p>Para proteger su privacidad y seguridad, podemos tomar medidas razonables para verificar su identidad antes de conceder acceso o hacer cambios en su información.</p>
                </div>
                
                <div class="privacy-item">
                    <p class="privacy-title">7.3. Derecho a presentar una queja</p>
                    <p>Si no está satisfecho con nuestra respuesta o cree que estamos procesando sus datos personales de manera ilegal, tiene derecho a presentar una queja ante la autoridad de protección de datos correspondiente.</p>
                </div>
            </div>
            
            <div class="section">
                <h2>8. Privacidad de los Niños</h2>
                <p>Nuestra Plataforma no está dirigida a personas menores de 18 años, y no recopilamos a sabiendas información personal de niños menores de 18 años. Si descubrimos que hemos recopilado información personal de un niño menor de 18 años sin verificación del consentimiento parental, tomaremos medidas para eliminar esa información lo antes posible.</p>
            </div>
            
            <div class="section">
                <h2>9. Enlaces a Sitios de Terceros</h2>
                <p>Nuestra Plataforma puede contener enlaces a sitios web, productos o servicios de terceros que no son de nuestra propiedad ni están controlados por nosotros. Esta Política de Privacidad no se aplica a esos sitios web de terceros. Le recomendamos que revise las políticas de privacidad de cualquier sitio que visite.</p>
            </div>
            
            <div class="section">
                <h2>10. Cambios a Esta Política de Privacidad</h2>
                <p>Podemos actualizar esta Política de Privacidad de vez en cuando para reflejar cambios en nuestras prácticas o por otras razones operativas, legales o regulatorias. Si realizamos cambios materiales, le notificaremos a través de un aviso prominente en nuestra Plataforma o, en algunos casos, enviándole una notificación directa. Le animamos a revisar periódicamente esta página para obtener la información más reciente sobre nuestras prácticas de privacidad.</p>
                <p>La fecha de la última actualización se indica al principio de esta Política de Privacidad. Su uso continuado de la Plataforma después de que publiquemos cambios a esta Política de Privacidad constituirá su aceptación de dichos cambios.</p>
            </div>
            
            <div class="section">
                <h2>11. Contacto</h2>
                <p>Si tiene preguntas, comentarios o inquietudes sobre esta Política de Privacidad o nuestras prácticas de privacidad, por favor contáctenos a:</p>
                <p><strong>Stock Control S.A.S.</strong><br>
                Atención: Oficial de Protección de Datos<br>
                Correo electrónico: truquemdaniels.cla@gmail.com<br>
                Teléfono: (+57) 3246044420</p>
            </div>
            
            <div class="mt-4">
                <p>Al utilizar nuestra Plataforma, usted reconoce que ha leído y entendido esta Política de Privacidad.</p>
            </div>
        </div>
        
        <div class="mt-4 mb-4">
            <a href="dashboard.php" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Volver al Dashboard</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>