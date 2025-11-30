//LISTAR ENCOMIENDAS
var tbl_nota_credito;

function listar_nota_salida() {
  Cargar_Select_Usuarios();
  Cargar_Select_Rutas();
  document.getElementById("txt_fecha_desde").value = "";
  document.getElementById("txt_fecha_hasta").value = "";
  document.getElementById("select_estado_buscar").value = "";

  tbl_nota_credito = $("#tabla_nota_credito").DataTable({
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
    ajax: {
      url: "../controller/tickets/controller_listar_tickets.php",
      type: "POST",
    },
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excelHtml5",
        text: '<i class="fas fa-file-excel"></i> Excel',
        titleAttr: "Exportar a Excel",
        filename: "TICKETS_VIAJE",
        title: "TICKETS DE VIAJE",
        className: "btn btn-success btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
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
        filename: "TICKETS_VIAJE",
        title: "TICKETS DE VIAJE",
        className: "btn btn-danger btn-sm",
        orientation: "landscape",
        pageSize: "A4",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
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
        title: "TICKETS DE VIAJE",
        className: "btn btn-info btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
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
      { defaultContent: "" },
      { data: "numero_ticket" },
      { data: "fecha_viaje" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            "<strong>" +
            row.nro_documento +
            "</strong><br>" +
            row.cliente
          );
        },
      },
      { data: "servicio" },
      { data: "origen" },
      { data: "destino" },
      {
        data: "total",
        render: function (data, type, row) {
          return '<span class="badge bg-success">S/ ' + data + "</span>";
        },
      },
      {
        data: "estado",
        render: function (data, type, row) {
          if (data == "VALIDO") {
            return '<span class="badge bg-success">VÁLIDO</span>';
          } else {
            return '<span class="badge bg-danger">ANULADO</span>';
          }
        },
      },
            { data: "usuario" },


    {
  data: null,
  defaultContent: "",
  render: function (data, type, row) {
    let botones = "";

    if (row.estado === "VALIDO") {
      botones = `
        <button class='imprimir btn btn-info btn-sm' title='Imprimir ticket'>
          <i class='fa fa-print'></i> Imprimir
        </button>

        <button class='editar btn btn-primary btn-sm' title='Editar ticket'>
          <i class='fa fa-edit'></i> Editar
        </button>

        <button class='mostrar btn btn-success btn-sm' title='Mostrar ticket'>
          <i class='fa fa-eye'></i> Mostrar
        </button>

        <button class='anular btn btn-danger btn-sm' title='Anular ticket'>
          <i class='fa fa-trash'></i> Anular
        </button>
      `;
    } else {
      botones = `
        <button class='ver_motivo btn btn-warning btn-sm' title='Ver motivo de anulación'>
          <i class='fa fa-info-circle'></i> Motivo
        </button>
      `;
    }

    return botones;
  },
},

    ],
    language: idioma_espanol,
    select: true,
  });
  tbl_nota_credito.on("draw.td", function () {
    var PageInfo = $("#tabla_nota_credito").DataTable().page.info();
    tbl_nota_credito
      .column(0, { page: "current" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1 + PageInfo.start;
      });
  });
}

