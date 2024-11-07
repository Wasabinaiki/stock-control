<?php
session_start();
require_once "includes/config.php";

$email = $new_password = $confirm_password = "";
$email_err = $new_password_err = $confirm_password_err = "";
$success_message = $error_message = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validar email
    if(empty(trim($_POST["email"]))){
        $email_err = "Por favor ingrese su email.";
    } else{
        $sql = "SELECT id_usuario FROM usuarios WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email = trim($_POST["email"]);
                } else{
                    $email_err = "No existe una cuenta con ese email.";
                }
            } else{
                $error_message = "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    // Validar contraseña
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Por favor ingrese la nueva contraseña.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "La contraseña debe tener al menos 6 caracteres.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validar confirmación de contraseña
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Por favor confirme la contraseña.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Las contraseñas no coinciden.";
        }
    }
    
    // Verificar errores antes de actualizar
    if(empty($email_err) && empty($new_password_err) && empty($confirm_password_err)){
        $sql = "UPDATE usuarios SET password = ? WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $param_password, $param_email);
            
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_email = $email;
            
            if(mysqli_stmt_execute($stmt)){
                $success_message = "Contraseña actualizada exitosamente.";
                unset($_SESSION['login_attempts']);
            } else{
                $error_message = "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
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
    <title>Restablecer Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .wrapper {
            width: 380px;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .wrapper h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        .form-control {
            height: 50px;
            padding-left: 45px;
            font-size: 16px;
            border: none;
            border-bottom: 2px solid #ddd;
            background: transparent;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #764ba2;
            box-shadow: none;
        }
        .form-group i {
            position: absolute;
            left: 15px;
            top: 17px;
            color: #764ba2;
            font-size: 18px;
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
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2><i class="fas fa-lock me-2"></i>Restablecer Contraseña</h2>
        
        <?php 
        if(!empty($success_message)){
            echo '<div class="alert alert-success">' . $success_message . '</div>';
        }
        if(!empty($error_message)){
            echo '<div class="alert alert-danger">' . $error_message . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" placeholder="Email">
                <i class="fas fa-envelope"></i>
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>" placeholder="Nueva contraseña">
                <i class="fas fa-lock"></i>
                <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" placeholder="Confirmar contraseña">
                <i class="fas fa-lock"></i>
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
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
</body>
</html>