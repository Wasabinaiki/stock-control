<?php

/**
 * Registra una acción en el sistema de auditoría
 * 
 * @param int $id_usuario ID del usuario que realiza la acción
 * @param string $accion Descripción de la acción realizada
 * @param string $tabla Nombre de la tabla afectada (opcional)
 * @param int $id_registro ID del registro afectado (opcional)
 * @param string $detalles Detalles adicionales (opcional)
 * @return bool True si se registró correctamente, False en caso contrario
 */
function registrar_auditoria($id_usuario, $accion, $tabla = null, $id_registro = null, $detalles = null)
{
    global $link;

    $ip_usuario = obtener_ip_usuario();

    $sql = "INSERT INTO auditoria (id_usuario, accion, tabla, id_registro, detalles, ip_usuario) 
            VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ississ", $id_usuario, $accion, $tabla, $id_registro, $detalles, $ip_usuario);

        $resultado = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        return $resultado;
    }

    return false;
}

/**
 * Obtiene la dirección IP del usuario
 * 
 * @return string La dirección IP del usuario
 */
function obtener_ip_usuario()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

/**
 * Registra un inicio de sesión en el sistema de auditoría
 * 
 * @param int $id_usuario ID del usuario que inicia sesión
 * @param bool $exitoso Indica si el inicio de sesión fue exitoso
 * @return bool True si se registró correctamente, False en caso contrario
 */
function registrar_inicio_sesion($id_usuario, $exitoso = true)
{
    $accion = $exitoso ? "Inicio de sesión exitoso" : "Intento fallido de inicio de sesión";
    return registrar_auditoria($id_usuario, $accion, "usuarios", $id_usuario);
}

/**
 * Registra un cierre de sesión en el sistema de auditoría
 * 
 * @param int $id_usuario ID del usuario que cierra sesión
 * @return bool True si se registró correctamente, False en caso contrario
 */
function registrar_cierre_sesion($id_usuario)
{
    return registrar_auditoria($id_usuario, "Cierre de sesión", "usuarios", $id_usuario);
}

/**
 * Registra la creación de un registro en el sistema de auditoría
 * 
 * @param int $id_usuario ID del usuario que crea el registro
 * @param string $tabla Nombre de la tabla donde se crea el registro
 * @param int $id_registro ID del registro creado
 * @param string $detalles Detalles adicionales (opcional)
 * @return bool True si se registró correctamente, False en caso contrario
 */
function registrar_creacion($id_usuario, $tabla, $id_registro, $detalles = null)
{
    return registrar_auditoria($id_usuario, "Creación de registro", $tabla, $id_registro, $detalles);
}

/**
 * Registra la modificación de un registro en el sistema de auditoría
 * 
 * @param int $id_usuario ID del usuario que modifica el registro
 * @param string $tabla Nombre de la tabla donde se modifica el registro
 * @param int $id_registro ID del registro modificado
 * @param string $detalles Detalles adicionales (opcional)
 * @return bool True si se registró correctamente, False en caso contrario
 */
function registrar_modificacion($id_usuario, $tabla, $id_registro, $detalles = null)
{
    return registrar_auditoria($id_usuario, "Modificación de registro", $tabla, $id_registro, $detalles);
}

/**
 * Registra la eliminación de un registro en el sistema de auditoría
 * 
 * @param int $id_usuario ID del usuario que elimina el registro
 * @param string $tabla Nombre de la tabla donde se elimina el registro
 * @param int $id_registro ID del registro eliminado
 * @param string $detalles Detalles adicionales (opcional)
 * @return bool True si se registró correctamente, False en caso contrario
 */
function registrar_eliminacion($id_usuario, $tabla, $id_registro, $detalles = null)
{
    return registrar_auditoria($id_usuario, "Eliminación de registro", $tabla, $id_registro, $detalles);
}

/**
 * Registra el acceso a un módulo en el sistema de auditoría
 * 
 * @param int $id_usuario ID del usuario que accede al módulo
 * @param string $modulo Nombre del módulo al que se accede
 * @return bool True si se registró correctamente, False en caso contrario
 */
function registrar_acceso_modulo($id_usuario, $modulo)
{
    return registrar_auditoria($id_usuario, "Acceso a módulo: " . $modulo);
}

/**
 * Registra la generación de un reporte en el sistema de auditoría
 * 
 * @param int $id_usuario ID del usuario que genera el reporte
 * @param string $tipo_reporte Tipo de reporte generado
 * @param string $detalles Detalles adicionales (opcional)
 * @return bool True si se registró correctamente, False en caso contrario
 */
function registrar_generacion_reporte($id_usuario, $tipo_reporte, $detalles = null)
{
    return registrar_auditoria($id_usuario, "Generación de reporte: " . $tipo_reporte, null, null, $detalles);
}
