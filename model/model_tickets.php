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

        // VISTAS ASISTENTE
        public function Listar_ticket_asis($ori){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_NOTA_SALIDA_ASIS(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$ori);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }

 // VISTAS ASISTENTE
        public function Listar_ticket_pordia_asis($ori){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_NOTA_PORDIA_ASIS(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$ori);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Listar_nota_fecha_ori_asis($fechaini,$fechafin,$esta,$ori){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_NOTA_FECHA_ESTA_ASIS(?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$fechaini);
            $query->bindParam(2,$fechafin);
            $query->bindParam(3,$esta);
            $query->bindParam(4,$ori);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        // CRUD
         public function Modificar_Ticket($idnota,$tipo_doc,$dniemi,$nomemi,$celemi,$ser,$ori,$des,$basegr,$igv,$total){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_TICKET(?,?,?,?,?,?,?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$idnota);
            $query ->bindParam(2,$tipo_doc);
            $query ->bindParam(3,$dniemi);
            $query ->bindParam(4,$nomemi);
            $query ->bindParam(5,$celemi);
            $query ->bindParam(6,$ser);
            $query ->bindParam(7,$ori);
            $query ->bindParam(8,$des);
            $query ->bindParam(9,$basegr);
            $query ->bindParam(10,$igv);
            $query ->bindParam(11,$total);
    $resul = $query->execute();
            if($resul){
                return 1;
            }else{
                return 0;
            }
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