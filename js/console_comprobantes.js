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

// 3️⃣ Traer precio desde el backend y calcular totales
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
          let monto = data[0].monto || data[0][1];
          $("#txt_base_gravada").val(monto);
          calcularTotales(); // 👈 Se calcula automáticamente al traer el precio
        } else {
          $("#txt_base_gravada").val("");
          $("#txt_igv").val("");
          $("#txt_total").val("");
        }
      } catch (error) {
        console.error("Error al parsear JSON:", resp);
        $("#txt_base_gravada").val("");
        $("#txt_igv").val("");
        $("#txt_total").val("");
      }
    })
    .fail(function () {
      console.error("Error al traer el precio del servicio.");
    });
}

// 4️⃣ Función para calcular IGV y total considerando la cantidad
function calcularTotales() {
  var baseGravada =
    parseFloat(document.getElementById("txt_base_gravada").value) || 0;
  var cantidad = parseFloat(document.getElementById("txt_cantidad").value) || 0;

  // Subtotal = precio unitario * cantidad
  var subtotal = baseGravada * cantidad;

  // Cálculo del IGV (18%)
  var igv = subtotal * 0.18;

  // Total general
  var total = subtotal + igv;

  // Mostrar valores con 2 decimales
  document.getElementById("txt_igv").value = igv.toFixed(2);
  document.getElementById("txt_total").value = total.toFixed(2);
}

// LIMPIAR TOTALES
function limpiarTotales() {
  $("#txt_base_gravada").val("");
  $("#txt_igv").val("");
  $("#txt_total").val("");
  $("#span_subtotal_serv").text("S/ 0.00");
}

// OBTENER CORRELATIVO AUTOMÁTICO
function obtenerCorrelativo() {
  let serie = $("#txt_serie").val();
  let tipo = $("#select_tipo_comprobante").val();

  if (!serie || !tipo) return;

  $.ajax({
    url: "../controller/comprobante/controller_comprobante.php",
    type: "POST",
    data: {
      accion: "OBTENER_CORRELATIVO",
      serie: serie,
      tipo_comprobante: tipo,
    },
  }).done(function (resp) {
    let data = JSON.parse(resp);
    if (data.correlativo) {
      $("#txt_correlativo").val(data.correlativo);
    }
  });
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

// GUARDAR COMPROBANTE
// ======================================================
// GUARDAR COMPROBANTE - CORREGIDO Y COMPLETO
// ======================================================
function guardarComprobante(estadoSunat) {
  // 1️⃣ CAPTURA DE DATOS DEL FORMULARIO
  let tipo_comprobante = $("#select_tipo_comprobante").val();
  let serie = $("#txt_serie").val();
  let correlativo = $("#txt_correlativo").val();
  let fecha_emision = $("#txt_fecha_emision").val() || ""; // se envía vacío si no selecciona
  let moneda = $("#select_moneda").val();
  let tipo_documento_cliente = $("#select_tipo_documento_cliente").val();
  let numero_documento = $("#txt_numero_documento").val();
  let razon_social = $("#txt_razon_social").val();
  let direccion = $("#txt_direccion").val();
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

  // 3️⃣ CONSTRUIR OBJETO formData
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
    estado_sunat: estadoSunat,
    id_usuario,
  };

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
      // 5️⃣ ENVÍO AJAX
      $.ajax({
        url: "../controller/comprobante/controller_comprobante.php",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function (response) {
          console.log("Respuesta del servidor:", response);

          if (typeof response === "string") {
            try {
              response = JSON.parse(response);
            } catch (e) {
              console.error("No es JSON válido:", response);
              return Swal.fire(
                "Error",
                "Respuesta inválida del servidor",
                "error"
              );
            }
          }

          // ✅ Solo mostrar éxito (sin imprimir)
          if (response.status === "success") {
            Swal.fire({
              title: "✅ Comprobante guardado correctamente",
              text: "El comprobante se ha registrado localmente. Puede enviarlo a SUNAT más adelante.",
              icon: "success",
              confirmButtonText: "Aceptar",
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
          console.error("Error AJAX:", xhr.responseText);
          Swal.fire("Error", "No se pudo registrar el comprobante", "error");
        },
      });
    }
  });
}

function limpiarFormulario() {
  document.getElementById("select_tipo_comprobante").value = "";
  document.getElementById("txt_serie").value = "";
  document.getElementById("txt_correlativo").value = "";
  document.getElementById("txt_cantidad").value = "";
  document.getElementById("select_tipo_documento_cliente").value = "";
  document.getElementById("txt_numero_documento").value = "";
  document.getElementById("txt_razon_social").value = "";
  document.getElementById("select_conductor").value = "";
  document.getElementById("txt_base_gravada").value = "";
  document.getElementById("txt_igv").value = "";
  document.getElementById("txt_total").value = "";
  document.getElementById("select_tipo_pago").value = "";
  Cargar_Select_Conductores();
  Cargar_Select_Tipopago();
  Cargar_Select_Servicios();
  Cargar_Select_Rutas();
}

