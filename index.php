<?php
session_start();
require_once __DIR__ . '/config/config.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit();
}

$page_title = 'Bienvenida - Mi Tienda';
include __DIR__ . '/includes/header.php';
?>

<section class="py-5 bg-white">
    <div class="container text-center fade-in">
        <h1 class="display-4 fw-bold text-gradient">Bienvenido a Mi Tienda</h1>
        <p class="lead text-muted mt-3 mb-4">
            Gestioná tus productos de forma eficiente, moderna y segura.<br>
            Accedé al sistema para comenzar a trabajar con tu inventario.
        </p>

        <a href="login.php" class="btn btn-primary btn-lg shadow">
            <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
        </a>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <i class="fas fa-box fa-3x text-primary mb-3"></i>
                <h5 class="fw-bold">Gestión de Productos</h5>
                <p class="text-muted">Creá, editá y organizá tus productos fácilmente.</p>
            </div>
            <div class="col-md-4 mb-4">
                <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                <h5 class="fw-bold">Reportes</h5>
                <p class="text-muted">Visualizá estadísticas y tomá decisiones inteligentes.</p>
            </div>
            <div class="col-md-4 mb-4">
                <i class="fas fa-user-shield fa-3x text-warning mb-3"></i>
                <h5 class="fw-bold">Acceso Seguro</h5>
                <p class="text-muted">Tu información protegida con acceso personalizado.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
