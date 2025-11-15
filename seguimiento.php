<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Encomiendas - Tours Micaela</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="img/logito.png" type="image/jpg">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Fondo con carretera animada */
        .road-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.1) 48%, rgba(255, 255, 255, 0.3) 50%, rgba(255, 255, 255, 0.1) 52%, transparent 100%),
                radial-gradient(ellipse at center, rgba(2, 61, 119, 0.3) 0%, transparent 70%);
            z-index: 0;
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
            z-index: 0;
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
            background: linear-gradient(45deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
            clip-path: polygon(0 100%, 15% 60%, 25% 80%, 40% 40%, 55% 70%, 70% 30%, 85% 60%, 100% 45%, 100% 100%);
            opacity: 0.6;
            z-index: 0;
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
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% {
                transform: translateX(calc(100vw + 100px)) scale(1.2);
                opacity: 0;
            }
        }

        /* Estrellas */
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
            0%, 100% {
                opacity: 0.3;
                transform: scale(1);
            }
            50% {
                opacity: 1;
                transform: scale(1.2);
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 3rem;
            animation: fadeInDown 0.8s ease;
        }

        .logo-container {
            width: 200px;
            height: 120px;
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
            padding: 10px;
        }

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

        .logo-image {
            width: 100%;
            height: 100%;
            max-width: 180px;
            max-height: 100px;
            border-radius: 15px;
            object-fit: contain;
            background: rgba(255, 255, 255, 0.95);
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow:
                0 8px 25px rgba(0, 0, 0, 0.3),
                inset 0 2px 4px rgba(255, 255, 255, 0.2);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 2;
            position: relative;
            padding: 5px;
        }

        .logo-container:hover .logo-image {
            transform: scale(1.05) translateY(-2px);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow:
                0 15px 35px rgba(0, 0, 0, 0.4),
                inset 0 3px 6px rgba(255, 255, 255, 0.3),
                0 0 30px rgba(116, 185, 255, 0.3);
        }

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

        .header h1 {
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
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .search-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease;
        }

        .search-title {
            font-size: 1.5rem;
            color: #023D77;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .search-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .input-group {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .input-field {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 1rem;
            color: #333;
            background: #f8f9fa;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-field:focus {
            border-color: #023D77;
            background: white;
            box-shadow: 0 0 0 4px rgba(2, 61, 119, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.1rem;
        }

        .search-button {
            padding: 1rem 2.5rem;
            background: linear-gradient(135deg, #023D77 0%, #0056b3 50%, #74b9ff 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 10px 25px rgba(2, 61, 119, 0.3);
        }

        .search-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(2, 61, 119, 0.4);
        }

        .results-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            display: none;
            animation: fadeInUp 0.8s ease;
        }

        .results-card.show {
            display: block;
        }

        .encomienda-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #e1e8ed;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .info-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 1.1rem;
            color: #333;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-PENDIENTE { background: #fff3cd; color: #856404; }
        .status-EN-TRANSITO { background: #cfe2ff; color: #084298; }
        .status-EN-AGENCIA { background: #d1ecf1; color: #0c5460; }
        .status-ENTREGADO { background: #d1e7dd; color: #0f5132; }
        .status-OBSERVADO { background: #f8d7da; color: #842029; }
        .status-ANULADO { background: #e2e3e5; color: #41464b; }
        .status-INCOMPLETO { background: #ffe5d0; color: #984c0c; }

        .timeline-section {
            margin-top: 2rem;
        }

        .timeline-title {
            font-size: 1.3rem;
            color: #023D77;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #023D77, #74b9ff);
        }

        .timeline-item {
            position: relative;
            padding-bottom: 2rem;
            animation: fadeInLeft 0.5s ease;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -1.5rem;
            top: 0.3rem;
            width: 1rem;
            height: 1rem;
            background: #023D77;
            border: 3px solid white;
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(2, 61, 119, 0.2);
        }

        .timeline-dot.active {
            background: #74b9ff;
            box-shadow: 0 0 0 3px rgba(116, 185, 255, 0.3), 0 0 20px rgba(116, 185, 255, 0.5);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 1.2rem;
            border-radius: 12px;
            border-left: 4px solid #023D77;
        }

        .timeline-status {
            font-size: 1.1rem;
            font-weight: 600;
            color: #023D77;
            margin-bottom: 0.5rem;
        }

        .timeline-date {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .timeline-observation {
            font-size: 0.95rem;
            color: #555;
            font-style: italic;
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .no-results i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 1rem;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            display: none;
        }

        .loading.show {
            display: block;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #023D77;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .logo-container {
                width: 160px;
                height: 100px;
            }

            .logo-image {
                max-width: 140px;
                max-height: 80px;
            }

            .search-card, .results-card {
                padding: 1.5rem;
            }

            .search-form {
                flex-direction: column;
            }

            .input-group {
                min-width: 100%;
            }

            .search-button {
                width: 100%;
                justify-content: center;
            }

            .encomienda-info {
                grid-template-columns: 1fr;
            }

            .timeline {
                padding-left: 1.5rem;
            }

            .floating-car {
                font-size: 2rem;
            }

            .mountains {
                height: 30%;
            }
        }

        @media (max-width: 480px) {
            .logo-container {
                width: 140px;
                height: 85px;
            }

            .logo-image {
                max-width: 120px;
                max-height: 65px;
            }

            .header h1 {
                font-size: 1.6rem;
            }

            .header p {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
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

    <!-- Estrellas -->
    <div class="stars" id="stars"></div>

    <div class="container">
        <div class="header">
            <div class="logo-container">
                <img src="img/logito.png" alt="Tours Micaela Logo" class="logo-image" onerror="this.style.display='none'">
            </div>
            <h1><i class="fas fa-box"></i> Seguimiento de Encomiendas</h1>
            <p>Ingresa el número de boleta para rastrear tu encomienda</p>
        </div>

        <div class="search-card">
            <div class="search-title">
                <i class="fas fa-search"></i>
                Buscar Encomienda
            </div>
            <form class="search-form" id="searchForm">
                <div class="input-group">
                    <i class="fas fa-receipt input-icon"></i>
                    <input type="text" class="input-field" id="boletaInput" placeholder="Número de Boleta" required>
                </div>
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                    Buscar
                </button>
            </form>
        </div>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Buscando encomienda...</p>
        </div>

        <div class="results-card" id="resultsCard">
            <div id="resultsContent"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Generar estrellas aleatorias
        function createStars() {
            const starsContainer = document.getElementById('stars');
            const numberOfStars = 50;
            
            for(let i = 0; i < numberOfStars; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.style.width = Math.random() * 3 + 'px';
                star.style.height = star.style.width;
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.animationDelay = Math.random() * 3 + 's';
                starsContainer.appendChild(star);
            }
        }
        
        createStars();
    </script>
    <script src="js/seguimiento.js"></script>
</body>
</html>
