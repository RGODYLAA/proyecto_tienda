<?php
session_start();
require_once "includes/db.php";

$id_carrito = intval($_GET['id']);

$sql = "DELETE FROM carrito WHERE id_carrito = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_carrito);
$stmt->execute();

header("Location: carrito.php");
exit;
?>
