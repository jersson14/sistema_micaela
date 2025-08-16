<?php
    require_once 'model_conexion.php';

    class Modelo_Clientes extends conexionBD{
        

        public function Listar_Clientes(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_CLIENTES()";
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
      
        public function Modificar_Clientes($id,$tipo,$nro,$nombre,$proce,$celular,$direccion,$email){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_CLIENTES(?,?,?,?,?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$tipo);
            $query ->bindParam(3,$nro);
            $query ->bindParam(4,$nombre);
            $query ->bindParam(5,$proce);
            $query ->bindParam(6,$celular);
            $query ->bindParam(7,$direccion);
            $query ->bindParam(8,$email);
            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }

        public function Eliminar_Clientes($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_CLIENTE(?)";
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
      
    }




?>