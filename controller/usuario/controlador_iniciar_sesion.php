<?php
    require '../../model/model_usuario.php';
    require '../../utilitario/jwt_config.php';
    require '../../utilitario/JWTHelper.php';
    
    $MU = new Modelo_Usuario();
    $usu = htmlspecialchars($_POST['u'],ENT_QUOTES,'UTF-8');
    $con = htmlspecialchars($_POST['c'],ENT_QUOTES,'UTF-8');
    $consulta = $MU->Verificar_Usuario($usu,$con);
    
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
        
        // Generar access token
        $accessToken = JWTHelper::generateToken($userData, JWT_ACCESS_TOKEN_EXPIRATION);
        
        // Generar refresh token (7 días)
        $refreshToken = JWTHelper::generateRefreshToken($userData);
        
        // Agregar tokens a la respuesta
        $response = array(
            'success' => true,
            'data' => $consulta[0],
            'tokens' => array(
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'expires_in' => JWT_ACCESS_TOKEN_EXPIRATION * 3600
            )
        );
        
        echo json_encode($response);
    }else{
        echo json_encode(array('success' => false, 'message' => 'Credenciales incorrectas'));
    }
?>
