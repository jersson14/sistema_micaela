var tbl_encomiendas;
function listar_encomiendas() {
  tbl_encomiendas = $("#tabla_encomiendas").DataTable({
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
    pagingType: "full_numbers",
    scrollCollapse: true,
    responsive: true,
    async: false,
    processing: true,
    ajax: {
      url: "../controller/encomiendas/controlador_listar_encomiendas.php",
      type: "POST",
    },
    dom: "Bfrtip",

    buttons: [
      {
        extend: "excelHtml5",
        text: '<i class="fas fa-file-excel"></i> Excel',
        titleAttr: "Exportar a Excel",
        filename: "LISTA DE ENCOMIENDAS",
        title: "LISTA DE ENCOMIENDAS",
        className: "btn btn-excel",
        exportOptions: {
          columns: [1, 3, 4, 5, 6, 7, 8, 9], // Exportar solo hasta la columna 'estado'
        },
      },
      {
        extend: "pdfHtml5",
        text: '<i class="fas fa-file-pdf"></i> PDF',
        titleAttr: "Exportar a PDF",
        filename: "LISTA DE ENCOMIENDAS",
        title: "LISTA DE ENCOMIENDAS",
        className: "btn btn-pdf",
        orientation: "landscape", // <-- Establece la orientación en horizontal
        pageSize: "A4", // <-- Especifica el tamaño de la página
        exportOptions: {
          columns: [1, 3, 4, 5, 6, 7, 8, 9], // Exportar solo hasta la columna 'estado'
        },
      },
      {
        extend: "print",
        text: '<i class="fa fa-print"></i> Imprimir',
        titleAttr: "Imprimir",
        title: "LISTA DE ENCOMIENDAS",
        className: "btn btn-print",
        exportOptions: {
          columns: [1, 3, 4, 5, 6, 7, 8, 9], // Exportar solo hasta la columna 'estado'
        },
      },
    ],
    columns: [
      { defaultContent: "" },
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
            return '<span class="badge bg-info text-danger">ANULADO</span>';
          } else {
            return '<span class="badge bg-warning text-dark">POR PAGAR</span>';
          }
        },
      },

      // ---- ESTADO ENCOMIENDA ----
      {
        data: "estado_encomienda",
        render: function (data, type, row) {
          switch (data) {
            case "PENDIENTE":
              return '<span class="badge bg-warning text-dark">PENDIENTE</span>';
            case "ENTREGADO":
              return '<span class="badge bg-success">ENTREGADO</span>';
            case "OBSERVADO":
              return '<span class="badge bg-danger">OBSERVADO</span>';
            case "EN TRANSITO":
              return '<span class="badge bg-info text-dark">EN TRÁNSITO</span>';
            case "EN AGENCIA":
              return '<span class="badge bg-primary">EN AGENCIA</span>';
            case "ANULADO":
              return '<span class="badge bg-secondary">ANULADO</span>';
            default:
              return (
                '<span class="badge bg-light text-dark">' + data + "</span>"
              );
          }
        },
      },

      // ---- BOTONES ----
      // SOLUCIÓN JAVASCRIPT MEJORADA
      // Reemplaza la columna de botones en tu DataTable

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
          };

          const reglas = {
            "PAGADO|PENDIENTE": [
              botones.eliminar,
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
            ],
            "PAGADO|EN TRANSITO": [
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
              botones.anular,
            ],
            "PAGADO|EN AGENCIA": [
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
            ],
            "PAGADO|ENTREGADO": [
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
            ],
            "PAGADO|OBSERVADO": [
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
              botones.ajustar,
            ],
            "PAGADO|ANULADO": [botones.mostrar, botones.motivo],

            "POR PAGAR|PENDIENTE": [
              botones.eliminar,
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
              botones.pagar,
            ],
            "POR PAGAR|EN TRANSITO": [
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
              botones.anular,
              botones.pagar,
            ],
            "POR PAGAR|EN AGENCIA": [
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
              botones.pagar,
            ],
            "POR PAGAR|ENTREGADO": [
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
            ],
            "POR PAGAR|OBSERVADO": [
              botones.editar,
              botones.mostrar,
              botones.cambiar,
              botones.imprimir,
              botones.ajustar,
            ],
            "POR PAGAR|ANULADO": [botones.mostrar, botones.motivo],
          };

          let clave = pago + "|" + estado;
          let acciones = reglas[clave] || [botones.mostrar];

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
    ],
    language: idioma_espanol,
    select: true,
  });
  tbl_encomiendas.on("draw.td", function () {
    var PageInfo = $("#tabla_encomiendas").DataTable().page.info();
    tbl_encomiendas
      .column(0, { page: "current" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1 + PageInfo.start;
      });
  });
}
$(document).ready(function () {
  // Después de inicializar la tabla
  listar_encomiendas();

  // Manejar clicks en dropdowns para ajustar posición
  $(document).on("show.bs.dropdown", ".dropdown", function () {
    var dropdown = $(this).find(".dropdown-menu");
    var toggle = $(this).find(".dropdown-toggle");

    // Obtener posición del botón
    var toggleOffset = toggle.offset();
    var tableContainer = $(".table-responsive");
    var containerOffset = tableContainer.offset();
    var containerWidth = tableContainer.width();
    var containerHeight = tableContainer.height();

    // Verificar si está cerca del borde derecho
    if (toggleOffset.left + 150 > containerOffset.left + containerWidth) {
      dropdown.addClass("dropdown-menu-right");
    }

    // Verificar si está cerca del borde inferior
    if (toggleOffset.top + 200 > containerOffset.top + containerHeight) {
      dropdown.addClass("dropup");
      $(this).addClass("dropup");
    }
  });

  // Limpiar clases al cerrar
  $(document).on("hide.bs.dropdown", ".dropdown", function () {
    $(this).removeClass("dropup");
    $(this).find(".dropdown-menu").removeClass("dropup dropdown-menu-right");
  });
});
function AbrirRegistro() {
  $("#modal_registro").modal({ backdrop: "static", keyboard: false });
  $("#modal_registro").modal("show");
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

// Una sola vez cuando se abre el modal
$("#modal_registro").on("shown.bs.modal", function () {
  $("#select_conductor").select2({
    placeholder: "Seleccionar Conductor",
    allowClear: true,
    dropdownParent: $("#modal_registro"),
  });
});

//ABRIR MODAL EDITAR
$("#tabla_encomiendas").on("click", ".cambiar_estado", function () {
  var data = tbl_encomiendas.row($(this).parents("tr")).data();

  if (tbl_encomiendas.row(this).child.isShown()) {
    var data = tbl_encomiendas.row(this).data();
  }
  $("#modal_estado").modal("show");
  document.getElementById("id_encomienda").value = data.id_encomienda;
  document.getElementById("select_estado_editar2").value =
    data.estado_encomienda;
  document.getElementById("text_observacion_enco").value = data.observacion;
  document.getElementById("txt_anula_enco").value = data.motivo_anulacion;
});

$("#tabla_encomiendas").on("click", ".mostrar", function () {
  var data = tbl_encomiendas.row($(this).parents("tr")).data();

  if (tbl_encomiendas.row(this).child.isShown()) {
    var data = tbl_encomiendas.row(this).data();
  }
  $("#modal_mostrar").modal("show");
  document.getElementById("select_tipo_documento_mostrar").value =
    data.tipo_documen;
  document.getElementById("txt_dni_mostrar").value = data.nro_doc;
  document.getElementById("txt_nomb_mostrar").value = data.nombres_apellidos;
  document.getElementById("txt_celu1_mostrar").value = data.celular;

  document.getElementById("txt_celu2_mostrar").value = data.celular_2;
  document.getElementById("txt_procedencia_mostrar").value = data.procedencia;
  document.getElementById("txt_direc_mostrar").value = data.direccion;
  document.getElementById("txt_foto_actual").value = data.foto;

  var imgElement = document.getElementById("preview3");
  if (imgElement) {
    console.log("Data:", data); // Depuración
    console.log("Image URL:", data.foto); // Verificar URL

    if (data.foto && data.foto.trim() !== "") {
      imgElement.src = "../" + data.foto; // Ruta relativa
    } else {
      imgElement.src = "../img/vacio.png"; // Ruta por defecto
    }

    imgElement.style.display = "block"; // Mostrar siempre la imagen

    // Manejar errores de carga
    imgElement.onerror = function () {
      console.error(
        "Error al cargar la imagen desde la ruta: " + imgElement.src
      );
      imgElement.src = "../img/vacio.png"; // Ruta por defecto
    };
  } else {
    console.error("Elemento img con id preview2 no encontrado");
  }

  document.getElementById("txt_marca_mostrar").value = data.marca_vehiculo;
  document.getElementById("txt_placa_mostrar").value = data.placa_vehiculo;
  document.getElementById("txt_clase_categoria_mostrar").value =
    data.clase_categoria;
  document.getElementById("txt_nro_licencia_mostrar").value = data.nro_licencia;
  document.getElementById("txt_fecha_expira_mostrar").value =
    data.fecha_vencimiento_licencia;
  document.getElementById("select_estado_mostrar").value = data.estado;
});

//REGISTROS DE CHOFERES
function Registrar_Choferes() {
  //DATOS DEL DOCENTE
  let tipo_doc = document.getElementById("select_tipo_documento").value;
  let dni = document.getElementById("txt_dni").value;
  let dni2 = document.getElementById("txt_dni2").value;
  let nom_ape = document.getElementById("txt_nomb").value;
  let celu = document.getElementById("txt_celu1").value;
  let celu2 = document.getElementById("txt_celu2").value;
  let proc = document.getElementById("txt_procedencia").value;
  let dire = document.getElementById("txt_direc").value;
  let foto = document.getElementById("txt_foto").value;

  //DATOS DEL CARRO
  let marca = document.getElementById("txt_marca").value;
  let placa = document.getElementById("txt_placa").value;
  let clase_cate = document.getElementById("txt_clase_categoria").value;
  let nro_lice = document.getElementById("txt_nro_licencia").value;
  let fec_ven = document.getElementById("txt_fecha_expira").value;
  let idusuario = document.getElementById("txtprincipalid").value;

  if (
    tipo_doc.length == 0 ||
    nom_ape.length == 0 ||
    celu.length == 0 ||
    marca.length == 0 ||
    placa.length == 0 ||
    clase_cate.length == 0 ||
    nro_lice.length == 0 ||
    fec_ven.length == 0
  ) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Tiene campos vacios en el formulario, revise por favor",
      "warning"
    );
  }
  // Validar documento según tipo
  let documentoFinal = "";
  if (tipo_doc === "DNI") {
    if (!dni) {
      return Swal.fire(
        "Mensaje de Advertencia",
        "El campo DNI es obligatorio",
        "warning"
      );
    }
    documentoFinal = dni;
  } else {
    if (!dni2) {
      return Swal.fire(
        "Mensaje de Advertencia",
        "El campo de documento es obligatorio",
        "warning"
      );
    }
    documentoFinal = dni2;
  }

  let extension = foto.split(".").pop();
  let nombrefoto = "";
  let f = new Date();
  if (foto.length > 0) {
    nombrefoto =
      "IMG" +
      f.getDate() +
      "-" +
      (f.getMonth() + 1) +
      "-" +
      f.getFullYear() +
      "-" +
      f.getHours() +
      "-" +
      f.getMilliseconds() +
      "." +
      extension;
  }
  //CONDICIONANDO LOS CAMPOS VACIOS

  let formData = new FormData();
  let fotoobj = $("#txt_foto")[0].files[0];

  formData.append("tipo_doc", tipo_doc);
  formData.append("documentoFinal", documentoFinal);
  formData.append("nom_ape", nom_ape);
  formData.append("celu", celu);
  formData.append("celu2", celu2);
  formData.append("proc", proc);
  formData.append("dire", dire);
  formData.append("nombrefoto", nombrefoto);
  formData.append("foto", fotoobj);

  formData.append("marca", marca);
  formData.append("placa", placa);
  formData.append("clase_cate", clase_cate);
  formData.append("nro_lice", nro_lice);
  formData.append("fec_ven", fec_ven);
  formData.append("idusuario", idusuario);

  $.ajax({
    url: "../controller/choferes/controlador_registro_choferes.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (resp) {
      if (resp.length > 0) {
        if (resp == 1) {
          Swal.fire(
            "Mensaje de Confirmación",
            "Se registro correctamente al chofer con el DNI N° <b>" +
              documentoFinal +
              "</b>",
            "success"
          ).then((value) => {
            // Limpiar todos los campos
            document.getElementById("txt_dni").value = "";
            document.getElementById("txt_dni2").value = "";
            document.getElementById("txt_nomb").value = "";
            document.getElementById("txt_celu1").value = "";
            document.getElementById("txt_celu2").value = "";
            document.getElementById("txt_procedencia").value = "";
            document.getElementById("txt_direc").value = "";

            document.getElementById("txt_marca").value = "";
            document.getElementById("txt_placa").value = "";
            document.getElementById("txt_clase_categoria").value = "";
            document.getElementById("txt_nro_licencia").value = "";
            document.getElementById("txt_fecha_expira").value = "";

            // Limpiar la vista previa de la imagen
            document.getElementById("preview").src = "#";
            document.getElementById("preview").alt = "Vista previa";

            // Cerrar el modal
            $("#modal_registro").modal("hide");
            tbl_encomiendas.ajax.reload();
          });
        } else {
          Swal.fire(
            "Mensaje de Advertencia",
            "El DNI que intentas registrar ya se encuentra en la base de datos, revise por favor",
            "warning"
          );
        }
      } else {
        Swal.fire(
          "Mensaje de Advertencia",
          "No se pudo registrar al usuario",
          "warning"
        );
      }
    },
  });
}

