<?php
session_start();
require_once "includes/db.php";

$errores = [];
$exito = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $tarjeta = trim($_POST['tarjeta'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    // Validaciones básicas
    if ($nombre === "" || $correo === "" || $password === "" || $password2 === "" ||
        $fecha_nacimiento === "" || $tarjeta === "" || $direccion === "") {
        $errores[] = "Todos los campos son obligatorios.";
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }

    if ($password !== $password2) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (empty($errores)) {
        // ¿El correo ya existe?
        $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errores[] = "Ya existe una cuenta con ese correo.";
        } else {
            // Guardar nuevo usuario
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare(
                "INSERT INTO usuarios (nombre, correo, contraseña, fecha_nacimiento, tarjeta, direccion)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("ssssss", $nombre, $correo, $hash, $fecha_nacimiento, $tarjeta, $direccion);

            if ($stmt->execute()) {
                $exito = "Cuenta creada correctamente. Ahora puedes iniciar sesión.";
                // Opcional: iniciar sesión automáticamente
                // $_SESSION['id_usuario'] = $stmt->insert_id;
                // $_SESSION['nombre'] = $nombre;
                // header("Location: usuario.php");
                // exit;
            } else {
                $errores[] = "Error al crear la cuenta.";
            }
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro - Mi Tienda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="container px-4 px-lg-5 mt-4">
    <h1>Registro de usuario</h1>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($exito): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($exito); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre completo</label>
            <input type="text" name="nombre" class="form-control"
                   value="<?php echo htmlspecialchars($nombre ?? ''); ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="correo" class="form-control"
                   value="<?php echo htmlspecialchars($correo ?? ''); ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" name="password2" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Fecha de nacimiento</label>
            <input type="date" name="fecha_nacimiento" class="form-control"
                   value="<?php echo htmlspecialchars($fecha_nacimiento ?? ''); ?>" required>
        </div>
        <div class="col-md-8">
            <label class="form-label">Número de tarjeta bancaria</label>
            <input type="text" name="tarjeta" class="form-control"
                   value="<?php echo htmlspecialchars($tarjeta ?? ''); ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control"
                   value="<?php echo htmlspecialchars($direccion ?? ''); ?>" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Crear cuenta</button>
        </div>
    </form>

    <hr class="my-4">
    
    <div class="text-center">
        <p class="mb-0">¿Ya tienes cuenta? <a href="login.php" class="fw-bold text-decoration-none">Inicia sesión aquí</a></p>
    </div>
</div>

<script src="js/scripts.js"></script>
</body>
</html>