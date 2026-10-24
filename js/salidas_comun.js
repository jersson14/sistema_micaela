// ============================================================
// SALIDAS DIARIAS - funciones comunes
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
      $("#txt_nombre_pasajero").val(d.nombre_completo);
      $("#txt_edad").val(d.edad);
      $("#txt_cel_pasajero").val(d.celular);
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
      $("#txt_nombre_pasajero_editar").val(d.nombre_completo);
      $("#txt_edad_editar").val(d.edad);
      $("#txt_cel_pasajero_editar").val(d.celular);
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

function Modificar_Estatus_Salida_Diaria(id) {
  let idusu = document.getElementById("txtprincipalid").value;

  $.ajax({
    url: "../controller/salidas_diarias/controlador_modificar_estado_viaje.php",
    type: "POST",
    data: {
      id: id,
      idusu: idusu,
    },
  }).done(function (resp) {
    if (resp > 0) {
      Swal.fire(
        "Mensaje de Confirmación",
        "Se COMPLETO con exito el viaje del conductor",
        "success"
      ).then((value) => {
        tbl_salidas_diarias.ajax.reload();
      });
    } else {
      return Swal.fire(
        "Mensaje de Error",
        EstadosOperacion.mensaje(resp, "No se completo la actualización"),
        "error"
      );
    }
  });
}

