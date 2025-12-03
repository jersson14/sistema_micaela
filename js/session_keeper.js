/**
 * Session Keeper - Mantiene sesión PHP activa
 * Reemplaza a jwt_handler.js
 */
(function() {
    'use strict';

    const SessionKeeper = {
        config: {
            inactivityTimeout: 2 * 60 * 60 * 1000, // 2 horas en milisegundos
            checkInterval: 5 * 60 * 1000, // Verificar cada 5 minutos
            pingInterval: 10 * 60 * 1000 // Ping al servidor cada 10 minutos
        },

        lastActivity: Date.now(),
        checkTimer: null,
        pingTimer: null,

        init: function() {
            console.log('🔐 Session Keeper iniciado (sin JWT)');
            this.trackUserActivity();
            this.startInactivityCheck();
            this.startSessionPing();
            this.cleanupOldTokens();
        },

        // Limpiar tokens JWT antiguos si existen
        cleanupOldTokens: function() {
            localStorage.removeItem('access_token');
            localStorage.removeItem('refresh_token');
            localStorage.removeItem('token_expires');
            console.log('🧹 Tokens JWT antiguos eliminados');
        },

        // Rastrear actividad del usuario
        trackUserActivity: function() {
            const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
            
            events.forEach(event => {
                document.addEventListener(event, () => {
                    this.lastActivity = Date.now();
                }, true);
            });
        },

        // Verificar inactividad periódicamente
        startInactivityCheck: function() {
            this.checkTimer = setInterval(() => {
                const inactiveTime = Date.now() - this.lastActivity;
                const inactiveMinutes = Math.floor(inactiveTime / 1000 / 60);
                
                // Debug en consola cada 5 minutos
                console.log(`⏱️ Inactividad: ${inactiveMinutes} minutos`);
                
                if (inactiveTime >= this.config.inactivityTimeout) {
                    console.log('❌ Sesión expirada por inactividad (2 horas)');
                    this.logout('Tu sesión expiró por inactividad (2 horas sin actividad)');
                }
            }, this.config.checkInterval);
        },

        // Hacer ping al servidor para mantener sesión PHP viva
        startSessionPing: function() {
            this.pingTimer = setInterval(() => {
                $.ajax({
                    url: '../controller/usuario/ping_session.php',
                    type: 'GET',
                    cache: false,
                    success: (response) => {
                        if (response.active) {
                            console.log('✅ Sesión PHP activa:', response.user);
                        } else {
                            console.log('❌ Sesión PHP inválida');
                            this.logout('Tu sesión ha expirado');
                        }
                    },
                    error: (xhr) => {
                        if (xhr.status === 401) {
                            console.log('❌ Sesión no autorizada');
                            this.logout('Tu sesión ha expirado');
                        }
                    }
                });
            }, this.config.pingInterval);
        },

        // Cerrar sesión y redirigir
        logout: function(message) {
            // Limpiar timers
            if (this.checkTimer) clearInterval(this.checkTimer);
            if (this.pingTimer) clearInterval(this.pingTimer);

            Swal.fire({
                icon: 'warning',
                title: 'Sesión Expirada',
                text: message || 'Tu sesión ha expirado',
                confirmButtonText: 'Iniciar Sesión',
                confirmButtonColor: '#023D77',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                window.location.href = '../controller/usuario/controlador_cerrar_sesion.php';
            });
        },

        // Destruir (útil para testing)
        destroy: function() {
            if (this.checkTimer) clearInterval(this.checkTimer);
            if (this.pingTimer) clearInterval(this.pingTimer);
            console.log('🛑 Session Keeper detenido');
        }
    };

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        SessionKeeper.init();
    });

    // Exponer globalmente para debug
    window.SessionKeeper = SessionKeeper;

})();