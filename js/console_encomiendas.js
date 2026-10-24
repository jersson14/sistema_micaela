//LISTAR ENCOMIENDAS
var tbl_encomiendas;

function listar_encomiendas_pordia() {


  tbl_encomiendas = $("#tabla_encomiendas").DataTable(Opciones_Tabla_Encomiendas({
      url: "../controller/encomiendas/controlador_listar_encomiendas_pordia.php",
      type: "POST",
    }));

}
function listar_encomiendas() {
  Cargar_Select_Usuarios();
  Cargar_Select_Rutas();
  document.getElementById("txt_fecha_desde").value = "";
  document.getElementById("txt_fecha_hasta").value = "";
  document.getElementById("select_estado_buscar").value = "";

  tbl_encomiendas = $("#tabla_encomiendas").DataTable(Opciones_Tabla_Encomiendas({
      url: "../controller/encomiendas/controlador_listar_encomiendas.php",
      type: "POST",
    }));

}

//FILTRO POR RUTAS Y ESTADO
function listar_encomiendas_ruta_estado() {
  let ori = document.getElementById("select_origen_bus").value;
  let des = document.getElementById("select_destino_bus").value;
  let esta = document.getElementById("select_estado_buscar").value;

  tbl_encomiendas = $("#tabla_encomiendas").DataTable(Opciones_Tabla_Encomiendas({
      url: "../controller/encomiendas/controlador_listar_encomiendas_filtro1.php",
      type: "POST",
      data: {
        ori: ori,
        des: des,
        esta: esta,
      },
    }));

}


