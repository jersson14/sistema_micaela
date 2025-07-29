//LISTAR TODOS
var tbl_facturas;
function listar_facturas_diario(){
  tbl_facturas = $("#tabla_facturas").DataTable({
      "ordering":false,   
      "bLengthChange":true,
      "searching": { "regex": false },
      "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
      "pageLength": 10,
      pagingType: 'full_numbers',
      scrollCollapse: true,
      responsive: true,
      "destroy":true,
      "async": false ,
      "processing": true,
      "ajax":{
          "url":"../controller/facturas/controlador_listar_facturas.php",
          type:'POST'
      },
      dom: 'Bfrtip', 
       
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE FACTURAS",
          title: "LISTA DE FACTURAS",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE FACTURAS",
          title: "LISTA DE FACTURAS",
          className: 'btn btn-pdf',
          orientation: 'landscape', // <-- Establece la orientación en horizontal
          pageSize: 'A4', // <-- Especifica el tamaño de la página
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE FACTURAS",
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      "columns":[
        {"defaultContent":""},
        {"data":"obra_social"},
        {"data":"numero_fact"},
        {
          "data": "monto",
          "render": function (data, type, row) {
            return `<strong>$AR ${data}</strong>`;
          }
        },
        {
          "data": "saldo_cobrado",
          "render": function (data, type, row) {
            return `<strong>$AR ${data}</strong>`;
          }
        },
        {
          "data": "saldo_pendiente",
          render: function(data, type, row) {
            if (data == 0) {
              return '<span class="badge bg-success fs-5">$AR ' + data + '</span>';
            } else {
              return '<span class="badge bg-danger fs-5">$AR ' + data + '</span>';
            }
          }
        },
        {"data":"archivo_fact",
          render: function(data,type,row){
                  if(data=='' || data=='controller/facturas/filefacturas/'){
                      return "<button class='btn btn-danger btn-sm' disabled title='Sin archivo'><i class='fa fa-file-pdf'></i></button>";
                  }else{
                    return "<a class='btn btn-success btn-sm' href='../"+data+"' target='_blank' title='Ver archivo'><i class='fas fa-eye'></i> Ver factura</a>";
                  }
              }   
          },    
        {"data":"nota_credito",
          render: function(data,type,row){
                  if(data=='' || data=='controller/facturas/filenotacredito/'){
                      return "<button class='btn btn-danger btn-sm' disabled title='Ver archivo'><i class='fa fa-file-pdf'></i></button>";
                  }else{
                    return "<a class='btn btn-success btn-sm' href='../"+data+"' target='_blank' title='Ver archivo'><i class='fas fa-eye'></i> Ver nota de credito</a>";
                  }
              }   
          },    
       
        

        {"data":"fecha_credito"},

        {"data":"fecha_formateada"},

        {
            "data": "estado_fact",
            render: function(data, type, row) {
                if (data == 'PENDIENTE') {
                    return '<span class="badge bg-warning">PENDIENTE</span>';
                } else if (data == 'COBRADA') {
                    return '<span class="badge bg-success">COBRADA</span>';
                } else if (data == 'FACTURADA') {
                  return '<span class="badge bg-primary">FACTURADA</span>';
              } else {
                    return '<span class="badge bg-danger">RECHAZADA</span>';
                }
            }
        },        
        {
          "data": "saldo_pendiente",
          render: function(data, type, row) {
              if (data == 0) {
                  return  `
             
                  <button class='pagar btn btn-success btn-sm' title='Realizar pago' hidden>
                      <i class='fa fa-exchange-alt'></i> Pagar
                  </button>
                  <button class='historial_pagos btn btn-dark btn-sm' title='Ver historial pagos' > 
                      <i class="fa fa-dollar-sign"></i> Historial pagos
                    </button>
                    `;
            } else {
                  return ` <button class='pagar btn btn-success btn-sm' title='Realizar pago'>
                      <i class='fa fa-exchange-alt'></i> Pagar
                  </button>
                  <button class='historial_pagos btn btn-dark btn-sm' title='Ver historial pagos'> 
                      <i class="fa fa-dollar-sign"></i> Historial pagos
                    </button>                    `;
              }
          }
      },        

        {"data":"estado_fact",
          render: function(data,type,row){
                  if(data=='PENDIENTE'){
                    if (row.monto === row.saldo_pendiente) {
                      return  `
                      <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                        <i class="fa fa-history"></i> Historial
                      </button>
                      <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                          <i class='fa fa-exchange-alt'></i> Cambiar estado
                      </button>
                      <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                        <i class='fa fa-eye'></i> Ver Detalles
                      </button>
                      <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                        <i class='fa fa-edit'></i> Editar
                      </button>
                      <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                        <i class='fa fa-trash'></i> Eliminar
                      </button>
                    `;
                    }else{
                      return  `
                      <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                        <i class="fa fa-history"></i> Historial
                      </button>
                      <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                          <i class='fa fa-exchange-alt'></i> Cambiar estado
                      </button>
                      <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                        <i class='fa fa-eye'></i> Ver Detalles
                      </button>
                      <button class='editar hidden btn btn-primary btn-sm' title='Editar datos de la factura'>
                        <i class='fa fa-edit'></i> Editar
                      </button>
                      <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                        <i class='fa fa-trash'></i> Eliminar
                      </button>
                    `;
                    }
                   
                  }else if(data=='COBRADA'){
                    if (row.monto === row.saldo_pendiente) {
                      return  `
                      <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                        <i class="fa fa-history"></i> Historial
                      </button>
                      <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                          <i class='fa fa-exchange-alt'></i> Cambiar estado
                      </button>
                      <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                        <i class='fa fa-eye'></i> Ver Detalles
                      </button>
                      <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                        <i class='fa fa-edit'></i> Editar
                      </button>
                      <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                        <i class='fa fa-trash'></i> Eliminar
                      </button>
                    `;
                    }else{
                      return  `
                      <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                        <i class="fa fa-history"></i> Historial
                      </button>
                      <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                          <i class='fa fa-exchange-alt'></i> Cambiar estado
                      </button>
                      <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                        <i class='fa fa-eye'></i> Ver Detalles
                      </button>
                      <button class='editar hidden btn btn-primary btn-sm' title='Editar datos de la factura'>
                        <i class='fa fa-edit'></i> Editar
                      </button>
                      <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                        <i class='fa fa-trash'></i> Eliminar
                      </button>
                    `;
                    }
                  }else if(data=='FACTURADA'){
                    if (row.monto === row.saldo_pendiente) {
                      return  `
                      <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                        <i class="fa fa-history"></i> Historial
                      </button>
                      <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                          <i class='fa fa-exchange-alt'></i> Cambiar estado
                      </button>
                      <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                        <i class='fa fa-eye'></i> Ver Detalles
                      </button>
                      <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                        <i class='fa fa-edit'></i> Editar
                      </button>
                      <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                        <i class='fa fa-trash'></i> Eliminar
                      </button>
                    `;
                    }else{
                      return  `
                      <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                        <i class="fa fa-history"></i> Historial
                      </button>
                      <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                          <i class='fa fa-exchange-alt'></i> Cambiar estado
                      </button>
                      <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                        <i class='fa fa-eye'></i> Ver Detalles
                      </button>
                      <button class='editar hidden btn btn-primary btn-sm' title='Editar datos de la factura'>
                        <i class='fa fa-edit'></i> Editar
                      </button>
                      <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                        <i class='fa fa-trash'></i> Eliminar
                      </button>
                    `;
                    }
                }
                  else{
                    if (row.monto === row.saldo_pendiente) {
                      return  `
                      <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                        <i class="fa fa-history"></i> Historial
                      </button>
                      <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                          <i class='fa fa-exchange-alt'></i> Cambiar estado
                      </button>
                      <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                        <i class='fa fa-eye'></i> Ver Detalles
                      </button>
                      <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                        <i class='fa fa-edit'></i> Editar
                      </button>
                      <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                        <i class='fa fa-trash'></i> Eliminar
                      </button>
                    `;
                    }else{
                      return  `
                      <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                        <i class="fa fa-history"></i> Historial
                      </button>
                      <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                          <i class='fa fa-exchange-alt'></i> Cambiar estado
                      </button>
                      <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                        <i class='fa fa-eye'></i> Ver Detalles
                      </button>
                      <button class='editar hidden btn btn-primary btn-sm' title='Editar datos de la factura'>
                        <i class='fa fa-edit'></i> Editar
                      </button>
                      <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                        <i class='fa fa-trash'></i> Eliminar
                      </button>
                    `;
                    }
                  }
          }   
      },
                
    ],

    "language":idioma_espanol,
    select: true
});
tbl_facturas.on('draw.td',function(){
  var PageInfo = $("#tabla_facturas").DataTable().page.info();
  tbl_facturas.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}

function listar_facturas(){
  Cargar_Select_Obras_Sociales();
  Cargar_Select_Usuarios();
  document.getElementById('txt_fecha_desde').value='';
  document.getElementById('txt_fecha_hasta').value='';
  document.getElementById('select_estado').value='';

  tbl_facturas = $("#tabla_facturas").DataTable({
      "ordering":false,   
      "bLengthChange":true,
      "searching": { "regex": false },
      "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
      "pageLength": 10,
      pagingType: 'full_numbers',
      scrollCollapse: true,
      responsive: true,
      "destroy":true,
      "async": false ,
      "processing": true,
      "ajax":{
          "url":"../controller/facturas/controlador_listar_facturas_total.php",
          type:'POST'
      },
      dom: 'Bfrtip', 
     
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE FACTURAS",
          title: "LISTA DE FACTURAS",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE FACTURAS",
          title: "LISTA DE FACTURAS",
          className: 'btn btn-pdf',
          orientation: 'landscape', // <-- Establece la orientación en horizontal
          pageSize: 'A4', // <-- Especifica el tamaño de la página
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE FACTURAS",
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      "columns":[
        {"defaultContent":""},
        {"data":"obra_social"},
        {"data":"numero_fact"},
        {
          "data": "monto",
          "render": function (data, type, row) {
            return `<strong>$AR ${data}</strong>`;
          }
        },
        {
          "data": "saldo_cobrado",
          "render": function (data, type, row) {
            return `<strong>$AR ${data}</strong>`;
          }
        },
        {
          "data": "saldo_pendiente",
          render: function(data, type, row) {
            if (data == 0) {
              return '<span class="badge bg-success fs-5">$AR ' + data + '</span>';
            } else {
              return '<span class="badge bg-danger fs-5">$AR ' + data + '</span>';
            }
          }
        },
        {"data":"archivo_fact",
          render: function(data,type,row){
                  if(data=='' || data=='controller/facturas/filefacturas/'){
                      return "<button class='btn btn-danger btn-sm' disabled title='Sin archivo'><i class='fa fa-file-pdf'></i></button>";
                  }else{
                    return "<a class='btn btn-success btn-sm' href='../"+data+"' target='_blank' title='Ver archivo'><i class='fas fa-eye'></i> Ver factura</a>";
                  }
              }   
          },    
        {"data":"nota_credito",
          render: function(data,type,row){
                  if(data=='' || data=='controller/facturas/filenotacredito/'){
                      return "<button class='btn btn-danger btn-sm' disabled title='Ver archivo'><i class='fa fa-file-pdf'></i></button>";
                  }else{
                    return "<a class='btn btn-success btn-sm' href='../"+data+"' target='_blank' title='Ver archivo'><i class='fas fa-eye'></i> Ver nota de credito</a>";
                  }
              }   
          },    
       
        

        {"data":"fecha_credito"},

        {"data":"fecha_formateada"},

        {
          "data": "estado_fact",
          render: function(data, type, row) {
              if (data == 'PENDIENTE') {
                  return '<span class="badge bg-warning">PENDIENTE</span>';
              } else if (data == 'COBRADA') {
                  return '<span class="badge bg-success">COBRADA</span>';
              } else if (data == 'FACTURADA') {
                return '<span class="badge bg-primary">FACTURADA</span>';
            } else {
              return ` <span class="badge bg-danger">RECHAZADA</span>                    `;

              }
          }
      },        
      {
        "data": "saldo_pendiente",
        render: function(data, type, row) {
            if (data == 0) {
                return  `
           
                <button class='pagar btn btn-success btn-sm' title='Realizar pago' hidden>
                    <i class='fa fa-exchange-alt'></i> Pagar
                </button>
                <button class='historial_pagos btn btn-dark btn-sm' title='Ver historial pagos'> 
                    <i class="fa fa-dollar-sign"></i> Historial pagos
                  </button>
                  `;
          } else {
                return ` <button class='pagar btn btn-success btn-sm' title='Realizar pago'>
                    <i class='fa fa-exchange-alt'></i> Pagar
                </button>
                <button class='historial_pagos btn btn-dark btn-sm' title='Ver historial pagos'> 
                    <i class="fa fa-dollar-sign"></i> Historial pagos
                  </button>                    `;
            }
        }
    },        
    {"data":"estado_fact",
      render: function(data,type,row){
              if(data=='PENDIENTE'){
                if (row.monto === row.saldo_pendiente) {
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }else{
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar hidden btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }
               
              }else if(data=='COBRADA'){
                if (row.monto === row.saldo_pendiente) {
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }else{
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar hidden btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }
              }else if(data=='FACTURADA'){
                if (row.monto === row.saldo_pendiente) {
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }else{
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar hidden btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }
            }
              else{
                if (row.monto === row.saldo_pendiente) {
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }else{
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar hidden btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }
              }
      }   
  },
                
    ],

    "language":idioma_espanol,
    select: true
});
tbl_facturas.on('draw.td',function(){
  var PageInfo = $("#tabla_facturas").DataTable().page.info();
  tbl_facturas.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}

//LISTAR POR OBRAS SOCIALES
function listar_practica_paciente_obras(){
  let obra = document.getElementById('select_obras_buscar').value;
  let estado = document.getElementById('select_estado').value;

  tbl_facturas = $("#tabla_facturas").DataTable({
      "ordering":false,   
      "bLengthChange":true,
      "searching": { "regex": false },
      "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
      "pageLength": 10,
      pagingType: 'full_numbers',
      scrollCollapse: true,
      responsive: true,
      "destroy":true,
      "async": false ,
      "processing": true,
      "ajax":{
          "url":"../controller/facturas/controlador_listar_facturas_obra_estado.php",
          type:'POST',
          data:{
            obra:obra,
            estado:estado
          }
      },
      dom: 'Bfrtip', 
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE FACTURAS",
          title: "LISTA DE FACTURAS",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE FACTURAS",
          title: "LISTA DE FACTURAS",
          className: 'btn btn-pdf',
          orientation: 'landscape', // <-- Establece la orientación en horizontal
          pageSize: 'A4', // <-- Especifica el tamaño de la página
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE FACTURAS",
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      "columns":[
        {"defaultContent":""},
        {"data":"obra_social"},
        {"data":"numero_fact"},
        {
          "data": "monto",
          "render": function (data, type, row) {
            return `<strong>$AR ${data}</strong>`;
          }
        },
        {
          "data": "saldo_cobrado",
          "render": function (data, type, row) {
            return `<strong>$AR ${data}</strong>`;
          }
        },
        {
          "data": "saldo_pendiente",
          render: function(data, type, row) {
            if (data == 0) {
              return '<span class="badge bg-success fs-5">$AR ' + data + '</span>';
            } else {
              return '<span class="badge bg-danger fs-5">$AR ' + data + '</span>';
            }
          }
        },
        {"data":"archivo_fact",
          render: function(data,type,row){
                  if(data=='' || data=='controller/facturas/filefacturas/'){
                      return "<button class='btn btn-danger btn-sm' disabled title='Sin archivo'><i class='fa fa-file-pdf'></i></button>";
                  }else{
                    return "<a class='btn btn-success btn-sm' href='../"+data+"' target='_blank' title='Ver archivo'><i class='fas fa-eye'></i> Ver factura</a>";
                  }
              }   
          },    
        {"data":"nota_credito",
          render: function(data,type,row){
                  if(data=='' || data=='controller/facturas/filenotacredito/'){
                      return "<button class='btn btn-danger btn-sm' disabled title='Ver archivo'><i class='fa fa-file-pdf'></i></button>";
                  }else{
                    return "<a class='btn btn-success btn-sm' href='../"+data+"' target='_blank' title='Ver archivo'><i class='fas fa-eye'></i> Ver nota de credito</a>";
                  }
              }   
          },    
       
        

        {"data":"fecha_credito"},

        {"data":"fecha_formateada"},

        {
          "data": "estado_fact",
          render: function(data, type, row) {
              if (data == 'PENDIENTE') {
                  return '<span class="badge bg-warning">PENDIENTE</span>';
              } else if (data == 'COBRADA') {
                  return '<span class="badge bg-success">COBRADA</span>';
              } else if (data == 'FACTURADA') {
                return '<span class="badge bg-primary">FACTURADA</span>';
            } else {
              return ` <span class="badge bg-danger">RECHAZADA</span>                    `;

              }
          }
      },        
      {
        "data": "saldo_pendiente",
        render: function(data, type, row) {
            if (data == 0) {
                return  `
           
                <button class='pagar btn btn-success btn-sm' title='Realizar pago' hidden>
                    <i class='fa fa-exchange-alt'></i> Pagar
                </button>
                <button class='historial_pagos btn btn-dark btn-sm' title='Ver historial pagos'> 
                    <i class="fa fa-dollar-sign"></i> Historial pagos
                  </button>
                  `;
          } else {
                return ` <button class='pagar btn btn-success btn-sm' title='Realizar pago'>
                    <i class='fa fa-exchange-alt'></i> Pagar
                </button>
                <button class='historial_pagos btn btn-dark btn-sm' title='Ver historial pagos'> 
                    <i class="fa fa-dollar-sign"></i> Historial pagos
                  </button>                    `;
            }
        }
    },        
      {"data":"estado_fact",
        render: function(data,type,row){
                if(data=='PENDIENTE'){
                    return  `
                    <button class='historial_fac btn btn-dark btn-sm' title='Ver historia'>
                      <i class="fa fa-history"></i> Historial
                    </button>
                    <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                        <i class='fa fa-exchange-alt'></i> Cambiar estado
                    </button>
                    <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                      <i class='fa fa-eye'></i> Ver Detalles
                    </button>
                    <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                      <i class='fa fa-edit'></i> Editar
                    </button>
                    <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                      <i class='fa fa-trash'></i> Eliminar
                    </button>
                  `;
                }else if(data=='COBRADA'){
                    return  `
                    <button class='historial_fac btn btn-dark btn-sm' title='Ver historia'>
                      <i class="fa fa-history"></i> Historial
                    </button>
                    <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                        <i class='fa fa-exchange-alt'></i> Cambiar estado
                    </button>
                    <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                      <i class='fa fa-eye'></i> Ver Detalles
                    </button>
                    <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                      <i class='fa fa-edit'></i> Editar
                    </button>
                    <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                      <i class='fa fa-trash'></i> Eliminar
                    </button>
                  `;
                }else if(data=='FACTURADA'){
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historia'>
                    <i class="fa fa-history"></i> Historial
                  </button>

                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
            <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                        <i class='fa fa-trash'></i> Eliminar
                      </button>
                `;
              }
                else{
                    return `
                    <button class='historial_fac btn btn-dark btn-sm' title='Ver historia'>
                      <i class="fa fa-history"></i> Historial
                    </button>
                    <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                        <i class='fa fa-exchange-alt'></i> Cambiar estado
                    </button>
                    <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                      <i class='fa fa-eye'></i> Ver Detalles
                    </button>
                    <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                      <i class='fa fa-edit'></i> Editar
                    </button>
                    <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                      <i class='fa fa-trash'></i> Eliminar
                    </button>
                  `;
                }
        }   
    },
                
    ],

    "language":idioma_espanol,
    select: true
});
tbl_facturas.on('draw.td',function(){
  var PageInfo = $("#tabla_facturas").DataTable().page.info();
  tbl_facturas.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}

//LISTAR POR FECHAS Y USUARIO
function listar_practica_paciente_fecha_usu(){
  let fechaini = document.getElementById('txt_fecha_desde').value;
  let fechafin = document.getElementById('txt_fecha_hasta').value;
  let usu = document.getElementById('select_usuario').value;

  tbl_facturas = $("#tabla_facturas").DataTable({
      "ordering":false,   
      "bLengthChange":true,
      "searching": { "regex": false },
      "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
      "pageLength": 10,
      pagingType: 'full_numbers',
      scrollCollapse: true,
      responsive: true,
      "destroy":true,
      "async": false ,
      "processing": true,
      "ajax":{
          "url":"../controller/facturas/controlador_listar_facturas_fecha_usu.php",
          type:'POST',
          data:{
            fechaini:fechaini,
            fechafin:fechafin,
            usu:usu
          }
      },
      dom: 'Bfrtip', 
     
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE FACTURAS",
          title: "LISTA DE FACTURAS",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE FACTURAS",
          title: "LISTA DE FACTURAS",
          className: 'btn btn-pdf',
          orientation: 'landscape', // <-- Establece la orientación en horizontal
          pageSize: 'A4', // <-- Especifica el tamaño de la página
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE FACTURAS",
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      "columns":[
        {"defaultContent":""},
        {"data":"obra_social"},
        {"data":"numero_fact"},
        {
          "data": "monto",
          "render": function (data, type, row) {
            return `<strong>$AR ${data}</strong>`;
          }
        },
        {
          "data": "saldo_cobrado",
          "render": function (data, type, row) {
            return `<strong>$AR ${data}</strong>`;
          }
        },
        {
          "data": "saldo_pendiente",
          render: function(data, type, row) {
            if (data == 0) {
              return '<span class="badge bg-success fs-5">$AR ' + data + '</span>';
            } else {
              return '<span class="badge bg-danger fs-5">$AR ' + data + '</span>';
            }
          }
        },
        {"data":"archivo_fact",
          render: function(data,type,row){
                  if(data=='' || data=='controller/facturas/filefacturas/'){
                      return "<button class='btn btn-danger btn-sm' disabled title='Sin archivo'><i class='fa fa-file-pdf'></i></button>";
                  }else{
                    return "<a class='btn btn-success btn-sm' href='../"+data+"' target='_blank' title='Ver archivo'><i class='fas fa-eye'></i> Ver factura</a>";
                  }
              }   
          },    
        {"data":"nota_credito",
          render: function(data,type,row){
                  if(data=='' || data=='controller/facturas/filenotacredito/'){
                      return "<button class='btn btn-danger btn-sm' disabled title='Ver archivo'><i class='fa fa-file-pdf'></i></button>";
                  }else{
                    return "<a class='btn btn-success btn-sm' href='../"+data+"' target='_blank' title='Ver archivo'><i class='fas fa-eye'></i> Ver nota de credito</a>";
                  }
              }   
          },    
       
        

        {"data":"fecha_credito"},

        {"data":"fecha_formateada"},

    

        {
          "data": "estado_fact",
          render: function(data, type, row) {
              if (data == 'PENDIENTE') {
                  return '<span class="badge bg-warning">PENDIENTE</span>';
              } else if (data == 'COBRADA') {
                  return '<span class="badge bg-success">COBRADA</span>';
              } else if (data == 'FACTURADA') {
                return '<span class="badge bg-primary">FACTURADA</span>';
            } else {
              return ` <span class="badge bg-danger">RECHAZADA</span>`;

              }
          }
      },        
      {
        "data": "saldo_pendiente",
        render: function(data, type, row) {
            if (data == 0) {
                return  `
           
                <button class='pagar btn btn-success btn-sm' title='Realizar pago' hidden>
                    <i class='fa fa-exchange-alt'></i> Pagar
                </button>
                <button class='historial_pagos btn btn-dark btn-sm' title='Ver historial pagos'> 
                    <i class="fa fa-dollar-sign"></i> Historial pagos
                  </button>
                  `;
          } else {
                return ` <button class='pagar btn btn-success btn-sm' title='Realizar pago'>
                    <i class='fa fa-exchange-alt'></i> Pagar
                </button>
                <button class='historial_pagos btn btn-dark btn-sm' title='Ver historial pagos'> 
                    <i class="fa fa-dollar-sign"></i> Historial pagos
                  </button>                    `;
            }
        }
    },        
    {"data":"estado_fact",
      render: function(data,type,row){
              if(data=='PENDIENTE'){
                if (row.monto === row.saldo_pendiente) {
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }else{
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar hidden btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }
               
              }else if(data=='COBRADA'){
                if (row.monto === row.saldo_pendiente) {
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }else{
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar hidden btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }
              }else if(data=='FACTURADA'){
                if (row.monto === row.saldo_pendiente) {
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }else{
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar hidden btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }
            }
              else{
                if (row.monto === row.saldo_pendiente) {
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }else{
                  return  `
                  <button class='historial_fac btn btn-dark btn-sm' title='Ver historial'>
                    <i class="fa fa-history"></i> Historial
                  </button>
                  <button class='cambio btn btn-warning btn-sm' title='Cambiar estado'>
                      <i class='fa fa-exchange-alt'></i> Cambiar estado
                  </button>
                  <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                  </button>
                  <button class='editar hidden btn btn-primary btn-sm' title='Editar datos de la factura'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar factura'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
                `;
                }
              }
      }   
  },
    ],

    "language":idioma_espanol,
    select: true
});
tbl_facturas.on('draw.td',function(){
  var PageInfo = $("#tabla_facturas").DataTable().page.info();
  tbl_facturas.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}



function AbrirRegistro(){
  $("#modal_registro").modal({backdrop:'static',keyboard:false})
  $("#modal_registro").modal('show');


}
function Cargar_Select_Usuarios() {
  $.ajax({
    url: "../controller/practicas_paciente/controlador_cargar_select_usuario.php",
    type: 'POST',
  }).done(function(resp) {
    let data = JSON.parse(resp);
    let cadena = "<option value=''>Seleccionar Usuario</option>";
    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) {
        cadena += "<option value='" + data[i][0] + "'>DNI: " + data[i][1] + " - Usuario: " + data[i][2] + "</option>";
      }
    } else {
      cadena += "<option value=''>No hay usuarios disponibles</option>";
    }
    $('#select_usuario').html(cadena);


    // Inicializar Select2 después de cargar opciones
    $('#select_usuario').select2({
      placeholder: "Seleccionar Usuario",
      allowClear: true,
      width: '100%' // Asegura que use todo el ancho
    });
  });
}

function Cargar_Select_Areas() {
  $.ajax({
    url: "../controller/practicas_paciente/controlador_cargar_select_area.php",
    type: 'POST',
  }).done(function(resp) {
    let data = JSON.parse(resp);
    let cadena = "<option value=''>Seleccionar Área</option>";
    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) {
        cadena += "<option value='" + data[i][0] + "'>" + data[i][1] + "</option>";
      }
    } else {
      cadena += "<option value=''>No hay áreas disponibles</option>";
    }
    $('#select_area').html(cadena);
    $('#select_area_editar').html(cadena);


    // Inicializar Select2 después de cargar opciones
    $('#select_area').select2({
      placeholder: "Seleccionar Área",
      allowClear: true,
      width: '100%' // Asegura que use todo el ancho
    });
  });
}

function Cargar_Select_Obras_Sociales() {
  $.ajax({
    url: "../controller/obras_sociales/controlador_cargar_select_obras_sociales.php",
    type: 'POST',
  }).done(function(resp) {
    let data = JSON.parse(resp);
    let cadena = "<option value=''>Seleccionar Obra Social</option>";
    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) {
        cadena += "<option value='" + data[i][0] + "'>CUIT: " + data[i][1] + " - Nombre: " + data[i][2] + "</option>";
      }
    } else {
      cadena += "<option value=''>No hay obras disponibles</option>";
    }
    $('#select_obras_buscar').html(cadena);


    // Inicializar Select2 después de cargar opciones
    $('#select_obras_buscar').select2({
      placeholder: "Seleccionar Obra Social",
      allowClear: true,
      width: '100%' // Asegura que use todo el ancho
    });
  });
} 


function Cargar_Select_Obras_Sociales2() {
  $.ajax({
    url: "../controller/obras_sociales/controlador_cargar_select_obras_sociales.php",
    type: 'POST'
  }).done(function(resp) {
    let data = JSON.parse(resp);
    let cadena = "<option value='' disabled selected>Seleccione Obra Social</option>"; // Placeholder por defecto
    
    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) {
        cadena += `<option value='${data[i][0]}'>CUIT: ${data[i][1]} - Nombre: ${data[i][2]}</option>`;
      }
    }

    $('#select_obras, #select_obras_editar').html(cadena).select2({
      placeholder: "Seleccionar Obra Social",
      allowClear: true,
      width: '100%' 
    });

    // Evento change para cargar prácticas cuando cambia la obra social
    $('#select_obras, #select_obras_editar').off('change').on('change', function() {
      let obraSocialId = $(this).val();
      let selectPracticaId = $(this).attr("id") === "select_obras" ? "select_practica" : "select_practica_editar";

      if (obraSocialId) {
        Cargar_Select_Practica(obraSocialId, selectPracticaId); // Cargar prácticas para la obra social seleccionada
        cargarDetallePracticasPorObraSocial(obraSocialId);
      } else {
        // Si no hay obra social seleccionada, limpiar la tabla y selects
        $(`#${selectPracticaId}`).html("<option value='' disabled selected>No hay datos disponibles</option>").trigger("change");
        $('#tbody_tabla_practica_editar').html('');
        $('#lbl_totalneto1_editar').text('Total: $0.00');
      }
    });

    // Si ya hay una obra social seleccionada, cargar sus prácticas
    let obraSocialIdInicial = $('#select_obras_editar').val();
    if (obraSocialIdInicial) {
      Cargar_Select_Practica(obraSocialIdInicial, "select_practica_editar");
      cargarDetallePracticasPorObraSocial(obraSocialIdInicial);
    }
  }).fail(function() {
    console.error("Error al cargar las obras sociales.");
  });
}

function Cargar_Select_Practica(obraSocialId, selectId, id_practica = null) {
  $.ajax({
    url: "../controller/facturas/controlador_cargar_select_paciente_practica_factura.php",
    type: 'POST',
    data: { id2: obraSocialId },
  }).done(function(resp) {
    let data = JSON.parse(resp);
    let cadena = "<option value='' disabled selected>No hay datos disponibles</option>";

    if (data.length > 0) {
      cadena = "<option value='' disabled selected>Seleccione práctica</option>";
      for (let i = 0; i < data.length; i++) {
        cadena += `<option value='${data[i][0]}'>DNI: ${data[i][1]} - Paciente: ${data[i][2]}</option>`;
      }
    }

    $(`#${selectId}`).html(cadena).select2({
      placeholder: "Seleccionar práctica - paciente",
      allowClear: true,
      width: '100%'
    });

    if (id_practica) {
      $(`#${selectId}`).val(id_practica).trigger('change');
    }

    // Evento change para cargar el precio cuando se seleccione una práctica
    $(`#${selectId}`).off('change').on('change', function() {
      let idSeleccionado = $(this).val();
      if (idSeleccionado) {
        Traerprecio(idSeleccionado);
      } else {
        $("#txt_precio").val('');
      }
    });

  }).fail(function() {
    console.error("Error al cargar las prácticas.");
  });
}


function Traerprecio(id) {
  $.ajax({
    url: "../controller/facturas/controlador_traermonto.php",
    type: 'POST',
    data: { id: id }
  }).done(function(resp) {
    var data = JSON.parse(resp);
    if (data.length > 0) {
      $("#txt_precio").val(data[0].monto || data[0][1]); // Asegurar acceso correcto al dato
      $("#txt_precio_editar").val(data[0].monto || data[0][1]); // Asegurar acceso correcto al dato
    } else {
      $("#txt_precio").val('');
      $("#txt_precio_editar").val('');

    }
  }).fail(function() {
    console.log("Error al traer el precio.");
  });
}
function cargarDetallePracticasPorObraSocial(obraSocialId) {
  let tbody = $('#tbody_tabla_practica');
  tbody.html('<tr><td colspan="4">Cargando...</td></tr>');

  $.ajax({
    url: "../controller/facturas/controlador_cargar_practicas_por_obra.php",
    type: 'POST',
    data: { id_obra: obraSocialId }
  }).done(function(resp) {
    try {
      let data = JSON.parse(resp);
      let html = '';
      let totalNeto = 0;
      
      if (data.length > 0) {
        data.forEach(practica => {
          const subtotal = parseFloat(practica.total) || 0;
          totalNeto += subtotal;
          html += `
            <tr>
              <td for="id">${practica.id_paciente_practica}</td>
              <td>${practica.Dni} - ${practica.paciente}</td>
              <td>${subtotal.toFixed(2)}</td>
              <td>
                <button class="btn btn-danger btn-sm" onclick='remove(this)'>
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>`;
        });
      } else {
        html = '<tr><td colspan="4">No hay prácticas disponibles para esta obra social</td></tr>';
      }

      tbody.html(html);
      $("#lbl_totalneto1").html(`<b>Total:</b> $AR ${totalNeto.toFixed(2)}`);
    } catch (error) {
      console.error('Error al procesar los datos:', error);
      tbody.html('<tr><td colspan="4">Error al procesar los datos</td></tr>');
    }
  }).fail(function(error) {
    console.error('Error al cargar las prácticas:', error);
    tbody.html('<tr><td colspan="4">Error al cargar las prácticas</td></tr>');
  });
}





// MODAL ESTADO
$('#tabla_facturas').on('click','.cambio',function(){
    var data = tbl_facturas.row($(this).parents('tr')).data();
  
    if(tbl_facturas.row(this).child.isShown()){
        var data = tbl_facturas.row(this).data();
    }
  $("#modal_estado").modal('show');
  
    document.getElementById('lb_tituloesta').innerHTML="<b>FACTURA N°:</b> "+data.numero_fact+"";
    document.getElementById('lb_titulo2esta').innerHTML="<b>OBRA SOCIAL:</b> "+data.obra_social+"";
    document.getElementById('id_estado').value=data.id_factura;
    document.getElementById('select_estado_edit').value=data.estado_fact;
  
  })





 //EDITAR ESTADO
 function Modificar_Estado() {
    let id = document.getElementById('id_estado').value;
    let esta = document.getElementById('select_estado_edit').value;
    let motivo = document.getElementById('txt_motivo').value;
    let idusu = document.getElementById('txtprincipalid').value;

    
    if (esta=='RECHAZADA'){
      if (id.length == 0 || esta.length == 0||motivo.length == 0) {
        return Swal.fire("Mensaje de Advertencia", "Tiene campos vacíos", "warning");
    }
  }else if(esta=='COBRADA'){
    if (id.length == 0 || esta.length == 0) {
      return Swal.fire("Mensaje de Advertencia", "Tiene campos vacíos", "warning");
  }

  }else{
    if (id.length == 0 || esta.length == 0) {
      return Swal.fire("Mensaje de Advertencia", "Tiene campos vacíos", "warning");
  }
  }


    if (esta=='RECHAZADA'){
        estado='<b style="color:red">' + esta + '</b> '
    }else if(esta=='COBRADA'){
        estado='<b style="color:green">' + esta + '</b> '

    }else if(esta=='FACTURADA'){
      estado='<b style="color:blue">' + esta + '</b> '

  }else{
        estado='<b style="color:#dcdc00">' + esta + '</b> '

    }
    // Confirmación antes de modificar el estado
    Swal.fire({
        title: '¿Está seguro de modificar el estado de la factura?',
        html: 'El estado de la factura será actualizado al estado de: '+estado+'',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, modificar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Procede con la petición AJAX si el usuario confirma
            $.ajax({
                url: "../controller/facturas/controlador_modificar_estado.php",
                type: 'POST',
                data: {
                    id: id,
                    esta: esta,
                    motivo: motivo,
                    idusu:idusu
                }
            }).done(function (resp) {
                if (resp > 0) {
                    Swal.fire("Mensaje de Confirmación", "Estado actualizado satisfactoriamente ", "success").then(() => {
                        tbl_facturas.ajax.reload();
                        $("#modal_estado").modal('hide');
                    });
                } else {
                    Swal.fire("Mensaje de Error", "No se completó la actualización", "error");
                }
            });
        }
    });
}

   

//ELIMINAR PRACTICA PACIENTE
function Eliminar_Factura(id){

  let idusu = document.getElementById('txtprincipalid').value;


  $.ajax({
    "url":"../controller/facturas/controlador_eliminar_facturas.php",
    type:'POST',
    data:{
      id:id,
      idusu:idusu
    }
  }).done(function(resp){
    if(resp>0){
        Swal.fire("Mensaje de Confirmación","Se elimino la factura con exito","success").then((value)=>{
          tbl_facturas.ajax.reload();
        });
    }else{
      return Swal.fire("Mensaje de Advetencia","No se puede eliminar esta factura, verifique por favor","warning");

    }
  })
}

//ENVIANDO AL BOTON DELETE
$('#tabla_facturas').on('click','.eliminar',function(){
  var data = tbl_facturas.row($(this).parents('tr')).data();

  if(tbl_facturas.row(this).child.isShown()){
      var data = tbl_facturas.row(this).data();
  }
  Swal.fire({
    title: 'Desea eliminar la factura con el numero: <b style="color:blue">'+data.numero_fact+'</b>?',
    text: "Una vez aceptado la factura pasara a FACTURAS ARCHIVADAS en ahí lo podra encontrar!!!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
        Eliminar_Factura(data.id_factura);
    }
  })
})

//MODAL VER PRÁCTICAS
$('#tabla_facturas').on('click','.mostrar',function(){
  var data = tbl_facturas.row($(this).parents('tr')).data();

  if(tbl_facturas.row(this).child.isShown()){
      var data = tbl_facturas.row(this).data();
  }
$("#modal_ver_facturas_paci").modal('show');

document.getElementById('lb_titulo_facturas').innerHTML="<b>FACTURA N°:</b> "+data.numero_fact+"";
document.getElementById('lb_titulo2_facturas').innerHTML="<b>OBRA SOCIAL:</b> "+data.obra_social+"";
document.getElementById('lb_titulo3_facturas').innerHTML="<b style='color:blue'>FECHA DE ULTIMA ACTUALIZACIÓN:</b> "+data.fecha_formateada2+"";
document.getElementById('lb_titulo4_facturas').innerHTML="<b style='color:blue'>USUARIO QUE ACTUALIZO:</b> "+data.USUARIO+"";

listar_detalle_factura(data.id_factura);

})
// VISTA DE PRACTICAS
var tbl_detalle_factu;

function listar_detalle_factura(id) {
  tbl_detalle_factu = $("#tabla_ver_facturas_paci").DataTable({
      "ordering": false,
      "bLengthChange": true,
      "searching": false,  // Deshabilita la barra de búsqueda
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      "pageLength": 5,
      "destroy": true,
      "pagingType": 'full_numbers',
      "scrollCollapse": true,
      "responsive": true,
      "async": false,
      "processing": true,
      "dom": 'Bfrtip', // Agrega los botones
      "buttons": [
          {
              extend: 'excelHtml5',
              text: '<i class="fas fa-file-excel"></i> Excel',
              className: 'btn btn-success'
          },
          {
              extend: 'pdfHtml5',
              text: '<i class="fas fa-file-pdf"></i> PDF',
              className: 'btn btn-danger',
              orientation: 'landscape',
              pageSize: 'A4'
          },
          {
              extend: 'print',
              text: '<i class="fas fa-print"></i> Imprimir',
              className: 'btn btn-primary'
          }
      ],
      "ajax": {
          "url": "../controller/facturas/controlador_listar_detalle_factura.php",
          "type": "POST",
          "data": { id: id },
          "dataSrc": function(json) {
              console.log("Respuesta JSON:", json);
              return json.data;
          }
      },

      "columns": [
          { 
              "data": null, 
              "render": function(data, type, row, meta) {
                  return meta.row + 1; // Asigna un número correlativo
              }
          },
          { "data": "Dni" },
          { "data": "PACIENTE" },
          { 
              "data": "subtotal",
              "render": function(data) {
                  return `<strong>$AR ${parseFloat(data).toFixed(2)}</strong>`;  
              }
          }
      ],
      "language": idioma_espanol,
      "select": true
  });

  // Evento para calcular y mostrar el total con tamaño más grande
  tbl_detalle_factu.on('draw.dt', function() {
      var total = 0;

      // Sumar los valores de la columna "subtotal"
      tbl_detalle_factu.column(3, { page: 'current' }).data().each(function(value) {
          total += parseFloat(value) || 0;
      });

      // Mostrar el total con tamaño más grande
      $('#total_sub_total').html(`<span style="font-size: 20px; font-weight: bold; color: #0A5D86;">$AR ${total.toFixed(2)}</span>`);
  });
}




function Agregar_practica() {
  var id_practica = $("#select_practica").val();
  var practica = $("#select_practica option:selected").text();
  var precio = $("#txt_precio").val().trim(); // Elimina espacios en blanco
  
  if (!id_practica || id_practica.trim() === "" || !precio || precio === "") {
      return Swal.fire("Mensaje de Advertencia", "Seleccione una práctica paciente y un precio por favor", "warning");
  }
  
  if(verificarid(id_practica)){
    return Swal.fire("Mensaje de Advertencia","La asignatura ya fue agregado a la tabla","warning");
   }
 
   var datos_agregar ="<tr>";
   datos_agregar+="<td for='id'>"+id_practica+"</td>";
   datos_agregar+="<td>"+practica+"</td>";
   datos_agregar+="<td>"+precio+"</td>";
 
   datos_agregar+="<td><button class='btn btn-danger' onclick='remove(this)'><i class='fas fa-trash'><i></button></td>";
   datos_agregar+="</tr>";
   $("#tabla_detalle_factura").append(datos_agregar);
   SumarTotal();
 
  
 }
function remove(t){
  console.log('hola');

  var td =t.parentNode;
  var tr=td.parentNode;
  var table =tr.parentNode;
  table.removeChild(tr);
  SumarTotal();
  console.log(SumarTotal());
}
function SumarTotal() {
  let total = 0;

  $("#tabla_detalle_factura tbody#tbody_tabla_practica tr").each(function() {
    let monto = $(this).find('td').eq(2).text().replace(/[^0-9.]/g, ''); // Elimina todo excepto números y punto decimal
    
    let valorNumerico = parseFloat(monto);
    
    if (!isNaN(valorNumerico)) { // Verifica si es un número válido
      total += valorNumerico;
    }
  });

  $("#lbl_totalneto1").html(`<b>Total:</b> $AR ${total.toFixed(2)}`);
}


function verificarid(id){
  let idverificar=document.querySelectorAll('#tabla_detalle_factura td[for="id"]');
  return [].filter.call(idverificar, td=>td.textContent ===id).length===1;
}



function Registrar_Practica_paciente() {
  let count = $("#tabla_detalle_factura tbody#tbody_tabla_practica tr").length;
  if (count === 0) {
      return Swal.fire("Mensaje de Advertencia", "La tabla de prácticas debe tener al menos un registro", "warning");
  }

   //DATOS DEL DOCENTE
   let nrofact = document.getElementById('txt_nro_factura').value;
   let total = parseFloat(document.getElementById('lbl_totalneto1').textContent.replace(/[^0-9.]/g, '')) || 0;
   let factura = document.getElementById('txt_factura').value;
   let notacre = document.getElementById('txt_notacre').value;
   let fecha = document.getElementById('txt_fecha_nota').value;
   let idusu = document.getElementById('txtprincipalid').value;
   
   if (nrofact.length == 0 || total == 0 || factura.length == 0) {
     return Swal.fire("Mensaje de Advertencia", "Tiene campos vacíos en el formulario, revise", "warning");
   }
   
   // Obtener la fecha actual para generar nombres únicos
   let f = new Date();
   
   // Procesar Factura
   let nombrefactura = "";
   if (factura.length > 0) {
     let extensionFact = factura.split('.').pop();
     nombrefactura = "IMG" + f.getDate() + "-" + (f.getMonth() + 1) + "-" + f.getFullYear() + "-" + f.getHours() + "-" + f.getMilliseconds() + "." + extensionFact;
   }
   
   // Procesar Nota de Crédito
   let nombrenotacre = "";
   if (notacre.length > 0) {
     let extensionNotacre = notacre.split('.').pop();
     nombrenotacre = "NC" + f.getDate() + "-" + (f.getMonth() + 1) + "-" + f.getFullYear() + "-" + f.getHours() + "-" + f.getMilliseconds() + "." + extensionNotacre;
   }
   
   // Crear FormData
   let formData = new FormData();
   let facturaObj = $("#txt_factura")[0].files[0]; // Obtener el archivo de factura
   let notacreObj = $("#txt_notacre")[0].files[0]; // Obtener el archivo de nota de crédito
   
   if (facturaObj) {
     formData.append("factura", facturaObj, nombrefactura);
   }
   if (notacreObj) {
     formData.append("notacre", notacreObj, nombrenotacre);
   }
   
   // Agregar otros datos al FormData
      formData.append("nrofact",nrofact);
      formData.append("total",total);
      formData.append("nombrefactura",nombrefactura);
      formData.append("facturaObj",facturaObj);
      formData.append("nombrenotacre",nombrenotacre);
      formData.append("notacreObj",notacreObj);
      formData.append("fecha",fecha);
      formData.append("idusu",idusu);

     $.ajax({
       url:"../controller/facturas/controlador_registro_factura.php",
       type:'POST',
       data:formData,
       contentType:false,
       processData:false,
       success:function(resp){
         if(resp.length>0){
          Registrar_Detalle_factura(parseInt(resp));
          Swal.fire("Mensaje de Confirmación","Se registro correctamente al la factura con el N° <b>"+nrofact+"</b>","success").then((value)=>{

             document.getElementById('txt_nro_factura').value = "";
             document.getElementById('txt_precio').value = "";
             document.getElementById('txt_factura').value = "";
             document.getElementById('txt_notacre').value = "";
             document.getElementById('txt_fecha_nota').value = "";
            Cargar_Select_Obras_Sociales2();
            Cargar_Select_Practica();
            $("#tabla_detalle_factura tbody#tbody_tabla_practica").empty();

             // Cerrar el modal

            });

         }else{
           Swal.fire("Mensaje de Advertencia","No se pudo registrar al usuario","warning");
         }
       }
     });
 }
 

// REGISTRO DETALLE PRACTICAS
function Registrar_Detalle_factura(id) {
  let count = $("#tabla_detalle_factura tbody#tbody_tabla_practica tr").length;
  if (count === 0) {
      return Swal.fire("Mensaje de Advertencia", "El detalle de las prácticas debe tener al menos un registro", "warning");
  }

  let arreglo_practica_paciente = [];
  let arreglo_subtotal = [];

  $("#tabla_detalle_factura tbody#tbody_tabla_practica tr").each(function () {
    arreglo_practica_paciente.push($(this).find('td').eq(0).text().trim());
      arreglo_subtotal.push($(this).find('td').eq(2).text().trim());
  });

  let practicas_paciente = arreglo_practica_paciente.join(",");
  let subtotal = arreglo_subtotal.join(",");

  $.ajax({
      url: "../controller/facturas/controlador_detalle_facturas.php",
      type: 'POST',
      data: {
          id: id,
          practicas_paciente: practicas_paciente,
          subtotal:subtotal
      }
  }).done(function (resp) {
      if (resp > 0) {
          tbl_facturas.ajax.reload();
          $("#modal_registro").modal('hide');
          document.getElementById('txt_nro_factura').value="";
          Cargar_Select_Obras_Sociales2();
          Cargar_Select_Practica();
          document.getElementById('txt_precio').value="";
          document.getElementById('txt_factura').value="";
          document.getElementById('label_txt_factura').innerHTML="Seleccione Factura...";
          document.getElementById('txt_notacre').value="";
          document.getElementById('label_txt_notacre').innerHTML="Seleccione Nota de crédito...";
          document.getElementById('txt_fecha_nota').value="";

          $("#tabla_detalle_factura tbody#tbody_tabla_practica").empty();

          $("#lbl_totalneto1").html("<b>Total:</b> $AR");


      } else {
          return Swal.fire("Mensaje De Advertencia", "Lo sentimos, no se pudo completar el registro", "warning");
      }
  });
}

//EDITAR PRACTICAS - PACIENTE

$('#tabla_facturas').on('click','.editar',function(){
  var data = tbl_facturas.row($(this).parents('tr')).data();

  if(tbl_facturas.row(this).child.isShown()){
      var data = tbl_facturas.row(this).data();
  }
  $("#modal_editar").modal('show');
  document.getElementById('id_factura').value = data.id_factura;
  document.getElementById('txt_nro_factura_editar').value = data.numero_fact;

  $("#select_obras_editar").val(data.id_cuit).trigger('change');



  document.getElementById('facturaactual').value = data.archivo_fact;
  document.getElementById('notaactual').value = data.nota_credito;
  document.getElementById('txt_fecha_nota_editar').value = data.fecha_nota_credito;
  document.getElementById('lbl_totalneto1_editar').innerHTML = '<b>Total:</b> $AR '+data.monto;

  listar_practicas_del_paciente(data.id_factura);


})
var tbl_traer_datos;
function listar_practicas_del_paciente(id) {

  tbl_traer_datos = $("#tabla_detalle_factura_editar").DataTable({
    "ordering": false,
    "bLengthChange": false,
    "searching": false,
    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    "pageLength": 10,
    "destroy": true,
    "pagingType": 'full_numbers',
    "scrollCollapse": false,
    "responsive": true,
    "processing": true,
   "ajax": {
    "url": "../controller/facturas/controlador_listar_detalle_facturas_edit.php",
    "type": 'POST',
    "data": { id: id },
   
    },
    "columns": [
      {"data": "id_detalle_factura"},
      {"data": "id_factura"},
      {"data": "PACIENTE"},
      {"data": "subtotal"},
      {"defaultContent": "<button class='delete btn btn-danger btn-sm' title='Eliminar datos de especialidad'><i class='fa fa-trash'></i> Eliminar</button>"}
    ],
    "language": idioma_espanol,
    "select": true
  });
}

function Eliminar_detalle_factura_unico(id){
  $.ajax({
    url: "../controller/facturas/controlador_eliminar_detalle_facturas_unico.php",
    type: 'POST',
    data: { id: id }
  }).done(function(resp) {
    if(resp > 0){
      Swal.fire("Mensaje de Confirmación", "Se eliminó la práctica - paciente exitosamente", "success")
      .then(() => {
        tbl_traer_datos.ajax.reload(null, false); // Recargar sin reiniciar paginación
        setTimeout(SumarTotal_Editar, 500); // Esperar un poco antes de recalcular
        Cargar_Select_Practica();

      });
    } else {
      Swal.fire("Mensaje de Advertencia", "No se puede eliminar esta práctica paciente, verifique por favor", "warning");
    }
  });
}


//ENVIANDO AL BOTON DELETE
$('#tabla_detalle_factura_editar').on('click','.delete',function(){
  var data = tbl_traer_datos.row($(this).parents('tr')).data();

  if(tbl_traer_datos.row(this).child.isShown()){
      var data = tbl_traer_datos.row(this).data();
  }
  Swal.fire({
    title: 'Desea eliminar la prácticas del paciente: '+data.PACIENTE+' seleccionado?',
    text: "Una vez aceptado la práctica - paciente sera eliminada!!!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_detalle_factura_unico(data.id_detalle_factura);
      SumarTotal_Editar();
      Cargar_Select_Practica();
    }
  })
})



function Agregar_practica_editar(){
  console.log("dsfsdf");
  var id_factura_principal=$("#id_factura").val();
  var id_practica_edi=$("#select_practica_editar").val();
  var practica_edi=$("#select_practica_editar option:selected").text();
  var precio_edi=$("#txt_precio_editar").val();

  if (!id_practica_edi || id_practica_edi.trim() === "" || !precio_edi || precio_edi === "") {
    return Swal.fire("Mensaje de Advertencia", "Seleccione una práctica y un precio por favor", "warning");
}
  if(verificarid_editar(id_practica_edi)){
   return Swal.fire("Mensaje de Advertencia","La práctica ya fue agregado a la tabla","warning");
  }

  var datos_agregar ="<tr>";

  datos_agregar+="<td >"+id_factura_principal+"</td>";
  datos_agregar+="<td for='id'>"+id_practica_edi+"</td>";
  datos_agregar+="<td>"+practica_edi+"</td>";
  datos_agregar+="<td>"+precio_edi+"</td>";

  datos_agregar+="<td><button class='btn btn-danger' onclick='remove1(this)'><i class='fas fa-trash'><i></button></td>";
  datos_agregar+="</tr>";
  $("#tabla_detalle_factura_editar").append(datos_agregar);
  SumarTotal_Editar();

 
}
//BORRAR REGISTRO
function remove1(t){
  var td =t.parentNode;
  var tr=td.parentNode;
  var table =tr.parentNode;
  table.removeChild(tr);
  SumarTotal_Editar();

}
//SUMAR TOTAL
// SUMAR TOTAL
function SumarTotal_Editar() {
  let total = 0;

  // Recorremos cada fila de la tabla
  $("#tabla_detalle_factura_editar tbody tr").each(function () {
    let precio = $(this).find('td').eq(3).text().trim(); // Tomamos el precio de la columna correcta (índice 2)
    
    if (precio !== "") { // Aseguramos que el valor no esté vacío
      total += parseFloat(precio) || 0; // Convertimos a número y evitamos NaN con || 0
    }
  });

  // Mostramos el total en el label
  $("#lbl_totalneto1_editar").html("<b>Total: </b>$AR " + total.toFixed(2));
}

//VALIDACIÓN
function verificarid_editar(id){
  let idverificar=document.querySelectorAll('#tbody_tabla_practica_editar td[for="id"]');
  return [].filter.call(idverificar, td=>td.textContent ===id).length===1;
}



//EDITANDO PRACTICAS - PACIENTE
function Modificar_detalle_practicas() {
  let componentes = [];

  let totalText = document.getElementById('lbl_totalneto1_editar')?.textContent.replace(/[^0-9.]/g, '').trim();
  let total = parseFloat(totalText) || 0;  // Validación robusta del total
  let idusu = document.getElementById('txtprincipalid')?.value.trim();

  if (!idusu) {
    return Swal.fire("Mensaje de Advertencia", "El ID del usuario no es válido.", "warning");
  }

  // Recorremos solo el cuerpo de la tabla para evitar la cabecera
  $("#tabla_practica_editar tbody tr").each(function() {
    let id_practica_general = $(this).find('td').eq(0).text().trim();
    let id_practica = $(this).find('td').eq(1).text().trim();
    let precio = parseFloat($(this).find('td').eq(3).text().trim()) || 0;
    let cantidad = parseFloat($(this).find('td').eq(4).text().trim()) || 0;
    let precioText = $(this).find('td').eq(5).text().trim();
    let subtotal = parseFloat(precioText) || 0; // Convertimos correctamente a número

    // Validar que todos los campos estén completos
    if (id_practica && subtotal > 0) {
      componentes.push({
        id_practica_general,
        id_practica,
        precio: precio.toFixed(2),
        cantidad,
        subtotal: subtotal.toFixed(2) // Formato con 2 decimales
      });
    }
  });

  // Verificar si hay datos para enviar
  if (componentes.length === 0) {
    return Swal.fire("Mensaje de Advertencia", "No hay prácticas válidas en la tabla para modificar", "warning");
  }

  // Enviar datos por AJAX
  $.ajax({
    url: '../controller/practicas_paciente/controlador_modificar_detalle_practicas.php',
    type: 'POST',
    data: {
      total: total.toFixed(2),
      idusu: idusu,
      componentes: JSON.stringify(componentes)
    },
    dataType: 'json' // Aseguramos que la respuesta sea JSON
  })
  .done(function(resp) {
    console.log("Respuesta del servidor:", resp);

    if (resp === 1) {
      Swal.fire("Mensaje de Confirmación", "Prácticas modificadas satisfactoriamente!!!", "success").then(() => {
        tbl_paciente_practica.ajax.reload();  // Recargar tabla
        $("#modal_editar").modal('hide');  // Cerrar modal
      });
    } else if (resp === 0) {
      Swal.fire("Mensaje de Información", "No se modificaron las prácticas porque ya existen", "warning");
    } else {
      Swal.fire("Error", "Hubo un problema en la actualización", "error");
    }
  })
  .fail(function(jqXHR, textStatus, errorThrown) {
    console.error("Error en AJAX:", textStatus, errorThrown);
    console.error("Respuesta del servidor:", jqXHR.responseText);
    Swal.fire("Error", "No se pudo actualizar las prácticas. Inténtalo de nuevo.", "error");
  });
}






//MODAL VER HISTORIAL
$('#tabla_facturas').on('click','.historial_fac',function(){
  var data = tbl_facturas.row($(this).parents('tr')).data();

  if(tbl_facturas.row(this).child.isShown()){
      var data = tbl_facturas.row(this).data();
  }
$("#modal_ver_historial").modal('show');

  document.getElementById('lb_titulo_historial').innerHTML="<b>HISTORIAL DE FACTURA N°:</b> "+data.numero_fact+"";
  document.getElementById('lb_titulo_historial2').innerHTML="<b>OBRA SOCIAL:</b> "+data.obra_social+"";

  listar_historial(data.id_factura);

})
// VISTA DE HISTORIAL
var tbl_historial;
function listar_historial(id) {
  tbl_historial = $("#tabla_ver_historial").DataTable({
      "ordering": false,
      "bLengthChange": true,
      "searching": false,  // Deshabilita la barra de búsqueda
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
      "pageLength": 5,
      "destroy": true,
      "pagingType": 'full_numbers',
      "scrollCollapse": true,
      "responsive": true,
      "async": false,
      "processing": true,
      "ajax": {
          "url": "../controller/facturas/controlador_listar_historial_facturas.php",
          "type": 'POST',
          "data": { id: id },
          "dataSrc": function(json) {
              console.log("Respuesta JSON:", json);
              return json.data;
          }
      },
      "dom": 'Bfrtip', 
      "buttons": [
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA_DE_HISTORIAL",
          title: "LISTA DE HISTORIAL",
          className: 'btn btn-success' 
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA_DE_HISTORIAL",
          title: "LISTA DE HISTORIAL",
          className: 'btn btn-danger'
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE HISTORIAL",
          className: 'btn btn-primary' 
        }
      ],
      "columns": [
          { "data": null, "render": function(data, type, row, meta) { return meta.row + 1; } }, 
          { "data": "USUARIO" },
          
          {
            "data": "estado",
            render: function(data, type, row) {
                if (data == 'PENDIENTE') {
                    return '<span class="badge bg-warning">PENDIENTE</span>';
                }else if (data == 'COBRADA') 
                  {
                  return '<span class="badge bg-success">COBRADA</span>';
                } 
              else if (data == 'FACTURADA') 
                {
                    return '<span class="badge bg-primary">FACTURADA</span>';
                } 
                else if (data == 'REGISTRO DE FACTURA') 
                  {
                      return '<span class="badge bg-dark">REGISTRO DE FACTURA</span>';
                  } 
                else 
                {
                  return '<span class="badge bg-danger">RECHAZADA</span>';

                }
            }
        },        
        { "data": "motivo" },

          { "data": "fecha_formateada" }
      ],
      "language": {
          "emptyTable": "No se encontraron datos", // ✅ Mensaje cuando la tabla está vacía
          "zeroRecords": "No se encontraron resultados", // ✅ Mensaje para búsquedas sin coincidencias
          "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
          "infoEmpty": "Mostrando 0 a 0 de 0 registros",
          "infoFiltered": "(filtrado de _MAX_ registros en total)",
          "lengthMenu": "Mostrar _MENU_ registros",
          "loadingRecords": "Cargando...",
          "processing": "Procesando...",
          "search": "Buscar:",
          "paginate": {
              "first": "Primero",
              "last": "Último",
              "next": "Siguiente",
              "previous": "Anterior"
          }
      },
      "select": true
  });
}



function Modificar_Practica_paciente() {
  let count = $("#tabla_detalle_factura_editar tbody#tbody_tabla_practica_editar tr").length;
  if (count === 0) {
      return Swal.fire("Mensaje de Advertencia", "La tabla de prácticas - paciente debe tener al menos un registro", "warning");
  }

  // DATOS DEL DOCENTE
  let idfactu = document.getElementById('id_factura').value;
  let total = parseFloat(document.getElementById('lbl_totalneto1_editar').textContent.replace(/[^0-9.]/g, '')) || 0;
  let facturaactu = document.getElementById('facturaactual').value;
  let factura = document.getElementById('txt_factura_editar').value;
  let notacre = document.getElementById('txt_notacre_editar').value;
  let notacreactu = document.getElementById('notaactual').value;
  let fecha = document.getElementById('txt_fecha_nota_editar').value;
  let idusu = document.getElementById('txtprincipalid').value;

  if (idfactu.length == 0 || total == 0) {
      return Swal.fire("Mensaje de Advertencia", "Tiene campos vacíos en el formulario, revise", "warning");
  }

  // Obtener la fecha actual para generar nombres únicos
  let f = new Date();

  // Procesar Factura
  let nombrefactura = "";
  if (factura.length > 0) {
      let extensionFact = factura.split('.').pop();
      nombrefactura = "FAC" + f.getDate() + "-" + (f.getMonth() + 1) + "-" + f.getFullYear() + "-" + f.getHours() + "-" + f.getMilliseconds() + "." + extensionFact;
  }

  // Procesar Nota de Crédito
  let nombrenotacre = "";
  if (notacre.length > 0) {
      let extensionNotacre = notacre.split('.').pop();
      nombrenotacre = "NC" + f.getDate() + "-" + (f.getMonth() + 1) + "-" + f.getFullYear() + "-" + f.getHours() + "-" + f.getMilliseconds() + "." + extensionNotacre;
  }

  // Crear FormData
  let formData = new FormData();
  let facturaObj = $("#txt_factura_editar")[0].files[0]; // Obtener el archivo de factura
  let notacreObj = $("#txt_notacre_editar")[0].files[0]; // Obtener el archivo de nota de crédito

  if (facturaObj) {
      formData.append("factura", facturaObj, nombrefactura);
  }
  if (notacreObj) {
      formData.append("notacre", notacreObj, nombrenotacre);
  }

  // Agregar otros datos al FormData
  formData.append("idfactu", idfactu);
  formData.append("total", total);
  formData.append("facturaactu", facturaactu);
  formData.append("nombrefactura", nombrefactura);
  formData.append("facturaObj", facturaObj);
  formData.append("notacreactu", notacreactu);
  formData.append("nombrenotacre", nombrenotacre);
  formData.append("notacreObj", notacreObj);
  formData.append("fecha", fecha);
  formData.append("idusu", idusu);

  $.ajax({
      url: "../controller/facturas/controlador_modificar_factura.php",
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (resp) {
          if (resp.length > 0) {
              // Verificar si hay datos en la tabla antes de llamar a Modificar_detalle_facturas
              if ($("#tabla_detalle_factura_editar tbody tr").length > 0) {
                  Modificar_detalle_facturas();
              } else {
                  Swal.fire("Mensaje de Confirmación", "La factura se modificó correctamente.", "success").then(() => {
                      tbl_facturas.ajax.reload(); // Recargar tabla
                      $("#modal_editar").modal('hide'); // Cerrar modal
                  });
              }
          } else {
              Swal.fire("Mensaje de Advertencia", "No se pudo registrar la factura", "warning");
          }
      }
  });
}

function Modificar_detalle_facturas() {
  let componentes = [];

  let totalText = document.getElementById('lbl_totalneto1_editar')?.textContent.replace(/[^0-9.]/g, '').trim();
  let total = parseFloat(totalText) || 0;
  let idusu = document.getElementById('txtprincipalid')?.value.trim();
  let nro = document.getElementById('txt_nro_factura_editar')?.value.trim();
  let id_Fac = document.getElementById('id_factura')?.value.trim();

  if (!idusu) {
      return Swal.fire("Mensaje de Advertencia", "El ID del usuario no es válido.", "warning");
  }

  $("#tabla_detalle_factura_editar tbody tr").each(function () {
      let id_factura = $(this).find('td').eq(0).text().trim();
      let id_practica = $(this).find('td').eq(1).text().trim();
      let precioText = $(this).find('td').eq(3).text().trim();
      let subtotal = parseFloat(precioText) || 0;

      if (id_practica && subtotal && id_factura > 0) {
          componentes.push({
              id_factura,
              id_practica,
              subtotal: subtotal.toFixed(2)
          });
      }
  });

  $.ajax({
      url: '../controller/facturas/controlador_modificar_detalle_facturas.php',
      type: 'POST',
      data: {
          total: total.toFixed(2),
          idusu: idusu,
          id_Fac:id_Fac,
          componentes: JSON.stringify(componentes) // Se envía vacío si no hay datos
      },
      dataType: 'json'
  })
  .done(function (resp) {
      console.log("Respuesta del servidor:", resp);
      if (resp.status === 1) {
          Swal.fire("Mensaje de Confirmación", "Se actualizó correctamente la factura con el N° <b>" + nro + "</b>", "success").then(() => {
              tbl_facturas.ajax.reload();
              $("#modal_editar").modal('hide');
          });
      } else if (resp.status === 2) {
          Swal.fire("Mensaje de Información", "No se modificaron las prácticas porque ya existen o no había registros nuevos- solo se modifico la factura", "success");
          tbl_facturas.ajax.reload();
          $("#modal_editar").modal('hide');

      } else {
          Swal.fire("Error", "Hubo un problema en la actualización", "error");
      }
  })
  .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Error en AJAX:", textStatus, errorThrown);
      console.error("Respuesta del servidor:", jqXHR.responseText);
      Swal.fire("Error", "No se pudo actualizar las prácticas. Inténtalo de nuevo.", "error");
  });
}