function listar_nota_salida_pordia() {
  tbl_nota_credito = $("#tabla_nota_credito").DataTable({
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
    ajax: {
      url: "../controller/tickets/controller_listar_tickets_pordia.php",
      type: "POST",
    },
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excelHtml5",
        text: '<i class="fas fa-file-excel"></i> Excel',
        titleAttr: "Exportar a Excel",
        filename: "TICKETS_VIAJE",
        title: "TICKETS DE VIAJE",
        className: "btn btn-success btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
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
        filename: "TICKETS_VIAJE",
        title: "TICKETS DE VIAJE",
        className: "btn btn-danger btn-sm",
        orientation: "landscape",
        pageSize: "A4",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
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
        title: "TICKETS DE VIAJE",
        className: "btn btn-info btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
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
      { defaultContent: "" },
      { data: "numero_ticket" },
      { data: "fecha_viaje" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            "<strong>" +
            row.nro_documento +
            "</strong><br>" +
            row.cliente
          );
        },
      },
      { data: "servicio" },
      { data: "origen" },
      { data: "destino" },
      {
        data: "total",
        render: function (data, type, row) {
          return '<span class="badge bg-success">S/ ' + data + "</span>";
        },
      },
      {
        data: "estado",
        render: function (data, type, row) {
          if (data == "VALIDO") {
            return '<span class="badge bg-success">VÁLIDO</span>';
          } else {
            return '<span class="badge bg-danger">ANULADO</span>';
          }
        },
      },
            { data: "usuario" },


    {
  data: null,
  defaultContent: "",
  render: function (data, type, row) {
    let botones = "";

    if (row.estado === "VALIDO") {
      botones = `
        <button class='imprimir btn btn-info btn-sm' title='Imprimir ticket'>
          <i class='fa fa-print'></i> Imprimir
        </button>

        <button class='editar btn btn-primary btn-sm' title='Editar ticket'>
          <i class='fa fa-edit'></i> Editar
        </button>

        <button class='mostrar btn btn-success btn-sm' title='Mostrar ticket'>
          <i class='fa fa-eye'></i> Mostrar
        </button>

        <button class='anular btn btn-danger btn-sm' title='Anular ticket'>
          <i class='fa fa-trash'></i> Anular
        </button>
      `;
    } else {
      botones = `
        <button class='ver_motivo btn btn-warning btn-sm' title='Ver motivo de anulación'>
          <i class='fa fa-info-circle'></i> Motivo
        </button>
      `;
    }

    return botones;
  },
},

    ],
    language: idioma_espanol,
    select: true,
  });
  tbl_nota_credito.on("draw.td", function () {
    var PageInfo = $("#tabla_nota_credito").DataTable().page.info();
    tbl_nota_credito
      .column(0, { page: "current" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1 + PageInfo.start;
      });
  });
}

function listar_nota_ruta_estado() {
  let ori = document.getElementById("select_origen_bus").value;
  let des = document.getElementById("select_destino_bus").value;
  let esta = document.getElementById("select_estado_buscar").value;

  tbl_nota_credito = $("#tabla_nota_credito").DataTable({
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
    ajax: {
      url: "../controller/tickets/controller_listar_tickets_filtro1.php",
      type: "POST",
      data: {
        ori: ori,
        des: des,
        esta: esta,
      },
    },
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excelHtml5",
        text: '<i class="fas fa-file-excel"></i> Excel',
        titleAttr: "Exportar a Excel",
        filename: "TICKETS_VIAJE",
        title: "TICKETS DE VIAJE",
        className: "btn btn-success btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
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
        filename: "TICKETS_VIAJE",
        title: "TICKETS DE VIAJE",
        className: "btn btn-danger btn-sm",
        orientation: "landscape",
        pageSize: "A4",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
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
        title: "TICKETS DE VIAJE",
        className: "btn btn-info btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
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
      { defaultContent: "" },
      { data: "numero_ticket" },
      { data: "fecha_viaje" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            "<strong>" +
            row.nro_documento +
            "</strong><br>" +
            row.cliente
          );
        },
      },
      { data: "servicio" },
      { data: "origen" },
      { data: "destino" },
      {
        data: "total",
        render: function (data, type, row) {
          return '<span class="badge bg-success">S/ ' + data + "</span>";
        },
      },
      {
        data: "estado",
        render: function (data, type, row) {
          if (data == "VALIDO") {
            return '<span class="badge bg-success">VÁLIDO</span>';
          } else {
            return '<span class="badge bg-danger">ANULADO</span>';
          }
        },
      },
            { data: "usuario" },


      {
  data: null,
  defaultContent: "",
  render: function (data, type, row) {
    let botones = "";

    if (row.estado === "VALIDO") {
      botones = `
        <button class='imprimir btn btn-info btn-sm' title='Imprimir ticket'>
          <i class='fa fa-print'></i> Imprimir
        </button>

        <button class='editar btn btn-primary btn-sm' title='Editar ticket'>
          <i class='fa fa-edit'></i> Editar
        </button>

        <button class='mostrar btn btn-success btn-sm' title='Mostrar ticket'>
          <i class='fa fa-eye'></i> Mostrar
        </button>

        <button class='anular btn btn-danger btn-sm' title='Anular ticket'>
          <i class='fa fa-trash'></i> Anular
        </button>
      `;
    } else {
      botones = `
        <button class='ver_motivo btn btn-warning btn-sm' title='Ver motivo de anulación'>
          <i class='fa fa-info-circle'></i> Motivo
        </button>
      `;
    }

    return botones;
  },
},

    ],
    language: idioma_espanol,
    select: true,
  });
  tbl_nota_credito.on("draw.td", function () {
    var PageInfo = $("#tabla_nota_credito").DataTable().page.info();
    tbl_nota_credito
      .column(0, { page: "current" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1 + PageInfo.start;
      });
  });
}

