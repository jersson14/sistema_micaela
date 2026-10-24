<?php
    require '../../utilitario/requiere_sesion.php';
    require '../../model/model_encomiendas.php';
    require '../../model/model_estados_encomienda.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $id = (int)($_POST['id'] ?? 0);
    $nuevo_estado = strtoupper(htmlspecialchars($_POST['nuevo_estado'] ?? '',ENT_QUOTES,'UTF-8'));
    $observacion = strtoupper(htmlspecialchars($_POST['observacion'] ?? '',ENT_QUOTES,'UTF-8'));
    $anula = strtoupper(htmlspecialchars($_POST['anula'] ?? '',ENT_QUOTES,'UTF-8'));

    $actual = $MEN->Obtener_Estado($id);
    if (!$actual) {
        echo 'La encomienda no existe';
        exit;
    }
    $error = Reglas_Estado_Encomienda::Validar_Cambio($actual, $nuevo_estado, $anula, $observacion);
    if ($error !== null) {
        echo $error;
        exit;
    }
    // La observación es opcional (salvo OBSERVADO); el historial siempre lleva un texto
    if (trim($observacion) === '') {
        $observacion = 'CAMBIO DE ESTADO A ' . $nuevo_estado;
    }

    try {
        echo $MEN->Modificar_Estado($id,$nuevo_estado,$observacion,$anula,$ID_USUARIO_SESION);
    } catch (PDOException $e) {
        echo Reglas_Estado_Encomienda::Mensaje_Error($e);
    }
?>
