// ============================================================
// ENCOMIENDAS - funciones comunes
// Funciones comunes a todos los roles. Cada vista carga este archivo antes del
// JS de su rol; si un rol necesita algo distinto, declara su propia versión
// en su archivo y esa reemplaza a la de aquí.
// ============================================================

//CARGAR SELECT CONDUCTORES
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
    $("#select_conductor_editar").html(cadena);
  });
}

async function buscarPorDocumento() {
  const tipo = document.getElementById("select_tipo_documento_emisor").value;
  const dni = document.getElementById("txt_dni_emisor").value.trim();
  const otroDoc = document.getElementById("txt_dni_emisor2").value.trim();

  let numero_documento = "";

  if (tipo === "DNI" && dni !== "") {
    numero_documento = dni;
  } else if (tipo !== "DNI" && otroDoc !== "") {
    numero_documento = otroDoc;
  } else {
    Swal.fire(
      "Advertencia",
      "Debe ingresar un número de documento válido.",
      "warning"
    );
    return;
  }

  try {
    const resp = await $.ajax({
      url: "../controller/encomiendas/controlador_buscar_persona_por_documento.php",
      type: "POST",
      data: { numero_documento },
      dataType: "json",
    });

    if (resp.data && resp.data.length > 0) {
      const d = resp.data[0];

      // Rellenar campos
      $("#txt_nomb_emisor").val(d.nombre_completo);
      $("#txt_celu1_emisor").val(d.celular);
    } else {
      Swal.fire(
        "No encontrado",
        "No se encontró ninguna persona con ese documento.",
        "info"
      );
    }
  } catch (error) {
    console.error("❌ Error en AJAX:", error);
    Swal.fire("Error", "No se pudo hacer la búsqueda.", "error");
  }
}

async function buscarPorDocumento2() {
  const tipo = document.getElementById("select_tipo_documento_receptor").value;
  const dni = document.getElementById("txt_dni_receptor").value.trim();
  const otroDoc = document.getElementById("txt_dni_recepto2").value.trim();

  let numero_documento = "";

  if (tipo === "DNI" && dni !== "") {
    numero_documento = dni;
  } else if (tipo !== "DNI" && otroDoc !== "") {
    numero_documento = otroDoc;
  } else {
    Swal.fire(
      "Advertencia",
      "Debe ingresar un número de documento válido.",
      "warning"
    );
    return;
  }

  try {
    const resp = await $.ajax({
      url: "../controller/encomiendas/controlador_buscar_persona_por_documento.php",
      type: "POST",
      data: { numero_documento },
      dataType: "json",
    });

    if (resp.data && resp.data.length > 0) {
      const d = resp.data[0];

      // Rellenar campos
      $("#txt_nomb_receptor").val(d.nombre_completo);
      $("#txt_celu1_recepto").val(d.celular);
    } else {
      Swal.fire(
        "No encontrado",
        "No se encontró ninguna persona con ese documento.",
        "info"
      );
    }
  } catch (error) {
    console.error("❌ Error en AJAX:", error);
    Swal.fire("Error", "No se pudo hacer la búsqueda.", "error");
  }
}

async function buscarPorDocumentoEditar() {
  const tipo = document.getElementById(
    "select_tipo_documento_emisor_editar"
  ).value;
  const dni = document.getElementById("txt_dni_emisor_editar").value.trim();
  const otroDoc = document
    .getElementById("txt_dni_emisor2_editar")
    .value.trim();

  let numero_documento = "";

  if (tipo === "DNI" && dni !== "") {
    numero_documento = dni;
  } else if (tipo !== "DNI" && otroDoc !== "") {
    numero_documento = otroDoc;
  } else {
    Swal.fire(
      "Advertencia",
      "Debe ingresar un número de documento válido.",
      "warning"
    );
    return;
  }

  try {
    const resp = await $.ajax({
      url: "../controller/encomiendas/controlador_buscar_persona_por_documento.php",
      type: "POST",
      data: { numero_documento },
      dataType: "json",
    });

    if (resp.data && resp.data.length > 0) {
      const d = resp.data[0];

      // Rellenar campos
      $("#txt_nomb_emisor_editar").val(d.nombre_completo);
      $("#txt_celu1_emisor_editar").val(d.celular);
    } else {
      Swal.fire(
        "No encontrado",
        "No se encontró ninguna persona con ese documento.",
        "info"
      );
    }
  } catch (error) {
    console.error("❌ Error en AJAX:", error);
    Swal.fire("Error", "No se pudo hacer la búsqueda.", "error");
  }
}

async function buscarPorDocumento2Editar() {
  const tipo = document.getElementById(
    "select_tipo_documento_receptor_editar"
  ).value;
  const dni = document.getElementById("txt_dni_receptor_editar").value.trim();
  const otroDoc = document
    .getElementById("txt_dni_recepto2_editar")
    .value.trim();

  let numero_documento = "";

  if (tipo === "DNI" && dni !== "") {
    numero_documento = dni;
  } else if (tipo !== "DNI" && otroDoc !== "") {
    numero_documento = otroDoc;
  } else {
    Swal.fire(
      "Advertencia",
      "Debe ingresar un número de documento válido.",
      "warning"
    );
    return;
  }

  try {
    const resp = await $.ajax({
      url: "../controller/encomiendas/controlador_buscar_persona_por_documento.php",
      type: "POST",
      data: { numero_documento },
      dataType: "json",
    });

    if (resp.data && resp.data.length > 0) {
      const d = resp.data[0];

      // Rellenar campos
      $("#txt_nomb_receptor_editar").val(d.nombre_completo);
      $("#txt_celu1_recepto_editar").val(d.celular);
    } else {
      Swal.fire(
        "No encontrado",
        "No se encontró ninguna persona con ese documento.",
        "info"
      );
    }
  } catch (error) {
    console.error("❌ Error en AJAX:", error);
    Swal.fire("Error", "No se pudo hacer la búsqueda.", "error");
  }
}

