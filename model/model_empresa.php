<?php
    require_once 'model_conexion.php';

    class Modelo_Empresa extends conexionBD{
        

        public function Listar_Empresa(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_EMPRESA()";
            $query  = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Modificar_Empresa($id,$nom,$raz,$nomco,$tipo_doc,$nro_doc,$email,$codi,$tele,$dire,$ubi,$urb,$dis,$pro,$dep,$codpa,$ususol,$passol){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_EMPRESA(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$nom);
            $query ->bindParam(3,$raz);
            $query ->bindParam(4,$nomco);
            $query ->bindParam(5,$tipo_doc);
            $query ->bindParam(6,$nro_doc);
            $query ->bindParam(7,$email);
            $query ->bindParam(8,$codi);
            $query ->bindParam(9,$tele);
            $query ->bindParam(10,$dire);
            $query ->bindParam(11,$ubi);
            $query ->bindParam(12,$urb);
            $query ->bindParam(13,$dis);
            $query ->bindParam(14,$pro);
            $query ->bindParam(15,$dep);
            $query ->bindParam(16,$codpa);
            $query ->bindParam(17,$ususol);
            $query ->bindParam(18,$passol);

            $resul = $query->execute();
            if($resul){
                return 1;
            }else{
                return 0;
            }
            conexionBD::cerrar_conexion();
        }
        public function Modificar_foto_empresa($id,$ruta){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_EMPRESA_FOTO(?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$ruta);

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