function listar_reservas_fecha_usu() {
  let fedes = document.getElementById("txt_fecha_desde").value;
  let fehas = document.getElementById("txt_fecha_hasta").value;
  let usu = document.getElementById("select_usuario").value;

  tbl_nota_credito = $("#tabla_nota_credito").DataTable({
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
    ajax: {
      url: "../controller/tickets/controller_listar_tickets_filtro2.php",
      type: "POST",
      data: {
        fedes: fedes,
        fehas: fehas,
        usu: usu,
      },
    },
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excelHtml5",
        text: '<i class="fas fa-file-excel"></i> Excel',
        titleAttr: "Exportar a Excel",
        filename: "TICKETS_VIAJE",
        title: "TICKETS DE VIAJE",
        className: "btn btn-success btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
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
        filename: "TICKETS_VIAJE",
        title: "TICKETS DE VIAJE",
        className: "btn btn-danger btn-sm",
        orientation: "landscape",
        pageSize: "A4",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
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
        title: "TICKETS DE VIAJE",
        className: "btn btn-info btn-sm",
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
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
      { defaultContent: "" },
      { data: "numero_ticket" },
      { data: "fecha_viaje" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            "<strong>" +
            row.nro_documento +
            "</strong><br>" +
            row.cliente
          );
        },
      },
      { data: "servicio" },
      { data: "origen" },
      { data: "destino" },
      {
        data: "total",
        render: function (data, type, row) {
          return '<span class="badge bg-success">S/ ' + data + "</span>";
        },
      },
      {
        data: "estado",
        render: function (data, type, row) {
          if (data == "VALIDO") {
            return '<span class="badge bg-success">VÁLIDO</span>';
          } else {
            return '<span class="badge bg-danger">ANULADO</span>';
          }
        },
      },
            { data: "usuario" },


    {
  data: null,
  defaultContent: "",
  render: function (data, type, row) {
    let botones = "";

    if (row.estado === "VALIDO") {
      botones = `
        <button class='imprimir btn btn-info btn-sm' title='Imprimir ticket'>
          <i class='fa fa-print'></i> Imprimir
        </button>

        <button class='editar btn btn-primary btn-sm' title='Editar ticket'>
          <i class='fa fa-edit'></i> Editar
        </button>

        <button class='mostrar btn btn-success btn-sm' title='Mostrar ticket'>
          <i class='fa fa-eye'></i> Mostrar
        </button>

        <button class='anular btn btn-danger btn-sm' title='Anular ticket'>
          <i class='fa fa-trash'></i> Anular
        </button>
      `;
    } else {
      botones = `
        <button class='ver_motivo btn btn-warning btn-sm' title='Ver motivo de anulación'>
          <i class='fa fa-info-circle'></i> Motivo
        </button>
      `;
    }

    return botones;
  },
},

    ],
    language: idioma_espanol,
    select: true,
  });
  tbl_nota_credito.on("draw.td", function () {
    var PageInfo = $("#tabla_nota_credito").DataTable().page.info();
    tbl_nota_credito
      .column(0, { page: "current" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1 + PageInfo.start;
      });
  });
}