// FUNCIÓN PARA ASIGNAR COLORES AL ESTADO DE PAGO
function asignarEstadoPago(estado) {
  const spanEstadoPago = document.getElementById("span_estado_pago_mostrar");
  spanEstadoPago.textContent = estado;

  // Remover clases anteriores
  spanEstadoPago.classList.remove(
    "badge-success",
    "badge-danger",
    "badge-warning",
    "badge-info"
  );

  switch (estado.toLowerCase()) {
    case "pagado":
    case "completado":
      spanEstadoPago.classList.add("badge-success");
      break;
    case "pendiente":
    case "por pagar":
      spanEstadoPago.classList.add("badge-warning");
      break;
    case "cancelado":
    case "anulado":
      spanEstadoPago.classList.add("badge-danger");
      break;
    default:
      spanEstadoPago.classList.add("badge-info");
      break;
  }
}

// FUNCIÓN PARA ASIGNAR COLORES AL ESTADO DE ENCOMIENDA
function asignarEstadoEncomienda(estado) {
  const spanEstadoEncomienda = document.getElementById(
    "span_estado_encomienda_mostrar"
  );
  spanEstadoEncomienda.textContent = estado;

  // Remover clases anteriores
  spanEstadoEncomienda.classList.remove(
    "badge-success",
    "badge-danger",
    "badge-warning",
    "badge-info",
    "badge-primary"
  );

  switch (estado.toLowerCase()) {
    case "entregado":
    case "completado":
      spanEstadoEncomienda.classList.add("badge-success");
      break;
    case "en transito":
    case "en tránsito":
    case "enviado":
      spanEstadoEncomienda.classList.add("badge-primary");
      break;
    case "pendiente":
    case "en espera":
      spanEstadoEncomienda.classList.add("badge-warning");
      break;
    case "cancelado":
    case "anulado":
      spanEstadoEncomienda.classList.add("badge-danger");
      break;
    default:
      spanEstadoEncomienda.classList.add("badge-info");
      break;
  }
}

//LIMPIAR CAMPOS
function LimpiarCamposEncomienda() {
  // CAMPOS PRINCIPALES
  document.getElementById("select_conductor").value = "";
  document.getElementById("select_origen").value = "";
  document.getElementById("select_destino").value = "";
  document.getElementById("txt_descripcion").value = ""; // CORREGIDO: era txtxt_descripciont_fecha_creacion

  // DATOS DEL EMISOR
  document.getElementById("txt_dni_emisor").value = "";
  document.getElementById("txt_dni_emisor2").value = "";
  document.getElementById("txt_nomb_emisor").value = "";
  document.getElementById("txt_celu1_emisor").value = "";

  // DATOS DEL RECEPTOR
  document.getElementById("txt_dni_receptor").value = "";
  document.getElementById("txt_dni_recepto2").value = "";
  document.getElementById("txt_nomb_receptor").value = "";
  document.getElementById("txt_celu1_recepto").value = "";

  // DATOS DE PAGO
  document.getElementById("txt_pago").value = "0.00";
  document.getElementById("txt_por_pagar").value = "0.00";
  document.getElementById("txt_a_domicilio").value = "0.00";

  // SI TIENES SELECT2 O CHOSEN, TAMBIÉN NECESITAS ACTUALIZARLOS
  // $('#select_conductor').trigger('change'); // Para Select2
  // $('#select_origen').trigger('change'); // Para Select2
  // $('#select_destino').trigger('change'); // Para Select2
  // $('#select_tipo_documento_emisor').trigger('change'); // Para Select2
  // $('#select_tipo_documento_receptor').trigger('change'); // Para Select2
}

