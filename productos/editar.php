<?php
session_start();
require_once(__DIR__ . '/../config/database.php');

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

// Obtener los datos del producto
try {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ? AND activo = 1");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$producto) {
        $_SESSION['mensaje'] = "Producto no encontrado.";
        $_SESSION['tipo_mensaje'] = "error";
        header('Location: productos.php');
        exit();
    }
} catch(PDOException $e) {
    $_SESSION['mensaje'] = "Error al obtener el producto: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = "error";
    header('Location: productos.php');
    exit();
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $codigo_producto = trim($_POST['codigo_producto']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $imagen_actual = $producto['imagen'];
    $nueva_imagen = '';
    
    // Validaciones
    if (empty($nombre)) {
        $error = "El nombre del producto es obligatorio.";
    } elseif (empty($codigo_producto)) {
        $error = "El código del producto es obligatorio.";
    } elseif ($precio <= 0) {
        $error = "El precio debe ser mayor a cero.";
    } else {
        // Verificar si el código ya existe (excluyendo el producto actual)
        $stmt = $pdo->prepare("SELECT id FROM productos WHERE codigo_producto = ? AND id != ? AND activo = 1");
        $stmt->execute([$codigo_producto, $producto_id]);
        if ($stmt->fetch()) {
            $error = "Ya existe un producto con ese código.";
        }
    }
    
    // Procesar la imagen si se subió una nueva
    if (!isset($error) && isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $archivo = $_FILES['imagen'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($extension, $extensiones_permitidas)) {
            $error = "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
        } elseif ($archivo['size'] > 5 * 1024 * 1024) { // 5MB
            $error = "El archivo es muy grande. Máximo 5MB.";
        } else {
            // Crear directorio si no existe
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }
            
            // Generar nombre único para la imagen
            $nueva_imagen = time() . '_' . uniqid() . '.' . $extension;
            $ruta_completa = 'uploads/' . $nueva_imagen;
            
            if (!move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                $error = "Error al subir la imagen.";
            }
        }
    }
    
    // Si no hay errores, actualizar el producto
    if (!isset($error)) {
        try {
            // Determinar qué imagen usar
            $imagen_final = !empty($nueva_imagen) ? $nueva_imagen : $imagen_actual;
            
            $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, codigo_producto = ?, descripcion = ?, precio = ?, imagen = ? WHERE id = ?");
            $stmt->execute([$nombre, $codigo_producto, $descripcion, $precio, $imagen_final, $producto_id]);
            
            // Si se subió una nueva imagen y había una anterior, eliminar la anterior
            if (!empty($nueva_imagen) && !empty($imagen_actual) && file_exists('uploads/' . $imagen_actual)) {
                unlink('uploads/' . $imagen_actual);
            }
            
            $_SESSION['mensaje'] = "Producto actualizado exitosamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header('Location: ver.php?id=' . $producto_id);
            exit();
            
        } catch(PDOException $e) {
            // Si hay error y se subió una nueva imagen, eliminarla
            if (!empty($nueva_imagen) && file_exists('uploads/' . $nueva_imagen)) {
                unlink('uploads/' . $nueva_imagen);
            }
            $error = "Error al actualizar el producto: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - <?php echo htmlspecialchars($producto['nombre']); ?></title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
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
                <a class="nav-link" href="../logout.php">
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
                <li class="breadcrumb-item"><a href="ver.php?id=<?php echo $producto['id']; ?>"><?php echo htmlspecialchars($producto['nombre']); ?></a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>

        <!-- Título -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-edit"></i> Editar Producto</h2>
            <a href="ver.php?id=<?php echo $producto['id']; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Mostrar errores -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="row">
                <!-- Información básica -->
                <div class="col-md-8">
                    <div class="form-section">
                        <h4 class="mb-3"><i class="fas fa-info-circle"></i> Información del Producto</h4>
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-tag"></i> Nombre del Producto *
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="codigo_producto" class="form-label">
                                <i class="fas fa-barcode"></i> Código del Producto *
                            </label>
                            <input type="text" class="form-control" id="codigo_producto" name="codigo_producto" 
                                   value="<?php echo htmlspecialchars($producto['codigo_producto']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="precio" class="form-label">
                                <i class="fas fa-dollar-sign"></i> Precio *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio" name="precio" 
                                       step="0.01" min="0" value="<?php echo $producto['precio']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left"></i> Descripción
                            </label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" 
                                      placeholder="Descripción detallada del producto..."><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Imagen -->
                <div class="col-md-4">
                    <div class="form-section">
                        <h4 class="mb-3"><i class="fas fa-image"></i> Imagen del Producto</h4>
                        
                        <!-- Imagen actual -->
                        <?php if (!empty($producto['imagen']) && file_exists('uploads/' . $producto['imagen'])): ?>
                            <div class="mb-3">
                                <label class="form-label">Imagen Actual:</label>
                                <div class="text-center">
                                    <img src="uploads/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                         alt="Imagen actual" class="current-image">
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 10px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <p class="mt-2 text-muted">Sin imagen actual</p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Subir nueva imagen -->
                        <div class="mb-3">
                            <label for="imagen" class="form-label">Nueva Imagen (opcional):</label>
                            <div class="image-upload-area">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                                <small class="text-muted">JPG, JPEG, PNG, GIF - Máximo 5MB</small>
                            </div>
                        </div>

                        <!-- Preview de nueva imagen -->
                        <div id="imagePreview" class="text-center" style="display: none;">
                            <img id="image-preview" class="preview-image" alt="Vista previa">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="form-section">
                <h4 class="mb-3"><i class="fas fa-info"></i> Información Adicional</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <strong><i class="fas fa-calendar-plus"></i> Fecha de Creación:</strong><br>
                            <?php echo date('d/m/Y H:i:s', strtotime($producto['fecha_creacion'])); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-warning">
                            <strong><i class="fas fa-calendar-alt"></i> Última Modificación:</strong><br>
                            <?php echo date('d/m/Y H:i:s', strtotime($producto['fecha_modificacion'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="form-section">
                <div class="d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="ver.php?id=<?php echo $producto['id']; ?>" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                    <div>
                        <small class="text-muted">* Campos obligatorios</small>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>