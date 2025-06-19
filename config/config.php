<?php
/**
 * Configuración general del sistema
 * - Carga variables desde .env con función externa
 * - Define constantes globales como BASE_URL y APP_VERSION
 */

require_once __DIR__ . '/env_loader.php';

// Ruta al archivo .env
$envPath = __DIR__ . '/../.env';

try {
    loadEnv($envPath);
} catch (Exception $e) {
    error_log("No se pudo cargar .env: " . $e->getMessage());
}

// Detectar si estamos en entorno local
$isLocalhost = strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false;

// BASE_URL dinámico
define('BASE_URL', $isLocalhost
    ? '/cursoPHP/TrabajoFinalGrupo2/tienda_crud/'
    : '/');

// Versión de la app
define('APP_VERSION', $_ENV['APP_VERSION'] ?? '1.0.0');

