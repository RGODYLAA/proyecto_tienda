<?php
session_start();
require_once "includes/db.php";

// Verificar si está logueado
if (empty($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Verificar si es administrador
$id_usuario = $_SESSION['id_usuario'];
$sql_admin = "SELECT es_admin FROM usuarios WHERE id_usuario = ?";
$stmt_admin = $conexion->prepare($sql_admin);
$stmt_admin->bind_param("i", $id_usuario);
$stmt_admin->execute();
$resultado_admin = $stmt_admin->get_result()->fetch_assoc();
$stmt_admin->close();

if (!$resultado_admin || $resultado_admin['es_admin'] != 1) {
    // No es administrador, redirigir a inicio
    header("Location: index.php");
    exit;
}

// Mensajes de feedback
$mensaje = "";
$tipo_mensaje = "";

// ============================================
// PROCESAR ACCIONES POST
// ============================================

// AGREGAR PRODUCTO
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion'])) {
    
    if ($_POST['accion'] === 'agregar') {
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $foto = trim($_POST['foto'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $cantidad = intval($_POST['cantidad'] ?? 0);
        $fabricante = trim($_POST['fabricante'] ?? '');
        $origen = trim($_POST['origen'] ?? '');
        $activo = isset($_POST['activo']) ? 1 : 0;

        if ($nombre && $precio > 0) {
            $sql = "INSERT INTO productos (nombre, descripcion, foto, precio, cantidad, fabricante, origen, activo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssdiisi", $nombre, $descripcion, $foto, $precio, $cantidad, $fabricante, $origen, $activo);
            
            if ($stmt->execute()) {
                $mensaje = "Producto agregado correctamente.";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al agregar el producto.";
                $tipo_mensaje = "danger";
            }
            $stmt->close();
        } else {
            $mensaje = "El nombre y precio son obligatorios.";
            $tipo_mensaje = "warning";
        }
    }
    
    // MODIFICAR PRODUCTO
    if ($_POST['accion'] === 'modificar') {
        $id_producto = intval($_POST['id_producto'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $foto = trim($_POST['foto'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $cantidad = intval($_POST['cantidad'] ?? 0);
        $fabricante = trim($_POST['fabricante'] ?? '');
        $origen = trim($_POST['origen'] ?? '');
        $activo = isset($_POST['activo']) ? 1 : 0;

        if ($id_producto > 0 && $nombre && $precio > 0) {
            $sql = "UPDATE productos 
                    SET nombre = ?, descripcion = ?, foto = ?, precio = ?, cantidad = ?, fabricante = ?, origen = ?, activo = ?
                    WHERE id_producto = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssdiisii", $nombre, $descripcion, $foto, $precio, $cantidad, $fabricante, $origen, $activo, $id_producto);
            
            if ($stmt->execute()) {
                $mensaje = "Producto actualizado correctamente.";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al actualizar el producto.";
                $tipo_mensaje = "danger";
            }
            $stmt->close();
        }
    }
    
    // ELIMINAR PRODUCTO
    if ($_POST['accion'] === 'eliminar') {
        $id_producto = intval($_POST['id_producto'] ?? 0);
        
        if ($id_producto > 0) {
            // En lugar de eliminar, desactivamos el producto
            $sql = "UPDATE productos SET activo = 0 WHERE id_producto = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_producto);
            
            if ($stmt->execute()) {
                $mensaje = "Producto desactivado correctamente.";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al desactivar el producto.";
                $tipo_mensaje = "danger";
            }
            $stmt->close();
        }
    }
}

// ============================================
// OBTENER DATOS PARA MOSTRAR
// ============================================

// Todos los productos (activos e inactivos)
$sql_productos = "SELECT * FROM productos ORDER BY id_producto DESC";
$productos = $conexion->query($sql_productos);

// Estadísticas de inventario
$sql_stats = "SELECT 
                COUNT(*) as total_productos,
                SUM(cantidad) as total_stock,
                SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as productos_activos,
                SUM(CASE WHEN cantidad <= 5 AND activo = 1 THEN 1 ELSE 0 END) as stock_bajo
              FROM productos";
$stats = $conexion->query($sql_stats)->fetch_assoc();

// Historial de compras (todas las compras de todos los usuarios)
$sql_historial = "SELECT h.id_compra, h.cantidad, h.precio_unitario, h.fecha_compra,
                         p.nombre as producto_nombre, p.foto,
                         u.nombre as usuario_nombre, u.correo
                  FROM historial_compras h
                  JOIN productos p ON h.id_producto = p.id_producto
                  JOIN usuarios u ON h.id_usuario = u.id_usuario
                  ORDER BY h.fecha_compra DESC
                  LIMIT 50";
$historial = $conexion->query($sql_historial);

// Calcular ventas totales
$sql_ventas = "SELECT SUM(cantidad * precio_unitario) as total_ventas, COUNT(*) as total_ordenes FROM historial_compras";
$ventas = $conexion->query($sql_ventas)->fetch_assoc();

// Producto a editar (si viene por GET)
$producto_editar = null;
if (isset($_GET['editar'])) {
    $id_editar = intval($_GET['editar']);
    $sql_edit = "SELECT * FROM productos WHERE id_producto = ?";
    $stmt_edit = $conexion->prepare($sql_edit);
    $stmt_edit->bind_param("i", $id_editar);
    $stmt_edit->execute();
    $producto_editar = $stmt_edit->get_result()->fetch_assoc();
    $stmt_edit->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Administración - Mi Tienda</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-card.primary { border-left-color: #0d6efd; }
        .stat-card.success { border-left-color: #198754; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.info { border-left-color: #0dcaf0; }
        .table-responsive { max-height: 500px; overflow-y: auto; }
        .nav-tabs .nav-link.active { font-weight: bold; }
        .producto-inactivo { opacity: 0.6; background-color: #f8f9fa; }
        .badge-stock-bajo { animation: pulse 2s infinite; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold"><i class="bi bi-gear-fill me-2"></i>Panel de Administración</h1>
        <span class="text-muted">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
    </div>

    <!-- Mensaje de feedback -->
    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card stat-card primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Productos</h6>
                            <h3 class="fw-bold"><?php echo $stats['total_productos'] ?? 0; ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-box-seam fs-1 text-primary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card stat-card success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Productos Activos</h6>
                            <h3 class="fw-bold"><?php echo $stats['productos_activos'] ?? 0; ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1 text-success opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card stat-card warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Stock Total</h6>
                            <h3 class="fw-bold"><?php echo number_format($stats['total_stock'] ?? 0); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-archive fs-1 text-warning opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card stat-card info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Ventas Totales</h6>
                            <h3 class="fw-bold">$<?php echo number_format($ventas['total_ventas'] ?? 0, 2); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-currency-dollar fs-1 text-info opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pestañas de navegación -->
    <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link <?php echo !$producto_editar ? 'active' : ''; ?>" id="inventario-tab" data-bs-toggle="tab" data-bs-target="#inventario" type="button">
                <i class="bi bi-boxes me-1"></i>Inventario
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link <?php echo $producto_editar ? 'active' : ''; ?>" id="producto-tab" data-bs-toggle="tab" data-bs-target="#producto" type="button">
                <i class="bi bi-plus-circle me-1"></i><?php echo $producto_editar ? 'Editar Producto' : 'Agregar Producto'; ?>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="historial-tab" data-bs-toggle="tab" data-bs-target="#historial" type="button">
                <i class="bi bi-clock-history me-1"></i>Historial de Compras
            </button>
        </li>
    </ul>

    <div class="tab-content" id="adminTabsContent">
        
        <!-- TAB: INVENTARIO -->
        <div class="tab-pane fade <?php echo !$producto_editar ? 'show active' : ''; ?>" id="inventario" role="tabpanel">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-boxes me-2"></i>Reporte de Inventario</span>
                    <?php if ($stats['stock_bajo'] > 0): ?>
                        <span class="badge bg-warning text-dark badge-stock-bajo">
                            <i class="bi bi-exclamation-triangle me-1"></i><?php echo $stats['stock_bajo']; ?> productos con stock bajo
                        </span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>ID</th>
                                    <th>Foto</th>
                                    <th>Nombre</th>
                                    <th>Fabricante</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($productos && $productos->num_rows > 0): ?>
                                    <?php while ($p = $productos->fetch_assoc()): ?>
                                        <tr class="<?php echo $p['activo'] ? '' : 'producto-inactivo'; ?>">
                                            <td><?php echo $p['id_producto']; ?></td>
                                            <td>
                                                <img src="assets/<?php echo htmlspecialchars($p['foto']); ?>" 
                                                     alt="<?php echo htmlspecialchars($p['nombre']); ?>" 
                                                     width="50" class="rounded">
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($p['nombre']); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars(mb_strimwidth($p['descripcion'], 0, 40, '...')); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($p['fabricante']); ?></td>
                                            <td>$<?php echo number_format($p['precio'], 2); ?></td>
                                            <td>
                                                <?php if ($p['cantidad'] <= 5 && $p['activo']): ?>
                                                    <span class="badge bg-danger"><?php echo $p['cantidad']; ?></span>
                                                <?php elseif ($p['cantidad'] <= 15): ?>
                                                    <span class="badge bg-warning text-dark"><?php echo $p['cantidad']; ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-success"><?php echo $p['cantidad']; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($p['activo']): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="admin.php?editar=<?php echo $p['id_producto']; ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php if ($p['activo']): ?>
                                                <form method="post" class="d-inline" 
                                                      onsubmit="return confirm('¿Desactivar este producto?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id_producto" value="<?php echo $p['id_producto']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Desactivar">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">No hay productos registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: AGREGAR/EDITAR PRODUCTO -->
        <div class="tab-pane fade <?php echo $producto_editar ? 'show active' : ''; ?>" id="producto" role="tabpanel">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-<?php echo $producto_editar ? 'pencil-square' : 'plus-circle'; ?> me-2"></i>
                    <?php echo $producto_editar ? 'Editar Producto' : 'Agregar Nuevo Producto'; ?>
                </div>
                <div class="card-body">
                    <form method="post" class="row g-3">
                        <input type="hidden" name="accion" value="<?php echo $producto_editar ? 'modificar' : 'agregar'; ?>">
                        <?php if ($producto_editar): ?>
                            <input type="hidden" name="id_producto" value="<?php echo $producto_editar['id_producto']; ?>">
                        <?php endif; ?>

                        <div class="col-md-6">
                            <label class="form-label">Nombre del producto *</label>
                            <input type="text" name="nombre" class="form-control" required
                                   value="<?php echo htmlspecialchars($producto_editar['nombre'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fabricante</label>
                            <input type="text" name="fabricante" class="form-control"
                                   value="<?php echo htmlspecialchars($producto_editar['fabricante'] ?? ''); ?>"
                                   placeholder="Ej: Nike, Adidas, Gucci...">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"
                                      placeholder="Descripción detallada del producto..."><?php echo htmlspecialchars($producto_editar['descripcion'] ?? ''); ?></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nombre del archivo de foto</label>
                            <input type="text" name="foto" class="form-control"
                                   value="<?php echo htmlspecialchars($producto_editar['foto'] ?? ''); ?>"
                                   placeholder="Ej: nike_airmax.jpg">
                            <small class="text-muted">El archivo debe estar en la carpeta /assets/</small>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Precio (MXN) *</label>
                            <input type="number" name="precio" class="form-control" step="0.01" min="0" required
                                   value="<?php echo $producto_editar['precio'] ?? ''; ?>">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control" min="0"
                                   value="<?php echo $producto_editar['cantidad'] ?? 0; ?>">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Origen</label>
                            <input type="text" name="origen" class="form-control"
                                   value="<?php echo htmlspecialchars($producto_editar['origen'] ?? ''); ?>"
                                   placeholder="Ej: Italia">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="activo" class="form-check-input" id="activo"
                                       <?php echo (!$producto_editar || $producto_editar['activo']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="activo">Activo</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-<?php echo $producto_editar ? 'save' : 'plus-circle'; ?> me-1"></i>
                                <?php echo $producto_editar ? 'Guardar Cambios' : 'Agregar Producto'; ?>
                            </button>
                            <?php if ($producto_editar): ?>
                                <a href="admin.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancelar
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- TAB: HISTORIAL DE COMPRAS -->
        <div class="tab-pane fade" id="historial" role="tabpanel">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2"></i>Historial de Compras</span>
                    <span class="badge bg-light text-dark"><?php echo $ventas['total_ordenes'] ?? 0; ?> órdenes</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($historial && $historial->num_rows > 0): ?>
                                    <?php while ($h = $historial->fetch_assoc()): 
                                        $total = $h['cantidad'] * $h['precio_unitario'];
                                    ?>
                                        <tr>
                                            <td><?php echo $h['id_compra']; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($h['fecha_compra'])); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($h['usuario_nombre']); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($h['correo']); ?></small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="assets/<?php echo htmlspecialchars($h['foto']); ?>" width="40" class="me-2 rounded">
                                                    <?php echo htmlspecialchars($h['producto_nombre']); ?>
                                                </div>
                                            </td>
                                            <td><?php echo $h['cantidad']; ?></td>
                                            <td>$<?php echo number_format($h['precio_unitario'], 2); ?></td>
                                            <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">No hay compras registradas.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Footer -->
<footer class="py-4 bg-dark mt-5">
    <div class="container">
        <p class="m-0 text-center text-white">&copy; Mi Tienda <?php echo date('Y'); ?></p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>