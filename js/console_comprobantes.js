function Cargar_Select_Rutas() {
  $.ajax({
    url: "../controller/rutas/controlador_cargar_select_rutas.php",
    type: "POST",
  }).done(function (resp) {
    let data = JSON.parse(resp);
    let cadena =
      "<option value='' disabled selected>Seleccionar destino u origen</option>";

    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) {
        cadena +=
          "<option value='" + data[i][0] + "'>" + data[i][1] + "</option>";
      }
    } else {
      cadena += "<option value=''>No hay rutas disponibles</option>";
    }

    $("#select_origen").html(cadena);
    $("#select_destino").html(cadena);
  });
}
function Cargar_Select_Conductores() {
  $.ajax({
    url: "../controller/choferes/controlador_cargar_select_choferes.php",
    type: "POST",
  }).done(function (resp) {
    let data = JSON.parse(resp);
    let cadena = "<option value=''>Seleccionar conductor</option>";

    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) {
        cadena +=
          "<option value='" +
          data[i][0] +
          "'>DNI: " +
          data[i][1] +
          " - CONDUCTOR: " +
          data[i][2] +
          "</option>";
      }
    } else {
      cadena += "<option value=''>No hay conductores disponibles</option>";
    }

    $("#select_conductor").html(cadena);
  });
}

function Cargar_Select_Tipopago() {
  $.ajax({
    url: "../controller/tipo_pago/controlador_cargar_select_tipo_pago.php",
    type: "POST",
  })
    .done(function (resp) {
      let data = JSON.parse(resp);
      let cadena = "";

      if (data.length > 0) {
        // Llenar las opciones
        cadena = "<option value=''>Seleccionar tipo pago</option>";
        for (let i = 0; i < data.length; i++) {
          cadena += `<option value="${data[i][0]}">${data[i][1]}</option>`;
        }

        // Insertar opciones en el select
        $("#select_tipo_pago").html(cadena);

        // ✅ Seleccionar automáticamente el primer tipo de pago
        $("#select_tipo_pago").val(data[0][0]);
      } else {
        // Si no hay datos
        $("#select_tipo_pago").html(
          "<option value=''>No hay tipo de pago disponibles</option>"
        );
      }
    })
    .fail(function (xhr, status, error) {
      console.error("Error al cargar tipos de pago:", error);
    });
}

// 1️⃣ Cargar servicios al iniciar
function Cargar_Select_Servicios() {
  $.ajax({
    url: "../controller/servicios/controlador_cargar_select_servicios.php",
    type: "POST",
  }).done(function (resp) {
    let data = JSON.parse(resp);
    let cadena = "<option value=''>Seleccionar servicio</option>";

    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) {
        cadena +=
          "<option value='" + data[i][0] + "'>" + data[i][1] + "</option>";
      }
    } else {
      cadena += "<option value=''>No hay servicios disponibles</option>";
    }

    $("#select_servicio").html(cadena);
  });
}
$("#select_servicio").on("change", function () {
  let id_servicio = $(this).val();

  // Objeto de reglas según id_servicio
  const reglas = {
    1: { origen: "1", destino: "2" },
    2: { origen: "2", destino: "1" },
    3: { origen: "1", destino: "2" },
    4: { origen: "2", destino: "1" },
  };

  if (reglas[id_servicio]) {
    $("#select_origen").val(reglas[id_servicio].origen).trigger("change");
    $("#select_destino").val(reglas[id_servicio].destino).trigger("change");
  }
});

// 2️⃣ Detectar cambio en el select de servicios
$(document).on("change", "#select_servicio", function () {
  let id = $(this).val();
  if (id !== "") {
    Traerprecio(id);
  } else {
    $("#txt_base_gravada").val("");
    $("#txt_igv").val("");
    $("#txt_total").val("");
  }
});
// 🎯 Variable global para guardar el precio UNITARIO original
let precioUnitarioOriginal = 0;
let editandoManualmente = false; // Para detectar si el usuario está editando

// 3️⃣ Traer precio desde el backend y calcular totales
function Traerprecio(id) {
  $.ajax({
    url: "../controller/servicios/controlador_traermonto.php",
    type: "POST",
    data: { id: id },
  })
    .done(function (resp) {
      try {
        var data = JSON.parse(resp);
        if (data.length > 0) {
          // 🔥 GUARDAR el precio UNITARIO original (incluye IGV)
          precioUnitarioOriginal = parseFloat(data[0].monto || data[0][1]);

          // Establecer cantidad inicial en 1 si está vacío
          let cantidadActual = parseFloat($("#txt_cantidad").val()) || 1;
          $("#txt_cantidad").val(cantidadActual);

          // Calcular totales con la cantidad actual
          calcularDesdePrecioUnitario();
        } else {
          precioUnitarioOriginal = 0;
          limpiarCampos();
        }
      } catch (error) {
        console.error("Error al parsear JSON:", resp);
        precioUnitarioOriginal = 0;
        limpiarCampos();
      }
    })
    .fail(function () {
      console.error("Error al traer el precio del servicio.");
      precioUnitarioOriginal = 0;
      limpiarCampos();
    });
}

// 🔄 Función para calcular desde el precio UNITARIO ORIGINAL (cuando cambia cantidad)
function calcularDesdePrecioUnitario() {
  if (editandoManualmente) return; // No recalcular si está editando manualmente

  var precioUnitarioConIGV = precioUnitarioOriginal;
  var cantidad = parseFloat(document.getElementById("txt_cantidad").value) || 0;

  // Validar cantidad mínima
  if (cantidad === 0) {
    cantidad = 1;
    document.getElementById("txt_cantidad").value = 1;
  }

  // ✅ PASO 1: Calcular base gravada UNITARIA (sin IGV)
  var baseGravadaUnitaria = precioUnitarioConIGV / 1.18;

  // ✅ PASO 2: Multiplicar por la cantidad
  var baseGravadaTotal = baseGravadaUnitaria * cantidad;

  // ✅ PASO 3: Calcular IGV (18% de la base gravada total)
  var igvTotal = baseGravadaTotal * 0.18;

  // ✅ PASO 4: Calcular total general
  var totalGeneral = precioUnitarioConIGV * cantidad;

  // Actualizar campos con 2 decimales
  document.getElementById("txt_base_gravada").value =
    baseGravadaTotal.toFixed(2);
  document.getElementById("txt_igv").value = igvTotal.toFixed(2);
  document.getElementById("txt_total").value = totalGeneral.toFixed(2);
}

// 🆕 Función para calcular desde el TOTAL editado manualmente
function calcularDesdeTotal() {
  var totalEditado =
    parseFloat(document.getElementById("txt_total").value) || 0;

  if (totalEditado === 0) {
    limpiarCampos();
    return;
  }

  // ✅ PASO 1: Calcular base gravada desde el total
  // Total = Base Gravada × 1.18
  // Base Gravada = Total / 1.18
  var baseGravadaTotal = totalEditado / 1.18;

  // ✅ PASO 2: Calcular IGV (18% de la base gravada)
  var igvTotal = baseGravadaTotal * 0.18;

  // Actualizar campos con 2 decimales
  document.getElementById("txt_base_gravada").value =
    baseGravadaTotal.toFixed(2);
  document.getElementById("txt_igv").value = igvTotal.toFixed(2);
}

// 🧹 Función auxiliar para limpiar campos
function limpiarCampos() {
  $("#txt_base_gravada").val("");
  $("#txt_igv").val("");
  $("#txt_total").val("");
}

// ============================================================
// EVENTOS - Conectar con los campos del formulario
// ============================================================
document.addEventListener("DOMContentLoaded", function () {
  // 📊 Cuando cambia la CANTIDAD (recalcula desde precio unitario)
  var inputCantidad = document.getElementById("txt_cantidad");
  if (inputCantidad) {
    inputCantidad.addEventListener("input", function () {
      editandoManualmente = false;
      calcularDesdePrecioUnitario();
    });
    inputCantidad.addEventListener("blur", function () {
      editandoManualmente = false;
      calcularDesdePrecioUnitario();
    });
    inputCantidad.addEventListener("change", function () {
      editandoManualmente = false;
      calcularDesdePrecioUnitario();
    });
  }

  // 💰 Cuando se EDITA MANUALMENTE el TOTAL (recalcula base e IGV)
  var inputTotal = document.getElementById("txt_total");
  if (inputTotal) {
    // Detectar cuando empieza a escribir
    inputTotal.addEventListener("focus", function () {
      editandoManualmente = true;
    });

    // Recalcular mientras escribe
    inputTotal.addEventListener("input", function () {
      editandoManualmente = true;
      calcularDesdeTotal();
    });

    // Recalcular cuando termina de editar
    inputTotal.addEventListener("blur", function () {
      calcularDesdeTotal();
      setTimeout(() => {
        editandoManualmente = false;
      }, 100);
    });
  }
});

// 🔄 Mantener compatibilidad con el código HTML que llama calcularTotalesServicio()
function calcularTotalesServicio() {
  calcularDesdePrecioUnitario();
}
// LIMPIAR TOTALES
function limpiarTotales() {
  $("#txt_base_gravada").val("");
  $("#txt_igv").val("");
  $("#txt_total").val("");
  $("#span_subtotal_serv").text("S/ 0.00");
}

// OBTENER CORRELATIVO AUTOMÁTICO
var intervalo_correlativo = null;
var suprimirAvisoCorrelativo = false;

