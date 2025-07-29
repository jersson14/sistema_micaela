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
          "url":"../controller/facturas/controlador_listar_facturas_archivadas.php",
          type:'POST'
      },
      dom: 'Bfrtip', 
       
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE FACTURAS ARCHIVADAS",
          title: "LISTA DE FACTURAS ARCHIVADAS",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE FACTURAS ARCHIVADAS",
          title: "LISTA DE FACTURAS ARCHIVADAS",
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
          title: "LISTA DE FACTURAS ARCHIVADAS",
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
              if (data == 'ELIMINADA') {
                  return '<span class="badge bg-danger">ARCHIVADO</span>';
              } 
          }
      },        

      {
        "data": "estado_fact",
        render: function (data, type, row) {
            if (data == 'ELIMINADA') {
                return `
                <button class='historial_fac btn btn-dark btn-sm' title='ver historial'>
                  <i class="fa fa-history"></i> Historial
                </button>

                <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                </button>
            `;            
          } 
          
        }
    }
    
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
          "url":"../controller/facturas/controlador_listar_facturas_total_archivados.php",
          type:'POST'
      },
      dom: 'Bfrtip', 
     
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE FACTURAS ARCHIVADAS",
          title: "LISTA DE FACTURAS ARCHIVADAS",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE FACTURAS ARCHIVADAS",
          title: "LISTA DE FACTURAS ARCHIVADAS",
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
          title: "LISTA DE FACTURAS ARCHIVADAS",
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
              if (data == 'ELIMINADA') {
                  return '<span class="badge bg-danger">ARCHIVADO</span>';
              } 
          }
      },        

      {
        "data": "estado_fact",
        render: function (data, type, row) {
            if (data == 'ELIMINADA') {
                return `
                <button class='historial_fac btn btn-dark btn-sm' title='ver historial'>
                  <i class="fa fa-history"></i> Historial
                </button>

                <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                </button>
            `;            
          } 
          
        }
    }
    
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
          "url":"../controller/facturas/controlador_listar_facturas_obra_estado_archivado.php",
          type:'POST',
          data:{
            obra:obra,
          }
      },
      dom: 'Bfrtip', 
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE FACTURAS ARCHIVADAS",
          title: "LISTA DE FACTURAS ARCHIVADAS",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE FACTURAS ARCHIVADAS",
          title: "LISTA DE FACTURAS ARCHIVADAS",
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
          title: "LISTA DE FACTURAS ARCHIVADAS",
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
              if (data == 'ELIMINADA') {
                  return '<span class="badge bg-danger">ARCHIVADO</span>';
              } 
          }
      },        

      {
        "data": "estado_fact",
        render: function (data, type, row) {
            if (data == 'ELIMINADA') {
                return `
                <button class='historial_fac btn btn-dark btn-sm' title='ver historial'>
                  <i class="fa fa-history"></i> Historial
                </button>

                <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                </button>
            `;            
          } 
          
        }
    }
    
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
          "url":"../controller/facturas/controlador_listar_facturas_fecha_usu_archivado.php",
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
          filename: "LISTA DE FACTURAS ARCHIVADAS",
          title: "LISTA DE FACTURAS ARCHIVADAS",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE FACTURAS ARCHIVADAS",
          title: "LISTA DE FACTURAS ARCHIVADAS",
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
          title: "LISTA DE FACTURAS ARCHIVADAS",
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
              if (data == 'ELIMINADA') {
                  return '<span class="badge bg-danger">ARCHIVADO</span>';
              } 
          }
      },        

      {
        "data": "estado_fact",
        render: function (data, type, row) {
            if (data == 'ELIMINADA') {
                return `
                <button class='historial_fac btn btn-dark btn-sm' title='ver historial'>
                  <i class="fa fa-history"></i> Historial
                </button>

                <button class='mostrar btn btn-success btn-sm' title='Mostrar detalle de la factura'>
                    <i class='fa fa-eye'></i> Ver Detalles
                </button>
            `;            
          } 
          
        }
    }
    
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





   

//MODAL VER PRÁCTICAS
$('#tabla_facturas').on('click','.mostrar',function(){
  var data = tbl_facturas.row($(this).parents('tr')).data();

  if(tbl_facturas.row(this).child.isShown()){
      var data = tbl_facturas.row(this).data();
  }
$("#modal_ver_facturas_paci").modal('show');

document.getElementById('lb_titulo_facturas').innerHTML="<b>FACTURA N°:</b> "+data.numero_fact+"";
document.getElementById('lb_titulo2_facturas').innerHTML="<b>OBRA SOCIAL:</b> "+data.obra_social+"";
document.getElementById('lb_titulo3_facturas').innerHTML="<b style='color:red'>FECHA DE ARCHIVAMIENTO:</b> "+data.fecha_formateada+"";
document.getElementById('lb_titulo4_facturas').innerHTML="<b style='color:red'>USUARIO QUE ARCHIVO:</b> "+data.USUARIO+"";

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
                else if (data == 'ELIMINADA') 
                {
                    return '<span class="badge bg-danger">ARCHIVADA</span>';
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
