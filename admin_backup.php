<?php
session_start();
require_once "includes/config.php";
require_once "includes/audit_functions.php";
require_once "includes/backup_functions.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

registrar_acceso_modulo($_SESSION["id"], "Backup");

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["accion"])) {
        $accion = $_POST["accion"];

        if ($accion === "crear_backup") {
            $resultado = crear_backup_bd();

            if ($resultado["exito"]) {
                $mensaje = $resultado["mensaje"];
                $tipo_mensaje = 'success';

                registrar_auditoria($_SESSION["id"], "Creación de backup", "backups", null, "Nombre: " . $resultado["archivo"]);
            } else {
                $mensaje = $resultado["mensaje"];
                $tipo_mensaje = 'danger';
            }
        } elseif ($accion === "restaurar_backup" && isset($_POST["id_backup"])) {
            $id_backup = $_POST["id_backup"];

            $sql = "SELECT * FROM backups WHERE id = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id_backup);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $resultado = restaurar_backup_bd($row["ruta"]);

                if ($resultado["exito"]) {
                    $mensaje = $resultado["mensaje"];
                    $tipo_mensaje = 'success';

                    registrar_auditoria($_SESSION["id"], "Restauración de backup", "backups", $id_backup, "Nombre: " . $row["nombre"]);
                } else {
                    $mensaje = $resultado["mensaje"];
                    $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = "Backup no encontrado";
                $tipo_mensaje = 'danger';
            }
        } elseif ($accion === "eliminar_backup" && isset($_POST["id_backup"])) {
            $id_backup = $_POST["id_backup"];

            $sql = "SELECT * FROM backups WHERE id = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id_backup);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            $resultado = eliminar_backup($id_backup);

            if ($resultado["exito"]) {
                $mensaje = $resultado["mensaje"];
                $tipo_mensaje = 'success';

                registrar_auditoria($_SESSION["id"], "Eliminación de backup", "backups", $id_backup, "Nombre: " . $row["nombre"]);
            } else {
                $mensaje = $resultado["mensaje"];
                $tipo_mensaje = 'danger';
            }
        } elseif ($accion === "programar_backup") {
            $frecuencia = $_POST["frecuencia"];

            $resultado = programar_backup_automatico($frecuencia);

            if ($resultado["exito"]) {
                $mensaje = $resultado["mensaje"];
                $tipo_mensaje = 'success';

                registrar_auditoria($_SESSION["id"], "Programación de backup automático", "configuracion", null, "Frecuencia: " . $frecuencia);
            } else {
                $mensaje = $resultado["mensaje"];
                $tipo_mensaje = 'danger';
            }
        }
    }
}

$backups = obtener_backups();