function Modificar_Estatus_Salida_Incompleta_Diaria(id, observacion, llegaron) {
  if (observacion.trim() === "") {
    return Swal.fire(
      "Mensaje de Error",
      "La observación es obligatoria para marcar el viaje como INCOMPLETO.",
      "error"
    );
  }

  $.ajax({
    url: "../controller/salidas_diarias/controlador_modificar_estado_incompleto_viaje.php",
    type: "POST",
    data: {
      id: id,
      observacion: observacion,
      llegaron: JSON.stringify(llegaron || []),
    },
  }).done(function (resp) {
    if (resp > 0) {
      Swal.fire(
        "Mensaje de Confirmación",
        "Se cambió a INCOMPLETO el viaje del conductor",
        "success"
      ).then(() => {
        tbl_salidas_diarias.ajax.reload();
      });
    } else {
      Swal.fire("Mensaje de Error", EstadosOperacion.mensaje(resp, "No se completó la actualización"), "error");
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
       $("#select_origen, #select_destino").off('change').on('change', function() {
      let origen = $("#select_origen").val();
      let destino = $("#select_destino").val();
      
      // Solo cargar reservas si ambos están seleccionados
      if (origen && destino) {
        Cargar_Select_Reservas();
      }
    });
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
      url: "../controller/salidas_diarias/controlador_listar_estados_salidas.php",
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
        filename: "LISTA_DE_HISTORIAL_ESTADO_SALIDAS",
        title: "LISTA DE HISTORIAL DE ESTADOS DE SALIDAS DIARIAS",
        className: "btn btn-success",
      },
      {
        extend: "pdfHtml5",
        text: " PDF",
        titleAttr: "Exportar a PDF",
        filename: "LISTA_DE_HISTORIAL_ESTADO_SALIDAS",
        title: "LISTA DE HISTORIAL DE ESTADOS DE SALIDAS DIARIAS",
        className: "btn btn-danger",
      },
      {
        extend: "print",
        text: " Imprimir",
        titleAttr: "Imprimir",
        title: "LISTA DE HISTORIAL DE ESTADOS DE SALIDAS DIARIAS",
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
          if (data == "EN TRANSITO") {
            return '<span class="badge bg-dark">EN TRANSITO</span>';
          } else if (data == "COMPLETADO") {
            return '<span class="badge bg-success">COMPLETADO</span>';
          } else if (data == "INCOMPLETO") {
            return '<span class="badge bg-warning">INCOMPLETO</span>';
          } else {
            return '<span class="badge bg-danger">ELIMINADO</span>';
          }
        },
      },
      { data: "observacion" },

      { data: "fecha_formateada" },
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

function removePasajero(boton) {
  var fila = boton.parentNode.parentNode;
  fila.parentNode.removeChild(fila);
  actualizarNumeracion();
  actualizarTotalPasajeros();
}

function actualizarNumeracion() {
  var filas = document.querySelectorAll("#tabla_pasajeros tbody tr");
  filas.forEach((fila, index) => {
    fila.cells[0].innerText = index + 1;
  });
}

function actualizarTotalPasajeros() {
  var total = document.querySelectorAll("#tabla_pasajeros tbody tr").length;
  document.getElementById("total_pasajeros").innerText = total;
}

function verificarDocumento(documento) {
  var filas = document.querySelectorAll("#tabla_pasajeros tbody tr");
  for (var i = 0; i < filas.length; i++) {
    var doc = filas[i].cells[1].innerText;
    if (doc === documento) {
      return true;
    }
  }
  return false;
}

// === Función corregida para cargar encomiendas CON DEPURACIÓN ===
function cargarEncomiendas(idConductor, idOrigen, idDestino) {
  console.log("=== cargarEncomiendas llamada ===");
  console.log("idConductor:", idConductor);
  console.log("idOrigen:", idOrigen);
  console.log("idDestino:", idDestino);

  let tbody = $("#tabla_encomiendas tbody");
  console.log("tbody encontrado:", tbody.length > 0);

  tbody.html('<tr><td colspan="8">Cargando...</td></tr>');

  $.ajax({
    url: "../controller/salidas_diarias/controlador_cargar_encomiendas.php",
    type: "POST",
    data: {
      id_conductor: idConductor,
      id_origen: idOrigen,
      id_destino: idDestino,
    },
    beforeSend: function () {
      console.log("Enviando petición AJAX...");
    },
  })
    .done(function (resp) {
      console.log("Respuesta recibida:", resp);

      try {
        let data = JSON.parse(resp);
        console.log("Datos parseados:", data);

        let html = "";

        if (data.length > 0) {
          console.log("Procesando", data.length, "encomiendas");

          data.forEach((encomienda, index) => {
            console.log("Encomienda", index, ":", encomienda);

            // Ahora que el procedimiento incluye id_encomienda
            const idEncomienda = encomienda.id_encomienda;
            console.log("ID de encomienda extraído:", idEncomienda);

            html += `
                        <tr data-id-encomienda="${idEncomienda}">
                            <td>
                                <input type="checkbox" 
                                    class="check_encomienda" 
                                    value="${idEncomienda}" 
                                    data-id="${idEncomienda}" 
                                    checked>
                            </td>
                            <td>${index + 1}</td>
                            <td>${encomienda.nombre_emisor || "N/A"}</td>
                            <td>${encomienda.nombre_receptor || "N/A"}</td>
                            <td>S/ ${parseFloat(encomienda.pago || 0).toFixed(
                              2
                            )}</td>
                            <td>S/ ${parseFloat(
                              encomienda.por_pagar || 0
                            ).toFixed(2)}</td>
                            <td>${
                              encomienda.a_domicilio == 1 ? "Sí" : "No"
                            }</td>
                            <td>
                                <span class="badge ${
                                  encomienda.estado_pago === "PAGADO"
                                    ? "badge-success"
                                    : encomienda.estado_pago === "POR PAGAR"
                                    ? "badge-danger"
                                    : encomienda.estado_pago === "A DOMICILIO"
                                    ? "badge-success"
                                    : "badge-warning"
                                }">
                                    ${encomienda.estado_pago || "Pendiente"}
                                </span>
                            </td>
                        </tr>`;
          });
        } else {
          console.log("No hay encomiendas disponibles");
          html =
            '<tr><td colspan="8" class="text-muted">No hay encomiendas disponibles para los parámetros seleccionados</td></tr>';
        }

        tbody.html(html);

        // Recontar después de renderizar
        let totalEncomiendas = $(".check_encomienda:checked").length;
        $("#total_encomiendas").text(totalEncomiendas);
        console.log(
          "Tabla actualizada con",
          totalEncomiendas,
          "encomiendas seleccionadas"
        );

        // Actualizar el estado del checkbox "seleccionar todos"
        actualizarCheckboxTodos();
      } catch (error) {
        console.error("Error al procesar los datos:", error);
        console.log("Respuesta que causó el error:", resp);
        tbody.html(
          '<tr><td colspan="8" class="text-danger">Error al procesar los datos</td></tr>'
        );
      }
    })
    .fail(function (xhr, status, error) {
      console.error("Error AJAX:", { xhr, status, error });
      console.log("Status:", xhr.status);
      console.log("ResponseText:", xhr.responseText);
      tbody.html(
        '<tr><td colspan="8" class="text-danger">Error al cargar las encomiendas. Intente nuevamente.</td></tr>'
      );
    });
}

// === Función para actualizar el estado del checkbox "Seleccionar todos" ===
function actualizarCheckboxTodos() {
  console.log("Actualizando checkbox todos");
  const checkboxes = $(".check_encomienda");
  const checkboxTodos = $("#check_all_encomiendas");

  console.log("Checkboxes encontrados:", checkboxes.length);

  if (checkboxes.length === 0) {
    checkboxTodos.prop("checked", false).prop("indeterminate", false);
    return;
  }

  const marcados = checkboxes.filter(":checked").length;
  console.log("Checkboxes marcados:", marcados);

  if (marcados === 0) {
    checkboxTodos.prop("checked", false).prop("indeterminate", false);
  } else if (marcados === checkboxes.length) {
    checkboxTodos.prop("checked", true).prop("indeterminate", false);
  } else {
    checkboxTodos.prop("checked", false).prop("indeterminate", true);
  }
}

// Obtener todos los IDs de encomiendas seleccionadas
function obtenerEncomiendasSeleccionadas() {
  let idsSeleccionados = [];

  $(".check_encomienda:checked").each(function () {
    let id = $(this).val(); // o $(this).data('id')
    idsSeleccionados.push(id);
  });

  return idsSeleccionados;
}

// Pasajeros agregados en la tabla del formulario de registro
function Leer_Pasajeros_Tabla() {
  const pasajeros = [];
  document.querySelectorAll("#tabla_pasajeros tbody tr").forEach((row) => {
    const celdas = row.cells;
    if (!celdas || celdas.length < 6) return;
    pasajeros.push({
      tipo_documento: celdas[1].innerText.trim(),
      documento: celdas[2].innerText.trim(),
      nombres: celdas[3].innerText.trim(),
      edad: celdas[4].innerText.trim(),
      celular: celdas[5].innerText.trim(),
    });
  });
  return pasajeros;
}

// Función para listar pasajeros
function listar_pasajeros(id) {
  // Destruir tabla existente si existe
  if ($.fn.DataTable.isDataTable("#tabla_pasajeros_mostrar")) {
    $("#tabla_pasajeros_mostrar").DataTable().destroy();
  }

  tbl_detalle_pasajeros = $("#tabla_pasajeros_mostrar").DataTable({
    ordering: false,
    bLengthChange: false,
    searching: false,
    paging: false,
    info: false,
    processing: false,
    dom: "t",
    columnDefs: [
      {
        targets: "_all",
        className: "text-center",
      },
    ],
    ajax: {
      url: "../controller/salidas_diarias/controlador_listar_detalle_pasajeros.php",
      type: "POST",
      data: { id: id },
      dataSrc: function (json) {
        console.log("Respuesta JSON pasajeros:", json);
        // Actualizar contador de pasajeros
        if (json.data && json.data.length > 0) {
          $("#total_pasajeros_mostrar").text(json.data.length);
        } else {
          $("#total_pasajeros_mostrar").text(0);
        }
        return json.data || []; // sin filas el servidor puede no enviar data
      },
      error: function (xhr, error, thrown) {
        console.error("Error cargando pasajeros:", error, thrown);
        $("#total_pasajeros_mostrar").text(0);
      },
    },
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
        width: "8%",
      },
      {
        data: "tipo_documento",
        width: "15%",
      },
      {
        data: "nro_documento",
        width: "15%",
      },
      {
        data: "nombre_completo",
        width: "30%",
      },
      {
        data: "edad",
        width: "10%",
      },
      {
        data: "celular",
        width: "15%",
      },
    ],
    language: {
      emptyTable: "No hay pasajeros registrados",
      zeroRecords: "No se encontraron pasajeros",
      loadingRecords: "Cargando pasajeros...",
    },
  });
}

