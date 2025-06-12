<?php
// Archivo temporal para generar hash de contraseña
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Contraseña: " . $password . "<br>";
echo "Hash generado: " . $hash . "<br><br>";

echo "SQL para actualizar el usuario:<br>";
echo "UPDATE usuarios SET password = '$hash' WHERE email = 'admin@tienda.com';<br>";

// Verificar que funciona
if (password_verify($password, $hash)) {
    echo "<br><span style='color: green;'>✓ Verificación exitosa</span>";
} else {
    echo "<br><span style='color: red;'>✗ Error en verificación</span>";
}
?>