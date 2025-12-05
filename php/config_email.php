<?php

// Configuración del servidor SMTP de Gmail
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls'); // tls o ssl

// Credenciales de Gmail (usa variables de entorno en producción, valores locales para desarrollo)
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: 'lalehurtado.g@gmail.com');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: 'xoih gyvj jcda rtqd');

// Correo de destino (donde el abogado recibirá las notificaciones)
define('EMAIL_DESTINO', getenv('EMAIL_DESTINO') ?: 'laurahvalleavanza@gmail.com');

// Nombre que aparecerá como remitente
define('EMAIL_REMITENTE_NOMBRE', 'Sistema de Consultas Legales');

// Asunto del correo
define('EMAIL_ASUNTO', 'Nueva Consulta Legal - Formulario de Contacto');

?>