//ABRIR MODAL REGISTRO
function AbrirRegistro() {
  $("#modal_registro").modal({ backdrop: "static", keyboard: false });
  $("#modal_registro").modal("show");
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

//EDITAR BUSQUEDA

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

//ABRIR MODAL VER MOTIVO ANULACION
$("#tabla_nota_credito").on("click", ".ver_motivo", function () {
  var data = tbl_nota_credito.row($(this).parents("tr")).data();

  if (tbl_nota_credito.row(this).child.isShown()) {
    var data = tbl_nota_credito.row(this).data();
  }
  $("#modal_motivo_anula").modal("show");
  console.log(data);
  document.getElementById("txt_fecha_anula").value = data.fecha_anulacion;
  document.getElementById("txt_cliente").value = data.cliente;

    document.getElementById("txt_motivo_anulacion").value = data.motivo_anulacion;

});



$('#tabla_nota_credito').on('click','.editar',function(){
  var data = tbl_nota_credito.row($(this).parents('tr')).data();

  if(tbl_nota_credito.row(this).child.isShown()){
      var data = tbl_nota_credito.row(this).data();
  }
  $("#modal_editar").modal('show');
  document.getElementById('txt_id_nota').value=data.id;
  document.getElementById('select_tipo_documento_emisor_editar').value=data.tipo_documento;
  document.getElementById('txt_nro_documento_editar').value=data.nro_documento;
  document.getElementById('txt_nomb_emisor_editar').value=data.cliente;
  document.getElementById('txt_celu1_emisor_editar').value=data.celular;


      $("#select_servicio_editar").val(data.idservicio).trigger("change");

      $("#select_origen_editar").val(data.idorigen).trigger("change");
      $("#select_destino_editar").val(data.iddestino).trigger("change");

      document.getElementById("txt_base_gravada_editar").value =
        data.gravada;
      document.getElementById("txt_igv_editar").value =
        data.igv;
      document.getElementById("txt_total_editar").value =
        data.total;
})

//ABRIR MODAL MOSTRAR
$("#tabla_nota_credito").on("click", ".mostrar", function () {
  var data = tbl_nota_credito.row($(this).parents("tr")).data();
  if (tbl_nota_credito.row(this).child.isShown()) {
    var data = tbl_nota_credito.row(this).data();
  }
  $("#modal_mostrar").modal("show");

  // CAMPOS EXISTENTES
  document.getElementById('select_tipo_documento_emisor_mostrar').value=data.tipo_documento;
  document.getElementById('txt_nrodoc_mostrar').value=data.nro_documento;
  document.getElementById('txt_nomb_emisor_mostrar').value=data.cliente;
  document.getElementById('txt_celu1_emisor_mostrar').value=data.celular;

  document.getElementById("txt_fecha_emitida_mostrar").value =
      data.fecha_emision;
    $("#select_servicio_mostrar").val(data.idservicio).trigger("change");

    $("#select_origen_mostrar").val(data.idorigen).trigger("change");
    $("#select_destino_mostrar").val(data.iddestino).trigger("change");

    document.getElementById("txt_base_gravada_mostrar").value =
      data.gravada;
    document.getElementById("txt_igv_mostrar").value =
      data.igv;
    document.getElementById("txt_total_mostrar").value =
      data.total;
});
//LIMPIAR CAMPOS
function LimpiarCampos() {
  // CAMPOS PRINCIPALES
  document.getElementById("txt_dni_emisor").value = "";
  document.getElementById("txt_dni_emisor2").value = "";
  document.getElementById("txt_nomb_emisor").value = "";
  document.getElementById("txt_celu1_emisor").value = ""; // CORREGIDO: era txtxt_descripciont_fecha_creacion

  // DATOS DEL EMISOR
  document.getElementById("select_origen").value = "";
  document.getElementById("select_destino").value = "";
  document.getElementById("txt_base_gravada").value = "";
  document.getElementById("txt_igv").value = "";
    document.getElementById("txt_total").value = "";

}
//REGISTROS DE ENCOMIENDAS
function Registrar_tickets() {
  // DATOS DEL PASAJERO
  let tipodocemi = document.getElementById(
    "select_tipo_documento_emisor"
  ).value;
  let dniemi = document.getElementById("txt_dni_emisor").value;
  let dni2emi = document.getElementById("txt_dni_emisor2").value;
  let nomemi = document.getElementById("txt_nomb_emisor").value;
  let celemi = document.getElementById("txt_celu1_emisor").value;

  // DATOS DE LA RESERVA
  let fechare = document.getElementById("txt_fecha_emitida").value;
  let ser = document.getElementById("select_servicio").value;
  let ori = document.getElementById("select_origen").value;
  let des = document.getElementById("select_destino").value;
  let basegr = document.getElementById("txt_base_gravada").value;
  let igv = document.getElementById("txt_igv").value;
  let total = document.getElementById("txt_total").value;

  let idusu = document.getElementById("txtprincipalid").value;

  // Obtener el nombre del destino (opcional)
  let selectDestino = document.getElementById("select_destino");
  let nombre_destino = selectDestino.options[selectDestino.selectedIndex].text;

  // 🔹 Validar campos vacíos
  if (
    tipodocemi.length == 0 ||
    nomemi.length == 0 ||
    fechare.length == 0 ||
    ori.length == 0 ||
    basegr.length == 0 ||
    igv.length == 0 ||
    total.length == 0

  ) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Todos los campos son obligatorios",
      "warning"
    );
  }

  // 🔹 Validar que ORIGEN y DESTINO no sean iguales
  if (ori === des) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "El ORIGEN y DESTINO no pueden ser iguales",
      "warning"
    );
  }

  // 🔹 Validar documento según tipo EMISOR
  let documentoFinal = "";
  if (tipodocemi === "DNI") {
    if (!dniemi) {
      return Swal.fire(
        "Mensaje de Advertencia",
        "El campo DNI del pasajero es obligatorio",
        "warning"
      );
    }
    documentoFinal = dniemi;
  } else {
    if (!dni2emi) {
      return Swal.fire(
        "Mensaje de Advertencia",
        "El campo de documento del pasajero es obligatorio",
        "warning"
      );
    }
    documentoFinal = dni2emi;
  }

  // 🔹 Enviar por AJAX
  $.ajax({
    url: "../controller/tickets/controller_registrar_ticket.php",
    type: "POST",
    data: {
      tipodocemi: tipodocemi,
      documento: documentoFinal,
      nomemi: nomemi,
      celemi: celemi,
      ser: ser,
      ori: ori,
      des: des,
      basegr: basegr,
      igv: igv,
      total: total,
      idusu: idusu,
    },
  }).done(function (resp) {
    if (resp > 0) {
      if (resp == 1) {
        Swal.fire(
          "Mensaje de Confirmación",
          "Nueva salida registrada para el pasajero: <b>" + nomemi + "</b>",
          "success"
        ).then(() => {
          tbl_nota_credito.ajax.reload();
          LimpiarCampos();
          $("#modal_registro").modal("hide");
        });
      } else {
        Swal.fire(
          "Mensaje de Advertencia",
          "La salida que intentas registrar ya se encuentra en la base de datos, revise por favor",
          "warning"
        );
      }
    } else {
      Swal.fire("Mensaje de Error", "No se completó el registro", "error");
    }
  });
}

