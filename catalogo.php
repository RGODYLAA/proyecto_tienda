<?php
session_start();
require_once "includes/db.php";

// Obtener todos los productos activos
$sql = "SELECT id_producto, nombre, descripcion, foto, precio, fabricante, origen
        FROM productos
        WHERE activo = 1
        ORDER BY fabricante, nombre";
$productos = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Catálogo de sneakers y zapatos de lujo" />
        <meta name="author" content="Mi Tienda" />
        <title>Catálogo - Mi Tienda</title>
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

        <!-- Encabezado -->
        <header class="py-5">
            <div class="container px-4 px-lg-5 my-3">
                <div class="text-center">
                    <h1 class="fw-bolder">Catálogo de sneakers y zapatos de lujo</h1>
                    <p class="lead text-muted mb-0">
                        Golden Goose, Alexander McQueen, Margiela, Loro Piana, Zegna y más.
                    </p>
                </div>
            </div>
        </header>

        <!-- Catálogo -->
        <section class="py-3">
            <div class="container px-4 px-lg-5 mt-3">
                <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
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
                                            <div class="small text-muted mb-1">
                                                <?php echo htmlspecialchars($p['fabricante']); ?> ·
                                                <?php echo htmlspecialchars($p['origen']); ?>
                                            </div>
                                            <h5 class="fw-bolder">
                                                <?php echo htmlspecialchars($p['nombre']); ?>
                                            </h5>
                                            <p class="mb-1" style="font-size: 0.9rem;">
                                                <?php echo htmlspecialchars(mb_strimwidth($p['descripcion'], 0, 70, '...')); ?>
                                            </p>
                                            <div class="mt-2">
                                                $<?php echo number_format($p['precio'], 2); ?> MXN
                                            </div>
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
                        <p>No hay productos activos en el catálogo.</p>
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