function obtenerCorrelativo() {
    // Si edición manual está activa, no sobreescribir lo que el usuario escribió
    var checkEditar = document.getElementById('check_editar_serie');
    if (checkEditar && checkEditar.checked) return;

    var tipo = document.getElementById('select_tipo_comprobante').value;
    var serie = document.getElementById('txt_serie').value;
    if (!tipo || !serie) return;

    $.ajax({
        url: "../controller/comprobante/controller_comprobante.php",
        type: "POST",
        data: { accion: "OBTENER_CORRELATIVO", serie: serie, tipo_comprobante: tipo },
        dataType: "json"
    }).done(function(data) {
        if (data && data.correlativo) {
            var anterior = document.getElementById('txt_correlativo').value;
            document.getElementById('txt_correlativo').value = data.correlativo;

            if (!suprimirAvisoCorrelativo && anterior !== '' && anterior !== data.correlativo) {
                Swal.fire({
                    icon: 'warning',
                    title: '⚠️ Correlativo actualizado',
                    html: `Otro usuario generó un comprobante.<br>
                           Correlativo actualizado a <b>${data.correlativo}</b>`,
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
            suprimirAvisoCorrelativo = false;
        }
    });
}
function cambiarTipoComprobante() {
    var tipo = document.getElementById('select_tipo_comprobante').value;
    var serie = document.getElementById('txt_serie');

    if (tipo == '01') {
        serie.value = 'FPP1';
        document.getElementById('select_tipo_documento_cliente').value = '6';
    } else if (tipo == '03') {
        serie.value = 'BPP1';
    }

    obtenerCorrelativo();

    // Refrescar cada 20 segundos por si otro usuario genera uno
    if (intervalo_correlativo) clearInterval(intervalo_correlativo);
    intervalo_correlativo = setInterval(obtenerCorrelativo, 20000);
}
// BUSCAR CLIENTE POR RUC (API SUNAT)
// BUSCAR CLIENTE POR RUC (API SUNAT)
function buscarRUC(ruc) {
  if (ruc.length != 11) {
    return Swal.fire("Advertencia", "El RUC debe tener 11 dígitos", "warning");
  }

  Swal.fire({
    title: "Buscando RUC...",
    text: "Consultando en SUNAT",
    allowOutsideClick: false,
    showConfirmButton: false,
    willOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "../view/consultar-ruc-ajax.php",
    type: "POST",
    data: { ruc: ruc },
    dataType: "json",
  })
    .done(function (data) {
      Swal.close();

      if (data.error) {
        Swal.fire("Error", data.error, "error");
      } else if (data.razon_social) {
        // 👇 Usar las claves reales que devuelve la API
        $("#txt_razon_social").val(data.razon_social);
        $("#txt_direccion").val(data.direccion || "");
        $("#txt_departamento").val(data.departamento || "");
        $("#txt_provincia").val(data.provincia || "");
        $("#txt_distrito").val(data.distrito || "");
        Swal.fire("Éxito", "RUC encontrado", "success");
      } else {
        Swal.fire("Advertencia", "RUC no encontrado", "warning");
      }
    })
    .fail(function () {
      Swal.close();
      Swal.fire("Error", "Error al consultar RUC", "error");
    });
}

// BUSCAR CLIENTE POR DNI (API RENIEC)
function buscarDNI(dni) {
  if (dni.length != 8) {
    return Swal.fire("Advertencia", "El DNI debe tener 8 dígitos", "warning");
  }

  Swal.fire({
    title: "Buscando DNI...",
    text: "Consultando en RENIEC",
    allowOutsideClick: false,
    showConfirmButton: false,
    willOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "../view/consulta-dni-ajax.php",
    type: "POST",
    data: { dni: dni },
    dataType: "json",
  })
    .done(function (data) {
      Swal.close();

      if (data == 1) {
        Swal.fire("Advertencia", "El DNI debe tener 8 dígitos", "warning");
      } else if (data.error) {
        Swal.fire("Error", data.error, "error");
      } else if (data.first_name) {
        let nombreCompleto =
          data.first_name +
          " " +
          data.first_last_name +
          " " +
          data.second_last_name;
        $("#txt_razon_social").val(nombreCompleto);
        Swal.fire("Éxito", "DNI encontrado", "success");
      } else {
        Swal.fire("Advertencia", "DNI no encontrado", "warning");
      }
    })
    .fail(function () {
      Swal.close();
      Swal.fire("Error", "Error al consultar DNI", "error");
    });
}

// BUSCAR CLIENTE (SELECTOR GENERAL)
function buscarCliente() {
  let tipoDoc = $("#select_tipo_documento_cliente").val();
  let numDoc = $("#txt_numero_documento").val();

  if (!tipoDoc || !numDoc) {
    return Swal.fire(
      "Advertencia",
      "Seleccione tipo y número de documento",
      "warning"
    );
  }

  if (tipoDoc == "6") {
    buscarRUC(numDoc);
  } else if (tipoDoc == "1") {
    buscarDNI(numDoc);
  } else {
    Swal.fire("Info", "Ingrese manualmente los datos", "info");
  }
}

// ======================================================
// GUARDAR COMPROBANTE - CORREGIDO Y COMPLETO
// ======================================================
function guardarComprobante(estadoSunat) {
  // 1️⃣ CAPTURA DE DATOS DEL FORMULARIO
  let tipo_comprobante = $("#select_tipo_comprobante").val();
  let serie = $("#txt_serie").val();
  let correlativo = $("#txt_correlativo").val();
  let fecha_emision = $("#txt_fecha_emision").val() || "";
  let moneda = $("#select_moneda").val();
  let tipo_documento_cliente = $("#select_tipo_documento_cliente").val();
  let numero_documento = $("#txt_numero_documento").val();
  let razon_social = $("#txt_razon_social").val();
  let direccion = $("#txt_direccion").val();

  // 📱 CAPTURA DEL TELÉFONO - TRIPLE VERIFICACIÓN
  let celular = $("#txt_telefono").val() || "";
  if (celular === undefined || celular === null) {
    celular = document.getElementById("txt_telefono")?.value || "";
  }
  celular = String(celular).trim();

  let departamento = $("#txt_departamento").val();
  let provincia = $("#txt_provincia").val();
  let distrito = $("#txt_distrito").val() || "ABANCAY";
  let id_servicio = $("#select_servicio").val();
  let cantidad = $("#txt_cantidad").val() || 1;
  let id_conductor = $("#select_conductor").val();
  let id_origen = $("#select_origen").val();
  let id_destino = $("#select_destino").val();
  let fecha_viaje = $("#txt_fecha_viaje").val();
  let base_gravada = $("#txt_base_gravada").val();
  let igv = $("#txt_igv").val();
  let total = $("#txt_total").val();
  let forma_pago = $("#select_forma_pago").val();
  let id_tipo_pago = $("#select_tipo_pago").val();
  let observaciones = $("#txt_observaciones").val() || "";
  let id_usuario = $("#txtprincipalid").val();

  // 🔍 DEBUG CRÍTICO
  console.log("═══════════════════════════════════════");
  console.log("🔍 DEBUG TELÉFONO:");
  console.log("Campo existe:", $("#txt_telefono").length);
  console.log("Valor jQuery:", $("#txt_telefono").val());
  console.log(
    "Valor JS nativo:",
    document.getElementById("txt_telefono")?.value
  );
  console.log("Valor final (celular):", celular);
  console.log("Tipo:", typeof celular);
  console.log("Length:", celular.length);
  console.log("═══════════════════════════════════════");

  // 2️⃣ VALIDACIONES BÁSICAS
  if (!tipo_comprobante)
    return Swal.fire(
      "Advertencia",
      "Seleccione un tipo de comprobante",
      "warning"
    );
  if (!serie)
    return Swal.fire(
      "Advertencia",
      "Ingrese la serie del comprobante",
      "warning"
    );
  if (!correlativo)
    return Swal.fire(
      "Advertencia",
      "Ingrese el número correlativo del comprobante",
      "warning"
    );
  if (!fecha_emision)
    return Swal.fire(
      "Advertencia",
      "Seleccione la fecha de emisión",
      "warning"
    );
  if (!tipo_documento_cliente)
    return Swal.fire(
      "Advertencia",
      "Seleccione el tipo de documento del cliente",
      "warning"
    );
  if (!numero_documento)
    return Swal.fire(
      "Advertencia",
      "Ingrese el número de documento del cliente",
      "warning"
    );
  if (!razon_social)
    return Swal.fire(
      "Advertencia",
      "Ingrese la razón social o nombre del cliente",
      "warning"
    );
  if (!id_servicio || id_servicio === "0")
    return Swal.fire("Advertencia", "Seleccione un servicio", "warning");
  if (!id_conductor || id_conductor === "0")
    return Swal.fire("Advertencia", "Seleccione un conductor", "warning");
  if (!id_origen || id_origen === "0")
    return Swal.fire("Advertencia", "Seleccione la ruta de origen", "warning");
  if (!id_destino || id_destino === "0")
    return Swal.fire("Advertencia", "Seleccione la ruta de destino", "warning");
  if (!fecha_viaje)
    return Swal.fire("Advertencia", "Seleccione la fecha del viaje", "warning");
  if (!forma_pago)
    return Swal.fire("Advertencia", "Seleccione la forma de pago", "warning");
  if (!id_tipo_pago || id_tipo_pago === "0")
    return Swal.fire(
      "Advertencia",
      "Seleccione un tipo de pago válido",
      "warning"
    );
  if (!base_gravada || base_gravada <= 0)
    return Swal.fire(
      "Advertencia",
      "Ingrese una base gravada válida",
      "warning"
    );
  if (!igv || igv < 0)
    return Swal.fire("Advertencia", "Ingrese un IGV válido", "warning");
  if (!total || total <= 0)
    return Swal.fire("Advertencia", "El total no puede ser 0", "warning");

  if (id_origen === id_destino) {
    return Swal.fire({
      icon: "warning",
      title: "Origen y Destino iguales",
      text: "La ruta de ORIGEN y DESTINO no pueden ser iguales. Por favor seleccione rutas diferentes.",
      confirmButtonText: "Entendido",
    });
  }

  // 3️⃣ CONSTRUIR OBJETO formData - SINTAXIS EXPLÍCITA
  let formData = {
    accion: "REGISTRAR_COMPROBANTE",
    tipo_comprobante: tipo_comprobante,
    serie: serie,
    correlativo: correlativo,
    fecha_emision: fecha_emision,
    moneda: moneda,
    tipo_documento_cliente: tipo_documento_cliente,
    numero_documento: numero_documento,
    razon_social: razon_social,
    direccion: direccion,
    celular: celular, // 👈 CRÍTICO: DEBE ESTAR AQUÍ
    departamento: departamento,
    provincia: provincia,
    distrito: distrito,
    ubigeo: "030101",
    id_servicio: id_servicio,
    cantidad: cantidad,
    id_conductor: id_conductor,
    id_origen: id_origen,
    id_destino: id_destino,
    fecha_viaje: fecha_viaje,
    base_gravada: base_gravada,
    igv: igv,
    total: total,
    forma_pago: forma_pago,
    id_tipo_pago: id_tipo_pago,
    observaciones: observaciones,
    estado_sunat: estadoSunat,
    id_usuario: id_usuario,
  };

  // 🔍 DEBUG FORMDATA
  console.log("📦 FormData completo:", formData);
  console.log("📱 Celular en formData:", formData.celular);
  console.log("📱 ¿Tiene propiedad 'celular'?:", "celular" in formData);
  console.log("📱 Todas las claves:", Object.keys(formData));

  // 4️⃣ CONFIRMACIÓN VISUAL
  Swal.fire({
    title: "¿Confirmar registro?",
    text: "Se guardará el comprobante localmente (sin enviar a SUNAT).",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, guardar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // 🔍 DEBUG FINAL ANTES DE ENVIAR
      console.log("═══════════════════════════════════════");
      console.log("📤 ENVIANDO AL SERVIDOR:");
      console.log("Celular:", formData.celular);
      console.log("FormData JSON:", JSON.stringify(formData, null, 2));
      console.log("═══════════════════════════════════════");

      // 5️⃣ ENVÍO AJAX
      $.ajax({
        url: "../controller/comprobante/controller_comprobante.php",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function (response) {
          console.log("✅ Respuesta del servidor:", response);

          if (typeof response === "string") {
            try {
              response = JSON.parse(response);
            } catch (e) {
              console.error("❌ No es JSON válido:", response);
              return Swal.fire(
                "Error",
                "Respuesta inválida del servidor",
                "error"
              );
            }
          }

          if (response.status === "success") {
            Swal.fire({
              title: "✅ Comprobante guardado correctamente",
              text: "El comprobante se ha registrado localmente. Puede enviarlo a SUNAT más adelante.",
              icon: "success",
              confirmButtonText: "Aceptar",
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire(
              "Error",
              response.message || "No se pudo registrar el comprobante",
              "error"
            );
          }
        },
        error: function (xhr, status, error) {
          console.error("❌ Error AJAX:", xhr.responseText);
          Swal.fire("Error", "No se pudo registrar el comprobante", "error");
        },
      });
    }
  });
}

function debugFormulario() {
  console.log("═══════════════════════════════════════");
  console.log("🔍 TODOS LOS INPUTS DEL FORMULARIO:");
  console.log("═══════════════════════════════════════");

  $("input, select, textarea").each(function (index) {
    let elemento = $(this);
    let info = {
      index: index,
      tipo: elemento.prop("tagName"),
      id: elemento.attr("id") || "SIN ID",
      name: elemento.attr("name") || "SIN NAME",
      value: elemento.val(),
      placeholder: elemento.attr("placeholder") || "",
    };

    // Resaltar campos relacionados con teléfono
    if (
      info.id.toLowerCase().includes("tel") ||
      info.id.toLowerCase().includes("cel") ||
      info.name.toLowerCase().includes("tel") ||
      info.name.toLowerCase().includes("cel")
    ) {
      console.log("📱 CAMPO TELÉFONO ENCONTRADO:", info);
    } else {
      console.log(info);
    }
  });

  console.log("═══════════════════════════════════════");
}

// GUARDAR Y ENVIAR A SUNAT
var envioEnProceso = false;

function setEstadoBotonGuardarEnviar(bloquear) {
  const $btn = $("#btn_guardar_enviar_sunat");
  if (!$btn.length) return;

  if (bloquear) {
    if (!$btn.data("html-original")) {
      $btn.data("html-original", $btn.html());
    }
    $btn.prop("disabled", true);
    $btn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
  } else {
    const htmlOriginal =
      $btn.data("html-original") ||
      '<i class="fas fa-paper-plane"></i> Guardar y Enviar a SUNAT';
    $btn.prop("disabled", false);
    $btn.html(htmlOriginal);
  }
}

function finalizarFlujoGuardarEnviar() {
  envioEnProceso = false;
  setEstadoBotonGuardarEnviar(false);
  reactivarRefrescoCorrelativo();
}

function guardarYEnviar() {
  if (envioEnProceso) {
    return Swal.fire(
      "Espere",
      "Ya hay un proceso de guardado/envío en curso. No vuelva a hacer clic todavía.",
      "info"
    );
  }
  // Primero guardar como PENDIENTE
  guardarComprobanteYEnviar();
}

function reactivarRefrescoCorrelativo() {
  if (intervalo_correlativo) clearInterval(intervalo_correlativo);
  intervalo_correlativo = setInterval(obtenerCorrelativo, 20000);
  suprimirAvisoCorrelativo = true;
  setTimeout(function () {
    obtenerCorrelativo();
  }, 300);
}

function guardarComprobanteYEnviar() {
  // Captura de datos igual que guardarComprobante()
  let tipo_comprobante = $("#select_tipo_comprobante").val();
  let serie = $("#txt_serie").val();
  let correlativo = $("#txt_correlativo").val();
  let fecha_emision = $("#txt_fecha_emision").val();
  let moneda = $("#select_moneda").val();
  let tipo_documento_cliente = $("#select_tipo_documento_cliente").val();
  let numero_documento = $("#txt_numero_documento").val();
  let razon_social = $("#txt_razon_social").val();
  let direccion = $("#txt_direccion").val();
  let celular = ($("#txt_telefono").val() || "").trim();
  let departamento = $("#txt_departamento").val();
  let provincia = $("#txt_provincia").val();
  let distrito = $("#txt_distrito").val();
  let id_servicio = $("#select_servicio").val();
  let cantidad = $("#txt_cantidad").val() || 1;
  let id_conductor = $("#select_conductor").val();
  let id_origen = $("#select_origen").val();
  let id_destino = $("#select_destino").val();
  let fecha_viaje = $("#txt_fecha_viaje").val();
  let base_gravada = $("#txt_base_gravada").val();
  let igv = $("#txt_igv").val();
  let total = $("#txt_total").val();
  let forma_pago = $("#select_forma_pago").val();
  let id_tipo_pago = $("#select_tipo_pago").val();
  let observaciones = $("#txt_observaciones").val();
  let id_usuario = $("#txtprincipalid").val();

  // Validaciones principales
  if (
    !tipo_comprobante ||
    !serie ||
    !correlativo ||
    !fecha_emision ||
    !tipo_documento_cliente ||
    !numero_documento ||
    !razon_social ||
    !id_servicio ||
    id_servicio === "0" ||
    !id_conductor ||
    id_conductor === "0" ||
    !id_origen ||
    id_origen === "0" ||
    !id_destino ||
    id_destino === "0" ||
    !fecha_viaje ||
    !forma_pago ||
    !id_tipo_pago ||
    id_tipo_pago === "0" ||
    !base_gravada ||
    base_gravada <= 0 ||
    !total ||
    total <= 0
  ) {
    return Swal.fire(
      "Advertencia",
      "Complete todos los campos obligatorios",
      "warning"
    );
  }
  if (id_origen === id_destino) {
    return Swal.fire({
      icon: "warning",
      title: "Origen y Destino iguales",
      text: "La ruta de ORIGEN y DESTINO no pueden ser iguales. Por favor seleccione rutas diferentes.",
      confirmButtonText: "Entendido",
    });
  }

  envioEnProceso = true;
  setEstadoBotonGuardarEnviar(true);

  // Reservar ventana de ticket desde el gesto del usuario para evitar bloqueo del navegador.
  // En modo estricto NO se imprime aún: solo se mostrará si SUNAT responde ACEPTADO.
  const popupWidth = 480;
  const popupHeight = 700;
  const left = (screen.width - popupWidth) / 2;
  const top = (screen.height - popupHeight) / 2;
  let ventanaTicket = window.open(
    "about:blank",
    "TicketSUNAT",
    `width=${popupWidth},height=${popupHeight},top=${top},left=${left},resizable=yes,scrollbars=yes,status=no`
  );
  if (ventanaTicket && !ventanaTicket.closed) {
    ventanaTicket.document.write(
      "<html><body style='font-family:Arial,sans-serif;padding:16px;'><h3>Validando con SUNAT...</h3><p>Se imprimira el ticket solo cuando SUNAT lo acepte.</p></body></html>"
    );
  }

  // Construir objeto de envío
  let formData = {
    accion: "REGISTRAR_COMPROBANTE",
    tipo_comprobante,
    serie,
    correlativo,
    fecha_emision,
    moneda,
    tipo_documento_cliente,
    numero_documento,
    razon_social,
    direccion,
    celular,
    departamento,
    provincia,
    distrito,
    ubigeo: "030101",
    id_servicio,
    cantidad,
    id_conductor,
    id_origen,
    id_destino,
    fecha_viaje,
    base_gravada,
    igv,
    total,
    forma_pago,
    id_tipo_pago,
    observaciones,
    estado_sunat: "PENDIENTE",
    id_usuario,
  };

  // Detener refresco solo cuando ya vamos a registrar/enviar
  if (intervalo_correlativo) clearInterval(intervalo_correlativo);

  Swal.fire({
    title: "Guardando y enviando...",
    html: "Paso 1/2: Registrando comprobante...",
    allowOutsideClick: false,
    showConfirmButton: false,
    willOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "../controller/comprobante/controller_comprobante.php",
    type: "POST",
    data: formData,
    dataType: "json",
    timeout: 45000,
  })
    .done(function (resp) {
      if (resp.status == "success") {
        // Ahora enviar a SUNAT en modo estricto (esperar respuesta para imprimir ticket)
        Swal.update({
          html: "Paso 2/2: Enviando a SUNAT y esperando respuesta...",
        });

        enviarASunat(
          resp.id_comprobante,
          resp.serie,
          resp.correlativo,
          ventanaTicket
        );
      } else {
        Swal.close();
        if (ventanaTicket && !ventanaTicket.closed) {
          ventanaTicket.close();
        }
        finalizarFlujoGuardarEnviar();
        Swal.fire("Error", resp.message, "error");
      }
    })
    .fail(function (xhr, status) {
      Swal.close();
      if (ventanaTicket && !ventanaTicket.closed) {
        ventanaTicket.close();
      }
      finalizarFlujoGuardarEnviar();
      if (status === "timeout") {
        Swal.fire(
          "Error",
          "El guardado tardó demasiado. Verifique su conexión y vuelva a intentar.",
          "error"
        );
      } else {
        Swal.fire("Error", "Error al guardar el comprobante", "error");
      }
    });
}

// ENVIAR A SUNAT
function enviarASunatEnSegundoPlano(
  id_comprobante,
  serie,
  correlativo,
  ventanaTicket
) {
  const popupWidth = 480;
  const popupHeight = 700;
  const left = (screen.width - popupWidth) / 2;
  const top = (screen.height - popupHeight) / 2;
  const urlTicket =
    "../view/MPDF/REPORTE/ticket_comprobante.php?id=" + id_comprobante;

  function abrirTicketSeguro() {
    if (ventanaTicket && !ventanaTicket.closed) {
      ventanaTicket.location.href = urlTicket;
      ventanaTicket.focus();
    } else {
      window.open(
        urlTicket,
        "TicketSUNAT",
        `width=${popupWidth},height=${popupHeight},top=${top},left=${left},resizable=yes,scrollbars=yes,status=no`
      );
    }
  }

  $.ajax({
    url: "../controller/comprobante/controller_comprobante.php",
    type: "POST",
    data: {
      accion: "ENVIAR_SUNAT",
      id_comprobante: id_comprobante,
      background: 1,
    },
    dataType: "json",
    timeout: 180000,
  })
    .done(function (resp) {
      Swal.close();

      if (resp.status == "success") {
        abrirTicketSeguro();

        Swal.fire({
          icon: "success",
          title: "Comprobante guardado",
          html: `
            <b>${serie}-${correlativo}</b> se registró correctamente.<br>
            <small>El envío a SUNAT quedó en segundo plano.</small>
          `,
          showConfirmButton: true,
        }).then(() => {
          limpiarFormulario();
          finalizarFlujoGuardarEnviar();
        });
      } else if (
        resp.status == "queued" ||
        resp.status == "pending" ||
        resp.status == "info" ||
        resp.status == "warning"
      ) {
        // En modo rapido abrir ticket SI o SI (aunque SUNAT siga en proceso)
        abrirTicketSeguro();
        Swal.fire({
          icon: "success",
          title: "Comprobante guardado",
          html: `
            <b>${serie}-${correlativo}</b> se registró correctamente.<br>
            <small>Envío a SUNAT en proceso (segundo plano).</small><br>
            <small><b>Ticket generado. Si aún figura pendiente, reimprima luego de ACEPTADO para validez tributaria.</b></small>
          `,
          showConfirmButton: true,
        }).then(() => {
          limpiarFormulario();
          finalizarFlujoGuardarEnviar();
        });
      } else {
        finalizarFlujoGuardarEnviar();
        Swal.fire({
          icon: "error",
          title: "No se inició el envío en segundo plano",
          html: resp.message || "Intente reenviar desde la lista de pendientes.",
          showConfirmButton: true,
        });
      }
    })
    .fail(function (_xhr, status) {
      Swal.close();
      if (ventanaTicket && !ventanaTicket.closed) {
        ventanaTicket.close();
      }
      finalizarFlujoGuardarEnviar();
      const msg = status === "timeout"
        ? "SUNAT demoró demasiado. El comprobante quedó guardado; revise su estado en la lista."
        : "No se pudo enviar a SUNAT. El comprobante quedó guardado; reenvíelo desde pendientes.";
      Swal.fire("Advertencia", msg, "warning");
    });
}

// Envuelve el log técnico crudo de SUNAT en un detalle colapsado
// para no saturar el modal con el volcado de reintentos.
function detalleTecnicoHtml(output) {
  if (!output) return "";
  return (
    '<details style="text-align:left; margin-top:8px;">' +
    '<summary style="cursor:pointer; color:#888;"><small>Ver detalle técnico</small></summary>' +
    '<small style="color:#999;">' + output + "</small>" +
    "</details>"
  );
}

function enviarASunat(id_comprobante, serie, correlativo, ventanaTicket = null) {
  const popupWidth = 480;
  const popupHeight = 700;
  const left = (screen.width - popupWidth) / 2;
  const top = (screen.height - popupHeight) / 2;
  const urlTicket =
    "../view/MPDF/REPORTE/ticket_comprobante.php?id=" + id_comprobante;

  function abrirTicketSeguro() {
    if (ventanaTicket && !ventanaTicket.closed) {
      ventanaTicket.location.href = urlTicket;
      ventanaTicket.focus();
    } else {
      window.open(
        urlTicket,
        "TicketSUNAT",
        `width=${popupWidth},height=${popupHeight},top=${top},left=${left},resizable=yes,scrollbars=yes,status=no`
      );
    }
  }

  function cerrarVentanaReserva() {
    if (ventanaTicket && !ventanaTicket.closed) {
      ventanaTicket.close();
    }
  }

  $.ajax({
    url: "../controller/comprobante/controller_comprobante.php",
    type: "POST",
    data: {
      accion: "ENVIAR_SUNAT",
      id_comprobante: id_comprobante,
    },
    dataType: "json",
    timeout: 180000,
  })
    .done(function (resp) {
      Swal.close();

      if (resp.status == "success") {
        abrirTicketSeguro();

        Swal.fire({
          icon: "success",
          title: "¡Comprobante enviado correctamente!",
          html: `
            <b>${serie}-${correlativo}</b> fue enviado a SUNAT.<br>
            <small>Se abrió el ticket en una ventana emergente.</small>
          `,
          showConfirmButton: true,
        }).then(() => {
          limpiarFormulario();
          finalizarFlujoGuardarEnviar();
        });
      } else if (
        resp.status == "queued" ||
        resp.status == "pending" ||
        resp.status == "info" ||
        resp.status == "warning"
      ) {
        cerrarVentanaReserva();
        reactivarRefrescoCorrelativo();
        Swal.fire({
          icon: resp.status == "info" ? "info" : "warning",
          title: "SUNAT respondió temporalmente",
          html:
            resp.message +
            "<br><small><b>No anules</b> el comprobante. Reintenta el envío en 1-2 minutos.</small>" +
            detalleTecnicoHtml(resp.output),
          showConfirmButton: true,
        }).then(() => {
          // El comprobante ya fue guardado; limpiar para continuar con el siguiente
          limpiarFormulario();
          finalizarFlujoGuardarEnviar();
        });
      } else {
        cerrarVentanaReserva();
        finalizarFlujoGuardarEnviar();
        Swal.fire({
          icon: "error",
          title: "Error al enviar a SUNAT",
          html: resp.message + detalleTecnicoHtml(resp.output),
          showConfirmButton: true,
        });
      }
    })
    .fail(function (xhr, status) {
      Swal.close();
      cerrarVentanaReserva();
      finalizarFlujoGuardarEnviar();
      if (status === "timeout") {
        Swal.fire(
          "Advertencia",
          "SUNAT demoró demasiado en responder. El comprobante ya está guardado; revise su estado en la lista e intente reenviar si quedó PENDIENTE.",
          "warning"
        );
      } else {
        Swal.fire("Error", "Error al comunicarse con SUNAT", "error");
      }
    });
}

// LIMPIAR FORMULARIO
function limpiarFormulario() {
  // Guardar tipo de comprobante y serie antes de limpiar
  let tipoComprobante = $("#select_tipo_comprobante").val();
  let serie = $("#txt_serie").val();

  $("#select_tipo_comprobante").val("").trigger("change");
  $("#txt_serie").val("");
  $("#txt_correlativo").val("");
  $("#select_tipo_documento_cliente").val("").trigger("change");
  $("#txt_numero_documento").val("");
  $("#txt_razon_social").val("");
  $("#txt_direccion").val("");
  $("#txt_telefono").val("");
  $("#select_servicio").val("").trigger("change");
  $("#txt_cantidad").val(1);

  // Limpiar selects con Select2 de forma robusta (incluye placeholders deshabilitados)
  $("#select_conductor").val(null).trigger("change");
  $("#select_origen").val(null).trigger("change");
  $("#select_destino").val(null).trigger("change");
  $("#txt_observaciones").val("");
  $("#txt_dni_pasajero").val("");
  $("#txt_nombre_pasajero").val("");
  $("#txt_asiento").val("");
  $("#txt_placa").val("");
  $("#txt_base_gravada").val("");
  $("#txt_igv").val("");
  $("#txt_total").val("");

  // Establecer fecha actual
  var hoy = new Date().toISOString().split("T")[0];
  $("#txt_fecha_emision").val(hoy);

  // Restaurar tipo de comprobante y serie, y cargar siguiente correlativo
  if (tipoComprobante && serie) {
    $("#select_tipo_comprobante").val(tipoComprobante).trigger("change");
    $("#txt_serie").val(serie);

    // Cargar el siguiente correlativo automáticamente
    suprimirAvisoCorrelativo = true;
    setTimeout(function () {
      obtenerCorrelativo();
    }, 300); // Pequeño delay para asegurar que los campos estén listos
  }
}

// VER LISTA DE COMPROBANTES

// FUNCIONES AUXILIARES
function soloNumeros(e) {
  var key = e.charCode || e.keyCode || 0;
  return (key >= 48 && key <= 57) || key == 8 || key == 9;
}

function sololetras(e) {
  var key = e.charCode || e.keyCode || 0;
  return (
    (key >= 65 && key <= 90) ||
    (key >= 97 && key <= 122) ||
    key == 32 ||
    key == 8 ||
    key == 9 ||
    key == 225 ||
    key == 233 ||
    key == 237 ||
    key == 243 ||
    key == 250 ||
    key == 193 ||
    key == 201 ||
    key == 205 ||
    key == 211 ||
    key == 218 ||
    key == 241 ||
    key == 209
  );
}

// listados:
var tbl_comprobantes;
var intervalo_refresco_comprobantes = null;

function iniciarAutoRefrescoComprobantes() {
  if (intervalo_refresco_comprobantes) {
    clearInterval(intervalo_refresco_comprobantes);
  }

  intervalo_refresco_comprobantes = setInterval(function () {
    try {
      if (
        tbl_comprobantes &&
        $.fn.DataTable.isDataTable("#tabla_comprobantes") &&
        $("#tabla_comprobantes").is(":visible")
      ) {
        tbl_comprobantes.ajax.reload(null, false);
      }
    } catch (e) {
      // Evitar que un error de refresco afecte al flujo principal
      console.warn("Auto-refresco de comprobantes omitido:", e);
    }
  }, 15000);
}

// ============================================================
// LISTAR TODOS LOS COMPROBANTES CON EXPORTACIÓN
// ============================================================
function listar_comprobantes() {
  tbl_comprobantes = $("#tabla_comprobantes").DataTable({
    scrollCollapse: false, // 👈 CAMBIAR A false
    ordering: true,
    order: [[3, "desc"]], // 👈 ORDENAR por la columna "Fecha Emisión" descendente
    bLengthChange: true,
    searching: true,
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "Todos"],
    ],
    pageLength: 10,
    destroy: true,
    async: false,
    processing: true,
    responsive: true,
    dom: '<"row"<"col-sm-6"l><"col-sm-6"f>><"row"<"col-sm-12 text-right"B>>rtip',
    buttons: [
      {
        extend: "excelHtml5",
        text: '<i class="fas fa-file-excel"></i> Excel',
        titleAttr: "Exportar a Excel",
        className: "btn btn-success btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        },
        title: "Comprobantes Electrónicos",
        filename: "Comprobantes_" + new Date().toISOString().slice(0, 10),
        customize: function (xlsx) {
          var sheet = xlsx.xl.worksheets["sheet1.xml"];
          // Estilos personalizados para Excel
          $('row c[r^="H"]', sheet).attr("s", "67"); // Formato moneda
        },
      },
      {
        extend: "pdfHtml5",
        text: '<i class="fas fa-file-pdf"></i> PDF',
        titleAttr: "Exportar a PDF",
        className: "btn btn-danger btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 10],
        },
        title: "Comprobantes Electrónicos",
        filename: "Comprobantes_" + new Date().toISOString().slice(0, 10),
        orientation: "landscape",
        pageSize: "A4",
        customize: function (doc) {
          doc.styles.title = {
            color: "#0066cc",
            fontSize: "20",
            alignment: "center",
            bold: true,
            margin: [0, 0, 0, 20],
          };
          doc.defaultStyle.fontSize = 7;
          doc.styles.tableHeader = {
            bold: true,
            fontSize: 8,
            color: "white",
            fillColor: "#2c3e50",
            alignment: "center",
          };
          // Ancho de columnas
          doc.content[1].table.widths = [
            "5%",
            "10%",
            "12%",
            "10%",
            "18%",
            "12%",
            "13%",
            "8%",
            "7%",
            "5%",
          ];
        },
      },
      {
        extend: "print",
        text: '<i class="fas fa-print"></i> Imprimir',
        titleAttr: "Imprimir",
        className: "btn btn-info btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 10],
        },
        title: "Comprobantes Electrónicos",
        messageTop:
          '<h4 style="text-align:center; color:#0066cc;">Listado de Comprobantes Electrónicos</h4>',
        customize: function (win) {
          $(win.document.body).css("font-size", "9pt");
          $(win.document.body)
            .find("table")
            .addClass("compact")
            .css("font-size", "9pt");
          $(win.document.body).find("h1").css("text-align", "center");
        },
      },
    ],
    ajax: {
      url: "../controller/comprobante/controller_comprobante.php",
      type: "POST",
      data: {
        accion: "LISTAR_COMPROBANTES",
        fecha_desde: $("#txt_fecha_desde").val(),
        fecha_hasta: $("#txt_fecha_hasta").val(),
      },
    },
    columns: [
      { data: "id_comprobante" },
      {
        data: "tipo_comprobante",
        render: function (data) {
          if (data == "01")
            return '<span class="badge badge-info">FACTURA</span>';
          if (data == "03")
            return '<span class="badge badge-primary">BOLETA</span>';
          if (data == "07")
            return '<span class="badge badge-warning">N. CRÉDITO</span>';
          if (data == "08")
            return '<span class="badge badge-secondary">N. DÉBITO</span>';
          return data;
        },
      },
      {
        data: null,
        render: (data) => "<b>" + data.numero_comprobante + "</b>",
      },
      {
        data: "fecha_hora_emision",
        render: function (data, type, row) {
          if (!data) return "-";

          // Data viene como "YYYY-MM-DD HH:MM:SS"
          const [fecha, hora] = data.split(" ");
          const [yyyy, mm, dd] = fecha.split("-");
          const formatted = `${dd}/${mm}/${yyyy} ${hora}`;

          // Para ordenamiento y exportación, devolver data en formato original
          if (type === "sort" || type === "type") {
            return data; // mantiene orden por YYYY-MM-DD HH:MM:SS
          }

          return formatted;
        },
      },

      { data: "razon_social" },
      { data: "numero_documento" },
      {
        data: null,
        render: (data) =>
          data.origen && data.destino
            ? data.origen + " → " + data.destino
            : "-",
      },
      {
        data: "total",
        render: (data) => "S/ " + parseFloat(data).toFixed(2),
      },
      {
        data: "estado_sunat",
        render: function (data) {
          if (data == "PENDIENTE")
            return '<span class="badge badge-warning"><i class="fas fa-clock"></i> PENDIENTE</span>';
          if (data == "ENVIADO" || data == "ACEPTADO")
            return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> ACEPTADO</span>';
          if (data == "RECHAZADO")
            return '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> RECHAZADO</span>';
          if (data == "ANULADO")
            return '<span class="badge badge-secondary"><i class="fas fa-ban"></i> ANULADO</span>';
          return data;
        },
      },
      {
        data: "descripcion_respuesta_sunat",
        render: (data) =>
          data ? '<small class="text-muted">' + data + "</small>" : "-",
      },
      { data: "usuario_nombre" },
      {
        data: null,
        orderable: false,
        render: function (data) {
          let estado = data.estado_sunat;
          let estado_doc = data.estado_documento;
          let tipo = data.tipo_comprobante;

          let botones = `
            <div class="btn-group" role="group">
                <button class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" data-boundary="window">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" style="z-index:1050;">
                    <a class="dropdown-item" href="javascript:void(0)" onclick="verDetalle(${data.id_comprobante})">
                        <i class="fas fa-eye text-info"></i> Ver Detalle
                    </a>`;

          // ✅ BOTÓN EDITAR (solo para PENDIENTE)
          if (estado == "PENDIENTE" && estado_doc == "ACTIVO") {
            botones += `
                <a class="dropdown-item" href="javascript:void(0)" onclick="editarComprobante(${data.id_comprobante})">
                    <i class="fas fa-edit text-warning"></i> Editar Comprobante
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="javascript:void(0)" onclick="abrirModalEnviar(${data.id_comprobante}, '${data.serie}', '${data.correlativo}')">
                    <i class="fas fa-paper-plane text-success"></i> Enviar a SUNAT
                </a>`;
          }

          if (
            (estado == "ENVIADO" || estado == "ACEPTADO") &&
            estado_doc == "ACTIVO"
          ) {
            botones += `
                <a class="dropdown-item" href="javascript:void(0)" onclick="descargarXML('${data.serie}', '${data.correlativo}')">
                    <i class="fas fa-file-code text-primary"></i> Descargar XML
                </a>
                <a class="dropdown-item" href="javascript:void(0)" onclick="descargarCDR('${data.serie}', '${data.correlativo}')">
                    <i class="fas fa-file-archive text-secondary"></i> Descargar CDR
                </a>
                <a class="dropdown-item" href="javascript:void(0)" onclick="imprimirTicket(${data.id_comprobante})">
                    <i class="fas fa-print text-dark"></i> Imprimir Ticket
                </a>`;
          }

          if (estado_doc == "ACTIVO") {
            botones += `
                <a class="dropdown-item" href="javascript:void(0)" onclick="descargarPDF(${data.id_comprobante})">
                    <i class="fas fa-file-pdf text-danger"></i> Descargar PDF
                </a>`;
          }

          // En la función render de la columna de acciones
          if (
            tipo == "01" &&
            (estado == "ENVIADO" || estado == "ACEPTADO") &&
            estado_doc == "ACTIVO"
          ) {
            botones += `
              <div class="dropdown-divider"></div>
              <a class="dropdown-item text-danger" href="javascript:void(0)" 
                onclick="abrirModalAnularComprobante(${data.id_comprobante}, '${data.serie}', '${data.correlativo}', '01')">
                  <i class="fas fa-ban"></i> Anular Factura (Comunicar SUNAT)
              </a>`;
          }

          if (
            tipo == "03" &&
            (estado == "ENVIADO" || estado == "ACEPTADO") &&
            estado_doc == "ACTIVO"
          ) {
            botones += `
              <div class="dropdown-divider"></div>
              <a class="dropdown-item text-danger" href="javascript:void(0)" 
                onclick="abrirModalAnularComprobante(${data.id_comprobante}, '${data.serie}', '${data.correlativo}', '03')">
                  <i class="fas fa-file-invoice"></i> Anular Boleta (Comunicar SUNAT)
              </a>`;
          }

         
          botones += `
                </div>
            </div>`;
          return botones;
        },
      },
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
      buttons: {
        excel: "Excel",
        pdf: "PDF",
        print: "Imprimir",
      },
    },
  });

  iniciarAutoRefrescoComprobantes();
}

