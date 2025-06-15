
<?php
/**
 * Configuración general del sistema
 * - Lee variables desde un archivo .env
 * - Define constantes globales como BASE_URL
 */

// Cargar el archivo .env desde el directorio raíz del proyecto
$envPath = __DIR__ . '/../.env'; // Ajustar si .env está en otra ubicación

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Definir constantes a partir de variables de entorno
define('BASE_URL', $_ENV['BASE_URL'] ?? '/cursoPHP/TrabajoFinalGrupo2/tienda_crud/');
define('APP_VERSION', $_ENV['APP_VERSION'] ?? '1.0.0');