// Función para listar encomiendas
function listar_encomiendas(id) {
  // Destruir tabla existente si existe
  if ($.fn.DataTable.isDataTable("#tabla_encomiendas_mostrar")) {
    $("#tabla_encomiendas_mostrar").DataTable().destroy();
  }

  tbl_detalle_encomiendas = $("#tabla_encomiendas_mostrar").DataTable({
    ordering: false,
    bLengthChange: false,
    searching: false,
    paging: false,
    info: false,
    processing: true, // Cambiado a true para mostrar "Cargando..."
    dom: "t",
    columnDefs: [
      {
        targets: [0, 1, 2, 3, 4, 5],
        className: "text-center",
      },
      {
        targets: [6],
        className: "text-center align-middle",
      },
    ],
    ajax: {
      url: "../controller/salidas_diarias/controlador_listar_detalle_encomiendas.php",
      type: "POST",
      data: { id: id },
      dataSrc: function (json) {
        console.log("Respuesta JSON encomiendas:", json);
        
        // Validar que json y json.data existan
        if (!json || typeof json !== 'object') {
          console.error("Respuesta inválida del servidor");
          $("#total_encomiendas_mostrar").text(0);
          return [];
        }
        
        // Si no tiene la propiedad data, crear un array vacío
        if (!json.data || !Array.isArray(json.data)) {
          console.log("No hay datos de encomiendas o formato incorrecto");
          $("#total_encomiendas_mostrar").text(0);
          return [];
        }
        
        // Actualizar contador de encomiendas
        $("#total_encomiendas_mostrar").text(json.data.length);
        return json.data || []; // sin filas el servidor puede no enviar data
      },
      error: function (xhr, error, thrown) {
        console.error("Error cargando encomiendas:", {
          status: xhr.status,
          error: error,
          thrown: thrown,
          response: xhr.responseText
        });
        $("#total_encomiendas_mostrar").text(0);
        
        // Mostrar mensaje de error al usuario
        Swal.fire({
          icon: 'error',
          title: 'Error al cargar encomiendas',
          text: 'No se pudieron cargar las encomiendas. Por favor, intente nuevamente.',
          timer: 3000,
          showConfirmButton: false
        });
      },
    },
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
        width: "8%",
      },
      {
        data: "emisor",
        width: "20%",
      },
      {
        data: "receptor",
        width: "20%",
      },
      {
        data: "pago",
        width: "12%",
        render: function (data, type, row) {
          return "S/ " + parseFloat(data || 0).toFixed(2);
        },
      },
      {
        data: "por_pagar",
        width: "12%",
        render: function (data, type, row) {
          return "S/ " + parseFloat(data || 0).toFixed(2);
        },
      },
      {
        data: "a_domicilio",
        width: "13%",
        render: function (data, type, row) {
          return data == "1" || data == "SI"
            ? '<span class="badge badge-info">SÍ</span>'
            : '<span class="badge badge-secondary">NO</span>';
        },
      },
      {
        data: "estado_pago",
        width: "15%",
        render: function (data, type, row) {
          let badgeClass =
            data == "PAGADO"
              ? "badge-success"
              : data == "POR PAGAR"
              ? "badge-danger"
              : data == "A DOMICILIO"
              ? "badge-warning"
              : "badge-secondary";

          return '<span class="badge ' + badgeClass + '">' + data + "</span>";
        },
      },
    ],
    language: {
      emptyTable: "No hay encomiendas registradas",
      zeroRecords: "No se encontraron encomiendas",
      loadingRecords: "Cargando encomiendas...",
      processing: "Cargando...",
    },
  });
}

