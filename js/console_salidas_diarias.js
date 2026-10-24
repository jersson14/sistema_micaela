var tbl_salidas_diarias;
function listar_salidas_diarias_dia() {


  tbl_salidas_diarias = $("#tabla_salida_diaria").DataTable(Opciones_Tabla_Salidas({
      url: "../controller/salidas_diarias/controlador_listar_salidas_diarias_pordia.php",
      type: "POST",
    }));
}
function listar_salidas_diarias() {
  Cargar_Select_Usuarios();
  Cargar_Select_Rutas();
  document.getElementById("txt_fecha_desde").value = "";
  document.getElementById("txt_fecha_hasta").value = "";
  document.getElementById("select_estado_buscar").value = "";

  tbl_salidas_diarias = $("#tabla_salida_diaria").DataTable(Opciones_Tabla_Salidas({
      url: "../controller/salidas_diarias/controlador_listar_salidas_diarias.php",
      type: "POST",
    }));
}

function listar_salidas_diarias_ruta_estado() {
  let ori = document.getElementById("select_origen_bus").value;
  let des = document.getElementById("select_destino_bus").value;
  let esta = document.getElementById("select_estado_buscar").value;

  tbl_salidas_diarias = $("#tabla_salida_diaria").DataTable(Opciones_Tabla_Salidas({
      url: "../controller/salidas_diarias/controlador_listar_salidas_diarias_ruta_estado.php",
      type: "POST",
      data: {
        ori: ori,
        des: des,
        esta: esta,
      },
    }));
}

function listar_salidas_diarias_fecha_usu() {
  let fedes = document.getElementById("txt_fecha_desde").value;
  let fehas = document.getElementById("txt_fecha_hasta").value;
  let usu = document.getElementById("select_usuario").value;

  tbl_salidas_diarias = $("#tabla_salida_diaria").DataTable(Opciones_Tabla_Salidas({
      url: "../controller/salidas_diarias/controlador_listar_salidas_diarias_fecha_usu.php",
      type: "POST",
      data: {
        fedes: fedes,
        fehas: fehas,
        usu: usu,
      },
    }));
}

//ABRIR MODAL REGISTRO
function AbrirRegistro() {
  console.log("Abriendo modal de registro");
  
  // 1. PRIMERO: Cargar los datos en los selects
  Cargar_Select_Conductores();
  Cargar_Select_Rutas();
  
  // 2. SEGUNDO: Inicializar Select2 si no está inicializado
  // Usar setTimeout para asegurar que los datos se carguen primero
  setTimeout(function() {
    if (!$("#select_conductor").hasClass("select2-hidden-accessible")) {
      $("#select_conductor").select2({
        placeholder: "Seleccionar Conductor",
        allowClear: true,
        dropdownParent: $("#modal_registro"),
      });
      console.log("Select2 conductor inicializado");
    }
    
    if (!$("#select_origen").hasClass("select2-hidden-accessible")) {
      $("#select_origen").select2({
        placeholder: "Seleccionar Origen",
        allowClear: true,
        dropdownParent: $("#modal_registro"),
      });
      console.log("Select2 origen inicializado");
    }
    
    if (!$("#select_destino").hasClass("select2-hidden-accessible")) {
      $("#select_destino").select2({
        placeholder: "Seleccionar Destino",
        allowClear: true,
        dropdownParent: $("#modal_registro"),
      });
      console.log("Select2 destino inicializado");
    }
    
 const now = new Date();

const fechaLima = new Intl.DateTimeFormat('sv-SE', {
  timeZone: 'America/Lima',
  year: 'numeric',
  month: '2-digit',
  day: '2-digit',
  hour: '2-digit',
  minute: '2-digit',
  hour12: false
}).format(now).replace(' ', 'T');

$("#txt_fecha_creacion").val(fechaLima);
console.log("Fecha establecida:", fechaLima);

    
  }, 300); // Esperar 300ms para que los datos se carguen
  
  // 3. TERCERO: Abrir el modal
  $("#modal_registro").modal({ backdrop: "static", keyboard: false });
  $("#modal_registro").modal("show");
}

// Una sola vez cuando se abre el modal
$("#modal_registro").on("shown.bs.modal", function () {
  $("#select_conductor").select2({
    placeholder: "Seleccionar Conductor",
    allowClear: true,
    dropdownParent: $("#modal_registro"),
  });
});

//REALIZAR BUSQUEDA CLIENTE



