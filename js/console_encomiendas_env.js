//LISTAR ENCOMIENDAS
var tbl_encomiendas;
function listar_todas_los_encomiendas_env() {
  Cargar_Select_Usuarios();
  Cargar_Select_Rutas();
  let usu = document.getElementById("txtprincipalid").value;
  let des = document.getElementById("txt_sucursal").value;

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
    pageLength: 5,
    destroy: true,
    pagingType: "full_numbers",
    scrollCollapse: true,
    responsive: true,
    async: false,
    processing: true,
    ajax: {
      url: "../controller/encomiendas/controlador_listar_todas_encomiendas_env.php",
      type: "POST",
       data: {
        usu: usu,
        des: des
      },
    },
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

}
//FILTRO POR RUTAS Y ESTADO
function listar_encomiendas_por_dia_env() {
  let usu = document.getElementById("txtprincipalid").value;
  let des = document.getElementById("txt_sucursal").value;

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
    pageLength: 5,
    destroy: true,
    pagingType: "full_numbers",
    scrollCollapse: true,
    responsive: true,
    async: false,
    processing: true,
    ajax: {
      url: "../controller/encomiendas/controlador_listar_encomiendas_por_dia_env.php",
      type: "POST",
      data: {
        usu: usu,
        des: des
      },
    },
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

}

//FILTRO POR FECHA Y USUARIO
function listar_encomiendas_fecha_estado_env() {
  let usu = document.getElementById("txtprincipalid").value;
  let des = document.getElementById("txt_sucursal").value;
  let fedes = document.getElementById("txt_fecha_desde").value;
  let fehas = document.getElementById("txt_fecha_hasta").value;
  let esta = document.getElementById("select_estado_buscar").value;

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
    pageLength: 5,
    destroy: true,
    pagingType: "full_numbers",
    scrollCollapse: true,
    responsive: true,
    async: false,
    processing: true,
    ajax: {
      url: "../controller/encomiendas/controlador_listar_encomiendas_fecha_estado_env.php",
      type: "POST",
      data: {
        usu: usu,
        des: des,
        fedes: fedes,
        fehas: fehas,
        esta: esta,
      },
    },
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

}

//ABRIR MODAL REGISTRO
function AbrirRegistro() {
  $("#modal_registro").modal({ backdrop: "static", keyboard: false });
  $("#modal_registro").modal("show");
  
  // Validar que el elemento existe antes de obtener su valor
  let sucursalElement = document.getElementById("txt_sucursal");
  if (!sucursalElement) {
    console.error("Elemento 'txt_sucursal' no encontrado");
    return;
  }
  
  let des = sucursalElement.value;
  
  // Validar que los elementos destino existen
  let origenElement = document.getElementById("select_origen");
  let destinoElement = document.getElementById("select_destino");
  
  if (!origenElement) {
    console.error("Elemento 'txt_origen' no encontrado");
    return;
  }
  
  if (!destinoElement) {
    console.error("Elemento 'txt_destino' no encontrado");
    return;
  }
  
  // Asignar valores
  if (des == "ABANCAY") {
    origenElement.value = "1";
    destinoElement.value = "2";
  } else {
    origenElement.value = "2";
    destinoElement.value = "1";
  }
  
  console.log("Sucursal:", des, "Origen:", origenElement.value, "Destino:", destinoElement.value);
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
//ABRIR MODAL EDITAR ESTADO
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

  document.getElementById("select_estado_editar4").value =
    data.estado_encomienda;

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
        return Swal.fire("Mensaje de Error", "No se completó el registro", "error");
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Error AJAX:", textStatus, errorThrown);
      Swal.fire("Mensaje de Error", "Error de conexión: " + textStatus, "error");
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
        "No se puede eliminar la encomienda, verifique por favor",
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
              tbl_encomiendas.ajax.reload();
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
              tbl_encomiendas.ajax.reload();
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
        return json.data;
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
  document.getElementById("select_estado_pago").value = data.estado_encomienda;
  
  // ✅ GUARDAR EL ESTADO INICIAL PARA COMPARAR DESPUÉS
  document.getElementById("select_estado_pago").setAttribute("data-estado-inicial", data.estado_encomienda);
  
  document.getElementById("txt_saldo_pendiente").value = data.por_pagar;
});

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

  // ✅ VALIDAR QUE EL NUEVO ESTADO SEA DIFERENTE AL INICIAL
  if (nuevo_estado === estado_inicial) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Debe seleccionar un estado diferente al actual",
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

// BOLETA DE PAGO

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
          "No se completó el registro",
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