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

        .navbar-brand,
        .nav-link {
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

        h1,
        h2,
        h3 {
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

        .term-item {
            margin-bottom: 20px;
        }

        .term-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
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
            <h1 class="mb-3">Términos y Condiciones de Uso</h1>
            <p class="last-updated">Última actualización: 3 de abril de 2025</p>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Por favor, lea detenidamente estos términos y condiciones antes
                de utilizar nuestra plataforma de Control de Stock. Al acceder o utilizar nuestro servicio, usted acepta
                estar sujeto a estos términos.
            </div>

            <div class="section">
                <h2>1. Introducción</h2>
                <p>Estos Términos y Condiciones ("Términos") rigen el uso de la plataforma de Control de Stock ("la
                    Plataforma"), operada por Stock Control S.A.S. ("nosotros", "nuestro" o "la Compañía"). Al acceder o
                    utilizar la Plataforma, usted ("Usuario", "usted" o "su") acepta estar legalmente vinculado por
                    estos Términos. Si no está de acuerdo con alguna parte de estos términos, no podrá acceder o
                    utilizar nuestros servicios.</p>

                <p>Estos Términos constituyen un acuerdo legal vinculante entre usted y Stock Control S.A.S. con
                    respecto a su uso de la Plataforma y todos los servicios relacionados. Le recomendamos que guarde
                    una copia de estos Términos para futuras referencias.</p>
            </div>

            <div class="section">
                <h2>2. Definiciones</h2>
                <div class="term-item">
                    <p class="term-title">2.1. "Plataforma"</p>
                    <p>Se refiere al sistema de Control de Stock, incluyendo el sitio web, aplicaciones móviles, APIs y
                        cualquier otro software o servicio proporcionado por Stock Control S.A.S.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">2.2. "Usuario"</p>
                    <p>Cualquier persona o entidad que acceda o utilice la Plataforma, ya sea como usuario registrado o
                        visitante.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">2.3. "Cuenta"</p>
                    <p>El registro personal de un Usuario en la Plataforma, que incluye información de identificación y
                        acceso.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">2.4. "Contenido"</p>
                    <p>Toda la información, datos, texto, software, gráficos, mensajes u otros materiales que se
                        muestran o están disponibles a través de la Plataforma.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">2.5. "Datos del Usuario"</p>
                    <p>Información proporcionada por el Usuario durante el uso de la Plataforma, incluyendo pero no
                        limitado a información de inventario, dispositivos, mantenimientos y envíos.</p>
                </div>
            </div>

            <div class="section">
                <h2>3. Registro y Cuentas de Usuario</h2>
                <div class="term-item">
                    <p class="term-title">3.1. Requisitos de Registro</p>
                    <p>Para utilizar ciertas funciones de la Plataforma, debe registrarse y crear una cuenta. Al
                        registrarse, usted acepta proporcionar información precisa, actualizada y completa. La Compañía
                        se reserva el derecho de suspender o terminar su cuenta si alguna información proporcionada
                        durante el proceso de registro o posteriormente resulta ser inexacta, desactualizada o
                        incompleta.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">3.2. Seguridad de la Cuenta</p>
                    <p>Usted es responsable de mantener la confidencialidad de su contraseña y de todas las actividades
                        que ocurran bajo su cuenta. Debe notificar inmediatamente a la Compañía sobre cualquier uso no
                        autorizado de su cuenta o cualquier otra violación de seguridad. La Compañía no será responsable
                        por ninguna pérdida o daño que surja de su incumplimiento en mantener la seguridad de su cuenta.
                    </p>
                </div>

                <div class="term-item">
                    <p class="term-title">3.3. Restricciones de Cuenta</p>
                    <p>Cada Usuario puede tener solo una cuenta personal. Las cuentas no son transferibles y no pueden
                        ser vendidas, intercambiadas o cedidas a terceros sin el consentimiento previo por escrito de la
                        Compañía. La Compañía se reserva el derecho de rechazar el servicio, cerrar cuentas, eliminar o
                        editar contenido, o cancelar pedidos a su entera discreción.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">3.4. Niveles de Acceso</p>
                    <p>La Plataforma puede ofrecer diferentes niveles de acceso y funcionalidades según el tipo de
                        cuenta (por ejemplo, administrador, usuario regular). Usted solo debe acceder a las áreas de la
                        Plataforma para las que tiene autorización explícita.</p>
                </div>
            </div>

            <div class="section">
                <h2>4. Uso de la Plataforma</h2>
                <div class="term-item">
                    <p class="term-title">4.1. Licencia de Uso</p>
                    <p>Sujeto a estos Términos, la Compañía le otorga una licencia limitada, no exclusiva, no
                        transferible y revocable para acceder y utilizar la Plataforma para sus propósitos comerciales
                        internos.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">4.2. Restricciones de Uso</p>
                    <p>Al utilizar la Plataforma, usted acepta NO:</p>
                    <ul>
                        <li>Utilizar la Plataforma de manera que viole cualquier ley o regulación aplicable.</li>
                        <li>Intentar acceder a áreas restringidas de la Plataforma o a los sistemas o redes conectados a
                            la Plataforma sin autorización.</li>
                        <li>Interferir o interrumpir la integridad o el rendimiento de la Plataforma o los datos
                            contenidos en ella.</li>
                        <li>Intentar probar, escanear o evaluar la vulnerabilidad de la Plataforma o eludir las medidas
                            de seguridad implementadas.</li>
                        <li>Recopilar o almacenar datos personales de otros usuarios de la Plataforma sin su
                            consentimiento.</li>
                        <li>Utilizar cualquier robot, spider, scraper u otro medio automatizado para acceder a la
                            Plataforma con cualquier propósito.</li>
                        <li>Copiar, modificar, crear trabajos derivados, descompilar, desensamblar o intentar descubrir
                            cualquier código fuente de la Plataforma.</li>
                        <li>Eliminar, ocultar o alterar cualquier aviso de derechos de autor, marca registrada u otros
                            derechos de propiedad contenidos en la Plataforma.</li>
                    </ul>
                </div>

                <div class="term-item">
                    <p class="term-title">4.3. Disponibilidad del Servicio</p>
                    <p>La Compañía se esfuerza por mantener la Plataforma disponible y funcionando correctamente. Sin
                        embargo, no garantizamos que la Plataforma estará disponible de forma ininterrumpida o libre de
                        errores. Nos reservamos el derecho de suspender o restringir el acceso a la Plataforma para
                        realizar mantenimiento, actualizaciones o por cualquier otra razón a nuestra discreción.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">4.4. Modificaciones a la Plataforma</p>
                    <p>La Compañía se reserva el derecho de modificar, suspender o descontinuar cualquier aspecto de la
                        Plataforma en cualquier momento, incluyendo la disponibilidad de cualquier característica, base
                        de datos o contenido. También podemos imponer límites en ciertas características o restringir su
                        acceso a partes o a toda la Plataforma sin previo aviso ni responsabilidad.</p>
                </div>
            </div>

            <div class="section">
                <h2>5. Datos del Usuario y Privacidad</h2>
                <div class="term-item">
                    <p class="term-title">5.1. Propiedad de los Datos</p>
                    <p>Usted conserva todos los derechos sobre los Datos del Usuario que proporciona a la Plataforma. Al
                        enviar Datos del Usuario a la Plataforma, usted otorga a la Compañía una licencia mundial, no
                        exclusiva, libre de regalías para usar, copiar, almacenar, modificar y mostrar dichos datos
                        únicamente con el propósito de proporcionar y mejorar la Plataforma.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">5.2. Privacidad</p>
                    <p>La recopilación y el uso de información personal en relación con la Plataforma se rigen por
                        nuestra Política de Privacidad, que se incorpora por referencia a estos Términos. Al utilizar la
                        Plataforma, usted acepta nuestras prácticas de recopilación y uso de datos como se describe en
                        la Política de Privacidad.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">5.3. Seguridad de los Datos</p>
                    <p>La Compañía implementa medidas de seguridad diseñadas para proteger los Datos del Usuario contra
                        el acceso, la divulgación, la alteración o la destrucción no autorizados. Sin embargo, ningún
                        método de transmisión por Internet o método de almacenamiento electrónico es 100% seguro, y no
                        podemos garantizar la seguridad absoluta de los Datos del Usuario.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">5.4. Respaldo de Datos</p>
                    <p>Aunque la Compañía realiza copias de seguridad periódicas de los datos de la Plataforma, usted es
                        responsable de mantener copias de seguridad independientes de sus Datos del Usuario. La Compañía
                        no será responsable por la pérdida de datos en caso de fallo del sistema, error humano u otros
                        eventos.</p>
                </div>
            </div>

            <div class="section">
                <h2>6. Propiedad Intelectual</h2>
                <div class="term-item">
                    <p class="term-title">6.1. Derechos de la Compañía</p>
                    <p>La Plataforma y todo su contenido, características y funcionalidad (incluyendo pero no limitado a
                        todo el texto, información, software, gráficos, video, audio y diseño) son propiedad de la
                        Compañía, sus licenciantes u otros proveedores de dicho material y están protegidos por leyes de
                        derechos de autor, marcas registradas, patentes, secretos comerciales y otras leyes de propiedad
                        intelectual.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">6.2. Restricciones de Uso</p>
                    <p>Ninguna parte de la Plataforma puede ser copiada, reproducida, distribuida, republicada,
                        descargada, mostrada, publicada o transmitida en cualquier forma o por cualquier medio sin el
                        permiso previo por escrito de la Compañía, excepto según lo permitido por la funcionalidad de la
                        Plataforma o estos Términos.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">6.3. Marcas Comerciales</p>
                    <p>El nombre de la Compañía, el logotipo y todos los nombres, logotipos, nombres de productos y
                        servicios, diseños y eslóganes relacionados son marcas comerciales de la Compañía o sus
                        afiliados o licenciantes. No puede usar dichas marcas sin el permiso previo por escrito de la
                        Compañía.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">6.4. Comentarios</p>
                    <p>Si proporciona a la Compañía cualquier idea, sugerencia, documento u otra propuesta
                        ("Comentarios"), usted acepta que: (i) sus Comentarios no contienen información confidencial o
                        de propiedad de terceros; (ii) la Compañía no tiene ninguna obligación de confidencialidad con
                        respecto a los Comentarios; (iii) la Compañía puede tener algo similar a los Comentarios ya en
                        consideración o desarrollo; y (iv) usted otorga a la Compañía una licencia irrevocable, no
                        exclusiva, libre de regalías, perpetua, mundial para usar, modificar, publicar, distribuir y
                        sublicenciar los Comentarios.</p>
                </div>
            </div>

            <div class="section">
                <h2>7. Limitación de Responsabilidad</h2>
                <div class="term-item">
                    <p class="term-title">7.1. Exención de Garantías</p>
                    <p>LA PLATAFORMA SE PROPORCIONA "TAL CUAL" Y "SEGÚN DISPONIBILIDAD", SIN GARANTÍAS DE NINGÚN TIPO,
                        YA SEAN EXPRESAS O IMPLÍCITAS. LA COMPAÑÍA RENUNCIA EXPRESAMENTE A TODAS LAS GARANTÍAS DE
                        CUALQUIER TIPO, YA SEAN EXPRESAS O IMPLÍCITAS, INCLUYENDO PERO NO LIMITADO A LAS GARANTÍAS
                        IMPLÍCITAS DE COMERCIABILIDAD, IDONEIDAD PARA UN PROPÓSITO PARTICULAR, NO INFRACCIÓN Y CUALQUIER
                        OTRA GARANTÍA QUE PUEDA SURGIR DEL CURSO DE NEGOCIACIÓN O USO COMERCIAL.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">7.2. Limitación de Responsabilidad</p>
                    <p>EN LA MEDIDA MÁXIMA PERMITIDA POR LA LEY APLICABLE, EN NINGÚN CASO LA COMPAÑÍA, SUS AFILIADOS,
                        DIRECTORES, EMPLEADOS, AGENTES, PROVEEDORES O LICENCIANTES SERÁN RESPONSABLES POR DAÑOS
                        INDIRECTOS, PUNITIVOS, INCIDENTALES, ESPECIALES, CONSECUENTES O EJEMPLARES, INCLUYENDO SIN
                        LIMITACIÓN DAÑOS POR PÉRDIDA DE BENEFICIOS, FONDO DE COMERCIO, USO, DATOS U OTRAS PÉRDIDAS
                        INTANGIBLES, QUE SURJAN DE O ESTÉN RELACIONADOS CON EL USO O LA IMPOSIBILIDAD DE USAR LA
                        PLATAFORMA.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">7.3. Exclusión de Ciertos Daños</p>
                    <p>ALGUNAS JURISDICCIONES NO PERMITEN LA EXCLUSIÓN O LIMITACIÓN DE RESPONSABILIDAD POR DAÑOS
                        CONSECUENTES O INCIDENTALES, POR LO QUE LA LIMITACIÓN ANTERIOR PUEDE NO APLICARSE A USTED. EN
                        TALES CASOS, LA RESPONSABILIDAD DE LA COMPAÑÍA SE LIMITARÁ AL MÁXIMO PERMITIDO POR LA LEY.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">7.4. Base de Negociación</p>
                    <p>Las limitaciones de daños establecidas anteriormente son elementos fundamentales de la base del
                        acuerdo entre la Compañía y usted. La Plataforma no sería proporcionada sin tales limitaciones.
                    </p>
                </div>
            </div>

            <div class="section">
                <h2>8. Indemnización</h2>
                <p>Usted acepta defender, indemnizar y mantener indemne a la Compañía, sus afiliados, licenciantes y
                    proveedores de servicios, y sus respectivos funcionarios, directores, empleados, contratistas,
                    agentes, licenciantes, proveedores, sucesores y cesionarios de y contra cualquier reclamo,
                    responsabilidad, daño, juicio, premio, pérdida, costo, gasto o tarifa (incluyendo honorarios
                    razonables de abogados) que surjan de o estén relacionados con su violación de estos Términos o su
                    uso de la Plataforma, incluyendo, pero no limitado a, cualquier uso de los contenidos, servicios e
                    información de la Plataforma que no sea como se autoriza expresamente en estos Términos.</p>
            </div>

            <div class="section">
                <h2>9. Terminación</h2>
                <div class="term-item">
                    <p class="term-title">9.1. Terminación por Parte del Usuario</p>
                    <p>Usted puede dejar de usar la Plataforma en cualquier momento. Para terminar su cuenta, debe
                        seguir las instrucciones disponibles en la Plataforma o contactar al soporte al cliente.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">9.2. Terminación por Parte de la Compañía</p>
                    <p>La Compañía puede, a su sola discreción, suspender o terminar su acceso y uso de la Plataforma,
                        con o sin previo aviso, por cualquier razón, incluyendo, sin limitación, si la Compañía cree
                        razonablemente que usted ha violado estos Términos.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">9.3. Efecto de la Terminación</p>
                    <p>Tras la terminación de su cuenta o acceso a la Plataforma, su derecho a utilizar la Plataforma
                        cesará inmediatamente. La Compañía no tendrá obligación de mantener o proporcionar ningún Dato
                        del Usuario después de dicha terminación. Todas las disposiciones de estos Términos que por su
                        naturaleza deberían sobrevivir a la terminación sobrevivirán, incluyendo, sin limitación, las
                        disposiciones de propiedad, renuncias de garantía, indemnización y limitaciones de
                        responsabilidad.</p>
                </div>
            </div>

            <div class="section">
                <h2>10. Modificaciones a los Términos</h2>
                <p>La Compañía se reserva el derecho, a su sola discreción, de modificar o reemplazar cualquier parte de
                    estos Términos en cualquier momento. Es su responsabilidad revisar estos Términos periódicamente
                    para ver si hay cambios. Su uso continuado de o acceso a la Plataforma después de la publicación de
                    cualquier cambio a estos Términos constituye la aceptación de esos cambios. La Compañía puede, a su
                    discreción, notificarle sobre cambios materiales a estos Términos por correo electrónico o mediante
                    un aviso en la Plataforma.</p>
            </div>

            <div class="section">
                <h2>11. Disposiciones Generales</h2>
                <div class="term-item">
                    <p class="term-title">11.1. Ley Aplicable</p>
                    <p>Estos Términos y cualquier disputa o reclamo que surja de o en relación con ellos o su objeto o
                        formación (incluyendo disputas o reclamos no contractuales) se regirán e interpretarán de
                        acuerdo con las leyes de Colombia, sin dar efecto a ninguna elección o conflicto de
                        disposiciones o reglas de ley que resultarían en la aplicación de las leyes de cualquier otra
                        jurisdicción.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">11.2. Resolución de Disputas</p>
                    <p>Cualquier disputa legal que surja de estos Términos será resuelta exclusivamente mediante
                        arbitraje vinculante de acuerdo con las reglas de arbitraje de la Cámara de Comercio de Bogotá.
                        El lugar del arbitraje será Bogotá, Colombia. El idioma del arbitraje será el español.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">11.3. Divisibilidad</p>
                    <p>Si alguna disposición de estos Términos se considera inválida, ilegal o inaplicable por cualquier
                        razón por cualquier tribunal de jurisdicción competente, dicha disposición se eliminará o
                        limitará al mínimo de modo que las disposiciones restantes de estos Términos continuarán en
                        pleno vigor y efecto.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">11.4. Acuerdo Completo</p>
                    <p>Estos Términos, junto con la Política de Privacidad y cualquier otro acuerdo expresamente
                        incorporado por referencia, constituyen el acuerdo completo entre usted y la Compañía con
                        respecto a la Plataforma y reemplazan todas las comunicaciones y propuestas anteriores o
                        contemporáneas, ya sean electrónicas, orales o escritas, entre usted y la Compañía con respecto
                        a la Plataforma.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">11.5. Renuncia</p>
                    <p>El hecho de que la Compañía no ejerza o haga cumplir cualquier derecho o disposición de estos
                        Términos no constituirá una renuncia a tal derecho o disposición. La renuncia a cualquier
                        derecho o disposición solo será efectiva si es por escrito y firmada por un representante
                        debidamente autorizado de la Compañía.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">11.6. Cesión</p>
                    <p>Estos Términos, y cualquier derecho y licencia otorgados en virtud del presente, no pueden ser
                        transferidos o cedidos por usted, pero pueden ser cedidos por la Compañía sin restricción.
                        Cualquier intento de transferencia o cesión en violación de lo anterior será nulo.</p>
                </div>

                <div class="term-item">
                    <p class="term-title">11.7. Notificaciones</p>
                    <p>Cualquier notificación u otra comunicación proporcionada por la Compañía bajo estos Términos se
                        considerará entregada: (i) al ser publicada en la Plataforma; o (ii) al ser enviada por correo
                        electrónico a la dirección de correo electrónico asociada con su cuenta.</p>
                </div>
            </div>

            <div class="section">
                <h2>12. Contacto</h2>
                <p>Si tiene alguna pregunta sobre estos Términos, por favor contáctenos a:</p>
                <p><strong>Stock Control S.A.S.</strong><br>
                    Dirección: Calle 123 #45-67, Bogotá, Colombia<br>
                    Correo electrónico: truquemdaniels.cla@gmail.com<br>
                    Teléfono: (+57) 3246044420</p>
            </div>

            <div class="mt-4">
                <p>Al utilizar nuestra Plataforma, usted reconoce que ha leído, entendido y acepta estar sujeto a estos
                    Términos y Condiciones.</p>
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