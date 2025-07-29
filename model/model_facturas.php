<?php
    require_once 'model_conexion.php';

    class Modelo_Facturas extends conexionBD{
        

        public function Listar_Facturas(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PRACTICAS_FACTURAS()";
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
      
        public function Registrar_Factura($nrofact, $total, $rutaFactura, $rutaNotacre, $fecha, $idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_FACTURA(?,?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$nrofact);
            $query ->bindParam(2,$total);
            $query ->bindParam(3,$rutaFactura);
            $query ->bindParam(4,$rutaNotacre);
            $query ->bindParam(5,$fecha);
            $query ->bindParam(6,$idusu);

            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();

        }
        public function Modificar_Factura($idfactu, $total, $ruta_factura, $ruta_notacre, $fecha, $idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_FACTURA(?,?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$idfactu);
            $query ->bindParam(2,$total);
            $query ->bindParam(3,$ruta_factura);
            $query ->bindParam(4,$ruta_notacre);
            $query ->bindParam(5,$fecha);
            $query ->bindParam(6,$idusu);

            $resul = $query->execute();
            if($resul){
                return 1;
            }else{
                return 0;
            }
            conexionBD::cerrar_conexion();

        }
        function Registrar_detalle_facturas($id, $array_practicas_paciente, $array_subtotal){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_DETALLE_FACTURA(?,?,?)"; // Se agregaron 3 placeholders
            $query = $c->prepare($sql);
            $query->bindParam(1, $id, PDO::PARAM_INT);
            $query->bindParam(2, $array_practicas_paciente, PDO::PARAM_INT);
            $query->bindParam(3, $array_subtotal, PDO::PARAM_STR); // Asegurar que el subtotal sea string/decimal
            
            $resul = $query->execute();
            conexionBD::cerrar_conexion();
            
            return $resul ? 1 : 0;
        }
        


        public function Eliminar_Factura($id,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_FACTURA(?,?)";
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
        public function Modificar_Estado($id,$esta,$motivo,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_ESTADO(?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$esta);
            $query ->bindParam(3,$motivo);
            $query ->bindParam(4,$idusu);

            $resul = $query->execute();
            if($resul){
                return 1;
            }else{
                return 0;
            }
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
        public function Cargar_Areas(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_AREA()";
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
        public function Cargar_Practicaspaciente_factura($id2){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_PACIENTEYPRACTICA_FACTURA(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id2);
            $query->execute();
            $resultado = $query->fetchAll();
            foreach($resultado as $resp){
                $arreglo[]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
       
        public function Cargar_Traermonto($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_TRAER_PRECIO_PRACTICA_PACIENTE(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query->execute();
            $resultado = $query->fetchAll();
            foreach($resultado as $resp){
                $arreglo[]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Listar_practicas_apci($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_VER_PRACTICAS_PACIENTE(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$id);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }

        public function Listar_detalle_facturas($id){
            $c = conexionBD::conexionPDO();
            $arreglo = array();
            $sql = "CALL SP_LISTA_DETALLE_FACTURAS(?)";
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
        public function Listar_detalle_factura_edit($id){
            $c = conexionBD::conexionPDO();
            $arreglo = array();
            $sql = "CALL SP_LISTA_DETALLE_FACTURAS_EDITAR(?)";
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
        public function Eliminar_detalle_factura_unico($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_DETALLE_FACTURA(?)";
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
        public function Modificar_Detalle_practicas($idpracitcageneral,$idpracitca, $precio) {
            $c = conexionBD::conexionPDO();
        
            // Suponiendo que usas una consulta SQL o un procedimiento almacenado
            $sql = "CALL SP_MODIFICAR_DETALLE_PRACTICAS(?, ?,?)"; // Cambia esto a tu consulta real
            $query = $c->prepare($sql);
            $query->bindParam(1, $idpracitcageneral);
            $query->bindParam(2, $idpracitca);
            $query->bindParam(3, $precio);
        
            try {
                $query->execute();
                // Dependiendo del resultado, puedes devolver 1 para éxito o 0 para error
                // Asegúrate de ajustar esto según tu procedimiento almacenado o lógica SQL
                return $query->rowCount() > 0 ? 1 : 0; // 1 si se modificó algo, 0 si no
            } catch (PDOException $e) {
                error_log($e->getMessage()); // Guarda el error en el log del servidor
                return 0; // Error en la modificación
            } finally {
                conexionBD::cerrar_conexion();
            }
        }
        public function Listar_Historial_facturas($id){
            $c = conexionBD::conexionPDO();
            $arreglo = array();
            $sql = "CALL SP_LISTA_HISTORIAL_FACTURA(?)";
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
        public function Modificar_Detalle_facturas($id_factura,$id_practica, $subtotal,$total,$idusu) {
            $c = conexionBD::conexionPDO();
        
            // Suponiendo que usas una consulta SQL o un procedimiento almacenado
            $sql = "CALL SP_MODIFICAR_DETALLE_FACTURA(?,?,?,?,?)"; // Cambia esto a tu consulta real
            $query = $c->prepare($sql);
            $query->bindParam(1, $id_factura);
            $query->bindParam(2, $id_practica);
            $query->bindParam(3, $subtotal);
            $query->bindParam(4, $total);
            $query->bindParam(5, $idusu);


        
            try {
                $query->execute();
                // Dependiendo del resultado, puedes devolver 1 para éxito o 0 para error
                // Asegúrate de ajustar esto según tu procedimiento almacenado o lógica SQL
                return $query->rowCount() > 0 ? 1 : 0; // 1 si se modificó algo, 0 si no
            } catch (PDOException $e) {
                error_log($e->getMessage()); // Guarda el error en el log del servidor
                return 0; // Error en la modificación
            } finally {
                conexionBD::cerrar_conexion();
            }
        }
        public function Modificar_Factura_Solo_Total_Usuario($total, $idusu,$id_Fac) {
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_FACTURA_SOLA(?,?,?)"; // Cambia esto a tu consulta real
            $query = $c->prepare($sql);
            $query->bindParam(1, $total);
            $query->bindParam(2, $idusu);
            $query->bindParam(3, $id_Fac);

            try {
                $query->execute();
                return $query->rowCount() > 0 ? 1 : 0;
            } catch (PDOException $e) {
                error_log($e->getMessage());
                return 0;
            } finally {
                conexionBD::cerrar_conexion();
            }
        }
        public function Listar_Facturas_archivadas(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_FACTURAS_ARCHIVADAS()";
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
        public function Listar_Facturas_todo_archivado(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_FACTURAS_TODO_ARCHIVADO()";
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
        public function Listar_facturas_edtado_obra_archivado($obra){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_FACTURAS_OBRA_ESTADO_ARCHIVADO(?,?)";
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
        public function Listar_facturas_fecha_usu_archivado($fechaini,$fechafin,$usu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_FACTURAS_FECHAS_USU_ARCHIVADO(?,?,?)";
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

        //PAGAR FACTURA
        public function Realizar_pago($id,$pagar,$saldo,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REALIZAR_PAGO(?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$pagar);
            $query ->bindParam(3,$saldo);
            $query ->bindParam(4,$idusu);

            $resul = $query->execute();
            if($resul){
                return 1;
            }else{
                return 0;
            }
            conexionBD::cerrar_conexion();
        }
        public function Listar_historial_pagoas($id) {
            $c = conexionBD::conexionPDO();
            $arreglo = ["data" => []]; // ✅ Asegura que la clave "data" siempre existe
            $sql = "CALL SP_LISTA_HISTORIAL_PAGOS(?)";
            $query = $c->prepare($sql);
            $query->bindParam(1, $id);
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
        
            if (!empty($resultado)) { // ✅ Solo llena "data" si hay resultados
                $arreglo["data"] = $resultado;
            }
        
            conexionBD::cerrar_conexion();
            return $arreglo;
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

        public function Listar_practocaticas_por_obra($id_obra){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PRACTICAS_OBRA(?)";
            $arreglo = array();
            $query = $c->prepare($sql);
            $query->bindParam(1, $id_obra); // Aquí estabas usando $id en lugar de $id_obra
        
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // No necesitas el formato "data" para el uso que le estás dando
            // Simplemente devuelve el array de resultados
            return $resultado;
            
            // Esta línea nunca se ejecuta porque está después del return
            // conexionBD::cerrar_conexion();
        }
           public function Modificar_Factura_Archivo($idfactu, $ruta_factura, $ruta_notacre, $fecha, $idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_FACTURA_ARCHIVO(?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$idfactu);
            $query ->bindParam(2,$ruta_factura);
            $query ->bindParam(3,$ruta_notacre);
            $query ->bindParam(4,$fecha);
            $query ->bindParam(5,$idusu);

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