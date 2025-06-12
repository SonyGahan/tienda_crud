<?php
/**
 * Configuración de conexión a la base de datos
 * Este archivo contiene los parámetros de conexión a MySQL
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');        // Servidor de base de datos
define('DB_USER', 'root');             // Usuario de MySQL (por defecto en XAMPP)
define('DB_PASS', '1974Sony$');        // Contraseña (vacía por defecto en XAMPP)
define('DB_NAME', 'tienda_productos'); // Nombre de la base de datos

/**
 * Clase para manejar la conexión a la base de datos
 */
class Database {
    private $connection;

    /**
     * Constructor - Establece la conexión automáticamente
     */
    public function __construct() {
        $this->connect();
    }

    /**
     * Método para conectar a la base de datos
     * Usa PDO para mayor seguridad
     */
    private function connect() {
        try {
            // Crear conexión PDO
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->connection = new PDO($dsn, DB_USER, DB_PASS);
            
            // Configurar PDO para mostrar errores
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            // En caso de error, mostrar mensaje y detener ejecución
            die("Error de conexión: " . $e->getMessage());
        }
    }

    /**
     * Obtener la conexión para usar en otras partes del código
     * @return PDO conexión a la base de datos
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Método para cerrar la conexión
     */
    public function closeConnection() {
        $this->connection = null;
    }
}

/**
 * Función auxiliar para obtener una conexión rápidamente
 * @return PDO conexión a la base de datos
 */
function getDbConnection() {
    $database = new Database();
    return $database->getConnection();
}

/**
 * Función para ejecutar consultas SELECT de forma segura
 * @param string $query Consulta SQL
 * @param array $params Parámetros para la consulta
 * @return array Resultados de la consulta
 */
function executeQuery($query, $params = []) {
    try {
        $connection = getDbConnection();
        $stmt = $connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error en consulta: " . $e->getMessage());
        return false;
    }
}

/**
 * Función para ejecutar consultas INSERT, UPDATE, DELETE
 * @param string $query Consulta SQL
 * @param array $params Parámetros para la consulta
 * @return bool|int Éxito de la operación o ID del último registro insertado
 */
function executeUpdate($query, $params = []) {
    try {
        $connection = getDbConnection();
        $stmt = $connection->prepare($query);
        $result = $stmt->execute($params);
        
        // Si es INSERT, devolver el ID del nuevo registro
        if (strpos(strtoupper($query), 'INSERT') === 0) {
            return $connection->lastInsertId();
        }
        
        return $result;
    } catch(PDOException $e) {
        error_log("Error en actualización: " . $e->getMessage());
        return false;
    }
}

// Verificar conexión al cargar este archivo
try {
    $testConnection = new Database();
    // echo "Conexión exitosa a la base de datos"; // Descomenta para debug
} catch(Exception $e) {
    echo "Error al conectar con la base de datos: " . $e->getMessage();
}

// Crear variable $pdo para probar la conexion a la base de datos
$pdo = getDbConnection();
?>