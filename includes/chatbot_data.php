<?php
/**
 * Datos para el chatbot de ayuda
 */

// Categorías y preguntas frecuentes
$chatbot_categorias = [
    [
        'id' => 'dispositivos',
        'nombre' => 'Dispositivos',
        'icono' => 'fas fa-laptop',
        'preguntas' => [
            [
                'id' => 'registrar_dispositivo',
                'pregunta' => '¿Cómo registrar un nuevo dispositivo?',
                'respuesta' => 'Para registrar un nuevo dispositivo, sigue estos pasos:<br>
                               1. Ve a la sección "Dispositivos" en el menú lateral.<br>
                               2. Selecciona el tipo de dispositivo (Computadora, Tablet o Celular).<br>
                               3. Haz clic en el botón "Agregar nuevo dispositivo".<br>
                               4. Completa el formulario con la información requerida.<br>
                               5. Haz clic en "Guardar" para registrar el dispositivo.'
            ],
            [
                'id' => 'editar_dispositivo',
                'pregunta' => '¿Cómo editar información de un dispositivo?',
                'respuesta' => 'Para editar la información de un dispositivo existente:<br>
                               1. Ve a la sección "Dispositivos" en el menú lateral.<br>
                               2. Selecciona el tipo de dispositivo que deseas editar.<br>
                               3. Busca el dispositivo en la lista y haz clic en el botón "Editar".<br>
                               4. Modifica la información necesaria en el formulario.<br>
                               5. Haz clic en "Guardar cambios" para actualizar la información.'
            ],
            [
                'id' => 'eliminar_dispositivo',
                'pregunta' => '¿Cómo eliminar un dispositivo?',
                'respuesta' => 'Para eliminar un dispositivo de tu inventario:<br>
                               1. Ve a la sección "Dispositivos" en el menú lateral.<br>
                               2. Selecciona el tipo de dispositivo que deseas eliminar.<br>
                               3. Busca el dispositivo en la lista y haz clic en el botón "Eliminar".<br>
                               4. Confirma la eliminación en el cuadro de diálogo que aparece.<br>
                               <strong>Nota:</strong> Esta acción no se puede deshacer. Asegúrate de que realmente deseas eliminar el dispositivo.'
            ],
            [
                'id' => 'estados_dispositivo',
                'pregunta' => '¿Qué significan los estados de los dispositivos?',
                'respuesta' => 'Los dispositivos pueden tener los siguientes estados:<br>
                               <strong>Activo:</strong> El dispositivo está en uso y funcionando correctamente.<br>
                               <strong>En Reparación:</strong> El dispositivo está siendo reparado o recibiendo mantenimiento.<br>
                               <strong>Inactivo:</strong> El dispositivo no está en uso actualmente.<br>
                               <strong>Completado:</strong> El dispositivo ha completado su ciclo de vida o proceso.'
            ]
        ]
    ],
    [
        'id' => 'mantenimientos',
        'nombre' => 'Mantenimientos',
        'icono' => 'fas fa-tools',
        'preguntas' => [
            [
                'id' => 'programar_mantenimiento',
                'pregunta' => '¿Cómo programar un mantenimiento?',
                'respuesta' => 'Para programar un mantenimiento para tu dispositivo:<br>
                               1. Ve a la sección "Mantenimientos" en el menú lateral.<br>
                               2. Haz clic en el botón "Programar Mantenimiento".<br>
                               3. Selecciona el dispositivo que requiere mantenimiento.<br>
                               4. Completa el formulario con la descripción del problema y la fecha deseada.<br>
                               5. Haz clic en "Programar" para registrar el mantenimiento.'
            ],
            [
                'id' => 'ver_mantenimientos',
                'pregunta' => '¿Cómo ver el historial de mantenimientos?',
                'respuesta' => 'Para ver el historial de mantenimientos de tus dispositivos:<br>
                               1. Ve a la sección "Mantenimientos" en el menú lateral.<br>
                               2. Verás una lista de todos los mantenimientos programados y completados.<br>
                               3. Puedes filtrar por estado (Programado, En Proceso, Completado) usando el filtro en la parte superior.<br>
                               4. Para ver más detalles, haz clic en el mantenimiento específico.'
            ],
            [
                'id' => 'estados_mantenimiento',
                'pregunta' => '¿Qué significan los estados de mantenimiento?',
                'respuesta' => 'Los mantenimientos pueden tener los siguientes estados:<br>
                               <strong>Programado:</strong> El mantenimiento ha sido agendado pero aún no ha comenzado.<br>
                               <strong>En Proceso:</strong> El mantenimiento está siendo realizado actualmente.<br>
                               <strong>Completado:</strong> El mantenimiento ha sido finalizado satisfactoriamente.'
            ],
            [
                'id' => 'cancelar_mantenimiento',
                'pregunta' => '¿Cómo cancelar un mantenimiento programado?',
                'respuesta' => 'Para cancelar un mantenimiento programado:<br>
                               1. Ve a la sección "Mantenimientos" en el menú lateral.<br>
                               2. Localiza el mantenimiento que deseas cancelar.<br>
                               3. Haz clic en el botón "Cancelar" junto al mantenimiento.<br>
                               4. Confirma la cancelación en el cuadro de diálogo que aparece.<br>
                               <strong>Nota:</strong> Solo puedes cancelar mantenimientos en estado "Programado".'
            ]
        ]
    ],
    [
        'id' => 'pqrs',
        'nombre' => 'PQRS',
        'icono' => 'fas fa-clipboard-list',
        'preguntas' => [
            [
                'id' => 'crear_pqrs',
                'pregunta' => '¿Cómo crear una nueva solicitud PQRS?',
                'respuesta' => 'Para crear una nueva solicitud PQRS (Petición, Queja, Reclamo o Sugerencia):<br>
                               1. Ve a la sección "PQRS" en el menú lateral.<br>
                               2. Haz clic en el botón "Enviar PQRS".<br>
                               3. Selecciona el tipo de solicitud (Petición, Queja, Reclamo o Sugerencia).<br>
                               4. Completa el formulario con la descripción detallada.<br>
                               5. Haz clic en "Enviar" para registrar tu solicitud.'
            ],
            [
                'id' => 'seguimiento_pqrs',
                'pregunta' => '¿Cómo dar seguimiento a mi solicitud PQRS?',
                'respuesta' => 'Para dar seguimiento a tus solicitudes PQRS:<br>
                               1. Ve a la sección "PQRS" en el menú lateral.<br>
                               2. En la pestaña "Mis Solicitudes" verás todas tus PQRS.<br>
                               3. Puedes ver el estado actual de cada solicitud.<br>
                               4. Para ver más detalles o la respuesta, haz clic en "Ver detalles".'
            ],
            [
                'id' => 'estados_pqrs',
                'pregunta' => '¿Qué significan los estados de las solicitudes PQRS?',
                'respuesta' => 'Las solicitudes PQRS pueden tener los siguientes estados:<br>
                               <strong>Pendiente:</strong> La solicitud ha sido recibida pero aún no ha sido revisada.<br>
                               <strong>En Proceso:</strong> La solicitud está siendo atendida por el personal correspondiente.<br>
                               <strong>Resuelto:</strong> La solicitud ha sido atendida y resuelta satisfactoriamente.'
            ],
            [
                'id' => 'adjuntar_archivos',
                'pregunta' => '¿Cómo adjuntar archivos a mi solicitud PQRS?',
                'respuesta' => 'Para adjuntar archivos a tu solicitud PQRS:<br>
                               1. Al crear una nueva solicitud, verás la opción "Adjuntar archivos" en el formulario.<br>
                               2. Haz clic en "Seleccionar archivo" y elige el archivo que deseas adjuntar.<br>
                               3. Puedes adjuntar múltiples archivos si es necesario.<br>
                               4. Los formatos permitidos son: PDF, JPG, PNG y documentos de Office.<br>
                               5. El tamaño máximo por archivo es de 5MB.'
            ]
        ]
    ],
    [
        'id' => 'reportes',
        'nombre' => 'Reportes',
        'icono' => 'fas fa-chart-bar',
        'preguntas' => [
            [
                'id' => 'generar_reporte',
                'pregunta' => '¿Cómo generar un reporte?',
                'respuesta' => 'Para generar un reporte:<br>
                               1. Ve a la sección "Reportes" en el menú lateral.<br>
                               2. Selecciona el tipo de reporte que deseas generar.<br>
                               3. Aplica los filtros necesarios (fechas, categorías, etc.).<br>
                               4. Haz clic en "Generar Reporte" para ver los resultados.<br>
                               5. Puedes exportar el reporte en formato PDF o CSV usando los botones correspondientes.'
            ],
            [
                'id' => 'tipos_reportes',
                'pregunta' => '¿Qué tipos de reportes puedo generar?',
                'respuesta' => 'Puedes generar los siguientes tipos de reportes:<br>
                               <strong>Inventario de Dispositivos:</strong> Muestra todos tus dispositivos registrados.<br>
                               <strong>Mantenimientos:</strong> Historial de mantenimientos de tus dispositivos.<br>
                               <strong>PQRS:</strong> Historial de tus solicitudes PQRS.<br>
                               <strong>Actividad:</strong> Registro de tus acciones en el sistema.'
            ],
            [
                'id' => 'exportar_reporte',
                'pregunta' => '¿Cómo exportar un reporte a PDF?',
                'respuesta' => 'Para exportar un reporte a PDF:<br>
                               1. Genera el reporte que deseas exportar.<br>
                               2. En la parte superior de la tabla de resultados, haz clic en el botón "Exportar a PDF".<br>
                               3. El sistema generará el archivo PDF y comenzará la descarga automáticamente.<br>
                               4. También puedes imprimir directamente el reporte usando el botón "Imprimir".'
            ],
            [
                'id' => 'filtrar_reporte',
                'pregunta' => '¿Cómo filtrar la información en los reportes?',
                'respuesta' => 'Para filtrar la información en los reportes:<br>
                               1. Ve a la sección de reportes y selecciona el tipo de reporte.<br>
                               2. Verás opciones de filtro en la parte superior de la página.<br>
                               3. Puedes filtrar por fechas, categorías, estados, etc.<br>
                               4. Aplica los filtros deseados y haz clic en "Aplicar Filtros".<br>
                               5. Para limpiar los filtros, haz clic en "Limpiar Filtros".'
            ]
        ]
    ],
    [
        'id' => 'cuenta',
        'nombre' => 'Cuenta y Seguridad',
        'icono' => 'fas fa-user-shield',
        'preguntas' => [
            [
                'id' => 'cambiar_contrasena',
                'pregunta' => '¿Cómo cambiar mi contraseña?',
                'respuesta' => 'Para cambiar tu contraseña:<br>
                               1. Haz clic en tu nombre de usuario en la esquina superior derecha.<br>
                               2. Selecciona "Perfil" en el menú desplegable.<br>
                               3. En la sección "Seguridad", haz clic en "Cambiar contraseña".<br>
                               4. Ingresa tu contraseña actual y la nueva contraseña dos veces.<br>
                               5. Haz clic en "Guardar cambios" para actualizar tu contraseña.'
            ],
            [
                'id' => 'actualizar_perfil',
                'pregunta' => '¿Cómo actualizar mi información de perfil?',
                'respuesta' => 'Para actualizar tu información de perfil:<br>
                               1. Haz clic en tu nombre de usuario en la esquina superior derecha.<br>
                               2. Selecciona "Perfil" en el menú desplegable.<br>
                               3. En la sección "Información personal", verás tus datos actuales.<br>
                               4. Haz clic en "Editar" para modificar tu información.<br>
                               5. Actualiza los campos necesarios y haz clic en "Guardar cambios".'
            ],
            [
                'id' => 'recuperar_contrasena',
                'pregunta' => '¿Qué hacer si olvidé mi contraseña?',
                'respuesta' => 'Si olvidaste tu contraseña:<br>
                               1. En la página de inicio de sesión, haz clic en "¿Olvidaste tu contraseña?".<br>
                               2. Ingresa tu dirección de correo electrónico registrada.<br>
                               3. Recibirás un correo con un enlace para restablecer tu contraseña.<br>
                               4. Haz clic en el enlace y sigue las instrucciones para crear una nueva contraseña.<br>
                               5. Una vez cambiada, podrás iniciar sesión con tu nueva contraseña.'
            ],
            [
                'id' => 'cerrar_sesion',
                'pregunta' => '¿Cómo cerrar sesión correctamente?',
                'respuesta' => 'Para cerrar sesión de forma segura:<br>
                               1. Haz clic en tu nombre de usuario en la esquina superior derecha.<br>
                               2. Selecciona "Cerrar sesión" en el menú desplegable.<br>
                               <strong>Importante:</strong> Siempre cierra sesión cuando termines de usar el sistema, especialmente en dispositivos compartidos o públicos.'
            ]
        ]
    ]
];

