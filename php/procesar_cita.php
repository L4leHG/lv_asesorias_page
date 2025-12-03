<?php
// Configurar headers para JSON y CORS
header('Content-Type: application/json; charset=utf-8');

// Incluir archivo de conexión
require_once 'conexion.php';

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

// Preparar la consulta SQL usando prepared statements para prevenir SQL injection
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
    echo json_encode([
        'success' => true,
        'message' => '¡Tu consulta ha sido registrada exitosamente! Te contactaremos pronto.'
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