// ============================================================
// LISTAR CON FILTROS Y EXPORTACIÓN
// ============================================================
function listar_comprobantes_filtro() {
  let estado = $("#select_estado_filtro").val();
  let fecha_desde = $("#txt_fecha_desde").val();
  let fecha_hasta = $("#txt_fecha_hasta").val();

  if (tbl_comprobantes) tbl_comprobantes.destroy();

  tbl_comprobantes = $("#tabla_comprobantes").DataTable({
    scrollCollapse: false, // 👈 CAMBIAR A false

    ordering: true,
    order: [[3, "desc"]], // 👈 ORDENAR por la columna "Fecha Emisión" descendente
    bLengthChange: true,
    searching: true,
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "Todos"],
    ],
    pageLength: 10,
    destroy: true,
    processing: true,
    responsive: true,
    dom: '<"row"<"col-sm-6"l><"col-sm-6"f>><"row"<"col-sm-12 text-right"B>>rtip',
    buttons: [
      {
        extend: "excelHtml5",
        text: '<i class="fas fa-file-excel"></i> Excel',
        titleAttr: "Exportar a Excel",
        className: "btn btn-success btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        },
        title: "Comprobantes Electrónicos - Filtrado",
        filename:
          "Comprobantes_Filtrado_" + new Date().toISOString().slice(0, 10),
      },
      {
        extend: "pdfHtml5",
        text: '<i class="fas fa-file-pdf"></i> PDF',
        titleAttr: "Exportar a PDF",
        className: "btn btn-danger btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 10],
        },
        title: "Comprobantes Electrónicos - Filtrado",
        filename:
          "Comprobantes_Filtrado_" + new Date().toISOString().slice(0, 10),
        orientation: "landscape",
        pageSize: "A4",
        customize: function (doc) {
          doc.styles.title = {
            color: "#0066cc",
            fontSize: "20",
            alignment: "center",
            bold: true,
          };
          doc.defaultStyle.fontSize = 7;
          doc.styles.tableHeader = {
            bold: true,
            fontSize: 8,
            color: "white",
            fillColor: "#2c3e50",
            alignment: "center",
          };

          // Agregar información de filtros
          let filterInfo = [];
          if (estado)
            filterInfo.push({ text: "Estado: " + estado, style: "filterText" });
          if (fecha_desde)
            filterInfo.push({
              text: "Desde: " + fecha_desde,
              style: "filterText",
            });
          if (fecha_hasta)
            filterInfo.push({
              text: "Hasta: " + fecha_hasta,
              style: "filterText",
            });

          if (filterInfo.length > 0) {
            doc.content.splice(1, 0, {
              text: "Filtros Aplicados:",
              style: "filterTitle",
              margin: [0, 10, 0, 5],
            });
            doc.content.splice(2, 0, {
              columns: filterInfo,
              margin: [0, 0, 0, 15],
            });

            doc.styles.filterTitle = {
              fontSize: 11,
              bold: true,
              color: "#555",
            };
            doc.styles.filterText = {
              fontSize: 9,
              color: "#666",
            };
          }

          // Ancho de columnas
          doc.content[doc.content.length - 1].table.widths = [
            "5%",
            "10%",
            "12%",
            "10%",
            "18%",
            "12%",
            "13%",
            "8%",
            "7%",
            "5%",
          ];
        },
      },
      {
        extend: "print",
        text: '<i class="fas fa-print"></i> Imprimir',
        titleAttr: "Imprimir",
        className: "btn btn-info btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 10],
        },
        title: "Comprobantes Electrónicos - Filtrado",
        messageTop: function () {
          let msg =
            '<h4 style="text-align:center; color:#0066cc;">Listado de Comprobantes Electrónicos</h4>';
          msg +=
            '<p style="text-align:center;"><strong>Filtros aplicados:</strong><br>';
          if (estado) msg += "Estado: " + estado + " | ";
          if (fecha_desde) msg += "Desde: " + fecha_desde + " | ";
          if (fecha_hasta) msg += "Hasta: " + fecha_hasta;
          msg += "</p>";
          return msg;
        },
        customize: function (win) {
          $(win.document.body).css("font-size", "9pt");
          $(win.document.body)
            .find("table")
            .addClass("compact")
            .css("font-size", "9pt");
        },
      },
    ],
    ajax: {
      url: "../controller/comprobante/controller_comprobante.php",
      type: "POST",
      data: {
        accion: "LISTAR_COMPROBANTES",
        estado,
        fecha_desde,
        fecha_hasta,
      },
    },
    columns: [
      { data: "id_comprobante" },
      {
        data: "tipo_comprobante",
        render: (data) => {
          if (data == "01")
            return '<span class="badge badge-info">FACTURA</span>';
          if (data == "03")
            return '<span class="badge badge-primary">BOLETA</span>';
          if (data == "07")
            return '<span class="badge badge-warning">N. CRÉDITO</span>';
          if (data == "08")
            return '<span class="badge badge-secondary">N. DÉBITO</span>';
          return data;
        },
      },
      {
        data: null,
        render: (data) => "<b>" + data.numero_comprobante + "</b>",
      },
      {
        data: "fecha_hora_emision",
        render: function (data, type, row) {
          if (!data) return "-";

          // Data viene como "YYYY-MM-DD HH:MM:SS"
          const [fecha, hora] = data.split(" ");
          const [yyyy, mm, dd] = fecha.split("-");
          const formatted = `${dd}/${mm}/${yyyy} ${hora}`;

          // Para ordenamiento y exportación, devolver data en formato original
          if (type === "sort" || type === "type") {
            return data; // mantiene orden por YYYY-MM-DD HH:MM:SS
          }

          return formatted;
        },
      },
      { data: "razon_social" },
      { data: "numero_documento" },
      {
        data: null,
        render: (data) =>
          data.origen && data.destino
            ? data.origen + " → " + data.destino
            : "-",
      },
      {
        data: "total",
        render: (data) => "S/ " + parseFloat(data).toFixed(2),
      },
      {
        data: "estado_sunat",
        render: function (data) {
          if (data == "PENDIENTE")
            return '<span class="badge badge-warning"><i class="fas fa-clock"></i> PENDIENTE</span>';
          if (data == "ENVIADO" || data == "ACEPTADO")
            return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> ACEPTADO</span>';
          if (data == "RECHAZADO")
            return '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> RECHAZADO</span>';
          if (data == "ANULADO")
            return '<span class="badge badge-secondary"><i class="fas fa-ban"></i> ANULADO</span>';
          return data;
        },
      },
      {
        data: "descripcion_respuesta_sunat",
        render: (data) =>
          data ? '<small class="text-muted">' + data + "</small>" : "-",
      },
      { data: "usuario_nombre" },
      {
        data: null,
        orderable: false,
        render: function (data) {
          let estado = data.estado_sunat;
          let estado_doc = data.estado_documento;
          let tipo = data.tipo_comprobante;

          let botones = `
            <div class="btn-group" role="group">
                <button class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" data-boundary="window">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" style="z-index:1050;">
                    <a class="dropdown-item" href="javascript:void(0)" onclick="verDetalle(${data.id_comprobante})">
                        <i class="fas fa-eye text-info"></i> Ver Detalle
                    </a>`;

          // ✅ BOTÓN EDITAR (solo para PENDIENTE)
          if (estado == "PENDIENTE" && estado_doc == "ACTIVO") {
            botones += `
                <a class="dropdown-item" href="javascript:void(0)" onclick="editarComprobante(${data.id_comprobante})">
                    <i class="fas fa-edit text-warning"></i> Editar Comprobante
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="javascript:void(0)" onclick="abrirModalEnviar(${data.id_comprobante}, '${data.serie}', '${data.correlativo}')">
                    <i class="fas fa-paper-plane text-success"></i> Enviar a SUNAT
                </a>`;
          }

          if (
            (estado == "ENVIADO" || estado == "ACEPTADO") &&
            estado_doc == "ACTIVO"
          ) {
            botones += `
                <a class="dropdown-item" href="javascript:void(0)" onclick="descargarXML('${data.serie}', '${data.correlativo}')">
                    <i class="fas fa-file-code text-primary"></i> Descargar XML
                </a>
                <a class="dropdown-item" href="javascript:void(0)" onclick="descargarCDR('${data.serie}', '${data.correlativo}')">
                    <i class="fas fa-file-archive text-secondary"></i> Descargar CDR
                </a>
                <a class="dropdown-item" href="javascript:void(0)" onclick="imprimirTicket(${data.id_comprobante})">
                    <i class="fas fa-print text-dark"></i> Imprimir Ticket
                </a>`;
          }

          if (estado_doc == "ACTIVO") {
            botones += `
                <a class="dropdown-item" href="javascript:void(0)" onclick="descargarPDF(${data.id_comprobante})">
                    <i class="fas fa-file-pdf text-danger"></i> Descargar PDF
                </a>`;
          }

          // En la función render de la columna de acciones
          if (
            tipo == "01" &&
            (estado == "ENVIADO" || estado == "ACEPTADO") &&
            estado_doc == "ACTIVO"
          ) {
            botones += `
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger" href="javascript:void(0)" 
           onclick="abrirModalAnularComprobante(${data.id_comprobante}, '${data.serie}', '${data.correlativo}', '01')">
            <i class="fas fa-ban"></i> Anular Factura (Comunicar SUNAT)
        </a>`;
          }

          if (
            tipo == "03" &&
            (estado == "ENVIADO" || estado == "ACEPTADO") &&
            estado_doc == "ACTIVO"
          ) {
            botones += `
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger" href="javascript:void(0)" 
           onclick="abrirModalAnularComprobante(${data.id_comprobante}, '${data.serie}', '${data.correlativo}', '03')">
            <i class="fas fa-file-invoice"></i> Anular Boleta (Comunicar SUNAT)
        </a>`;
          }

        

          botones += `
                </div>
            </div>`;
          return botones;
        },
      },
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
      buttons: {
        excel: "Excel",
        pdf: "PDF",
        print: "Imprimir",
      },
    },
  });
}