// Función para listar pasajeros
function listar_pasajerosEditar(id) {
  // Destruir tabla existente si existe
  if ($.fn.DataTable.isDataTable("#tabla_pasajeros_editar")) {
    $("#tabla_pasajeros_editar").DataTable().destroy();
  }

  tbl_detalle_pasajerosEditar = $("#tabla_pasajeros_editar").DataTable({
    ordering: false,
    bLengthChange: false,
    searching: false,
    paging: false,
    info: false,
    processing: false,
    dom: "t",
    columnDefs: [
      {
        targets: "_all",
        className: "text-center",
      },
    ],
    ajax: {
      url: "../controller/salidas_diarias/controlador_listar_detalle_pasajeros.php",
      type: "POST",
      data: { id: id },
      dataSrc: function (json) {
        console.log("Respuesta JSON pasajeros:", json);
        // Actualizar contador de pasajeros
        if (json.data && json.data.length > 0) {
          $("#total_pasajeros_editar").text(json.data.length);
        } else {
          $("#total_pasajeros_editar").text(0);
        }
        return json.data || []; // sin filas el servidor puede no enviar data
      },
      error: function (xhr, error, thrown) {
        console.error("Error cargando pasajeros:", error, thrown);
        $("#total_pasajeros_editar").text(0);
      },
    },
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
        width: "8%",
      },
      {
        data: "tipo_documento",
        width: "15%",
      },
      {
        data: "nro_documento",
        width: "15%",
      },
      {
        data: "nombre_completo",
        width: "30%",
      },
      {
        data: "edad",
        width: "10%",
      },
      {
        data: "celular",
        width: "15%",
      },
      {
        data: null,
        render: function (data, type, row) {
          return (
            '<button class="btn btn-danger btn-sm" onclick="eliminarPasajeroEditar(' +
            row.id_cliente_salida +
            ')" title="Eliminar pasajero">' +
            '<i class="fas fa-trash"></i>' +
            "</button>"
          );
        },
        width: "7%",
      },
    ],
    language: {
      emptyTable: "No hay pasajeros registrados",
      zeroRecords: "No se encontraron pasajeros",
      loadingRecords: "Cargando pasajeros...",
    },
  });
}

