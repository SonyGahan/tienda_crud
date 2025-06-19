<?php
/**
 * Configuración general del sistema
 * - Detecta si estamos en local o en producción
 * - Define BASE_URL dinámicamente
 * - Carga variables desde .env (si existiera)
 */

// Cargar variables desde .env si existe
$envPath = __DIR__ . '/../.env';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Detectar si estamos en localhost
$isLocalhost = strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false;

// BASE_URL se adapta según el entorno
define('BASE_URL', $isLocalhost
    ? '/cursoPHP/TrabajoFinalGrupo2/tienda_crud/' // path en la máquina local
    : '/'); // path raíz en AlwaysData

// Otras constantes útiles
define('APP_VERSION', $_ENV['APP_VERSION'] ?? '1.0.0');
