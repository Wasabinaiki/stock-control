<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'todos';
$formato = isset($_GET['formato']) ? $_GET['formato'] : 'pdf';
$id_usuario = isset($_GET['usuario']) ? $_GET['usuario'] : $_SESSION['id'];

$es_admin = ($_SESSION["rol"] === "administrador");

if (!$es_admin) {
    $id_usuario = $_SESSION['id'];
}

if (function_exists('registrar_generacion_reporte')) {
    registrar_generacion_reporte($_SESSION["id"], "Reporte: " . $tipo, "Formato: " . $formato);
} else {
    $accion = "Exportación de reporte: " . $tipo . " en formato " . $formato;
    $ip_usuario = $_SERVER['REMOTE_ADDR'];
    $sql_auditoria = "INSERT INTO auditoria (id_usuario, accion, ip_usuario, detalles) VALUES (?, ?, ?, ?)";
    $stmt_auditoria = mysqli_prepare($link, $sql_auditoria);
    if ($stmt_auditoria) {
        $detalles = "Usuario: " . $_SESSION['username'] . ", Tipo: " . $tipo . ", Formato: " . $formato;
        mysqli_stmt_bind_param($stmt_auditoria, "isss", $_SESSION['id'], $accion, $ip_usuario, $detalles);
        mysqli_stmt_execute($stmt_auditoria);
        mysqli_stmt_close($stmt_auditoria);
    }
}

function formatearEstado($estado)
{
    $estado = strtolower(str_replace('_', ' ', $estado));
    return ucwords($estado);
}

function obtenerMantenimientos($link, $id_usuario, $es_admin)
{
    $mantenimientos = [];

    if ($es_admin && $id_usuario == 'todos') {
        $sql = "SELECT m.id, m.id_dispositivo, m.fecha_programada, m.descripcion, m.estado, 
                d.marca, d.modelo, d.tipo as tipo_dispositivo, u.username 
                FROM mantenimientos m 
                JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
                JOIN usuarios u ON d.id_usuario = u.id_usuario
                ORDER BY m.fecha_programada DESC";
        $result = mysqli_query($link, $sql);
    } else {
        $sql = "SELECT m.id, m.id_dispositivo, m.fecha_programada, m.descripcion, m.estado, 
                d.marca, d.modelo, d.tipo as tipo_dispositivo, u.username 
                FROM mantenimientos m 
                JOIN dispositivos d ON m.id_dispositivo = d.id_dispositivo
                JOIN usuarios u ON d.id_usuario = u.id_usuario
                WHERE d.id_usuario = ?
                ORDER BY m.fecha_programada DESC";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $mantenimientos[] = $row;
        }
    }

    return $mantenimientos;
}

function obtenerDispositivos($link, $id_usuario, $es_admin)
{
    $dispositivos = [];

    if ($es_admin && $id_usuario == 'todos') {
        $sql = "SELECT d.*, u.username FROM dispositivos d 
                JOIN usuarios u ON d.id_usuario = u.id_usuario 
                ORDER BY d.fecha_entrega DESC";
        $result = mysqli_query($link, $sql);
    } else {
        $sql = "SELECT d.*, u.username FROM dispositivos d 
                JOIN usuarios u ON d.id_usuario = u.id_usuario 
                WHERE d.id_usuario = ?
                ORDER BY d.fecha_entrega DESC";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $dispositivos[] = $row;
        }
    }

    return $dispositivos;
}

function obtenerPQRs($link, $id_usuario, $es_admin)
{
    $pqrs = [];

    if ($es_admin && $id_usuario == 'todos') {
        $sql = "SELECT p.*, u.username 
                FROM pqrs p 
                JOIN usuarios u ON p.id_usuario = u.id_usuario 
                ORDER BY p.fecha_creacion DESC";
        $result = mysqli_query($link, $sql);
    } else {
        $sql = "SELECT p.*, u.username 
                FROM pqrs p 
                JOIN usuarios u ON p.id_usuario = u.id_usuario 
                WHERE p.id_usuario = ?
                ORDER BY p.fecha_creacion DESC";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $pqrs[] = $row;
        }
    }

    return $pqrs;
}