// ============================================================
// VER DETALLE DEL COMPROBANTE
// ============================================================
// ============================================================
// VER DETALLE DEL COMPROBANTE (Versión profesional con tipo de pago)
// ============================================================
function verDetalle(id) {
  $.ajax({
    url: "../controller/comprobante/controller_comprobante.php",
    type: "POST",
    data: {
      accion: "OBTENER_COMPROBANTE",
      id_comprobante: id,
    },
    dataType: "json",
  })
    .done(function (data) {
      if (data) {
        let html = `
                <div class="container-fluid px-3 py-2">

                    <!-- ENCABEZADO -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="text-primary fw-bold mb-0">
                                    <i class="fas fa-file-invoice"></i> ${
                                      data.tipo_comprobante == "01"
                                        ? "FACTURA"
                                        : "BOLETA"
                                    }
                                </h5>
                                <span class="badge bg-secondary fs-6">${
                                  data.numero_comprobante
                                }</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4"><i class="far fa-calendar-alt text-muted"></i> <b>Fecha:</b> ${
                                  data.fecha_emision
                                }</div>
                                <div class="col-md-4"><i class="fas fa-coins text-muted"></i> <b>Moneda:</b> ${
                                  data.moneda || "Soles"
                                }</div>
                                <div class="col-md-4"><i class="fas fa-credit-card text-muted"></i> <b>Tipo de Pago:</b> ${
                                  data.tipo_pago_actual || "No especificado"
                                }</div>
                            </div>
                        </div>
                    </div>

                    <!-- DATOS DEL CLIENTE -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-primary text-white py-2">
                            <i class="fas fa-user-circle"></i> <b>Datos del Cliente</b>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Razón Social:</strong><br>
                                    ${data.razon_social}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>N° Documento:</strong><br>
                                    ${data.numero_documento}
                                </div>
                                <div class="col-md-12 mb-2">
                                    <strong>Dirección:</strong><br>
                                    ${data.direccion || "-"}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RESUMEN DE MONTOS -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-success text-white py-2">
                            <i class="fas fa-cash-register"></i> <b>Resumen del Comprobante</b>
                        </div>
                        <div class="card-body p-3">
                            <div class="row text-center">
                                <div class="col-md-3 mb-2">
                                    <h6 class="text-muted mb-1">Base Gravada</h6>
                                    <span class="fw-bold">S/ ${parseFloat(
                                      data.total_gravada
                                    ).toFixed(2)}</span>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <h6 class="text-muted mb-1">IGV</h6>
                                    <span class="fw-bold">S/ ${parseFloat(
                                      data.total_igv
                                    ).toFixed(2)}</span>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <h6 class="text-muted mb-1">Descuento</h6>
                                    <span class="fw-bold">S/ ${(
                                      parseFloat(data.total_descuento) || 0
                                    ).toFixed(2)}</span>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <h6 class="text-muted mb-1">Total</h6>
                                    <span class="fw-bold text-success fs-5">S/ ${parseFloat(
                                      data.total
                                    ).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ESTADO SUNAT -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-info text-white py-2">
                            <i class="fas fa-paper-plane"></i> <b>Estado SUNAT</b>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <strong>Estado:</strong>
                                    <span class="badge ${
                                      data.estado_sunat === "ACEPTADO"
                                        ? "bg-success"
                                        : "bg-danger"
                                    }">
                                        ${data.estado_sunat}
                                    </span>
                                </div>
                                ${
                                  data.fecha_envio_sunat
                                    ? `
                                    <div class="col-md-4 mb-2">
                                        <strong>Fecha Envío:</strong> ${data.fecha_envio_sunat}
                                    </div>`
                                    : ""
                                }
                                ${
                                  data.descripcion_respuesta_sunat
                                    ? `
                                    <div class="col-md-12 mt-2">
                                        <strong>Respuesta SUNAT:</strong><br>
                                        <small class="text-muted">${data.descripcion_respuesta_sunat}</small>
                                    </div>`
                                    : ""
                                }
                            </div>
                        </div>
                    </div>
            `;

        // DETALLE DE ÍTEMS
        if (data.detalles && data.detalles.length > 0) {
          html += `
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-warning text-dark py-2">
                            <i class="fas fa-list"></i> <b>Detalle de Ítems</b>
                        </div>
                        <div class="card-body p-3 table-responsive">
                            <table class="table table-hover table-bordered table-sm align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unit.</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>`;
          data.detalles.forEach((item, i) => {
            html += `
                        <tr>
                            <td class="text-center">${i + 1}</td>
                            <td>${item.descripcion}</td>
                            <td class="text-center">${item.cantidad}</td>
                            <td class="text-end">S/ ${parseFloat(
                              item.precio_unitario
                            ).toFixed(2)}</td>
                            <td class="text-end fw-bold">S/ ${parseFloat(
                              item.subtotal
                            ).toFixed(2)}</td>
                        </tr>`;
          });
          html += `
                                </tbody>
                            </table>
                        </div>
                    </div>`;
        }

        html += `</div>`;

        $("#contenido_detalle").html(html);
        $("#modal_detalle").modal("show");
      } else {
        Swal.fire(
          "Advertencia",
          "No se encontró información del comprobante.",
          "warning"
        );
      }
    })
    .fail(function () {
      Swal.fire(
        "Error",
        "No se pudo obtener el detalle del comprobante.",
        "error"
      );
    });
}

