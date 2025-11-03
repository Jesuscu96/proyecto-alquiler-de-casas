<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: ./admin/index.php");
    exit();
}

include("./includes/sessions.php");

$sesion = new Sesion();
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['username'] ?? '');
    $clave = trim($_POST['password'] ?? '');
    
    // Validar campos vacíos
    if (empty($usuario) || empty($clave)) {
        $error = "El usuario y la contraseña son obligatorios.";
    } else {
        $datos = $sesion->comprobarCredenciales($usuario, $clave);
        
        if ($datos) {
            $sesion->crearSesion($datos);
            header("Location: ./admin/index.php");
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Casas - Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./styles.css">
    <style>
        :root {
            --primary: #0b5482;
            --secondary: #4fd1c5;
            --tertiary: #501563;
            --accent: #ffd166;
            --dark: #072d4b;
            --light: #e9f7f6;
            --danger: #f14b4b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 2rem 1.5rem;
            text-align: center;
        }

        .login-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: 1px;
        }

        .login-header p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .login-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(79, 209, 197, 0.15);
            background-color: #f8fffe;
        }

        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            color: var(--primary);
        }

        .input-group-text:hover {
            background: #e9ecef;
        }

        .login-btn {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.85rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1rem;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 84, 130, 0.3);
            color: white;
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .alert {
            border: none;
            border-radius: 8px;
            border-left: 4px solid;
            margin-bottom: 1.5rem;
            animation: slideIn 0.4s ease;
        }

        .alert-danger {
            background-color: #fff5f5;
            border-left-color: var(--danger);
            color: #721c24;
        }

        .alert-success {
            background-color: #f0fdf4;
            border-left-color: #22c55e;
            color: #155724;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .form-check-input {
            width: 1.1em;
            height: 1.1em;
            border: 2px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }

        .form-check-input:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.25rem rgba(79, 209, 197, 0.25);
        }

        .form-check-label {
            margin-left: 0.5rem;
            cursor: pointer;
            color: var(--dark);
            user-select: none;
        }

        .login-footer {
            text-align: center;
            padding: 1.5rem;
            border-top: 1px solid #e9ecef;
            font-size: 0.85rem;
            color: #6c757d;
        }

        .icon-input {
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                padding: 15px;
            }

            .login-header {
                padding: 1.5rem;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }

            .login-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="bi bi-house-door-fill"></i> CasasApp</h1>
                <p>Sistema de Gestión de Casas</p>
            </div>

            <div class="login-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="bi bi-person-fill icon-input"></i> Usuario
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="username" 
                            name="username" 
                            placeholder="Ingresa tu usuario"
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock-fill icon-input"></i> Contraseña
                        </label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            placeholder="Ingresa tu contraseña"
                        >
                    </div>

                    <div class="remember-me">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Recuérdame</label>
                    </div>

                    <button type="submit" class="login-btn">
                        <i class="bi bi-arrow-right-circle-fill"></i> Iniciar Sesión
                    </button>
                </form>
            </div>

            <div class="login-footer">
                <p>© 2025 CasasApp. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>