//FILTRO POR FECHA Y USUARIO
function listar_encomiendas_fecha_usu() {
  let fedes = document.getElementById("txt_fecha_desde").value;
  let fehas = document.getElementById("txt_fecha_hasta").value;
  let usu = document.getElementById("select_usuario").value;

  tbl_encomiendas = $("#tabla_encomiendas").DataTable(Opciones_Tabla_Encomiendas({
      url: "../controller/encomiendas/controlador_listar_encomiendas_filtro2.php",
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



//EDITAR BUSQUEDA


//ABRIR MODAL EDITAR ESTADO
$("#tabla_encomiendas").on("click", ".cambiar_estado", function () {
  var data = tbl_encomiendas.row($(this).parents("tr")).data();

  if (tbl_encomiendas.row(this).child.isShown()) {
    var data = tbl_encomiendas.row(this).data();
  }
  $("#modal_estado").modal("show");
  document.getElementById("id_encomienda").value = data.id_encomienda;
  EstadosOperacion.limitarSelect(
    document.getElementById("select_estado_editar2"),
    EstadosOperacion.permitidos(data.estado_encomienda, data.estado_pago)
  );
  document.getElementById("text_observacion_enco").value = ""; // cada cambio lleva su propia observación
  document.getElementById("txt_anula_enco").value = data.motivo_anulacion;
});

//ABRIR MODAL VER MOTIVO ANULACION
$("#tabla_encomiendas").on("click", ".motivo_anulacion", function () {
  var data = tbl_encomiendas.row($(this).parents("tr")).data();

  if (tbl_encomiendas.row(this).child.isShown()) {
    var data = tbl_encomiendas.row(this).data();
  }
  $("#modal_motivo_anula").modal("show");
  document.getElementById("select_estado_editar3").value =
    data.estado_encomienda;
  document.getElementById("txt_anula_enco2").value = data.motivo_anulacion;
});

//ABRIR MODAL AJUSTAR PRECIO
$("#tabla_encomiendas").on("click", ".ajustar_precio", function () {
  var data = tbl_encomiendas.row($(this).parents("tr")).data();

  if (tbl_encomiendas.row(this).child.isShown()) {
    var data = tbl_encomiendas.row(this).data();
  }
  $("#modal_ajustar_precio").modal("show");
  document.getElementById("id_encomienda3").value = data.id_encomienda;

  EstadosOperacion.limitarSelect(
    document.getElementById("select_estado_editar4"),
    EstadosOperacion.permitidosAjuste(data.estado_pago),
    "OBSERVADO"
  );

  if (data.pago > 0) {
    document.getElementById("txt_monto_anterior").value = data.pago;
  } else if (data.por_pagar > 0) {
    document.getElementById("txt_monto_anterior").value = data.por_pagar;
  } else if (data.a_domicilio > 0) {
    document.getElementById("txt_monto_anterior").value = data.a_domicilio;
  }
});
//ABRIR MODAL MOSTRAR
$("#tabla_encomiendas").on("click", ".mostrar", function () {
  var data = tbl_encomiendas.row($(this).parents("tr")).data();
  if (tbl_encomiendas.row(this).child.isShown()) {
    var data = tbl_encomiendas.row(this).data();
  }
  $("#modal_mostrar").modal("show");

  // ASIGNAR ESTADOS CON COLORES
  asignarEstadoPago(data.estado_pago);
  asignarEstadoEncomienda(data.estado_encomienda);

  // CAMPOS EXISTENTES
  document.getElementById("select_conductor_mostrar").value =
    data.nombres_apellidos;
  document.getElementById("select_origen_mostrar").value = data.nombre_origen;
  document.getElementById("select_destino_mostrar").value = data.nombre_destino;
  document.getElementById("txt_fecha_creacion_mostrar").value = data.fecha_hora;
  document.getElementById("select_tipo_documento_emisor_mostrar").value =
    data.tipo_doc_emisor;
  document.getElementById("txt_dni_emisor_mostrar").value = data.nro_doc_emisor;
  document.getElementById("txt_nomb_emisor_mostrar").value = data.nombre_emisor;
  document.getElementById("txt_celu1_emisor_mostrar").value =
    data.celular_emisor;
  document.getElementById("select_tipo_documento_receptor_mostrar").value =
    data.tipo_doc_receptor;
  document.getElementById("txt_dni_receptor_mostrar").value =
    data.nro_doc_receptor;
  document.getElementById("txt_nomb_receptor_mostrar").value =
    data.nombre_receptor;
  document.getElementById("txt_celu1_recepto_mostrar").value =
    data.celular_receptor;
  document.getElementById("txt_pago_mostrar").value = data.pago;
  document.getElementById("txt_por_pagar_mostrar").value = data.por_pagar;
  document.getElementById("txt_a_domicilio_mostrar").value = data.a_domicilio;
  document.getElementById("txt_descripcion_mostrar").value = data.descripcion;
  document.getElementById("txt_observacion_mostrar").value = data.observacion;
});



//ABRIR MODAL EDITAR
$("#tabla_encomiendas").on("click", ".editar", function () {
  var data = tbl_encomiendas.row($(this).parents("tr")).data();
  if (tbl_encomiendas.row(this).child.isShown()) {
    var data = tbl_encomiendas.row(this).data();
  }
  $("#modal_editar").modal("show");

  // CAMPOS EXISTENTES
  
  document.getElementById("txt_id_encomienda").value =
    data.id_encomienda;
  $("#select_conductor_editar").val(data.id_conductor).trigger("change");
  $("#select_origen_editar").val(data.id_origen).trigger("change");
  $("#select_destino_editar").val(data.id_destino).trigger("change");

  document.getElementById("select_tipo_documento_emisor_editar").value =
    data.tipo_doc_emisor;
  document.getElementById("txt_dni_emisor_editar").value = data.nro_doc_emisor;
  document.getElementById("txt_nomb_emisor_editar").value = data.nombre_emisor;
  document.getElementById("txt_celu1_emisor_editar").value =
    data.celular_emisor;
  document.getElementById("select_tipo_documento_receptor_editar").value =
    data.tipo_doc_receptor;
  document.getElementById("txt_dni_receptor_editar").value =
    data.nro_doc_receptor;
  document.getElementById("txt_nomb_receptor_editar").value =
    data.nombre_receptor;
  document.getElementById("txt_celu1_recepto_editar").value =
    data.celular_receptor;
  document.getElementById("txt_pago_editar").value = data.pago;
  document.getElementById("txt_por_pagar_editar").value = data.por_pagar;
  document.getElementById("txt_a_domicilio_editar").value = data.a_domicilio;
  document.getElementById("txt_descripcion_editar").value = data.descripcion;
  document.getElementById("txt_observacion_editar").value = data.observacion;
});
//REGISTROS DE ENCOMIENDAS




//ENVIANDO AL BOTON DELETE
$("#tabla_encomiendas").on("click", ".eliminar", function () {
  var data = tbl_encomiendas.row($(this).parents("tr")).data();

  if (tbl_encomiendas.row(this).child.isShown()) {
    var data = tbl_encomiendas.row(this).data();
  }
  Swal.fire({
    title:
      "Desea eliminar la encomienda registrada el: " +
      data.fecha_formateada +
      " del cliente: " +
      data.nombre_emisor +
      "?",
    text: "Una vez aceptado la encomienda sera eliminado, sin poder recuperarlo, tendra que volver a registrarlo!!!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Eliminar",
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_encomienda(data.id_encomienda);
    }
  });
});

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
$("#tabla_encomiendas").on("click", ".historial", function () {
  var data = tbl_encomiendas.row($(this).parents("tr")).data();

  if (tbl_encomiendas.row(this).child.isShown()) {
    var data = tbl_encomiendas.row(this).data();
  }
  $("#modal_ver_historial").modal("show");

  document.getElementById("lb_titulo_historial").innerHTML =
    "<b>HISTORIAL DE LA ENCOMIENDA DEL EMISOR :</b> " +
    data.nro_doc_emisor +
    " - " +
    data.nombre_emisor +
    "";

  listar_historial_estado(data.id_encomienda);
});
// VISTA DE HISTORIAL
var tbl_historial_estado;

//PAGAR ENCOMIENDA
$("#tabla_encomiendas").on("click", ".pagar", function () {
  var data = tbl_encomiendas.row($(this).parents("tr")).data();

  if (tbl_encomiendas.row(this).child.isShown()) {
    var data = tbl_encomiendas.row(this).data();
  }
  $("#modal_pagar").modal("show");
  document.getElementById("id_encomienda_pago").value = data.id_encomienda;
  //DATOS DEL EMISOR Y RECEPTOR
  document.getElementById("txt_emisor_pago").value =
    data.nro_doc_emisor + " - " + data.nombre_emisor;
  document.getElementById("txt_origen_pago").value = data.nombre_origen;
  document.getElementById("txt_receptor_pago").value =
    data.nro_doc_receptor + " - " + data.nombre_receptor;
  document.getElementById("txt_destino_pago").value = data.nombre_destino;
  // DATOS DEL PAGO
  EstadosOperacion.limitarSelect(
    document.getElementById("select_estado_pago"),
    EstadosOperacion.permitidosPago(data.estado_encomienda),
    data.estado_encomienda
  );
  
  // ✅ GUARDAR EL ESTADO INICIAL PARA COMPARAR DESPUÉS
  document.getElementById("select_estado_pago").setAttribute("data-estado-inicial", data.estado_encomienda);
  
  document.getElementById("txt_saldo_pendiente").value = data.por_pagar;
});


// BOLETA DE PAGO
$("#tabla_encomiendas").on("click", ".imprimir", function () {
  var data = tbl_encomiendas.row($(this).parents("tr")).data();

  if (tbl_encomiendas.row(this).child.isShown()) {
    var data = tbl_encomiendas.row(this).data();
  }
  var url =
    "../view/MPDF/REPORTE/boleta_pago.php?id=" +
    encodeURIComponent(data.id_encomienda) +
    "#zoom=100%";

  // Abrir una nueva ventana con la URL construida
  var newWindow = window.open(url, "BOLETA DE PAGO", "scrollbars=NO");

  // Asegurarse de que la ventana se abre en tamaño máximo
  if (newWindow) {
    newWindow.moveTo(0, 0);
    newWindow.resizeTo(screen.width, screen.height);
  }
});





// Agrega al final de tu archivo JS
$(document).on('show.bs.dropdown', '#tabla_encomiendas .dropdown', function () {
  let $dropdown = $(this).find('.dropdown-menu');
  let $button = $(this).find('.dropdown-toggle');
  let buttonPos = $button.offset();
  
  $dropdown.css({
    'position': 'fixed',
    'top': buttonPos.top + $button.outerHeight() + 5,
    'left': buttonPos.left - $dropdown.outerWidth() + $button.outerWidth(),
    'margin': 0
  });
});

// Cerrar dropdown al hacer scroll
$('.dataTables_scrollBody, .table-responsive').on('scroll', function() {
  $('#tabla_encomiendas .dropdown-menu.show').removeClass('show');
});

// Configuración de la tabla de encomiendas (administrador).
// Es la misma en todas las listas; cada una solo indica de dónde lee los datos (ajax).
function Opciones_Tabla_Encomiendas(ajax) {
  return Opciones_Tabla_Encomiendas_Base(ajax, 10, [

      // ---- BOTONES CON HISTORIAL AGREGADO ----
      {
        data: null,
        render: function (data, type, row) {
          let pago = row.estado_pago;
          let estado = row.estado_encomienda;
          let id = row.id_encomienda;

          const botones = {
            mostrar:
              "<a href='#' class='dropdown-item mostrar' data-id='" +
              id +
              "'><i class='fa fa-eye'></i> Mostrar</a>",
            editar:
              "<a href='#' class='dropdown-item editar' data-id='" +
              id +
              "'><i class='fa fa-edit'></i> Editar</a>",
            eliminar:
              "<a href='#' class='dropdown-item eliminar' data-id='" +
              id +
              "'><i class='fa fa-trash'></i> Eliminar</a>",
            cambiar:
              "<a href='#' class='dropdown-item cambiar_estado' data-id='" +
              id +
              "'><i class='fa fa-retweet'></i> Cambiar Estado</a>",
            imprimir:
              "<a href='#' class='dropdown-item imprimir' data-id='" +
              id +
              "'><i class='fa fa-print'></i> Imprimir</a>",
            anular:
              "<a href='#' class='dropdown-item anular' data-id='" +
              id +
              "'><i class='fa fa-ban'></i> Anular</a>",
            pagar:
              "<a href='#' class='dropdown-item pagar' data-id='" +
              id +
              "'><i class='fa fa-credit-card'></i> Pagar</a>",
            ajustar:
              "<a href='#' class='dropdown-item ajustar_precio' data-id='" +
              id +
              "'><i class='fa fa-dollar-sign'></i> Ajustar Precio</a>",
            motivo:
              "<a href='#' class='dropdown-item motivo_anulacion' data-id='" +
              id +
              "'><i class='fa fa-info-circle'></i> Motivo Anulación</a>",
            historial:
              "<a href='#' class='dropdown-item historial' data-id='" +
              id +
              "'><i class='fa fa-history'></i> Historial</a>",
          };

          const reglas = {
            "PAGADO|PENDIENTE": [
              botones.eliminar,
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
              botones.historial,
            ],
            "PAGADO|EN TRANSITO": [
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
              botones.historial,
            ],
            "PAGADO|EN AGENCIA": [
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
              botones.historial,
            ],
            "PAGADO|ENTREGADO": [
              botones.editar,
              botones.mostrar,
              botones.imprimir,
              botones.historial,
            ],
            "PAGADO|OBSERVADO": [
              botones.editar,
              botones.mostrar,
              botones.imprimir,
              botones.ajustar,
              botones.historial,
            ],
            "PAGADO|ANULADO": [
              botones.mostrar,
              botones.motivo,
              botones.historial,
            ],
            "POR PAGAR|PENDIENTE": [
              botones.eliminar,
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
              botones.pagar,
              botones.historial,
            ],
            "POR PAGAR|EN TRANSITO": [
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
              botones.pagar,
              botones.historial,
            ],
            "POR PAGAR|EN AGENCIA": [
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
              botones.pagar,
              botones.historial,
            ],
            "POR PAGAR|ENTREGADO": [
              botones.editar,
              botones.mostrar,
              botones.imprimir,
              botones.historial,
            ],
            "POR PAGAR|OBSERVADO": [
              botones.editar,
              botones.mostrar,
              botones.imprimir,
              botones.ajustar,
              botones.historial,
            ],
            "POR PAGAR|ANULADO": [
              botones.mostrar,
              botones.motivo,
              botones.historial,
            ],
            "ANULADO|ANULADO": [
              botones.mostrar,
              botones.motivo,
              botones.historial,
            ],
          };

          let clave = pago + "|" + estado;
          let acciones = reglas[clave] || [botones.mostrar, botones.historial];
          acciones = EstadosOperacion.filtrarAcciones(acciones, row, botones);

          // Dropdown mejorado con mejor posicionamiento
          return `
        <div class="dropdown" style="position: relative;">
            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" 
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    style="border: none; background: #6c757d;">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" style="position: absolute; 
                     z-index: 9999; min-width: 150px; right: 0;">
                ${acciones.join("")}
            </div>
        </div>`;
        },
        width: "80px", // Fija el ancho de la columna
        className: "text-center no-wrap", // Clase para centrar y evitar wrap
      },
  ]);
}
