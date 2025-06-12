<?php
session_start();
require_once(__DIR__ . '/../config/database.php');

// Validar que se haya recibido el ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID de producto inválido.";
    header("Location: dashboard.php");
    exit();
}

$id = intval($_GET['id']);

try {
    // Eliminar lógicamente (marcar como inactivo)
    $sql = "UPDATE productos SET activo = 0, fecha_modificacion = NOW() WHERE id = ?";
    $resultado = executeUpdate($sql, [$id]);

    if ($resultado) {
        $_SESSION['success_message'] = "Producto eliminado correctamente.";
    } else {
        $_SESSION['error_message'] = "No se pudo eliminar el producto.";
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
}

header("Location: dashboard.php");
exit();
