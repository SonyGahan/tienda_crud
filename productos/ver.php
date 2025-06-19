<?php
session_start();
require_once(__DIR__ . '/../config/database.php');
require_once __DIR__ . '/../config/config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: productos.php');
    exit();
}

$producto_id = intval($_GET['id']);

try {
    // Obtener los datos del producto
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ? AND activo = 1");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        $_SESSION['mensaje'] = "Producto no encontrado.";
        $_SESSION['tipo_mensaje'] = "error";
        header('Location: productos.php');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = "Error al obtener el producto: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = "error";
    header('Location: productos.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Producto - <?php echo htmlspecialchars($producto['nombre']); ?></title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo isset($css_path) ? $css_path : '../assets/css/style.css'; ?>" rel="stylesheet">
</head>

<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php">
                <i class="fas fa-store"></i> Tienda Productos
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../dashboard.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a class="nav-link" href="./listar.php">
                    <i class="fas fa-box"></i> Productos
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="./listar.php">Productos</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($producto['nombre']); ?></li>
            </ol>
        </nav>

        <!-- Botones de acción -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-eye"></i> Detalles del Producto</h2>
            <div>
                <a href="./listar.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="editar.php?id=<?php echo $producto['id']; ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="eliminar.php?id=<?php echo $producto['id']; ?>"
                    class="btn btn-danger btn-delete"
                    data-product-name="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    <i class="fas fa-trash"></i> Eliminar
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Imagen del producto -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <?php
                        $imgPath = BASE_URL . 'productos/uploads/' . $producto['imagen'];
                        ?>

                        <?php if (!empty($producto['imagen'])): ?>
                            <img src="<?php echo $imgPath; ?>"
                                alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                class="product-image mb-4"
                                style="max-width: 300px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <?php else: ?>
                            <div class="text-center p-5" style="background-color: #e9ecef; border-radius: 10px;">
                                <i class="fas fa-image fa-5x text-muted"></i>
                                <p class="mt-3 text-muted">Sin imagen</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Información del producto -->
            <div class="col-md-6">
                <div class="product-info">
                    <h3 class="mb-3"><?php echo htmlspecialchars($producto['nombre']); ?></h3>

                    <div class="mb-3">
                        <span class="code-badge">
                            <i class="fas fa-barcode"></i> <?php echo htmlspecialchars($producto['codigo_producto']); ?>
                        </span>
                    </div>

                    <div class="price-tag mb-3">
                        $<?php echo number_format($producto['precio'], 2); ?>
                    </div>

                    <?php if (!empty($producto['descripcion'])): ?>
                        <div class="mb-3">
                            <h5><i class="fas fa-info-circle"></i> Descripción</h5>
                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-sm-6">
                            <strong><i class="fas fa-calendar-plus"></i> Fecha de Creación:</strong><br>
                            <span class="text-muted">
                                <?php echo date('d/m/Y H:i', strtotime($producto['fecha_creacion'])); ?>
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <strong><i class="fas fa-calendar-alt"></i> Última Modificación:</strong><br>
                            <span class="text-muted">
                                <?php echo date('d/m/Y H:i', strtotime($producto['fecha_modificacion'])); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas adicionales -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar"></i> Información Adicional</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border-end">
                                    <h4 class="text-primary"><?php echo $producto['id']; ?></h4>
                                    <small class="text-muted">ID Producto</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end">
                                    <h4 class="text-success">
                                        <i class="fas fa-check-circle"></i>
                                    </h4>
                                    <small class="text-muted">Activo</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <h4 class="text-info">
                                    <?php
                                    $dias_creado = floor((time() - strtotime($producto['fecha_creacion'])) / (60 * 60 * 24));
                                    echo $dias_creado;
                                    ?>
                                </h4>
                                <small class="text-muted">Días creado</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar el producto <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="eliminar.php" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>