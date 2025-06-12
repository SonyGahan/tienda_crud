</main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <!-- Información de la empresa -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-store me-2"></i>Mi Tienda
                    </h5>
                    <p class="text-muted">
                        Sistema de gestión de productos diseñado para facilitar 
                        la administración de tu inventario de manera eficiente y profesional.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light" data-bs-toggle="tooltip" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-light" data-bs-toggle="tooltip" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-light" data-bs-toggle="tooltip" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-light" data-bs-toggle="tooltip" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <!-- Enlaces rápidos -->
                <div class="col-lg-2 col-md-6 mb-3">
                    <h6 class="fw-bold mb-3">Enlaces Rápidos</h6>
                    <ul class="list-unstyled">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="mb-2">
                                <a href="dashboard.php" class="text-muted text-decoration-none">
                                    <i class="fas fa-tachometer-alt me-1"></i>Panel
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="productos/listar.php" class="text-muted text-decoration-none">
                                    <i class="fas fa-box me-1"></i>Productos
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="productos/crear.php" class="text-muted text-decoration-none">
                                    <i class="fas fa-plus me-1"></i>Nuevo Producto
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="mb-2">
                                <a href="index.php" class="text-muted text-decoration-none">
                                    <i class="fas fa-home me-1"></i>Inicio
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="login.php" class="text-muted text-decoration-none">
                                    <i class="fas fa-sign-in-alt me-1"></i>Acceso
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Información de contacto -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <h6 class="fw-bold mb-3">Contacto</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2 text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Buenos Aires, Argentina
                        </li>
                        <li class="mb-2 text-muted">
                            <i class="fas fa-phone me-2"></i>
                            +54 11 1234-5678
                        </li>
                        <li class="mb-2 text-muted">
                            <i class="fas fa-envelope me-2"></i>
                            contacto@mitienda.com
                        </li>
                        <li class="mb-2 text-muted">
                            <i class="fas fa-clock me-2"></i>
                            Lun - Vie: 9:00 - 18:00
                        </li>
                    </ul>
                </div>

                <!-- Estadísticas del sistema -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <h6 class="fw-bold mb-3">Sistema</h6>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <ul class="list-unstyled">
                            <?php
                            try {
                                // Obtener estadísticas rápidas
                                $totalProductos = executeQuery("SELECT COUNT(*) as total FROM productos WHERE activo = 1");
                                $totalCount = $totalProductos ? $totalProductos[0]['total'] : 0;
                                
                                $ultimaActualizacion = executeQuery("SELECT MAX(fecha_modificacion) as ultima FROM productos");
                                $ultimaFecha = $ultimaActualizacion ? date('d/m/Y', strtotime($ultimaActualizacion[0]['ultima'])) : 'N/A';
                            } catch (Exception $e) {
                                $totalCount = 0;
                                $ultimaFecha = 'N/A';
                            }
                            ?>
                            <li class="mb-2 text-muted">
                                <i class="fas fa-box me-2"></i>
                                Productos: <span class="fw-bold text-light"><?php echo $totalCount; ?></span>
                            </li>
                            <li class="mb-2 text-muted">
                                <i class="fas fa-user me-2"></i>
                                Usuario: <span class="fw-bold text-light"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            </li>
                            <li class="mb-2 text-muted">
                                <i class="fas fa-calendar me-2"></i>
                                Última actualización: <span class="fw-bold text-light"><?php echo $ultimaFecha; ?></span>
                            </li>
                        </ul>
                    <?php else: ?>
                        <div class="text-muted">
                            <i class="fas fa-lock me-2"></i>
                            <small>Inicia sesión para ver estadísticas del sistema</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <hr class="my-4">

            <!-- Copyright y información adicional -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        &copy; <?php echo date('Y'); ?> Mi Tienda. Todos los derechos reservados.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">
                        <i class="fas fa-code me-1"></i>
                        Desarrollado con PHP y Bootstrap 5
                        <?php if (isset($_SESSION['user_id'])): ?>
                            | Versión 1.0
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts de Bootstrap y JavaScript personalizado -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script personalizado -->
    <script src="<?php echo isset($js_path) ? $js_path : 'assets/js/script.js'; ?>"></script>
    
    <!-- Scripts adicionales si están definidos -->
    <?php if (isset($additional_scripts)): ?>
        <?php foreach ($additional_scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Script inline si está definido -->
    <?php if (isset($inline_script)): ?>
        <script>
            <?php echo $inline_script; ?>
        </script>
    <?php endif; ?>

</body>
</html>