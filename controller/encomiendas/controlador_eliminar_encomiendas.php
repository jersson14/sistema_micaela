<?php
    require '../../utilitario/requiere_sesion.php';
    require '../../model/model_encomiendas.php';
    require '../../model/model_estados_encomienda.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    $id = (int)($_POST['id'] ?? 0);

    $actual = $MEN->Obtener_Estado($id);
    if (!$actual) {
        echo 'La encomienda no existe';
        exit;
    }
    $error = Reglas_Estado_Encomienda::Validar_Eliminar($actual);
    if ($error !== null) {
        echo $error;
        exit;
    }

    try {
        echo $MEN->Eliminar_Encomienda($id);
    } catch (PDOException $e) {
        // Si alguna vez estuvo en una salida, la relación impide borrarla
        error_log('Eliminar encomienda ' . $id . ': ' . $e->getMessage());
        echo 'No se puede eliminar: la encomienda ya estuvo en una salida. Use Anular';
    }
?>
