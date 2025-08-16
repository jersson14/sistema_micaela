<?php
require '../../model/model_choferes.php';
$MCH = new Modelo_Choferes();

// DATOS DE CHOFER
$tipo_doc = strtoupper(htmlspecialchars($_POST['tipo_doc'],ENT_QUOTES,'UTF-8'));
$documentoFinal = strtoupper(htmlspecialchars($_POST['documentoFinal'],ENT_QUOTES,'UTF-8'));
$nom_ape = strtoupper(htmlspecialchars($_POST['nom_ape'],ENT_QUOTES,'UTF-8'));
$celu = strtoupper(htmlspecialchars($_POST['celu'],ENT_QUOTES,'UTF-8'));
$celu2 = htmlspecialchars($_POST['celu2'],ENT_QUOTES,'UTF-8');
$proc = strtoupper(htmlspecialchars($_POST['proc'],ENT_QUOTES,'UTF-8'));
$dire = strtoupper(htmlspecialchars($_POST['dire'],ENT_QUOTES,'UTF-8'));
$nombrefoto = htmlspecialchars($_POST['nombrefoto'],ENT_QUOTES,'UTF-8');

// DATOS DEL USUARIO
$marca = htmlspecialchars($_POST['marca'],ENT_QUOTES,'UTF-8');
$placa = htmlspecialchars($_POST['placa'],ENT_QUOTES,'UTF-8');
$clase_cate = htmlspecialchars($_POST['clase_cate'],ENT_QUOTES,'UTF-8');
$nro_lice = htmlspecialchars($_POST['nro_lice'],ENT_QUOTES,'UTF-8');
$fec_ven = htmlspecialchars($_POST['fec_ven'],ENT_QUOTES,'UTF-8');
$idusuario = htmlspecialchars($_POST['idusuario'],ENT_QUOTES,'UTF-8');

// Validamos si se subió una nueva foto
$tieneNuevaFoto = isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK;

$ruta = 'controller/choferes/fotos/' . $nombrefoto;

$consulta = $MCH->Registrar_choferes($tipo_doc, $documentoFinal, $nom_ape, $celu, $celu2, $proc, $dire, $ruta, $marca, $placa, $clase_cate, $nro_lice, $fec_ven, $idusuario);

if ($consulta) {
    if ($tieneNuevaFoto) {
        move_uploaded_file($_FILES['foto']['tmp_name'], "fotos/" . $nombrefoto);
    }
    echo $consulta;
}
?>
