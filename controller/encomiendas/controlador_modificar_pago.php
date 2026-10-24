<?php
    require '../../utilitario/requiere_sesion.php';
    require '../../model/model_encomiendas.php';
    require '../../model/model_estados_encomienda.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $id = (int)($_POST['id'] ?? 0);
    $nuevo_estado = strtoupper(htmlspecialchars($_POST['nuevo_estado'] ?? '',ENT_QUOTES,'UTF-8'));
    $pago_nuevo = $_POST['pago_nuevo'] ?? '';

    $actual = $MEN->Obtener_Estado($id);
    if (!$actual) {
        echo 'La encomienda no existe';
        exit;
    }
    $error = Reglas_Estado_Encomienda::Validar_Ajuste($actual, $nuevo_estado, $pago_nuevo);
    if ($error !== null) {
        echo $error;
        exit;
    }

    // El monto anterior lo toma el procedimiento de la BD
    try {
        echo $MEN->Modificar_Pago($id,$nuevo_estado,0,(float)$pago_nuevo,$ID_USUARIO_SESION);
    } catch (PDOException $e) {
        echo Reglas_Estado_Encomienda::Mensaje_Error($e);
    }
?>
