<?php
require_once "includes/config.php";

$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Por favor ingrese un nombre de usuario.";
    } else {
        $sql = "SELECT id_usuario FROM usuarios WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            $param_username = trim($_POST["username"]);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "Este nombre de usuario ya está en uso.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor ingrese una contraseña.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Por favor confirme la contraseña.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Las contraseñas no coinciden.";
        }
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor ingrese un correo electrónico.";
    } else {
        $sql = "SELECT id_usuario FROM usuarios WHERE email = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            $param_email = trim($_POST["email"]);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "Este correo electrónico ya está registrado.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)) {

        $sql = "INSERT INTO usuarios (username, password, email, rol) VALUES (?, ?, ?, 'usuario')";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_email);

            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_email = $email;

            if (mysqli_stmt_execute($stmt)) {
                header("location: login.php");
            } else {
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #ff9f43, #1e90ff, #667eea, #764ba2);
            background-size: 400% 400%;
            animation: gradientAnimation 10s ease infinite;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            min-height: 650px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 20px;
        }

        .form-container {
            flex: 1;
            padding: 40px;
            background-color: #ffffff;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
            text-align: center;
            display: flex;
            flex-direction: column;
        }

        .info-container {
            flex: 1;
            padding: 40px;
            background-color: rgb(55, 19, 85);
            color: white;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
            text-align: center;
            display: flex;
            flex-direction: column;
        }

        .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo img {
            width: 150px;
            height: 150px;
            filter: drop-shadow(0 0 10px rgba(0, 0, 0, 0.5));
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            font-size: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            position: relative;
            margin-bottom: 30px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        .form-control {
            padding-left: 40px;
            padding-right: 40px;
            height: 50px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 100%;
        }

        .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: rgb(87, 27, 227);
        }

        .eye-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #764ba2;
        }

        .eye-icon i {
            font-size: 18px;
        }

        .invalid-feedback {
            position: absolute;
            bottom: -20px;
            left: 0;
            font-size: 14px;
            color: red;
            min-height: 20px;
        }

        .form-container form {
            max-width: 550px;
            margin: 0 auto;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .form-group+.form-group {
            margin-top: 20px;
        }

        .links {
            margin-top: 20px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                min-height: auto;
            }

            .form-container,
            .info-container {
                border-radius: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h2><i class="fas fa-user-plus me-2" style="margin-bottom: 8%; margin-top: 2%;"></i>Registro</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <input type="text" name="username"
                        class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $username; ?>" placeholder="Nombre de Usuario">
                    <i class="fas fa-user icon"></i>
                    <div class="invalid-feedback"><?php echo $username_err; ?></div>
                </div>
                <div class="form-group position-relative">
                    <input type="password" name="password"
                        class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>"
                        placeholder="Contraseña">
                    <i class="fas fa-lock icon"></i>
                    <span class="eye-icon show-password" onclick="togglePasswordVisibility(this)">
                        <i class="fas fa-eye-slash"></i>
                    </span>
                    <div class="invalid-feedback"><?php echo $password_err; ?></div>
                </div>
                <div class="form-group position-relative">
                    <input type="password" name="confirm_password"
                        class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>"
                        placeholder="Confirmar Contraseña">
                    <i class="fas fa-lock icon"></i>
                    <span class="eye-icon show-password" onclick="togglePasswordVisibility(this)">
                        <i class="fas fa-eye-slash"></i>
                    </span>
                    <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
                </div>
                <div class="form-group">
                    <input type="email" name="email"
                        class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $email; ?>" placeholder="Correo Electrónico">
                    <i class="fas fa-envelope icon"></i>
                    <div class="invalid-feedback"><?php echo $email_err; ?></div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Registrarse
                    </button>
                </div>
                <div class="links">
                    <a href="login.php">¿Ya tienes una cuenta? Inicia sesión aquí</a>
                </div>
            </form>
        </div>

        <div class="info-container">
            <div class="logo">
                <img src="Stock Control Logo Curved.png" alt="Logo StockControl">
            </div>
            <h1 style="margin-bottom: 5%;">Bienvenido a Stock Control</h1>
            <p style="margin-bottom: 6%;">Gestiona fácilmente tus equipos de manera rápida y eficaz con la disposición
                de métricas y analíticas a tu disposición.</p>
            <p style="font-size: 140%;">¡Optimiza tu control de dispositivos hoy mismo!</p>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(icon) {
            const passwordInput = icon.closest('.form-group').querySelector('input[type="password"], input[type="text"]');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.innerHTML = '<i class="fas fa-eye"></i>';
            } else {
                passwordInput.type = 'password';
                icon.innerHTML = '<i class="fas fa-eye-slash"></i>';
            }
        }
    </script>
</body>

</html>