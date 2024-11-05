<?php
require_once "includes/config.php";

$nombre_completo = $email = $password = $confirm_password = $telefono = $direccion = "";
$nombre_completo_err = $email_err = $password_err = $confirm_password_err = $telefono_err = $direccion_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validar nombre completo
    if(empty(trim($_POST["nombre_completo"]))){
        $nombre_completo_err = "Por favor ingrese su nombre completo.";
    } else{
        $nombre_completo = trim($_POST["nombre_completo"]);
    }
    
    // Validar email
    if(empty(trim($_POST["email"]))){
        $email_err = "Por favor ingrese un email.";
    } else{
        $sql = "SELECT id_usuario FROM Usuarios WHERE email = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            $param_email = trim($_POST["email"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "Este email ya está registrado.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Algo salió mal. Por favor intente más tarde.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    // Validar contraseña
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor ingrese una contraseña.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "La contraseña debe tener al menos 6 caracteres.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validar confirmación de contraseña
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Por favor confirme la contraseña.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Las contraseñas no coinciden.";
        }
    }
    
    // Validar teléfono
    if(empty(trim($_POST["telefono"]))){
        $telefono_err = "Por favor ingrese un número de teléfono.";     
    } else{
        $telefono = trim($_POST["telefono"]);
    }
    
    // Validar dirección
    if(empty(trim($_POST["direccion"]))){
        $direccion_err = "Por favor ingrese una dirección.";     
    } else{
        $direccion = trim($_POST["direccion"]);
    }
    
    // Verificar los errores de entrada antes de insertar en la base de datos
    if(empty($nombre_completo_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($telefono_err) && empty($direccion_err)){
        
        $sql = "INSERT INTO Usuarios (nombre_completo, email, password, telefono, direccion, rol) VALUES (?, ?, ?, ?, ?, 'cliente')";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "sssss", $param_nombre_completo, $param_email, $param_password, $param_telefono, $param_direccion);
            
            $param_nombre_completo = $nombre_completo;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Crea un hash de la contraseña
            $param_telefono = $telefono;
            $param_direccion = $direccion;
            
            if(mysqli_stmt_execute($stmt)){
                header("location: login.php");
            } else{
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Registro</h2>
        <p>Por favor complete este formulario para crear una cuenta.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre_completo" class="form-control <?php echo (!empty($nombre_completo_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nombre_completo; ?>">
                <span class="invalid-feedback"><?php echo $nombre_completo_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirmar Contraseña</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Teléfono</label>
                <input type="tel" name="telefono" class="form-control <?php echo (!empty($telefono_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $telefono; ?>">
                <span class="invalid-feedback"><?php echo $telefono_err; ?></span>
            </div>
            <div class="form-group">
                <label>Dirección</label>
                <textarea name="direccion" class="form-control <?php echo (!empty($direccion_err)) ? 'is-invalid' : ''; ?>"><?php echo $direccion; ?></textarea>
                <span class="invalid-feedback"><?php echo $direccion_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Registrarse">
                <input type="reset" class="btn btn-secondary ml-2" value="Reiniciar">
            </div>
            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
        </form>
    </div>    
</body>
</html>