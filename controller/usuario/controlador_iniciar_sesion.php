<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    try {
        require '../../model/model_usuario.php';
        require '../../utilitario/JWTHelper.php';
        
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
        // Generar tokens JWT
        $userData = array(
            'id_usuario' => $consulta[0][0],
            'dni_usuario' => $consulta[0][1],
            'nombre' => $consulta[0][2],
            'nombres_completos' => $consulta[0][4],
            'usuario' => $consulta[0][7],
            'id_role' => $consulta[0][9],
            'foto' => $consulta[0][15],
            'foto_empresa' => $consulta[0][17],
            'razon' => $consulta[0][18],
            'nombre_rol' => $consulta[0][19],
            'sucursal' => $consulta[0][20]
        );
        
        // Generar access token (2 horas)
        $accessToken = JWTHelper::generateToken($userData, 2);
        
        // Generar refresh token (7 días)
        $refreshToken = JWTHelper::generateRefreshToken($userData);
        
        // Agregar tokens a la respuesta
        $response = array(
            'success' => true,
            'data' => $consulta[0],
            'tokens' => array(
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'expires_in' => 7200 // 2 horas en segundos
            )
        );
        
        echo json_encode($response);
    }else{
        echo json_encode(array('success' => false, 'message' => 'Credenciales incorrectas'));
    }
?>