// GUARDAR Y ENVIAR A SUNAT
function guardarYEnviar() {
  // Primero guardar como PENDIENTE
  guardarComprobanteYEnviar();
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
  })
    .done(function (resp) {
      if (resp.status == "success") {
        // Ahora enviar a SUNAT
        Swal.update({
          html: "Paso 2/2: Enviando a SUNAT...",
        });

        enviarASunat(resp.id_comprobante, resp.serie, resp.correlativo);
      } else {
        Swal.close();
        Swal.fire("Error", resp.message, "error");
      }
    })
    .fail(function () {
      Swal.close();
      Swal.fire("Error", "Error al guardar el comprobante", "error");
    });
}

// ENVIAR A SUNAT

function enviarASunat(id_comprobante, serie, correlativo) {
  $.ajax({
    url: "../controller/comprobante/controller_comprobante.php",
    type: "POST",
    data: {
      accion: "ENVIAR_SUNAT",
      id_comprobante: id_comprobante,
    },
    dataType: "json",
  })
    .done(function (resp) {
      Swal.close();

      if (resp.status == "success") {
        // 🧾 Abrir ticket en una ventanita pequeña adelante de la principal
        const popupWidth = 480;
        const popupHeight = 700;
        const left = (screen.width - popupWidth) / 2;
        const top = (screen.height - popupHeight) / 2;

        window.open(
          "../view/MPDF/REPORTE/ticket_comprobante.php?id=" + id_comprobante,
          "TicketSUNAT",
          `width=${popupWidth},height=${popupHeight},top=${top},left=${left},resizable=yes,scrollbars=yes,status=no`
        );

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
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error al enviar a SUNAT",
          html: resp.message + "<br><small>" + (resp.output || "") + "</small>",
          showConfirmButton: true,
        });
      }
    })
    .fail(function () {
      Swal.close();
      Swal.fire("Error", "Error al comunicarse con SUNAT", "error");
    });
}

