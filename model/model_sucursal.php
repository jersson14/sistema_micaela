<?php
    require_once 'model_conexion.php';

    class Modelo_Sucursal extends conexionBD{
        

        public function Listar_Sucursal(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_SUCURSAL()";
            $query  = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Registrar_Sucursal($sucur,$tele1,$tele2,$direc,$desc){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_SUCURSAL(?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$sucur);
            $query ->bindParam(2,$tele1);
            $query ->bindParam(3,$tele2);
            $query ->bindParam(4,$direc);
            $query ->bindParam(5,$desc);

            $resultado = $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
        public function Modificar_Sucursal($id,$sucu,$tele1,$tele2,$direc,$desc,$esta){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_SUCURSAL(?,?,?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$sucu);
            $query ->bindParam(3,$tele1);
            $query ->bindParam(4,$tele2);
            $query ->bindParam(5,$direc);
            $query ->bindParam(6,$desc);
            $query ->bindParam(7,$esta);

            $resultado = $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
        public function Eliminar_Sucursal($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_SUCURSAL(?)";
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
        public function Cargar_Select_Sucursal(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_SELECT_SUCURSAL()";
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