//LIMPIAR CAMPOS
function LimpiarCamposEncomienda2() {
  // CAMPOS PRINCIPALES
  document.getElementById("txt_dni_emisor_editar").value = "";
  document.getElementById("txt_dni_emisor2_editar").value = "";
  document.getElementById("txt_nomb_emisor_editar").value = "";
  document.getElementById("txt_celu1_emisor_editar").value = ""; // CORREGIDO: era txtxt_descripciont_fecha_creacion

  // DATOS DEL EMISOR
  document.getElementById("txt_fecha_viaje_editar").value = "";
  document.getElementById("select_origen_editar").value = "";
  document.getElementById("select_destino_editar").value = "";
  document.getElementById("txt_monto_adelantado_editar").value = "";
  document.getElementById("txt_observacion_editar").value = "";
}
function Modificar_Reservas() {
  //DATOS DEL DOCENTE
  let idreserva = document.getElementById("txt_idreserva").value;
  let tipodocemi = document.getElementById(
    "select_tipo_documento_emisor_editar"
  ).value;
  let dniemi = document.getElementById("txt_dni_emisor_editar").value;
  let dni2emi = document.getElementById("txt_dni_emisor2_editar").value;
  let nomemi = document.getElementById("txt_nomb_emisor_editar").value;
  let celemi = document.getElementById("txt_celu1_emisor_editar").value;

  // DATOS DEL RECEPTOR
  let fechare = document.getElementById("txt_fecha_rerserva_editar").value;
  let fechavia = document.getElementById("txt_fecha_viaje_editar").value;
  let ori = document.getElementById("select_origen_editar").value;
  let des = document.getElementById("select_destino_editar").value;
  let monto = document.getElementById("txt_monto_adelantado_editar").value;
  let obser = document.getElementById("txt_observacion_editar").value;
  let idusu = document.getElementById("txtprincipalid").value;

  if (
    tipodocemi.length == 0 ||
    nomemi.length == 0 ||
    celemi.length == 0 ||
    fechare.length == 0 ||
    fechavia.length == 0 ||
    ori.length == 0 ||
    monto.length == 0
  ) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Todo los campos son obligatorios",
      "warning"
    );
  }
  if (ori === des) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "El ORIGEN y DESTINO no pueden ser iguales",
      "warning"
    );
  }
  // Validar documento según tipo EMISOR
  let documentoFinal = "";
  if (tipodocemi === "DNI") {
    if (!dniemi) {
      return Swal.fire(
        "Mensaje de Advertencia",
        "El campo DNI del pasajero es obligatorio",
        "warning"
      );
    }
    documentoFinal = dniemi;
  } else {
    if (!dni2emi) {
      return Swal.fire(
        "Mensaje de Advertencia",
        "El campo de documento del pasajero es obligatorio",
        "warning"
      );
    }
    documentoFinal = dni2emi;
  }

  $.ajax({
    url: "../controller/reservas/controlador_modificar_reservas.php",
    type: "POST",
    data: {
      idreserva: idreserva,
      tipodocemi: tipodocemi,
      documento: documentoFinal,
      nomemi: nomemi,
      celemi: celemi,
      fechare: fechare,
      fechavia: fechavia,
      ori: ori,
      des: des,
      monto: monto,
      obser: obser,
      idusu: idusu,
    },
  }).done(function (resp) {
    if (resp > 0) {
      if (resp == 1) {
        Swal.fire(
          "Mensaje de Confirmación",
          "Se modifico la reserva para el pasajero: <b>" + nomemi + "</b>",
          "success"
        ).then((value) => {
          tbl_nota_credito.ajax.reload();
          LimpiarCamposEncomienda2();
          $("#modal_editar").modal("hide");
        });
      } else {
        Swal.fire(
          "Mensaje de Advertencia",
          "La reserva que intentas modificar ya se encuentra en la base de datos, revise por favor",
          "warning"
        );
      }
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se completo el registro",
        "error"
      );
    }
  });
}