function Modificar_Choferes() {
  //DATOS DEL DOCENTE
  let id = document.getElementById("id_chofer").value;
  let dni = document.getElementById("txt_dni_editar").value;
  let nom_ape = document.getElementById("txt_nomb_editar").value;
  let celu1 = document.getElementById("txt_celu1_editar").value;
  let celu2 = document.getElementById("txt_celu2_editar").value;
  let proc = document.getElementById("txt_procedencia_editar").value;
  let dire = document.getElementById("txt_direc_editar").value;
  let fotoactual = document.getElementById("txt_foto_actual").value;
  let foto = document.getElementById("txt_foto_editar").value;

  //DATOS DEL CARRO
  let marca = document.getElementById("txt_marca_editar").value;
  let placa = document.getElementById("txt_placa_editar").value;
  let clase_cate = document.getElementById("txt_clase_categoria_editar").value;
  let nro_lice = document.getElementById("txt_nro_licencia_editar").value;
  let fec_ven = document.getElementById("txt_fecha_expira_editar").value;
  let esta = document.getElementById("select_estado_editar").value;
  let idusuario = document.getElementById("txtprincipalid").value;

  if (
    id.length == 0 ||
    dni.length == 0 ||
    nom_ape.length == 0 ||
    celu1.length == 0 ||
    marca.length == 0 ||
    placa.length == 0 ||
    clase_cate.length == 0 ||
    nro_lice.length == 0 ||
    fec_ven.length == 0
  ) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Los campos obligatorios siempre deben ir llenos",
      "warning"
    );
  }

  let extension = foto.split(".").pop();
  let nombrefoto = "";
  let f = new Date();
  if (foto.length > 0) {
    nombrefoto =
      "IMG" +
      f.getDate() +
      "-" +
      (f.getMonth() + 1) +
      "-" +
      f.getFullYear() +
      "-" +
      f.getHours() +
      "-" +
      f.getMilliseconds() +
      "." +
      extension;
  }
  //CONDICIONANDO LOS CAMPOS VACIOS

  let formData = new FormData();
  let fotoobj = $("#txt_foto_editar")[0].files[0];

  formData.append("id", id);
  formData.append("dni", dni);
  formData.append("nom_ape", nom_ape);
  formData.append("celu1", celu1);
  formData.append("celu2", celu2);
  formData.append("proc", proc);
  formData.append("dire", dire);
  formData.append("fotoactual", fotoactual);
  formData.append("nombrefoto", nombrefoto);
  formData.append("foto", fotoobj);

  formData.append("marca", marca);
  formData.append("placa", placa);
  formData.append("clase_cate", clase_cate);
  formData.append("nro_lice", nro_lice);
  formData.append("fec_ven", fec_ven);
  formData.append("esta", esta);
  formData.append("idusuario", idusuario);

  $.ajax({
    url: "../controller/choferes/controlador_modificar_choferes.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (resp) {
      if (resp.length > 0) {
        if (resp == 1) {
          Swal.fire(
            "Mensaje de Confirmación",
            "Se actualizo correctamente el chofer con el DNI N° <b>" +
              dni +
              "</b>",
            "success"
          ).then((value) => {
            // Cerrar el modal
            $("#modal_editar").modal("hide");
            tbl_encomiendas.ajax.reload();
            document.getElementById("txt_foto_editar").value = "";
          });
        } else {
          Swal.fire(
            "Mensaje de Advertencia",
            "El DNI que intentas actualizar ya se encuentra en la base de datos, revise por favor",
            "warning"
          );
        }
      } else {
        Swal.fire(
          "Mensaje de Advertencia",
          "No se pudo actualizar al usuario",
          "warning"
        );
      }
    },
  });
}

