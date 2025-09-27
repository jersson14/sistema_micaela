<?php
    require '../../model/model_ingresos.php';
    $MIN = new Modelo_Ingresos();//Instaciamos
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $descri = strtoupper(htmlspecialchars($_POST['descri'],ENT_QUOTES,'UTF-8'));
    $monto = strtoupper(htmlspecialchars($_POST['monto'],ENT_QUOTES,'UTF-8'));
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));

    $consulta = $MIN->Anular_Ingresos($id,$descri,$monto,$idusu);
    echo $consulta;



?>