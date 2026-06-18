/**
 * JWT Handler - Manejo automático de tokens JWT
 * Incluir este archivo en todas las páginas internas del sistema
 */

(function() {
    'use strict';

    const JWTHandler = {
        // Configuración de Producción
        config: {
            refreshThreshold: 15 * 60 * 1000, // 15 minutos
            checkInterval: 5 * 60 * 1000, // Verificar cada 5 minutos
            inactivityTimeout: 2 * 3600 * 1000, // 2 horas de inactividad
        },

        lastActivity: Date.now(),
        userActive: true,

        /**
         * Inicializa el manejador de tokens
         */
        init: function() {
            // Registrar actividad del usuario
            this.trackUserActivity();

            // Verificar token al cargar la página
            this.checkTokenExpiration();

            // Verificar periódicamente
            setInterval(() => {
                this.checkTokenExpiration();
            }, this.config.checkInterval);

            // Interceptar todas las peticiones AJAX para agregar token
            this.setupAjaxInterceptor();
        },

        /**
         * Rastrea la actividad del usuario
         */
        trackUserActivity: function() {
            const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
            
            events.forEach(event => {
                document.addEventListener(event, () => {
                    this.lastActivity = Date.now();
                    this.userActive = true;
                }, true);
            });
        },

        /**
         * Verifica si el usuario está activo
         */
        isUserActive: function() {
            const inactiveTime = Date.now() - this.lastActivity;
            return inactiveTime < this.config.inactivityTimeout;
        },

        /**
         * Verifica si el token está próximo a expirar
         */
        checkTokenExpiration: function() {
            const tokenExpires = localStorage.getItem('token_expires');
            
            if (!tokenExpires) {
                console.log('⚠️ No hay token, redirigiendo al login...');
                this.logout();
                return;
            }

            const expiresAt = parseInt(tokenExpires);
            const now = Date.now();
            const timeLeft = expiresAt - now;
            const minutesLeft = Math.floor(timeLeft / 1000 / 60);
            const secondsLeft = Math.floor((timeLeft / 1000) % 60);

            // Verificar si el usuario está activo
            const isActive = this.isUserActive();
            const inactiveTime = Date.now() - this.lastActivity;
            const inactiveMinutes = Math.floor(inactiveTime / 1000 / 60);

            // Log para debug en consola
            console.log(`🔐 JWT: Token válido por ${minutesLeft}m ${secondsLeft}s | Inactivo: ${inactiveMinutes}m | Usuario: ${isActive ? 'Activo' : 'Inactivo'}`);

            // Si el usuario está inactivo por más de 5 minutos, cerrar sesión
            if (!isActive) {
                console.log('❌ Usuario inactivo por más de 5 minutos, cerrando sesión...');
                this.logout();
                return;
            }

            // Si quedan menos de 2 minutos Y el usuario está activo, refrescar token
            if (timeLeft < this.config.refreshThreshold && timeLeft > 0 && isActive) {
                console.log('⚠️ Token próximo a expirar (usuario activo), refrescando...');
                this.refreshToken();
            } else if (timeLeft <= 0) {
                console.log('❌ Token expirado, cerrando sesión...');
                this.logout();
            }
        },

        /**
         * Refresca el access token usando el refresh token
         */
        refreshToken: function() {
            const refreshToken = localStorage.getItem('refresh_token');
            
            if (!refreshToken) {
                console.error('No hay refresh token disponible');
                this.logout();
                return;
            }

            $.ajax({
                url: '../controller/usuario/controlador_refresh_token.php',
                type: 'POST',
                data: {
                    refresh_token: refreshToken
                },
                success: (response) => {
                    if (response.success && response.tokens) {
                        // Actualizar tokens
                        localStorage.setItem('access_token', response.tokens.access_token);
                        localStorage.setItem('token_expires', Date.now() + (response.tokens.expires_in * 1000));
                        console.log('Token refrescado exitosamente');
                    } else {
                        console.error('Error al refrescar token');
                        this.logout();
                    }
                },
                error: (xhr) => {
                    console.error('Error al refrescar token:', xhr.status);
                    if (xhr.status === 401) {
                        this.logout();
                    }
                }
            });
        },

        /**
         * Cierra sesión y redirige al login
         */
        logout: function() {
            console.log('🚪 Cerrando sesión...');
            
            // Limpiar tokens
            localStorage.removeItem('access_token');
            localStorage.removeItem('refresh_token');
            localStorage.removeItem('token_expires');
            
            // Cerrar sesión PHP también
            $.ajax({
                url: '../controller/usuario/controlador_cerrar_sesion.php',
                type: 'GET',
                async: false
            });
            
            // Redirigir al login
            window.location.href = '../index.php?expired=1';
        },

        /**
         * Configura interceptor para agregar token a peticiones AJAX
         */
        setupAjaxInterceptor: function() {
            // Guardar la función ajax original
            const originalAjax = $.ajax;

            // Sobrescribir $.ajax
            $.ajax = function(options) {
                const token = localStorage.getItem('access_token');
                
                // Si hay token, agregarlo al header
                if (token) {
                    options.beforeSend = function(xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                    };
                }

                // Manejar errores 401 (no autenticado)
                const originalError = options.error;
                options.error = function(xhr, status, error) {
                    if (xhr.status === 401) {
                        console.log('Sesión expirada, redirigiendo al login...');
                        JWTHandler.logout();
                    }
                    
                    if (originalError) {
                        originalError(xhr, status, error);
                    }
                };

                // Llamar a la función ajax original
                return originalAjax.call(this, options);
            };
        },

        /**
         * Obtiene el token actual
         */
        getToken: function() {
            return localStorage.getItem('access_token');
        },

        /**
         * Verifica si hay un token válido
         */
        hasValidToken: function() {
            const token = this.getToken();
            const tokenExpires = localStorage.getItem('token_expires');
            
            if (!token || !tokenExpires) {
                return false;
            }

            return parseInt(tokenExpires) > Date.now();
        }
    };

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        JWTHandler.init();
    });

    // Exponer globalmente
    window.JWTHandler = JWTHandler;

})();
