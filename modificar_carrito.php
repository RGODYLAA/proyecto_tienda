<?php
session_start();
require_once "includes/db.php";

$id_usuario = $_SESSION['id_usuario'];
$id_carrito = intval($_GET['id']);
$op = intval($_GET['op']); // 1 = sum 2 = restar

if ($op == 1) {
    $sql = "UPDATE carrito SET cantidad = cantidad + 1 WHERE id_carrito = ?";
} else {
    $sql = "UPDATE carrito SET cantidad = cantidad - 1 WHERE id_carrito = ? AND cantidad > 1";
}

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_carrito);
$stmt->execute();

header("Location: carrito.php");
exit;
?>
