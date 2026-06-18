@echo off
REM Script de verificación antes de subir a GitHub (Windows)
REM Ejecuta: check-before-github.bat

echo.
echo Verificando archivos sensibles antes de subir a GitHub...
echo.

set ERRORS=0
set WARNINGS=0

echo [Verificando archivos de configuracion...]
echo.

REM Archivos que NO deben existir
if exist "model\model_conexion.php" (
    echo [X] PELIGRO: model\model_conexion.php existe y podria subirse a GitHub
    echo     Verifica que este en .gitignore
    set /a ERRORS+=1
) else (
    echo [OK] model\model_conexion.php no existe ^(correcto^)
)

if exist "view\MPDF\conexion.php" (
    echo [X] PELIGRO: view\MPDF\conexion.php existe y podria subirse a GitHub
    set /a ERRORS+=1
) else (
    echo [OK] view\MPDF\conexion.php no existe ^(correcto^)
)

if exist ".env" (
    echo [X] PELIGRO: .env existe y podria subirse a GitHub
    set /a ERRORS+=1
) else (
    echo [OK] .env no existe ^(correcto^)
)

if exist "certificate.pem" (
    echo [!] ADVERTENCIA: certificate.pem existe - verifica que este en .gitignore
    set /a WARNINGS+=1
) else (
    echo [OK] certificate.pem no existe ^(correcto^)
)

echo.
echo [Verificando archivos de ejemplo...]
echo.

if exist "model\model_conexion.example.php" (
    echo [OK] model\model_conexion.example.php existe
) else (
    echo [!] ADVERTENCIA: model\model_conexion.example.php no existe
    set /a WARNINGS+=1
)

if exist "view\MPDF\conexion.example.php" (
    echo [OK] view\MPDF\conexion.example.php existe
) else (
    echo [!] ADVERTENCIA: view\MPDF\conexion.example.php no existe
    set /a WARNINGS+=1
)

if exist ".env.example" (
    echo [OK] .env.example existe
) else (
    echo [!] ADVERTENCIA: .env.example no existe
    set /a WARNINGS+=1
)

if exist ".gitignore" (
    echo [OK] .gitignore existe
) else (
    echo [X] ERROR: .gitignore no existe
    set /a ERRORS+=1
)

echo.
echo [Verificando estructura de carpetas...]
echo.

if exist "greenter\xml" (
    echo [OK] greenter\xml existe
) else (
    echo [!] ADVERTENCIA: greenter\xml no existe
    set /a WARNINGS+=1
)

if exist "greenter\cdr" (
    echo [OK] greenter\cdr existe
) else (
    echo [!] ADVERTENCIA: greenter\cdr no existe
    set /a WARNINGS+=1
)

if exist "greenter\pdf" (
    echo [OK] greenter\pdf existe
) else (
    echo [!] ADVERTENCIA: greenter\pdf no existe
    set /a WARNINGS+=1
)

echo.
echo ===============================================
echo.

if %ERRORS% EQU 0 (
    if %WARNINGS% EQU 0 (
        echo [PERFECTO] Tu proyecto esta listo para subir a GitHub
        echo.
        echo Proximos pasos:
        echo 1. git init
        echo 2. git add .
        echo 3. git commit -m "Initial commit"
        echo 4. git remote add origin https://github.com/TU_USUARIO/sistema-tours-micaela.git
        echo 5. git push -u origin main
    ) else (
        echo [ADVERTENCIA] Hay %WARNINGS% advertencias
        echo.
        echo Puedes continuar, pero revisa las advertencias arriba.
    )
) else (
    echo [ERROR] Hay %ERRORS% errores criticos
    echo.
    echo NO SUBAS EL PROYECTO A GITHUB HASTA CORREGIR LOS ERRORES.
)

echo.
echo ===============================================
echo.
pause
