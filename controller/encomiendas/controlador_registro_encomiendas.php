<?php
    require '../../model/model_encomiendas.php';
    $MEN = new Modelo_Encomiendas();//Instaciamos
    // DATOS DE LA ENCOMIENDA
    $conduc = strtoupper(htmlspecialchars($_POST['conduc'],ENT_QUOTES,'UTF-8'));
    $ori = strtoupper(htmlspecialchars($_POST['ori'],ENT_QUOTES,'UTF-8'));
    $des = strtoupper(htmlspecialchars($_POST['des'],ENT_QUOTES,'UTF-8'));
    $fecha = strtoupper(htmlspecialchars($_POST['fecha'],ENT_QUOTES,'UTF-8'));
    $desc = strtoupper(htmlspecialchars($_POST['desc'],ENT_QUOTES,'UTF-8'));
    // DATOS DEL EMISOR
    $tipodocemi = strtoupper(htmlspecialchars($_POST['tipodocemi'],ENT_QUOTES,'UTF-8'));
    $documentoFinal = strtoupper(htmlspecialchars($_POST['documentoFinal'],ENT_QUOTES,'UTF-8'));
    $nomemi = strtoupper(htmlspecialchars($_POST['nomemi'],ENT_QUOTES,'UTF-8'));
    $celemi = strtoupper(htmlspecialchars($_POST['celemi'],ENT_QUOTES,'UTF-8'));
    // DATOS DEL RECEPTOR
    $tipodocrece = strtoupper(htmlspecialchars($_POST['tipodocrece'],ENT_QUOTES,'UTF-8'));
    $documentoFinal2 = strtoupper(htmlspecialchars($_POST['documentoFinal2'],ENT_QUOTES,'UTF-8'));
    $nomrece = strtoupper(htmlspecialchars($_POST['nomrece'],ENT_QUOTES,'UTF-8'));
    $celurece = strtoupper(htmlspecialchars($_POST['celurece'],ENT_QUOTES,'UTF-8'));
    // DATOS DEL PAGO
    $pago = strtoupper(htmlspecialchars($_POST['pago'],ENT_QUOTES,'UTF-8'));
    $porpagar = strtoupper(htmlspecialchars($_POST['porpagar'],ENT_QUOTES,'UTF-8'));
    $adomicilio = strtoupper(htmlspecialchars($_POST['adomicilio'],ENT_QUOTES,'UTF-8'));
    // DATOS DEL USUARIO
    $idusu = strtoupper(htmlspecialchars($_POST['idusu'],ENT_QUOTES,'UTF-8'));

    $consulta = $MEN->Registrar_Encomiendas($conduc,$ori,$des,$fecha,$desc,$tipodocemi,$documentoFinal,$nomemi,$celemi,$tipodocrece,$documentoFinal2,$nomrece,$celurece,$pago,$porpagar,$adomicilio,$idusu);
    echo $consulta;



?>