function Registrar_Encomiendas() {
  let conduc = document.getElementById("select_conductor").value;
  let ori = document.getElementById("select_origen").value;
  let des = document.getElementById("select_destino").value;
  let fecha = document.getElementById("txt_fecha_creacion").value;
  let desc = document.getElementById("txt_descripcion").value;

  // DATOS DEL EMISOR
  let tipodocemi = document.getElementById("select_tipo_documento_emisor").value;
  let dniemi = document.getElementById("txt_dni_emisor").value;
  let dni2emi = document.getElementById("txt_dni_emisor2").value;
  let nomemi = document.getElementById("txt_nomb_emisor").value;
  let celemi = document.getElementById("txt_celu1_emisor").value;

  // DATOS DEL RECEPTOR
  let tipodocrece = document.getElementById("select_tipo_documento_receptor").value;
  let dnirece = document.getElementById("txt_dni_receptor").value;
  let deni2rece = document.getElementById("txt_dni_recepto2").value;
  let nomrece = document.getElementById("txt_nomb_receptor").value;
  let celurece = document.getElementById("txt_celu1_recepto").value;

  // DATOS DE PAGO
  let pago = document.getElementById("txt_pago").value;
  let porpagar = document.getElementById("txt_por_pagar").value;
  let adomicilio = document.getElementById("txt_a_domicilio").value;
  let idusu = document.getElementById("txtprincipalid").value;

  // Obtener el nombre del select de destino
  let selectDestino = document.getElementById("select_destino");
  let nombre_destino = selectDestino.options[selectDestino.selectedIndex].text;

  if (
    conduc.length == 0 ||
    ori.length == 0 ||
    des.length == 0 ||
    fecha.length == 0 ||
    tipodocemi.length == 0 ||
    dniemi.length == 0 ||
    nomemi.length == 0 ||
    celemi.length == 0 ||
    tipodocrece.length == 0 ||
    dnirece.length == 0 ||
    nomrece.length == 0 ||
    celurece.length == 0
  ) {
    return Swal.fire("Mensaje de Advertencia", "Todo los campos son obligatorios", "warning");
  }

  // Origen y destino distintos: la salida solo lleva encomiendas de su misma ruta
  if (ori === des) {
    return Swal.fire("Mensaje de Advertencia", "El origen y el destino no pueden ser iguales", "warning");
  }

  //validacion de pago
  if (pago.length == 0 || porpagar.length == 0 || adomicilio.length == 0) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Los campos de pago, por pagar y a domicilio no pueden ir vacíos, uno de los 3 siempre debe ser mayor a 0",
      "warning"
    );
  }

  // Validar documento según tipo EMISOR
  let documentoFinal = "";
  if (tipodocemi === "DNI") {
    if (!dniemi) {
      return Swal.fire("Mensaje de Advertencia", "El campo DNI del emisor es obligatorio", "warning");
    }
    documentoFinal = dniemi;
  } else {
    if (!dni2emi) {
      return Swal.fire("Mensaje de Advertencia", "El campo de documento del emisor es obligatorio", "warning");
    }
    documentoFinal = dni2emi;
  }

  // Validar documento según tipo RECEPTOR
  let documentoFinal2 = "";
  if (tipodocrece === "DNI") {
    if (!dnirece) {
      return Swal.fire("Mensaje de Advertencia", "El campo DNI del receptor es obligatorio", "warning");
    }
    documentoFinal2 = dnirece;
  } else {
    if (!deni2rece) {
      return Swal.fire("Mensaje de Advertencia", "El campo de documento del receptor es obligatorio", "warning");
    }
    documentoFinal2 = deni2rece;
  }

  $.ajax({
    url: "../controller/encomiendas/controlador_registro_encomiendas.php",
    type: "POST",
    data: {
      conduc: conduc,
      ori: ori,
      des: des,
      fecha: fecha,
      desc: desc,
      // DATOS DEL EMISOR
      tipodocemi: tipodocemi,
      documentoFinal: documentoFinal,
      nomemi: nomemi,
      celemi: celemi,
      // DATOS DEL RECEPTOR
      tipodocrece: tipodocrece,
      documentoFinal2: documentoFinal2,
      nomrece: nomrece,
      celurece: celurece,
      // DATOS DE PAGO
      pago: pago,
      porpagar: porpagar,
      adomicilio: adomicilio,
      idusu: idusu,
    },
  })
    .done(function (resp) {
      if (resp > 0) {
        if (resp > 1) {
          // Fecha y hora actual
          let ahora = new Date();
          let fechaActual =
            ahora.getDate().toString().padStart(2, "0") +
            "/" +
            (ahora.getMonth() + 1).toString().padStart(2, "0") +
            "/" +
            ahora.getFullYear() +
            " a las " +
            ahora.getHours().toString().padStart(2, "0") +
            ":" +
            ahora.getMinutes().toString().padStart(2, "0");

          // CONFIRMACIÓN CON OPCIÓN DE IMPRIMIR BOLETA
          Swal.fire({
            title: "Encomienda registrada correctamente",
            html:
              "Registrada el: <b>" +
              fechaActual +
              "</b><br>Destino: <b>" +
              nombre_destino +
              "</b><br><br>¿Desea imprimir la boleta?",
            icon: "success",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Imprimir Boleta!",
          }).then((result) => {
            if (result.isConfirmed) {
              var url =
                "../view/MPDF/REPORTE/boleta_pago.php?id=" +
                encodeURIComponent(resp) +
                "#zoom=100%";
              var newWindow = window.open(url, "ENCOMIENDA", "scrollbars=NO");

              if (newWindow) {
                newWindow.moveTo(0, 0);
                newWindow.resizeTo(screen.width, screen.height);
              }
            }
            $("#modal_registro").modal("hide");
            tbl_encomiendas.ajax.reload();
            LimpiarCamposEncomienda();
          });
        } else {
          Swal.fire(
            "Mensaje de Advertencia",
            "La encomienda que intentas registrar ya se encuentra en la base de datos, revise por favor",
            "warning"
          );
        }
      } else {
        return Swal.fire("Mensaje de Error", EstadosOperacion.mensaje(resp, "No se completó el registro"), "error");
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Error AJAX:", textStatus, errorThrown);
      Swal.fire("Mensaje de Error", "Error de conexión: " + textStatus, "error");
    });
}

//ELIMINAR AREAS
function Eliminar_encomienda(id) {
  $.ajax({
    url: "../controller/encomiendas/controlador_eliminar_encomiendas.php",
    type: "POST",
    data: {
      id: id,
    },
  }).done(function (resp) {
    if (resp > 0) {
      Swal.fire(
        "Mensaje de Confirmación",
        "Se elimino la encomienda con exito, si desea recuperarlo, tendra que volver a registrarlo",
        "success"
      ).then((value) => {
        tbl_encomiendas.ajax.reload();
      });
    } else {
      return Swal.fire(
        "Mensaje de Advetencia",
        EstadosOperacion.mensaje(resp, "No se puede eliminar la encomienda, verifique por favor"),
        "warning"
      );
    }
  });
}

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
      cadena += "<option value=''>No hay conductores disponibles</option>";
    }

    $("#select_origen").html(cadena);
    $("#select_destino").html(cadena);
    $("#select_origen_bus").html(cadena);
    $("#select_destino_bus").html(cadena);
    $("#select_origen_editar").html(cadena);
    $("#select_destino_editar").html(cadena);
  });
}

