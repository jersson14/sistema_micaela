<?php
    require_once 'model_conexion.php';

    class Modelo_Salidas_Diarias extends conexionBD{
        

        public function Listar_Salidas_Diarias(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_SALIDAS_DIARIAS()";
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
         public function Listar_encomienda_ruta_estado($ori,$des,$esta){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_SALIDAS_RUTA_ESTADO(?,?,?)";
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
        public function Listar_salida_fecha_usuario($fedes,$fehas,$usu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_SALIDAS_FECHA_USUARIO(?,?,?)";
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
        public function Modificar_Salida_Estatus($id,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_SALIDA_ESTATUS(?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$idusu);

            $resul = $query->execute();
            if($resul){
                return 1;
            }else{
                return 0;
            }
            conexionBD::cerrar_conexion();
        }

        public function Modificar_Salida_Estatus_Incompleto($id,$idusu,$observacion){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_SALIDA_INCOMPLETO_ESTATUS(?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$idusu);
            $query ->bindParam(3,$observacion);

            $resul = $query->execute();
            if($resul){
                return 1;
            }else{
                return 0;
            }
            conexionBD::cerrar_conexion();
        }
       

        public function Listar_Encomiendas($id_conductor, $id_origen, $id_destino){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_ENCOMIENDAS_SALIDAS(?,?,?)";
            $arreglo = array();
            $query = $c->prepare($sql);
            $query->bindParam(1, $id_conductor); // Aquí estabas usando $id en lugar de $id_obra
            $query->bindParam(2, $id_origen); // Aquí estabas usando $id en lugar de $id_obra
            $query->bindParam(3, $id_destino); // Aquí estabas usando $id en lugar de $id_obra

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // No necesitas el formato "data" para el uso que le estás dando
            // Simplemente devuelve el array de resultados
            return $resultado;
            
            // Esta línea nunca se ejecuta porque está después del return
            // conexionBD::cerrar_conexion();
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
      
        public function Registrar_Salida_Diaria($conductor,$monto,$fechaHora,$origen,$destino,$observacion,$idUsuario,$totalPasajeros,$totalEncomiendas){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_SALIDA_DIARIA(?,?,?,?,?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$conductor);
            $query ->bindParam(2,$monto);
            $query ->bindParam(3,$fechaHora);
            $query ->bindParam(4,$origen);
            $query ->bindParam(5,$destino); 
            $query ->bindParam(6,$observacion);
            $query ->bindParam(7,$idUsuario);
            $query ->bindParam(8,$totalPasajeros);
            $query ->bindParam(9,$totalEncomiendas);

            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();

        }

         function Registrar_detalle_pasajeros($idSalida, $tipo_documento, $documento, $nombres, $edad, $celular){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_DETALLE_PASAJEROS(?,?,?,?,?,?)";
            $query = $c->prepare($sql);
            $query->bindParam(1, $idSalida, PDO::PARAM_INT);
            $query->bindParam(2, $tipo_documento, PDO::PARAM_STR);
            $query->bindParam(3, $documento, PDO::PARAM_STR);
            $query->bindParam(4, $nombres, PDO::PARAM_STR);
            
            // Manejar edad nula
            if ($edad === null) {
                $query->bindParam(5, $edad, PDO::PARAM_NULL);
            } else {
                $query->bindParam(5, $edad, PDO::PARAM_INT);
            }
            
            $query->bindParam(6, $celular, PDO::PARAM_STR);
            
            $resul = $query->execute();
            conexionBD::cerrar_conexion();
            
            return $resul ? 1 : 0;
        }

            function Registrar_detalle_encomiendas($idSalida, $idEncomienda){
                $c = conexionBD::conexionPDO();
                $sql = "CALL SP_REGISTRAR_DETALLE_ENCOMIENDAS(?,?)";
                $query = $c->prepare($sql);
                $query->bindParam(1, $idSalida, PDO::PARAM_INT);
                $query->bindParam(2, $idEncomienda, PDO::PARAM_INT);
                
                $resul = $query->execute();
                conexionBD::cerrar_conexion();
                
                return $resul ? 1 : 0;
            }
        
        public function Modificar_Salida_Diaria($idSalida,$monto,$fechaactualizar,$observacion,$idUsuario,$totalPasajeros,$totalEncomiendas){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_SALIDA_DIARIA(?,?,?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$idSalida);
            $query ->bindParam(2,$monto);
            $query ->bindParam(3,$fechaactualizar);
            $query ->bindParam(4,$observacion);
            $query ->bindParam(5,$idUsuario);
            $query ->bindParam(6,$totalPasajeros);
            $query ->bindParam(7,$totalEncomiendas);

            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();

        }
         function Modificar_detalle_pasajeros($idSalida, $tipo_documento, $documento, $nombres, $edad, $celular){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_DETALLE_PASAJEROS(?,?,?,?,?,?)";
            $query = $c->prepare($sql);
            $query->bindParam(1, $idSalida, PDO::PARAM_INT);
            $query->bindParam(2, $tipo_documento, PDO::PARAM_STR);
            $query->bindParam(3, $documento, PDO::PARAM_STR);
            $query->bindParam(4, $nombres, PDO::PARAM_STR);
            
            // Manejar edad nula
            if ($edad === null) {
                $query->bindParam(5, $edad, PDO::PARAM_NULL);
            } else {
                $query->bindParam(5, $edad, PDO::PARAM_INT);
            }
            
            $query->bindParam(6, $celular, PDO::PARAM_STR);
            
            $resul = $query->execute();
            conexionBD::cerrar_conexion();
            
            return $resul ? 1 : 0;
        }
            function Modificar_detalle_encomiendas($idSalida, $idEncomienda){
                $c = conexionBD::conexionPDO();
                $sql = "CALL SP_MODIFICAR_DETALLE_ENCOMIENDAS(?,?)";
                $query = $c->prepare($sql);
                $query->bindParam(1, $idSalida, PDO::PARAM_INT);
                $query->bindParam(2, $idEncomienda, PDO::PARAM_INT);
                
                $resul = $query->execute();
                conexionBD::cerrar_conexion();
                
                return $resul ? 1 : 0;
            }
         public function Listar_detalle_salida_pasajeros($id){
            $c = conexionBD::conexionPDO();
            $arreglo = array();
            $sql = "CALL SP_LISTA_DETALLE_SALIDA_PASAJEROS(?)";
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
         public function Listar_detalle_salida_encomiendas($id){
            $c = conexionBD::conexionPDO();
            $arreglo = array();
            $sql = "CALL SP_LISTA_DETALLE_SALIDA_ENCOMIENDAS(?)";
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
        public function Listar_detalle_salida_encomiendasEditar($id){
            $c = conexionBD::conexionPDO();
            $arreglo = array();
            $sql = "CALL SP_LISTA_DETALLE_SALIDA_ENCOMIENDAS_EDITAR(?)";
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
        public function Listar_Historial_Estado_Salida($id){
            $c = conexionBD::conexionPDO();
            $arreglo = array();
            $sql = "CALL SP_LISTA_HISTORIAL_ESTADOS_SALIDA(?)";
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

        public function Eliminar_Salida_diaria($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_SALIDA_DIARIA(?)";
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
         public function Eliminar_Cliente_Salida_diaria($id_pasajero){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_CLIENTE_SALIDA_DIARIA(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id_pasajero);

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