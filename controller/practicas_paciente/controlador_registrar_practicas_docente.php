<?php
    require '../../model/model_practicas_paciente.php';
    $MPP = new Modelo_Practicas_Paciente();//Instaciamos

// DATOS DE FACTURA
$area = strtoupper(htmlspecialchars($_POST['area'],ENT_QUOTES,'UTF-8'));
$paciente = strtoupper(htmlspecialchars($_POST['paciente'],ENT_QUOTES,'UTF-8')); 
$total = strtoupper(htmlspecialchars($_POST['total'],ENT_QUOTES,'UTF-8'));
$histocli = htmlspecialchars($_POST['histocli'], ENT_QUOTES, 'UTF-8');

// DATOS DEL USUARIO
$idusu = htmlspecialchars($_POST['idusu'], ENT_QUOTES, 'UTF-8');

// Definir rutas de almacenamiento
$rutaPracticaPaci = 'controller/practicas_paciente/filepracticas/' . $histocli;

// Registrar en la base de datos
$consulta = $MPP->Registrar_practica($area,$paciente,$total,$rutaPracticaPaci,$idusu);

if ($consulta) {
    // Subir archivo de factura si existe
    if ($histocli!="" ) {
        move_uploaded_file($_FILES['hc']['tmp_name'], "filepracticas/" . $histocli);
    }
  
    echo $consulta;
}
?>
