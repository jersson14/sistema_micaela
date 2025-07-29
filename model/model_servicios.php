<?php
    require_once 'model_conexion.php';

    class Modelo_Servicios extends conexionBD{
        

        public function Listar_Servicios(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_SERVICIOS()";
            $query  = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Registrar_Servicios($serv,$cost,$desc,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_SERVICIOS(?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$serv);
            $query ->bindParam(2,$cost);
            $query ->bindParam(3,$desc);
            $query ->bindParam(4,$idusu);

            $resultado = $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
        public function Modificar_Servicios($id,$serv,$cost,$desc,$estado,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_SERVICIOS(?,?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$serv);
            $query ->bindParam(3,$cost);
            $query ->bindParam(4,$desc);
            $query ->bindParam(5,$estado);
            $query ->bindParam(6,$idusu);
            $resultado = $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }

        public function Eliminar_Servicios($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_SERVICIOS(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
    
            $resul = $query->execute();
            if($resul){
                return 1;
            }else{
                return 0;
            }
            conexionBD::cerrar_conexion();
        }
                public function Cargar_Select_Obras_Sociales(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_OBRAS_SOCIALES()";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetchAll();
            foreach($resultado as $resp){
                $arreglo[]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
    }




?>