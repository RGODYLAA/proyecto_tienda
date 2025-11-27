<?php
session_start();
require_once "includes/db.php";

if (empty($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT h.id_compra, h.cantidad, h.precio_unitario, h.fecha_compra,
               p.nombre, p.foto
        FROM historial_compras h
        JOIN productos p ON h.id_producto = p.id_producto
        WHERE h.id_usuario = ?
        ORDER BY h.fecha_compra DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$compras = $stmt->get_result();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Historial de compras - Mi Tienda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="container px-4 px-lg-5 mt-4">
    <h1>Historial de compras</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Foto</th>
                <th>Cantidad</th>
                <th>Precio unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($c = $compras->fetch_assoc()): 
            $total = $c['cantidad'] * $c['precio_unitario'];
        ?>
            <tr>
                <td><?php echo htmlspecialchars($c['fecha_compra']); ?></td>
                <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                <td><img src="assets/<?php echo htmlspecialchars($c['foto']); ?>" width="60"></td>
                <td><?php echo $c['cantidad']; ?></td>
                <td>$<?php echo number_format($c['precio_unitario'], 2); ?></td>
                <td>$<?php echo number_format($total, 2); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="js/scripts.js"></script>
</body>
</html>
