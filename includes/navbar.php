<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";

// Contar productos en carrito
$total_carrito = 0;
$es_admin = false;

if (!empty($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
    
    // Contar carrito
    $sql = "SELECT SUM(cantidad) AS total FROM carrito WHERE id_usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $total_carrito = (int)($res['total'] ?? 0);
    
    // Verificar si es admin
    $sql_admin = "SELECT es_admin FROM usuarios WHERE id_usuario = ?";
    $stmt_admin = $conexion->prepare($sql_admin);
    $stmt_admin->bind_param("i", $id_usuario);
    $stmt_admin->execute();
    $res_admin = $stmt_admin->get_result()->fetch_assoc();
    $es_admin = ($res_admin && $res_admin['es_admin'] == 1);
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container px-4 px-lg-5">
    <a class="navbar-brand" href="index.php">Mi Tienda</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="catalogo.php">Catálogo</a></li>

        <li class="nav-item">
            <a class="nav-link" href="carrito.php">
                Carrito
                <?php if ($total_carrito > 0): ?>
                    <span class="badge bg-success ms-1"><?php echo $total_carrito; ?></span>
                <?php endif; ?>
            </a>
        </li>

        <?php if (!empty($_SESSION['id_usuario'])): ?>
            <li class="nav-item"><a class="nav-link" href="historial.php">Historial</a></li>
            <li class="nav-item"><a class="nav-link" href="usuario.php">Mi cuenta</a></li>
            <?php if ($es_admin): ?>
                <li class="nav-item"><a class="nav-link text-warning" href="admin.php"><i class="bi bi-gear-fill"></i> Admin</a></li>
            <?php endif; ?>
            <li class="nav-item">
              <span class="nav-link disabled">
                Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
              </span>
            </li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar sesión</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Iniciar sesión</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Registrarse</a></li>
        <?php endif; ?>

        <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
      </ul>
    </div>
  </div>
</nav>