// ============================================================
// ABRIR MODAL ENVIAR A SUNAT
// ============================================================
function abrirModalEnviar(id, serie, correlativo) {
  $("#txt_id_comprobante_enviar").val(id);
  $("#txt_serie_enviar").val(serie);
  $("#txt_correlativo_enviar").val(correlativo);
  $("#span_numero_enviar").text(serie + "-" + correlativo);
  $("#modal_enviar_sunat").modal("show");
}

// ============================================================
// CONFIRMAR ENVÍO A SUNAT
// ============================================================
function confirmarEnvioSunat() {
  let id = $("#txt_id_comprobante_enviar").val();
  let serie = $("#txt_serie_enviar").val();
  let correlativo = $("#txt_correlativo_enviar").val();

  $("#modal_enviar_sunat").modal("hide");

  Swal.fire({
    title: "Enviando a SUNAT...",
    html: "Procesando comprobante <b>" + serie + "-" + correlativo + "</b>",
    allowOutsideClick: false,
    showConfirmButton: false,
    willOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "../controller/comprobante/controller_comprobante.php",
    type: "POST",
    data: {
      accion: "ENVIAR_SUNAT",
      id_comprobante: id,
    },
    dataType: "json",
    timeout: 180000,
  })
    .done(function (resp) {
      Swal.close();

      if (resp.status == "success") {
        Swal.fire({
          icon: "success",
          title: "¡Comprobante aceptado por SUNAT!",
          html: "<b>" + serie + "-" + correlativo + "</b> enviado exitosamente",
          showConfirmButton: true,
        }).then(() => {
          tbl_comprobantes.ajax.reload();
        });
      } else if (
        resp.status == "queued" ||
        resp.status == "pending" ||
        resp.status == "info" ||
        resp.status == "warning"
      ) {
        Swal.fire({
          icon: resp.status == "info" ? "info" : "warning",
          title: "SUNAT respondió temporalmente",
          html:
            resp.message +
            "<br><small><b>No anules</b> el comprobante. Reintenta el envío en 1-2 minutos.</small>",
          showConfirmButton: true,
        }).then(() => {
          tbl_comprobantes.ajax.reload();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error al enviar a SUNAT",
          html: resp.message,
          showConfirmButton: true,
        });
      }
    })
    .fail(function () {
      Swal.close();
      Swal.fire("Error", "Error al comunicarse con SUNAT", "error");
    });
}

