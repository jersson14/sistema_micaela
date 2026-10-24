<?php
    require '../../utilitario/requiere_sesion.php';
    require '../../model/model_salidas_diarias.php';
    require '../../model/model_estados_encomienda.php';
    $MSD = new Modelo_Salidas_Diarias();//Instaciamos
    $id = (int)($_POST['id'] ?? 0);
    $observacion = strtoupper(htmlspecialchars($_POST['observacion'] ?? '',ENT_QUOTES,'UTF-8'));

    // Encomiendas que sí llegaron (JSON con sus ids); las demás quedan OBSERVADAS
    $llegaron = json_decode($_POST['llegaron'] ?? '[]', true);
    $llegaron = is_array($llegaron) ? implode(',', array_filter(array_map('intval', $llegaron))) : '';

    if (trim($observacion) === '') {
        echo 'Indique el motivo por el que la salida quedó incompleta';
        exit;
    }

    try {
        echo $MSD->Modificar_Salida_Estatus_Incompleto($id,$ID_USUARIO_SESION,$observacion,$llegaron);
    } catch (PDOException $e) {
        echo Reglas_Estado_Encomienda::Mensaje_Error($e);
    }
?>