// LIMPIAR FORMULARIO
function limpiarFormulario() {
  $("#select_tipo_comprobante").val("").trigger("change");
  $("#txt_serie").val("");
  $("#txt_correlativo").val("");
  $("#select_tipo_documento_cliente").val("").trigger("change");
  $("#txt_numero_documento").val("");
  $("#txt_razon_social").val("");
  $("#txt_direccion").val("");
  $("#select_origen").val("").trigger("change");
  $("#select_destino").val("").trigger("change");
  $("#txt_fecha_viaje").val("");
  $("#txt_asiento").val("");
  $("#txt_placa").val("");
  $("#txt_base_gravada").val("");
  $("#txt_igv").val("");
  $("#txt_total").val("");
  $("#txt_observaciones").val("");

  // Establecer fecha actual
  var hoy = new Date().toISOString().split("T")[0];
  $("#txt_fecha_emision").val(hoy);
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

// ============================================================
// LISTAR TODOS LOS COMPROBANTES CON EXPORTACIÓN
// ============================================================
function listar_comprobantes() {
  tbl_comprobantes = $("#tabla_comprobantes").DataTable({
    ordering: true,
    order: [[3, 'desc']], // 👈 ORDENAR por la columna "Fecha Emisión" descendente
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
      data: { accion: "LISTAR_COMPROBANTES" },
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
        data: "fecha_emision",
        render: function (data) {
          if (!data) return "-";
          const partes = data.split("-");
          return `${partes[2]}/${partes[1]}/${partes[0]}`; // formato dd/mm/yyyy
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

        if (estado == "PENDIENTE" && estado_doc == "ACTIVO") {
            botones += `
                <a class="dropdown-item" href="javascript:void(0)" onclick="abrirModalEnviar(${data.id_comprobante}, '${data.serie}', '${data.correlativo}')">
                    <i class="fas fa-paper-plane text-success"></i> Enviar a SUNAT
                </a>`;
        }

        if ((estado == "ENVIADO" || estado == "ACEPTADO") && estado_doc == "ACTIVO") {
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

        // ✅ BOTÓN ANULAR BOLETA (solo para tipo 03 - BOLETA)
        if (tipo == "03" && (estado == "ENVIADO" || estado == "ACEPTADO") && estado_doc == "ACTIVO") {
            botones += `
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="abrirModalAnularBoleta(${data.id_comprobante}, '${data.serie}', '${data.correlativo}')">
                    <i class="fas fa-file-invoice"></i> Anular Boleta (Comunicar SUNAT)
                </a>`;
        }

        // ANULAR LOCAL (para cualquier comprobante pendiente)
        if ((estado == "PENDIENTE" || estado == "ENVIADO" || estado == "ACEPTADO") && estado_doc == "ACTIVO") {
            botones += `
                <a class="dropdown-item text-warning" href="javascript:void(0)" onclick="abrirModalAnular(${data.id_comprobante})">
                    <i class="fas fa-ban"></i> Anular Comprobante (Local)
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
// LISTAR CON FILTROS Y EXPORTACIÓN
// ============================================================
function listar_comprobantes_filtro() {
  let estado = $("#select_estado_filtro").val();
  let fecha_desde = $("#txt_fecha_desde").val();
  let fecha_hasta = $("#txt_fecha_hasta").val();

  if (tbl_comprobantes) tbl_comprobantes.destroy();

  tbl_comprobantes = $("#tabla_comprobantes").DataTable({
    ordering: true,
    order: [[3, 'desc']], // 👈 ORDENAR por la columna "Fecha Emisión" descendente
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
        data: "fecha_emision",
        render: function (data) {
          if (!data) return "-";
          const partes = data.split("-");
          return `${partes[2]}/${partes[1]}/${partes[0]}`; // formato dd/mm/yyyy
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

        if (estado == "PENDIENTE" && estado_doc == "ACTIVO") {
            botones += `
                <a class="dropdown-item" href="javascript:void(0)" onclick="abrirModalEnviar(${data.id_comprobante}, '${data.serie}', '${data.correlativo}')">
                    <i class="fas fa-paper-plane text-success"></i> Enviar a SUNAT
                </a>`;
        }

        if ((estado == "ENVIADO" || estado == "ACEPTADO") && estado_doc == "ACTIVO") {
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

        // ✅ BOTÓN ANULAR BOLETA (solo para tipo 03 - BOLETA)
        if (tipo == "03" && (estado == "ENVIADO" || estado == "ACEPTADO") && estado_doc == "ACTIVO") {
            botones += `
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="abrirModalAnularBoleta(${data.id_comprobante}, '${data.serie}', '${data.correlativo}')">
                    <i class="fas fa-file-invoice"></i> Anular Boleta (Comunicar SUNAT)
                </a>`;
        }

        // ANULAR LOCAL (para cualquier comprobante pendiente)
        if ((estado == "PENDIENTE" || estado == "ENVIADO" || estado == "ACEPTADO") && estado_doc == "ACTIVO") {
            botones += `
                <a class="dropdown-item text-warning" href="javascript:void(0)" onclick="abrirModalAnular(${data.id_comprobante})">
                    <i class="fas fa-ban"></i> Anular Comprobante (Local)
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
  let url = "../greenter/xml/" + serie + "-" + correlativo + ".xml";
  window.open(url, "_blank");
}

// ============================================================
// DESCARGAR CDR
// ============================================================
function descargarCDR(serie, correlativo) {
  let url = "../greenter/cdr/R-" + serie + "-" + correlativo + ".zip";
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
// ABRIR MODAL ANULAR BOLETA (COMUNICAR A SUNAT)
// ============================================================
function abrirModalAnularBoleta(id, serie, correlativo) {
    $("#txt_id_comprobante_anular_boleta").val(id);
    $("#txt_serie_anular_boleta").val(serie);
    $("#txt_correlativo_anular_boleta").val(correlativo);
    $("#span_numero_anular_boleta").text(serie + "-" + correlativo);
    $("#txt_motivo_anulacion_boleta").val("");
    $("#modal_anular_boleta").modal("show");
}

// ============================================================
// CONFIRMAR ANULACIÓN DE BOLETA Y COMUNICAR A SUNAT
// ============================================================
function confirmarAnulacionBoleta() {
    let id = $("#txt_id_comprobante_anular_boleta").val();
    let motivo = $("#txt_motivo_anulacion_boleta").val().trim();
    let serie = $("#txt_serie_anular_boleta").val();
    let correlativo = $("#txt_correlativo_anular_boleta").val();
    let usuario = $("#txtprincipalid").val();

    if (!motivo) {
        return Swal.fire("Advertencia", "Debe ingresar el motivo de anulación", "warning");
    }

    $("#modal_anular_boleta").modal("hide");

    Swal.fire({
        title: "Anulando boleta...",
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
            accion: "ANULAR_BOLETA_SUNAT",
            id_comprobante: id,
            motivo: motivo,
            usuario: usuario,
        },
        dataType: "json",
    })
    .done(function (resp) {
        Swal.close();

        if (resp.status == "success") {
            Swal.fire({
                icon: "success",
                title: "Boleta anulada correctamente",
                html: `<b>${serie}-${correlativo}</b> fue anulada y comunicada a SUNAT`,
                showConfirmButton: true,
            }).then(() => {
                tbl_comprobantes.ajax.reload();
            });
        } else {
            Swal.fire({
                icon: "error",
                title: "Error al anular boleta",
                html: resp.message + "<br><small>" + (resp.output || "") + "</small>",
                showConfirmButton: true,
            });
        }
    })
    .fail(function () {
        Swal.close();
        Swal.fire("Error", "Error al comunicarse con el servidor", "error");
    });
}
