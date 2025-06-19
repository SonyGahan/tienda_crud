<?php
session_start();
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../config/config.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

$errores = [];
$datos = [];

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger y limpiar datos
    $datos['nombre'] = trim($_POST['nombre'] ?? '');
    $datos['codigo_producto'] = trim($_POST['codigo_producto'] ?? '');
    $datos['descripcion'] = trim($_POST['descripcion'] ?? '');
    $datos['precio'] = trim($_POST['precio'] ?? '');
    $datos['activo'] = isset($_POST['activo']) ? 1 : 0;

    // Validaciones
    if (empty($datos['nombre'])) {
        $errores['nombre'] = 'El nombre del producto es obligatorio';
    } elseif (strlen($datos['nombre']) > 200) {
        $errores['nombre'] = 'El nombre no puede tener más de 200 caracteres';
    }

    if (empty($datos['codigo_producto'])) {
        $errores['codigo_producto'] = 'El código del producto es obligatorio';
    } elseif (strlen($datos['codigo_producto']) > 50) {
        $errores['codigo_producto'] = 'El código no puede tener más de 50 caracteres';
    } else {
        // Verificar que el código no exista
        try {
            $stmt = $pdo->prepare("SELECT id FROM productos WHERE codigo_producto = ?");
            $stmt->execute([$datos['codigo_producto']]);
            if ($stmt->fetch()) {
                $errores['codigo_producto'] = 'Este código de producto ya existe';
            }
        } catch (PDOException $e) {
            $errores['codigo_producto'] = 'Error al verificar el código';
        }
    }

    if (empty($datos['precio'])) {
        $errores['precio'] = 'El precio es obligatorio';
    } elseif (!is_numeric($datos['precio']) || $datos['precio'] < 0) {
        $errores['precio'] = 'El precio debe ser un número válido mayor o igual a 0';
    } elseif ($datos['precio'] > 999999.99) {
        $errores['precio'] = 'El precio no puede ser mayor a $999,999.99';
    }

    if (strlen($datos['descripcion']) > 1000) {
        $errores['descripcion'] = 'La descripción no puede tener más de 1000 caracteres';
    }

    // Procesar imagen si se subió
    $nombre_imagen = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $archivo = $_FILES['imagen'];
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $tamaño_maximo = 5 * 1024 * 1024; // 5MB

        // Validar extensión
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $extensiones_permitidas)) {
            $errores['imagen'] = 'Solo se permiten archivos JPG, JPEG, PNG, GIF y WEBP';
        }

        // Validar tamaño
        if ($archivo['size'] > $tamaño_maximo) {
            $errores['imagen'] = 'La imagen no puede ser mayor a 5MB';
        }

        // Si no hay errores, procesar la imagen
        if (!isset($errores['imagen'])) {
            $directorio_destino = 'uploads/';

            // Crear directorio si no existe
            if (!is_dir($directorio_destino)) {
                mkdir($directorio_destino, 0755, true);
            }

            // Generar nombre único para la imagen
            $nombre_imagen = time() . '_' . uniqid() . '.' . $extension;
            $ruta_completa = $directorio_destino . $nombre_imagen;

            // Mover archivo
            if (!move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                $errores['imagen'] = 'Error al subir la imagen';
                $nombre_imagen = null;
            }
        }
    }

    // Si no hay errores, insertar en la base de datos
    if (empty($errores)) {
        try {
            $sql = "INSERT INTO productos (nombre, codigo_producto, descripcion, precio, imagen, activo) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $datos['nombre'],
                $datos['codigo_producto'],
                $datos['descripcion'],
                $datos['precio'],
                $nombre_imagen,
                $datos['activo']
            ]);

            // Redirigir con mensaje de éxito
            header("Location: listar.php?mensaje=" . urlencode("Producto creado exitosamente"));
            exit();
        } catch (PDOException $e) {
            $errores['general'] = "Error al crear el producto: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto - Sistema de Inventario</title>
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
                                <i class="fas fa-plus-circle text-success me-2"></i>Crear Nuevo Producto
                            </h2>
                            <p class="text-muted mb-0">Agrega un nuevo producto a tu inventario</p>
                        </div>
                        <a href="listar.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                        </a>
                    </div>

                    <!-- Mensajes de error general -->
                    <?php if (isset($errores['general'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($errores['general']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Formulario -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Información del Producto
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                        <div class="row">
                                            <!-- Nombre del producto -->
                                            <div class="col-md-8 mb-3">
                                                <div class="form-floating">
                                                    <input type="text"
                                                        class="form-control <?php echo isset($errores['nombre']) ? 'is-invalid' : ''; ?>"
                                                        id="nombre"
                                                        name="nombre"
                                                        value="<?php echo htmlspecialchars($datos['nombre'] ?? ''); ?>"
                                                        placeholder="Nombre del producto"
                                                        required>
                                                    <label for="nombre">
                                                        <i class="fas fa-tag me-1"></i>Nombre del Producto *
                                                    </label>
                                                    <?php if (isset($errores['nombre'])): ?>
                                                        <div class="invalid-feedback">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                                            <?php echo $errores['nombre']; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Código del producto -->
                                            <div class="col-md-4 mb-3">
                                                <div class="form-floating">
                                                    <input type="text"
                                                        class="form-control <?php echo isset($errores['codigo_producto']) ? 'is-invalid' : ''; ?>"
                                                        id="codigo_producto"
                                                        name="codigo_producto"
                                                        value="<?php echo htmlspecialchars($datos['codigo_producto'] ?? ''); ?>"
                                                        placeholder="Código"
                                                        required>
                                                    <label for="codigo_producto">
                                                        <i class="fas fa-barcode me-1"></i>Código *
                                                    </label>
                                                    <?php if (isset($errores['codigo_producto'])): ?>
                                                        <div class="invalid-feedback">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                                            <?php echo $errores['codigo_producto']; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Precio -->
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="number"
                                                        class="form-control <?php echo isset($errores['precio']) ? 'is-invalid' : ''; ?>"
                                                        id="precio"
                                                        name="precio"
                                                        value="<?php echo htmlspecialchars($datos['precio'] ?? ''); ?>"
                                                        placeholder="0.00"
                                                        step="0.01"
                                                        min="0"
                                                        required>
                                                    <label for="precio">
                                                        <i class="fas fa-dollar-sign me-1"></i>Precio *
                                                    </label>
                                                    <?php if (isset($errores['precio'])): ?>
                                                        <div class="invalid-feedback">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                                            <?php echo $errores['precio']; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Estado -->
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <select class="form-select" id="estado" name="activo">
                                                        <option value="1" <?php echo (isset($datos['activo']) && $datos['activo'] == 1) ? 'selected' : ''; ?>>
                                                            Activo
                                                        </option>
                                                        <option value="0" <?php echo (isset($datos['activo']) && $datos['activo'] == 0) ? 'selected' : ''; ?>>
                                                            Inactivo
                                                        </option>
                                                    </select>
                                                    <label for="estado">
                                                        <i class="fas fa-toggle-on me-1"></i>Estado
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Descripción -->
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <textarea class="form-control <?php echo isset($errores['descripcion']) ? 'is-invalid' : ''; ?>"
                                                    id="descripcion"
                                                    name="descripcion"
                                                    placeholder="Descripción del producto"
                                                    style="height: 120px;"><?php echo htmlspecialchars($datos['descripcion'] ?? ''); ?></textarea>
                                                <label for="descripcion">
                                                    <i class="fas fa-align-left me-1"></i>Descripción
                                                </label>
                                                <?php if (isset($errores['descripcion'])): ?>
                                                    <div class="invalid-feedback">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        <?php echo $errores['descripcion']; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Botones -->
                                        <div class="d-flex justify-content-between">
                                            <a href="<?php echo BASE_URL; ?>productos/listar.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-2"></i>Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save me-2"></i>Crear Producto
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Panel lateral - Imagen -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-image me-2"></i>Imagen del Producto
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Área de subida de archivo -->
                                    <div class="file-upload-area" onclick="document.getElementById('imagen').click();">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">Haz clic para seleccionar una imagen</h6>
                                        <p class="text-muted small mb-0">
                                            JPG, PNG, GIF, WEBP<br>
                                            Máximo 5MB
                                        </p>
                                    </div>

                                    <input type="file"
                                        class="form-control d-none <?php echo isset($errores['imagen']) ? 'is-invalid' : ''; ?>"
                                        id="imagen"
                                        name="imagen"
                                        accept="image/*">

                                    <?php if (isset($errores['imagen'])): ?>
                                        <div class="invalid-feedback d-block mt-2">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            <?php echo $errores['imagen']; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Preview de la imagen -->
                                    <div id="preview-container" class="preview-container d-none">
                                        <img id="preview-image" class="preview-image" alt="Preview">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearImage()">
                                                <i class="fas fa-trash me-1"></i>Quitar imagen
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Información adicional -->
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            La imagen es opcional. Si no subes una imagen, se usará una imagen por defecto.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Tips -->
                            <div class="card mt-3">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-lightbulb me-2"></i>Consejos
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0 small">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Usa nombres descriptivos y únicos
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            El código debe ser único en el sistema
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Incluye una descripción detallada
                                        </li>
                                        <li class="mb-0">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Verifica el precio antes de guardar
                                        </li>
                                    </ul>
                                </div>
                            </div>
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