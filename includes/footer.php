<?php
if (!defined('APP_VERSION')) {
    require_once __DIR__ . '/../config/config.php';
}
?>
           
           </main> <!-- Cierre de main que comenzó en header -->
            </div> <!-- Cierre de .row -->
            </div> <!-- Cierre de .container-fluid -->

            <footer class="footer bg-dark text-light py-4 mt-5">
                <div class="container">
                    <div class="row">
                        <!-- Información de la empresa -->
                        <div class="col-lg-4 col-md-6 mb-3">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-store me-2"></i>Mi Tienda
                            </h5>
                            <p class="text-light">
                                Sistema de gestión de productos diseñado para facilitar
                                la administración de tu inventario de manera eficiente y profesional.
                            </p>
                            <div class="d-flex gap-3">
                                <a href="#" class="text-light"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="text-light"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-light"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="text-light"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>

                        <!-- Enlaces rápidos -->
                        <div class="col-lg-2 col-md-6 mb-3">
                            <h6 class="fw-bold mb-3">Enlaces Rápidos</h6>
                            <ul class="list-unstyled">
                                <?php if (isset($_SESSION['usuario_id'])): ?>
                                    <li><a href="<?php echo BASE_URL; ?>dashboard.php" class="text-light text-decoration-none"><i class="fas fa-tachometer-alt me-1"></i>Panel</a></li>
                                    <li><a href="<?php echo BASE_URL; ?>productos/listar.php" class="text-light text-decoration-none"><i class="fas fa-box me-1"></i>Productos</a></li>
                                    <li><a href="<?php echo BASE_URL; ?>productos/crear.php" class="text-light text-decoration-none"><i class="fas fa-plus me-1"></i>Nuevo Producto</a></li>
                                <?php else: ?>
                                    <li><a href="<?php echo BASE_URL; ?>index.php" class="text-light text-decoration-none"><i class="fas fa-home me-1"></i>Inicio</a></li>
                                    <li><a href="<?php echo BASE_URL; ?>login.php" class="text-light text-decoration-none"><i class="fas fa-sign-in-alt me-1"></i>Acceso</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <!-- Información de contacto -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h6 class="fw-bold mb-3">Contacto</h6>
                            <ul class="list-unstyled text-light">
                                <li><i class="fas fa-map-marker-alt me-2"></i>Buenos Aires, Argentina</li>
                                <li><i class="fas fa-phone me-2"></i>+54 11 1234-5678</li>
                                <li><i class="fas fa-envelope me-2"></i>contacto@mitienda.com</li>
                                <li><i class="fas fa-clock me-2"></i>Lun - Vie: 9:00 - 18:00</li>
                            </ul>
                        </div>

                        <!-- Estadísticas del sistema -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h6 class="fw-bold mb-3">Sistema</h6>
                            <?php if (isset($_SESSION['usuario_id'])): ?>
                                <?php
                                require_once __DIR__ . '/../config/database.php';
                                $totalCount = executeQuery("SELECT COUNT(*) as total FROM productos WHERE activo = 1", [], false)['total'] ?? 0;
                                $ultimaFecha = executeQuery("SELECT MAX(fecha_modificacion) as ultima FROM productos", [], false)['ultima'] ?? null;
                                ?>
                                <ul class="list-unstyled text-light">
                                    <li><i class="fas fa-box me-2"></i>Productos: <span class="fw-bold text-light"><?php echo $totalCount; ?></span></li>
                                    <li><i class="fas fa-user me-2"></i>Usuario: <span class="fw-bold text-light"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span></li>
                                    <li><i class="fas fa-calendar me-2"></i>Última actualización: <span class="fw-bold text-light"><?php echo $ultimaFecha ? date('d/m/Y', strtotime($ultimaFecha)) : 'N/A'; ?></span></li>
                                </ul>
                            <?php else: ?>
                                <div class="text-light"><i class="fas fa-lock me-2"></i><small>Inicia sesión para ver estadísticas</small></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-light">
                            &copy; <?php echo date('Y'); ?> Mi Tienda. Todos los derechos reservados.
                        </div>
                        <div class="col-md-6 text-md-end text-light">
                            <i class="fas fa-code me-1"></i>Desarrollado con PHP y Bootstrap 5 | Versión <?php echo APP_VERSION; ?>
                        </div>
                    </div>
                </div>
            </footer>

            <!-- Scripts -->
            <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
            <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
            </body>

            </html>