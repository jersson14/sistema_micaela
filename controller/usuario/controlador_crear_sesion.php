<?php
    // Incluir configuración de sesiones
    require_once '../../utilitario/session_config.php';
    
    // Verificar que llegaron los datos
    if (!isset($_POST['idusuario']) || empty($_POST['idusuario'])) {
        echo json_encode(array('success' => false, 'message' => 'No se recibieron datos de usuario'));
        exit;
    }
    
    $idusuario = htmlspecialchars($_POST['idusuario'],ENT_QUOTES,'UTF-8');
    $DNIusuario = htmlspecialchars($_POST['DNIusuario'],ENT_QUOTES,'UTF-8');
    $usuario = htmlspecialchars($_POST['usuario'],ENT_QUOTES,'UTF-8');
    $rol = htmlspecialchars($_POST['rol'],ENT_QUOTES,'UTF-8');
    $solonombres = htmlspecialchars($_POST['solonombres'],ENT_QUOTES,'UTF-8');
    $nombres = htmlspecialchars($_POST['nombres'],ENT_QUOTES,'UTF-8');
    $foto = htmlspecialchars($_POST['foto'],ENT_QUOTES,'UTF-8');
    $foto_empresa = htmlspecialchars($_POST['foto_empresa'],ENT_QUOTES,'UTF-8');
    $razon = htmlspecialchars($_POST['razon'],ENT_QUOTES,'UTF-8');
    $nombre_rol = htmlspecialchars($_POST['nombre_rol'],ENT_QUOTES,'UTF-8');
    $sucursal = htmlspecialchars($_POST['sucursal'],ENT_QUOTES,'UTF-8');

    // Establecer variables de sesión
    $_SESSION['S_ID']=$idusuario;
    $_SESSION['S_DNIUSUARIO']=$DNIusuario;
    $_SESSION['S_USU']=$usuario;
    $_SESSION['S_ROL']=$rol;
    $_SESSION['S_COMPLETOS']=$solonombres;
    $_SESSION['S_NOMBRE']=$nombres;
    $_SESSION['S_FOTO']=$foto;
    $_SESSION['S_FOTO_EMPRESA']=$foto_empresa;
    $_SESSION['S_RAZON']=$razon;
    $_SESSION['S_NOMBRE_ROL']=$nombre_rol;
    $_SESSION['S_SUCURSAL']=$sucursal;
    
    // Guardar tiempo de login para expiración
    $_SESSION['LOGIN_TIME']=time();
    $_SESSION['LAST_ACTIVITY']=time();
    
    // Forzar escritura de sesión
    session_write_close();
    
    // Confirmar que la sesión se creó
    echo json_encode(array(
        'success' => true, 
        'message' => 'Sesión creada correctamente',
        'session_id' => session_id(),
        'data' => array(
            'id' => $idusuario,
            'usuario' => $usuario,
            'rol' => $rol
        )
    ));
?>