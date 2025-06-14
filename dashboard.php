<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Redirigir si no hay sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

// Obtener estadísticas del dashboard
try {
    // Total de productos (todos, activos o no)
    $totalProductos = executeQuery("SELECT COUNT(*) as total FROM productos", [], false)['total'];

    // Productos activos
    $productosActivos = executeQuery("SELECT COUNT(*) as total FROM productos WHERE activo = 1", [], false)['total'];

    // Total de usuarios
    $totalUsuarios = executeQuery("SELECT COUNT(*) as total FROM usuarios WHERE activo = 1", [], false)['total'];

    // Últimos 5 productos activos
    $productosRecientes = executeQuery("SELECT * FROM productos WHERE activo = 1 ORDER BY fecha_creacion DESC LIMIT 5");

    // Valor total del inventario
    $valorInventario = executeQuery("SELECT SUM(precio) as total FROM productos WHERE activo = 1", [], false)['total'] ?? 0;
} catch (Exception $e) {
    $error = "Error al cargar estadísticas: " . $e->getMessage();
}

$page_title = 'Dashboard - Mi Tienda';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <div class="sidebar-sticky pt-3 text-center text-white">
                <img src="./assets/img/logoPCchica.jpg" class="rounded-circle mb-2" alt="Avatar">
                <h6>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h6>
                <small class="text-light"><?php echo htmlspecialchars($_SESSION['usuario_email']); ?></small>

                <ul class="nav flex-column mt-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo BASE_URL; ?>dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>productos/listar.php">
                            <i class="fas fa-box me-2"></i>Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>productos/crear.php">
                            <i class="fas fa-plus me-2"></i>Agregar Producto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#reportesMenu">
                            <i class="fas fa-chart-bar me-2"></i>Reportes
                            <i class="fas fa-chevron-down float-end"></i>
                        </a>
                        <div class="collapse" id="reportesMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item"><a class="nav-link" href="#">Inventario</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Más vendidos</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="nav-link text-danger" href="<?php echo BASE_URL; ?>logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
            <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar">
                    <div class="btn-group me-2">
                        <button class="btn btn-sm btn-outline-secondary">Exportar</button>
                    </div>
                    <a href="<?php echo BASE_URL; ?>productos/crear.php" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Producto
                    </a>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Estadísticas -->
            <div class="row mb-4">
                <?php
                $cards = [
                    ['Total Productos', $totalProductos, 'primary', 'fa-box'],
                    ['Productos Activos', $productosActivos, 'success', 'fa-check-circle'],
                    ['Total Usuarios', $totalUsuarios, 'info', 'fa-users'],
                    ['Valor Inventario', '$' . number_format($valorInventario, 2), 'warning', 'fa-dollar-sign']
                ];
                foreach ($cards as [$label, $value, $color, $icon]) : ?>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-<?php echo $color; ?> shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-xs fw-bold text-<?php echo $color; ?> text-uppercase mb-1">
                                            <?php echo $label; ?>
                                        </div>
                                        <div class="h5 mb-0 fw-bold text-gray-800">
                                            <?php echo $value; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas <?php echo $icon; ?> fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Productos recientes -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0 fw-bold text-primary">Productos Recientes</h6>
                            <a href="<?php echo BASE_URL; ?>productos/listar.php" class="btn btn-sm btn-primary">Ver Todos</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($productosRecientes)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-box fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-muted">No hay productos registrados</p>
                                    <a href="<?php echo BASE_URL; ?>productos/crear.php" class="btn btn-primary">Agregar Producto</a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Código</th>
                                                <th>Precio</th>
                                                <th>Fecha</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($productosRecientes as $p): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($p['nombre']); ?></strong>
                                                        <br><small class="text-muted">
                                                            <?php echo htmlspecialchars(substr($p['descripcion'], 0, 50)) . '...'; ?>
                                                        </small>
                                                    </td>
                                                    <td><code><?php echo htmlspecialchars($p['codigo_producto']); ?></code></td>
                                                    <td><span class="badge bg-success">$<?php echo number_format($p['precio'], 2); ?></span></td>
                                                    <td><?php echo date('d/m/Y', strtotime($p['fecha_creacion'])); ?></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="<?php echo BASE_URL; ?>productos/editar.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                                            <a href="<?php echo BASE_URL; ?>productos/eliminar.php?id=<?php echo $p['id']; ?>" class="btn btn-danger btn-delete"><i class="fas fa-trash"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h6 class="m-0 fw-bold text-primary">Acciones Rápidas</h6>
                        </div>
                        <div class="card-body d-grid gap-2">
                            <a href="<?php echo BASE_URL; ?>productos/crear.php" class="btn btn-success"><i class="fas fa-plus me-2"></i>Agregar Producto</a>
                            <a href="<?php echo BASE_URL; ?>productos/listar.php" class="btn btn-primary"><i class="fas fa-list me-2"></i>Ver Productos</a>
                            <a href="#" class="btn btn-info"><i class="fas fa-chart-bar me-2"></i>Generar Reporte</a>
                            <a href="#" class="btn btn-warning"><i class="fas fa-download me-2"></i>Exportar Datos</a>
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="m-0 fw-bold text-primary">Sistema</h6>
                        </div>
                        <div class="card-body small text-muted">
                            <strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?><br>
                            <strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['usuario_email']); ?><br>
                            <strong>Último acceso:</strong> <?php echo date('d/m/Y H:i'); ?><br>
                            <strong>Versión:</strong> 1.0.0
                        </div>
                    </div>
                </div>
            </div>
        

<?php include __DIR__ . '/includes/footer.php'; ?>