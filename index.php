<?php
session_start();
if(isset($_SESSION['S_ID'])){
    header('Location: view/index.php');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tours Micaela - Portal de Acceso</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
      <link rel="icon" href="img/logo.jpg" type="image/jpg">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
            height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            position: relative;
        }

        /* Fondo con carretera animada */
        .road-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.1) 48%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0.1) 52%, transparent 100%),
                radial-gradient(ellipse at center, rgba(2, 61, 119, 0.3) 0%, transparent 70%);
            z-index: -1;
            animation: roadMove 3s ease-in-out infinite;
        }

        @keyframes roadMove {
            0%, 100% { transform: translateX(-2px); }
            50% { transform: translateX(2px); }
        }

        /* Fondo con patrón sutil de autos */
        .cars-pattern-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M8 8h4v4H8V8zm8 0h4v4h-4V8zm8 0h4v4h-4V8zM8 16h4v4H8v-4zm8 0h4v4h-4v-4zm8 0h4v4h-4v-4zM8 24h4v4H8v-4zm8 0h4v4h-4v-4zm8 0h4v4h-4v-4z'/%3E%3C/g%3E%3C/svg%3E");
            z-index: -2;
            animation: patternMove 20s linear infinite;
        }

        @keyframes patternMove {
            0% { transform: translateX(0) translateY(0); }
            100% { transform: translateX(40px) translateY(40px); }
        }

        /* Montañas de fondo */
        .mountains {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40%;
            background: 
                linear-gradient(45deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
            clip-path: polygon(0 100%, 15% 60%, 25% 80%, 40% 40%, 55% 70%, 70% 30%, 85% 60%, 100% 45%, 100% 100%);
            opacity: 0.6;
            z-index: -1;
        }

        /* Automóviles flotantes */
        .floating-cars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .floating-car {
            position: absolute;
            color: rgba(255, 255, 255, 0.15);
            font-size: 2.5rem;
            animation: moveCar 20s linear infinite;
        }

        .floating-car:nth-child(1) {
            top: 15%;
            animation-delay: 0s;
            color: rgba(74, 144, 226, 0.2);
        }

        .floating-car:nth-child(2) {
            top: 25%;
            animation-delay: 7s;
            color: rgba(46, 204, 113, 0.2);
            font-size: 2rem;
        }

        .floating-car:nth-child(3) {
            top: 70%;
            animation-delay: 14s;
            color: rgba(241, 196, 15, 0.2);
            font-size: 3rem;
        }

        .floating-car:nth-child(4) {
            top: 80%;
            animation-delay: 3s;
            color: rgba(231, 76, 60, 0.2);
            font-size: 2.2rem;
        }

        @keyframes moveCar {
            0% { 
                transform: translateX(-100px) scale(0.8);
                opacity: 0;
            }
            10% { 
                opacity: 1;
            }
            90% { 
                opacity: 1;
            }
            100% { 
                transform: translateX(calc(100vw + 100px)) scale(1.2);
                opacity: 0;
            }
        }

        /* Container principal centrado */
        .main-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
            position: relative;
            z-index: 2;
        }

        /* Header superior */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 1.5rem 2rem;
            text-align: center;
            z-index: 3;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #fff, #74b9ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
        }

        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            font-style: italic;
            font-weight: 300;
        }

        /* Card de login centrada */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            position: relative;
            animation: slideInUp 1s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes slideInUp {
            from {
                transform: translateY(100px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Logo central RECTANGULAR con imagen */
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-container {
            width: 280px;
            height: 140px;
            background: linear-gradient(135deg, #023D77 0%, #0056b3 50%, #074a9e 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 
                0 20px 40px rgba(2, 61, 119, 0.4),
                0 8px 20px rgba(0, 0, 0, 0.15),
                inset 0 2px 4px rgba(255, 255, 255, 0.1);
            animation: logoFloat 4s ease-in-out infinite;
            position: relative;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        /* Borde animado gradiente */
        .logo-container::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            background: linear-gradient(45deg, #023D77, #74b9ff, #00d4ff, #023D77, #74b9ff);
            background-size: 300% 300%;
            border-radius: 23px;
            z-index: -1;
            animation: gradientMove 4s ease-in-out infinite;
        }

        @keyframes gradientMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Imagen rectangular del logo */
        .logo-image {
            width: 240px;
            height: 100px;
            border-radius: 15px;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.2);
            box-shadow: 
                0 8px 25px rgba(0, 0, 0, 0.3),
                inset 0 2px 4px rgba(255, 255, 255, 0.2);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 2;
            position: relative;
        }

        /* Efecto hover para la imagen */
        .logo-container:hover .logo-image {
            transform: scale(1.05) translateY(-2px);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 
                0 15px 35px rgba(0, 0, 0, 0.4),
                inset 0 3px 6px rgba(255, 255, 255, 0.3),
                0 0 30px rgba(116, 185, 255, 0.3);
        }

        /* Ícono de respaldo rectangular */
        .backup-icon {
            font-size: 3.5rem;
            color: rgba(255, 255, 255, 0.9);
            display: none;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        /* Efecto de brillo que pasa sobre la imagen */
        .logo-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            border-radius: 20px;
            transition: left 0.8s ease-in-out;
            pointer-events: none;
            z-index: 3;
        }

        .logo-container:hover::after {
            left: 100%;
        }

        /* Partículas flotantes alrededor del logo */
        .logo-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: radial-gradient(circle, rgba(116, 185, 255, 0.8), transparent);
            border-radius: 50%;
            animation: particleFloat 3s ease-in-out infinite;
        }

        .particle:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { top: 20%; right: 15%; animation-delay: 0.5s; }
        .particle:nth-child(3) { bottom: 20%; left: 20%; animation-delay: 1s; }
        .particle:nth-child(4) { bottom: 15%; right: 10%; animation-delay: 1.5s; }

        @keyframes particleFloat {
            0%, 100% { transform: translateY(0px) scale(1); opacity: 0.7; }
            50% { transform: translateY(-15px) scale(1.2); opacity: 1; }
        }

        /* Estados de error de imagen */
        .logo-image-error .backup-icon {
            display: block !important;
        }

        .logo-image-error .logo-image {
            display: none !important;
        }

        @keyframes logoFloat {
            0%, 100% { 
                transform: translateY(0px) scale(1); 
                box-shadow: 
                    0 20px 40px rgba(2, 61, 119, 0.4),
                    0 8px 20px rgba(0, 0, 0, 0.15),
                    inset 0 2px 4px rgba(255, 255, 255, 0.1);
            }
            50% { 
                transform: translateY(-8px) scale(1.02); 
                box-shadow: 
                    0 25px 50px rgba(2, 61, 119, 0.5),
                    0 12px 25px rgba(0, 0, 0, 0.2),
                    inset 0 3px 6px rgba(255, 255, 255, 0.15);
            }
        }

        .login-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #023D77;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(2, 61, 119, 0.1);
        }

        .login-subtitle {
            color: #666;
            font-size: 0.95rem;
            font-weight: 400;
            opacity: 0.9;
        }

        /* Formulario estilizado */
        .form-container {
            margin-top: 2rem;
        }

        .input-group {
            position: relative;
            margin-bottom: 1.8rem;
        }

        .input-field {
            width: 100%;
            padding: 1.2rem 1.2rem 1.2rem 3.5rem;
            border: 2px solid #e1e8ed;
            border-radius: 15px;
            font-size: 1rem;
            font-weight: 400;
            color: #333;
            background: #f8f9fa;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }

        .input-field:focus {
            border-color: #023D77;
            background: white;
            box-shadow: 
                0 0 0 4px rgba(2, 61, 119, 0.1),
                0 10px 25px rgba(2, 61, 119, 0.15);
            transform: translateY(-3px);
        }

        .input-field:focus + .input-icon {
            color: #023D77;
            transform: translateY(-50%) scale(1.1);
        }

        .input-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .password-toggle {
            position: absolute;
            right: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            padding: 0.5rem;
        }

        .password-toggle:hover {
            color: #023D77;
            transform: translateY(-50%) scale(1.1);
        }

        /* Opciones de login */
        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .remember-checkbox {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            color: #555;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .remember-checkbox:hover {
            color: #023D77;
        }

        .remember-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #023D77;
            cursor: pointer;
            border-radius: 4px;
        }

        /* Botón de login SÚPER LLAMATIVO */
        .login-button {
            width: 100%;
            padding: 1.4rem;
            background: linear-gradient(135deg, #023D77 0%, #0056b3 30%, #74b9ff 60%, #0056b3 90%, #023D77 100%);
            background-size: 300% 300%;
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 
                0 10px 30px rgba(2, 61, 119, 0.4),
                0 5px 15px rgba(0, 0, 0, 0.2),
                inset 0 2px 4px rgba(255, 255, 255, 0.2);
            animation: buttonPulse 2s ease-in-out infinite;
        }

        /* Animación de pulso del botón */
        @keyframes buttonPulse {
            0%, 100% {
                box-shadow: 
                    0 10px 30px rgba(2, 61, 119, 0.4),
                    0 5px 15px rgba(0, 0, 0, 0.2),
                    inset 0 2px 4px rgba(255, 255, 255, 0.2),
                    0 0 0 0 rgba(116, 185, 255, 0.7);
            }
            50% {
                box-shadow: 
                    0 15px 40px rgba(2, 61, 119, 0.6),
                    0 8px 20px rgba(0, 0, 0, 0.3),
                    inset 0 3px 6px rgba(255, 255, 255, 0.3),
                    0 0 0 10px rgba(116, 185, 255, 0);
            }
        }

        /* Animación del gradiente del botón */
        .login-button {
            animation: 
                buttonPulse 2s ease-in-out infinite,
                gradientShift 3s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0%, 100% { 
                background-position: 0% 50%; 
                transform: scale(1);
            }
            50% { 
                background-position: 100% 50%; 
                transform: scale(1.02);
            }
        }

        /* Efecto hover del botón */
        .login-button:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 
                0 20px 50px rgba(2, 61, 119, 0.6),
                0 10px 25px rgba(0, 0, 0, 0.3),
                inset 0 3px 6px rgba(255, 255, 255, 0.3),
                0 0 30px rgba(116, 185, 255, 0.5);
            animation: none;
        }

        .login-button:active {
            transform: translateY(-2px) scale(1.02);
        }

        /* Efecto de ondas en el botón */
        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.4), 
                rgba(255, 255, 255, 0.6), 
                rgba(255, 255, 255, 0.4), 
                transparent);
            transition: left 0.6s ease;
            border-radius: 20px;
        }

        .login-button:hover::before {
            left: 100%;
        }

        /* Partículas brillantes en el botón */
        .login-button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.8) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .login-button:hover::after {
            width: 300px;
            height: 300px;
            opacity: 0.3;
        }

        .button-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            position: relative;
            z-index: 2;
        }

        /* Icono del botón con animación */
        #loginIcon {
            transition: all 0.3s ease;
            animation: iconBounce 2s ease-in-out infinite;
        }

        @keyframes iconBounce {
            0%, 100% { transform: translateX(0px); }
            50% { transform: translateX(3px); }
        }

        .login-button:hover #loginIcon {
            transform: translateX(5px) scale(1.2);
            animation: none;
        }

        /* Loading spinner mejorado */
        .loading-spinner {
            width: 22px;
            height: 22px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Estados del botón al hacer clic */
        .login-button.loading {
            animation: none;
            background: linear-gradient(135deg, #1a365d 0%, #2c5282 50%, #3182ce 100%);
            cursor: not-allowed;
        }

        .login-button.loading:hover {
            transform: none;
            box-shadow: 
                0 10px 30px rgba(2, 61, 119, 0.4),
                0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            z-index: 3;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Efecto de partículas de estrella */
        .stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .star {
            position: absolute;
            background: white;
            border-radius: 50%;
            opacity: 0.8;
            animation: twinkle 3s ease-in-out infinite;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.2); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .header p {
                font-size: 1rem;
            }

            .login-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
                border-radius: 20px;
            }

            .logo-container {
                width: 240px;
                height: 120px;
            }

            .logo-image {
                width: 200px;
                height: 80px;
            }

            .backup-icon {
                font-size: 2.5rem;
            }

            .login-title {
                font-size: 1.5rem;
            }

            .floating-car {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 1rem;
            }

            .login-card {
                padding: 1.5rem 1rem;
            }

            .logo-container {
                width: 200px;
                height: 100px;
            }

            .logo-image {
                width: 160px;
                height: 70px;
            }

            .backup-icon {
                font-size: 2rem;
            }

            .input-field {
                padding: 1rem 1rem 1rem 3rem;
            }

            .login-options {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header Superior -->
    <div class="header">
        <h1>EMPRESA DE TRANSPORTES TOURS MICAELA</h1>
        <p>"SISTEMA WEB DE SALIDAS DIARIAS Y FACTURACIÓN ELECTRÓNICA"</p>
    </div>

    <!-- Fondo con patrón sutil de autos -->
    <div class="cars-pattern-bg"></div>

    <!-- Montañas de fondo -->
    <div class="mountains"></div>

    <!-- Carretera animada -->
    <div class="road-background"></div>

    <!-- Automóviles flotantes -->
    <div class="floating-cars">
        <i class="fas fa-car floating-car"></i>
        <i class="fas fa-car-side floating-car"></i>
        <i class="fas fa-shuttle-van floating-car"></i>
        <i class="fas fa-car-alt floating-car"></i>
    </div>

    <!-- Estrellas de fondo -->
    <div class="stars"></div>

    <!-- Container Principal -->
    <div class="main-container">
        <div class="login-card">
            <!-- Logo rectangular y título -->
            <div class="login-logo">
                <div class="logo-container" id="logoContainer">
                    <!-- Partículas flotantes -->
                    <div class="logo-particles">
                        <div class="particle"></div>
                        <div class="particle"></div>
                        <div class="particle"></div>
                        <div class="particle"></div>
                    </div>
                    
                    <img src="img/logo.jpg" alt="Tours Micaela - Transporte" class="logo-image" id="logoImage">
                    <i class="fas fa-route backup-icon"></i>
                </div>
                <h2 class="login-title">Bienvenido de Vuelta</h2>
                <p class="login-subtitle">Accede al sistema de gestión Tours Micaela</p>
            </div>

            <!-- Formulario -->
            <div class="form-container">
                <form id="loginForm">
                    <!-- Campo Usuario -->
                    <div class="input-group">
                        <input type="text" class="input-field" id="txt_usuario" placeholder="Nombre de usuario" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    <!-- Campo Contraseña -->
                    <div class="input-group">
                        <input type="password" class="input-field" id="txt_contra" placeholder="Contraseña" required>
                        <i class="fas fa-lock input-icon"></i>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>

                    <!-- Opciones -->
                    <div class="login-options">
                        <label class="remember-checkbox">
                            <input type="checkbox" id="remember">
                            <span>Recuérdame</span>
                        </label>
                    </div>

                    <!-- Botón de Login -->
                    <button type="button" class="login-button" id="entrar" onclick="Iniciar_Sesion()">
                        <div class="button-content">
                            <div class="loading-spinner"></div>
                            <i class="fas fa-sign-in-alt" id="loginIcon"></i>
                            <span id="loginText">Iniciar Sesión</span>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>CUSCO ⟷ ABANCAY</strong> | Tours Micaela - "Llegamos a tu felicidad"</p>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/console_usuario.js?rev=<?php echo time();?>"></script>

    <script>
        // Crear estrellas de fondo
        function createStars() {
            const stars = document.querySelector('.stars');
            for (let i = 0; i < 100; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.width = star.style.height = Math.random() * 3 + 1 + 'px';
                star.style.animationDelay = Math.random() * 3 + 's';
                star.style.animationDuration = (Math.random() * 3 + 2) + 's';
                stars.appendChild(star);
            }
        }

        // Manejar error de carga de imagen
        document.getElementById('logoImage').onerror = function() {
            document.getElementById('logoCircle').classList.add('logo-image-error');
        };

        // Precargar imagen para mejor rendimiento
        const logoImg = new Image();
        logoImg.src = 'img/logo.jpg';

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('txt_contra');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Recordar usuario
        const rmcheck = document.getElementById('remember'),
              usuarioInput = document.getElementById('txt_usuario'),
              passInput = document.getElementById('txt_contra');

        if(localStorage.checkbox && localStorage.checkbox !== "") {
            rmcheck.checked = true;
            usuarioInput.value = localStorage.usuario;
            passInput.value = localStorage.pass;
        } else {
            rmcheck.checked = false;
            usuarioInput.value = "";
            passInput.value = "";
        }

        // Enter key para login
        document.getElementById('txt_contra').addEventListener('keyup', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                document.getElementById('entrar').click();
            }
        });

        // Focus automático
        document.getElementById('txt_usuario').focus();

        // Inicializar estrellas
        createStars();

        // Efecto de enfoque suave en inputs
        document.querySelectorAll('.input-field').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Efecto parallax suave en mousemove
        document.addEventListener('mousemove', function(e) {
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;
            
            document.querySelector('.mountains').style.transform = `translateX(${mouseX * 10}px)`;
            document.querySelector('.login-card').style.transform = `translateX(${mouseX * 5}px) translateY(${mouseY * 5}px)`;
        });

      
    </script>
</body>
</html>