function Anular_nota_salida(id, motivo){
  $.ajax({
    url: "../controller/tickets/controlador_anular_nota_salida.php",
    type: 'POST',
    data: {
      id: id,
      motivo: motivo
    }
  }).done(function(resp){
    if(resp > 0){
        Swal.fire(
          "Mensaje de Confirmación",
          "La nota de salida fue anulada con éxito",
          "success"
        ).then(() => {
          tbl_nota_credito.ajax.reload();
        });
    }else{
      Swal.fire(
        "Mensaje de Advertencia",
        "No se pudo anular la nota de salida, verifique por favor",
        "warning"
      );
    }
  });
}


//ENVIANDO AL BOTON DELETE
$('#tabla_nota_credito').on('click', '.anular', function() {
  var data = tbl_nota_credito.row($(this).parents('tr')).data();

  if (tbl_nota_credito.row(this).child.isShown()) {
    data = tbl_nota_credito.row(this).data();
  }

  Swal.fire({
    title: "¿Desea anular la nota de salida del cliente: " + data.cliente + "?",
    text: "Una vez aceptado la nota será anulada.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, anular",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {

      // 2. AHORA SE PIDE EL MOTIVO
      Swal.fire({
        title: "Ingrese el motivo de anulación",
        input: "textarea",
        inputPlaceholder: "Escriba el motivo...",
        inputAttributes: {
          maxlength: 300,
          "aria-label": "Motivo"
        },
        showCancelButton: true,
        confirmButtonText: "Guardar Motivo",
        cancelButtonText: "Cancelar",
        preConfirm: (motivo) => {
          if (!motivo || motivo.trim() === "") {
            Swal.showValidationMessage("El motivo es obligatorio");
          }
          return motivo;
        }
      }).then((respMotivo) => {
        if (respMotivo.isConfirmed) {
          const motivo = respMotivo.value;
          Anular_nota_salida(data.id, motivo);
        }
      });

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

    $("#select_origen_mostrar").html(cadena);
    $("#select_destino_mostrar").html(cadena);

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



// ============================================================
// CARGAR SERVICIOS AL INICIAR
// ============================================================
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
        $("#select_servicio_editar").html(cadena);
        $("#select_servicio_mostrar").html(cadena);

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
          let total = data[0].monto || data[0][1];

          // Siempre el backend devuelve precio unitario con IGV
          document.getElementById("txt_total").value = parseFloat(total).toFixed(2);
          
          calcularDesdeTotal(); // recalcular BG + IGV
        } else {
          resetCamposCalculo();
        }
      } catch (e) {
        console.error("JSON inválido:", resp);
        resetCamposCalculo();
      }
    })
    .fail(function () {
      console.error("Error AJAX");
      resetCamposCalculo();
    });
}

