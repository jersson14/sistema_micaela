<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    try {
        require '../../model/model_usuario.php';
        // JWT desactivado temporalmente para producción
        // require '../../utilitario/JWTHelper.php';
        
        // Verificar que lleguen los datos POST
        if (!isset($_POST['u']) || !isset($_POST['c'])) {
            echo json_encode(array('success' => false, 'message' => 'Faltan datos de usuario o contraseña'));
            exit;
        }
        
        $MU = new Modelo_Usuario();
        $usu = htmlspecialchars($_POST['u'],ENT_QUOTES,'UTF-8');
        $con = htmlspecialchars($_POST['c'],ENT_QUOTES,'UTF-8');
        $consulta = $MU->Verificar_Usuario($usu,$con);
    } catch (Exception $e) {
        echo json_encode(array('success' => false, 'message' => 'Error: ' . $e->getMessage()));
        exit;
    }
    
    if(count($consulta)>0){
        // Respuesta simple sin JWT (modo compatible)
        echo json_encode($consulta);
    }else{
        echo json_encode(array());
    }
?>