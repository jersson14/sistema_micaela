<?php
require_once 'model_conexion.php';

class Modelo_Tickets extends conexionBD {
    
         public function Registrar_Ticket($tipodocemi, $documento, $nomemi, $celemi, $ser, $ori, $des, $basegr, $igv, $total, $idusu) {
            $c = conexionBD::conexionPDO();

            $sql = "CALL SP_REGISTRAR_TICKET(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $query = $c->prepare($sql);

            $query->bindParam(1, $tipodocemi);
            $query->bindParam(2, $documento);
            $query->bindParam(3, $nomemi);
            $query->bindParam(4, $celemi);
            $query->bindParam(5, $ser);
            $query->bindParam(6, $ori);
            $query->bindParam(7, $des);
            $query->bindParam(8, $basegr);
            $query->bindParam(9, $igv);
            $query->bindParam(10, $total);
            $query->bindParam(11, $idusu);

            $query->execute(); // EJECUTAR SOLO UNA VEZ

            $result = $query->fetchColumn(); // tomar resultado

            $query->closeCursor(); // ← IMPORTANTE PARA PROCEDIMIENTOS

            conexionBD::cerrar_conexion();

            return $result;
        }

            public function Listar_Tickets(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_NOTA_SALIDA()";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }

        
            public function Listar_Tickets_por_dia(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_NOTA_SALIDA_POR_DIA()";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }

         public function Listar_nota_ruta_estado($ori,$des,$esta){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_NOTA_RUTA_ESTADO(?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$ori);
            $query->bindParam(2,$des);
            $query->bindParam(3,$esta);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
 public function Listar_nota_fecha_usuario($fedes,$fehas,$usu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_NOTA_FECHA_USUARIO(?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$fedes);
            $query->bindParam(2,$fehas);
            $query->bindParam(3,$usu);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
    public function Anular_nota_salida($id,$motivo){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ANULAR_NOTA_SALIDA(?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$motivo);  

            $resul = $query->execute();
            if($resul){
                return 1;
            }else{
                return 0;
            }
            conexionBD::cerrar_conexion();
        }

     
}
?>