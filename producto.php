<?php
session_start();
require_once "includes/db.php";

// Si no viene id, regreso a inicio
if (empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_producto = intval($_GET['id']);

$sql = "SELECT id_producto, nombre, descripcion, foto, precio, fabricante, origen 
        FROM productos 
        WHERE id_producto = ? AND activo = 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$resultado = $stmt->get_result();
$producto = $resultado->fetch_assoc();

if (!$producto) {
    // Producto no encontrado
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Detalle de sneaker de lujo" />
        <meta name="author" content="Mi Tienda" />
        <title><?php echo htmlspecialchars($producto['nombre']); ?> - Mi Tienda</title>
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
        
        <!-- Sección de producto -->
        <section class="py-5">
            <div class="container px-4 px-lg-5 my-5">
                <div class="row gx-4 gx-lg-5 align-items-center">
                    <!-- Imagen grande -->
                    <div class="col-md-6">
                        <img class="card-img-top mb-5 mb-md-0"
                             src="assets/<?php echo htmlspecialchars($producto['foto']); ?>"
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>" />
                    </div>

                    <!-- Info producto -->
                    <div class="col-md-6">
                        <div class="small mb-2 text-muted">
                            <?php echo htmlspecialchars($producto['fabricante']); ?> · 
                            <?php echo htmlspecialchars($producto['origen']); ?>
                        </div>

                        <h1 class="display-5 fw-bolder">
                            <?php echo htmlspecialchars($producto['nombre']); ?>
                        </h1>

                        <div class="fs-4 mb-4">
                            <span>$<?php echo number_format($producto['precio'], 2); ?> MXN</span>
                        </div>

                        <p class="lead mb-4">
                            <?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?>
                        </p>

                        <!-- Formulario talla + cantidad + agregar al carrito -->
                        <form class="d-flex flex-column flex-md-row align-items-start gap-2"
                              action="agregar_carrito.php"
                              method="post">

                            <input type="hidden" name="id_producto"
                                   value="<?php echo (int)$producto['id_producto']; ?>">

                            <!-- Talla (solo frontend, no toca BD) -->
                            <div class="me-2">
                                <label for="talla" class="form-label mb-1">Talla</label>
                                <select class="form-select" id="talla" name="talla" style="max-width: 8rem;">
                                    <option value="38">38</option>
                                    <option value="39" selected>39</option>
                                    <option value="40">40</option>
                                    <option value="41">41</option>
                                    <option value="42">42</option>
                                    <option value="43">43</option>
                                    <option value="44">44</option>
                                </select>
                            </div>

                            <!-- Cantidad -->
                            <div class="me-2">
                                <label for="cantidad" class="form-label mb-1">Cantidad</label>
                                <input class="form-control text-center"
                                       id="cantidad"
                                       name="cantidad"
                                       type="number"
                                       value="1"
                                       min="1"
                                       style="max-width: 5rem" />
                            </div>

                            <!-- Botón -->
                            <div class="mt-3 mt-md-4">
                                <button class="btn btn-outline-dark flex-shrink-0" type="submit">
                                    <i class="bi-cart-fill me-1"></i>
                                    Agregar al carrito
                                </button>
                            </div>
                        </form>

                        <div class="mt-3">
                            <a href="index.php" class="text-decoration-none">
                                &larr; Volver al inicio
                            </a>
                        </div>
                    </div>
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
