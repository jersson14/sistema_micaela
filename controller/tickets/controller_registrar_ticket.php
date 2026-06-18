<?php
require_once '../../utilitario/session_config.php';
require_once '../../model/model_tickets.php';

$MT = new Modelo_Tickets();

$tipodocemi = htmlspecialchars($_POST['tipodocemi'], ENT_QUOTES, 'UTF-8');
$documento = htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8');
$nomemi = htmlspecialchars($_POST['nomemi'], ENT_QUOTES, 'UTF-8');
$celemi = htmlspecialchars($_POST['celemi'], ENT_QUOTES, 'UTF-8');
$ser = htmlspecialchars($_POST['ser'], ENT_QUOTES, 'UTF-8');
$ori = htmlspecialchars($_POST['ori'], ENT_QUOTES, 'UTF-8');
$des = htmlspecialchars($_POST['des'], ENT_QUOTES, 'UTF-8');
$basegr = htmlspecialchars($_POST['basegr'], ENT_QUOTES, 'UTF-8');
$igv = htmlspecialchars($_POST['igv'], ENT_QUOTES, 'UTF-8');
$total = htmlspecialchars($_POST['total'], ENT_QUOTES, 'UTF-8');
$idusu = htmlspecialchars($_POST['idusu'], ENT_QUOTES, 'UTF-8');


$consulta = $MT->Registrar_Ticket(
    $tipodocemi,
    $documento,
    $nomemi,
    $celemi,
    $ser,
    $ori,
    $des,
    $basegr,
    $igv,
    $total,
    $idusu
);

echo $consulta;
?>
