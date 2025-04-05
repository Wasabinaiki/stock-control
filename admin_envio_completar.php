<?php
session_start();
require_once "includes/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "administrador") {
    header("location: login.php");
    exit;
}

if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $sql = "UPDATE envios SET estado_envio = 'Completado', fecha_llegada = CURDATE() WHERE id_envio = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = trim($_POST["id"]);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "El envío ha sido marcado como Completado.";
            header("location: admin_envios.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error al actualizar el estado del envío: " . mysqli_error($link);
            header("location: admin_envios.php");
            exit();
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
    if (empty(trim($_GET["id"]))) {
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completar Envío</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }

        .wrapper {
            width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #764ba2;
        }

        .btn-success {
            background: linear-gradient(135deg, #20bf6b 0%, #0b8a45 100%);
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #0b8a45 0%, #20bf6b 100%);
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5 mb-3"><i class="fas fa-check-circle me-2"></i>Completar Envío</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-info">
                            <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>" />
                            <p>¿Está seguro que desea marcar este envío como completado?</p>
                            <p>Se registrará la fecha actual como fecha de llegada.</p>
                            <p>
                                <input type="submit" value="Sí, completar" class="btn btn-success">
                                <a href="admin_envios.php" class="btn btn-secondary">Cancelar</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>