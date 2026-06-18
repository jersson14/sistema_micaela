<?php
    require_once 'model_conexion.php';

    class Modelo_Encomiendas extends conexionBD{
        

        public function Listar_Encomiendas(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_ENCOMIENDAS()";
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
          public function Listar_Encomiendas_pordia(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_ENCOMIENDAS_PORDIA()";
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
            $sql = "CALL SP_LISTAR_ENCOMIENDAS_RUTA_ESTADO(?,?,?)";
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
        public function Listar_encomienda_fecha_usuario($fedes,$fehas,$usu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_ENCOMIENDAS_FECHA_USUARIO(?,?,?)";
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

        public function Buscar_persona_por_documento_compro($numero_documento) {
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_BUSCAR_PERSONA_POR_DOCUMENTO_COMPRO(?)";
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
                $c = null; // Cerrar conexión
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
        // ASISTENTE:
         public function Listar_todas_encomienda_asis($des){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_ENCOMIENDAS_TODOS_ASIS(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$des);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
         public function Listar_todas_encomienda_por_dia_asis($des){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_ENCOMIENDAS_POR_DIA_ASIS(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$des);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Listar_todas_encomienda_por_fechas_estado($des,$fedes,$fehas,$esta){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_ENCOMIENDAS_POR_FECHA_ESTADO(?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$des);
            $query->bindParam(2,$fedes);
            $query->bindParam(3,$fehas);
            $query->bindParam(4,$esta);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        // ASISTENTE ENVIO:
         public function Listar_todas_encomienda_env($usu,$des){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_ENCOMIENDAS_TODOS_ENVI(?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$usu);
            $query->bindParam(2,$des);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
            public function Listar_todas_encomienda_por_dia_env($usu,$des){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_ENCOMIENDAS_POR_DIA_ENV(?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$usu);
            $query->bindParam(2,$des);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
         public function Listar_todas_encomienda_por_fechas_estado_env($usu,$des,$fedes,$fehas,$esta){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_ENCOMIENDAS_POR_FECHA_ESTADO_ENV(?,?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$usu);
            $query->bindParam(2,$des);
            $query->bindParam(3,$fedes);
            $query->bindParam(4,$fehas);
            $query->bindParam(5,$esta);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }

        // SEGUIMIENTO PÚBLICO
        public function Buscar_Encomienda_Por_Boleta($boleta_nro) {
            $c = conexionBD::conexionPDO();
            $arreglo = array();

            try {
                // Buscar información de la encomienda
                $sql = "SELECT 
                    e.id_encomienda,
                    e.boleta_nro,
                    e.fecha_hora,
                    e.descripcion,
                    e.pago,
                    e.por_pagar,
                    e.a_domicilio,
                    e.estado_encomienda,
                    e.estado_pago,
                    e.observacion,
                    e.created_at,
                    ce.nombre_completo as emisor_nombre,
                    ce.celular as emisor_celular,
                    cr.nombre_completo as receptor_nombre,
                    cr.celular as receptor_celular,
                    so.sucrusal as origen,
                    sd.sucrusal as destino,
                    ch.nombres_apellidos as conductor
                FROM encomiendas e
                LEFT JOIN clientes ce ON e.id_cliente_emisor = ce.id_cliente
                LEFT JOIN clientes cr ON e.id_cliente_receptor = cr.id_cliente
                LEFT JOIN sucursales so ON e.id_origen = so.id_sucursal
                LEFT JOIN sucursales sd ON e.id_destino = sd.id_sucursal
                LEFT JOIN choferes ch ON e.id_conductor = ch.id_chofer
                WHERE e.boleta_nro = ?";
                
                $query = $c->prepare($sql);
                $query->bindParam(1, $boleta_nro);
                $query->execute();
                
                $encomienda = $query->fetch(PDO::FETCH_ASSOC);
                
                if($encomienda) {
                    $arreglo['encomienda'] = $encomienda;
                    
                    // Buscar historial de estados
                    $sql_historial = "SELECT 
                        id_historial_estado,
                        estado,
                        observacion,
                        created_at
                    FROM historial_estados
                    WHERE id_encomienda = ?
                    ORDER BY created_at DESC";
                    
                    $query_historial = $c->prepare($sql_historial);
                    $query_historial->bindParam(1, $encomienda['id_encomienda']);
                    $query_historial->execute();
                    
                    $historial = $query_historial->fetchAll(PDO::FETCH_ASSOC);
                    $arreglo['historial'] = $historial;
                    
                    return $arreglo;
                } else {
                    return null;
                }
                
            } catch (Exception $e) {
                return ["error" => true, "message" => $e->getMessage()];
            } finally {
                $c = null;
            }
        }
    }




?>