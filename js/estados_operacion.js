// ============================================================
// ESTADOS DE ENCOMIENDAS Y SALIDAS DIARIAS
// Ayudas compartidas por los módulos de encomiendas y salidas (todos los roles).
// Las reglas vienen de model/model_estados_encomienda.php: index.php las deja en
// window.REGLAS_ESTADO_ENCOMIENDA. El servidor las vuelve a validar; aquí solo
// sirven para no ofrecer opciones que se van a rechazar.
// ============================================================
var EstadosOperacion = (function () {
  function permitidos(estado, estadoPago) {
    const reglas = window.REGLAS_ESTADO_ENCOMIENDA || {};
    let lista = (reglas[estado] || []).slice();
    if (estadoPago !== "PAGADO") {
      lista = lista.filter((e) => e !== "ENTREGADO");
    }
    return lista;
  }

  // Estados que se pueden elegir al cobrar: mantener el actual o entregar si ya está en agencia
  function permitidosPago(estado) {
    const lista = [estado];
    if (estado === "EN AGENCIA" || estado === "OBSERVADO") lista.push("ENTREGADO");
    return lista;
  }

  // Al ajustar el precio de una OBSERVADA (anular va por "Cambiar estado", con su motivo)
  function permitidosAjuste(estadoPago) {
    return ["OBSERVADO"].concat(permitidos("OBSERVADO", estadoPago).filter((e) => e !== "ANULADO"));
  }

  // Quita "Cambiar estado" si no hay a dónde pasar; lo agrega a las INCOMPLETO antiguas
  function filtrarAcciones(acciones, row, botones) {
    const puedeCambiar = permitidos(row.estado_encomienda, row.estado_pago).length > 0;
    acciones = acciones.filter((a) => a !== botones.cambiar || puedeCambiar);
    if (puedeCambiar && row.estado_encomienda === "INCOMPLETO" && acciones.indexOf(botones.cambiar) === -1) {
      acciones.push(botones.cambiar);
    }
    return acciones;
  }

  // Deja visibles en el <select> solo los valores indicados y selecciona el primero.
  // Dispara "change" para que las vistas muestren los campos de ese estado
  // (observación, motivo de anulación).
  function limitarSelect(select, valores, seleccionado) {
    if (!select) return;
    Array.from(select.options).forEach((opt) => {
      const visible = opt.value === "" || valores.indexOf(opt.value) !== -1;
      opt.hidden = !visible;
      opt.disabled = !visible;
    });
    select.value = seleccionado !== undefined ? seleccionado : (valores[0] || "");
    $(select).trigger("change");
  }

  // Motivo devuelto por el servidor (texto corto); si no hay, el mensaje por defecto
  function mensaje(resp, porDefecto) {
    const texto = String(resp === undefined || resp === null ? "" : resp).trim();
    if (texto === "" || !isNaN(texto) || texto.length > 300 || texto.indexOf("<") !== -1) {
      return porDefecto;
    }
    return texto;
  }

  return {
    permitidos: permitidos,
    permitidosPago: permitidosPago,
    permitidosAjuste: permitidosAjuste,
    filtrarAcciones: filtrarAcciones,
    limitarSelect: limitarSelect,
    mensaje: mensaje,
  };
})();

// ============================================================
// ALERTA DE SALIDAS SIN CERRAR (administrador y asistente)
// Salidas EN TRANSITO con más de 24 h: mientras no se completen, sus
// encomiendas siguen figurando en tránsito.
// ============================================================
(function () {
  const INTERVALO_MS = 5 * 60 * 1000;

  function actualizarAlertaSalidas() {
    const $item = $("#alerta_salidas");
    if ($item.length === 0) return;

    $.ajax({
      url: "../controller/salidas_diarias/controlador_alerta_salidas_abiertas.php",
      type: "POST",
      dataType: "json",
      global: false,
    }).done(function (resp) {
      const atrasadas = parseInt(resp && resp.atrasadas, 10) || 0;
      if (atrasadas === 0) {
        $item.hide();
        return;
      }
      $("#alerta_salidas_badge").html(
        `<i class="fas fa-bus"></i> ${atrasadas} salida${atrasadas === 1 ? "" : "s"} sin cerrar`
      );
      $("#alerta_salidas_link").attr(
        "title",
        "Salidas EN TRANSITO hace más de 24 h. Complétalas o márcalas incompletas para que sus encomiendas pasen a agencia."
      );
      $item.show();
    });
  }

  $(function () {
    actualizarAlertaSalidas();
    setInterval(actualizarAlertaSalidas, INTERVALO_MS);
  });
})();