// ============================================================
// ABRIR MODAL ANULAR
// ============================================================
function abrirModalAnular(id) {
  $("#txt_id_comprobante_anular").val(id);
  $("#txt_motivo_anulacion").val("");
  $("#modal_anular").modal("show");
}

// ============================================================
// CONFIRMAR ANULACIÓN
// ============================================================
function confirmarAnulacion() {
  let id = $("#txt_id_comprobante_anular").val();
  let motivo = $("#txt_motivo_anulacion").val().trim();
  let usuario = $("#txtprincipalid").val().trim();

  if (!motivo) {
    return Swal.fire(
      "Advertencia",
      "Debe ingresar el motivo de anulación",
      "warning"
    );
  }

  $("#modal_anular").modal("hide");

  Swal.fire({
    title: "¿Está seguro?",
    text: "Esta acción no se puede revertir",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, Anular",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../controller/comprobante/controller_comprobante.php",
        type: "POST",
        data: {
          accion: "ANULAR_COMPROBANTE",
          id_comprobante: id,
          motivo: motivo,
          usuario: usuario,
        },
        dataType: "json",
      })
        .done(function (resp) {
          if (resp.status == "success") {
            Swal.fire({
              icon: "success",
              title: "Comprobante anulado",
              text: "El comprobante ha sido anulado correctamente",
              showConfirmButton: true,
            }).then(() => {
              tbl_comprobantes.ajax.reload();
            });
          } else {
            Swal.fire("Error", resp.message, "error");
          }
        })
        .fail(function () {
          Swal.fire("Error", "Error al anular el comprobante", "error");
        });
    }
  });
}

// ============================================================
// DESCARGAR XML
// ============================================================
function descargarXML(serie, correlativo) {
    window.location.href = "../greenter/descargar_xml.php?serie=" + serie + "&correlativo=" + correlativo;
}

// ============================================================
// DESCARGAR CDR
// ============================================================
function descargarCDR(serie, correlativo) {
  let correlativoPadded = String(correlativo).padStart(8, '0');
  let url = "../greenter/cdr/R-" + serie + "-" + correlativoPadded + ".zip";
  window.open(url, "_blank");
}

// ============================================================
// IMPRIMIR TICKET
// ============================================================
function imprimirTicket(id) {
  window.open("../view/MPDF/REPORTE/ticket_comprobante.php?id=" + id, "_blank");
}

// ============================================================
// DESCARGAR PDF
// ============================================================
function descargarPDF(id) {
  Swal.fire({
    title: "Generando PDF...",
    text: "Espere un momento",
    allowOutsideClick: false,
    showConfirmButton: false,
    willOpen: () => {
      Swal.showLoading();
    },
  });

  setTimeout(() => {
    Swal.close();
    window.open("../view/MPDF/REPORTE/pdf_comprobante.php?id=" + id, "_blank");
  }, 1000);
}

