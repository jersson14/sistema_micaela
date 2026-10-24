var tbl_salidas_diarias;
function listar_salidas_diarias() {
  Cargar_Select_Usuarios();
  Cargar_Select_Rutas();
  document.getElementById("txt_fecha_desde").value = "";
  document.getElementById("txt_fecha_hasta").value = "";
  document.getElementById("select_estado_buscar").value = "";

  let usu = document.getElementById("txtprincipalid").value;


  tbl_salidas_diarias = $("#tabla_salida_diaria").DataTable(Opciones_Tabla_Salidas_Sucursal({
      url: "../controller/salidas_diarias/controlador_listar_salidas_diarias_conduc.php",
      type: "POST",
        data: {
        usu: usu
        },
    }));
}

function listar_salidas_diarias_pordia() {
  Cargar_Select_Usuarios();
  Cargar_Select_Rutas();
  document.getElementById("txt_fecha_desde").value = "";
  document.getElementById("txt_fecha_hasta").value = "";
  document.getElementById("select_estado_buscar").value = "";

  let usu = document.getElementById("txtprincipalid").value;


  tbl_salidas_diarias = $("#tabla_salida_diaria").DataTable(Opciones_Tabla_Salidas_Sucursal({
      url: "../controller/salidas_diarias/controlador_listar_salidas_diarias_asis_pordia_con.php",
      type: "POST",
        data: {
        usu: usu
        },
    }));
}


function listar_salidas_diarias_fecha_estado() {

  let fedes = document.getElementById("txt_fecha_desde").value;
  let fehas = document.getElementById("txt_fecha_hasta").value;
  let estado = document.getElementById("select_estado_buscar").value;
  let usu = document.getElementById("txtprincipalid").value;

  tbl_salidas_diarias = $("#tabla_salida_diaria").DataTable(Opciones_Tabla_Salidas_Sucursal({
      url: "../controller/salidas_diarias/controlador_listar_salidas_diarias_fechas_estado_conduc.php",
      type: "POST",
      data: {
        fedes: fedes,
        fehas: fehas,
        estado: estado,
        usu: usu,
      },
    }));
}

//ABRIR MODAL REGISTRO
function AbrirRegistro(){
  $("#modal_registro").modal({backdrop:'static',keyboard:false})
  $("#modal_registro").modal('show');
}
//CARGAR SELECT CONDUCTORES
function Cargar_Select_Conductores() {
    let dni = document.getElementById("txtDNIusuario").value;
  $.ajax({
    url: "../controller/choferes/controlador_cargar_select_choferes_unico.php",
    type: "POST",
    data: { dni: dni },
  }).done(function (resp) {
    let data = JSON.parse(resp);
    let cadena = "";

    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) {
        cadena +=
          "<option value='" +
          data[i][0] +
          "' selected>DNI: " +
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
  const encomiendas = [] // el conductor no sube encomiendas;

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
        listar_salidas_diarias_pordia();
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
    listar_pasajeros(data.id_salidas_diarias); // la vista del conductor no muestra encomiendas
  }, 300);
});

var tbl_detalle_pasajeros;




//ABRIR MODAL EDITAR DATOS
$("#tabla_salida_diaria").on("click", ".editar", function () {
  var data = tbl_salidas_diarias.row($(this).parents("tr")).data();
  if (tbl_salidas_diarias.row(this).child.isShown()) {
    var data = tbl_salidas_diarias.row(this).data();
  }
  document.getElementById("id_salida_editar").value = data.id_salidas_diarias;

  $("#select_conductor_editar").val(data.id_conductor).trigger("change");
  document.getElementById("txt_pago_editar").value = data.monto;
  $("#select_origen_editar").val(data.id_origen).trigger("change");
  $("#select_destino_editar").val(data.id_destino).trigger("change");
  document.getElementById("txt_descripcion_editar").value = data.observacion;

  // MOSTRAR MODAL
  $("#modal_editar").modal("show");

  // CARGAR TABLAS DESPUÉS DE MOSTRAR EL MODAL
  setTimeout(function () {
    listar_pasajerosEditar(data.id_salidas_diarias);
    listar_encomiendasEditar(data.id_salidas_diarias);
  }, 300);
});

var tbl_detalle_pasajerosEditar;
var tbl_detalle_encomiendasEditar;









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
  const quitar = [] // el conductor no maneja encomiendas;

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
        listar_salidas_diarias_pordia();
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





