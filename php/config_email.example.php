<?php
/**
 * Archivo de ejemplo para config_email.php
 * 
 * Copia este archivo como config_email.php y completa con tus datos
 * O configura las variables de entorno en Vercel
 */

// Configuración del servidor SMTP de Gmail
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls'); // tls o ssl

// Credenciales de Gmail (usa variables de entorno en producción)
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: 'tu_correo@gmail.com');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: 'tu_contraseña_de_aplicacion');

// Correo de destino (donde el abogado recibirá las notificaciones)
define('EMAIL_DESTINO', getenv('EMAIL_DESTINO') ?: 'leidyvr@gmail.com');

// Nombre que aparecerá como remitente
define('EMAIL_REMITENTE_NOMBRE', 'Sistema de Consultas Legales');

// Asunto del correo
define('EMAIL_ASUNTO', 'Nueva Consulta Legal - Formulario de Contacto');

?>