$frecuencia_backup = obtener_configuracion_backup_automatico();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Backup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-bottom: 40px;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin-bottom: 30px;
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }

        .card-body {
            padding: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .btn-success {
            background: linear-gradient(135deg, #20bf6b 0%, #0b8a45 100%);
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #0b8a45 0%, #20bf6b 100%);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%);
            border: none;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #ee5253 0%, #ff6b6b 100%);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
        }

        .backup-info {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .backup-size {
            font-weight: bold;
            color: #764ba2;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-database me-2"></i>Sistema de Backup</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <?php if ($tipo_mensaje === 'success'): ?>
                    <i class="fas fa-check-circle me-2"></i>
                <?php else: ?>
                    <i class="fas fa-exclamation-circle me-2"></i>
                <?php endif; ?>
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Crear Backup</h5>
                    </div>
                    <div class="card-body">
                        <p>Crea un backup completo de la base de datos. Este proceso puede tardar unos minutos
                            dependiendo del tamaño de la base de datos.</p>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="accion" value="crear_backup">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-database me-2"></i>Crear Backup Ahora
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Programar Backup Automático</h5>
                    </div>
                    <div class="card-body">
                        <p>Configura la frecuencia con la que se realizarán backups automáticos de la base de datos.</p>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="accion" value="programar_backup">
                            <div class="mb-3">
                                <label for="frecuencia" class="form-label">Frecuencia</label>
                                <select class="form-select" id="frecuencia" name="frecuencia">
                                    <option value="diario" <?php echo ($frecuencia_backup === 'diario') ? 'selected' : ''; ?>>Diario</option>
                                    <option value="semanal" <?php echo ($frecuencia_backup === 'semanal') ? 'selected' : ''; ?>>Semanal</option>
                                    <option value="mensual" <?php echo ($frecuencia_backup === 'mensual') ? 'selected' : ''; ?>>Mensual</option>
                                    <option value="ninguno" <?php echo ($frecuencia_backup === 'ninguno' || $frecuencia_backup === null) ? 'selected' : ''; ?>>Ninguno</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Configuración
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Backups</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($backups) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Fecha</th>
                                            <th>Tamaño</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($backups as $backup): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($backup['nombre']); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($backup['fecha'])); ?></td>
                                                <td class="backup-size"><?php echo formatear_tamanio($backup['tamanio']); ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="descargar_backup.php?id=<?php echo $backup['id']; ?>"
                                                            class="btn btn-sm btn-primary" title="Descargar">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#restaurarModal<?php echo $backup['id']; ?>"
                                                            title="Restaurar">
                                                            <i class="fas fa-undo-alt"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#eliminarModal<?php echo $backup['id']; ?>"
                                                            title="Eliminar">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>

                                                    <div class="modal fade" id="restaurarModal<?php echo $backup['id']; ?>"
                                                        tabindex="-1"
                                                        aria-labelledby="restaurarModalLabel<?php echo $backup['id']; ?>"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="restaurarModalLabel<?php echo $backup['id']; ?>">
                                                                        Confirmar Restauración</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p><strong>¡Advertencia!</strong> Estás a punto de restaurar
                                                                        la base de datos con el backup
                                                                        <strong><?php echo htmlspecialchars($backup['nombre']); ?></strong>
                                                                        del
                                                                        <strong><?php echo date('d/m/Y H:i', strtotime($backup['fecha'])); ?></strong>.
                                                                    </p>
                                                                    <p>Esta acción sobrescribirá todos los datos actuales.
                                                                        ¿Estás seguro de que deseas continuar?</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Cancelar</button>
                                                                    <form method="post"
                                                                        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                                                        <input type="hidden" name="accion"
                                                                            value="restaurar_backup">
                                                                        <input type="hidden" name="id_backup"
                                                                            value="<?php echo $backup['id']; ?>">
                                                                        <button type="submit"
                                                                            class="btn btn-success">Restaurar</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="eliminarModal<?php echo $backup['id']; ?>"
                                                        tabindex="-1"
                                                        aria-labelledby="eliminarModalLabel<?php echo $backup['id']; ?>"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="eliminarModalLabel<?php echo $backup['id']; ?>">
                                                                        Confirmar Eliminación</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>¿Estás seguro de que deseas eliminar el backup
                                                                        <strong><?php echo htmlspecialchars($backup['nombre']); ?></strong>?
                                                                    </p>
                                                                    <p>Esta acción no se puede deshacer.</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Cancelar</button>
                                                                    <form method="post"
                                                                        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                                                        <input type="hidden" name="accion"
                                                                            value="eliminar_backup">
                                                                        <input type="hidden" name="id_backup"
                                                                            value="<?php echo $backup['id']; ?>">
                                                                        <button type="submit"
                                                                            class="btn btn-danger">Eliminar</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No hay backups disponibles. Crea tu primer backup
                                haciendo clic en el botón "Crear Backup Ahora".
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
/**
 * Formatea el tamaño en bytes a una unidad legible
 * 
 * @param int $bytes Tamaño en bytes
 * @return string Tamaño formateado
 */
function formatear_tamanio($bytes)
{
    $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;

    while ($bytes >= 1024 && $i < count($unidades) - 1) {
        $bytes /= 1024;
        $i++;
    }

    return round($bytes, 2) . ' ' . $unidades[$i];
}
?>