/**
 * Obtiene todas las categorías del chatbot
 * 
 * @return array Categorías del chatbot
 */
function obtener_categorias_chatbot()
{
    global $chatbot_categorias;
    return $chatbot_categorias;
}

/**
 * Obtiene una categoría específica del chatbot
 * 
 * @param string $id_categoria ID de la categoría
 * @return array|null Categoría encontrada o null si no existe
 */
function obtener_categoria_chatbot($id_categoria)
{
    global $chatbot_categorias;

    foreach ($chatbot_categorias as $categoria) {
        if ($categoria['id'] === $id_categoria) {
            return $categoria;
        }
    }

    return null;
}

/**
 * Obtiene una pregunta específica del chatbot
 * 
 * @param string $id_categoria ID de la categoría
 * @param string $id_pregunta ID de la pregunta
 * @return array|null Pregunta encontrada o null si no existe
 */
function obtener_pregunta_chatbot($id_categoria, $id_pregunta)
{
    $categoria = obtener_categoria_chatbot($id_categoria);

    if ($categoria) {
        foreach ($categoria['preguntas'] as $pregunta) {
            if ($pregunta['id'] === $id_pregunta) {
                return $pregunta;
            }
        }
    }

    return null;
}

/**
 * Busca preguntas en el chatbot que coincidan con un término de búsqueda
 * 
 * @param string $termino Término de búsqueda
 * @return array Preguntas encontradas
 */
function buscar_preguntas_chatbot($termino)
{
    global $chatbot_categorias;
    $resultados = [];

    if (empty($termino)) {
        return $resultados;
    }

    $termino = strtolower($termino);

    foreach ($chatbot_categorias as $categoria) {
        foreach ($categoria['preguntas'] as $pregunta) {
            if (
                strpos(strtolower($pregunta['pregunta']), $termino) !== false ||
                strpos(strtolower($pregunta['respuesta']), $termino) !== false
            ) {
                $resultados[] = [
                    'categoria' => $categoria['nombre'],
                    'categoria_id' => $categoria['id'],
                    'pregunta' => $pregunta
                ];
            }
        }
    }

    return $resultados;
}
