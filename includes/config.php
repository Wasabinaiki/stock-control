<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'control-stock');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: No se pudo conectar. " . mysqli_connect_error());
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (file_exists(__DIR__ . "/audit_functions.php")) {
    require_once __DIR__ . "/audit_functions.php";
}

function isLoggedIn()
{
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}

function hasRole($role)
{
    return isset($_SESSION["rol"]) && $_SESSION["rol"] === $role;
}

function redirectIfNotLoggedIn()
{
    if (!isLoggedIn()) {
        header("location: login.php");
        exit;
    }
}

function redirectIfNotAdmin()
{
    if (!hasRole('administrador')) {
        header("location: index.php");
        exit;
    }
}

function registrarAccion($accion, $tabla = null, $id_registro = null, $detalles = null)
{
    if (function_exists('registrar_auditoria') && isset($_SESSION["id"])) {
        return registrar_auditoria($_SESSION["id"], $accion, $tabla, $id_registro, $detalles);
    }
    return false;
}

function registrarAccesoPagina()
{
    if (function_exists('registrar_acceso_modulo') && isset($_SESSION["id"])) {
        // Obtener el nombre del archivo actual sin la extensión
        $pagina_actual = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        registrar_acceso_modulo($_SESSION["id"], "Acceso a página: " . $pagina_actual);
    }
}

function insertarRegistro($tabla, $datos, $sql = null)
{
    global $link;

    if ($sql === null) {
        $columnas = implode(", ", array_keys($datos));
        $valores = implode(", ", array_fill(0, count($datos), "?"));
        $sql = "INSERT INTO $tabla ($columnas) VALUES ($valores)";
    }

    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        $tipos = str_repeat("s", count($datos));
        $valores = array_values($datos);

        $params = array();
        $params[] = &$tipos;
        foreach ($valores as $key => $value) {
            $params[] = &$valores[$key];
        }

        call_user_func_array(array($stmt, 'bind_param'), $params);

        if (mysqli_stmt_execute($stmt)) {
            $id_insertado = mysqli_insert_id($link);
            mysqli_stmt_close($stmt);

            if (isset($_SESSION["id"])) {
                $detalles = "Datos: " . json_encode($datos);
                registrarAccion("Creación de registro", $tabla, $id_insertado, $detalles);
            }

            return $id_insertado;
        }

        mysqli_stmt_close($stmt);
    }

    return false;
}

function actualizarRegistro($tabla, $datos, $condicion, $id_registro = null, $sql = null)
{
    global $link;

    if ($sql === null) {
        $actualizaciones = array();
        foreach (array_keys($datos) as $columna) {
            $actualizaciones[] = "$columna = ?";
        }
        $sql = "UPDATE $tabla SET " . implode(", ", $actualizaciones) . " WHERE $condicion";
    }

    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        $tipos = str_repeat("s", count($datos));
        $valores = array_values($datos);

        $params = array();
        $params[] = &$tipos;
        foreach ($valores as $key => $value) {
            $params[] = &$valores[$key];
        }

        call_user_func_array(array($stmt, 'bind_param'), $params);

        if (mysqli_stmt_execute($stmt)) {
            $filas_afectadas = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);

            if (isset($_SESSION["id"]) && $filas_afectadas > 0) {
                $detalles = "Datos: " . json_encode($datos) . ", Condición: $condicion";
                registrarAccion("Modificación de registro", $tabla, $id_registro, $detalles);
            }

            return $filas_afectadas;
        }

        mysqli_stmt_close($stmt);
    }

    return false;
}

function eliminarRegistro($tabla, $condicion, $id_registro = null)
{
    global $link;

    $sql = "DELETE FROM $tabla WHERE $condicion";
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        if (mysqli_stmt_execute($stmt)) {
            $filas_afectadas = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);

            if (isset($_SESSION["id"]) && $filas_afectadas > 0) {
                $detalles = "Condición: $condicion";
                registrarAccion("Eliminación de registro", $tabla, $id_registro, $detalles);
            }

            return $filas_afectadas;
        }

        mysqli_stmt_close($stmt);
    }

    return false;
}

if (isLoggedIn() && !strpos($_SERVER['PHP_SELF'], 'admin_auditoria.php')) {
    registrarAccesoPagina();
}
?>