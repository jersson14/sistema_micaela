<?php
    require '../../model/model_choferes.php';
    $MCH = new Modelo_Choferes();
    $id = intval($_POST['id'] ?? 0);
    $notificar = intval($_POST['notificar'] ?? 1);

    try {
        if (($_POST['todos'] ?? '') === '1') {
            echo $MCH->Cambiar_Notificacion_Todos($notificar);
        } else {
            echo $id > 0 ? $MCH->Cambiar_Notificacion_Chofer($id, $notificar) : 0;
        }
    } catch (PDOException $e) {
        // Columna notificar_licencia aún no creada (falta la migración)
        echo 2;
    }
?>
