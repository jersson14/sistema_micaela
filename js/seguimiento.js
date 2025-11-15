$(document).ready(function() {
    
    // Manejar el envío del formulario
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        
        const boletaNro = $('#boletaInput').val().trim();
        
        if(boletaNro === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Campo requerido',
                text: 'Por favor ingresa el número de boleta',
                confirmButtonColor: '#023D77'
            });
            return;
        }
        
        buscarEncomienda(boletaNro);
    });
    
    function buscarEncomienda(boletaNro) {
        // Mostrar loading
        $('#loading').addClass('show');
        $('#resultsCard').removeClass('show');
        
        $.ajax({
            url: 'controller/encomiendas/controlador_seguimiento.php',
            type: 'POST',
            data: { boleta_nro: boletaNro },
            dataType: 'json',
            success: function(response) {
                $('#loading').removeClass('show');
                
                if(response.error) {
                    mostrarNoResultados();
                } else if(response.encomienda) {
                    mostrarResultados(response);
                } else {
                    mostrarNoResultados();
                }
            },
            error: function() {
                $('#loading').removeClass('show');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonColor: '#023D77'
                });
            }
        });
    }
    
    function mostrarResultados(data) {
        const enc = data.encomienda;
        const historial = data.historial || [];
        
        // Formatear estado para clase CSS
        const estadoClass = enc.estado_encomienda.replace(/ /g, '-');
        
        let html = `
            <div class="encomienda-info">
                <div class="info-item">
                    <span class="info-label">Número de Boleta</span>
                    <span class="info-value">${enc.boleta_nro || 'N/A'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Estado Actual</span>
                    <span class="info-value">
                        <span class="status-badge status-${estadoClass}">${enc.estado_encomienda}</span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Fecha de Envío</span>
                    <span class="info-value">${formatearFecha(enc.fecha_hora)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Origen</span>
                    <span class="info-value">${enc.origen || 'N/A'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Destino</span>
                    <span class="info-value">${enc.destino || 'N/A'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Conductor</span>
                    <span class="info-value">${enc.conductor || 'N/A'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Remitente</span>
                    <span class="info-value">${enc.emisor_nombre || 'N/A'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Destinatario</span>
                    <span class="info-value">${enc.receptor_nombre || 'N/A'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Descripción</span>
                    <span class="info-value">${enc.descripcion || 'N/A'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Monto Total</span>
                    <span class="info-value">S/ ${parseFloat(enc.pago || 0).toFixed(2)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Por Pagar</span>
                    <span class="info-value">S/ ${parseFloat(enc.por_pagar || 0).toFixed(2)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Estado de Pago</span>
                    <span class="info-value">${enc.estado_pago || 'N/A'}</span>
                </div>
            </div>
        `;
        
        // Agregar historial si existe
        if(historial.length > 0) {
            html += `
                <div class="timeline-section">
                    <div class="timeline-title">
                        <i class="fas fa-history"></i>
                        Historial de Estados
                    </div>
                    <div class="timeline">
            `;
            
            historial.forEach((item, index) => {
                const isActive = index === 0;
                const estadoItemClass = item.estado.replace(/ /g, '-');
                
                html += `
                    <div class="timeline-item">
                        <div class="timeline-dot ${isActive ? 'active' : ''}"></div>
                        <div class="timeline-content">
                            <div class="timeline-status">
                                <span class="status-badge status-${estadoItemClass}">${item.estado}</span>
                            </div>
                            <div class="timeline-date">
                                <i class="far fa-clock"></i> ${formatearFecha(item.created_at)}
                            </div>
                            ${item.observacion ? `<div class="timeline-observation">${item.observacion}</div>` : ''}
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        }
        
        // Agregar mensaje informativo según el estado
        if(enc.estado_encomienda === 'ENTREGADO') {
            html += `
                <div style="margin-top: 2rem; padding: 1.5rem; background: #d1e7dd; border-radius: 12px; border-left: 4px solid #0f5132;">
                    <i class="fas fa-check-circle" style="color: #0f5132; font-size: 1.5rem; margin-right: 0.5rem;"></i>
                    <strong style="color: #0f5132;">¡Tu encomienda ha sido entregada!</strong>
                </div>
            `;
        } else if(enc.estado_encomienda === 'EN AGENCIA') {
            html += `
                <div style="margin-top: 2rem; padding: 1.5rem; background: #d1ecf1; border-radius: 12px; border-left: 4px solid #0c5460;">
                    <i class="fas fa-info-circle" style="color: #0c5460; font-size: 1.5rem; margin-right: 0.5rem;"></i>
                    <strong style="color: #0c5460;">Tu encomienda está en agencia y lista para ser recogida</strong>
                </div>
            `;
        } else if(enc.estado_encomienda === 'EN TRANSITO') {
            html += `
                <div style="margin-top: 2rem; padding: 1.5rem; background: #cfe2ff; border-radius: 12px; border-left: 4px solid #084298;">
                    <i class="fas fa-truck" style="color: #084298; font-size: 1.5rem; margin-right: 0.5rem;"></i>
                    <strong style="color: #084298;">Tu encomienda está en camino</strong>
                </div>
            `;
        }
        
        $('#resultsContent').html(html);
        $('#resultsCard').addClass('show');
    }
    
    function mostrarNoResultados() {
        const html = `
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>No se encontró la encomienda</h3>
                <p>Verifica que el número de boleta sea correcto</p>
            </div>
        `;
        
        $('#resultsContent').html(html);
        $('#resultsCard').addClass('show');
    }
    
    function formatearFecha(fecha) {
        if(!fecha) return 'N/A';
        
        const date = new Date(fecha);
        const opciones = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        
        return date.toLocaleDateString('es-ES', opciones);
    }
    
});
