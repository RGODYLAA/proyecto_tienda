<?php
session_start();
require_once "includes/db.php";

if (empty($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// productos
$sql = "SELECT c.id_carrito, c.cantidad, p.nombre, p.precio, p.foto, p.id_producto
        FROM carrito c
        JOIN productos p ON c.id_producto = p.id_producto
        WHERE c.id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$productos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito</title>
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="container mt-4">
    <h1>Mi Carrito</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Foto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $gran_total = 0;
        while ($row = $productos->fetch_assoc()): 
            $total = $row['precio'] * $row['cantidad'];
            $gran_total += $total;
        ?>
            <tr>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><img src="assets/<?= htmlspecialchars($row['foto']) ?>" width="60"></td>
                <td>$<?= number_format($row['precio'], 2) ?></td>
                <td>
                    <a href="modificar_carrito.php?id=<?= $row['id_carrito'] ?>&op=1" class="btn btn-sm btn-primary">+</a>
                    <strong><?= $row['cantidad'] ?></strong>
                    <a href="modificar_carrito.php?id=<?= $row['id_carrito'] ?>&op=2" class="btn btn-sm btn-primary">-</a>
                </td>
                <td>$<?= number_format($total, 2) ?></td>
                <td>
                    <a href="eliminar_carrito.php?id=<?= $row['id_carrito'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <h3>Total: $<?= number_format($gran_total, 2) ?></h3>

    <a href="comprar.php" class="btn btn-success">Completar compra</a>
</div>

</body>
</html>