// Función para eliminar pasajero
function eliminarPasajeroEditar(idPasajero) {
  Swal.fire({
    title: "¿Está seguro?",
    text: "¿Desea eliminar este pasajero de la salida?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../controller/salidas_diarias/controlador_eliminar_pasajero_salida.php",
        type: "POST",
        data: {
          id_pasajero: idPasajero,
        },
        success: function (response) {
          console.log("Respuesta del servidor:", response); // Para depuración

          // Verificar si la respuesta es mayor a 0
          if (response > 0) {
            Swal.fire(
              "Eliminado",
              "El pasajero ha sido eliminado correctamente.",
              "success"
            );
            // Recargar la tabla de pasajeros
            if (typeof tbl_detalle_pasajerosEditar !== "undefined") {
              tbl_detalle_pasajerosEditar.ajax.reload();
            }
          } else {
            Swal.fire("Error", "No se pudo eliminar el pasajero.", "error");
          }
        },
        error: function (xhr, status, error) {
          console.error("Error:", error);
          Swal.fire(
            "Error",
            "Ocurrió un error al eliminar el pasajero.",
            "error"
          );
        },
      });
    }
  });
}

function agregarPasajeroEditar() {
  var tipodocumento = $("#select_tipo_documento_emisor_editar").val();
  var documento = $("#txt_dni_emisor_editar").val();
  var documento2 = $("#txt_dni_emisor2_editar").val();
  var nombres = $("#txt_nombre_pasajero_editar").val();
  var edad = $("#txt_edad_editar").val();
  var celular = $("#txt_cel_pasajero_editar").val();

  let documentoFinal = "";
  if (tipodocumento === "DNI") {
    if (!documento) {
      return Swal.fire(
        "Mensaje de Advertencia",
        "El campo DNI es obligatorio",
        "warning"
      );
    }
    documentoFinal = documento;
  } else {
    if (!documento2) {
      return Swal.fire(
        "Mensaje de Advertencia",
        "El campo de documento es obligatorio",
        "warning"
      );
    }
    documentoFinal = documento2;
  }

  if (
    !documentoFinal ||
    documentoFinal.trim() === "" ||
    !nombres ||
    nombres.trim() === ""
  ) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Complete los campos obligatorios",
      "warning"
    );
  }

  if (verificarDocumentEditar(documentoFinal)) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "El pasajero ya fue agregado a la tabla",
      "warning"
    );
  }

  var filasExistentes = document.querySelectorAll(
    "#tabla_pasajeros_editar tbody tr"
  ).length;
  var fila = "<tr class='pasajero-nuevo'>";
  fila += "<td>" + (filasExistentes + 1) + "</td>";
  fila += "<td>" + tipodocumento + "</td>";
  fila += "<td>" + documentoFinal + "</td>";
  fila += "<td>" + nombres + "</td>";
  fila += "<td>" + (edad || "N/A") + "</td>";
  fila += "<td>" + (celular || "N/A") + "</td>";
  fila +=
    "<td><button class='btn btn-danger' onclick='removePasajeroEditar(this)'><i class='fas fa-trash'></i></button></td>";
  fila += "</tr>";

  $("#tabla_pasajeros_editar tbody").append(fila);
  actualizarTotalPasajeros_Editar();

  // Limpiar campos
  $("#txt_dni_emisor_editar").val("");
  $("#txt_nombre_pasajero_editar").val("");
  $("#txt_edad_editar").val("");
  $("#txt_cel_pasajero_editar").val("");
}