//CAMBIO DE ESTADO DE ENCOMIENDAS
function Modificar_Estado() {
  let id = document.getElementById("id_encomienda").value;
  let nuevo_estado = document.getElementById("select_estado_editar2").value;
  let observacion = document.getElementById("text_observacion_enco").value;
  let anula = document.getElementById("txt_anula_enco").value;
  let idusu = document.getElementById("txtprincipalid").value;

  // Validaciones
  if (id.length == 0 || nuevo_estado.length == 0) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Tiene campos vacios, revise por favor",
      "warning"
    );
  }

  if (nuevo_estado == "ANULADO" && anula.length == 0) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "La anulación es obligatoria, revise por favor",
      "warning"
    );
  }

  // OBSERVADO necesita el motivo; en los demás estados la observación es opcional
  if (nuevo_estado == "OBSERVADO" && observacion.trim().length == 0) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Indique el motivo de la observación, revise por favor",
      "warning"
    );
  }

  // Mensaje de confirmación antes de modificar
  Swal.fire({
    title: "¿Está seguro de cambiar el estado a: " + nuevo_estado + "?",
    text: "Una vez confirmado, el estado de la encomienda será actualizado.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, Cambiar Estado",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // Si confirma, ejecutar el AJAX
      $.ajax({
        url: "../controller/encomiendas/controlador_modificar_estado.php",
        type: "POST",
        data: {
          id: id,
          nuevo_estado: nuevo_estado,
          observacion: observacion,
          anula: anula,
          idusu: idusu,
        },
      })
        .done(function (resp) {
          if (resp > 0) {
            Swal.fire(
              "Mensaje de Confirmación",
              "Se cambió el estado correctamente!!!",
              "success"
            ).then((value) => {
              tbl_encomiendas.ajax.reload();
              $("#modal_estado").modal("hide"); // Asegúrate que el ID del modal sea correcto
            });
          } else {
            return Swal.fire(
              "Mensaje de Error",
              EstadosOperacion.mensaje(resp, "No se completó la actualización"),
              "error"
            );
          }
        })
        .fail(function () {
          Swal.fire(
            "Mensaje de Error",
            "Error en la comunicación con el servidor",
            "error"
          );
        });
    }
  });
}

//CAMBIO DE ESTADO DE AJUSTE DE PRECIO
function Modificar_Estado2() {
  let id = document.getElementById("id_encomienda3").value;
  let nuevo_estado = document.getElementById("select_estado_editar4").value;
  let pago_anti = document.getElementById("txt_monto_anterior").value;
  let pago_nuevo = document.getElementById("txt_monto_nuevo").value;
  let idusu = document.getElementById("txtprincipalid").value;

  if (!nuevo_estado || nuevo_estado.trim() === "") {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Debe seleccionar un estado",
      "warning"
    );
  }

  if (!idusu || idusu.trim() === "") {
    return Swal.fire("Mensaje de Advertencia", "Usuario no válido", "warning");
  }

  // Convertir a números y validar
  let montoAnterior = parseFloat(pago_anti) || 0;
  let montoNuevo = parseFloat(pago_nuevo) || 0;

  // Validar que los campos de monto no estén vacíos
  if (
    !pago_anti ||
    pago_anti.trim() === "" ||
    !pago_nuevo ||
    pago_nuevo.trim() === ""
  ) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Los campos de monto son obligatorios",
      "warning"
    );
  }

  // Validar que los montos sean números válidos
  if (isNaN(montoAnterior) || isNaN(montoNuevo)) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Los montos deben ser números válidos",
      "warning"
    );
  }

  // Validar que ambos montos sean mayores a 0
  if (montoAnterior <= 0 || montoNuevo <= 0) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Ambos montos deben ser mayores a 0.00",
      "warning"
    );
  }

  // Validar que el nuevo estado no sea OBSERVADO
  if (nuevo_estado === "OBSERVADO") {
    return Swal.fire(
      "Mensaje de Advertencia",
      "No puede cambiar a estado OBSERVADO",
      "warning"
    );
  }

  // Validar que haya un cambio real en el monto (opcional)
  if (montoAnterior === montoNuevo) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "El monto nuevo debe ser diferente al monto anterior",
      "warning"
    );
  }

  // Validar que el monto nuevo sea positivo y realista
  if (montoNuevo > 99999.99) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "El monto nuevo es demasiado alto",
      "warning"
    );
  }

  // Mensaje de confirmación antes de modificar
  Swal.fire({
    title:
      "¿Está seguro de cambiar el nuevo monto a: " + pago_nuevo + " soles?",
    text: "Una vez confirmado, el monto de la encomienda será actualizado.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, Cambiar Estado",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // Si confirma, ejecutar el AJAX
      $.ajax({
        url: "../controller/encomiendas/controlador_modificar_pago.php",
        type: "POST",
        data: {
          id: id,
          nuevo_estado: nuevo_estado,
          pago_anti: pago_anti,
          pago_nuevo: pago_nuevo,
          idusu: idusu,
        },
      })
        .done(function (resp) {
          if (resp > 0) {
            Swal.fire(
              "Mensaje de Confirmación",
              "Se cambió el previo de la correctamente!!!",
              "success"
            ).then((value) => {
              tbl_encomiendas.ajax.reload();
              $("#modal_ajustar_precio").modal("hide"); // Asegúrate que el ID del modal sea correcto
            });
          } else {
            return Swal.fire(
              "Mensaje de Error",
              EstadosOperacion.mensaje(resp, "No se completó la actualización"),
              "error"
            );
          }
        })
        .fail(function () {
          Swal.fire(
            "Mensaje de Error",
            "Error en la comunicación con el servidor",
            "error"
          );
        });
    }
  });
}

