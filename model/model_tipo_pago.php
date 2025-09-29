<?php
    require_once 'model_conexion.php';

    class Modelo_TipoPago extends conexionBD{
        

        public function Listar_Sucursal(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_TIPO_PAGO()";
            $query  = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Registrar_Tipopago($tipopa,$desc){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_TIPO_PAGO(?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$tipopa);
            $query ->bindParam(2,$desc);

            $resultado = $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
        public function Modificar_TipoPago($id,$tipopa,$desc,$esta){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_TIPO_PAGO(?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$tipopa);
            $query ->bindParam(3,$desc);
            $query ->bindParam(4,$esta);

            $resultado = $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
        public function Eliminar_Tipo_pago($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_TIPO_PAGO(?)";
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
        public function Cargar_Select_Tipopago(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_SELECT_TIPOPAGO()";
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