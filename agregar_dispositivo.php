<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$tipo = $marca = $modelo = $fecha_entrega = $licencias = $procesador = $almacenamiento = $ram = $serial = "";
$tipo_err = $marca_err = $modelo_err = $fecha_entrega_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["tipo"]))) {
        $tipo_err = "Por favor seleccione un tipo de dispositivo.";
    } else {
        $tipo = trim($_POST["tipo"]);
    }

    if (empty(trim($_POST["marca"]))) {
        $marca_err = "Por favor ingrese la marca del dispositivo.";
    } else {
        $marca = trim($_POST["marca"]);
    }

    if (empty(trim($_POST["modelo"]))) {
        $modelo_err = "Por favor ingrese el modelo del dispositivo.";
    } else {
        $modelo = trim($_POST["modelo"]);
    }

    if (empty(trim($_POST["fecha_entrega"]))) {
        $fecha_entrega_err = "Por favor ingrese la fecha de entrega.";
    } else {
        $fecha_entrega = trim($_POST["fecha_entrega"]);
    }

    $licencias = trim($_POST["licencias"]);
    $procesador = trim($_POST["procesador"]);
    $almacenamiento = trim($_POST["almacenamiento"]);
    $ram = trim($_POST["ram"]);
    $serial = trim($_POST["serial"]);

    if (empty($tipo_err) && empty($marca_err) && empty($modelo_err) && empty($fecha_entrega_err)) {
        $sql = "INSERT INTO dispositivos (id_usuario, tipo, marca, modelo, fecha_entrega, licencias, procesador, almacenamiento, ram, serial) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "isssssssss", $param_id_usuario, $param_tipo, $param_marca, $param_modelo, $param_fecha_entrega, $param_licencias, $param_procesador, $param_almacenamiento, $param_ram, $param_serial);

            $param_id_usuario = $_SESSION["id"];
            $param_tipo = $tipo;
            $param_marca = $marca;
            $param_modelo = $modelo;
            $param_fecha_entrega = $fecha_entrega;
            $param_licencias = $licencias;
            $param_procesador = $procesador;
            $param_almacenamiento = $almacenamiento;
            $param_ram = $ram;
            $param_serial = $serial;

            if (mysqli_stmt_execute($stmt)) {
                header("location: dispositivos.php?tipo=" . $tipo);
                exit();
            } else {
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Dispositivo</title>
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

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
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
            <a class="navbar-brand" href="#"><i class="fas fa-laptop me-2"></i>Control de Stock</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
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

    <div class="container mt-5">
        <div class="wrapper">
            <h2 class="mb-4"><i class="fas fa-plus-circle me-2"></i>Agregar Dispositivo</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="tipo">Tipo de Dispositivo</label>
                    <select name="tipo" id="tipo"
                        class="form-control <?php echo (!empty($tipo_err)) ? 'is-invalid' : ''; ?>">
                        <option value="">Seleccione un tipo</option>
                        <option value="Computadora" <?php echo ($tipo == "Computadora") ? "selected" : ""; ?>>Computadora
                        </option>
                        <option value="Tablet" <?php echo ($tipo == "Tablet") ? "selected" : ""; ?>>Tablet</option>
                        <option value="Celular" <?php echo ($tipo == "Celular") ? "selected" : ""; ?>>Celular</option>
                    </select>
                    <span class="invalid-feedback"><?php echo $tipo_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="marca">Marca</label>
                    <input type="text" name="marca" id="marca"
                        class="form-control <?php echo (!empty($marca_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $marca; ?>">
                    <span class="invalid-feedback"><?php echo $marca_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="modelo">Modelo</label>
                    <input type="text" name="modelo" id="modelo"
                        class="form-control <?php echo (!empty($modelo_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $modelo; ?>">
                    <span class="invalid-feedback"><?php echo $modelo_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="fecha_entrega">Fecha de Entrega</label>
                    <input type="date" name="fecha_entrega" id="fecha_entrega"
                        class="form-control <?php echo (!empty($fecha_entrega_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $fecha_entrega; ?>">
                    <span class="invalid-feedback"><?php echo $fecha_entrega_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="licencias">Licencias</label>
                    <input type="text" name="licencias" id="licencias" class="form-control"
                        value="<?php echo $licencias; ?>">
                </div>
                <div class="form-group">
                    <label for="procesador">Procesador</label>
                    <input type="text" name="procesador" id="procesador" class="form-control"
                        value="<?php echo $procesador; ?>">
                </div>
                <div class="form-group">
                    <label for="almacenamiento">Almacenamiento</label>
                    <input type="text" name="almacenamiento" id="almacenamiento" class="form-control"
                        value="<?php echo $almacenamiento; ?>">
                </div>
                <div class="form-group">
                    <label for="ram">Memoria RAM</label>
                    <input type="text" name="ram" id="ram" class="form-control" value="<?php echo $ram; ?>">
                </div>
                <div class="form-group">
                    <label for="serial">Número de Serie</label>
                    <input type="text" name="serial" id="serial" class="form-control" value="<?php echo $serial; ?>">
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Agregar Dispositivo">
                    <a class="btn btn-secondary" href="dashboard.php">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>