// Manejo del formulario de contacto con AJAX
document.addEventListener('DOMContentLoaded', function() {
    const formulario = document.querySelector('.formulario-cita');
    const mensajeContainer = document.getElementById('mensaje-container');
    const btnSubmit = document.querySelector('.btn-submit');
    
    if (!formulario) {
        return;
    }
    
    // Función para mostrar mensajes
    function mostrarMensaje(mensaje, tipo) {
        // Limpiar mensajes anteriores
        if (mensajeContainer) {
            mensajeContainer.innerHTML = '';
            mensajeContainer.className = 'mensaje-container';
            
            // Crear elemento de mensaje
            const mensajeDiv = document.createElement('div');
            mensajeDiv.className = `mensaje mensaje-${tipo}`;
            mensajeDiv.textContent = mensaje;
            
            // Agregar botón de cerrar
            const cerrarBtn = document.createElement('button');
            cerrarBtn.className = 'cerrar-mensaje';
            cerrarBtn.innerHTML = '&times;';
            cerrarBtn.setAttribute('aria-label', 'Cerrar mensaje');
            cerrarBtn.onclick = function() {
                mensajeContainer.innerHTML = '';
                mensajeContainer.className = '';
            };
            
            mensajeDiv.appendChild(cerrarBtn);
            mensajeContainer.appendChild(mensajeDiv);
            mensajeContainer.className = 'mensaje-container mostrar';
            
            // Auto-ocultar mensaje de éxito después de 5 segundos
            if (tipo === 'exito') {
                setTimeout(function() {
                    if (mensajeContainer) {
                        mensajeContainer.innerHTML = '';
                        mensajeContainer.className = '';
                    }
                }, 5000);
            }
            
            // Scroll suave al mensaje
            mensajeContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
    
    // Función para deshabilitar/habilitar el botón de envío
    function toggleButton(deshabilitar) {
        if (btnSubmit) {
            btnSubmit.disabled = deshabilitar;
            if (deshabilitar) {
                btnSubmit.textContent = 'Enviando...';
                btnSubmit.style.opacity = '0.6';
                btnSubmit.style.cursor = 'not-allowed';
            } else {
                btnSubmit.textContent = 'Agendar Cita';
                btnSubmit.style.opacity = '1';
                btnSubmit.style.cursor = 'pointer';
            }
        }
    }
    
    // Manejar el envío del formulario
    formulario.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevenir envío tradicional
        
        // Obtener datos del formulario
        const formData = new FormData(formulario);
        
        // Deshabilitar botón durante el envío
        toggleButton(true);
        
        // Enviar datos con Fetch API
        fetch('php/procesar_cita.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Verificar si la respuesta es JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // Si no es JSON, intentar leer como texto
                return response.text().then(text => {
                    throw new Error('Respuesta inesperada del servidor');
                });
            }
        })
        .then(data => {
            if (data.success) {
                // Éxito: mostrar mensaje y limpiar formulario
                mostrarMensaje(data.message, 'exito');
                formulario.reset();
            } else {
                // Error: mostrar mensaje de error
                mostrarMensaje(data.message || 'Ocurrió un error al procesar tu solicitud', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error de conexión. Por favor, verifica tu conexión a internet e intenta nuevamente.', 'error');
        })
        .finally(() => {
            // Rehabilitar botón
            toggleButton(false);
        });
    });
});

