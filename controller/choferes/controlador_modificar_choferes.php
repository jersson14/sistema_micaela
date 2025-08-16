<?php
    require '../../model/model_choferes.php';
    $MCH = new Modelo_Choferes();
    $id = strtoupper(htmlspecialchars($_POST['id'],ENT_QUOTES,'UTF-8'));
    $dni = strtoupper(htmlspecialchars($_POST['dni'],ENT_QUOTES,'UTF-8'));
    $nom_ape = strtoupper(htmlspecialchars($_POST['nom_ape'],ENT_QUOTES,'UTF-8'));
    $celu1 = strtoupper(htmlspecialchars($_POST['celu1'],ENT_QUOTES,'UTF-8'));
    $celu2 = htmlspecialchars($_POST['celu2'],ENT_QUOTES,'UTF-8');
    $proc = strtoupper(htmlspecialchars($_POST['proc'],ENT_QUOTES,'UTF-8'));
    $dire = strtoupper(htmlspecialchars($_POST['dire'],ENT_QUOTES,'UTF-8'));
    $fotoactual = htmlspecialchars($_POST['fotoactual'],ENT_QUOTES,'UTF-8');
    $nombrefoto = htmlspecialchars($_POST['nombrefoto'],ENT_QUOTES,'UTF-8');

    //DATOS DEL USUARIO //
    $marca = htmlspecialchars($_POST['marca'],ENT_QUOTES,'UTF-8');
    $placa = htmlspecialchars($_POST['placa'],ENT_QUOTES,'UTF-8');
    $clase_cate = htmlspecialchars($_POST['clase_cate'],ENT_QUOTES,'UTF-8');
    $nro_lice = htmlspecialchars($_POST['nro_lice'],ENT_QUOTES,'UTF-8');
    $fec_ven = htmlspecialchars($_POST['fec_ven'],ENT_QUOTES,'UTF-8');
    $esta = htmlspecialchars($_POST['esta'],ENT_QUOTES,'UTF-8');
    $idusuario = htmlspecialchars($_POST['idusuario'],ENT_QUOTES,'UTF-8');

    if (empty($nombrefoto)) {
        $ruta = $fotoactual;
    } else {
        if ($nombrefoto == 'controller/choferes/fotos/') {
            $ruta = $nombrefoto; // Simplemente usa el nombre sin modificarlo
        } else {
            $ruta = 'controller/choferes/fotos/' . $nombrefoto; // Construye la ruta completa para la nueva foto
        }
    }
    
    if (!empty($nombrefoto)) {
        if ($nombrefoto != 'controller/choferes/fotos/' && move_uploaded_file($_FILES['foto']['tmp_name'], "fotos/" . $nombrefoto)) {
            $ruta = 'controller/choferes/fotos/' . $nombrefoto;
        } else {
            $ruta = $fotoactual;
        }
    }
    
    $consulta = $MCH->Modificar_Choferes($id,$dni, $nom_ape, $celu1, $celu2, $proc, $dire, $ruta,$marca,$placa,$clase_cate,$nro_lice,$fec_ven,$esta,$idusuario);
    echo $consulta;
    
    if ($consulta == 1) {
        if (!empty($nombrefoto) && $nombrefoto != 'controller/choferes/fotos/') {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], "fotos/" . $nombrefoto)) {
                unlink('../../' . $fotoactual);
            }
        }
    }
?>