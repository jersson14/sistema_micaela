<?php
require_once '../../utilitario/session_config.php';
require_once '../../model/model_tickets.php';

$MT = new Modelo_Tickets();

$idnota = htmlspecialchars($_POST['idnota'], ENT_QUOTES, 'UTF-8');
$tipo_doc = htmlspecialchars($_POST['tipo_doc'], ENT_QUOTES, 'UTF-8');
$dniemi = htmlspecialchars($_POST['dniemi'], ENT_QUOTES, 'UTF-8');
$nomemi = htmlspecialchars($_POST['nomemi'], ENT_QUOTES, 'UTF-8');
$celemi = htmlspecialchars($_POST['celemi'], ENT_QUOTES, 'UTF-8');
$ser = htmlspecialchars($_POST['ser'], ENT_QUOTES, 'UTF-8');
$ori = htmlspecialchars($_POST['ori'], ENT_QUOTES, 'UTF-8');
$des = htmlspecialchars($_POST['des'], ENT_QUOTES, 'UTF-8');
$basegr = htmlspecialchars($_POST['basegr'], ENT_QUOTES, 'UTF-8');
$igv = htmlspecialchars($_POST['igv'], ENT_QUOTES, 'UTF-8');
$total = htmlspecialchars($_POST['total'], ENT_QUOTES, 'UTF-8');

// 🔍 DEBUG TEMPORAL - ELIMINAR DESPUÉS
error_log("================================");
error_log("ID NOTA RECIBIDO: " . $idnota);
error_log("TIPO: " . gettype($idnota));
error_log("TODOS LOS POST: " . json_encode($_POST));
error_log("================================");

$consulta = $MT->Modificar_Ticket(
    $idnota,
    $tipo_doc,
    $dniemi,
    $nomemi,
    $celemi,
    $ser,
    $ori,
    $des,
    $basegr,
    $igv,
    $total
);

// 🔍 DEBUG - Ver qué devuelve el SP
error_log("RESULTADO CONSULTA: " . json_encode($consulta));

echo json_encode($consulta);
?>
