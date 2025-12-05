<?php
// Configurar headers para JSON y CORS
header('Content-Type: application/json; charset=utf-8');

// Incluir archivo de conexión
require_once 'conexion.php';

// Incluir configuración de correo
require_once 'config_email.php';

// Incluir PHPMailer (instalación manual)
require_once 'vendor/PHPMailer/src/Exception.php';
require_once 'vendor/PHPMailer/src/PHPMailer.php';
require_once 'vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Obtener y limpiar los datos del formulario
$nombre_completo = trim($_POST['nombre_completo'] ?? '');
$correo_electronico = trim($_POST['correo_electronico'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$descripcion_caso = trim($_POST['descripcion_caso'] ?? '');

// Validar campos requeridos
$errores = [];

if (empty($nombre_completo)) {
    $errores[] = "El nombre completo es requerido";
}

if (empty($correo_electronico)) {
    $errores[] = "El correo electrónico es requerido";
} elseif (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El correo electrónico no es válido";
}

if (empty($descripcion_caso)) {
    $errores[] = "La descripción del caso es requerida";
}

// Si hay errores, devolver JSON con errores
if (!empty($errores)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => implode('. ', $errores),
        'errors' => $errores
    ]);
    exit;
}

// ============================================
// PASO 1: ENVIAR CORREO ELECTRÓNICO PRIMERO
// ============================================
$correo_enviado = false;
$error_correo = '';

try {
    $mail = new PHPMailer(true);
    
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port = SMTP_PORT;
    $mail->CharSet = 'UTF-8';
    
    // Remitente
    $mail->setFrom(SMTP_USERNAME, EMAIL_REMITENTE_NOMBRE);
    
    // Destinatario (el abogado)
    $mail->addAddress(EMAIL_DESTINO);
    
    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = EMAIL_ASUNTO;
    
    // Cuerpo del mensaje HTML
    $mail->Body = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .container { 
                max-width: 600px; 
                margin: 20px auto; 
                background-color: #ffffff;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .header { 
                background-color: #4CAF50; 
                color: white; 
                padding: 30px 20px; 
                text-align: center; 
            }
            .header h2 {
                margin: 0;
                font-size: 24px;
            }
            .content { 
                background-color: #ffffff; 
                padding: 30px 20px; 
            }
            .field { 
                margin-bottom: 20px; 
                border-bottom: 1px solid #e0e0e0;
                padding-bottom: 15px;
            }
            .field:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }
            .label { 
                font-weight: bold; 
                color: #555; 
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 8px;
            }
            .value { 
                margin-top: 5px; 
                padding: 12px; 
                background-color: #f9f9f9; 
                border-left: 4px solid #4CAF50;
                border-radius: 4px;
                color: #333;
                font-size: 15px;
            }
            .footer {
                background-color: #f9f9f9;
                padding: 20px;
                text-align: center;
                color: #666;
                font-size: 12px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Nueva Consulta Legal</h2>
                <p style='margin: 10px 0 0 0; opacity: 0.9;'>Has recibido una nueva solicitud de consulta</p>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Nombre Completo:</div>
                    <div class='value'>" . htmlspecialchars($nombre_completo) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Correo Electrónico:</div>
                    <div class='value'>" . htmlspecialchars($correo_electronico) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Teléfono:</div>
                    <div class='value'>" . htmlspecialchars($telefono ?: 'No proporcionado') . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Descripción del Caso:</div>
                    <div class='value'>" . nl2br(htmlspecialchars($descripcion_caso)) . "</div>
                </div>
            </div>
            <div class='footer'>
                <p>Este correo fue generado automáticamente por el sistema de consultas legales.</p>
                <p>Fecha: " . date('d/m/Y H:i:s') . "</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Versión texto plano (para clientes de correo que no soportan HTML)
    $mail->AltBody = "Nueva Consulta Legal\n\n" .
                     "Has recibido una nueva solicitud de consulta legal.\n\n" .
                     "Nombre Completo: " . $nombre_completo . "\n" .
                     "Correo Electrónico: " . $correo_electronico . "\n" .
                     "Teléfono: " . ($telefono ?: 'No proporcionado') . "\n" .
                     "Descripción del Caso: " . $descripcion_caso . "\n\n" .
                     "Fecha: " . date('d/m/Y H:i:s');
    
    // Enviar correo
    $mail->send();
    $correo_enviado = true;
    
} catch (Exception $e) {
    $error_correo = "Error al enviar correo: " . $mail->ErrorInfo;
    // Continuamos con el proceso aunque falle el correo
    // El error se registrará en los logs pero no se mostrará al usuario
    error_log("Error enviando correo: " . $error_correo);
}

// ============================================
// PASO 2: GUARDAR EN BASE DE DATOS
// ============================================
$sql = "INSERT INTO citas_legales (nombre_completo, correo_electronico, telefono, descripcion_caso) 
        VALUES (?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al preparar la consulta: ' . $conexion->error
    ]);
    exit;
}

// Vincular parámetros
$stmt->bind_param("ssss", $nombre_completo, $correo_electronico, $telefono, $descripcion_caso);

// Ejecutar la consulta
if ($stmt->execute()) {
    // Éxito - devolver JSON con mensaje de éxito
    $mensaje = '¡Tu consulta ha sido registrada exitosamente! Te contactaremos pronto.';
    
    // Si el correo no se envió, se registra en logs pero no se informa al usuario
    if (!$correo_enviado) {
        error_log("Correo no enviado para: " . $correo_electronico . " - " . $error_correo);
    }
    
    echo json_encode([
        'success' => true,
        'message' => $mensaje
    ]);
} else {
    // Error al insertar
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar la cita. Por favor, intenta nuevamente.'
    ]);
}

// Cerrar statement y conexión
$stmt->close();
$conexion->close();
?>

