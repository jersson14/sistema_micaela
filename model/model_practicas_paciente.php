<?php
    require_once 'model_conexion.php';

    class Modelo_Practicas_Paciente extends conexionBD{
        

        public function Listar_Practicas_paciente(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PRACTICAS_PACIENTES()";
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
        public function Listar_Practicas_paciente_medico($idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PRACTICAS_PACIENTES_MEDICO(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$idusu);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Listar_Practicas_paciente_diario(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PRACTICAS_PACIENTES_DIARIO()";
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
        public function Listar_Practicas_paciente_diario_medico($idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PRACTICAS_PACIENTES_DIARIO_MEDICO(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$idusu);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Listar_practicas_paciente_filtro($obra){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PRACTICAS_PACIENTES_FILTRO(?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$obra);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Listar_practicas_paciente_filtro_medico($obra,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PRACTICAS_PACIENTES_FILTRO_MEDICO(?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$obra);
            $query->bindParam(2,$idusu);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Listar_practicas_paciente_fecha_usu($fechaini,$fechafin,$usu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PRACTICAS_PACIENTES_FECHAS(?,?,?)";
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
        public function Listar_practicas_paciente_fecha_usu_medico($fechaini,$fechafin,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PRACTICAS_PACIENTES_FECHAS_MEDICO(?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$fechaini);
            $query->bindParam(2,$fechafin);
            $query->bindParam(3,$idusu);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Registrar_practica($area,$paciente,$total,$rutaPracticaPaci,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_PRACTICAS_PACIENTE(?,?,?,?,?)";
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$area);
            $query ->bindParam(2,$paciente);
            $query ->bindParam(3,$total);
            $query ->bindParam(4,$rutaPracticaPaci);
            $query ->bindParam(5,$idusu);

            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();

        }
        function Registrar_detalle_practicas($id, $array_practicas,$array_precio,$array_cantidad, $array_subtotal){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_DETALLE_PRACTICAS(?,?,?,?,?)"; // Se agregaron 3 placeholders
            $query = $c->prepare($sql);
            $query->bindParam(1, $id, PDO::PARAM_INT);
            $query->bindParam(2, $array_practicas, PDO::PARAM_INT);
            $query->bindParam(3, $array_precio, PDO::PARAM_STR); // Asegurar que el subtotal sea string/decimal
            $query->bindParam(4, $array_cantidad, PDO::PARAM_STR); // Asegurar que el subtotal sea string/decimal
            $query->bindParam(5, $array_subtotal, PDO::PARAM_STR); // Asegurar que el subtotal sea string/decimal
            
            $resul = $query->execute();
            conexionBD::cerrar_conexion();
            
            return $resul ? 1 : 0;
        }
        


        public function Modificar_Paciente($id,$dni,$nom,$epell,$direc,$local,$tele,$obra,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_PACIENTE(?,?,?,?,?,?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$id);
            $query ->bindParam(2,$dni);
            $query ->bindParam(3,$nom);
            $query ->bindParam(4,$epell);
            $query ->bindParam(5,$direc);
            $query ->bindParam(6,$local);
            $query ->bindParam(7,$tele);
            $query ->bindParam(8,$obra);
            $query ->bindParam(9,$idusu);
            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
        }
      
        public function Eliminar_Practicas_paciente($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_PRACTICAS_PACIENTE(?)";
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
        public function Cargar_Practicaspaciente($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_PACIENTEYPRACTICA(?)";
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
        public function Cargar_Practicaspaciente2($id2) {
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_PACIENTEYPRACTICA2(?)";
            $arreglo = array();
            $query = $c->prepare($sql);
            $query->bindParam(1, $id2); // Cambiado de $id a $id2
            $query->execute();
            $resultado = $query->fetchAll();
            
            foreach($resultado as $resp) {
                $arreglo[] = $resp;
            }
            
            conexionBD::cerrar_conexion();
            return $arreglo;
        }
        public function Cargar_Traermonto($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_CARGAR_TRAER_PRECIO(?)";
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

        public function Listar_detalle_practicas($id){
            $c = conexionBD::conexionPDO();
            $arreglo = array();
            $sql = "CALL SP_LISTA_DETALLE_PRACTICAS(?)";
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
        public function Eliminar_detalle_practica_unico($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_DETALLE_PRACTICA(?)";
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
        public function Modificar_Detalle_practicas($idpracitcageneral,$idpracitca, $precio,$cantidad,$subtotal,$total,$idusu) {
            $c = conexionBD::conexionPDO();
        
            // Suponiendo que usas una consulta SQL o un procedimiento almacenado
            $sql = "CALL SP_MODIFICAR_DETALLE_PRACTICAS(?,?,?,?,?,?,?)"; // Cambia esto a tu consulta real
            $query = $c->prepare($sql);
            $query->bindParam(1, $idpracitcageneral);
            $query->bindParam(2, $idpracitca);
            $query->bindParam(3, $precio);
            $query->bindParam(4, $cantidad);
            $query->bindParam(5, $subtotal);
            $query->bindParam(6, $total);
            $query->bindParam(7, $idusu);

        
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

        public function Modificar_Total_Usuario($total, $idusu,$idprac) {
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_PRACTICA_SOLA(?,?,?)"; // Cambia esto a tu consulta real
            $query = $c->prepare($sql);
            $query->bindParam(1, $total);
            $query->bindParam(2, $idusu);
            $query->bindParam(3, $idprac);

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
        public function Modificar_archivo_HC($id,$ruta){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_MODIFICAR_HC(?,?)";
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