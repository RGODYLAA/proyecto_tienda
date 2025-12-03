<?php
session_start();
require_once "includes/db.php";

$errores = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($correo === "" || $password === "") {
        $errores[] = "Debes ingresar correo y contraseña.";
    } else {
        $stmt = $conexion->prepare("SELECT id_usuario, nombre, contraseña FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($fila = $resultado->fetch_assoc()) {
            if (password_verify($password, $fila['contraseña'])) {
                // Login correcto
                $_SESSION['id_usuario'] = $fila['id_usuario'];
                $_SESSION['nombre'] = $fila['nombre'];

                // Mantener sesión abierta hasta que el usuario cierre sesión
                header("Location: index.php");
                exit;
            } else {
                $errores[] = "Contraseña incorrecta.";
            }
        } else {
            $errores[] = "No existe una cuenta con ese correo.";
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Iniciar sesión - Mi Tienda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="container px-4 px-lg-5 mt-4">
    <h1>Iniciar sesión</h1>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="correo" class="form-control"
                   value="<?php echo htmlspecialchars($correo ?? ''); ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Entrar</button>
        </div>
    </form>

    <hr class="my-4">
    
    <div class="text-center">
        <p class="mb-0">¿No tienes cuenta? <a href="register.php" class="fw-bold text-decoration-none">Regístrate aquí</a></p>
    </div>
</div>

<script src="js/scripts.js"></script>
</body>
</html>