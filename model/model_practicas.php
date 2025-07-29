<?php
    require_once 'model_conexion.php';

    class Modelo_Practicas extends conexionBD{
        

        public function Listar_Practicas(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PRACTICAS()";
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
        public function Listar_practicas_filtro($obra,$fechaini,$fechafin){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PRACTICAS_FILTRO(?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$obra);
            $query->bindParam(2,$fechaini);
            $query->bindParam(3,$fechafin);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
         public function Registrar_Practicas($code,$pract,$valor,$obra,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_PRACTICAS(?,?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$code);
            $query ->bindParam(2,$pract);
            $query ->bindParam(3,$valor);
            $query ->bindParam(4,$obra);
            $query ->bindParam(5,$idusu);

            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
        public function Modificar_Practicas($id,$code,$pract,$valor,$obra,$status,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_PRACTICAS(?,?,?,?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$code);
            $query ->bindParam(3,$pract);
            $query ->bindParam(4,$valor);
            $query ->bindParam(5,$obra);
            $query ->bindParam(6,$status);
            $query ->bindParam(7,$idusu);

            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
        public function Eliminar_Practicas($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_PRACTICA(?)";
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
        public function Listar_Historial_practicas($id){
            $c = conexionBD::conexionPDO();
            $arreglo = array();
            $sql = "CALL SP_LISTA_HISTORIAL_PRACTICAS(?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        
        }
    }




?>