function obtenerContactos($link, $es_admin)
{
    $contactos = [];

    if ($es_admin) {
        $sql = "SELECT c.* FROM contactos c ORDER BY c.fecha DESC";
        $result = mysqli_query($link, $sql);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $contactos[] = $row;
            }
        }
    }

    return $contactos;
}

function obtenerDispositivosBodega($link, $id_usuario, $es_admin)
{
    $bodega = [];

    if ($es_admin && $id_usuario == 'todos') {
        $sql = "SELECT d.id_dispositivo, d.tipo, d.marca, d.modelo, d.estado, u.username 
                FROM dispositivos d
                JOIN usuarios u ON d.id_usuario = u.id_usuario
                ORDER BY d.id_dispositivo";
        $result = mysqli_query($link, $sql);
    } else {
        $sql = "SELECT d.id_dispositivo, d.tipo, d.marca, d.modelo, d.estado, u.username 
                FROM dispositivos d
                JOIN usuarios u ON d.id_usuario = u.id_usuario
                WHERE d.id_usuario = ?
                ORDER BY d.id_dispositivo";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $bodega[] = $row;
        }
    }

    return $bodega;
}

$mantenimientos = [];
$dispositivos = [];
$pqrs = [];
$contactos = [];
$bodega = [];

if ($tipo == 'todos' || $tipo == 'mantenimientos') {
    $mantenimientos = obtenerMantenimientos($link, $id_usuario, $es_admin);
}

if ($tipo == 'todos' || $tipo == 'dispositivos') {
    $dispositivos = obtenerDispositivos($link, $id_usuario, $es_admin);
}

if ($tipo == 'todos' || $tipo == 'pqrs') {
    $pqrs = obtenerPQRs($link, $id_usuario, $es_admin);
}

if (($tipo == 'todos' || $tipo == 'contactos') && $es_admin) {
    $contactos = obtenerContactos($link, $es_admin);
}

if ($tipo == 'todos' || $tipo == 'bodega') {
    $bodega = obtenerDispositivosBodega($link, $id_usuario, $es_admin);
}

