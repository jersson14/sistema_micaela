<?php
    require '../../utilitario/requiere_sesion.php';
    require '../../model/model_encomiendas.php';
    require '../../model/model_estados_encomienda.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $id = (int)($_POST['id'] ?? 0);
    $nuevo_estado = strtoupper(htmlspecialchars($_POST['nuevo_estado'] ?? '',ENT_QUOTES,'UTF-8'));
    $monto_recibido = $_POST['monto_recibido'] ?? '';

    $actual = $MEN->Obtener_Estado($id);
    if (!$actual) {
        echo 'La encomienda no existe';
        exit;
    }
    $error = Reglas_Estado_Encomienda::Validar_Pago($actual, $nuevo_estado, $monto_recibido);
    if ($error !== null) {
        echo $error;
        exit;
    }

    // El saldo se toma de la BD, no del formulario
    try {
        echo $MEN->Realizar_Pago($id,$nuevo_estado,$actual['por_pagar'],(float)$monto_recibido,$ID_USUARIO_SESION);
    } catch (PDOException $e) {
        echo Reglas_Estado_Encomienda::Mensaje_Error($e);
    }
?>
