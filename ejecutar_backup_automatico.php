<?php
/*
GUIA PARA USO DE BACKUP AUTOMATICO
CON WINDOWS MEDIANTE PROGRAMADOR DE TAREAS
1) Abrir el Programador de tareas de Windows
2) Clic en "Crear tarea básica"
3) Nombrarlo de cualquier forma, por ejemeplo: "Backup Automático Sistema"
4) Seleccionar la frecuencia "Diaria"
5) Establecer la hora (por ejemplo, 3:00 AM)
6) En Acción, seleccionar "Iniciar un programa"
7) En Programa/script, ingresar la ruta a PHP: `C:\xampp\php\php.exe`
8) En Argumentos, ingresar la ruta completa al script: `C:\xampp\htdocs\tu-proyecto\ejecutar_backup_automatico.php`
9) Listo!
*/
session_start();

require_once "includes/config.php";
require_once "includes/backup_functions.php";

if (!isset($_SESSION["id"])) {
    $sql = "SELECT id_usuario FROM usuarios WHERE rol = 'administrador' LIMIT 1";
    $result = mysqli_query($link, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION["id"] = $row['id_usuario'];
    } else {
        $_SESSION["id"] = 1;
    }
}

function verificar_backup_automatico($link)
{
    $sql = "SELECT valor FROM configuracion WHERE clave = 'backup_automatico'";
    $result = mysqli_query($link, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {
        return false;
    }

    $row = mysqli_fetch_assoc($result);
    $frecuencia = $row['valor'];

    if ($frecuencia == 'ninguno') {
        return false;
    }

    $sql = "SELECT valor FROM configuracion WHERE clave = 'ultimo_backup_automatico'";
    $result = mysqli_query($link, $sql);

    $ultimo_backup = null;
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $ultimo_backup = $row['valor'];
    }

    $ahora = time();
    $crear_backup = false;

    if ($ultimo_backup === null) {
        $crear_backup = true;
    } else {
        $ultimo_backup_time = strtotime($ultimo_backup);

        switch ($frecuencia) {
            case 'diario':
                $crear_backup = ($ahora - $ultimo_backup_time) >= 86400;
                break;

            case 'semanal':
                $crear_backup = ($ahora - $ultimo_backup_time) >= 604800;
                break;

            case 'mensual':
                $crear_backup = ($ahora - $ultimo_backup_time) >= 2592000;
                break;
        }
    }

    return $crear_backup;
}

function actualizar_fecha_ultimo_backup($link)
{
    $fecha_actual = date('Y-m-d H:i:s');

    $sql = "SELECT * FROM configuracion WHERE clave = 'ultimo_backup_automatico'";
    $result = mysqli_query($link, $sql);

    if (mysqli_num_rows($result) > 0) {
        $sql = "UPDATE configuracion SET valor = ? WHERE clave = 'ultimo_backup_automatico'";
    } else {
        $sql = "INSERT INTO configuracion (clave, valor) VALUES ('ultimo_backup_automatico', ?)";
    }

    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $fecha_actual);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function registrar_log($mensaje)
{
    $archivo_log = 'backups/backup_log.txt';
    $fecha = date('Y-m-d H:i:s');
    $entrada = "[$fecha] $mensaje\n";

    if (!is_dir('backups')) {
        mkdir('backups', 0777, true);
    }

    file_put_contents($archivo_log, $entrada, FILE_APPEND);
}

if (verificar_backup_automatico($link)) {

    $nombre_backup = 'backup_automatico_' . date('Y-m-d_H-i-s') . '.sql';
    $resultado = crear_backup_bd($nombre_backup);

    if ($resultado['exito']) {
        actualizar_fecha_ultimo_backup($link);

        registrar_log("Backup automático creado correctamente: " . $nombre_backup);

        if (function_exists('registrar_auditoria')) {
            registrar_auditoria($_SESSION["id"], "Creación de backup automático", "backups", null, "Nombre: " . $nombre_backup);
        }
    } else {
        registrar_log("Error al crear backup automático: " . $resultado['mensaje']);
    }
} else {
    registrar_log("Verificación de backup automático: no es necesario crear un backup en este momento");
}

mysqli_close($link);

echo "Proceso de verificación de backup automático completado.\n";
?>