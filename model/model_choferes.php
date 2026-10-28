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
            $query->closeCursor();
            $silenciados = $this->Choferes_Sin_Notificacion($c);
            foreach($resultado as $resp){
                $resp['notificar_licencia'] = isset($silenciados[$resp['id_chofer']]) ? 0 : 1;
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }   
          public function Listar_Choferes_vencidos(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_CHOFERES_VENCIDOS()";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();
            // Los choferes con las notificaciones desactivadas no aparecen en el aviso de vencimientos
            $silenciados = $this->Choferes_Sin_Notificacion($c);
            foreach($resultado as $resp){
                if(isset($silenciados[$resp['id_chofer']])) continue;
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }   
        public function Registrar_choferes($tipo_doc,$documentoFinal,$nom_ape,$celu,$celu2,$proc,$dire,$ruta,$marca,$placa,$clase_cate,$nro_lice,$fec_ven,$idusuario){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_CHOFERES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$tipo_doc);
            $query ->bindParam(2,$documentoFinal);
            $query ->bindParam(3,$nom_ape);
            $query ->bindParam(4,$celu);
            $query ->bindParam(5,$celu2);
            $query ->bindParam(6,$proc);
            $query ->bindParam(7,$dire);
            $query ->bindParam(8,$ruta);
            $query ->bindParam(9,$marca);
            $query ->bindParam(10,$placa);
            $query ->bindParam(11,$clase_cate);
            $query ->bindParam(12,$nro_lice);
            $query ->bindParam(13,$fec_ven);
            $query ->bindParam(14,$idusuario);
            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
        public function Modificar_Choferes($id,$dni, $nom_ape, $celu1, $celu2, $proc, $dire, $ruta,$marca,$placa,$clase_cate,$nro_lice,$fec_ven,$esta,$idusuario){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_CHOFERES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$dni);
            $query ->bindParam(3,$nom_ape);
            $query ->bindParam(4,$celu1);
            $query ->bindParam(5,$celu2);
            $query ->bindParam(6,$proc);
            $query ->bindParam(7,$dire);
            $query ->bindParam(8,$ruta);
            $query ->bindParam(9,$marca);
            $query ->bindParam(10,$placa);
            $query ->bindParam(11,$clase_cate);
            $query ->bindParam(12,$nro_lice);
            $query ->bindParam(13,$fec_ven);
            $query ->bindParam(14,$esta);
            $query ->bindParam(15,$idusuario);

            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
        // Ids de choferes con el aviso de vencimiento de licencia desactivado.
        // Si la columna aún no existe (base sin migrar) se avisa de todos.
        private function Choferes_Sin_Notificacion($c){
            try{
                $query = $c->query("SELECT id_chofer FROM choferes WHERE notificar_licencia = 0");
                return array_flip($query->fetchAll(PDO::FETCH_COLUMN));
            }catch(PDOException $e){
                return array();
            }
        }

        public function Cambiar_Notificacion_Chofer($id,$notificar){
            $c = conexionBD::conexionPDO();
            $query = $c->prepare("UPDATE choferes SET notificar_licencia = ?, updated_at = NOW() WHERE id_chofer = ?");
            $resul = $query->execute(array($notificar ? 1 : 0, (int)$id));
            conexionBD::cerrar_conexion();
            return $resul ? 1 : 0;
        }

        public function Eliminar_Chofer($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_CHOFERES(?)";
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
        public function Cargar_Select_Choferes(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_SELECT_CHOFERES()";
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
          public function Cargar_Select_Choferes_Unico($dni){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_SELECT_CHOFERES_DNI(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$dni);
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