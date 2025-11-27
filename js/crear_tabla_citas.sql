CREATE TABLE IF NOT EXISTS citas_legales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(255) NOT NULL,
    correo_electronico VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    descripcion_caso TEXT NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(50) DEFAULT 'pendiente',
    INDEX idx_email (correo_electronico),
    INDEX idx_fecha (fecha_registro)
);