async function buscarPorDocumento() {
  const tipo = document
    .getElementById("select_tipo_documento_cliente")
    .value.trim();
  const dni = document.getElementById("txt_numero_documento").value.trim();

  // ✅ Validación correcta
  if (dni === "") {
    Swal.fire(
      "Advertencia",
      "Debe ingresar un número de documento válido.",
      "warning"
    );
    return;
  }

  // ✅ Asignar valor correcto
  const numero_documento = dni;

  try {
    const resp = await $.ajax({
      url: "../controller/encomiendas/controlador_buscar_persona_por_documento_compro.php",
      type: "POST",
      data: { numero_documento },
      dataType: "json",
    });

    if (resp.data && resp.data.length > 0) {
      const d = resp.data[0];

      // ✅ Rellenar campos
      $("#txt_razon_social").val(d.razon_social);
      $("#txt_direccion").val(d.direccion);
      $("#txt_telefono").val(d.telefono);
      $("#txt_departamento").val(d.departamento);
      $("#txt_provincia").val(d.provincia);
    } else {
      Swal.fire(
        "No encontrado",
        "No se encontró ninguna persona o empresa con ese documento.",
        "info"
      );
    }
  } catch (error) {
    console.error("❌ Error en AJAX:", error);
    Swal.fire("Error", "No se pudo hacer la búsqueda.", "error");
  }
}

// ============================================================
// ABRIR MODAL GENÉRICO PARA ANULAR COMPROBANTE (FACTURA O BOLETA)
// ============================================================
function abrirModalAnularComprobante(id, serie, correlativo, tipo) {
  // tipo = '01' (Factura) o '03' (Boleta)
  let titulo = tipo == "01" ? "Factura" : "Boleta";
  let advertencia =
    tipo == "03"
      ? '<small class="text-warning">⚠️ Solo se pueden anular boletas con máximo 7 días de emisión</small>'
      : '<small class="text-info">ℹ️ Las facturas se pueden anular sin límite de tiempo</small>';

  Swal.fire({
    title: `Anular ${titulo}`,
    html: `
            <div class="text-left">
                <p><strong>Comprobante:</strong> ${serie}-${correlativo}</p>
                ${advertencia}
                <div class="form-group mt-3">
                    <label>Motivo de Anulación: <span class="text-danger">*</span></label>
                    <textarea id="swal_motivo_anulacion" class="form-control" rows="3" 
                              placeholder="Ingrese el motivo de la anulación" required></textarea>
                </div>
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    Esta acción comunicará la anulación a SUNAT mediante una 
                    <strong>${
                      tipo == "01"
                        ? "Comunicación de Baja (RA)"
                        : "Resumen de Reversiones (RC)"
                    }</strong>
                </div>
            </div>
        `,
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-check"></i> Sí, anular',
    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    width: "600px",
    preConfirm: () => {
      const motivo = document
        .getElementById("swal_motivo_anulacion")
        .value.trim();
      if (!motivo) {
        Swal.showValidationMessage("Debe ingresar el motivo de anulación");
        return false;
      }
      return motivo;
    },
  }).then((result) => {
    if (result.isConfirmed) {
      confirmarAnulacionComprobante(id, serie, correlativo, tipo, result.value);
    }
  });
}

// ============================================================
// CONFIRMAR ANULACIÓN Y COMUNICAR A SUNAT (GENÉRICO)
// ============================================================
function confirmarAnulacionComprobante(id, serie, correlativo, tipo, motivo) {
  let usuario = $("#txtprincipalid").val();
  let tipoTexto = tipo == "01" ? "Factura" : "Boleta";

  Swal.fire({
    title: `Anulando ${tipoTexto}...`,
    html: `Procesando anulación de <b>${serie}-${correlativo}</b><br>Se comunicará a SUNAT`,
    allowOutsideClick: false,
    showConfirmButton: false,
    willOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "../controller/comprobante/controller_comprobante.php",
    type: "POST",
    data: {
      accion: "ANULAR_COMPROBANTE_SUNAT",
      id_comprobante: id,
      motivo: motivo,
      usuario: usuario,
      tipo_comprobante: tipo,
    },
    dataType: "json",
  })
    .done(function (resp) {
      Swal.close();

      if (resp.status == "success") {
        Swal.fire({
          icon: "success",
          title: `${tipoTexto} procesada correctamente`,
          html: `
                    <div class="text-left">
                        <p><strong>Comprobante:</strong> ${resp.comprobante}</p>
                        <p><strong>Correlativo Baja:</strong> ${
                          resp.correlativo_baja
                        }</p>
                        ${
                          resp.ticket
                            ? `<p><strong>Ticket SUNAT:</strong> ${resp.ticket}</p>`
                            : ""
                        }
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> ${resp.message}
                        </div>
                        ${
                          resp.ticket
                            ? `
                            <div class="alert alert-warning">
                                <strong>⚠️ Importante:</strong> Debes consultar el ticket después para confirmar 
                                que SUNAT procesó la anulación correctamente.
                            </div>
                        `
                            : ""
                        }
                    </div>
                `,
          confirmButtonText: "Aceptar",
          width: "600px",
        }).then(() => {
          if (typeof tbl_comprobantes !== "undefined") {
            tbl_comprobantes.ajax.reload();
          }
        });
      } else {
        Swal.fire({
          icon: "error",
          title: `Error al anular ${tipoTexto}`,
          html: `
                    <div class="text-left">
                        <p>${resp.message}</p>
                        ${
                          resp.output
                            ? `
                            <div class="mt-3">
                                <strong>Detalle técnico:</strong>
                                <pre style="max-height: 200px; overflow-y: auto; background: #f5f5f5; padding: 10px; border-radius: 5px;">${resp.output}</pre>
                            </div>
                        `
                            : ""
                        }
                    </div>
                `,
          confirmButtonText: "Cerrar",
          width: "700px",
        });
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      Swal.close();
      Swal.fire({
        icon: "error",
        title: "Error de conexión",
        html: `
                <p>No se pudo comunicar con el servidor</p>
                <small>Error: ${textStatus} - ${errorThrown}</small>
            `,
        confirmButtonText: "Cerrar",
      });
    });
}
function consultarTicketAnulacion(id_comprobante) {
  Swal.fire({
    title: "Consultando ticket...",
    html: "Verificando estado en SUNAT",
    allowOutsideClick: false,
    showConfirmButton: false,
    willOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "../controller/comprobante/controller_comprobante.php",
    type: "POST",
    data: {
      accion: "CONSULTAR_TICKET_ANULACION",
      id_comprobante: id_comprobante,
    },
    dataType: "json",
  })
    .done(function (resp) {
      Swal.close();

      if (resp.status == "success") {
        Swal.fire({
          icon: "success",
          title: "Estado del Ticket",
          html: `
                    <div class="text-left">
                        <p><strong>Ticket:</strong> ${resp.ticket}</p>
                        <p><strong>Estado:</strong> <span class="badge badge-success">${
                          resp.estado
                        }</span></p>
                        <p><strong>Descripción:</strong> ${resp.descripcion}</p>
                        ${
                          resp.comprobante_anulado
                            ? `
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-check-circle"></i> El comprobante ha sido anulado correctamente
                            </div>
                        `
                            : ""
                        }
                    </div>
                `,
          width: "600px",
        }).then(() => {
          if (typeof tbl_comprobantes !== "undefined") {
            tbl_comprobantes.ajax.reload();
          }
        });
      } else if (resp.status == "pending") {
        Swal.fire({
          icon: "info",
          title: "Ticket en proceso",
          html: resp.message,
          confirmButtonText: "Entendido",
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error al consultar ticket",
          html: resp.message,
          confirmButtonText: "Cerrar",
        });
      }
    })
    .fail(function () {
      Swal.close();
      Swal.fire("Error", "Error al comunicarse con el servidor", "error");
    });
}

// ============================================================
// COMPATIBILIDAD CON MODAL ANTIGUO DE BOLETAS
// ============================================================
function abrirModalAnularBoleta(id, serie, correlativo) {
  abrirModalAnularComprobante(id, serie, correlativo, "03");
}

function confirmarAnulacionBoleta() {
  let id = $("#txt_id_comprobante_anular_boleta").val();
  let motivo = $("#txt_motivo_anulacion_boleta").val().trim();
  let serie = $("#txt_serie_anular_boleta").val();
  let correlativo = $("#txt_correlativo_anular_boleta").val();

  if (!motivo) {
    return Swal.fire(
      "Advertencia",
      "Debe ingresar el motivo de anulación",
      "warning"
    );
  }

  $("#modal_anular_boleta").modal("hide");
  confirmarAnulacionComprobante(id, serie, correlativo, "03", motivo);
}

// ============================================================
// EDITAR COMPROBANTE (SOLO PENDIENTES)
// ============================================================
function editarComprobante(id) {
  Swal.fire({
    title: "Cargando datos...",
    text: "Espere un momento",
    allowOutsideClick: false,
    showConfirmButton: false,
    willOpen: () => {
      Swal.showLoading();
    },
  });

  // Obtener datos del comprobante
  $.ajax({
    url: "../controller/comprobante/controller_comprobante.php",
    type: "POST",
    data: {
      accion: "OBTENER_COMPROBANTE_EDITAR",
      id_comprobante: id,
    },
    dataType: "json",
  })
    .done(function (data) {
      Swal.close();

      if (data && data.id_comprobante) {
        // Verificar que sea PENDIENTE
        if (data.estado_sunat !== "PENDIENTE") {
          return Swal.fire(
            "Advertencia",
            "Solo se pueden editar comprobantes PENDIENTES",
            "warning"
          );
        }

        // Guardar datos temporalmente
        window.datosComprobanteEditar = data;

        // Abrir modal (esto dispara el evento shown.bs.modal)
        $("#modal_editar_comprobante").modal("show");
      } else {
        Swal.fire(
          "Error",
          "No se pudo obtener los datos del comprobante",
          "error"
        );
      }
    })
    .fail(function () {
      Swal.close();
      Swal.fire("Error", "Error al consultar el comprobante", "error");
    });
}

// ============================================================
// EVENTO CUANDO SE ABRE EL MODAL
// ============================================================
$("#modal_editar_comprobante").on("shown.bs.modal", function () {
  console.log("🔵 Modal abierto, iniciando carga de selects...");

  // 1️⃣ Cargar todos los selects
  Promise.all([
    Cargar_Select_Servicios_Edit(),
    Cargar_Select_Conductores_Edit(),
    Cargar_Select_Rutas_Edit(),
    Cargar_Select_Tipopago_Edit(),
  ]).then(() => {
    console.log("✅ Todos los selects cargados");

    // 2️⃣ Inicializar Select2 en los selects del modal
    $("#edit_servicio").select2({
      dropdownParent: $("#modal_editar_comprobante"),
      width: "100%",
    });

    $("#edit_conductor").select2({
      dropdownParent: $("#modal_editar_comprobante"),
      width: "100%",
    });

    $("#edit_origen").select2({
      dropdownParent: $("#modal_editar_comprobante"),
      width: "100%",
    });

    $("#edit_destino").select2({
      dropdownParent: $("#modal_editar_comprobante"),
      width: "100%",
    });

    $("#edit_tipo_pago").select2({
      dropdownParent: $("#modal_editar_comprobante"),
      width: "100%",
    });

    // 3️⃣ Esperar un poco para que Select2 se renderice
    setTimeout(() => {
      if (window.datosComprobanteEditar) {
        console.log("📦 Datos a cargar:", window.datosComprobanteEditar);
        llenarFormularioEditar(window.datosComprobanteEditar);
        delete window.datosComprobanteEditar;
      }
    }, 300);
  });
});
$("#modal_editar_comprobante").on("hidden.bs.modal", function () {
  // Destruir Select2 para evitar duplicados
  $("#edit_servicio").select2("destroy");
  $("#edit_conductor").select2("destroy");
  $("#edit_origen").select2("destroy");
  $("#edit_destino").select2("destroy");
  $("#edit_tipo_pago").select2("destroy");
});

// ============================================================
// LLENAR FORMULARIO DE EDICIÓN
// ============================================================
function llenarFormularioEditar(data) {
  console.log("🔍 Iniciando llenado de formulario con datos:", data);

  // Guardar ID del comprobante
  $("#txt_id_comprobante_editar").val(data.id_comprobante);

  // Llenar datos del comprobante
  $("#edit_tipo_comprobante").val(data.tipo_comprobante);
  $("#edit_serie").val(data.serie);
  $("#edit_correlativo").val(data.correlativo);
  $("#edit_fecha_emision").val(data.fecha_emision);
  $("#edit_moneda").val(data.moneda);

  // Datos del cliente
  $("#edit_tipo_documento_cliente").val(data.tipo_documento_cliente);
  $("#edit_numero_documento").val(data.numero_documento);
  $("#edit_razon_social").val(data.razon_social);
  $("#edit_direccion").val(data.direccion);
  $("#edit_telefono").val(data.celular || "");
  $("#edit_departamento").val(data.departamento);
  $("#edit_provincia").val(data.provincia);
  $("#edit_distrito").val(data.distrito);

  // ✅ Datos del servicio (CON SELECT2)
  if (data.id_servicio) {
    $("#edit_servicio").val(data.id_servicio).trigger("change");
  }

  if (data.idconductor) {
    $("#edit_conductor").val(data.idconductor).trigger("change");
  }

  if (data.id_origen) {
    $("#edit_origen").val(data.id_origen).trigger("change");
  }

  if (data.iddestino) {
    $("#edit_destino").val(data.iddestino).trigger("change");
  }

  if (data.id_tipo_pago) {
    $("#edit_tipo_pago").val(data.id_tipo_pago).trigger("change");
  }

  $("#edit_cantidad").val(data.cantidad);
  $("#edit_fecha_viaje").val(data.fecha_viaje);

  // Montos
  $("#edit_base_gravada").val(parseFloat(data.total_gravada).toFixed(2));
  $("#edit_igv").val(parseFloat(data.total_igv).toFixed(2));
  $("#edit_total").val(parseFloat(data.total).toFixed(2));

  $("#edit_observaciones").val(data.observaciones || "");

  // Verificación final
  console.log("✅ Valores asignados:");
  console.log("  Servicio seleccionado:", $("#edit_servicio").val());
  console.log("  Conductor seleccionado:", $("#edit_conductor").val());
  console.log("  Origen seleccionado:", $("#edit_origen").val());
  console.log("  Destino seleccionado:", $("#edit_destino").val());
  console.log("  Tipo Pago seleccionado:", $("#edit_tipo_pago").val());
}

// ============================================================
// CARGAR SELECTS PARA EDICIÓN (con Promise)
// ============================================================
function Cargar_Select_Servicios_Edit() {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: "../controller/servicios/controlador_cargar_select_servicios.php",
      type: "POST",
    })
      .done(function (resp) {
        let data = JSON.parse(resp);
        let cadena = "<option value=''>Seleccionar servicio</option>";
        if (data.length > 0) {
          for (let i = 0; i < data.length; i++) {
            cadena +=
              "<option value='" + data[i][0] + "'>" + data[i][1] + "</option>";
          }
        }
        $("#edit_servicio").html(cadena);
        resolve();
      })
      .fail(reject);
  });
}