function listar_historial_estado(id) {
  tbl_historial_estado = $("#tabla_ver_historial").DataTable({
    ordering: false,
    bLengthChange: true,
    searching: false, // Deshabilita la barra de búsqueda
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "Todos"],
    ],
    pageLength: 5,
    destroy: true,
    pagingType: "full_numbers",
    scrollCollapse: true,
    responsive: true,
    async: false,
    processing: true,
    ajax: {
      url: "../controller/encomiendas/controlador_listar_estados.php",
      type: "POST",
      data: { id: id },
      dataSrc: function (json) {
        console.log("Respuesta JSON:", json);
        return json.data || []; // sin filas el servidor puede no enviar data
      },
    },
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excelHtml5",
        text: " Excel",
        titleAttr: "Exportar a Excel",
        filename: "LISTA_DE_HISTORIAL_ESTADO",
        title: "LISTA DE HISTORIAL DE ESTADOS",
        className: "btn btn-success",
      },
      {
        extend: "pdfHtml5",
        text: " PDF",
        titleAttr: "Exportar a PDF",
        filename: "LISTA_DE_HISTORIAL_ESTADO",
        title: "LISTA DE HISTORIAL DE ESTADOS",
        className: "btn btn-danger",
      },
      {
        extend: "print",
        text: " Imprimir",
        titleAttr: "Imprimir",
        title: "LISTA DE HISTORIAL DE ESTADOS",
        className: "btn btn-primary",
      },
    ],
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      { data: "USUARIO" },
      {
        data: "estado",
        render: function (data, type, row) {
          switch (data) {
            case "PENDIENTE":
              return '<span class="badge badge-warning" style="background-color: #ffc107; color: #212529;">PENDIENTE</span>';
            case "ENTREGADO":
              return '<span class="badge badge-success" style="background-color: #28a745; color: white;">ENTREGADO</span>';
            case "OBSERVADO":
              return '<span class="badge badge-info" style="background-color: #17a2b8; color: white;">OBSERVADO</span>';
            case "EN TRANSITO":
              return '<span class="badge badge-primary" style="background-color: #007bff; color: white;">EN TRÁNSITO</span>';
            case "EN AGENCIA":
              return '<span class="badge badge-secondary" style="background-color: #6c757d; color: white;">EN AGENCIA</span>';
            case "ANULADO":
              return '<span class="badge badge-danger" style="background-color: #dc3545; color: white;">ANULADO</span>';
            default:
              return (
                '<span class="badge badge-dark" style="background-color: #343a40; color: white;">' +
                data +
                "</span>"
              );
          }
        },
      },
      { data: "observacion" },
      {
        data: "precio_anterior",
        render: function (data, type, row) {
          if (data && data != "" && data != "0" && data != "0.00") {
            return "S/ " + parseFloat(data).toFixed(2);
          }
          return "-";
        },
      },
      {
        data: "precio_nuevi",
        render: function (data, type, row) {
          if (data && data != "" && data != "0" && data != "0.00") {
            return "S/ " + parseFloat(data).toFixed(2);
          }
          return "-";
        },
      },
      { data: "motivo_anula" },
      { data: "fecha_formateada" },
      { data: "fecha_formateada2" },
    ],
    language: {
      emptyTable: "No se encontraron datos",
      zeroRecords: "No se encontraron resultados",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoEmpty: "Mostrando 0 a 0 de 0 registros",
      infoFiltered: "(filtrado de _MAX_ registros en total)",
      lengthMenu: "Mostrar _MENU_ registros",
      loadingRecords: "Cargando...",
      processing: "Procesando...",
      search: "Buscar:",
      paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior",
      },
    },
    select: true,
  });
}

