<?php
    /**
     * Reglas de estado de las encomiendas.
     *
     * EN TRANSITO lo pone la salida diaria al subir la encomienda. EN AGENCIA lo
     * pone la salida al completarse, o recepción al recibirla antes de que se
     * cierre la salida; nunca sin haber viajado.
     * Una encomienda POR PAGAR se entrega con "Pagar", que cobra y entrega a la vez.
     * index.php pasa MANUALES al JS (window.REGLAS_ESTADO_ENCOMIENDA).
     */
    class Reglas_Estado_Encomienda {

        // Cambio manual ("Cambiar estado"): estado actual => estados a los que puede pasar
        const MANUALES = [
            'PENDIENTE'   => ['OBSERVADO', 'ANULADO'],
            'EN TRANSITO' => ['EN AGENCIA'],
            'EN AGENCIA'  => ['ENTREGADO', 'OBSERVADO'],
            'OBSERVADO'   => ['PENDIENTE', 'EN AGENCIA', 'ENTREGADO', 'ANULADO'],
            // Registros antiguos: antes una salida incompleta dejaba las encomiendas así
            'INCOMPLETO'  => ['PENDIENTE', 'OBSERVADO', 'ANULADO'],
            'ENTREGADO'   => [],
            'ANULADO'     => [],
        ];

        /** Estados a los que se puede pasar a mano, según el estado y el pago actuales. */
        public static function Permitidos_Manual($estado, $estado_pago) {
            $permitidos = self::MANUALES[$estado] ?? [];
            if ($estado_pago !== 'PAGADO') {
                $permitidos = array_values(array_diff($permitidos, ['ENTREGADO']));
            }
            return $permitidos;
        }

        /** Devuelve el motivo si el cambio manual no está permitido, o null si sí. */
        public static function Validar_Cambio($actual, $nuevo, $motivo_anula, $observacion = '') {
            $estado = $actual['estado_encomienda'];
            if ($nuevo === $estado) {
                return "La encomienda ya está en estado $estado";
            }
            if ($nuevo === 'EN TRANSITO') {
                return 'EN TRANSITO lo asigna la salida diaria al subir la encomienda';
            }
            if ($nuevo === 'EN AGENCIA' && $estado === 'PENDIENTE') {
                return 'La encomienda aún no viajó: súbala a una salida diaria';
            }
            if ($nuevo === 'ENTREGADO' && $actual['estado_pago'] !== 'PAGADO') {
                return 'La encomienda está POR PAGAR: entréguela con la opción Pagar';
            }
            if (!in_array($nuevo, self::Permitidos_Manual($estado, $actual['estado_pago']), true)) {
                return "No se puede pasar de $estado a $nuevo";
            }
            if ($nuevo === 'ANULADO' && trim($motivo_anula) === '') {
                return 'El motivo de anulación es obligatorio';
            }
            if ($nuevo === 'OBSERVADO' && trim($observacion) === '') {
                return 'Indique el motivo de la observación';
            }
            return null;
        }

        /** Cobro del saldo: se mantiene el estado o se entrega en el mismo paso. */
        public static function Validar_Pago($actual, $nuevo, $monto_recibido) {
            $estado = $actual['estado_encomienda'];
            if ($actual['estado_pago'] !== 'POR PAGAR' || (float)$actual['por_pagar'] <= 0) {
                return 'La encomienda no tiene saldo pendiente';
            }
            if (in_array($estado, ['ENTREGADO', 'ANULADO'], true)) {
                return "No se puede cobrar una encomienda $estado";
            }
            $entrega = ($nuevo === 'ENTREGADO' && in_array($estado, ['EN AGENCIA', 'OBSERVADO'], true));
            if ($nuevo !== $estado && !$entrega) {
                return "Al cobrar solo se puede mantener $estado o entregar la encomienda (si ya está en agencia)";
            }
            if (!is_numeric($monto_recibido) || (float)$monto_recibido < (float)$actual['por_pagar']) {
                return 'El monto recibido es menor al saldo pendiente';
            }
            return null;
        }

        /** Ajuste de precio: solo encomiendas OBSERVADAS; no anula (eso va con su motivo). */
        public static function Validar_Ajuste($actual, $nuevo, $pago_nuevo) {
            if ($actual['estado_encomienda'] !== 'OBSERVADO') {
                return 'Solo se ajusta el precio de encomiendas OBSERVADAS';
            }
            if (!is_numeric($pago_nuevo) || (float)$pago_nuevo < 0) {
                return 'El monto nuevo no es válido';
            }
            if ($nuevo !== 'OBSERVADO') {
                $permitidos = array_diff(self::Permitidos_Manual('OBSERVADO', $actual['estado_pago']), ['ANULADO']);
                if (!in_array($nuevo, $permitidos, true)) {
                    return "No se puede pasar de OBSERVADO a $nuevo al ajustar el precio";
                }
            }
            return null;
        }

        /** Montos de registro/edición: números no negativos y al menos uno mayor a 0. */
        public static function Validar_Montos($pago, $por_pagar, $a_domicilio) {
            foreach ([$pago, $por_pagar, $a_domicilio] as $monto) {
                if (!is_numeric($monto) || (float)$monto < 0) {
                    return 'Los montos deben ser números mayores o iguales a 0';
                }
            }
            if ((float)$pago + (float)$por_pagar + (float)$a_domicilio <= 0) {
                return 'Pago, por pagar o a domicilio debe ser mayor a 0';
            }
            return null;
        }

        /**
         * Edición: la ruta y el conductor solo cambian mientras está PENDIENTE
         * (después ya está en una salida o en destino); entregada no cambia montos.
         */
        public static function Validar_Edicion($actual, $conductor, $origen, $destino, $pago, $por_pagar, $a_domicilio) {
            $estado = $actual['estado_encomienda'];
            if ($estado === 'ANULADO') {
                return 'No se puede editar una encomienda anulada';
            }
            $cambiaRuta = (int)$conductor !== (int)$actual['id_conductor']
                || (int)$origen !== (int)$actual['id_origen']
                || (int)$destino !== (int)$actual['id_destino'];
            if ($cambiaRuta && $estado !== 'PENDIENTE') {
                return "La encomienda está $estado: ya no se puede cambiar el conductor, el origen ni el destino";
            }
            $cambiaMontos = abs((float)$pago - (float)$actual['pago']) > 0.009
                || abs((float)$por_pagar - (float)$actual['por_pagar']) > 0.009
                || abs((float)$a_domicilio - (float)$actual['a_domicilio']) > 0.009;
            if ($cambiaMontos && $estado === 'ENTREGADO') {
                return 'La encomienda ya fue entregada: no se pueden cambiar los montos';
            }
            return null;
        }

        /** Eliminar solo antes de que la encomienda viaje. */
        public static function Validar_Eliminar($actual) {
            if ($actual['estado_encomienda'] !== 'PENDIENTE') {
                return 'Solo se pueden eliminar encomiendas PENDIENTES; en otro estado use Anular';
            }
            return null;
        }

        /** Mensaje legible de un error de la BD (los SIGNAL de los procedimientos). */
        public static function Mensaje_Error(PDOException $e) {
            if (isset($e->errorInfo[0]) && $e->errorInfo[0] === '45000' && !empty($e->errorInfo[2])) {
                return $e->errorInfo[2];
            }
            error_log('Encomiendas/salidas: ' . $e->getMessage());
            return 'No se pudo completar la operación';
        }
    }
?>
