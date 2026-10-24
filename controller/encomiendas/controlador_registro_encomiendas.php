<?php
    require_once '../../utilitario/requiere_sesion.php';
    require '../../model/model_encomiendas.php';
    require '../../model/model_estados_encomienda.php';
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
    $idusu = $ID_USUARIO_SESION; // el usuario sale de la sesión

    // La salida solo lleva encomiendas de su misma ruta: origen y destino distintos
    if ($ori === '' || $ori === $des) {
        echo 'El origen y el destino no pueden ser iguales';
        exit;
    }

    $error = Reglas_Estado_Encomienda::Validar_Montos($pago, $porpagar, $adomicilio);
    if ($error !== null) {
        echo $error;
        exit;
    }

    try {
        $consulta = $MEN->Registrar_Encomiendas($conduc,$ori,$des,$fecha,$desc,$tipodocemi,$documentoFinal,$nomemi,$celemi,$tipodocrece,$documentoFinal2,$nomrece,$celurece,$pago,$porpagar,$adomicilio,$idusu);
        echo $consulta;
    } catch (PDOException $e) {
        echo Reglas_Estado_Encomienda::Mensaje_Error($e);
    }



?>