//FUNCIONALIDADES PARA PAGO

$('#tabla_facturas').on('click', '.pagar', function () {
  var data = tbl_facturas.row($(this).parents('tr')).data();

  if (tbl_facturas.row(this).child.isShown()) {
      data = tbl_facturas.row(this).data();
  }

  // Mostrar el modal
  $("#modal_pagar").modal('show');

  // Esperar a que el modal termine de abrirse para enfocar el campo
  $("#modal_pagar").on('shown.bs.modal', function () {
      $('#txt_pagar').trigger('focus');
  });

  // Actualizar los valores en el modal
  document.getElementById('lb_tituloesta_pagar').innerHTML = "<b>FACTURA N°:</b> " + data.numero_fact;
  document.getElementById('lb_titulo2esta_pagar').innerHTML = "<b>OBRA SOCIAL:</b> " + data.obra_social;
  document.getElementById('id_pago').value = data.id_factura;
  document.getElementById('txt_total').value = data.saldo_pendiente;
});


//PAGAR FACTURA
function Realizar_pago() {
  let id = document.getElementById('id_pago').value;
  let total = parseFloat(document.getElementById('txt_total').value) || 0;
  let pagar = parseFloat(document.getElementById('txt_pagar').value) || 0;
  let saldo = parseFloat(document.getElementById('txt_saldo').value) || 0;
  let idusu = document.getElementById('txtprincipalid').value;

  if (isNaN(pagar) || pagar <= 0) {
      return Swal.fire("Mensaje de Advertencia", "Ingrese un monto válido a pagar", "warning");
  }

  if (pagar > total) {
      return Swal.fire("Mensaje de Advertencia", "El monto a pagar no puede ser mayor al total", "warning");
  }

  $.ajax({
      url: "../controller/facturas/controlador_realizar_pago.php",
      type: 'POST',
      data: {
          id: id,
          pagar: pagar,
          saldo: saldo,
          idusu: idusu
      }
  }).done(function(resp) {
      if (resp > 0) {
          Swal.fire("Mensaje de Confirmación", "Se realizó correctamente el pago con el monto de: AR$" + pagar, "success").then((value) => {
              tbl_facturas.ajax.reload();
              $("#modal_pagar").modal('hide');
          });
      } else {
          return Swal.fire("Mensaje de Error", "No se completó el pago", "error");
      }
  });
}