function removePasajeroEditar(boton) {
  var fila = boton.parentNode.parentNode;
  fila.parentNode.removeChild(fila);
  actualizarNumeracionEditar();
  actualizarTotalPasajeros_Editar();
}

function actualizarNumeracionEditar() {
  var filas = document.querySelectorAll("#tabla_pasajeros_editar tbody tr");
  filas.forEach((fila, index) => {
    fila.cells[0].innerText = index + 1;
  });
}

function actualizarTotalPasajeros_Editar() {
  var total = document.querySelectorAll(
    "#tabla_pasajeros_editar tbody tr"
  ).length;
  document.getElementById("total_pasajeros_editar").innerText = total;
}

function verificarDocumentEditar(documento) {
  var filas = document.querySelectorAll("#tabla_pasajeros_editar tbody tr");
  for (var i = 0; i < filas.length; i++) {
    var doc = filas[i].cells[1].innerText;
    if (doc === documento) {
      return true;
    }
  }
  return false;
}

function Cargar_Select_Reservas() {
  let ori = $("#select_origen").val();
  let des = $("#select_destino").val();
  
  if (!ori || !des) {
    $("#select_reservas").html("<option value='' disabled selected>Primero seleccione origen y destino</option>");
    return;
  }
  
  $.ajax({
    url: "../controller/reservas/controlador_cargar_select_reservas.php",
    type: "POST",
    data: { ori: ori, des: des },
  }).done(function (resp) {
    let data = JSON.parse(resp);
    let cadena = "<option value='' disabled selected>Seleccione una reserva</option>";
    
    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) {
        // data[i][0] = id_reserva
        // data[i][1] = tipo_documento
        // data[i][2] = nro_documento
        // data[i][3] = nombre_completo
        // data[i][4] = celular
        // data[i][5] = edad
        cadena +=
          "<option value='" + data[i][0] + "'" +
          " data-tipodoc='" + data[i][1] + "'" +
          " data-dni='" + data[i][2] + "'" +
          " data-nombre='" + data[i][3] + "'" +
          " data-celular='" + data[i][4] + "'" +
          " data-edad='" + data[i][5] + "'>" +
          "DNI: " + data[i][2] + 
          " - " + data[i][3] + 
          " - CEL: " + data[i][4] + 
          "</option>";
      }
    } else {
      cadena += "<option value=''>No hay reservas disponibles</option>";
    }
    $("#select_reservas").html(cadena);
  });
}

