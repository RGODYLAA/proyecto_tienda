<?php
session_start();
require_once "includes/db.php";

if (empty($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener carrito del usuario
$sql = "SELECT id_producto, cantidad FROM carrito WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$carrito = $stmt->get_result();

while ($item = $carrito->fetch_assoc()) {

    $id_producto = $item['id_producto'];
    $cantidad    = $item['cantidad'];

    // Obtener precio actual del producto
    $res_p = $conexion->prepare("SELECT precio FROM productos WHERE id_producto = ?");
    $res_p->bind_param("i", $id_producto);
    $res_p->execute();
    $res = $res_p->get_result();
    $fila_precio = $res->fetch_assoc();
    $precio = $fila_precio['precio'] ?? 0;

    // 1. Insertar en historial
    $sql_i = "INSERT INTO historial_compras (id_usuario, id_producto, cantidad, precio_unitario)
              VALUES (?, ?, ?, ?)";
    $stmt_i = $conexion->prepare($sql_i);
    $stmt_i->bind_param("iiid", $id_usuario, $id_producto, $cantidad, $precio);
    $stmt_i->execute();

    // 2. Restar inventario
    $sql_u = "UPDATE productos SET cantidad = cantidad - ? WHERE id_producto = ?";
    $stmt_u = $conexion->prepare($sql_u);
    $stmt_u->bind_param("ii", $cantidad, $id_producto);
    $stmt_u->execute();
}

// 3. Vaciar carrito
$sql = "DELETE FROM carrito WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

header("Location: carrito.php");
exit;
?>
