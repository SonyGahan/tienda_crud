<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Obtener estadísticas del dashboard
try {
    // Total de productos
    $totalProductos = executeQuery("SELECT COUNT(*) as total FROM productos WHERE activo = 1");
    $totalProductos = $totalProductos[0]['total'];

    // Productos activos
    $productosActivos = executeQuery("SELECT COUNT(*) as total FROM productos WHERE activo = 1");
    $productosActivos = $productosActivos[0]['total'];

    // Total de usuarios
    $totalUsuarios = executeQuery("SELECT COUNT(*) as total FROM usuarios WHERE activo = 1");
    $totalUsuarios = $totalUsuarios[0]['total'];

    // Productos más recientes (últimos 5)
    $productosRecientes = executeQuery("SELECT * FROM productos WHERE activo = 1 ORDER BY fecha_creacion DESC LIMIT 5");

    // Calcular valor total del inventario
    $valorInventario = executeQuery("SELECT SUM(precio) as total FROM productos WHERE activo = 1");
    $valorInventario = $valorInventario[0]['total'] ?? 0;
} catch (Exception $e) {
    $error = "Error al cargar estadísticas: " . $e->getMessage();
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="sidebar-sticky pt-3">
                <div class="text-center mb-4">
                    <img src="https://via.placeholder.com/80x80/007bff/ffffff?text=TC" class="rounded-circle mb-2" alt="Avatar">
                    <h6>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h6>
                    <small class="text-muted"><?php echo htmlspecialchars($_SESSION['usuario_email']); ?></small>
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="productos/listar.php">
                            <i class="fas fa-box"></i>
                            Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="productos/crear.php">
                            <i class="fas fa-plus"></i>
                            Agregar Producto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#reportesMenu">
                            <i class="fas fa-chart-bar"></i>
                            Reportes
                            <i class="fas fa-chevron-down float-end"></i>
                        </a>
                        <div class="collapse" id="reportesMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Inventario</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Productos más vendidos</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Exportar</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i>
                        Nuevo Producto
                    </button>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Tarjetas de estadísticas -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Productos
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($totalProductos); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-box fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Productos Activos
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($productosActivos); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Usuarios
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($totalUsuarios); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Valor Inventario
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        $<?php echo number_format($valorInventario, 2); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos recientes -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Productos Recientes</h6>
                            <a href="productos/listar.php" class="btn btn-primary btn-sm">Ver Todos</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($productosRecientes)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-box fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-muted">No hay productos registrados</p>
                                    <a href="productos/crear.php" class="btn btn-primary">Agregar Primer Producto</a>
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
                                            <?php foreach ($productosRecientes as $producto): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <?php echo htmlspecialchars(substr($producto['descripcion'], 0, 50)) . '...'; ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <code><?php echo htmlspecialchars($producto['codigo_producto']); ?></code>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            $<?php echo number_format($producto['precio'], 2); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php echo date('d/m/Y', strtotime($producto['fecha_creacion'])); ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="productos/editar.php?id=<?php echo $producto['id']; ?>"
                                                                class="btn btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="eliminar.php?id=<?php echo $producto['id']; ?>"
                                                                class="btn btn-danger btn-delete"
                                                                data-product-name="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
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
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="productos/crear.php" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>
                                    Agregar Producto
                                </a>
                                <a href="productos/listar.php" class="btn btn-primary">
                                    <i class="fas fa-list me-2"></i>
                                    Ver Productos
                                </a>
                                <a href="#" class="btn btn-info">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Generar Reporte
                                </a>
                                <a href="#" class="btn btn-warning">
                                    <i class="fas fa-download me-2"></i>
                                    Exportar Datos
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Información del sistema -->
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Sistema</h6>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?><br>
                                <strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['usuario_email']); ?><br>
                                <strong>Último acceso:</strong> <?php echo date('d/m/Y H:i'); ?><br>
                                <strong>Versión:</strong> 1.0.0
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>