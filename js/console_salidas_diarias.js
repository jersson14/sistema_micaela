var tbl_salidas_diarias;
function listar_salidas_diarias_dia() {


  tbl_salidas_diarias = $("#tabla_salida_diaria").DataTable({
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
      url: "../controller/salidas_diarias/controlador_listar_salidas_diarias_pordia.php",
      type: "POST",
    },
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
               <button class='editar btn btn-primary btn-sm' title='Editar datos de servicio'>
                <i class='fa fa-edit'></i> Editar
              </button>
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
    ],
    language: idioma_espanol,
    select: true,
  });
}
function listar_salidas_diarias() {
  Cargar_Select_Usuarios();
  Cargar_Select_Rutas();
  document.getElementById("txt_fecha_desde").value = "";
  document.getElementById("txt_fecha_hasta").value = "";
  document.getElementById("select_estado_buscar").value = "";

  tbl_salidas_diarias = $("#tabla_salida_diaria").DataTable({
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
      url: "../controller/salidas_diarias/controlador_listar_salidas_diarias.php",
      type: "POST",
    },
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
               <button class='editar btn btn-primary btn-sm' title='Editar datos de servicio'>
                <i class='fa fa-edit'></i> Editar
              </button>
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
    ],
    language: idioma_espanol,
    select: true,
  });
}

function listar_salidas_diarias_ruta_estado() {
  let ori = document.getElementById("select_origen_bus").value;
  let des = document.getElementById("select_destino_bus").value;
  let esta = document.getElementById("select_estado_buscar").value;

  tbl_salidas_diarias = $("#tabla_salida_diaria").DataTable({
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
      url: "../controller/salidas_diarias/controlador_listar_salidas_diarias_ruta_estado.php",
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
      { data: "usuario_nombre_completo" },

      // ---- BOTONES DE ACCIÓN ----
      {
        data: "estado",
        render: function (data, type, row) {
          let botones = "";

          // EN TRANSITO / INCOMPLETO => Editar + Eliminar
          if (data === "EN TRANSITO" || data === "INCOMPLETO") {
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
              <button class='editar btn btn-primary btn-sm' title='Editar datos de servicio'>
                <i class='fa fa-edit'></i> Editar
              </button>
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
    ],
    language: idioma_espanol,
    select: true,
  });
}

function listar_salidas_diarias_fecha_usu() {
  let fedes = document.getElementById("txt_fecha_desde").value;
  let fehas = document.getElementById("txt_fecha_hasta").value;
  let usu = document.getElementById("select_usuario").value;

  tbl_salidas_diarias = $("#tabla_salida_diaria").DataTable({
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
      url: "../controller/salidas_diarias/controlador_listar_salidas_diarias_fecha_usu.php",
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
              <button class='editar btn btn-primary btn-sm' title='Editar datos de servicio'>
                <i class='fa fa-edit'></i> Editar
              </button>
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
    ],
    language: idioma_espanol,
    select: true,
  });
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
    
    // Establecer fecha y hora actual
    const now = new Date();
    const fechaActual = now.toISOString().slice(0, 16);
    $("#txt_fecha_creacion").val(fechaActual);
    console.log("Fecha establecida:", fechaActual);
    
  }, 300); // Esperar 300ms para que los datos se carguen
  
  // 3. TERCERO: Abrir el modal
  $("#modal_registro").modal({ backdrop: "static", keyboard: false });
  $("#modal_registro").modal("show");
}
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

// Una sola vez cuando se abre el modal
$("#modal_registro").on("shown.bs.modal", function () {
  $("#select_conductor").select2({
    placeholder: "Seleccionar Conductor",
    allowClear: true,
    dropdownParent: $("#modal_registro"),
  });
});

//REALIZAR BUSQUEDA CLIENTE

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
        "No se completo la actualización",
        "error"
      );
    }
  });
}