function agregarPasajeroDesdeReserva() {
  var reservaSeleccionada = $("#select_reservas").val();
  
  if (!reservaSeleccionada) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Debe seleccionar una reserva",
      "warning"
    );
  }

  var optionSeleccionado = $("#select_reservas option:selected");
  var tipodoc = optionSeleccionado.data("tipodoc");
  var dni = optionSeleccionado.data("dni");
  var nombre = optionSeleccionado.data("nombre");
  var celular = optionSeleccionado.data("celular");
  var edad = optionSeleccionado.data("edad");

  if (verificarDocumento(dni)) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Este pasajero ya fue agregado a la tabla",
      "warning"
    );
  }

  var filasExistentes = document.querySelectorAll("#tabla_pasajeros tbody tr").length;
  var fila = "<tr>";
  fila += "<td>" + (filasExistentes + 1) + "</td>";
  fila += "<td>" + tipodoc + "</td>";
  fila += "<td>" + dni + "</td>";
  fila += "<td>" + nombre + "</td>";
  fila += "<td>" + (edad || "N/A") + "</td>";
  fila += "<td>" + (celular || "N/A") + "</td>";
  fila += "<td><button class='btn btn-danger btn-sm' onclick='removePasajero(this)'><i class='fas fa-trash'></i></button></td>";
  fila += "</tr>";

  $("#tabla_pasajeros tbody").append(fila);
  actualizarTotalPasajeros();

  $("#select_reservas").val('').trigger('change');

 
}

// Configuración de la tabla de salidas diarias (asistente y conductor).
// Es la misma en todas las listas; cada una solo indica de dónde lee los datos (ajax).
function Opciones_Tabla_Salidas_Sucursal(ajax) {
  return Opciones_Tabla_Salidas_Base(ajax, [
     {
    data: null,
    render: function (data, type, row) {
        return (
        row.usuario_nombre_completo +
        " - <br><small><strong>" + 
        row.rol + // o el campo que contenga el rol
        "</strong></small>"
        );
    },
    },

      // ---- BOTONES DE ACCIÓN ----
      {
        data: "estado",
        render: function (data, type, row) {
          let botones = "";

 // SOLO EN TRANSITO => Completar viaje
          if (data === "EN TRANSITO") {
            botones += `
            <button class='editar btn btn-primary btn-sm' title='Editar datos de servicio'>
                <i class='fa fa-edit'></i> Editar
              </button>
        
              

            `;
          }

          // EN TRANSITO / INCOMPLETO / COMPLETADO => Manifiesto + Mostrar
          if (
            data === "COMPLETADO" ||
            data === "EN TRANSITO" ||
            data === "INCOMPLETO"
          ) {
            botones += `

              <button class='imprimir btn btn-success btn-sm' title='Imprimir Manifiesto'>
                <i class='fa fa-print'></i> Manifiesto
              </button>
              <button class='mostrar btn btn-secondary btn-sm' title='Mostrar'>
                <i class='fa fa-eye'></i> Mostrar
              </button>
              <button class='historial btn btn-dark btn-sm' title='Historial de viaje'>
                <i class='fa fa-history'></i> Historial
              </button>
            `;
          }

          // SOLO EN TRANSITO => Completar viaje
          if (data === "EN TRANSITO") {
            botones += `

              <button class='completar btn btn-info btn-sm' title='Completar viaje'>
                <i class='fa fa-check-circle'></i> Completar
              </button>
              <button class='incompleto btn btn-warning btn-sm' title='Viaje Incompleto'>
                <i class='fa fa-times-circle'></i> Incompleto
              </button>
              

            `;
          }

          // ELIMINADO => Solo Mostrar
          if (data === "ELIMINADO") {
            botones += `
              <button class='mostrar btn btn-secondary btn-sm' title='Mostrar'>
                <i class='fa fa-eye'></i> Mostrar
              </button>
              <button class='historial btn btn-dark btn-sm' title='Historial de viaje'>
                <i class='fa fa-history'></i> Historial
              </button>

            `;
          }

          return botones;
        },
      },
  ]);
}

