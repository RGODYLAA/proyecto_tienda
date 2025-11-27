<?php
session_start();
require_once "includes/db.php";

if (empty($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Puede venir por POST (desde producto.php) o por GET (botón rápido)
$id_producto = 0;
if (isset($_POST['id_producto'])) {
    $id_producto = intval($_POST['id_producto']);
} elseif (isset($_GET['id'])) {
    $id_producto = intval($_GET['id']);
}

if ($id_producto <= 0) {
    header("Location: index.php");
    exit;
}

// Cantidad: si viene en POST la usamos, si no, 1
if (isset($_POST['cantidad']) && intval($_POST['cantidad']) > 0) {
    $cantidad = intval($_POST['cantidad']);
} else {
    $cantidad = 1;
}

// Verificar si el producto ya está en el carrito
$sql = "SELECT cantidad FROM carrito WHERE id_usuario = ? AND id_producto = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_usuario, $id_producto);
$stmt->execute();
$resultado = $stmt->get_result();

if ($row = $resultado->fetch_assoc()) {
    // Ya existe → aumentar cantidad
    $sql = "UPDATE carrito 
            SET cantidad = cantidad + ? 
            WHERE id_usuario = ? AND id_producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iii", $cantidad, $id_usuario, $id_producto);
    $stmt->execute();
} else {
    // No existe → insertar
    $sql = "INSERT INTO carrito (id_usuario, id_producto, cantidad)
            VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iii", $id_usuario, $id_producto, $cantidad);
    $stmt->execute();
}

header("Location: carrito.php");
exit;
?>
