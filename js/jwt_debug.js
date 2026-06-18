/**
 * JWT Debug - Modo Prueba
 * Muestra contador visual en pantalla
 * SOLO PARA PRUEBAS - Remover en producción
 */

(function() {
    'use strict';

    // Crear elemento visual para el contador
    function createDebugPanel() {
        const panel = document.createElement('div');
        panel.id = 'jwt-debug-panel';
        panel.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.9);
            color: #00ff00;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            z-index: 99999;
            min-width: 250px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            border: 2px solid #00ff00;
        `;
        
        panel.innerHTML = `
            <div style="font-weight: bold; margin-bottom: 10px; color: #ffff00;">
                🧪 JWT DEBUG MODE
            </div>
            <div id="jwt-status">Cargando...</div>
            <div id="jwt-timer" style="font-size: 18px; margin-top: 10px; color: #00ff00;"></div>
            <div id="jwt-inactive" style="margin-top: 5px; font-size: 12px; color: #00ffff;"></div>
            <div id="jwt-action" style="margin-top: 10px; font-size: 12px; color: #ff9900;"></div>
        `;
        
        document.body.appendChild(panel);
    }

    // Actualizar panel de debug
    function updateDebugPanel() {
        const statusDiv = document.getElementById('jwt-status');
        const timerDiv = document.getElementById('jwt-timer');
        const inactiveDiv = document.getElementById('jwt-inactive');
        const actionDiv = document.getElementById('jwt-action');
        
        if (!statusDiv) return;

        const tokenExpires = localStorage.getItem('token_expires');
        
        if (!tokenExpires) {
            statusDiv.innerHTML = '❌ No hay token';
            timerDiv.innerHTML = '--:--';
            if (inactiveDiv) inactiveDiv.innerHTML = '';
            actionDiv.innerHTML = 'Redirigiendo al login...';
            return;
        }

        const expiresAt = parseInt(tokenExpires);
        const now = Date.now();
        const timeLeft = expiresAt - now;
        
        if (timeLeft <= 0) {
            statusDiv.innerHTML = '❌ Token EXPIRADO';
            timerDiv.innerHTML = '00:00';
            timerDiv.style.color = '#ff0000';
            if (inactiveDiv) inactiveDiv.innerHTML = '';
            actionDiv.innerHTML = '🚪 Cerrando sesión...';
            return;
        }

        const minutesLeft = Math.floor(timeLeft / 1000 / 60);
        const secondsLeft = Math.floor((timeLeft / 1000) % 60);
        
        // Calcular tiempo de inactividad (si JWTHandler está disponible)
        let inactiveTime = 0;
        let inactiveStr = '';
        if (window.JWTHandler && window.JWTHandler.lastActivity) {
            inactiveTime = Date.now() - window.JWTHandler.lastActivity;
            const inactiveMinutes = Math.floor(inactiveTime / 1000 / 60);
            const inactiveSeconds = Math.floor((inactiveTime / 1000) % 60);
            inactiveStr = `${inactiveMinutes}:${inactiveSeconds.toString().padStart(2, '0')}`;
            
            if (inactiveDiv) {
                if (inactiveTime > 4 * 60 * 1000) { // Más de 4 minutos inactivo
                    inactiveDiv.innerHTML = `⚠️ Inactivo: ${inactiveStr} (cerrará en ${5 - inactiveMinutes}m)`;
                    inactiveDiv.style.color = '#ff0000';
                } else if (inactiveTime > 3 * 60 * 1000) { // Más de 3 minutos
                    inactiveDiv.innerHTML = `⚡ Inactivo: ${inactiveStr}`;
                    inactiveDiv.style.color = '#ff9900';
                } else {
                    inactiveDiv.innerHTML = `✅ Activo (última actividad: ${inactiveStr})`;
                    inactiveDiv.style.color = '#00ffff';
                }
            }
        }
        
        // Formatear tiempo
        const timeStr = `${minutesLeft}:${secondsLeft.toString().padStart(2, '0')}`;
        
        // Cambiar color según tiempo restante
        if (timeLeft < 120000) { // Menos de 2 minutos
            timerDiv.style.color = '#ff0000';
            statusDiv.innerHTML = '⚠️ Token próximo a expirar';
            if (inactiveTime < 3 * 60 * 1000) {
                actionDiv.innerHTML = '🔄 Renovando automáticamente...';
            } else {
                actionDiv.innerHTML = '⚠️ Inactivo - NO se renovará';
            }
        } else if (timeLeft < 180000) { // Menos de 3 minutos
            timerDiv.style.color = '#ff9900';
            statusDiv.innerHTML = '⚡ Token activo';
            actionDiv.innerHTML = 'Verificando cada 30 segundos';
        } else {
            timerDiv.style.color = '#00ff00';
            statusDiv.innerHTML = '✅ Token válido';
            actionDiv.innerHTML = 'Verificando cada 30 segundos';
        }
        
        timerDiv.innerHTML = `⏱️ ${timeStr}`;
    }

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        // Crear panel de debug
        createDebugPanel();
        
        // Actualizar cada segundo
        setInterval(updateDebugPanel, 1000);
        
        // Actualizar inmediatamente
        updateDebugPanel();
        
        console.log('🧪 JWT Debug Mode activado');
        console.log('📊 Panel visual en la esquina superior derecha');
    });

})();