$("#tabla_salida_diaria").on("click", ".incompleto", function () {
  var data = tbl_salidas_diarias.row($(this).parents("tr")).data();

  if (tbl_salidas_diarias.row(this).child.isShown()) {
    var data = tbl_salidas_diarias.row(this).data();
  }

  Swal.fire({
    title:
      "Desea marcar como INCOMPLETO el viaje del conductor " +
      data.nombres_apellidos +
      "?",
    text: "Una vez se ponga INCOMPLETO el viaje, no se podrá editar ni eliminar la salida diaria. Además, las encomiendas se pondrán en estado INCOMPLETO porque no llegaron al destino final.",
    icon: "warning",
    input: "textarea", // campo para observación
    inputPlaceholder: "Escriba la razón del estado INCOMPLETO...",
    inputValidator: (value) => {
      if (!value) {
        return "Debe ingresar una observación para continuar";
      }
    },
    showCancelButton: true,
    confirmButtonColor: "#005CA5",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, cambiar!",
    cancelButtonText: "Cancelar",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      Modificar_Estatus_Salida_Incompleta_Diaria(
        data.id_salidas_diarias,
        result.value
      );
    }
  });
});

function Modificar_Estatus_Salida_Incompleta_Diaria(id, observacion) {
  let idusu = document.getElementById("txtprincipalid").value;

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
      idusu: idusu,
      observacion: observacion,
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
      Swal.fire("Mensaje de Error", "No se completó la actualización", "error");
    }
  });
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
//REGISTROS DE ENCOMIENDAS

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
            tbl_salidas_diarias.ajax.reload();
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
        "No se puede eliminar la salida diaria, verifique por favor",
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

  // Validación para otros estados que requieren observación
  if (nuevo_estado != "ANULADO" && observacion.length == 0) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "La observación es obligatoria, revise por favor",
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
              tbl_salidas_diarias.ajax.reload();
              $("#modal_estado").modal("hide"); // Asegúrate que el ID del modal sea correcto
            });
          } else {
            return Swal.fire(
              "Mensaje de Error",
              "No se completó la actualización",
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
              tbl_salidas_diarias.ajax.reload();
              $("#modal_ajustar_precio").modal("hide"); // Asegúrate que el ID del modal sea correcto
            });
          } else {
            return Swal.fire(
              "Mensaje de Error",
              "No se completó la actualización",
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
        return json.data;
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

//PAGAR ENCOMIENDA
$("#tabla_salida_diaria").on("click", ".pagar", function () {
  var data = tbl_salidas_diarias.row($(this).parents("tr")).data();

  if (tbl_salidas_diarias.row(this).child.isShown()) {
    var data = tbl_salidas_diarias.row(this).data();
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
  document.getElementById("select_estado_pago").value = data.estado_encomienda;
  document.getElementById("txt_saldo_pendiente").value = data.por_pagar;
});

function Realizar_pago() {
  let id = document.getElementById("id_encomienda_pago").value;
  let nuevo_estado = document.getElementById("select_estado_pago").value;
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
          let mensajeExito = `Pago procesado correctamente!\n\nVuelto entregado: S/ ${vueltoCalculado.toFixed(
            2
          )}`;

          Swal.fire("Pago Completado", mensajeExito, "success").then(
            (value) => {
              tbl_salidas_diarias.ajax.reload();
              $("#modal_pagar").modal("hide");
            }
          );
        } else {
          return Swal.fire(
            "Mensaje de Error",
            "No se completó el procesamiento del pago",
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

//LISTAR ENCOMIENDAS

// VERSIÓN CON DEPURACIÓN COMPLETA
console.log("Script de encomiendas cargado");

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

// Obtener todos los IDs de encomiendas seleccionadas
function obtenerEncomiendasSeleccionadas() {
  let idsSeleccionados = [];

  $(".check_encomienda:checked").each(function () {
    let id = $(this).val(); // o $(this).data('id')
    idsSeleccionados.push(id);
  });

  return idsSeleccionados;
}

// Ejemplo de uso
// let encomiendas = obtenerEncomiendasSeleccionadas();
// console.log('IDs seleccionados:', encomiendas); // [1, 3, 5, 7]

//REGISTRAR SALIDA DIARIA

function Registrar_Salida_Diaria() {
  const conductor = document.getElementById("select_conductor").value;
  const monto = document.getElementById("txt_pago").value;
  const fechaHora = document.getElementById("txt_fecha_creacion").value;
  const origen = document.getElementById("select_origen").value;
  const destino = document.getElementById("select_destino").value;
  const observacion = document.getElementById("txt_descripcion").value;
  const idUsuario = document.getElementById("txtprincipalid").value;
  const totalPasajeros = document.querySelectorAll(
    "#tabla_pasajeros tbody tr"
  ).length;
  const totalEncomiendas = document.querySelectorAll(
    "#tabla_encomiendas tbody input[type='checkbox']:checked"
  ).length;

  if (!conductor || !monto || !fechaHora || !origen || !destino) {
    return Swal.fire(
      "Advertencia",
      "Complete todos los campos obligatorios.",
      "warning"
    );
  }

  $.ajax({
    url: "../controller/salidas_diarias/controlador_registrar_salida_diaria.php",
    type: "POST",
    data: {
      conductor,
      monto,
      fechaHora,
      origen,
      destino,
      observacion,
      idUsuario,
      totalPasajeros,
      totalEncomiendas,
    },
    success: function (resp) {
      if (resp > 0) {
        Registrar_Detalle_Pasajeros(resp);
        Registrar_Detalle_Encomiendas(resp);

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
            totalPasajeros +
            "</b><br>Encomiendas: <b>" +
            totalEncomiendas +
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
      } else {
        Swal.fire("Error", "No se pudo registrar la salida diaria.", "error");
      }
    },
    error: function () {
      Swal.fire("Error", "Error en la comunicación con el servidor.", "error");
    },
  });
}
//AQUI PRIMERO SE GUARDA LA SALIDAS Y LUEGO EL INGRESO DE MONTO

//REGISTRAR DETALLE PASAJEROS Y ENCOMIENDAS
function Registrar_Detalle_Pasajeros(idSalida) {
  const pasajeros = [];
  document.querySelectorAll("#tabla_pasajeros tbody tr").forEach((row) => {
    const celdas = row.cells;
    pasajeros.push({
      tipo_documento: celdas[1].innerText,
      documento: celdas[2].innerText,
      nombres: celdas[3].innerText,
      edad: celdas[4].innerText,
      celular: celdas[5].innerText,
    });
  });

  if (pasajeros.length === 0) return;

  $.ajax({
    url: "../controller/salidas_diarias/controlador_registrar_detalle_pasajeros.php",
    type: "POST",
    data: { idSalida, pasajeros: JSON.stringify(pasajeros) },
    success: function (resp) {
      if (resp <= 0) {
        Swal.fire(
          "Advertencia",
          "No se pudieron registrar algunos pasajeros.",
          "warning"
        );
      }
    },
    error: function () {
      Swal.fire("Error", "Error al registrar los pasajeros.", "error");
    },
  });
}
//aqui se guarda primero pasajeros o actualiza y luego recien se isnertar con el ID SALIDA

// FUNCIÓN CORREGIDA - Usa esta versión mejorada
function Registrar_Detalle_Encomiendas(idSalida) {
  console.log("=== Registrando detalle de encomiendas ===");
  console.log("idSalida:", idSalida);

  const encomiendas = [];

  // MÉTODO 1: Usar jQuery (más confiable)
  $(".check_encomienda:checked").each(function () {
    const idEncomienda = $(this).data("id") || $(this).val();
    console.log("Procesando encomienda:", idEncomienda);
    if (
      idEncomienda &&
      idEncomienda !== "undefined" &&
      idEncomienda !== "null"
    ) {
      encomiendas.push(idEncomienda);
    }
  });

  // MÉTODO 2: Si no funciona jQuery, intentar con vanilla JS
  if (encomiendas.length === 0) {
    console.log(
      "Método jQuery no encontró encomiendas, probando vanilla JS..."
    );
    document.querySelectorAll(".check_encomienda").forEach((checkbox) => {
      if (checkbox.checked) {
        const idEncomienda = checkbox.dataset.id || checkbox.value;
        console.log("Vanilla JS - Procesando encomienda:", idEncomienda);
        if (
          idEncomienda &&
          idEncomienda !== "undefined" &&
          idEncomienda !== "null"
        ) {
          encomiendas.push(idEncomienda);
        }
      }
    });
  }

  console.log("Encomiendas seleccionadas:", encomiendas);

  if (encomiendas.length === 0) {
    console.log("No hay encomiendas seleccionadas");
    // NO MOSTRAR ALERTA AQUÍ - es normal que no siempre haya encomiendas
    return;
  }

  console.log("Enviando datos:", {
    idSalida: idSalida,
    encomiendas: JSON.stringify(encomiendas),
  });

  $.ajax({
    url: "../controller/salidas_diarias/controlador_registrar_detalle_encomiendas.php",
    type: "POST",
    data: {
      idSalida: idSalida,
      encomiendas: JSON.stringify(encomiendas),
    },
    beforeSend: function () {
      console.log("Enviando petición para registrar encomiendas...");
    },
    success: function (resp) {
      console.log("Respuesta del servidor:", resp);
      // ELIMINAR ESTE SWAL - Ya se mostró el mensaje principal
      // if (resp == 1) {
      //   Swal.fire("Éxito", "Encomiendas registradas correctamente.", "success");
      // }
      if (resp != 1) {
        console.error("Error al registrar encomiendas:", resp);
        // Solo mostrar error si falla
      }
    },
    error: function (xhr, status, error) {
      console.error("Error AJAX:", { xhr, status, error });
      // Solo mantener log del error, no mostrar Swal aquí
    },
  });
}
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
        return json.data;
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
        return json.data;
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

// Función adicional para mostrar salida completa (si la necesitas)
function mostrar_salida_completa(id) {
  $("#modal_mostrar").modal("show");

  setTimeout(function () {
    listar_pasajeros(id);
    listar_encomiendas(id);
  }, 300);
}

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

// Función optimizada para obtener encomiendas seleccionadas
function obtenerEncomiendasSeleccionadasEditar() {
  // Usar map en lugar de each para mejor performance
  let seleccionadas = $(".encomienda-checkbox:checked").map(function() {
    return $(this).val();
  }).get();
  
  return seleccionadas;
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
  var fila = "<tr>";
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
function Moidificar_Salida_Diaria() {
  console.log("🚀 === INICIANDO Moidificar_Salida_Diaria ===");

  const idSalida = document.getElementById("id_salida_editar").value;
  const monto = document.getElementById("txt_pago_editar").value;
  const fechaactualizar = document.getElementById(
    "txt_fecha_actualizacion"
  ).value;
  const observacion = document.getElementById("txt_descripcion_editar").value;
  const idUsuario = document.getElementById("txtprincipalid").value;
  const totalPasajeros = document.querySelectorAll(
    "#tabla_pasajeros_editar tbody tr"
  ).length;
  const totalEncomiendas = document.querySelectorAll(
    "#tabla_encomiendas_editar tbody tr"
  ).length;

  console.log("📊 Datos capturados:", {
    idSalida,
    monto,
    fechaactualizar,
    observacion,
    idUsuario,
    totalPasajeros,
    totalEncomiendas,
  });

  if (!monto || !fechaactualizar) {
    console.log("❌ Validación fallida: campos obligatorios vacíos");
    return Swal.fire(
      "Advertencia",
      "Complete todos los campos obligatorios.",
      "warning"
    );
  }

  console.log("🌐 Enviando AJAX para modificar salida principal...");

  $.ajax({
    url: "../controller/salidas_diarias/controlador_modificar_salida_diaria.php",
    type: "POST",
    data: {
      idSalida,
      monto,
      fechaactualizar,
      observacion,
      idUsuario,
      totalPasajeros,
      totalEncomiendas,
    },
    success: function (resp) {
      console.log("✅ SUCCESS - Respuesta salida principal:", resp);
      console.log("🔢 Tipo de respuesta:", typeof resp);
      console.log("📏 Longitud respuesta:", resp.length);

      if (resp > 0) {
        console.log(
          "🎯 Respuesta válida, procediendo con funciones secundarias..."
        );

        // ASEGÚRATE QUE ESTAS DOS LÍNEAS NO ESTÉN COMENTADAS
        console.log("👥 Llamando Modificar_Detalle_Pasajeros...");
        Modificar_Detalle_Pasajeros(resp);

        console.log("📦 Llamando Modificar_Detalle_Encomiendas...");
        Modificar_Detalle_Encomiendas(resp);

        Swal.fire(
          "Éxito",
          "Salida diaria modificada correctamente.",
          "success"
        );
        $("#modal_editar").modal("hide");
        listar_salidas_diarias_dia();
      } else {
        console.log("❌ Respuesta no válida:", resp);
        Swal.fire(
          "Error",
          "No se pudo registrar la salida diaria: " + resp,
          "error"
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("💥 ERROR AJAX:", { xhr, status, error });
      console.error("📄 Response Text:", xhr.responseText);
      Swal.fire("Error", "Error en la comunicación con el servidor.", "error");
    },
  });
}

//MODIFICAR DETALLE PASAJEROS
function Modificar_Detalle_Pasajeros(idSalida) {
  console.log("👥 === INICIANDO Modificar_Detalle_Pasajeros ===");
  console.log("🆔 idSalida recibido:", idSalida);

  const pasajeros = [];

  document
    .querySelectorAll("#tabla_pasajeros_editar tbody tr")
    .forEach((row, index) => {
      console.log(`📋 Procesando fila ${index + 1}:`, row);
      const celdas = row.cells;

      if (celdas.length >= 6) {
        const pasajero = {
          tipo_documento: (celdas[1].textContent || celdas[1].innerText || "")
            .replace(/\s+/g, " ")
            .trim(),
          documento: (celdas[2].textContent || celdas[2].innerText || "")
            .replace(/\s+/g, " ")
            .trim(),
          nombres: (celdas[3].textContent || celdas[3].innerText || "")
            .replace(/\s+/g, " ")
            .trim(),
          edad: (celdas[4].textContent || celdas[4].innerText || "")
            .replace(/\s+/g, " ")
            .trim(),
          celular: (celdas[5].textContent || celdas[5].innerText || "")
            .replace(/\s+/g, " ")
            .trim(),
        };

        if (pasajero.documento && pasajero.nombres) {
          pasajeros.push(pasajero);
          console.log(`✅ Pasajero ${index + 1} agregado:`, pasajero);
        } else {
          console.log(
            `❌ Pasajero ${index + 1} rechazado (datos incompletos):`,
            pasajero
          );
        }
      }
    });

  console.log("📊 Total pasajeros procesados:", pasajeros.length);

  if (pasajeros.length === 0) {
    console.log("⚠️ No hay pasajeros válidos para procesar");
    return;
  }

  console.log("🌐 Enviando AJAX para modificar pasajeros...");

  $.ajax({
    url: "../controller/salidas_diarias/controlador_modificar_detalle_pasajeros.php",
    type: "POST",
    data: {
      idSalida: idSalida,
      pasajeros: JSON.stringify(pasajeros),
    },
    success: function (resp) {
      console.log("✅ SUCCESS Pasajeros - Respuesta:", resp);
      if (resp <= 0) {
        Swal.fire(
          "Advertencia",
          "No se pudieron modificar algunos pasajeros.",
          "warning"
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("💥 ERROR AJAX Pasajeros:", { xhr, status, error });
      console.error("📄 Response Text:", xhr.responseText);
      Swal.fire("Error", "Error al modificar los pasajeros.", "error");
    },
  });
}

// FUNCIÓN PARA MODIFICAR DETALLE DE ENCOMIENDAS
// 🔍 FUNCIÓN TEMPORAL PARA DETECTAR CHECKBOXES
function Debug_Detectar_Checkboxes() {
  console.log("🔍 === ANALIZANDO TODOS LOS CHECKBOXES ===");

  const todosCheckboxes = $('input[type="checkbox"]');
  console.log("📊 Total checkboxes:", todosCheckboxes.length);

  todosCheckboxes.each(function (index) {
    const checkbox = $(this);
    const padre = checkbox.closest("tr");

    console.log(`📦 Checkbox ${index + 1}:`, {
      html: this.outerHTML,
      id: checkbox.attr("id"),
      name: checkbox.attr("name"),
      class: checkbox.attr("class"),
      value: checkbox.val(),
      dataId: checkbox.data("id"),
      checked: checkbox.is(":checked"),
      enTablaEncomiendas: padre.closest("#tabla_encomiendas_editar").length > 0,
    });
  });

  // Específicamente buscar en tabla de encomiendas
  const tablaEncomiendas = $("#tabla_encomiendas_editar tbody tr");
  console.log(
    "\n🔍 Filas en tabla_encomiendas_editar:",
    tablaEncomiendas.length
  );

  tablaEncomiendas.each(function (index) {
    const fila = $(this);
    const checkbox = fila.find('input[type="checkbox"]');
    console.log(`📋 Fila ${index + 1}:`, {
      checkbox: checkbox.length > 0 ? checkbox[0].outerHTML : "NO ENCONTRADO",
      checked: checkbox.is(":checked"),
      value: checkbox.val(),
      dataId: checkbox.data("id"),
    });
  });
}

// 📦 FUNCIÓN CORREGIDA PARA MODIFICAR ENCOMIENDAS
function Modificar_Detalle_Encomiendas(idSalida) {
  console.log("📦 === INICIANDO Modificar_Detalle_Encomiendas ===");
  console.log("🆔 idSalida recibido:", idSalida);

  // Arreglo para IDs a remover
  const encomiendas = [];

  // Buscar SOLO los checkboxes de encomiendas
  $("#tabla_encomiendas_editar tbody input.encomienda-checkbox").each(
    function () {
      const checkbox = $(this);
      const idEncomienda = checkbox.val();
      const isChecked = checkbox.is(":checked");

      // ✅ Lógica: si NO está marcado, lo agregamos a la lista
      if (!isChecked && idEncomienda) {
        encomiendas.push(idEncomienda);
        console.log(`❌ Encomienda ${idEncomienda} NO marcada -> SE REMOVERÁ`);
      } else {
        console.log(`✅ Encomienda ${idEncomienda} marcada -> SE MANTIENE`);
      }
    }
  );

  console.log("🗑️ IDs de encomiendas a REMOVER:", encomiendas);

  if (encomiendas.length === 0) {
    console.log(
      "ℹ️ Todas las encomiendas siguen seleccionadas, nada que remover."
    );
    return;
  }

  // 🌐 Enviar al servidor
  $.ajax({
    url: "../controller/salidas_diarias/controlador_modificar_detalle_encomiendas.php",
    type: "POST",
    data: {
      idSalida: idSalida,
      encomiendas: JSON.stringify(encomiendas),
    },
    beforeSend: function () {
      console.log("⏳ Enviando petición con IDs:", encomiendas);
    },
    success: function (resp) {
      console.log("✅ SUCCESS - Respuesta del servidor:", resp);
      if (resp == 1 || resp === "1") {
        Swal.fire("Éxito", "Encomiendas removidas exitosamente", "success");
      } else {
        Swal.fire("Advertencia", "Respuesta: " + resp, "warning");
      }
    },
    error: function (xhr, status, error) {
      console.error("💥 ERROR AJAX:", {
        xhr,
        status,
        error,
        responseText: xhr.responseText,
      });
      Swal.fire("Error", "Error al remover encomiendas: " + error, "error");
    },
  });
}

// 🚀 EJECUTAR DEBUG (llama esta función en la consola primero)
function Ejecutar_Debug() {
  console.clear();
  console.log("🚀 Ejecutando debug de checkboxes...");
  Debug_Detectar_Checkboxes();
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