if ($formato == 'pdf') {
    header('Content-Type: text/html; charset=utf-8');

    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Reporte de ' . ucfirst($tipo) . ' - ' . date('Y-m-d') . '</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                margin: 20px; 
                font-size: 14px;
            }
            @media print {
                body { margin: 0; }
                .no-print { display: none; }
            }
            table { 
                border-collapse: collapse; 
                width: 100%; 
                margin-top: 20px;
                margin-bottom: 30px;
            }
            th, td { 
                border: 1px solid #ddd; 
                padding: 8px; 
                text-align: left; 
                font-size: 12px;
            }
            th { 
                background-color: #f2f2f2; 
                color: #333;
                font-weight: bold;
            }
            h1, h2, h3 { 
                text-align: center; 
                color: #333;
            }
            .print-button { 
                background-color: #667eea; 
                color: white; 
                padding: 10px 20px; 
                border: none; 
                border-radius: 5px;
                cursor: pointer; 
                margin: 20px 0;
                font-weight: bold;
            }
            .print-button:hover {
                background-color: #764ba2;
            }
            .instructions {
                background-color: #f8f9fa;
                border: 1px solid #ddd;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .header-info {
                margin-bottom: 30px;
            }
            .logo {
                text-align: center;
                margin-bottom: 20px;
            }
            .page-footer {
                text-align: center;
                font-size: 12px;
                margin-top: 30px;
                color: #666;
            }
            .badge {
                display: inline-block;
                padding: 3px 7px;
                font-size: 12px;
                font-weight: bold;
                line-height: 1;
                color: #fff;
                text-align: center;
                white-space: nowrap;
                vertical-align: baseline;
                border-radius: 10px;
            }
            .badge-success {
                background-color: #28a745;
            }
            .badge-warning {
                background-color: #ffc107;
                color: #212529;
            }
            .badge-info {
                background-color: #17a2b8;
            }
            .badge-danger {
                background-color: #dc3545;
            }
            .section-title {
                background-color: #667eea;
                color: white;
                padding: 10px;
                border-radius: 5px;
                margin-top: 30px;
                margin-bottom: 15px;
            }
        </style>
    </head>
    <body>
        <div class="no-print">
            <button class="print-button" onclick="window.print()">Imprimir / Guardar como PDF</button>
            <div class="instructions">
                <p><strong>Instrucciones:</strong> Para guardar como PDF, haz clic en el botón "Imprimir / Guardar como PDF" y selecciona "Guardar como PDF" en las opciones de impresión de tu navegador.</p>
            </div>
        </div>
        
        <div class="header-info">
            <div class="logo">
                <h1>Sistema de Control de Stock</h1>
            </div>
            <h1>Reporte de ' . ucfirst($tipo) . '</h1>
            <h2>' . ($es_admin ? 'Administrador' : 'Usuario') . '</h2>
            <h3>Fecha de generación: ' . date('d/m/Y H:i:s') . '</h3>
        </div>';

    if ($tipo == 'todos' || $tipo == 'mantenimientos') {
        echo '<div class="section-title">
            <h2>Mantenimientos Programados</h2>
        </div>';

        if (!empty($mantenimientos)) {
            echo '<table>
                <thead>
                    <tr>';
            if ($es_admin) {
                echo '<th>ID</th>
                    <th>Usuario</th>
                    <th>Dispositivo</th>
                    <th>Tipo</th>
                    <th>Fecha Programada</th>
                    <th>Estado</th>';
            } else {
                echo '<th>ID</th>
                    <th>Dispositivo</th>
                    <th>Tipo</th>
                    <th>Fecha Programada</th>
                    <th>Estado</th>';
            }
            echo '</tr>
                </thead>
                <tbody>';

            foreach ($mantenimientos as $row) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                if ($es_admin) {
                    echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                }
                echo '<td>' . htmlspecialchars($row['marca'] . ' ' . $row['modelo']) . '</td>';
                echo '<td>' . htmlspecialchars($row['tipo_dispositivo']) . '</td>';
                echo '<td>' . date('d/m/Y', strtotime($row['fecha_programada'])) . '</td>';

                $badgeClass = '';
                if ($row['estado'] == 'completado') {
                    $badgeClass = 'badge-success';
                } elseif ($row['estado'] == 'en_proceso') {
                    $badgeClass = 'badge-warning';
                } else {
                    $badgeClass = 'badge-info';
                }

                echo '<td><span class="badge ' . $badgeClass . '">' . formatearEstado($row['estado']) . '</span></td>';
                echo '</tr>';
            }

            echo '</tbody>
            </table>';
        } else {
            echo '<p style="text-align: center;">No hay mantenimientos disponibles.</p>';
        }
    }

    if ($tipo == 'todos' || $tipo == 'dispositivos') {
        echo '<div class="section-title">
            <h2>Dispositivos Registrados</h2>
        </div>';

        if (!empty($dispositivos)) {
            echo '<table>
                <thead>
                    <tr>';
            if ($es_admin) {
                echo '<th>ID</th>
                    <th>Usuario</th>
                    <th>Tipo</th>
                    <th>Marca/Modelo</th>
                    <th>Fecha de Entrega</th>';
            } else {
                echo '<th>ID</th>
                    <th>Tipo</th>
                    <th>Marca/Modelo</th>
                    <th>Fecha de Entrega</th>';
            }
            echo '</tr>
                </thead>
                <tbody>';

            foreach ($dispositivos as $row) {
                echo '<tr>';
                echo '<td>' . $row['id_dispositivo'] . '</td>';
                if ($es_admin) {
                    echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                }
                echo '<td>' . htmlspecialchars($row['tipo']) . '</td>';
                echo '<td>' . htmlspecialchars($row['marca'] . ' ' . $row['modelo']) . '</td>';
                echo '<td>' . ($row['fecha_entrega'] ? date('d/m/Y', strtotime($row['fecha_entrega'])) : 'N/A') . '</td>';
                echo '</tr>';
            }

            echo '</tbody>
            </table>';
        } else {
            echo '<p style="text-align: center;">No hay dispositivos disponibles.</p>';
        }
    }

    if ($tipo == 'todos' || $tipo == 'pqrs') {
        echo '<div class="section-title">
            <h2>PQRs Registrados</h2>
        </div>';

        if (!empty($pqrs)) {
            echo '<table>
                <thead>
                    <tr>';
            if ($es_admin) {
                echo '<th>ID</th>
                    <th>Usuario</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Estado</th>';
            } else {
                echo '<th>ID</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Estado</th>';
            }
            echo '</tr>
                </thead>
                <tbody>';

            foreach ($pqrs as $row) {
                $descripcion = substr($row['descripcion'], 0, 50) . (strlen($row['descripcion']) > 50 ? '...' : '');

                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                if ($es_admin) {
                    echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                }
                echo '<td>' . htmlspecialchars($row['tipo']) . '</td>';
                echo '<td>' . htmlspecialchars($descripcion) . '</td>';
                echo '<td>' . date('d/m/Y', strtotime($row['fecha_creacion'])) . '</td>';

                $badgeClass = '';
                if ($row['estado'] == 'resuelto') {
                    $badgeClass = 'badge-success';
                } elseif ($row['estado'] == 'en_proceso') {
                    $badgeClass = 'badge-warning';
                } else {
                    $badgeClass = 'badge-info';
                }

                echo '<td><span class="badge ' . $badgeClass . '">' . formatearEstado($row['estado'] ? $row['estado'] : 'pendiente') . '</span></td>';
                echo '</tr>';
            }

            echo '</tbody>
            </table>';
        } else {
            echo '<p style="text-align: center;">No hay PQRs registrados.</p>';
        }
    }

    if (($tipo == 'todos' || $tipo == 'contactos') && $es_admin) {
        echo '<div class="section-title">
            <h2>Formularios de Contacto</h2>
        </div>';

        if (!empty($contactos)) {
            echo '<table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Asunto</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($contactos as $row) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td>' . htmlspecialchars($row['asunto']) . '</td>';
                echo '<td>' . date('d/m/Y', strtotime($row['fecha'])) . '</td>';

                $badgeClass = '';
                if ($row['estado'] == 'Resuelto') {
                    $badgeClass = 'badge-success';
                } elseif ($row['estado'] == 'En proceso') {
                    $badgeClass = 'badge-warning';
                } else {
                    $badgeClass = 'badge-info';
                }

                echo '<td><span class="badge ' . $badgeClass . '">' . formatearEstado($row['estado']) . '</span></td>';
                echo '</tr>';
            }

            echo '</tbody>
            </table>';
        } else {
            echo '<p style="text-align: center;">No hay formularios de contacto registrados.</p>';
        }
    }

    if ($tipo == 'todos' || $tipo == 'bodega') {
        echo '<div class="section-title">
            <h2>Dispositivos en Bodega</h2>
        </div>';

        if (!empty($bodega)) {
            echo '<table>
                <thead>
                    <tr>';
            if ($es_admin) {
                echo '<th>ID</th>
                    <th>Usuario</th>
                    <th>Tipo</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Estado</th>';
            } else {
                echo '<th>ID</th>
                    <th>Tipo</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Estado</th>';
            }
            echo '</tr>
                </thead>
                <tbody>';

            foreach ($bodega as $row) {
                echo '<tr>';
                echo '<td>' . $row['id_dispositivo'] . '</td>';
                if ($es_admin) {
                    echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                }
                echo '<td>' . htmlspecialchars($row['tipo']) . '</td>';
                echo '<td>' . htmlspecialchars($row['marca']) . '</td>';
                echo '<td>' . htmlspecialchars($row['modelo']) . '</td>';

                $badgeClass = '';
                if ($row['estado'] == 'Completado' || $row['estado'] == 'Activo') {
                    $badgeClass = 'badge-success';
                } elseif ($row['estado'] == 'En Reparación') {
                    $badgeClass = 'badge-warning';
                } else {
                    $badgeClass = 'badge-danger';
                }

                echo '<td><span class="badge ' . $badgeClass . '">' . formatearEstado($row['estado']) . '</span></td>';
                echo '</tr>';
            }

            echo '</tbody>
            </table>';
        } else {
            echo '<p style="text-align: center;">No hay dispositivos en Bodega.</p>';
        }
    }

    echo '<div class="page-footer">
        <p>Este reporte fue generado automáticamente por el Sistema de Control de Stock.</p>
        <p>© ' . date('Y') . ' - Todos los derechos reservados</p>
    </div>';

    echo '</body>
    </html>';
} else if ($formato == 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="reporte_' . $tipo . '.xls"');
    header('Cache-Control: max-age=0');

    echo '<!DOCTYPE html>';
    echo '<html>';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<title>Reporte de ' . ucfirst($tipo) . '</title>';
    echo '</head>';
    echo '<body>';

    $titulo = $es_admin ? 'Reporte de ' . ucfirst($tipo) . ' (Administrador)' : 'Mi Reporte de ' . ucfirst($tipo);
    echo '<h1>' . $titulo . '</h1>';
    echo '<p>Fecha: ' . date('Y-m-d H:i:s') . '</p>';

    if ($tipo == 'todos' || $tipo == 'mantenimientos') {
        echo '<h2>Mantenimientos Programados</h2>';

        if (!empty($mantenimientos)) {
            echo '<table border="1">';
            echo '<tr>';
            if ($es_admin) {
                echo '<th>ID</th>';
                echo '<th>Usuario</th>';
                echo '<th>Dispositivo</th>';
                echo '<th>Tipo</th>';
                echo '<th>Fecha Programada</th>';
                echo '<th>Estado</th>';
            } else {
                echo '<th>ID</th>';
                echo '<th>Dispositivo</th>';
                echo '<th>Tipo</th>';
                echo '<th>Fecha Programada</th>';
                echo '<th>Estado</th>';
            }
            echo '</tr>';

            foreach ($mantenimientos as $row) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                if ($es_admin) {
                    echo '<td>' . $row['username'] . '</td>';
                }
                echo '<td>' . $row['marca'] . ' ' . $row['modelo'] . '</td>';
                echo '<td>' . $row['tipo_dispositivo'] . '</td>';
                echo '<td>' . $row['fecha_programada'] . '</td>';
                echo '<td>' . formatearEstado($row['estado']) . '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p>No hay mantenimientos disponibles.</p>';
        }

        echo '<br><br>';
    }

    if ($tipo == 'todos' || $tipo == 'dispositivos') {
        echo '<h2>Dispositivos Registrados</h2>';

        if (!empty($dispositivos)) {
            echo '<table border="1">';
            echo '<tr>';
            if ($es_admin) {
                echo '<th>ID</th>';
                echo '<th>Usuario</th>';
                echo '<th>Tipo</th>';
                echo '<th>Marca/Modelo</th>';
                echo '<th>Fecha de Entrega</th>';
            } else {
                echo '<th>ID</th>';
                echo '<th>Tipo</th>';
                echo '<th>Marca/Modelo</th>';
                echo '<th>Fecha de Entrega</th>';
            }
            echo '</tr>';

            foreach ($dispositivos as $row) {
                echo '<tr>';
                echo '<td>' . $row['id_dispositivo'] . '</td>';
                if ($es_admin) {
                    echo '<td>' . $row['username'] . '</td>';
                }
                echo '<td>' . $row['tipo'] . '</td>';
                echo '<td>' . $row['marca'] . ' ' . $row['modelo'] . '</td>';
                echo '<td>' . $row['fecha_entrega'] . '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p>No hay dispositivos disponibles.</p>';
        }

        echo '<br><br>';
    }

    if ($tipo == 'todos' || $tipo == 'pqrs') {
        echo '<h2>PQRs Registrados</h2>';

        if (!empty($pqrs)) {
            echo '<table border="1">';
            echo '<tr>';
            if ($es_admin) {
                echo '<th>ID</th>';
                echo '<th>Usuario</th>';
                echo '<th>Tipo</th>';
                echo '<th>Descripción</th>';
                echo '<th>Fecha</th>';
                echo '<th>Estado</th>';
            } else {
                echo '<th>ID</th>';
                echo '<th>Tipo</th>';
                echo '<th>Descripción</th>';
                echo '<th>Fecha</th>';
                echo '<th>Estado</th>';
            }
            echo '</tr>';

            foreach ($pqrs as $row) {
                $descripcion = substr($row['descripcion'], 0, 50) . (strlen($row['descripcion']) > 50 ? '...' : '');

                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                if ($es_admin) {
                    echo '<td>' . $row['username'] . '</td>';
                }
                echo '<td>' . $row['tipo'] . '</td>';
                echo '<td>' . $descripcion . '</td>';
                echo '<td>' . $row['fecha_creacion'] . '</td>';
                echo '<td>' . formatearEstado($row['estado'] ? $row['estado'] : 'pendiente') . '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p>No hay PQRs registrados.</p>';
        }

        echo '<br><br>';
    }

    if (($tipo == 'todos' || $tipo == 'contactos') && $es_admin) {
        echo '<h2>Formularios de Contacto</h2>';

        if (!empty($contactos)) {
            echo '<table border="1">';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>Nombre</th>';
            echo '<th>Email</th>';
            echo '<th>Asunto</th>';
            echo '<th>Fecha</th>';
            echo '<th>Estado</th>';
            echo '</tr>';

            foreach ($contactos as $row) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . $row['nombre'] . '</td>';
                echo '<td>' . $row['email'] . '</td>';
                echo '<td>' . $row['asunto'] . '</td>';
                echo '<td>' . $row['fecha'] . '</td>';
                echo '<td>' . formatearEstado($row['estado']) . '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p>No hay formularios de contacto registrados.</p>';
        }

        echo '<br><br>';
    }

    if ($tipo == 'todos' || $tipo == 'bodega') {
        echo '<h2>Dispositivos en Bodega</h2>';

        if (!empty($bodega)) {
            echo '<table border="1">';
            echo '<tr>';
            if ($es_admin) {
                echo '<th>ID</th>';
                echo '<th>Usuario</th>';
                echo '<th>Tipo</th>';
                echo '<th>Marca</th>';
                echo '<th>Modelo</th>';
                echo '<th>Estado</th>';
            } else {
                echo '<th>ID</th>';
                echo '<th>Tipo</th>';
                echo '<th>Marca</th>';
                echo '<th>Modelo</th>';
                echo '<th>Estado</th>';
            }
            echo '</tr>';

            foreach ($bodega as $row) {
                echo '<tr>';
                echo '<td>' . $row['id_dispositivo'] . '</td>';
                if ($es_admin) {
                    echo '<td>' . $row['username'] . '</td>';
                }
                echo '<td>' . $row['tipo'] . '</td>';
                echo '<td>' . $row['marca'] . '</td>';
                echo '<td>' . $row['modelo'] . '</td>';
                echo '<td>' . formatearEstado($row['estado']) . '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p>No hay dispositivos en Bodega.</p>';
        }
    }

    echo '</body>';
    echo '</html>';
}

mysqli_close($link);
?>