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

      
        public function Registrar_Reserva($tipodocemi,$documento,$nomemi,$celemi,$fechare,$fechavia,$ori,$des,$monto,$obser,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_RESERVA(?,?,?,?,?,?,?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$tipodocemi);
            $query ->bindParam(2,$documento);
            $query ->bindParam(3,$nomemi);
            $query ->bindParam(4,$celemi);
            $query ->bindParam(5,$fechare);
            $query ->bindParam(6,$fechavia);
            $query ->bindParam(7,$ori);
            $query ->bindParam(8,$des);
            $query ->bindParam(9,$monto);
            $query ->bindParam(10,$obser);
            $query ->bindParam(11,$idusu);

            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();

        }
        public function Modificar_Reserva($idreserva,$tipodocemi,$documento,$nomemi,$celemi,$fechare,$fechavia,$ori,$des,$monto,$obser,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_RESERVA(?,?,?,?,?,?,?,?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$idreserva);
            $query ->bindParam(2,$tipodocemi);
            $query ->bindParam(3,$documento);
            $query ->bindParam(4,$nomemi);
            $query ->bindParam(5,$celemi);
            $query ->bindParam(6,$fechare);
            $query ->bindParam(7,$fechavia);
            $query ->bindParam(8,$ori);
            $query ->bindParam(9,$des);
            $query ->bindParam(10,$monto);
            $query ->bindParam(11,$obser);
            $query ->bindParam(12,$idusu);
            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();

        }
    

        public function Anular_reserva($id,$motivo){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ANULAR_RESERVA(?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$motivo);

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

        // ASISTENTE:
          public function Listar_reservas_asis($ori){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_RESERVAS_ASIS(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$ori);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Listar_reservas_pordia_asis($ori){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_RESERVAS_PORDIA_ASIS(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$ori);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
         public function Listar_reservas_fecha_estado_asis($ori,$fechaini,$fechafin,$esta){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_RESERVAS_FECHA_ESTADO_ASI(?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$ori);
            $query->bindParam(2,$fechaini);
            $query->bindParam(3,$fechafin);
            $query->bindParam(4,$esta);
            
            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Cargar_Reservas($ori,$des){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_RESERVAS(?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$ori);
            $query->bindParam(2,$des);
            
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