// Configuración base de la tabla de salidas diarias.
// Igual para todos los roles; cada rol pasa sus columnas propias.
function Opciones_Tabla_Salidas_Base(ajax, columnasPropias) {
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
    pageLength: 10,
    destroy: true,
    async: false,
    processing: true,
    ajax: ajax,
    dom: "Bfrtip",

  buttons: [
  {
    extend: "excelHtml5",
    text: '<i class="fas fa-file-excel"></i> Excel',
    titleAttr: "Exportar a Excel",
    filename: "LISTA DE SALIDAS DIARIAS",
    title: "LISTA DE SALIDAS DIARIAS",
    className: "btn btn-success btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9], // SIN la última columna de acciones
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
    filename: "LISTA DE SALIDAS DIARIAS",
    title: "LISTA DE SALIDAS DIARIAS",
    className: "btn btn-danger btn-sm",
    orientation: "landscape",
    pageSize: "A4",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9], // SIN la última columna de acciones
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
    extend: "print",
    text: '<i class="fa fa-print"></i> Imprimir',
    titleAttr: "Imprimir",
    title: "LISTA DE SALIDAS DIARIAS",
    className: "btn btn-info btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9], // SIN la última columna de acciones
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
      { data: "salida_nro" },
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
      {
        data: "monto",
        render: function (data, type, row) {
          if (parseFloat(data) > 0) {
            return '<span class="badge bg-success">S/ ' + data + "</span>";
          } else {
            return '<span class="badge bg-secondary">-</span>';
          }
        },
      },
      { data: "fecha_formateada_salida" },
      { data: "origen_nombre" },
      { data: "destino_nombre" },

      // TOTAL PASAJEROS más grande
      {
        data: "total_pasajeros",
        render: function (data, type, row) {
          return `<span style="font-size:18px; font-weight:bold;">${data}</span>`;
        },
      },

      // TOTAL ENCOMIENDAS más grande
      {
        data: "total_encomiendas",
        render: function (data, type, row) {
          return `<span style="font-size:18px; font-weight:bold;">${data}</span>`;
        },
      },

      // ---- ESTADO SALIDA ----
      {
        data: "estado",
        render: function (data, type, row) {
          if (data == "EN TRANSITO") {
            return '<span class="badge bg-dark">EN TRANSITO</span>';
          } else if (data == "COMPLETADO") {
            return '<span class="badge bg-success">COMPLETADO</span>';
          } else if (data == "INCOMPLETO") {
            return '<span class="badge bg-warning">INCOMPLETO</span>';
          } else {
            return '<span class="badge bg-danger">ELIMINADO</span>';
          }
        },
      },
      ...columnasPropias,
    ],
    language: idioma_espanol,
    select: true,
  };
}
