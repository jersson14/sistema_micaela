<?php
    require_once 'model_conexion.php';

    class Modelo_Choferes extends conexionBD{
        
        public function Listar_Choferes(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_CHOFERES()";
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
        public function Registrar_Usuario($dni,$nombre,$apelli,$correo,$tele,$dire,$ruta,$usu,$contra,$rol,$sucu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_USUARIO(?,?,?,?,?,?,?,?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$dni);
            $query ->bindParam(2,$nombre);
            $query ->bindParam(3,$apelli);
            $query ->bindParam(4,$correo);
            $query ->bindParam(5,$tele);
            $query ->bindParam(6,$dire);
            $query ->bindParam(7,$ruta);
            $query ->bindParam(8,$usu);
            $query ->bindParam(9,$contra);
            $query ->bindParam(10,$rol);
            $query ->bindParam(11,$sucu);
            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
        public function Modificar_Usuario($id,$dni, $nombre, $apelli, $correo, $tele, $dire, $ruta,$usu,$rol,$sucu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_USUARIO(?,?,?,?,?,?,?,?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$dni);
            $query ->bindParam(3,$nombre);
            $query ->bindParam(4,$apelli);
            $query ->bindParam(5,$correo);
            $query ->bindParam(6,$tele);
            $query ->bindParam(7,$dire);
            $query ->bindParam(8,$ruta);
            $query ->bindParam(9,$usu);
            $query ->bindParam(10,$rol);
            $query ->bindParam(11,$sucu);
            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
      
    }




?>