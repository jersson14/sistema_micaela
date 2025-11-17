//LISTAR ENCOMIENDAS
var tbl_reservas;

function listar_reservas() {
  Cargar_Select_Usuarios();
  Cargar_Select_Rutas();
  document.getElementById("txt_fecha_desde").value = "";
  document.getElementById("txt_fecha_hasta").value = "";
  document.getElementById("select_estado_buscar").value = "";



  tbl_reservas = $("#tabla_reservas").DataTable({
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
      url: "../controller/reservas/controlador_listar_reservas.php",
      type: "POST",
    },
    dom: "Bfrtip",

    buttons: [
  {
    extend: "excelHtml5",
    text: '<i class="fas fa-file-excel"></i> Excel',
    titleAttr: "Exportar a Excel",
    filename: "NOMBRE_DEL_MODULO",
    title: "NOMBRE_DEL_MODULO",
    className: "btn btn-success btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9], // Ajustar según columnas
      format: {
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          if (column === 1 && cleanData) {
            cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
          }
          return cleanData;
        }
      }
    }
  },
  {
    extend: "pdfHtml5",
    text: '<i class="fas fa-file-pdf"></i> PDF',
    titleAttr: "Exportar a PDF",
    filename: "NOMBRE_DEL_MODULO",
    title: "NOMBRE_DEL_MODULO",
    className: "btn btn-danger btn-sm",
    orientation: "landscape",
    pageSize: "A4",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
      format: {
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          if (column === 1 && cleanData) {
            cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
          }
          return cleanData;
        }
      }
    }
  },
  {
    extend: "print",
    text: '<i class="fa fa-print"></i> Imprimir',
    titleAttr: "Imprimir",
    title: "NOMBRE_DEL_MODULO",
    className: "btn btn-info btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
      format: {
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          if (column === 1 && cleanData) {
            cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
          }
          return cleanData;
        }
      }
    }
  }
],

    columns: [
      { defaultContent: "" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            "<strong>" +
            row.tipo_documento +
            ": " +
            row.nro_documento +
            "</strong><br>" +
            row.nombre_completo
          );
        },
      },
      { data: "fecha_reserva_formateada" },
      { data: "fecha_viaje_formateada" },

      { data: "nombre_origen" },
      { data: "nombre_destino" },

      { data: "celular" },

      // ---- PAGO ----
      {
        data: "monto_adelantado",
        render: function (data, type, row) {
          if (parseFloat(data) > 0) {
            return '<span class="badge bg-success">S/ ' + data + "</span>";
          } else {
            return '<span class="badge bg-secondary">-</span>';
          }
        },
      },

      // ---- ESTADO RESERVA ----
      {
        data: "estado",
        render: function (data, type, row) {
          if (data == "PENDIENTE") {
            return '<span class="badge bg-warning">PENDIENTE</span>';
          } else if (data == "COMPLETADO") {
            return '<span class="badge bg-success">COMPLETADO</span>';
          } else {
            return '<span class="badge bg-danger text-dark">ANULADO</span>';
          }
        },
      },
      { data: "USUARIO" },
      {
        data: null,
        defaultContent: "",
        render: function (data, type, row) {
          let botones = "";

          if (row.estado === "PENDIENTE") {
            // MOSTRAR TODOS LOS BOTONES
            botones = `
        <button class='mostrar btn btn-success btn-sm' title='Mostrar datos de reserva'>
          <i class='fa fa-eye'></i> Mostrar
        </button>
        <button class='editar btn btn-primary btn-sm' title='Editar datos de reserva'>
          <i class='fa fa-edit'></i> Editar
        </button>
        <button class='anular btn btn-danger btn-sm' title='Anular reserva'>
          <i class='fa fa-trash'></i> Anular
        </button>
      `;
          } else if (row.estado === "COMPLETADO") {
            // SOLO MOSTRAR
            botones = `
        <button class='mostrar btn btn-success btn-sm' title='Mostrar datos de reserva'>
          <i class='fa fa-eye'></i> Mostrar
        </button>
      `;
          } else if (row.estado === "ANULADO") {
            // SOLO MOTIVO ANULACIÓN
            botones = `
        <button class='ver_motivo btn btn-warning btn-sm' title='Ver motivo de anulación'>
          <i class='fa fa-info-circle'></i> Motivo Anulación
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
  tbl_reservas.on("draw.td", function () {
    var PageInfo = $("#tabla_reservas").DataTable().page.info();
    tbl_reservas
      .column(0, { page: "current" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1 + PageInfo.start;
      });
  });
}

function listar_reservas_pordia() {
  tbl_reservas = $("#tabla_reservas").DataTable({
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
      url: "../controller/reservas/controlador_listar_reservas_pordia.php",
      type: "POST",
    },
    dom: "Bfrtip",

  buttons: [
  {
    extend: "excelHtml5",
    text: '<i class="fas fa-file-excel"></i> Excel',
    titleAttr: "Exportar a Excel",
    filename: "NOMBRE_DEL_MODULO",
    title: "NOMBRE_DEL_MODULO",
    className: "btn btn-success btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9], // Ajustar según columnas
      format: {
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          if (column === 1 && cleanData) {
            cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
          }
          return cleanData;
        }
      }
    }
  },
  {
    extend: "pdfHtml5",
    text: '<i class="fas fa-file-pdf"></i> PDF',
    titleAttr: "Exportar a PDF",
    filename: "NOMBRE_DEL_MODULO",
    title: "NOMBRE_DEL_MODULO",
    className: "btn btn-danger btn-sm",
    orientation: "landscape",
    pageSize: "A4",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
      format: {
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          if (column === 1 && cleanData) {
            cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
          }
          return cleanData;
        }
      }
    }
  },
  {
    extend: "print",
    text: '<i class="fa fa-print"></i> Imprimir',
    titleAttr: "Imprimir",
    title: "NOMBRE_DEL_MODULO",
    className: "btn btn-info btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
      format: {
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          if (column === 1 && cleanData) {
            cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
          }
          return cleanData;
        }
      }
    }
  }
],

    columns: [
      { defaultContent: "" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            "<strong>" +
            row.tipo_documento +
            ": " +
            row.nro_documento +
            "</strong><br>" +
            row.nombre_completo
          );
        },
      },
      { data: "fecha_reserva_formateada" },
      { data: "fecha_viaje_formateada" },

      { data: "nombre_origen" },
      { data: "nombre_destino" },

      { data: "celular" },

      // ---- PAGO ----
      {
        data: "monto_adelantado",
        render: function (data, type, row) {
          if (parseFloat(data) > 0) {
            return '<span class="badge bg-success">S/ ' + data + "</span>";
          } else {
            return '<span class="badge bg-secondary">-</span>';
          }
        },
      },

      // ---- ESTADO RESERVA ----
      {
        data: "estado",
        render: function (data, type, row) {
          if (data == "PENDIENTE") {
            return '<span class="badge bg-warning">PENDIENTE</span>';
          } else if (data == "COMPLETADO") {
            return '<span class="badge bg-success">COMPLETADO</span>';
          } else {
            return '<span class="badge bg-danger text-dark">ANULADO</span>';
          }
        },
      },
      { data: "USUARIO" },
      {
        data: null,
        defaultContent: "",
        render: function (data, type, row) {
          let botones = "";

          if (row.estado === "PENDIENTE") {
            // MOSTRAR TODOS LOS BOTONES
            botones = `
        <button class='mostrar btn btn-success btn-sm' title='Mostrar datos de reserva'>
          <i class='fa fa-eye'></i> Mostrar
        </button>
        <button class='editar btn btn-primary btn-sm' title='Editar datos de reserva'>
          <i class='fa fa-edit'></i> Editar
        </button>
        <button class='anular btn btn-danger btn-sm' title='Anular reserva'>
          <i class='fa fa-trash'></i> Anular
        </button>
      `;
          } else if (row.estado === "COMPLETADO") {
            // SOLO MOSTRAR
            botones = `
        <button class='mostrar btn btn-success btn-sm' title='Mostrar datos de reserva'>
          <i class='fa fa-eye'></i> Mostrar
        </button>
      `;
          } else if (row.estado === "ANULADO") {
            // SOLO MOTIVO ANULACIÓN
            botones = `
        <button class='ver_motivo btn btn-warning btn-sm' title='Ver motivo de anulación'>
          <i class='fa fa-info-circle'></i> Motivo Anulación
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
  tbl_reservas.on("draw.td", function () {
    var PageInfo = $("#tabla_reservas").DataTable().page.info();
    tbl_reservas
      .column(0, { page: "current" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1 + PageInfo.start;
      });
  });
}

//FILTRO POR RUTAS Y ESTADO
function listar_reservas_ruta_estado() {
  let ori = document.getElementById("select_origen_bus").value;
  let des = document.getElementById("select_destino_bus").value;
  let esta = document.getElementById("select_estado_buscar").value;

  tbl_reservas = $("#tabla_reservas").DataTable({
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
      url: "../controller/reservas/controlador_listar_reservas_filtro1.php",
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
    filename: "NOMBRE_DEL_MODULO",
    title: "NOMBRE_DEL_MODULO",
    className: "btn btn-success btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9], // Ajustar según columnas
      format: {
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          if (column === 1 && cleanData) {
            cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
          }
          return cleanData;
        }
      }
    }
  },
  {
    extend: "pdfHtml5",
    text: '<i class="fas fa-file-pdf"></i> PDF',
    titleAttr: "Exportar a PDF",
    filename: "NOMBRE_DEL_MODULO",
    title: "NOMBRE_DEL_MODULO",
    className: "btn btn-danger btn-sm",
    orientation: "landscape",
    pageSize: "A4",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
      format: {
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          if (column === 1 && cleanData) {
            cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
          }
          return cleanData;
        }
      }
    }
  },
  {
    extend: "print",
    text: '<i class="fa fa-print"></i> Imprimir',
    titleAttr: "Imprimir",
    title: "NOMBRE_DEL_MODULO",
    className: "btn btn-info btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
      format: {
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          if (column === 1 && cleanData) {
            cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
          }
          return cleanData;
        }
      }
    }
  }
],

    columns: [
      { defaultContent: "" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            "<strong>" +
            row.tipo_documento +
            ": " +
            row.nro_documento +
            "</strong><br>" +
            row.nombre_completo
          );
        },
      },
      { data: "fecha_reserva_formateada" },
      { data: "fecha_viaje_formateada" },

      { data: "nombre_origen" },
      { data: "nombre_destino" },

      { data: "celular" },

      // ---- PAGO ----
      {
        data: "monto_adelantado",
        render: function (data, type, row) {
          if (parseFloat(data) > 0) {
            return '<span class="badge bg-success">S/ ' + data + "</span>";
          } else {
            return '<span class="badge bg-secondary">-</span>';
          }
        },
      },

      // ---- ESTADO RESERVA ----
      {
        data: "estado",
        render: function (data, type, row) {
          if (data == "PENDIENTE") {
            return '<span class="badge bg-warning">PENDIENTE</span>';
          } else if (data == "COMPLETADO") {
            return '<span class="badge bg-success">COMPLETADO</span>';
          } else {
            return '<span class="badge bg-danger text-dark">ANULADO</span>';
          }
        },
      },
      { data: "USUARIO" },
      {
        data: null,
        defaultContent: "",
        render: function (data, type, row) {
          let botones = "";

          if (row.estado === "PENDIENTE") {
            // MOSTRAR TODOS LOS BOTONES
            botones = `
        <button class='mostrar btn btn-success btn-sm' title='Mostrar datos de reserva'>
          <i class='fa fa-eye'></i> Mostrar
        </button>
        <button class='editar btn btn-primary btn-sm' title='Editar datos de reserva'>
          <i class='fa fa-edit'></i> Editar
        </button>
        <button class='anular btn btn-danger btn-sm' title='Anular reserva'>
          <i class='fa fa-trash'></i> Anular
        </button>
      `;
          } else if (row.estado === "COMPLETADO") {
            // SOLO MOSTRAR
            botones = `
        <button class='mostrar btn btn-success btn-sm' title='Mostrar datos de reserva'>
          <i class='fa fa-eye'></i> Mostrar
        </button>
      `;
          } else if (row.estado === "ANULADO") {
            // SOLO MOTIVO ANULACIÓN
            botones = `
        <button class='ver_motivo btn btn-warning btn-sm' title='Ver motivo de anulación'>
          <i class='fa fa-info-circle'></i> Motivo Anulación
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
  tbl_reservas.on("draw.td", function () {
    var PageInfo = $("#tabla_reservas").DataTable().page.info();
    tbl_reservas
      .column(0, { page: "current" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1 + PageInfo.start;
      });
  });
}

//FILTRO POR FECHA Y USUARIO
function listar_reservas_fecha_usu() {
  let fedes = document.getElementById("txt_fecha_desde").value;
  let fehas = document.getElementById("txt_fecha_hasta").value;
  let usu = document.getElementById("select_usuario").value;

  tbl_reservas = $("#tabla_reservas").DataTable({
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
      url: "../controller/reservas/controlador_listar_reservas_filtro2.php",
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
    filename: "NOMBRE_DEL_MODULO",
    title: "NOMBRE_DEL_MODULO",
    className: "btn btn-success btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9], // Ajustar según columnas
      format: {
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          if (column === 1 && cleanData) {
            cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
          }
          return cleanData;
        }
      }
    }
  },
  {
    extend: "pdfHtml5",
    text: '<i class="fas fa-file-pdf"></i> PDF',
    titleAttr: "Exportar a PDF",
    filename: "NOMBRE_DEL_MODULO",
    title: "NOMBRE_DEL_MODULO",
    className: "btn btn-danger btn-sm",
    orientation: "landscape",
    pageSize: "A4",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
      format: {
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          if (column === 1 && cleanData) {
            cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
          }
          return cleanData;
        }
      }
    }
  },
  {
    extend: "print",
    text: '<i class="fa fa-print"></i> Imprimir',
    titleAttr: "Imprimir",
    title: "NOMBRE_DEL_MODULO",
    className: "btn btn-info btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
      format: {
        body: function(data, row, column, node) {
          if (column === 0) return row + 1;
          var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
          if (column === 1 && cleanData) {
            cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
          }
          return cleanData;
        }
      }
    }
  }
],

    columns: [
      { defaultContent: "" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            "<strong>" +
            row.tipo_documento +
            ": " +
            row.nro_documento +
            "</strong><br>" +
            row.nombre_completo
          );
        },
      },
      { data: "fecha_reserva_formateada" },
      { data: "fecha_viaje_formateada" },

      { data: "nombre_origen" },
      { data: "nombre_destino" },

      { data: "celular" },

      // ---- PAGO ----
      {
        data: "monto_adelantado",
        render: function (data, type, row) {
          if (parseFloat(data) > 0) {
            return '<span class="badge bg-success">S/ ' + data + "</span>";
          } else {
            return '<span class="badge bg-secondary">-</span>';
          }
        },
      },

      // ---- ESTADO RESERVA ----
      {
        data: "estado",
        render: function (data, type, row) {
          if (data == "PENDIENTE") {
            return '<span class="badge bg-warning">PENDIENTE</span>';
          } else if (data == "COMPLETADO") {
            return '<span class="badge bg-success">COMPLETADO</span>';
          } else {
            return '<span class="badge bg-danger text-dark">ANULADO</span>';
          }
        },
      },
      { data: "USUARIO" },
      {
        data: null,
        defaultContent: "",
        render: function (data, type, row) {
          let botones = "";

          if (row.estado === "PENDIENTE") {
            // MOSTRAR TODOS LOS BOTONES
            botones = `
        <button class='mostrar btn btn-success btn-sm' title='Mostrar datos de reserva'>
          <i class='fa fa-eye'></i> Mostrar
        </button>
        <button class='editar btn btn-primary btn-sm' title='Editar datos de reserva'>
          <i class='fa fa-edit'></i> Editar
        </button>
        <button class='anular btn btn-danger btn-sm' title='Anular reserva'>
          <i class='fa fa-trash'></i> Anular
        </button>
      `;
          } else if (row.estado === "COMPLETADO") {
            // SOLO MOSTRAR
            botones = `
        <button class='mostrar btn btn-success btn-sm' title='Mostrar datos de reserva'>
          <i class='fa fa-eye'></i> Mostrar
        </button>
      `;
          } else if (row.estado === "ANULADO") {
            // SOLO MOTIVO ANULACIÓN
            botones = `
        <button class='ver_motivo btn btn-warning btn-sm' title='Ver motivo de anulación'>
          <i class='fa fa-info-circle'></i> Motivo Anulación
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
  tbl_reservas.on("draw.td", function () {
    var PageInfo = $("#tabla_reservas").DataTable().page.info();
    tbl_reservas
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
$("#tabla_reservas").on("click", ".motivo_anulacion", function () {
  var data = tbl_reservas.row($(this).parents("tr")).data();

  if (tbl_reservas.row(this).child.isShown()) {
    var data = tbl_reservas.row(this).data();
  }
  $("#modal_motivo_anula").modal("show");
  document.getElementById("select_estado_editar3").value =
    data.estado_encomienda;
  document.getElementById("txt_anula_enco2").value = data.motivo_anulacion;
});

// ABRIR MODAL MOSTRAR
$("#tabla_reservas").on("click", ".editar", function () {
  var data = tbl_reservas.row($(this).parents("tr")).data();
  if (tbl_reservas.row(this).child.isShown()) {
    data = tbl_reservas.row(this).data();
  }

  $("#modal_editar").modal("show");

  // Esperar a que el modal esté completamente visible
  $("#modal_editar")
    .off("shown.bs.modal")
    .on("shown.bs.modal", function () {
      // REFERENCIAS A LAS SECCIONES
      const dniSection = document.getElementById("dni_section_editar");
      const otrosSection = document.getElementById(
        "otros_documentos_section_editar"
      );
      const selectTipoDocumento = document.getElementById(
        "select_tipo_documento_emisor_editar"
      );

      // CARGAR CAMPOS COMUNES
      document.getElementById("txt_idreserva").value = data.id_reserva;
      selectTipoDocumento.value = data.tipo_documento;
      document.getElementById("txt_nomb_emisor_editar").value =
        data.nombre_completo;
      document.getElementById("txt_celu1_emisor_editar").value = data.celular;
      document.getElementById("txt_fecha_rerserva_editar").value =
        data.fecha_reserva;
      document.getElementById("txt_fecha_viaje_editar").value =
        data.fecha_viaje;
      $("#select_origen_editar").val(data.id_origen).trigger("change");
      $("#select_destino_editar").val(data.iddestino).trigger("change");

      document.getElementById("txt_monto_adelantado_editar").value =
        data.monto_adelantado;
      document.getElementById("txt_observacion_editar").value =
        data.observaciones;

      // LIMPIAR CAMPOS DE DOCUMENTO
      document.getElementById("txt_dni_emisor_editar").value = "";
      document.getElementById("txt_dni_emisor2_editar").value = "";

      // FUNCIÓN PARA MOSTRAR/OCULTAR SECCIONES SEGÚN TIPO
      function actualizarSecciones(tipo) {
        tipo = (tipo || "").toString().trim().toUpperCase();

        if (tipo === "DNI") {
          // MOSTRAR sección DNI (con botones Buscar y RENIEC)
          dniSection.style.display = "block";
          otrosSection.style.display = "none";
          // Cargar número de documento en campo DNI
          document.getElementById("txt_dni_emisor_editar").value =
            data.nro_documento;
        } else if (
          tipo === "PASAPORTE" ||
          tipo === "CARNET DE EXTRANJERIA" ||
          tipo === "CARNET DE EXTRANJERÍA"
        ) {
          // MOSTRAR sección otros documentos (solo botón Buscar verde)
          dniSection.style.display = "none";
          otrosSection.style.display = "block";
          // Cargar número de documento en campo otros
          document.getElementById("txt_dni_emisor2_editar").value =
            data.nro_documento;
        } else {
          // OCULTAR TODO si no hay tipo válido
          dniSection.style.display = "none";
          otrosSection.style.display = "none";
        }
      }

      // APLICAR AL CARGAR EL MODAL
      actualizarSecciones(data.tipo_documento);

      // ESCUCHAR CAMBIOS EN EL SELECT
      $(selectTipoDocumento)
        .off("change")
        .on("change", function () {
          // Limpiar campos al cambiar tipo
          document.getElementById("txt_dni_emisor_editar").value = "";
          document.getElementById("txt_dni_emisor2_editar").value = "";
          actualizarSecciones(this.value);
        });

      console.log(
        "TIPO DOC:",
        data.tipo_documento,
        "| NRO:",
        data.nro_documento
      );
    });
});

//ABRIR MODAL EDITAR
$("#tabla_reservas").on("click", ".mostrar", function () {
  var data = tbl_reservas.row($(this).parents("tr")).data();
  if (tbl_reservas.row(this).child.isShown()) {
    var data = tbl_reservas.row(this).data();
  }
  $("#modal_mostrar").modal("show");

  // CAMPOS EXISTENTES

  document.getElementById("select_tipo_documento_emisor_mostrar").value =
    data.tipo_documento;
  document.getElementById("txt_nrodoc_mostrar").value = data.nro_documento;
  document.getElementById("txt_nomb_emisor_mostrar").value =
    data.nombre_completo;
  document.getElementById("txt_celu1_emisor_mostrar").value = data.celular;
  document.getElementById("txt_fecha_rerserva_mostrar").value =
    data.fecha_reserva;
  document.getElementById("txt_fecha_viaje_mostrar").value = data.fecha_viaje;
  document.getElementById("select_origen_mostrar").value = data.nombre_origen;
  document.getElementById("select_destino_mostrar").value = data.nombre_destino;
  document.getElementById("txt_monto_adelantado_mostrar").value =
    data.monto_adelantado;
  document.getElementById("txt_observacion_mostrar").value = data.observaciones;
});
//LIMPIAR CAMPOS
function LimpiarCamposEncomienda() {
  // CAMPOS PRINCIPALES
  document.getElementById("txt_dni_emisor").value = "";
  document.getElementById("txt_dni_emisor2").value = "";
  document.getElementById("txt_nomb_emisor").value = "";
  document.getElementById("txt_celu1_emisor").value = ""; // CORREGIDO: era txtxt_descripciont_fecha_creacion

  // DATOS DEL EMISOR
  document.getElementById("txt_fecha_viaje").value = "";
  document.getElementById("select_origen").value = "";
  document.getElementById("select_destino").value = "";
  document.getElementById("txt_monto_adelantado").value = "";
  document.getElementById("txt_observacion").value = "";
}
//REGISTROS DE ENCOMIENDAS
function Registrar_Reservas() {
  // DATOS DEL PASAJERO
  let tipodocemi = document.getElementById(
    "select_tipo_documento_emisor"
  ).value;
  let dniemi = document.getElementById("txt_dni_emisor").value;
  let dni2emi = document.getElementById("txt_dni_emisor2").value;
  let nomemi = document.getElementById("txt_nomb_emisor").value;
  let celemi = document.getElementById("txt_celu1_emisor").value;

  // DATOS DE LA RESERVA
  let fechare = document.getElementById("txt_fecha_rerserva").value;
  let fechavia = document.getElementById("txt_fecha_viaje").value;
  let ori = document.getElementById("select_origen").value;
  let des = document.getElementById("select_destino").value;
  let monto = document.getElementById("txt_monto_adelantado").value;
  let obser = document.getElementById("txt_observacion").value;
  let idusu = document.getElementById("txtprincipalid").value;

  // Obtener el nombre del destino (opcional)
  let selectDestino = document.getElementById("select_destino");
  let nombre_destino = selectDestino.options[selectDestino.selectedIndex].text;

  // 🔹 Validar campos vacíos
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
    url: "../controller/reservas/controlador_registro_reservas.php",
    type: "POST",
    data: {
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
          "Nueva reserva registrada para el pasajero: <b>" + nomemi + "</b>",
          "success"
        ).then(() => {
          tbl_reservas.ajax.reload();
          LimpiarCamposEncomienda();
          $("#modal_registro").modal("hide");
        });
      } else {
        Swal.fire(
          "Mensaje de Advertencia",
          "La reserva que intentas registrar ya se encuentra en la base de datos, revise por favor",
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
          tbl_reservas.ajax.reload();
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

//ANULAR ENCOMIENDA CON MOTIVO
function Anular_reserva(id, motivo) {
  $.ajax({
    url: "../controller/reservas/controlador_anular_encomiendas.php",
    type: "POST",
    data: {
      id: id,
      motivo: motivo,
    },
  }).done(function (resp) {
    if (resp > 0) {
      Swal.fire(
        "Mensaje de Confirmación",
        "Se anuló la resergva con éxito",
        "success"
      ).then((value) => {
        tbl_reservas.ajax.reload();
      });
    } else {
      return Swal.fire(
        "Mensaje de Advertencia",
        "No se puede anular la resergva, verifique por favor",
        "warning"
      );
    }
  });
}

//ENVIANDO AL BOTON ANULAR
$("#tabla_reservas").on("click", ".anular", function () {
  var data = tbl_reservas.row($(this).parents("tr")).data();

  if (tbl_reservas.row(this).child.isShown()) {
    var data = tbl_reservas.row(this).data();
  }

  Swal.fire({
    title: "¿Desea anular la encomienda?",
    html: `
      <p><strong>Fecha de reserva:</strong> ${data.fecha_reserva_formateada}</p>
      <p><strong>Cliente:</strong> ${data.nombre_completo}</p>
      <br>
      <label for="motivo_anulacion" style="float:left; font-weight:bold; color:red;">
        Motivo de Anulación (*):</label>
      <textarea 
        id="motivo_anulacion" 
        class="swal2-input" 
        placeholder="Ingrese el motivo de anulación" 
        style="width:100%; height:100px; resize:none;"
        maxlength="500"
      ></textarea>
    `,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, Anular",
    cancelButtonText: "Cancelar",
    preConfirm: () => {
      const motivo = document.getElementById("motivo_anulacion").value.trim();

      if (!motivo) {
        Swal.showValidationMessage("Debe ingresar el motivo de anulación");
        return false;
      }

      if (motivo.length < 10) {
        Swal.showValidationMessage(
          "El motivo debe tener al menos 10 caracteres"
        );
        return false;
      }

      return motivo;
    },
  }).then((result) => {
    if (result.isConfirmed) {
      Anular_reserva(data.id_reserva, result.value);
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

//MOTIVO ANULACION
$("#tabla_reservas").on("click", ".ver_motivo", function () {
  var data = tbl_reservas.row($(this).parents("tr")).data();
  if (tbl_reservas.row(this).child.isShown()) {
    var data = tbl_reservas.row(this).data();
  }
  $("#modal_motivo_anula").modal("show");

  // CAMPOS EXISTENTES

  document.getElementById("txt_fecha_anula").value = data.fecha_anulado;
  document.getElementById("txt_anula_enco2").value = data.motivo_anula;
});