//MODAL VER PAGOS
$('#tabla_facturas').on('click','.historial_pagos',function(){
  var data = tbl_facturas.row($(this).parents('tr')).data();

  if(tbl_facturas.row(this).child.isShown()){
      var data = tbl_facturas.row(this).data();
  }
$("#modal_ver_pagos").modal('show');

document.getElementById('lb_tituloesta_history').innerHTML = "<b>FACTURA N°:</b> " + data.numero_fact;
document.getElementById('lb_titulo2esta_history').innerHTML = "<b>OBRA SOCIAL:</b> " + data.obra_social;
listar_pagos(data.id_factura);

})
// VISTA DE HISTORIAL
var tbl_pagos;
function listar_pagos(id) {
  tbl_pagos = $("#tabla_ver_pagos").DataTable({
      "ordering": false,
      "bLengthChange": true,
      "searching": false,  // Deshabilita la barra de búsqueda
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
      "pageLength": 5,
      "destroy": true,
      "pagingType": 'full_numbers',
      "scrollCollapse": true,
      "responsive": true,
      "async": false,
      "processing": true,
      "ajax": {
          "url": "../controller/facturas/controlador_listar_pagos.php",
          "type": 'POST',
          "data": { id: id },
          "dataSrc": function(json) {
              console.log("Respuesta JSON:", json);
              return json.data;
          }
      },
      "dom": 'Bfrtip', 
      "buttons": [
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA_DE_HISTORIAL_DE_PAGOS",
          title: "LISTA DE HISTORIAL DE PAGOS",
          className: 'btn btn-success' 
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA_DE_HISTORIAL_DE_PAGOS",
          title: "LISTA DE HISTORIAL DE PAGOS",
          className: 'btn btn-danger'
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE HISTORIAL DE PAGOS",
          className: 'btn btn-primary' 
        }
      ],
      "columns": [
          { "data": null, "render": function(data, type, row, meta) { return meta.row + 1; } }, 
          { "data": "USUARIO" },
          {
            "data": "monto_pagado",
            "render": function (data, type, row) {
              return `<strong>$AR ${data}</strong>`;
            }
          },
          { "data": "fecha_formateada" },
          {"data":"estado",
            render: function(data,type,row){
                    if(data=='VALIDO'){
                    return '<span class="badge bg-success">VALIDO</span>';
                    }else{
                    return '<span class="badge bg-danger">ANULADO</span>';
                    }
            }   
        },
          {
            "data": "estado",
            render: function(data, type, row) {
                if (data == 'VALIDO') {
                    return  `
                   <button class='anular btn btn-danger btn-sm' title='Anular el pago'><i class='fa fa-ban'></i> Anular</button>
                    <button class='ver_anulado btn btn-warning btn-sm' hidden title='Ver motivo'><i class='fa fa-eye'></i> Ver Anulado</button>
                      `;
              } else {
                return  `
                <button class='anular btn btn-danger btn-sm' hidden title='Anular el pago'><i class='fa fa-ban'></i> Anular</button>
                    <button class='ver_anulado btn btn-warning btn-sm' title='Ver motivo'><i class='fa fa-eye'></i> Ver Anulado</button>
                   `;
                }
            }
        },        
      ],
      "language": {
          "emptyTable": "No se encontraron datos", // ✅ Mensaje cuando la tabla está vacía
          "zeroRecords": "No se encontraron resultados", // ✅ Mensaje para búsquedas sin coincidencias
          "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
          "infoEmpty": "Mostrando 0 a 0 de 0 registros",
          "infoFiltered": "(filtrado de _MAX_ registros en total)",
          "lengthMenu": "Mostrar _MENU_ registros",
          "loadingRecords": "Cargando...",
          "processing": "Procesando...",
          "search": "Buscar:",
          "paginate": {
              "first": "Primero",
              "last": "Último",
              "next": "Siguiente",
              "previous": "Anterior"
          }
      },
      "select": true
  });
}


