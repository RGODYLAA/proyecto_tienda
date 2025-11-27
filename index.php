<?php
session_start();
require_once "includes/db.php";

// PRODUCTO DESTACADO (primer producto activo)
$sqlDest = "SELECT id_producto, nombre, descripcion, foto, precio, fabricante, origen
            FROM productos
            WHERE activo = 1
            ORDER BY id_producto ASC
            LIMIT 1";
$resDest = $conexion->query($sqlDest);
$destacado = $resDest ? $resDest->fetch_assoc() : null;

// LISTA DE DEMÁS PRODUCTOS ACTIVOS
$sqlLista = "SELECT id_producto, nombre, descripcion, foto, precio
             FROM productos
             WHERE activo = 1";
if ($destacado) {
    $id_dest = (int)$destacado['id_producto'];
    $sqlLista .= " AND id_producto <> $id_dest";
}
$productos = $conexion->query($sqlLista);
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Sneakers y zapatos de lujo" />
        <meta name="author" content="Mi Tienda" />
        <title>Mi Tienda - Sneakers de lujo</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <!-- Nav Bar -->
        <?php include "includes/navbar.php"; ?>
        
        <!-- Producto destacado -->
        <section class="py-5">
            <div class="container px-4 px-lg-5 my-5">
                <?php if ($destacado): ?>
                    <div class="row gx-4 gx-lg-5 align-items-center">
                        <div class="col-md-6">
                            <img class="card-img-top mb-5 mb-md-0"
                                 src="assets/<?php echo htmlspecialchars($destacado['foto']); ?>"
                                 alt="<?php echo htmlspecialchars($destacado['nombre']); ?>" />
                        </div>
                        <div class="col-md-6">
                            <div class="small mb-2 text-muted">
                                <?php echo htmlspecialchars($destacado['fabricante']); ?> ·
                                <?php echo htmlspecialchars($destacado['origen']); ?>
                            </div>
                            <h1 class="display-5 fw-bolder">
                                <?php echo htmlspecialchars($destacado['nombre']); ?>
                            </h1>
                            <div class="fs-4 mb-4">
                                <span>$<?php echo number_format($destacado['precio'], 2); ?> MXN</span>
                            </div>
                            <p class="lead mb-4">
                                <?php echo nl2br(htmlspecialchars($destacado['descripcion'])); ?>
                            </p>
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <a class="btn btn-dark flex-shrink-0"
                                   href="producto.php?id=<?php echo (int)$destacado['id_producto']; ?>">
                                    Ver modelo
                                </a>
                                <a class="btn btn-outline-dark flex-shrink-0"
                                   href="agregar_carrito.php?id=<?php echo (int)$destacado['id_producto']; ?>">
                                    <i class="bi-cart-fill me-1"></i>
                                    Agregar rápido al carrito
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <h2>No hay productos activos aún.</h2>
                    <p>Agrega productos en la tabla <strong>productos</strong> de la base de datos.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Resto del catálogo -->
        <section class="py-5 bg-light">
            <div class="container px-4 px-lg-5 mt-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bolder mb-0">Nuestros modelos</h2>
                    <a href="catalogo.php" class="text-decoration-none">Ver catálogo completo &rarr;</a>
                </div>

                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                    <?php if ($productos && $productos->num_rows > 0): ?>
                        <?php while ($p = $productos->fetch_assoc()): ?>
                            <div class="col mb-5">
                                <div class="card h-100">
                                    <!-- Imagen -->
                                    <img class="card-img-top"
                                         src="assets/<?php echo htmlspecialchars($p['foto']); ?>"
                                         alt="<?php echo htmlspecialchars($p['nombre']); ?>" />

                                    <!-- Detalles -->
                                    <div class="card-body p-4">
                                        <div class="text-center">
                                            <h5 class="fw-bolder">
                                                <?php echo htmlspecialchars($p['nombre']); ?>
                                            </h5>
                                            <p class="mb-1" style="font-size: 0.9rem;">
                                                <?php echo htmlspecialchars(mb_strimwidth($p['descripcion'], 0, 60, '...')); ?>
                                            </p>
                                            $<?php echo number_format($p['precio'], 2); ?> MXN
                                        </div>
                                    </div>

                                    <!-- Acciones -->
                                    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                        <div class="d-flex flex-column gap-2">
                                            <a class="btn btn-outline-dark btn-sm"
                                               href="producto.php?id=<?php echo (int)$p['id_producto']; ?>">
                                                Ver modelo
                                            </a>
                                            <a class="btn btn-dark btn-sm"
                                               href="agregar_carrito.php?id=<?php echo (int)$p['id_producto']; ?>">
                                                <i class="bi-cart-fill me-1"></i>
                                                Agregar al carrito
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No hay productos para mostrar.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Footer-->
        <footer class="py-5 bg-dark">
            <div class="container">
                <p class="m-0 text-center text-white">
                    &copy; Mi Tienda <?php echo date('Y'); ?>
                </p>
            </div>
        </footer>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>
