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
        public function Modificar_Empresa($id,$nom,$raz,$nomco,$nro_doc,$email,$codi,$tele,$dire,$ubi,$urb,$dis,$pro,$dep,$codpa,$ususol,$passol,$endpoint){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_EMPRESA(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$nom);
            $query ->bindParam(3,$raz);
            $query ->bindParam(4,$nomco);
            $query ->bindParam(5,$nro_doc);
            $query ->bindParam(6,$email);
            $query ->bindParam(7,$codi);
            $query ->bindParam(8,$tele);
            $query ->bindParam(9,$dire);
            $query ->bindParam(10,$ubi);
            $query ->bindParam(11,$urb);
            $query ->bindParam(12,$dis);
            $query ->bindParam(13,$pro);
            $query ->bindParam(14,$dep);
            $query ->bindParam(15,$codpa);
            $query ->bindParam(16,$ususol);
            $query ->bindParam(17,$passol);
            $query ->bindParam(18,$endpoint);

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