function Realizar_pago() {
  let id = document.getElementById("id_encomienda_pago").value;
  let nuevo_estado = document.getElementById("select_estado_pago").value;
  
  // ✅ OBTENER EL ESTADO INICIAL QUE SE GUARDÓ AL ABRIR EL MODAL
  let estado_inicial = document.getElementById("select_estado_pago").getAttribute("data-estado-inicial");
  
  let saldo_pendiente = document.getElementById("txt_saldo_pendiente").value;
  let monto_recibido = document.getElementById("txt_monto_recibido").value;
  let vuelto = document.getElementById("txt_vuelto").value;
  let idusu = document.getElementById("txtprincipalid").value;

  // Validar que se haya seleccionado un estado
  if (!nuevo_estado || nuevo_estado.trim() === "") {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Debe seleccionar un estado",
      "warning"
    );
  }


  // Validar que el usuario sea válido
  if (!idusu || idusu.trim() === "") {
    return Swal.fire("Mensaje de Advertencia", "Usuario no válido", "warning");
  }

  // Validar que el monto recibido no esté vacío
  if (!monto_recibido || monto_recibido.trim() === "") {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Debe ingresar el monto recibido",
      "warning"
    );
  }

  // Limpiar y convertir los valores a números
  let saldoPendienteNum =
    parseFloat(saldo_pendiente.replace("S/", "").replace(",", "").trim()) || 0;
  let montoRecibidoNum = parseFloat(monto_recibido) || 0;

  // Validar que los montos sean números válidos
  if (isNaN(saldoPendienteNum) || isNaN(montoRecibidoNum)) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Los montos deben ser números válidos",
      "warning"
    );
  }

  // Validar que el saldo pendiente sea mayor a 0
  if (saldoPendienteNum <= 0) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "El saldo pendiente debe ser mayor a 0.00",
      "warning"
    );
  }

  // Validar que el monto recibido sea mayor a 0
  if (montoRecibidoNum <= 0) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "El monto recibido debe ser mayor a 0.00",
      "warning"
    );
  }

  // Validar que el nuevo estado no sea OBSERVADO para pagos
  if (nuevo_estado === "OBSERVADO") {
    return Swal.fire(
      "Mensaje de Advertencia",
      "No puede cambiar a estado OBSERVADO en un pago",
      "warning"
    );
  }

  // Validar que el monto recibido no sea demasiado alto (opcional)
  if (montoRecibidoNum > 99999.99) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "El monto recibido es demasiado alto",
      "warning"
    );
  }

  // Calcular vuelto
  let vueltoCalculado = montoRecibidoNum - saldoPendienteNum;

  // Validar que el monto recibido sea suficiente (NO SE PERMITEN PAGOS PARCIALES)
  if (vueltoCalculado < 0) {
    return Swal.fire({
      title: "¡Monto insuficiente!",
      text: `El monto recibido (S/ ${montoRecibidoNum.toFixed(
        2
      )}) es menor al saldo pendiente (S/ ${saldoPendienteNum.toFixed(
        2
      )}). Falta: S/ ${Math.abs(vueltoCalculado).toFixed(2)}`,
      icon: "error",
      confirmButtonText: "Entendido",
    });
  }

  // Preparar mensaje de confirmación solo para pagos completos
  let mensajeConfirmacion = `¿Está seguro de procesar este pago?\n\nSaldo pendiente: S/ ${saldoPendienteNum.toFixed(
    2
  )}\nMonto recibido: S/ ${montoRecibidoNum.toFixed(
    2
  )}\nVuelto: S/ ${vueltoCalculado.toFixed(2)}`;

  // Mensaje de confirmación para pago completo
  Swal.fire({
    title: "¿Está seguro de procesar este pago?",
    text: mensajeConfirmacion,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, Procesar Pago",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      procesarPago();
    }
  });

  // Función interna para procesar el pago
  function procesarPago() {
    $.ajax({
      url: "../controller/encomiendas/controlador_procesar_pago.php",
      type: "POST",
      data: {
        id: id,
        nuevo_estado: nuevo_estado,
        saldo_pendiente: saldoPendienteNum.toFixed(2),
        monto_recibido: montoRecibidoNum.toFixed(2),
        vuelto: vueltoCalculado.toFixed(2),
        idusu: idusu,
      },
    })
      .done(function (resp) {
        if (resp > 0) {
          // Fecha y hora actual
          let ahora = new Date();
          let fechaActual =
            ahora.getDate().toString().padStart(2, "0") +
            "/" +
            (ahora.getMonth() + 1).toString().padStart(2, "0") +
            "/" +
            ahora.getFullYear() +
            " a las " +
            ahora.getHours().toString().padStart(2, "0") +
            ":" +
            ahora.getMinutes().toString().padStart(2, "0");

          // CONFIRMACIÓN CON OPCIÓN DE IMPRIMIR BOLETA
          Swal.fire({
            title: "Pago procesado correctamente",
            html:
              "Procesado el: <b>" +
              fechaActual +
              "</b><br>Vuelto entregado: <b>S/ " +
              vueltoCalculado.toFixed(2) +
              "</b><br><br>¿Desea imprimir la boleta?",
            icon: "success",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Imprimir Boleta!",
            cancelButtonText: "No, gracias",
          }).then((result) => {
            if (result.isConfirmed) {
              var url =
                "../view/MPDF/REPORTE/boleta_pago.php?id=" +
                encodeURIComponent(id) +
                "#zoom=100%";
              var newWindow = window.open(url, "BOLETA_PAGO", "scrollbars=NO");

              if (newWindow) {
                newWindow.moveTo(0, 0);
                newWindow.resizeTo(screen.width, screen.height);
              }
            }
            
            tbl_encomiendas.ajax.reload();
            $("#modal_pagar").modal("hide");
          });
        } else {
          return Swal.fire(
            "Mensaje de Error",
            EstadosOperacion.mensaje(resp, "No se completó el procesamiento del pago"),
            "error"
          );
        }
      })
      .fail(function () {
        Swal.fire(
          "Mensaje de Error",
          "Error en la comunicación con el servidor",
          "error"
        );
      });
  }
}

//CARGAR USUARIOS
function Cargar_Select_Usuarios() {
  $.ajax({
    url: "../controller/encomiendas/controlador_cargar_select_usuario.php",
    type: "POST",
  }).done(function (resp) {
    let data = JSON.parse(resp);
    let cadena = "<option value=''>Seleccionar Usuario</option>";
    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) {
        cadena +=
          "<option value='" +
          data[i][0] +
          "'>DNI: " +
          data[i][1] +
          " - Usuario: " +
          data[i][2] +
          "</option>";
      }
    } else {
      cadena += "<option value=''>No hay usuarios disponibles</option>";
    }
    $("#select_usuario").html(cadena);

    // Inicializar Select2 después de cargar opciones
    $("#select_usuario").select2({
      placeholder: "Seleccionar Usuario",
      allowClear: true,
      width: "100%", // Asegura que use todo el ancho
    });
  });
}

