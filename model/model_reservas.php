<?php
    require_once 'model_conexion.php';

    class Modelo_Reservas extends conexionBD{
        

        public function Listar_Reservas(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_RESERVAS()";
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
          public function Listar_Reservas_podia(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_RESERVAS_PORDIA()";
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
         public function Listar_reservas_ruta_estado($ori,$des,$esta){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_RESERVAS_RUTA_ESTADO(?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$ori);
            $query->bindParam(2,$des);
            $query->bindParam(3,$esta);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Listar_reservas_fecha_usuario($fedes,$fehas,$usu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_RESERVAS_FECHA_USUARIO(?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$fedes);
            $query->bindParam(2,$fehas);
            $query->bindParam(3,$usu);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Cargar_Usuarios(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_USUARIOS()";
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
        public function Listar_Facturas_todo(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_FACTURAS_TODO()";
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
       
        public function Listar_facturas_edtado_obra($obra,$estado){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_FACTURAS_OBRA_ESTADO(?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$obra);
            $query->bindParam(2,$estado);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Listar_facturas_fecha_usu($fechaini,$fechafin,$usu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_FACTURAS_FECHAS_USU(?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$fechaini);
            $query->bindParam(2,$fechafin);
            $query->bindParam(3,$usu);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
      
        public function Registrar_Encomiendas($conduc,$ori,$des,$fecha,$desc,$tipodocemi,$documentoFinal,$nomemi,$celemi,$tipodocrece,$documentoFinal2,$nomrece,$celurece,$pago,$porpagar,$adomicilio,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_ENCOMIENDA(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$conduc);
            $query ->bindParam(2,$ori);
            $query ->bindParam(3,$des);
            $query ->bindParam(4,$fecha);
            $query ->bindParam(5,$desc);
            $query ->bindParam(6,$tipodocemi);
            $query ->bindParam(7,$documentoFinal);
            $query ->bindParam(8,$nomemi);
            $query ->bindParam(9,$celemi);
            $query ->bindParam(10,$tipodocrece);
            $query ->bindParam(11,$documentoFinal2);
            $query ->bindParam(12,$nomrece);
            $query ->bindParam(13,$celurece);
            $query ->bindParam(14,$pago);
            $query ->bindParam(15,$porpagar);
            $query ->bindParam(16,$adomicilio);
            $query ->bindParam(17,$idusu);

            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();

        }
        public function Editar_Encomiendas($id,$conduc,$ori,$des,$fecha,$desc,$tipodocemi,$documentoFinal,$nomemi,$celemi,$tipodocrece,$documentoFinal2,$nomrece,$celurece,$pago,$porpagar,$adomicilio,$obse,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_ENCOMIENDA(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$conduc);
            $query ->bindParam(3,$ori);
            $query ->bindParam(4,$des);
            $query ->bindParam(5,$fecha);
            $query ->bindParam(6,$desc);
            $query ->bindParam(7,$tipodocemi);
            $query ->bindParam(8,$documentoFinal);
            $query ->bindParam(9,$nomemi);
            $query ->bindParam(10,$celemi);
            $query ->bindParam(11,$tipodocrece);
            $query ->bindParam(12,$documentoFinal2);
            $query ->bindParam(13,$nomrece);
            $query ->bindParam(14,$celurece);
            $query ->bindParam(15,$pago);
            $query ->bindParam(16,$porpagar);
            $query ->bindParam(17,$adomicilio);
            $query ->bindParam(18,$obse);
            $query ->bindParam(19,$idusu);

            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();

        }
        public function Modificar_Estado($id,$nuevo_estado,$observacion,$anula,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_ESTADO_ENCOMIENDA(?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$nuevo_estado);
            $query ->bindParam(3,$observacion);
            $query ->bindParam(4,$anula);
            $query ->bindParam(5,$idusu);

            $resul = $query->execute();
            if($resul){
                return 1;
            }else{
                return 0;
            }
            conexionBD::cerrar_conexion();

        }
         public function Modificar_Pago($id,$nuevo_estado,$pago_anti,$pago_nuevo,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_PAGO_ENCOMIENDA(?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$nuevo_estado);
            $query ->bindParam(3,$pago_anti);
            $query ->bindParam(4,$pago_nuevo);
            $query ->bindParam(5,$idusu);

            $resul = $query->execute();
            if($resul){
                return 1;
            }else{
                return 0;
            }
            conexionBD::cerrar_conexion();

        }
        public function Realizar_Pago($id,$nuevo_estado,$saldo_pendiente,$monto_recibido,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REALIZAR_PAGO_ENCOMIENDA(?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$nuevo_estado);
            $query ->bindParam(3,$saldo_pendiente);
            $query ->bindParam(4,$monto_recibido);
            $query ->bindParam(5,$idusu);

            $resul = $query->execute();
            if($resul){
                return 1;
            }else{
                return 0;
            }
            conexionBD::cerrar_conexion();

        }
        public function Listar_Historial_Estado($id){
            $c = conexionBD::conexionPDO();
            $arreglo = array();
            $sql = "CALL SP_LISTA_HISTORIAL_ESTADOS(?)";
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

        public function Eliminar_Encomienda($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_ENCOMIENDA(?)";
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
      public function Buscar_persona_por_documento($numero_documento) {
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_BUSCAR_PERSONA_POR_DOCUMENTO(?)";
            $arreglo = array();

            try {
                $query  = $c->prepare($sql);
                $query->bindParam(1, $numero_documento);
                $query->execute();

                $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($resultado as $resp) {
                    $arreglo["data"][] = $resp;
                }

                return $arreglo;
            } catch (Exception $e) {
                return ["error" => true, "message" => $e->getMessage()];
            } finally {
                // Esto garantiza que la conexión se cierre correctamente
                $c = null;
            }
        }

        

        public function Anular_pago($id,$idusu,$motivo_anulacion,$monto_anulado){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ANULAR_PAGO(?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$idusu);
            $query ->bindParam(3,$motivo_anulacion);
            $query ->bindParam(4,$monto_anulado);

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