@echo off
REM Script de inicio rápido para Windows
REM Uso: start.bat

echo ==========================================
echo    Sistema Tours Micaela - Inicio Rapido
echo ==========================================
echo.

REM Verificar si Docker está instalado
docker --version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Docker no esta instalado
    echo Instala Docker Desktop desde: https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)

echo [OK] Docker esta instalado
echo.

REM Verificar si existe carpeta backup
if not exist "backup" (
    mkdir backup
    echo [INFO] Carpeta 'backup' creada
)

REM Verificar backup de BD
if not exist "backup\micaela.sql" (
    echo [ADVERTENCIA] No se encontro backup\micaela.sql
    echo Copia tu backup de base de datos a: backup\micaela.sql
    echo.
    set /p continuar="Deseas continuar sin backup? (s/n): "
    if /i not "%continuar%"=="s" exit /b 1
)

REM Verificar certificado
if not exist "greenter\certificados\certificado.pem" (
    echo [ADVERTENCIA] No se encontro certificado digital
    echo Copia tu certificado a: greenter\certificados\certificado.pem
    echo (Necesario para facturacion electronica)
    echo.
)

REM Construir imágenes
echo Construyendo imagenes Docker...
docker-compose build

if errorlevel 1 (
    echo [ERROR] Error al construir las imagenes
    pause
    exit /b 1
)

echo.
echo [OK] Imagenes construidas exitosamente
echo.

REM Levantar servicios
echo Levantando servicios...
docker-compose up -d

if errorlevel 1 (
    echo [ERROR] Error al levantar los servicios
    pause
    exit /b 1
)

echo.
echo Esperando a que los servicios esten listos...
timeout /t 5 /nobreak >nul

REM Verificar estado
echo.
echo Estado de los contenedores:
docker-compose ps

echo.
echo ==========================================
echo [OK] Sistema iniciado correctamente
echo ==========================================
echo.
echo Accede a:
echo   - Aplicacion: http://localhost:8080
echo   - phpMyAdmin: http://localhost:8081
echo     Usuario: root
echo     Contrasena: root_password_2024
echo.
echo Comandos utiles:
echo   - Ver logs: docker-compose logs -f
echo   - Detener: docker-compose down
echo   - Reiniciar: docker-compose restart
echo.
echo Guia completa: DOCKER_SETUP.md
echo ==========================================
echo.
pause