//ANULAR PAGO
function Anular_pago(id, motivo, monto_anulado) {
  let idusu = document.getElementById('txtprincipalid').value;



  $.ajax({
      url: "../controller/facturas/controlador_anular_pago.php",
      type: 'POST',
      data: {
          id: id,
          idusu: idusu,
          motivo_anulacion: motivo,  // Enviamos el motivo de anulación
          monto_anulado: monto_anulado       // Enviamos el monto anulado
      }
  }).done(function(resp) {
      if (resp > 0) {
          Swal.fire("Mensaje de Confirmación", "Se anuló el pago con éxito", "success").then(() => {
              tbl_pagos.ajax.reload();
              tbl_facturas.ajax.reload();
          });
      } else {
          return Swal.fire("Mensaje de Advertencia", "No se puede anular el pago, verifique por favor", "warning");
      }
  });
}

// ENVIANDO AL BOTÓN DELETE
$('#tabla_ver_pagos').on('click', '.anular', function() {
  var data = tbl_pagos.row($(this).parents('tr')).data();

  if (tbl_pagos.row(this).child.isShown()) {
      var data = tbl_pagos.row(this).data();
  }

  Swal.fire({
      title: '¿Desea anular el pago de <b style="color:blue">AR$ ' + data.monto_pagado + '</b>?',
      html: "<p>Por favor, ingrese el motivo de la anulación antes de confirmar.</p>" +
            "<p style='color:red; font-weight: bold;'>⚠️ Al anular este pago:</p>" +
            "<ul style='text-align: left; color: red; font-weight: bold;'>" +
            "<li>El monto será sumado al <b>Saldo pendiente</b>.</li>" +
            "<li>El monto será restado del <b>Saldo cobrado</b>.</li>" +
            "</ul>",
      icon: 'warning',
      input: 'text',
      inputPlaceholder: 'Escriba el motivo de la anulación...',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, Anular',
      preConfirm: (motivo) => {
          if (!motivo) {
              Swal.showValidationMessage("El motivo de anulación es obligatorio");
          }
          return motivo;
      }
  }).then((result) => {
      if (result.isConfirmed) {
          let motivoAnulacion = result.value;
          Anular_pago(data.id_historial_pagos, motivoAnulacion, data.monto_pagado);
      }
  });
});


$('#tabla_ver_pagos').on('click','.ver_anulado',function(){
  var data = tbl_pagos.row($(this).parents('tr')).data();

  if(tbl_pagos.row(this).child.isShown()){
      var data = tbl_pagos.row(this).data();
  }
$("#modal_ver_anulado").modal('show');

document.getElementById('txt_motivo2').value = data.motivo_anulacion;
document.getElementById('txt_fecha_anulado2').value = data.fecha_anula;

})