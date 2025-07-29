<?php
    require_once 'model_conexion.php';

    class Modelo_Roles extends conexionBD{
        

        public function Listar_Roles(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_ROLES()";
            $query  = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Registrar_Rol($rol,$descripcion,$estado,$fecha){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_ROLES(?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$rol);
            $query ->bindParam(2,$descripcion);
            $query ->bindParam(3,$estado);
            $query ->bindParam(4,$fecha);

            $resultado = $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
        public function Modificar_Rol($id,$rol,$descripcion,$estado){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_ROL(?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$rol);
            $query ->bindParam(3,$descripcion);
            $query ->bindParam(4,$estado);

            $resultado = $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
        public function Eliminar_Rol($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_ROL(?)";
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
        public function Cargar_Select_Roles(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_SELECT_ROLES()";
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