//ELIMINAR AREAS
function Eliminar_chofer(id) {
  $.ajax({
    url: "../controller/choferes/controlador_eliminar_chofer.php",
    type: "POST",
    data: {
      id: id,
    },
  }).done(function (resp) {
    if (resp > 0) {
      Swal.fire(
        "Mensaje de Confirmación",
        "Se elimino el chofer con exito",
        "success"
      ).then((value) => {
        tbl_encomiendas.ajax.reload();
      });
    } else {
      return Swal.fire(
        "Mensaje de Advetencia",
        "No se puede eliminar esta CHOFER por que esta siendo utilizado en otros módulos como encomienda y salidas diarias, verifique por favor",
        "warning"
      );
    }
  });
}

//ENVIANDO AL BOTON DELETE
$("#tabla_encomiendas").on("click", ".eliminar", function () {
  var data = tbl_encomiendas.row($(this).parents("tr")).data();

  if (tbl_encomiendas.row(this).child.isShown()) {
    var data = tbl_encomiendas.row(this).data();
  }
  Swal.fire({
    title:
      "Desea eliminar al chofer con el nombre: " + data.nombres_apellidos + "?",
    text: "Una vez aceptado el chofer sera eliminado!!!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Eliminar",
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_chofer(data.id_chofer);
    }
  });
});




function Cargar_Select_Rutas() {
  $.ajax({
    url: "../controller/rutas/controlador_cargar_select_rutas.php",
    type: "POST",
  }).done(function (resp) {
    let data = JSON.parse(resp);
    let cadena = "<option value=''>Seleccionar conductor</option>";

    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) {
        cadena +=
          "<option value='"+data[i][0]+"'>"+data[i][1]+"</option>";
      }
    } else {
      cadena += "<option value=''>No hay conductores disponibles</option>";
    }

    $("#select_origen").html(cadena);
        $("#select_destino").html(cadena);

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