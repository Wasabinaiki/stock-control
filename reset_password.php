<?php
session_start();
require_once "includes/config.php";

$email = $new_password = $confirm_password = "";
$email_err = $new_password_err = $confirm_password_err = "";
$success_message = $error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
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
            min-height: 58vh;
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
            width: 110%;
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
            max-width: 450px;
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
            <h2><i class="fas fa-lock me-2" style="margin-bottom: 6%; margin-top: 4%;"></i>Restablecer Contraseña</h2>

            <?php
            if (!empty($success_message)) {
                echo '<div class="alert alert-success">' . $success_message . '</div>';
            }
            if (!empty($error_message)) {
                echo '<div class="alert alert-danger">' . $error_message . '</div>';
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <input type="email" name="email"
                        class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $email; ?>" placeholder="Correo Electrónico">
                    <i class="fas fa-envelope icon"></i>
                    <div class="invalid-feedback"><?php echo $email_err; ?></div>
                </div>
                <div class="form-group position-relative">
                    <input type="password" name="new_password"
                        class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>"
                        placeholder="Nueva Contraseña">
                    <i class="fas fa-lock icon"></i>
                    <span class="eye-icon show-password" onclick="togglePasswordVisibility(this)">
                        <i class="fas fa-eye-slash"></i>
                    </span>
                    <div class="invalid-feedback"><?php echo $new_password_err; ?></div>
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
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Restablecer Contraseña
                    </button>
                </div>
                <p class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none">Volver al inicio de sesión</a>
                </p>
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