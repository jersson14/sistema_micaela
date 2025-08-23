<?php
    require_once 'model_conexion.php';

    class Modelo_Rutas extends conexionBD{
        

        public function Listar_Rutas(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_RUTAS()";
            $query  = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Registrar_Ruta($nom,$desc){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_RUTA(?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$nom);
            $query ->bindParam(2,$desc);

            $resultado = $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
        public function Modificar_Ruta($id,$nom,$desc,$estado){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_RUTA(?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$nom);
            $query ->bindParam(3,$desc);
            $query ->bindParam(4,$estado);
            $resultado = $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }

        public function Eliminar_Ruta($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_RUTA(?)";
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
        public function Cargar_Select_Rutas(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_RUTAS()";
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