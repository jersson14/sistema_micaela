<?php
require_once 'model_conexion.php';

class Modelo_Usuario extends conexionBD
{

    public function Verificar_Usuario($usu, $con)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_VERIFICAR_USUARIO(?)";
        $arreglo = array();
        $query = $c->prepare($sql);
        $query->bindParam(1, $usu);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            if (password_verify($con, $resp['usu_contrasenia'])) {
                $arreglo[] = $resp;
            }
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function Listar_Usuario()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_USUARIO()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($resultado as $resp) {
            $arreglo["data"][] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function Registrar_Usuario($dni, $nombre, $apelli, $correo, $tele, $dire, $ruta, $usu, $contra, $rol, $sucu)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_REGISTRAR_USUARIO(?,?,?,?,?,?,?,?,?,?,?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $dni);
        $query->bindParam(2, $nombre);
        $query->bindParam(3, $apelli);
        $query->bindParam(4, $correo);
        $query->bindParam(5, $tele);
        $query->bindParam(6, $dire);
        $query->bindParam(7, $ruta);
        $query->bindParam(8, $usu);
        $query->bindParam(9, $contra);
        $query->bindParam(10, $rol);
        $query->bindParam(11, $sucu);
        $query->execute();
        if ($row = $query->fetchColumn()) {
            return $row;
        }
        conexionBD::cerrar_conexion();
    }
    public function Modificar_Usuario($id, $dni, $nombre, $apelli, $correo, $tele, $dire, $ruta, $usu, $rol, $sucu)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_MODIFICAR_USUARIO(?,?,?,?,?,?,?,?,?,?,?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $id);
        $query->bindParam(2, $dni);
        $query->bindParam(3, $nombre);
        $query->bindParam(4, $apelli);
        $query->bindParam(5, $correo);
        $query->bindParam(6, $tele);
        $query->bindParam(7, $dire);
        $query->bindParam(8, $ruta);
        $query->bindParam(9, $usu);
        $query->bindParam(10, $rol);
        $query->bindParam(11, $sucu);
        $query->execute();
        if ($row = $query->fetchColumn()) {
            return $row;
        }
        conexionBD::cerrar_conexion();
    }
    public function Modificar_Usuario_Contra($id, $con)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_MODIFICAR_USUARIO_CONTRA(?,?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $id);
        $query->bindParam(2, $con);
        $resul = $query->execute();
        if ($resul) {
            return 1;
        } else {
            return 0;
        }
        conexionBD::cerrar_conexion();
    }
    public function Modificar_Usuario_Estatus($id, $estatus)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_MODIFICAR_USUARIO_ESTATUS(?,?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $id);
        $query->bindParam(2, $estatus);
        $resul = $query->execute();
        if ($resul) {
            return 1;
        } else {
            return 0;
        }
        conexionBD::cerrar_conexion();
    }

    public function Cargar_Select_Datos_Seguimiento($numero, $dni)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_CARGAR_SEGUIMIENTO_TRAMITE(?,?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $numero);
        $query->bindParam(2, $dni);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function Traer_Datos_Detalle_Seguimiento($codigo)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_CARGAR_SEGUIMIENTO_TRAMITE_DETALLE(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $codigo);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function Listar_notificacion_tramite($idarea)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_NOTIFICACION_TRAMITE(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $idarea);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_servicios()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_SERVICIOS()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_choferes()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_CHOFERES()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_clientes()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_CLIENTES()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_encomiendas_diarias()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_ENCOMIENDAS_DIARIAS()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_encomiendas_semanales()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_ENCOMIENDAS_SEMANALES()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_encomiendas_mes()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_ENCOMIENDAS_MES()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_encomiendas()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_ENCOMIENDAS()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_salidas_dia()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_SALIDAS_DIA()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_salidas_semana()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_SALIDAS_SEMANA()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_salidas_mes()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_SALIDAS_MES()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_salidas()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_SALIDAS()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_ingresos_hoy()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_INGRESOS_HOY()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_gastos_hoy()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_GASTOS_HOY()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_ingresos_mes()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_INGRESOS_MES()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_gastos_mes()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_GASTOS_MES()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_reservas_dia()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_RESERVAS_DIA()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_reservas_semana()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_RESERVAS_SEMANA()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_reservas_mes()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_RESERVAS_MES()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_reservas()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_RESERVAS()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }

    //ASISTENTES

    //ENCOMIENDAS DIARIAS ASISTENTES
    public function listar_total_encomiendas_dia_asis($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_ENCOMIENDAS_DIA_ASIS(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_encomiendas_semana_asis($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_ENCOMIENDAS_SEMANALES_ASIS(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_encomiendas_mes_asis($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_ENCOMIENDAS_MES_ASIS(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_encomiendas_asis($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_ENCOMIENDAS_ASIS(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    //SALIDAS DIARIAS ASISTENTES
    public function listar_total_salidas_dia_asis($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_SALIDAS_DIA_ASIS(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_salidas_semana_asis($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_SALIDAS_SEMANALES_ASIS(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_salidas_mes_asis($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_SALIDAS_MES_ASIS(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_salidas_asis($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_SALIDAS_ASIS(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    //RESERVAS ASISTENTETES
    public function listar_total_reservas_dia_asis($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_RESERVAS_DIA_ASIST(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_reservas_semanales_asis($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_RESERVAS_SEMANALES_ASIST(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_reservas_mes_asis($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_RESERVAS_MES_ASIST(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_reservas_asis($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_RESERVAS_ASIST(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    //COMPROBANTES
    public function listar_total_comprobantes()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_cOMPROBANTES()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_facturas()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_FACTURAS()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
     public function listar_total_boletas()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_BOLETAS()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
     public function listar_total_notas_credito()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_NOTAS_CREDITO()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
         public function listar_total_notas_debito()
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_NOTAS_DEBITO()";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    // COMPROBANTES SUCURSALES
     public function listar_total_facturas_sucu($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_FACTURAS_SUCU(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_boletas_sucu($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_BOLETAS_SUCU(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_nota_credito_sucu($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_NOTA_CREDITO_SUCU(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
    public function listar_total_nota_debito_sucu($ori)
    {
        $c = conexionBD::conexionPDO();
        $sql = "CALL SP_LISTAR_TOTAL_NOTA_DEBITO_SUCU(?)";
        $arreglo = array();
        $query  = $c->prepare($sql);
        $query->bindParam(1, $ori);
        $query->execute();
        $resultado = $query->fetchAll();
        foreach ($resultado as $resp) {
            $arreglo[] = $resp;
        }
        return $arreglo;
        conexionBD::cerrar_conexion();
    }
}
