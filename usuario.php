<?php
session_start();
require_once "includes/db.php";

if (empty($_SESSION['id_usuario'])) {
    // Si no está logueado, lo mandamos a login
    header("Location: login.php");
    exit;
}

$id = $_SESSION['id_usuario'];

$stmt = $conexion->prepare("SELECT nombre, correo, fecha_nacimiento, tarjeta, direccion FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Mi cuenta - Mi Tienda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="container px-4 px-lg-5 mt-4">
    <h1>Mi cuenta</h1>

    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
    <p><strong>Correo:</strong> <?php echo htmlspecialchars($usuario['correo']); ?></p>
    <p><strong>Fecha de nacimiento:</strong> <?php echo htmlspecialchars($usuario['fecha_nacimiento']); ?></p>
    <p><strong>Tarjeta:</strong> <?php echo htmlspecialchars($usuario['tarjeta']); ?></p>
    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($usuario['direccion']); ?></p>
</div>

<script src="js/scripts.js"></script>
</body>
</html>