$("#tabla_salida_diaria").on("click", ".completar", function () {
  var data = tbl_salidas_diarias.row($(this).parents("tr")).data();

  if (tbl_salidas_diarias.row(this).child.isShown()) {
    var data = tbl_salidas_diarias.row(this).data();
  }
  Swal.fire({
    title:
      "Desea completar el viaje del conductor " + data.nombres_apellidos + "?",
    text: "Una vez COMPELTADO el viaje, no se podra editar ni eliminar la salida diaria, a su vez las encomiendas se pondran en estado EN AGENCIA en el destino final",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#005CA5",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Completar!",
    cancelButtonText: "Cancelar",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      Modificar_Estatus_Salida_Diaria(data.id_salidas_diarias);
    }
  });
});


$("#tabla_salida_diaria").on("click", ".incompleto", function () {
  var data = tbl_salidas_diarias.row($(this).parents("tr")).data();

  if (tbl_salidas_diarias.row(this).child.isShown()) {
    var data = tbl_salidas_diarias.row(this).data();
  }

  // Encomiendas de la salida, para marcar cuáles sí llegaron
  $.ajax({
    url: "../controller/salidas_diarias/controlador_listar_detalle_encomiendas.php",
    type: "POST",
    data: { id: data.id_salidas_diarias },
    dataType: "json",
  })
    .done(function (resp) {
      const encomiendas = (resp && (resp.data || resp.aaData)) || [];
      const texto = (valor) => $("<div>").text(valor || "").html();
      const lista =
        encomiendas.length === 0
          ? "<p class='text-muted mb-0'>Esta salida no lleva encomiendas.</p>"
          : encomiendas
              .map(
                (e) => `
            <div class="form-check text-left">
              <input class="form-check-input chk_llego" type="checkbox" value="${texto(e.id_encomienda)}" id="llego_${texto(e.id_encomienda)}">
              <label class="form-check-label" for="llego_${texto(e.id_encomienda)}">
                N° ${texto(e.id_encomienda)}: ${texto(e.emisor)} → ${texto(e.receptor)}
              </label>
            </div>`
              )
              .join("");

      Swal.fire({
        title: "Marcar como INCOMPLETO el viaje de " + texto(data.nombres_apellidos) + "?",
        html: `
          <p class="text-left mb-2">Marque las encomiendas que <b>sí llegaron</b>: pasan a EN AGENCIA.
          Las demás quedan <b>OBSERVADAS</b> con el motivo, para reenviarlas o anularlas.</p>
          <div style="max-height:220px; overflow:auto; border:1px solid #ddd; border-radius:4px; padding:8px;">${lista}</div>
          <textarea id="txt_motivo_incompleto" class="swal2-textarea" placeholder="Motivo (accidente, desvío, etc.)"></textarea>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#005CA5",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, cambiar!",
        cancelButtonText: "Cancelar",
        reverseButtons: true,
        preConfirm: () => {
          const motivo = document.getElementById("txt_motivo_incompleto").value.trim();
          if (!motivo) {
            Swal.showValidationMessage("Debe ingresar el motivo para continuar");
            return false;
          }
          const llegaron = Array.from(document.querySelectorAll(".chk_llego:checked")).map((c) => c.value);
          return { motivo: motivo, llegaron: llegaron };
        },
      }).then((result) => {
        if (result.isConfirmed) {
          Modificar_Estatus_Salida_Incompleta_Diaria(
            data.id_salidas_diarias,
            result.value.motivo,
            result.value.llegaron
          );
        }
      });
    })
    .fail(function () {
      Swal.fire("Mensaje de Error", "No se pudieron cargar las encomiendas de la salida", "error");
    });
});


//REGISTROS DE ENCOMIENDAS


//ELIMINAR AREAS
function Eliminar_Salida(id) {
  $.ajax({
    url: "../controller/salidas_diarias/controlador_eliminar_salida_diaria.php",
    type: "POST",
    data: {
      id: id,
    },
  }).done(function (resp) {
    if (resp > 0) {
      Swal.fire(
        "Mensaje de Confirmación",
        "Se elimino la salida diaria con exito, si desea recuperarlo, tendra que volver a registrarlo",
        "success"
      ).then((value) => {
        tbl_salidas_diarias.ajax.reload();
      });
    } else {
      return Swal.fire(
        "Mensaje de Advetencia",
        EstadosOperacion.mensaje(resp, "No se puede eliminar la salida diaria, verifique por favor"),
        "warning"
      );
    }
  });
}

//ENVIANDO AL BOTON DELETE
$("#tabla_salida_diaria").on("click", ".eliminar", function () {
  var data = tbl_salidas_diarias.row($(this).parents("tr")).data();

  if (tbl_salidas_diarias.row(this).child.isShown()) {
    var data = tbl_salidas_diarias.row(this).data();
  }
  Swal.fire({
    title:
      "Desea eliminar la salida diaria registrada el: " +
      data.fecha_formateada_salida +
      " del conductor: " +
      data.nombres_apellidos +
      "?",
    text: "Una vez aceptado la salida diaria sera eliminado, sin poder recuperarlo, tendra que volver a registrarlo!!!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Eliminar",
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_Salida(data.id_salidas_diarias);
    }
  });
});

function Cargar_Select_Rutas() {
  Cargar_Select_Reservas();
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

// Una sola vez cuando se abre el modal
$("#modal_registro").on("shown.bs.modal", function () {
  $("#select_origen").select2({
    placeholder: "Seleccionar origen",
    allowClear: true,
    dropdownParent: $("#modal_registro"),
  });
});
// Una sola vez cuando se abre el modal
$("#modal_registro").on("shown.bs.modal", function () {
  $("#select_destino").select2({
    placeholder: "Seleccionar destino",
    allowClear: true,
    dropdownParent: $("#modal_registro"),
  });
});

// Una sola vez cuando se abre el modal
$("#modal_editar").on("shown.bs.modal", function () {
  $("#select_origen_editar").select2({
    placeholder: "Seleccionar origen",
    allowClear: true,
    dropdownParent: $("#modal_editar"),
  });
});
// Una sola vez cuando se abre el modal
$("#modal_editar").on("shown.bs.modal", function () {
  $("#select_destino_editar").select2({
    placeholder: "Seleccionar destino",
    allowClear: true,
    dropdownParent: $("#modal_editar"),
  });
});



//VER MODAL DE HISTORIAL DE ESTADOS

//MODAL VER HISTORIAL
$("#tabla_salida_diaria").on("click", ".historial", function () {
  var data = tbl_salidas_diarias.row($(this).parents("tr")).data();

  if (tbl_salidas_diarias.row(this).child.isShown()) {
    var data = tbl_salidas_diarias.row(this).data();
  }
  $("#modal_ver_historial").modal("show");

  document.getElementById("lb_titulo_historial").innerHTML =
    "<b>HISTORIAL DE LA SALIDA DEL CONDUCTOR :</b> " +
    data.nombres_apellidos +
    " - <br><b>FECHA DE SALIDA :</b> " +
    data.fecha_formateada_salida;

  listar_historial_estado(data.id_salidas_diarias);
});
// VISTA DE HISTORIAL
var tbl_historial_estado;




// IMPRIMIR MANIFIESTO
$("#tabla_salida_diaria").on("click", ".imprimir", function () {
  var data = tbl_salidas_diarias.row($(this).parents("tr")).data();

  if (tbl_salidas_diarias.row(this).child.isShown()) {
    var data = tbl_salidas_diarias.row(this).data();
  }
  var url =
    "../view/MPDF/REPORTE/manifiesto.php?id=" +
    encodeURIComponent(data.id_salidas_diarias) +
    "#zoom=100%";

  // Abrir una nueva ventana con la URL construida
  var newWindow = window.open(url, "MANIFIESTO", "scrollbars=NO");

  // Asegurarse de que la ventana se abre en tamaño máximo
  if (newWindow) {
    newWindow.moveTo(0, 0);
    newWindow.resizeTo(screen.width, screen.height);
  }
});

// AGREGAR PASAJERAS A LA TABLA

function agregarPasajero() {
  var tipodocumento = $("#select_tipo_documento_emisor").val();
  var documento = $("#txt_dni_emisor").val();
  var documento2 = $("#txt_dni_emisor2").val();
  var nombres = $("#txt_nombre_pasajero").val();
  var edad = $("#txt_edad").val();
  var celular = $("#txt_cel_pasajero").val();

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

  // Validar edad
  if (edad && parseInt(edad) >= 100) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "La edad no puede ser mayor a 100",
      "warning"
    );
  }

  if (verificarDocumento(documentoFinal)) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "El pasajero ya fue agregado a la tabla",
      "warning"
    );
  }

  var filasExistentes = document.querySelectorAll(
    "#tabla_pasajeros tbody tr"
  ).length;
  var fila = "<tr>";
  fila += "<td>" + (filasExistentes + 1) + "</td>";
  fila += "<td>" + tipodocumento + "</td>";
  fila += "<td>" + documentoFinal + "</td>";
  fila += "<td>" + nombres + "</td>";
  fila += "<td>" + (edad || "N/A") + "</td>";
  fila += "<td>" + (celular || "N/A") + "</td>";
  fila +=
    "<td><button class='btn btn-danger' onclick='removePasajero(this)'><i class='fas fa-trash'></i></button></td>";
  fila += "</tr>";

  $("#tabla_pasajeros tbody").append(fila);
  actualizarTotalPasajeros();

  // Limpiar campos
  $("#txt_dni_emisor").val("");
  $("#txt_nombre_pasajero").val("");
  $("#txt_edad").val("");
  $("#txt_cel_pasajero").val("");
}






//LISTAR ENCOMIENDAS

// VERSIÓN CON DEPURACIÓN COMPLETA
console.log("Script de encomiendas cargado");


// === Eventos del documento ===
$(document).ready(function () {
  console.log("Document ready ejecutado");
  
  // Cargar datos iniciales (opcional, solo si los necesitas fuera del modal)
  // Cargar_Select_Conductores();
  // Cargar_Select_Rutas();
  
  // USAR $(document).on() para elementos dinámicos en modales
  $(document).on("change", "#select_conductor, #select_origen, #select_destino", function () {
    console.log("Cambio detectado en selectores");
    console.log("Elemento que cambió:", this.id);
    
    const idConductor = $("#select_conductor").val();
    const idOrigen = $("#select_origen").val();
    const idDestino = $("#select_destino").val();
    
    console.log("Valores actuales:");
    console.log("- Conductor:", idConductor);
    console.log("- Origen:", idOrigen);
    console.log("- Destino:", idDestino);
    
    // Verificar que los tres parámetros estén seleccionados
    if (idConductor && idOrigen && idDestino) {
      console.log("Todos los parámetros seleccionados, cargando encomiendas...");
      cargarEncomiendas(idConductor, idOrigen, idDestino);
    } else {
      console.log("Faltan parámetros por seleccionar");
      $("#tabla_encomiendas tbody").html(
        '<tr><td colspan="8" class="text-muted">Seleccione conductor, origen y destino para cargar encomiendas</td></tr>'
      );
      $("#total_encomiendas").text(0);
    }
  });
  
  // Manejar checkbox "Seleccionar todos"
  $(document).on("change", "#check_all_encomiendas", function () {
    console.log("Checkbox 'todos' cambiado");
    const isChecked = $(this).is(":checked");
    $(".check_encomienda").prop("checked", isChecked);
    
    let totalEncomiendas = $(".check_encomienda:checked").length;
    $("#total_encomiendas").text(totalEncomiendas);
  });
  
  // Manejar checkboxes individuales
  $(document).on("change", ".check_encomienda", function () {
    console.log("Checkbox individual cambiado");
    actualizarCheckboxTodos();
    
    let totalEncomiendas = $(".check_encomienda:checked").length;
    $("#total_encomiendas").text(totalEncomiendas);
  });
  
  // === Evento cuando se cierra el modal (limpiar todo) ===
  $('#modal_registro').on('hidden.bs.modal', function () {
    console.log("Modal cerrado - Limpiando todo");
    
    // Destruir Select2
    if ($("#select_conductor").hasClass("select2-hidden-accessible")) {
      $("#select_conductor").select2('destroy');
    }
    if ($("#select_origen").hasClass("select2-hidden-accessible")) {
      $("#select_origen").select2('destroy');
    }
    if ($("#select_destino").hasClass("select2-hidden-accessible")) {
      $("#select_destino").select2('destroy');
    }
    
    // Limpiar valores
    $("#select_conductor").val('');
    $("#select_origen").val('');
    $("#select_destino").val('');
    
    // Limpiar tabla
    $("#tabla_encomiendas tbody").html(
      '<tr><td colspan="8" class="text-muted">Seleccione conductor, origen y destino para cargar encomiendas</td></tr>'
    );
    $("#total_encomiendas").text(0);
  });
});
// Limpiar formulario al cerrar modal
$("#modal_registro").on("hidden.bs.modal", function () {
  console.log("Modal ocultado, limpiando formulario");

  // Limpiar selectores
  $("#select_conductor").val("").trigger("change");
  $("#select_origen").val("").trigger("change");
  $("#select_destino").val("").trigger("change");

  // Limpiar tabla de encomiendas
  $("#tabla_encomiendas tbody").html("");
  $("#total_encomiendas").text(0);

  // Limpiar otros campos si es necesario
  $("#txt_descripcion").val("");
});


// Ejemplo de uso
// let encomiendas = obtenerEncomiendasSeleccionadas();
// console.log('IDs seleccionados:', encomiendas); // [1, 3, 5, 7]

//REGISTRAR SALIDA DIARIA

// Registra la salida con sus pasajeros y encomiendas en una sola petición:
// el servidor guarda todo o nada, y el manifiesto se ofrece recién al confirmar
function Registrar_Salida_Diaria() {
  const conductor = document.getElementById("select_conductor").value;
  const monto = document.getElementById("txt_pago").value;
  const fechaHora = document.getElementById("txt_fecha_creacion").value;
  const origen = document.getElementById("select_origen").value;
  const destino = document.getElementById("select_destino").value;
  const observacion = document.getElementById("txt_descripcion").value;

  if (!conductor || !monto || !fechaHora || !origen || !destino) {
    return Swal.fire(
      "Advertencia",
      "Complete todos los campos obligatorios.",
      "warning"
    );
  }
  if (origen === destino) {
    return Swal.fire("Advertencia", "El origen y el destino no pueden ser iguales.", "warning");
  }

  const pasajeros = Leer_Pasajeros_Tabla();
  const encomiendas = $(".check_encomienda:checked").map(function () {
    return $(this).data("id") || $(this).val();
  }).get().filter((id) => id && id !== "undefined" && id !== "null");

  $.ajax({
    url: "../controller/salidas_diarias/controlador_registrar_salida_completa.php",
    type: "POST",
    data: {
      conductor,
      monto,
      fechaHora,
      origen,
      destino,
      observacion,
      pasajeros: JSON.stringify(pasajeros),
      encomiendas: JSON.stringify(encomiendas),
    },
    success: function (resp) {
      if (!(resp > 0)) {
        return Swal.fire(
          "Error",
          EstadosOperacion.mensaje(resp, "No se pudo registrar la salida diaria."),
          "error"
        );
      }

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

      // CONFIRMACIÓN CON OPCIÓN DE IMPRIMIR MANIFIESTO
      Swal.fire({
        title: "Salida diaria registrada correctamente",
        html:
          "Registrada el: <b>" +
          fechaActual +
          "</b><br>Pasajeros: <b>" +
          pasajeros.length +
          "</b><br>Encomiendas: <b>" +
          encomiendas.length +
          "</b><br><br>¿Desea imprimir el manifiesto?",
        icon: "success",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Imprimir Manifiesto!",
        cancelButtonText: "No, gracias",
      }).then((result) => {
        if (result.isConfirmed) {
          var url =
            "../view/MPDF/REPORTE/manifiesto.php?id=" +
            encodeURIComponent(resp) +
            "#zoom=100%";
          var newWindow = window.open(url, "MANIFIESTO", "scrollbars=NO");

          if (newWindow) {
            newWindow.moveTo(0, 0);
            newWindow.resizeTo(screen.width, screen.height);
          }
        }

        $("#modal_registro").modal("hide");
        listar_salidas_diarias_dia();
      });
    },
    error: function () {
      Swal.fire("Error", "Error en la comunicación con el servidor.", "error");
    },
  });
}

//AQUI PRIMERO SE GUARDA LA SALIDAS Y LUEGO EL INGRESO DE MONTO

//REGISTRAR DETALLE PASAJEROS Y ENCOMIENDAS

//aqui se guarda primero pasajeros o actualiza y luego recien se isnertar con el ID SALIDA

// FUNCIÓN CORREGIDA - Usa esta versión mejorada

//AQUI SE GUARDA salida_encomienda y cambia estado encomienda
//ABRIR MODAL MOSTRAR DATOS
$("#tabla_salida_diaria").on("click", ".mostrar", function () {
  var data = tbl_salidas_diarias.row($(this).parents("tr")).data();
  if (tbl_salidas_diarias.row(this).child.isShown()) {
    var data = tbl_salidas_diarias.row(this).data();
  }

  // CARGAR CAMPOS BÁSICOS
  document.getElementById("id_salida").value = data.id_salidas_diarias;
  document.getElementById("select_conductor_mostrar").value =
    data.nombres_apellidos;
  document.getElementById("select_origen_mostrar").value = data.origen_nombre;
  document.getElementById("select_destino_mostrar").value = data.destino_nombre;
  document.getElementById("txt_pago_mostrar").value = data.monto;
  document.getElementById("txt_fecha_creacion_mostrar").value = data.fecha_hora;
  document.getElementById("txt_descripcion_mostrar").value = data.observacion;

  // MOSTRAR MODAL
  $("#modal_mostrar").modal("show");

  // CARGAR TABLAS DESPUÉS DE MOSTRAR EL MODAL
  setTimeout(function () {
    listar_pasajeros(data.id_salidas_diarias);
    listar_encomiendas(data.id_salidas_diarias);
  }, 300);
});

var tbl_detalle_pasajeros;
var tbl_detalle_encomiendas;



//ABRIR MODAL EDITAR DATOS
$("#tabla_salida_diaria").on("click", ".editar", function () {
  var data = tbl_salidas_diarias.row($(this).parents("tr")).data();
  if (tbl_salidas_diarias.row(this).child.isShown()) {
    var data = tbl_salidas_diarias.row(this).data();
  }
  
  // Llenar datos del formulario
  document.getElementById("id_salida_editar").value = data.id_salidas_diarias;
  $("#select_conductor_editar").val(data.id_conductor).trigger("change");
  document.getElementById("txt_pago_editar").value = data.monto;
  $("#select_origen_editar").val(data.id_origen).trigger("change");
  $("#select_destino_editar").val(data.id_destino).trigger("change");
  document.getElementById("txt_descripcion_editar").value = data.observacion;

  // Limpiar event listeners previos para evitar duplicados
  $(".encomienda-checkbox").off("change");
  $("#check_all_encomiendas_editar").off("change");

  // MOSTRAR MODAL y cargar datos
  $("#modal_editar").modal("show");

  // Usar evento 'shown.bs.modal' en lugar de setTimeout
  $("#modal_editar").one("shown.bs.modal", function () {
    // Cargar tablas en paralelo para mayor velocidad
    Promise.all([
      listar_pasajerosEditar(data.id_salidas_diarias),
      listar_encomiendasEditar(data.id_salidas_diarias)
    ]).catch(function(error) {
      console.error("Error cargando datos:", error);
      Swal.fire("Error", "No se pudieron cargar todos los datos", "error");
    });
  });
});

var tbl_detalle_pasajerosEditar;
var tbl_detalle_encomiendasEditar;

// Función para listar pasajeros (retorna Promise)
function listar_pasajerosEditar(id) {
  return new Promise(function(resolve, reject) {
    // Destruir tabla existente si existe
    if ($.fn.DataTable.isDataTable("#tabla_pasajeros_editar")) {
      $("#tabla_pasajeros_editar").DataTable().clear().destroy();
      $("#tabla_pasajeros_editar").empty();
    }

    tbl_detalle_pasajerosEditar = $("#tabla_pasajeros_editar").DataTable({
      ordering: false,
      bLengthChange: false,
      searching: false,
      paging: false,
      info: false,
      processing: true,
      serverSide: false,
      dom: "t",
      deferRender: true, // Optimización para renderizado
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
        timeout: 10000, // 10 segundos timeout
        dataSrc: function (json) {
          // Actualizar contador de pasajeros
          let total = (json.data && json.data.length) || 0;
          $("#total_pasajeros_editar").text(total);
          resolve(json.data); // Resolver promesa
          return json.data || [];
        },
        error: function (xhr, error, thrown) {
          console.error("Error cargando pasajeros:", error, thrown);
          $("#total_pasajeros_editar").text(0);
          reject(error);
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
        { data: "tipo_documento", width: "15%" },
        { data: "nro_documento", width: "15%" },
        { data: "nombre_completo", width: "30%" },
        { data: "edad", width: "10%" },
        { data: "celular", width: "15%" },
        {
          data: null,
          render: function (data, type, row) {
            return '<button class="btn btn-danger btn-sm" onclick="eliminarPasajeroEditar(' +
              row.id_cliente_salida +
              ')" title="Eliminar pasajero">' +
              '<i class="fas fa-trash"></i></button>';
          },
          width: "7%",
        },
      ],
      language: {
        emptyTable: "No hay pasajeros registrados",
        zeroRecords: "No se encontraron pasajeros",
        loadingRecords: "Cargando...",
        processing: "Procesando...",
      },
    });
  });
}

// Función para listar encomiendas (retorna Promise)
function listar_encomiendasEditar(id) {
  return new Promise(function(resolve, reject) {
    if ($.fn.DataTable.isDataTable("#tabla_encomiendas_editar")) {
      $("#tabla_encomiendas_editar").DataTable().clear().destroy();
      $("#tabla_encomiendas_editar").empty();
    }

    tbl_detalle_encomiendasEditar = $("#tabla_encomiendas_editar").DataTable({
      ordering: false,
      bLengthChange: false,
      searching: false,
      paging: false,
      info: false,
      processing: true,
      serverSide: false,
      dom: "t",
      deferRender: true, // Optimización
      columnDefs: [
        {
          targets: [1, 2, 3, 4, 5, 6, 7],
          className: "text-center",
        },
        {
          targets: [7],
          className: "text-center align-middle",
        },
      ],
      ajax: {
        url: "../controller/salidas_diarias/controlador_listar_detalle_encomiendasEditar.php",
        type: "POST",
        data: { id: id },
        timeout: 10000, // 10 segundos timeout
        dataSrc: function (json) {
          resolve(json.data || []); // Resolver promesa
          return json.data || [];
        },
        error: function (xhr, error, thrown) {
          console.error("Error cargando encomiendas:", error, thrown);
          $("#total_encomiendas_editar").text(0);
          reject(error);
        },
      },
      columns: [
        {
          data: null,
          render: function (data, type, row) {
            let estaIncluida = row.estado == 1 || row.estado == "1";
            let checked = estaIncluida ? "checked" : "";
            return `<input type="checkbox" class="encomienda-checkbox" data-id="${row.id_encomienda}" value="${row.id_encomienda}" ${checked}>`;
          },
          width: "5%",
        },
        {
          data: null,
          render: (data, type, row, meta) => meta.row + 1,
          width: "8%",
        },
        { data: "emisor", width: "20%" },
        { data: "receptor", width: "20%" },
        {
          data: "pago",
          width: "12%",
          render: (data) => "S/ " + parseFloat(data || 0).toFixed(2),
        },
        {
          data: "por_pagar",
          width: "12%",
          render: (data) => "S/ " + parseFloat(data || 0).toFixed(2),
        },
        {
          data: "a_domicilio",
          width: "13%",
          render: function (data) {
            let aDomicilio = String(data).toUpperCase().trim();
            return aDomicilio === "1" || aDomicilio === "SI" || aDomicilio === "SÍ"
              ? '<span class="badge badge-info">SÍ</span>'
              : '<span class="badge badge-secondary">NO</span>';
          },
        },
        {
          data: "estado_pago",
          width: "10%",
          render: function (data) {
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
        loadingRecords: "Cargando...",
        processing: "Procesando...",
      },
      initComplete: function(settings, json) {
        // Configurar eventos DESPUÉS de que la tabla esté completamente cargada
        configurarEventosEncomiendas();
      },
      drawCallback: function (settings) {
        // Reconfigurar eventos en cada redibujado
        configurarEventosEncomiendas();
      },
    });
  });
}

// Función separada para configurar eventos (evita duplicados)
function configurarEventosEncomiendas() {
  // Remover eventos previos
  $(".encomienda-checkbox").off("change");
  $("#check_all_encomiendas_editar").off("change");
  
  // Función para actualizar totales
  function actualizarTotalSeleccionados() {
    let totalSeleccionados = $(".encomienda-checkbox:checked").length;
    $("#total_encomiendas_editar").text(totalSeleccionados);
    actualizarCheckboxPrincipalEditar();
  }
  
  // Event listener para checkboxes individuales
  $(document).on("change", ".encomienda-checkbox", function() {
    actualizarTotalSeleccionados();
  });
  
  // Event listener para checkbox principal
  $("#check_all_encomiendas_editar").on("change", function () {
    let isChecked = $(this).is(":checked");
    $(".encomienda-checkbox").prop("checked", isChecked);
    actualizarTotalSeleccionados();
  });
  
  // Actualizar totales iniciales
  actualizarTotalSeleccionados();
}

// Función optimizada para actualizar el estado del checkbox principal
function actualizarCheckboxPrincipalEditar() {
  let $checkboxes = $(".encomienda-checkbox");
  let totalCheckboxes = $checkboxes.length;
  let checkedCheckboxes = $checkboxes.filter(":checked").length;
  let checkboxPrincipal = $("#check_all_encomiendas_editar");

  if (totalCheckboxes === 0 || checkedCheckboxes === 0) {
    checkboxPrincipal.prop("checked", false).prop("indeterminate", false);
  } else if (checkedCheckboxes === totalCheckboxes) {
    checkboxPrincipal.prop("checked", true).prop("indeterminate", false);
  } else {
    checkboxPrincipal.prop("checked", false).prop("indeterminate", true);
  }
}

// Función optimizada para eliminar pasajero
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
      // Mostrar loading
      Swal.fire({
        title: "Eliminando...",
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
          Swal.showLoading();
        },
      });

      $.ajax({
        url: "../controller/salidas_diarias/controlador_eliminar_pasajero_salida.php",
        type: "POST",
        data: { id_pasajero: idPasajero },
        timeout: 10000, // 10 segundos timeout
        success: function (response) {
          // Convertir a número para comparación segura
          let resultado = parseInt(response);
          
          if (resultado > 0) {
            Swal.fire({
              icon: "success",
              title: "Eliminado",
              text: "El pasajero ha sido eliminado correctamente.",
              timer: 2000,
              showConfirmButton: false
            });
            
            // Recargar tabla si existe
            if (typeof tbl_detalle_pasajerosEditar !== "undefined" && tbl_detalle_pasajerosEditar) {
              tbl_detalle_pasajerosEditar.ajax.reload(null, false); // false = mantener paginación
            }
          } else {
            Swal.fire("Error", "No se pudo eliminar el pasajero.", "error");
          }
        },
        error: function (xhr, status, error) {
          console.error("Error eliminando pasajero:", {xhr, status, error});
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Ocurrió un error al eliminar el pasajero. Verifique su conexión.",
          });
        },
      });
    }
  });
}





// Guarda la edición en una sola petición (datos, pasajeros nuevos y encomiendas
// que se quitan): el servidor valida todo y deshace si algo falla
function Moidificar_Salida_Diaria() {
  const idSalida = document.getElementById("id_salida_editar").value;
  const monto = document.getElementById("txt_pago_editar").value;
  const fechaactualizar = document.getElementById("txt_fecha_actualizacion").value;
  const observacion = document.getElementById("txt_descripcion_editar").value;

  if (!monto || !fechaactualizar) {
    return Swal.fire(
      "Advertencia",
      "Complete todos los campos obligatorios.",
      "warning"
    );
  }

  // Solo los pasajeros agregados en esta edición (los guardados se quitan con su botón)
  const pasajeros = [];
  document
    .querySelectorAll("#tabla_pasajeros_editar tbody tr.pasajero-nuevo")
    .forEach((row) => {
      const texto = (celda) => (celda.textContent || "").replace(/\s+/g, " ").trim();
      const celdas = row.cells;
      pasajeros.push({
        tipo_documento: texto(celdas[1]),
        documento: texto(celdas[2]),
        nombres: texto(celdas[3]),
        edad: texto(celdas[4]),
        celular: texto(celdas[5]),
      });
    });

  // Encomiendas desmarcadas: se quitan de la salida
  const quitar = $("#tabla_encomiendas_editar tbody input.encomienda-checkbox:not(:checked)")
    .map(function () {
      return $(this).val();
    })
    .get()
    .filter((id) => id);

  $.ajax({
    url: "../controller/salidas_diarias/controlador_modificar_salida_completa.php",
    type: "POST",
    data: {
      idSalida,
      monto,
      fechaactualizar,
      observacion,
      pasajeros: JSON.stringify(pasajeros),
      quitar: JSON.stringify(quitar),
    },
    success: function (resp) {
      if (resp > 0) {
        Swal.fire(
          "Éxito",
          "Salida diaria modificada correctamente.",
          "success"
        );
        $("#modal_editar").modal("hide");
        listar_salidas_diarias_dia();
      } else {
        Swal.fire(
          "Error",
          EstadosOperacion.mensaje(resp, "No se pudo modificar la salida diaria."),
          "error"
        );
      }
    },
    error: function () {
      Swal.fire("Error", "Error en la comunicación con el servidor.", "error");
    },
  });
}

//MODIFICAR DETALLE PASAJEROS



// 📦 FUNCIÓN CORREGIDA PARA MODIFICAR ENCOMIENDAS

// Configuración de la tabla de salidas diarias (administrador).
// Es la misma en todas las listas; cada una solo indica de dónde lee los datos (ajax).
function Opciones_Tabla_Salidas(ajax) {
  return Opciones_Tabla_Salidas_Base(ajax, [
      { data: "usuario_nombre_completo" },

      // ---- BOTONES DE ACCIÓN ----
      {
        data: "estado",
        render: function (data, type, row) {
          let botones = "";

          // EN TRANSITO / INCOMPLETO => Editar + Eliminar
          if (data === "EN TRANSITO") {
            botones += `

              <button class='eliminar btn btn-danger btn-sm' title='Eliminar datos de servicio'>
                <i class='fa fa-trash'></i> Eliminar
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
               ${data === "EN TRANSITO" ? `<button class='editar btn btn-primary btn-sm' title='Editar datos de servicio'>
                <i class='fa fa-edit'></i> Editar
              </button>` : ""}
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
