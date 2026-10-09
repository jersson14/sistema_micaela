// ============================================================
// ALERTA DE COLA SUNAT (solo ADMINISTRADOR)
// Muestra en la barra superior cuántos comprobantes esperan envío a SUNAT.
//  - Amarillo: en cola, la cola (CRON) los envía sola
//  - Rojo: más de 24 h sin aceptarse o fuera del plazo de 3 días
// ============================================================
(function () {
  const INTERVALO_MS = 3 * 60 * 1000;

  function actualizarAlertaSunat() {
    $.ajax({
      url: "../controller/comprobante/controller_comprobante.php",
      type: "POST",
      data: { accion: "OBTENER_ALERTA_SUNAT" },
      dataType: "json",
    }).done(function (resp) {
      if (!resp || resp.status !== "success") return;

      const enCola = parseInt(resp.en_cola, 10) || 0;
      const atrasados = parseInt(resp.atrasados, 10) || 0;
      const fueraPlazo = parseInt(resp.fuera_plazo, 10) || 0;
      const $item = $("#alerta_sunat");
      const $badge = $("#alerta_sunat_badge");

      if (enCola === 0 && fueraPlazo === 0) {
        $item.hide();
        return;
      }

      const partes = [];
      if (atrasados > 0) partes.push(`${atrasados} con más de 24 h`);
      if (fueraPlazo > 0) partes.push(`${fueraPlazo} fuera de plazo`);

      if (partes.length > 0) {
        $badge
          .removeClass("badge-warning")
          .addClass("badge-danger")
          .html(`<i class="fas fa-exclamation-triangle"></i> SUNAT: ${partes.join(" · ")}`);
        $("#alerta_sunat_link").attr(
          "title",
          "Requiere revisión. En cola: " + enCola +
            " | Más de 24 h: " + atrasados +
            " | Fuera de plazo (la cola no los envía): " + fueraPlazo
        );
      } else {
        $badge
          .removeClass("badge-danger")
          .addClass("badge-warning")
          .html(`<i class="fas fa-clock"></i> SUNAT: ${enCola} en cola`);
        $("#alerta_sunat_link").attr(
          "title",
          "Comprobantes esperando envío a SUNAT. Se envían automáticamente."
        );
      }
      $item.show();
    });
  }

  $(function () {
    actualizarAlertaSunat();
    setInterval(actualizarAlertaSunat, INTERVALO_MS);
  });
})();