//FUNCION PARA EDITAR LA ENCOMIENDA
function Modificar_Encomiendas() {
  let id = document.getElementById("txt_id_encomienda").value;
  let conduc = document.getElementById("select_conductor_editar").value;
  let ori = document.getElementById("select_origen_editar").value;
  let des = document.getElementById("select_destino_editar").value;
  let fecha = document.getElementById("txt_fecha_creacion_editar").value;
  let desc = document.getElementById("txt_descripcion_editar").value;

  // DATOS DEL EMISOR
  let tipodocemi = document.getElementById(
    "select_tipo_documento_emisor_editar"
  ).value;
  let dniemi = document.getElementById("txt_dni_emisor_editar").value;
  let dni2emi = document.getElementById("txt_dni_emisor2_editar").value;
  let nomemi = document.getElementById("txt_nomb_emisor_editar").value;
  let celemi = document.getElementById("txt_celu1_emisor_editar").value;

  // DATOS DEL RECEPTOR
  let tipodocrece = document.getElementById(
    "select_tipo_documento_receptor_editar"
  ).value;
  let dnirece = document.getElementById("txt_dni_receptor_editar").value;
  let deni2rece = document.getElementById("txt_dni_recepto2_editar").value;
  let nomrece = document.getElementById("txt_nomb_receptor_editar").value;
  let celurece = document.getElementById("txt_celu1_recepto_editar").value;

  // DATOS DE PAGO
  let pago = document.getElementById("txt_pago_editar").value;
  let porpagar = document.getElementById("txt_por_pagar_editar").value;
  let adomicilio = document.getElementById("txt_a_domicilio_editar").value;
  let obse = document.getElementById("txt_observacion_editar").value;
  let idusu = document.getElementById("txtprincipalid").value;

  // Obtener el nombre del select de destino
  let selectDestino = document.getElementById("select_destino_editar");
  let nombre_destino = selectDestino.options[selectDestino.selectedIndex].text;

  if (
    conduc.length == 0 ||
    ori.length == 0 ||
    des.length == 0 ||
    fecha.length == 0 ||
    tipodocemi.length == 0 ||
    dniemi.length == 0 ||
    nomemi.length == 0 ||
    celemi.length == 0 ||
    tipodocrece.length == 0 ||
    dnirece.length == 0 ||
    nomrece.length == 0 ||
    celurece.length == 0
  ) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Todo los campos son obligatorios",
      "warning"
    );
  }

  // Origen y destino distintos: la salida solo lleva encomiendas de su misma ruta
  if (ori === des) {
    return Swal.fire("Mensaje de Advertencia", "El origen y el destino no pueden ser iguales", "warning");
  }

  //validacion de pago
  if (pago.length == 0 || porpagar.length == 0 || adomicilio.length == 0) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Los campos de pago, por pagar y o domicilio no pueden ir vacios, siempre debe ir uno con valor mayor a 0.00",
      "warning"
    );
  }

  // Validar documento según tipo EMISOR
  let documentoFinal = "";
  if (tipodocemi === "DNI") {
    if (!dniemi) {
      return Swal.fire("Mensaje de Advertencia", "El campo DNI del emisor es obligatorio", "warning");
    }
    documentoFinal = dniemi;
  } else {
    if (!dni2emi) {
      return Swal.fire("Mensaje de Advertencia", "El campo de documento del emisor es obligatorio", "warning");
    }
    documentoFinal = dni2emi;
  }

  // Validar documento según tipo RECEPTOR
  let documentoFinal2 = "";
  if (tipodocrece === "DNI") {
    if (!dnirece) {
      return Swal.fire("Mensaje de Advertencia", "El campo DNI del receptor es obligatorio", "warning");
    }
    documentoFinal2 = dnirece;
  } else {
    if (!deni2rece) {
      return Swal.fire("Mensaje de Advertencia", "El campo de documento del receptor es obligatorio", "warning");
    }
    documentoFinal2 = deni2rece;
  }

  $.ajax({
    url: "../controller/encomiendas/controlador_editar_encomiendas.php",
    type: "POST",
    data: {
      id: id,
      conduc: conduc,
      ori: ori,
      des: des,
      fecha: fecha,
      desc: desc,
      // DATOS DEL EMISOR
      tipodocemi: tipodocemi,
      documentoFinal: documentoFinal,
      nomemi: nomemi,
      celemi: celemi,
      // DATOS DEL RECEPTOR
      tipodocrece: tipodocrece,
      documentoFinal2: documentoFinal2,
      nomrece: nomrece,
      celurece: celurece,
      // DATOS DE PAGO
      pago: pago,
      porpagar: porpagar,
      adomicilio: adomicilio,
      obse: obse,
      idusu: idusu,
    },
  })
    .done(function (resp) {
      if (resp > 0) {
        if (resp == 1) {
          let ahora = new Date();
          let fechaActual =
            ahora.getDate().toString().padStart(2, "0") +
            "/" +
            (ahora.getMonth() + 1).toString().padStart(2, "0") +
            "/" +
            ahora.getFullYear() +
            " a las " +
            ahora.getHours().toString().padStart(2, "0") +
            ":" +
            ahora.getMinutes().toString().padStart(2, "0");

          Swal.fire(
            "Mensaje de Confirmación",
            "Se modifico exitosamente la encomienda el: <b>" +
              fechaActual +
              "</b> con destino: <b>" +
              nombre_destino +
              "</b>.",
            "success"
          ).then((value) => {
            $("#modal_editar").modal("hide");
            tbl_encomiendas.ajax.reload();

            // LLAMAR A LA FUNCIÓN DE LIMPIEZA
            LimpiarCamposEncomiendaEditar();
          });
        } else {
          Swal.fire(
            "Mensaje de Advertencia",
            "La encomienda que intentas modificar ya se encuentra en la base de datos, revise por favor",
            "warning"
          );
        }
      } else {
        return Swal.fire(
          "Mensaje de Error",
          EstadosOperacion.mensaje(resp, "No se completó el registro"),
          "error"
        );
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Error AJAX:", textStatus, errorThrown);
      Swal.fire(
        "Mensaje de Error",
        "Error de conexión: " + textStatus,
        "error"
      );
    });
}

function LimpiarCamposEncomiendaEditar() {
  // CAMPOS PRINCIPALES
  document.getElementById("select_conductor_editar").value = "";
  document.getElementById("select_origen_editar").value = "";
  document.getElementById("select_destino_editar").value = "";
  document.getElementById("txt_descripcion").value = ""; // CORREGIDO: era txtxt_descripciont_fecha_creacion

  // DATOS DEL EMISOR
  document.getElementById("txt_dni_emisor_editar").value = "";
  document.getElementById("txt_dni_emisor2_editar").value = "";
  document.getElementById("txt_nomb_emisor_editar").value = "";
  document.getElementById("txt_celu1_emisor").value = "";

  // DATOS DEL RECEPTOR
  document.getElementById("txt_dni_receptor_editar").value = "";
  document.getElementById("txt_dni_recepto2_editar").value = "";
  document.getElementById("txt_nomb_receptor_editar").value = "";
  document.getElementById("txt_celu1_recepto_editar").value = "";

  // DATOS DE PAGO
  document.getElementById("txt_pago_editar").value = "0.00";
  document.getElementById("txt_por_pagar_editar").value = "0.00";
  document.getElementById("txt_a_domicilio_editar").value = "0.00";

}

