<?php
    require_once 'model_conexion.php';

    class Modelo_Paciente extends conexionBD{
        

        public function Listar_Pacientes(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PACIENTES()";
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
        public function Listar_paciente_filtro($obra,$fechaini,$fechafin){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_PACIENTES_FILTRO(?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query->bindParam(1,$obra);
            $query->bindParam(2,$fechaini);
            $query->bindParam(3,$fechafin);

            $query->execute();
            $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $resp){
                $arreglo["data"][]=$resp;
            }
            return $arreglo;
            conexionBD::cerrar_conexion();
        }
        public function Registrar_Paciente($dni,$nom,$epell,$direc,$local,$tele,$obra,$idusu){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_REGISTRAR_PACIENTE(?,?,?,?,?,?,?,?)";
            $arreglo = array();
            $query  = $c->prepare($sql);
            $query ->bindParam(1,$dni);
            $query ->bindParam(2,$nom);
            $query ->bindParam(3,$epell);
            $query ->bindParam(4,$direc);
            $query ->bindParam(5,$local);
            $query ->bindParam(6,$tele);
            $query ->bindParam(7,$obra);
            $query ->bindParam(8,$idusu);
            $query->execute();
            if($row = $query->fetchColumn()){
                return $row;
            }
            conexionBD::cerrar_conexion();
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
        public function listar_total_Empleados(){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_LISTAR_TOTAL_EMPLEADO()";
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
        public function Eliminar_Pacientes($id){
            $c = conexionBD::conexionPDO();
            $sql = "CALL SP_ELIMINAR_PACIENTE(?)";
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
      
    }




?>