function resetCamposCalculo() {
  document.getElementById("txt_base_gravada").value = "";
  document.getElementById("txt_igv").value = "";
  document.getElementById("txt_total").value = "";
}

// 🔄 Función para calcular BASE GRAVADA desde el TOTAL
function calcularDesdeTotal() {
  var totalConIGV = parseFloat(document.getElementById("txt_total").value) || 0;

  if (totalConIGV <= 0) {
    document.getElementById("txt_base_gravada").value = "";
    document.getElementById("txt_igv").value = "";
    return;
  }

  // Cantidad SIEMPRE es 1
  var baseGravada = totalConIGV / 1.18;
  var igv = baseGravada * 0.18;

  document.getElementById("txt_base_gravada").value = baseGravada.toFixed(2);
  document.getElementById("txt_igv").value = igv.toFixed(2);
  document.getElementById("txt_total").value = totalConIGV.toFixed(2);
}


// ============================================================
// EVENTOS - Conectar con los campos del formulario
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    let inputTotal = document.getElementById("txt_total");

    if (inputTotal) {
        inputTotal.addEventListener("input", calcularDesdeTotal);
        inputTotal.addEventListener("blur", calcularDesdeTotal);
    }
});
$(document).on("keyup", "#txt_total", function () {
    calcularDesdeTotal();
});

// IMPRIMIR TICKET
$('#tabla_nota_credito').on('click', '.imprimir', function() {
  var data = tbl_nota_credito.row($(this).parents('tr')).data();
  
  if (tbl_nota_credito.row(this).child.isShown()) {
      var data = tbl_nota_credito.row(this).data();
  }
  window.open('../reportes/ticket_viaje.php?id='+data.id, '_blank');
});