function Cargar_Select_Conductores_Edit() {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: "../controller/choferes/controlador_cargar_select_choferes.php",
      type: "POST",
    })
      .done(function (resp) {
        let data = JSON.parse(resp);
        let cadena = "<option value=''>Seleccionar conductor</option>";
        if (data.length > 0) {
          for (let i = 0; i < data.length; i++) {
            cadena +=
              "<option value='" +
              data[i][0] +
              "'>DNI: " +
              data[i][1] +
              " - " +
              data[i][2] +
              "</option>";
          }
        }
        $("#edit_conductor").html(cadena);
        resolve();
      })
      .fail(reject);
  });
}

function Cargar_Select_Rutas_Edit() {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: "../controller/rutas/controlador_cargar_select_rutas.php",
      type: "POST",
    })
      .done(function (resp) {
        let data = JSON.parse(resp);
        let cadena = "<option value=''>Seleccionar ruta</option>";
        if (data.length > 0) {
          for (let i = 0; i < data.length; i++) {
            cadena +=
              "<option value='" + data[i][0] + "'>" + data[i][1] + "</option>";
          }
        }
        $("#edit_origen").html(cadena);
        $("#edit_destino").html(cadena);
        resolve();
      })
      .fail(reject);
  });
}

function Cargar_Select_Tipopago_Edit() {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: "../controller/tipo_pago/controlador_cargar_select_tipo_pago.php",
      type: "POST",
    })
      .done(function (resp) {
        let data = JSON.parse(resp);
        let cadena = "<option value=''>Seleccionar tipo pago</option>";
        if (data.length > 0) {
          for (let i = 0; i < data.length; i++) {
            cadena += `<option value="${data[i][0]}">${data[i][1]}</option>`;
          }
        }
        $("#edit_tipo_pago").html(cadena);
        resolve();
      })
      .fail(reject);
  });
}

// ============================================================
// ACTUALIZAR COMPROBANTE
// ============================================================
function actualizarComprobante() {
  let id_comprobante = $("#txt_id_comprobante_editar").val();
  let tipo_comprobante = $("#edit_tipo_comprobante").val();
  let serie = $("#edit_serie").val();
  let correlativo = $("#edit_correlativo").val();
  let fecha_emision = $("#edit_fecha_emision").val();
  let moneda = $("#edit_moneda").val();
  let tipo_documento_cliente = $("#edit_tipo_documento_cliente").val();
  let numero_documento = $("#edit_numero_documento").val();
  let razon_social = $("#edit_razon_social").val();
  let direccion = $("#edit_direccion").val();
  let celular = $("#edit_telefono").val() || "";
  let departamento = $("#edit_departamento").val();
  let provincia = $("#edit_provincia").val();
  let distrito = $("#edit_distrito").val();
  let id_servicio = $("#edit_servicio").val();
  let cantidad = $("#edit_cantidad").val();
  let id_conductor = $("#edit_conductor").val();
  let id_origen = $("#edit_origen").val();
  let id_destino = $("#edit_destino").val();
  let fecha_viaje = $("#edit_fecha_viaje").val();
  let base_gravada = $("#edit_base_gravada").val();
  let igv = $("#edit_igv").val();
  let total = $("#edit_total").val();
  let id_tipo_pago = $("#edit_tipo_pago").val();
  let observaciones = $("#edit_observaciones").val() || "";
  let id_usuario = $("#txtprincipalid").val();

  // Validaciones
  if (!tipo_comprobante || !serie || !correlativo || !fecha_emision) {
    return Swal.fire(
      "Advertencia",
      "Complete los datos del comprobante",
      "warning"
    );
  }
  if (!tipo_documento_cliente || !numero_documento || !razon_social) {
    return Swal.fire(
      "Advertencia",
      "Complete los datos del cliente",
      "warning"
    );
  }
  if (
    !id_servicio ||
    !id_conductor ||
    !id_origen ||
    !id_destino ||
    !fecha_viaje
  ) {
    return Swal.fire(
      "Advertencia",
      "Complete los datos del servicio",
      "warning"
    );
  }
  if (!base_gravada || !total || base_gravada <= 0 || total <= 0) {
    return Swal.fire(
      "Advertencia",
      "Los montos deben ser mayores a 0",
      "warning"
    );
  }

  // Confirmar actualización
  Swal.fire({
    title: "¿Confirmar actualización?",
    text: "Se actualizarán los datos del comprobante",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, actualizar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Actualizando...",
        text: "Procesando cambios",
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
          Swal.showLoading();
        },
      });

      $.ajax({
        url: "../controller/comprobante/controller_comprobante.php",
        type: "POST",
        data: {
          accion: "ACTUALIZAR_COMPROBANTE",
          id_comprobante: id_comprobante,
          tipo_comprobante: tipo_comprobante,
          serie: serie,
          correlativo: correlativo,
          fecha_emision: fecha_emision,
          moneda: moneda,
          tipo_documento_cliente: tipo_documento_cliente,
          numero_documento: numero_documento,
          razon_social: razon_social,
          direccion: direccion,
          celular: celular,
          departamento: departamento,
          provincia: provincia,
          distrito: distrito,
          id_servicio: id_servicio,
          cantidad: cantidad,
          id_conductor: id_conductor,
          id_origen: id_origen,
          id_destino: id_destino,
          fecha_viaje: fecha_viaje,
          base_gravada: base_gravada,
          igv: igv,
          total: total,
          id_tipo_pago: id_tipo_pago,
          observaciones: observaciones,
          id_usuario: id_usuario,
        },
        dataType: "json",
      })
        .done(function (response) {
          Swal.close();

          if (response.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Comprobante actualizado",
              text: "Los cambios se guardaron correctamente",
              showConfirmButton: true,
            }).then(() => {
              $("#modal_editar_comprobante").modal("hide");
              tbl_comprobantes.ajax.reload();
            });
          } else {
            Swal.fire(
              "Error",
              response.message || "No se pudo actualizar",
              "error"
            );
          }
        })
        .fail(function (xhr) {
          Swal.close();
          console.error("Error AJAX:", xhr.responseText);
          Swal.fire("Error", "Error al actualizar el comprobante", "error");
        });
    }
  });
}

// Recalcular totales en modal de edición
$(document).on("change", "#edit_servicio", function () {
  let id = $(this).val();
  if (id !== "") {
    TraerprecioEditar(id);
  }
});

// Recalcular totales en modal de edición
$(document).on("change", "#edit_servicio", function () {
  let id = $(this).val();
  if (id !== "" && id !== null) {
    TraerprecioEditar(id);
  }
});

$(document).on("change", "#edit_cantidad", function () {
  calcularDesdeTotalEditar();
});

// Recalcular cuando cambie el servicio (Select2)
$(document).on("select2:select", "#edit_servicio", function (e) {
  let id = e.params.data.id;
  if (id !== "" && id !== null) {
    TraerprecioEditar(id);
  }
});

// Recalcular cuando cambie la cantidad o el total
$(document).on("change input", "#edit_cantidad, #edit_total", function () {
  calcularDesdeTotalEditar();
});

function calcularDesdeTotalEditar() {
  // Obtener el precio UNITARIO con IGV
  var precioUnitarioConIGV = parseFloat($("#edit_total").val()) || 0;
  var cantidad = parseFloat($("#edit_cantidad").val()) || 0;

  // Validar cantidad mínima
  if (cantidad === 0) cantidad = 1;

  // ✅ PASO 1: Calcular base gravada UNITARIA (sin IGV)
  var baseGravadaUnitaria = precioUnitarioConIGV / 1.18;

  // ✅ PASO 2: Multiplicar por la cantidad
  var baseGravadaTotal = baseGravadaUnitaria * cantidad;

  // ✅ PASO 3: Calcular IGV (18% de la base gravada total)
  var igvTotal = baseGravadaTotal * 0.18;

  // ✅ PASO 4: Calcular total general
  var totalGeneral = precioUnitarioConIGV * cantidad;

  // Actualizar campos con 2 decimales
  $("#edit_base_gravada").val(baseGravadaTotal.toFixed(2));
  $("#edit_igv").val(igvTotal.toFixed(2));
  $("#edit_total").val(totalGeneral.toFixed(2));
}

// ============================================================
// TRAER PRECIO DEL SERVICIO (EDICIÓN)
// ============================================================
function TraerprecioEditar(id) {
  $.ajax({
    url: "../controller/servicios/controlador_traermonto.php",
    type: "POST",
    data: { id: id },
  }).done(function (resp) {
    try {
      var data = JSON.parse(resp);
      if (data.length > 0) {
        let total = data[0].monto || data[0][1];
        $("#edit_total").val(total);
        calcularDesdeTotalEditar();
      }
    } catch (error) {
      console.error("Error al parsear JSON:", resp);
    }
  });
}
// ============================================================
// SOLUCIÓN UNIVERSAL PARA DROPDOWNS EN DATATABLES
// ============================================================
