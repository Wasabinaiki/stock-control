<?php
/**
 * Crea un backup de la base de datos
 * 
 * @param string $nombre_backup Nombre del archivo de backup (opcional)
 * @return array Resultado de la operación
 */
function crear_backup_bd($nombre_backup = null)
{
    global $link;

    $db_host = DB_SERVER;
    $db_user = DB_USERNAME;
    $db_pass = DB_PASSWORD;
    $db_name = DB_NAME;

    if ($nombre_backup === null) {
        $nombre_backup = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    }

    $directorio_backups = 'backups/';

    if (!is_dir($directorio_backups)) {
        mkdir($directorio_backups, 0777, true);
    }

    $ruta_backup = $directorio_backups . $nombre_backup;

    try {
        $archivo = fopen($ruta_backup, 'w');

        if (!$archivo) {
            return [
                'exito' => false,
                'mensaje' => 'Error al crear el archivo de backup',
                'error' => error_get_last()
            ];
        }

        fwrite($archivo, "-- Backup generado el " . date('Y-m-d H:i:s') . "\n");
        fwrite($archivo, "-- Base de datos: " . $db_name . "\n\n");

        $tablas_result = mysqli_query($link, "SHOW TABLES");
        $tablas = [];

        while ($row = mysqli_fetch_row($tablas_result)) {
            $tablas[] = $row[0];
        }

        foreach ($tablas as $tabla) {
            fwrite($archivo, "-- Estructura de la tabla `$tabla`\n");
            fwrite($archivo, "DROP TABLE IF EXISTS `$tabla`;\n");

            $create_table_result = mysqli_query($link, "SHOW CREATE TABLE `$tabla`");
            $create_table_row = mysqli_fetch_row($create_table_result);
            fwrite($archivo, $create_table_row[1] . ";\n\n");

            fwrite($archivo, "-- Datos de la tabla `$tabla`\n");

            $data_result = mysqli_query($link, "SELECT * FROM `$tabla`");
            $num_fields = mysqli_num_fields($data_result);
            $num_rows = mysqli_num_rows($data_result);

            if ($num_rows > 0) {
                fwrite($archivo, "INSERT INTO `$tabla` VALUES\n");

                $counter = 0;
                while ($row = mysqli_fetch_row($data_result)) {
                    $counter++;
                    fwrite($archivo, "(");

                    for ($i = 0; $i < $num_fields; $i++) {
                        if (is_null($row[$i])) {
                            fwrite($archivo, "NULL");
                        } else {
                            $row[$i] = addslashes($row[$i]);
                            $row[$i] = str_replace("\n", "\\n", $row[$i]);
                            fwrite($archivo, "'" . $row[$i] . "'");
                        }

                        if ($i < ($num_fields - 1)) {
                            fwrite($archivo, ",");
                        }
                    }

                    if ($counter == $num_rows) {
                        fwrite($archivo, ");\n");
                    } else {
                        fwrite($archivo, "),\n");
                    }
                }
            }

            fwrite($archivo, "\n\n");
        }

        fclose($archivo);

        if (file_exists($ruta_backup) && filesize($ruta_backup) > 0) {
            $tamanio = filesize($ruta_backup);
            $id_usuario = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

            $sql = "INSERT INTO backups (nombre, fecha, tamanio, ruta, id_usuario) VALUES (?, NOW(), ?, ?, ?)";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "sisi", $nombre_backup, $tamanio, $ruta_backup, $id_usuario);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            return [
                'exito' => true,
                'mensaje' => 'Backup creado correctamente',
                'archivo' => $nombre_backup,
                'ruta' => $ruta_backup,
                'tamanio' => $tamanio
            ];
        } else {
            return [
                'exito' => false,
                'mensaje' => 'Error al crear el backup: archivo vacío o no creado',
                'error' => error_get_last()
            ];
        }
    } catch (Exception $e) {
        return [
            'exito' => false,
            'mensaje' => 'Error al crear el backup',
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Restaura un backup de la base de datos
 * 
 * @param string $ruta_backup Ruta del archivo de backup
 * @return array Resultado de la operación
 */
function restaurar_backup_bd($ruta_backup)
{
    global $link;

    if (!file_exists($ruta_backup)) {
        return [
            'exito' => false,
            'mensaje' => 'El archivo de backup no existe'
        ];
    }

    try {
        $sql_content = file_get_contents($ruta_backup);

        if ($sql_content === false) {
            return [
                'exito' => false,
                'mensaje' => 'No se pudo leer el archivo de backup',
                'error' => error_get_last()
            ];
        }

        $queries = explode(';', $sql_content);

        mysqli_query($link, "SET FOREIGN_KEY_CHECKS=0");

        $error = false;
        $error_message = '';

        foreach ($queries as $query) {
            $query = trim($query);

            if (!empty($query)) {
                $result = mysqli_query($link, $query);

                if (!$result) {
                    $error = true;
                    $error_message = mysqli_error($link);
                    break;
                }
            }
        }

        mysqli_query($link, "SET FOREIGN_KEY_CHECKS=1");

        if ($error) {
            return [
                'exito' => false,
                'mensaje' => 'Error al restaurar el backup',
                'error' => $error_message
            ];
        } else {
            return [
                'exito' => true,
                'mensaje' => 'Backup restaurado correctamente'
            ];
        }
    } catch (Exception $e) {
        return [
            'exito' => false,
            'mensaje' => 'Error al restaurar el backup',
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Obtiene la lista de backups disponibles
 * 
 * @return array Lista de backups
 */
function obtener_backups()
{
    global $link;

    $sql = "SELECT * FROM backups ORDER BY fecha DESC";
    $result = mysqli_query($link, $sql);

    $backups = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $backups[] = $row;
        }
    }

    return $backups;
}

/**
 * Elimina un backup
 * 
 * @param int $id_backup ID del backup a eliminar
 * @return array Resultado de la operación
 */
function eliminar_backup($id_backup)
{
    global $link;

    $sql = "SELECT * FROM backups WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_backup);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $ruta_backup = $row['ruta'];

        if (file_exists($ruta_backup)) {
            unlink($ruta_backup);
        }

        $sql = "DELETE FROM backups WHERE id = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_backup);
        mysqli_stmt_execute($stmt);

        return [
            'exito' => true,
            'mensaje' => 'Backup eliminado correctamente'
        ];
    } else {
        return [
            'exito' => false,
            'mensaje' => 'Backup no encontrado'
        ];
    }
}

/**
 * Descarga un backup
 * 
 * @param int $id_backup ID del backup a descargar
 * @return bool True si se inició la descarga, False en caso contrario
 */
function descargar_backup($id_backup)
{
    global $link;

    $sql = "SELECT * FROM backups WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_backup);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $ruta_backup = $row['ruta'];
        $nombre_backup = $row['nombre'];

        if (file_exists($ruta_backup)) {
            // Configurar cabeceras para descarga
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $nombre_backup . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($ruta_backup));

            readfile($ruta_backup);
            exit;
        }
    }

    return false;
}

