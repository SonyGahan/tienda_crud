<?php
session_start();
require_once(__DIR__ . '/../config/database.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Configuración de paginación
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Búsqueda
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
$where_clause = '';
$params = [];

if (!empty($busqueda)) {
    $where_clause = "WHERE nombre LIKE ? OR descripcion LIKE ?";
    $params = ["%$busqueda%", "%$busqueda%"];
}

try {
    // Contar total de registros
    $sql_count = "SELECT COUNT(*) as total FROM productos $where_clause";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($params);
    $total_registros = $stmt_count->fetch()['total'];
    $total_paginas = ceil($total_registros / $registros_por_pagina);

    // Obtener productos con paginación
    $sql = "SELECT * FROM productos $where_clause ORDER BY fecha_creacion DESC LIMIT $registros_por_pagina OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error al obtener productos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Productos - Sistema de Inventario</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar d-flex flex-column p-3">
                    <div class="text-center mb-4">
                        <i class="fas fa-boxes fa-2x text-white mb-2"></i>
                        <h5 class="text-white">Inventario Pro</h5>
                    </div>

                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="listar.php">
                                <i class="fas fa-box me-2"></i>Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Próximamente')">
                                <i class="fas fa-users me-2"></i>Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Próximamente')">
                                <i class="fas fa-chart-bar me-2"></i>Reportes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="alert('Próximamente')">
                                <i class="fas fa-cog me-2"></i>Configuración
                            </a>
                        </li>
                    </ul>

                    <div class="mt-auto">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user-edit me-2"></i>Perfil</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="col-md-9 col-lg-10">
                <div class="container-fluid py-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-1">
                                <i class="fas fa-box text-primary me-2"></i>Lista de Productos
                            </h2>
                            <p class="text-muted mb-0">Gestiona tu inventario de productos</p>
                        </div>
                        <a href="crear.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nuevo Producto
                        </a>
                    </div>

                    <!-- Mensajes de estado -->
                    <?php if (isset($_GET['mensaje'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo htmlspecialchars($_GET['mensaje']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Filtros y Búsqueda -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-6">
                                    <label for="buscar" class="form-label">Buscar productos</label>
                                    <input type="text" class="form-control" id="buscar" name="buscar"
                                        value="<?php echo htmlspecialchars($busqueda); ?>"
                                        placeholder="Buscar por nombre o descripción...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i>Buscar
                                        </button>
                                        <?php if (!empty($busqueda)): ?>
                                            <a href="listar.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-1"></i>Limpiar
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Mostrando</label>
                                    <div class="text-muted">
                                        <?php echo number_format($total_registros); ?> productos encontrados
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabla de Productos -->
                    <div class="card">
                        <div class="card-body">
                            <?php if (empty($productos)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                                    <h4 class="text-muted">No hay productos</h4>
                                    <p class="text-muted">
                                        <?php if (!empty($busqueda)): ?>
                                            No se encontraron productos que coincidan con tu búsqueda.
                                        <?php else: ?>
                                            Comienza agregando tu primer producto.
                                        <?php endif; ?>
                                    </p>
                                    <a href="crear.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Agregar Producto
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Producto</th>
                                                <th>Código</th>
                                                <th>Precio</th>
                                                <th>Estado</th>
                                                <th>Fecha</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($productos as $producto): ?>
                                                <tr class="<?php echo !$producto['activo'] ? 'table-secondary' : ''; ?>">
                                                    <td>
                                                        <span class="fw-bold text-primary">#<?php echo $producto['id']; ?></span>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($producto['nombre']); ?></div>
                                                            <?php if ($producto['descripcion']): ?>
                                                                <small class="text-muted">
                                                                    <?php echo htmlspecialchars(substr($producto['descripcion'], 0, 50)) . (strlen($producto['descripcion']) > 50 ? '...' : ''); ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            <?php echo htmlspecialchars($producto['codigo_producto']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold text-success">
                                                            $<?php echo number_format($producto['precio'], 2); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($producto['activo']): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?php echo date('d/m/Y', strtotime($producto['fecha_creacion'])); ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <?php if ($producto['activo']): ?>
                                                                <a href="ver.php?id=<?php echo $producto['id']; ?>"
                                                                    class="btn btn-outline-info"
                                                                    title="Ver detalles">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="editar.php?id=<?php echo $producto['id']; ?>"
                                                                    class="btn btn-outline-primary"
                                                                    title="Editar producto">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            <?php else: ?>
                                                                <button class="btn btn-outline-secondary disabled"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-placement="top"
                                                                    title="Este producto está inactivo y no puede visualizarse.">
                                                                    <i class="fas fa-eye-slash"></i>
                                                                </button>
                                                                <button class="btn btn-outline-secondary disabled"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-placement="top"
                                                                    title="Este producto está inactivo y no puede editarse.">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            <?php endif; ?>

                                                            <?php if ($producto['activo']): ?>
                                                                <a href="eliminar.php?id=<?php echo $producto['id']; ?>"
                                                                    class="btn btn-outline-danger btn-delete"
                                                                    title="Eliminar producto"
                                                                    data-product-name="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            <?php else: ?>
                                                                <button class="btn btn-outline-secondary disabled"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-placement="top"
                                                                    title="Este producto está inactivo y no puede eliminarse.">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Paginación -->
                                <?php if ($total_paginas > 1): ?>
                                    <div class="d-flex justify-content-between align-items-center mt-4">
                                        <div class="text-muted">
                                            Mostrando <?php echo ($offset + 1); ?> - <?php echo min($offset + $registros_por_pagina, $total_registros); ?>
                                            de <?php echo $total_registros; ?> productos
                                        </div>
                                        <nav>
                                            <ul class="pagination pagination-sm mb-0">
                                                <?php if ($pagina_actual > 1): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?pagina=<?php echo ($pagina_actual - 1); ?><?php echo !empty($busqueda) ? '&buscar=' . urlencode($busqueda) : ''; ?>">
                                                            <i class="fas fa-chevron-left"></i>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>

                                                <?php
                                                $rango_inicio = max(1, $pagina_actual - 2);
                                                $rango_fin = min($total_paginas, $pagina_actual + 2);

                                                for ($i = $rango_inicio; $i <= $rango_fin; $i++):
                                                ?>
                                                    <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                                                        <a class="page-link" href="?pagina=<?php echo $i; ?><?php echo !empty($busqueda) ? '&buscar=' . urlencode($busqueda) : ''; ?>">
                                                            <?php echo $i; ?>
                                                        </a>
                                                    </li>
                                                <?php endfor; ?>

                                                <?php if ($pagina_actual < $total_paginas): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?pagina=<?php echo ($pagina_actual + 1); ?><?php echo !empty($busqueda) ? '&buscar=' . urlencode($busqueda) : ''; ?>">
                                                            <i class="fas fa-chevron-right"></i>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>