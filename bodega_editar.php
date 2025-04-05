<?php
// bodega_editar.php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

$id = $tipo = $marca = $modelo = $estado = "";
$id_err = "";

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = trim($_GET["id"]);

    $sql = "SELECT id_dispositivo, tipo, marca, modelo, estado FROM dispositivos WHERE id_dispositivo = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $id;

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $tipo = $row["tipo"];
                $marca = $row["marca"];
                $modelo = $row["modelo"];
                $estado = $row["estado"];
            } else {
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
        }
    }
    mysqli_stmt_close($stmt);
} else {
    header("location: error.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["id"]))) {
        $id_err = "Por favor ingrese un ID.";
    } else {
        $id = trim($_POST["id"]);
    }

    if (empty($id_err)) {
        // Solo actualizamos los campos que se muestran en la bodega
        $sql = "UPDATE dispositivos SET tipo=?, marca=?, modelo=?, estado=? WHERE id_dispositivo=?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssi", $param_tipo, $param_marca, $param_modelo, $param_estado, $param_id);

            $param_tipo = $_POST["tipo"];
            $param_marca = $_POST["marca"];
            $param_modelo = $_POST["modelo"];
            $param_estado = $_POST["estado"];
            $param_id = $id;

            if (mysqli_stmt_execute($stmt)) {
                // Redirigir de vuelta a la página de bodega con un mensaje de éxito
                $_SESSION["success_message"] = "Dispositivo actualizado exitosamente.";
                header("location: bodega.php");
                exit();
            } else {
                $_SESSION["error_message"] = "Error al actualizar el dispositivo: " . mysqli_error($link);
                header("location: bodega.php");
                exit();
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
    <title>Editar Dispositivo en Bodega</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #764ba2;
            margin-bottom: 20px;
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

        .btn-default {
            background-color: #6c757d;
            color: white;
            margin-left: 10px;
        }

        .btn-default:hover {
            background-color: #5a6268;
            color: white;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2><i class="fas fa-edit me-2"></i>Editar Dispositivo en Bodega</h2>
        <p>Por favor edite los valores del dispositivo.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post">
            <div class="form-group">
                <label>ID del Dispositivo</label>
                <input type="text" class="form-control" value="<?php echo $id; ?>" disabled>
            </div>
            <div class="form-group">
                <label>Tipo</label>
                <input type="text" name="tipo" class="form-control" value="<?php echo $tipo; ?>">
            </div>
            <div class="form-group">
                <label>Marca</label>
                <input type="text" name="marca" class="form-control" value="<?php echo $marca; ?>">
            </div>
            <div class="form-group">
                <label>Modelo</label>
                <input type="text" name="modelo" class="form-control" value="<?php echo $modelo; ?>">
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="Activo" <?php echo ($estado == "Activo") ? "selected" : ""; ?>>Activo</option>
                    <option value="En Reparación" <?php echo ($estado == "En Reparación") ? "selected" : ""; ?>>En
                        Reparación</option>
                    <option value="Inactivo" <?php echo ($estado == "Inactivo") ? "selected" : ""; ?>>Inactivo</option>
                    <option value="Completado" <?php echo ($estado == "Completado") ? "selected" : ""; ?>>Completado
                    </option>
                </select>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Actualizar">
                <a href="bodega.php" class="btn btn-default">Cancelar</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>