/**
 * Programa un backup automático
 * 
 * @param string $frecuencia Frecuencia del backup (diario, semanal, mensual)
 * @return array Resultado de la operación
 */
function programar_backup_automatico($frecuencia)
{
    global $link;

    $sql = "SELECT * FROM configuracion WHERE clave = 'backup_automatico'";
    $result = mysqli_query($link, $sql);

    if (mysqli_num_rows($result) > 0) {
        $sql = "UPDATE configuracion SET valor = ? WHERE clave = 'backup_automatico'";
    } else {
        $sql = "INSERT INTO configuracion (clave, valor) VALUES ('backup_automatico', ?)";
    }

    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $frecuencia);
    $resultado = mysqli_stmt_execute($stmt);

    if ($resultado) {
        return [
            'exito' => true,
            'mensaje' => 'Backup automático programado correctamente'
        ];
    } else {
        return [
            'exito' => false,
            'mensaje' => 'Error al programar el backup automático'
        ];
    }
}

/**
 * Obtiene la configuración de backup automático
 * 
 * @return string|null Frecuencia del backup automático o null si no está configurado
 */
function obtener_configuracion_backup_automatico()
{
    global $link;

    $sql = "SELECT valor FROM configuracion WHERE clave = 'backup_automatico'";
    $result = mysqli_query($link, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['valor'];
    }

    return null;
}