// Configuración base de la tabla de encomiendas.
// Igual para todos los roles; cada rol pasa pageLength y sus columnas propias.
function Opciones_Tabla_Encomiendas_Base(ajax, pageLength, columnasPropias) {
  return {
    pagingType: "full_numbers",
    scrollCollapse: true,
    responsive: true,
    ordering: false,
    bLengthChange: true,
    searching: { regex: false },
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "All"],
    ],
    pageLength: pageLength,
    destroy: true,
    pagingType: "full_numbers",
    scrollCollapse: true,
    responsive: true,
    async: false,
    processing: true,
    ajax: ajax,
    dom: "Bfrtip",

buttons: [
  {
    extend: "excelHtml5",
    text: '<i class="fas fa-file-excel"></i> Excel',
    titleAttr: "Exportar a Excel",
    filename: "LISTA DE ENCOMIENDAS",
    title: "LISTA DE ENCOMIENDAS",
    className: "btn btn-success btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
      format: {
        header: function(data, columnIdx) {
          if (columnIdx === 0) return "NRO.";
          return data;
        },
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          return cleanData;
        }
      }
    }
  },
  {
    extend: "pdfHtml5",
    text: '<i class="fas fa-file-pdf"></i> PDF',
    titleAttr: "Exportar a PDF",
    filename: "LISTA DE ENCOMIENDAS",
    title: "LISTA DE ENCOMIENDAS",
    className: "btn btn-danger btn-sm",
    orientation: "landscape",
    pageSize: "A4",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
      format: {
        header: function(data, columnIdx) {
          if (columnIdx === 0) return "NRO.";
          return data;
        },
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          return cleanData;
        }
      }
    },
    customize: function(doc) {
      doc.defaultStyle.fontSize = 6;
      doc.styles.tableHeader.fontSize = 7;
    }
  },
  {
    extend: "print",
    text: '<i class="fa fa-print"></i> Imprimir',
    titleAttr: "Imprimir",
    title: "LISTA DE ENCOMIENDAS",
    className: "btn btn-info btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
      format: {
        header: function(data, columnIdx) {
          if (columnIdx === 0) return "NRO.";
          return data;
        },
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          return cleanData;
        }
      }
    }
  }
],



    columns: [
      { data: "boleta_nro" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            "<strong>" +
            row.tipo_documen +
            ": " +
            row.nro_doc +
            "</strong><br>" +
            row.nombres_apellidos
          );
        },
      },
      { data: "nombre_origen" },
      { data: "nombre_destino" },
      { data: "fecha_formateada" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            "<strong>" +
            row.tipo_doc_emisor +
            ": " +
            row.nro_doc_emisor +
            "</strong><br>" +
            row.nombre_emisor
          );
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return (
            "<strong>" +
            row.tipo_doc_receptor +
            ": " +
            row.nro_doc_receptor +
            "</strong><br>" +
            row.nombre_receptor
          );
        },
      },

      // ---- PAGO ----
      {
        data: "pago",
        render: function (data, type, row) {
          if (parseFloat(data) > 0) {
            return '<span class="badge bg-success">S/ ' + data + "</span>";
          } else {
            return '<span class="badge bg-secondary">-</span>';
          }
        },
      },

      // ---- POR PAGAR ----
      {
        data: "por_pagar",
        render: function (data, type, row) {
          if (parseFloat(data) > 0) {
            return '<span class="badge bg-danger">S/ ' + data + "</span>";
          } else {
            return '<span class="badge bg-secondary">-</span>';
          }
        },
      },

      // ---- A DOMICILIO ----
      {
        data: "a_domicilio",
        render: function (data, type, row) {
          if (parseFloat(data) > 0) {
            return '<span class="badge bg-success">S/ ' + data + "</span>";
          } else {
            return '<span class="badge bg-secondary">-</span>';
          }
        },
      },
      // ---- ESTADO PAGO ----
      {
        data: "estado_pago",
        render: function (data, type, row) {
          if (data == "PAGADO") {
            return '<span class="badge bg-success">PAGADO</span>';
          } else if (data == "ANULADO") {
            return '<span class="badge bg-danger text-danger">ANULADO</span>';
          } else {
            return '<span class="badge bg-warning text-dark">POR PAGAR</span>';
          }
        },
      },

      // ---- ESTADO ENCOMIENDA CON USUARIO ----
      {
        data: null,
        render: function (data, type, row) {
          let estadoBadge = "";
          let usuario = row.usu_nombre || "Sistema";
          let fechaUpdate = row.fecha_formateada3 || "";

          switch (row.estado_encomienda) {
            case "PENDIENTE":
              estadoBadge =
                '<span class="badge bg-warning text-dark">PENDIENTE</span>';
              break;
            case "ENTREGADO":
              estadoBadge = '<span class="badge bg-success">ENTREGADO</span>';
              break;
            case "OBSERVADO":
              estadoBadge = '<span class="badge bg-danger">OBSERVADO</span>';
              break;
            case "EN TRANSITO":
              estadoBadge =
                '<span class="badge bg-info text-dark">EN TRÁNSITO</span>';
              break;
            case "EN AGENCIA":
              estadoBadge = '<span class="badge bg-primary">EN AGENCIA</span>';
              break;
            case "ANULADO":
              estadoBadge = '<span class="badge bg-secondary">ANULADO</span>';
              break;
            default:
              estadoBadge =
                '<span class="badge bg-light text-dark">' +
                row.estado_encomienda +
                "</span>";
          }

          return `
            <div style="text-align: center;">
              ${estadoBadge}
              <br>
              <small style="color: #6c757d; font-size: 1.0rem;">
                <i class="fas fa-user" style="font-size: 1.0rem;"></i> ${usuario}
                ${
                  fechaUpdate
                    ? '<br><i class="fas fa-clock" style="font-size: 0.7rem;"></i> ' +
                      fechaUpdate
                    : ""
                }
              </small>
            </div>
          `;
        },
      },
      ...columnasPropias,
    ],
    language: idioma_espanol,
    select: true,
  };
}
