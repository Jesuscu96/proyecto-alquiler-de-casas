<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
require_once "./includes/sessions.php";
$sesion = new Sessions();

if (!$sesion->comprobarSesion()) {
    header("Location: ../login.php");
    exit();
}
//$usuarioActual = $sesion->getUsuarioActual();

require_once "./includes/crudUsuarios.php";
$usuarioObj = new Usuarios();
$usuarios = $usuarioObj->getAll();

$accion = $_GET['accion'] ?? null;
$id = $_GET['id'] ?? null;
$mensaje = "";
if($accion == "eliminar" && $id) {
    $usuarioObj->eliminarUsuario($id);
    $mensaje = "Usuario eliminado correctamente.";
}
$datos_usuario = ['nombre' => '',
                'apellidos' => '',
                'email' => '',
                'username' => '', 
                'password' => '']; //para que el value del formulario salga vaci­o

if ($accion === "editar" && $id) {
    $datos_usuario = $usuarioObj->getUsuarioById($id); 
}
// Procesar el formulario de creacion o edicion de categorÃ­a
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['passwordConfirm'] ?? '';
    $mensaje ="";
     if ($accion === "crear") {
        if ($password !== $passwordConfirm) {
            $mensaje = "Las contraseñas no coinciden.";
        } else {
            $usuarioObj->insertarUsuario($nombre, $apellidos, $email, $username, $password);
            header("Location: usuarios.php");
            exit();
        }
    } elseif ($accion === "editar" && $id) {
        // Actualización sin cambiar contraseña
        $usuarioObj->actualizarUsuario($id, $nombre, $apellidos, $email, $username);
        header("Location: usuarios.php");
        exit();
    } elseif ($accion === "editarPass" && $id) {
        // Validar contraseña antes de actualizar
        if ($password !== $passwordConfirm) {
            $mensaje = "Las contraseñas no coinciden.";
        } else {
            $usuarioObj->actualizarPassword($id, $password);
            header("Location: usuarios.php");
            exit();
        }
    } 
    
}
?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestion de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include("./menu.php"); ?>

    <!-- Contenedor principal -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <main class="col-md-10">
                <h2>Usuarios</h2>

                <a href="usuarios.php?accion=crear" class="btn btn-success mb-3">Añadir nuevo Usuario</a>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Email</th>
                            <th>Username</th>
                            <!-- <th>Contraseña</th> -->
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuarios as $usuario) : ?>
                        <tr>
                            <td><?=$usuario['nombre']?></td>
                            <td><?=$usuario['apellidos']?></td>
                            <td><?=$usuario['email']?></td>
                            <td><?=$usuario['username']?></td>
                            <!-- <td><?=$usuario['password']?></td -->
                            
                            <td>
                                <a href="usuarios.php?accion=editar&id=<?=$usuario['id']?>" class="btn btn-sm btn-primary">
                                    Editar
                                </a>
                                <a href="usuarios.php?accion=editarPass&id=<?=$usuario['id']?>" class="btn btn-sm btn-secondary">
                                    Editar contraseña
                                </a>
                                <?php if($_SESSION['usuario']['id'] !== (int)$usuario['id']) { ?>
                                    <a href="usuarios.php?accion=eliminar&id=<?=$usuario['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estas seguro?')">
                                        Eliminar
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <?php if ($accion === "editar" && $id): ?>
                    
                        <!-- TÃ­tulo dependiendo de si se estÃ¡ creando o editando -->
                        <h3><?= $accion === "crear" ? "Nuevo usuario" : "Editar usuario" ?></h3>
                        
                        <!-- Formulario para ingresar el nombre de la categorÃ­a -->
                        <form method="post" class="mb-4" style="max-width: 400px;">
                            <div class="mb-2">
                                <label class="form-label">Nombre:</label>
                                <input type="text" name="nombre" class="form-control"
                                value="<?= htmlspecialchars($datos_usuario['nombre']) ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Apellidos:</label>
                                <input type="text" name="apellidos" class="form-control"
                                value="<?=htmlspecialchars($datos_usuario['apellidos'])?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Email:</label>
                                <input type="text" name="email" class="form-control"
                                value="<?= htmlspecialchars($datos_usuario['email']) ?>" required>
                            </div>
                            
                            <div class="mb-2">
                                <label class="form-label">Username:</label>
                                <input type="text" name="username" class="form-control"
                                value="<?=htmlspecialchars($datos_usuario['username'])?>" required>
                            </div>
                            <!-- Botones para guardar o cancelar -->
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                        </form>
                <?php elseif (($accion === "crear")): ?>
                         <!-- TÃ­tulo dependiendo de si se estÃ¡ creando o editando -->
                        <h3><?= $accion === "crear" ? "Nuevo usuario" : "Editar usuario" ?></h3>
                        
                        <!-- Formulario para ingresar el nombre de la categorÃ­a -->
                        <form method="post" class="mb-4" style="max-width: 400px;">
                            <div class="mb-2">
                                <label class="form-label">Nombre:</label>
                                <input type="text" name="nombre" class="form-control"
                                value="<?= htmlspecialchars($datos_usuario['nombre']) ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Apellidos:</label>
                                <input type="text" name="apellidos" class="form-control"
                                value="<?=htmlspecialchars($datos_usuario['apellidos'])?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Email:</label>
                                <input type="text" name="email" class="form-control"
                                value="<?= htmlspecialchars($datos_usuario['email']) ?>" required>
                            </div>
                            
                            <div class="mb-2">
                                <label class="form-label">Username:</label>
                                <input type="text" name="username" class="form-control"
                                value="<?=htmlspecialchars($datos_usuario['username'])?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Password:</label>
                                <input type="password" name="password" class="form-control"
                                value="" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Confirmar password:</label>
                                <input type="password" name="passwordConfirm" class="form-control"
                                value="" required>
                            </div>
                            <?php if (!empty($mensaje)) { ?>
                                <div class="mb-2">
                                    <p style="color:red; font-weight:bold;"><?= htmlspecialchars($mensaje) ?></p>
                                </div>
                            <?php } ?>

                            <!-- Botones para guardar o cancelar -->
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                        </form>
                <?php elseif (($accion === "editarPass" && $id)): ?>
                    
                        <!-- TÃ­tulo dependiendo de si se estÃ¡ creando o editando -->
                        <h3><?= $accion === "crear" ? "Nuevo usuario" : "Editar contraseña" ?></h3>
                        
                        <!-- Formulario para ingresar el nombre de la categorÃ­a -->
                        <form method="post" class="mb-4" style="max-width: 400px;">                            
                            
                            <div class="mb-2">
                                <label class="form-label">Password:</label>
                                <input type="password" name="password" class="form-control"
                                value="" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Confirmar password:</label>
                                <input type="password" name="passwordConfirm" class="form-control"
                                value="" required>
                            </div>
                            <?php if (!empty($mensaje)) { ?>
                                <div class="mb-2">
                                    <p style="color:red; font-weight:bold;"><?= htmlspecialchars($mensaje) ?></p>
                                </div>
                            <?php } ?>

                            <!-- Botones para guardar o cancelar -->
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                        </form>
        
                <?php endif; ?>
            </main>
        </div>
    </div>
    
    <?php include("./footer.php"); ?>