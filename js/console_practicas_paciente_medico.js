//LISTAR TODOS
var tbl_paciente_practica;
function listar_practica_paciente_diario(){
  let idusu = document.getElementById('txtprincipalid').value;
  tbl_paciente_practica = $("#tabla_paciente_practica").DataTable({
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
          "url":"../controller/practicas_paciente/controlador_listar_practicas_paciente_diario_medico.php",
          type:'POST',
          data:{
            idusu:idusu
          }
      },
      dom: 'Bfrtip', 
     
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          title: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          title: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          className: 'btn btn-pdf',
          orientation: 'landscape', // <-- Establece la orientación en horizontal
          pageSize: 'A4', // <-- Especifica el tamaño de la página
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      "columns":[
        {"defaultContent":""},
        {"data":"obra_social"},
        {"data":"Dni"},
        {"data":"PACIENTE"},
  
        {"data":"fecha_formateada"},
        {"data":"fecha_formateada2"},
        {"data":"USUARIO"},
        {
          "data": "estado",
          render: function(data, type, row) {
              if (data == 'PENDIENTE') {
                  return '<span class="badge bg-warning">PENDIENTE</span>';
              
            } else {
              return ` <span class="badge bg-success">COMPLETO</span>`;

              }
          }
      },        
      {"data":"historia_clinica",
        render: function(data,type,row){
                if(data=='' || data=='controller/practicas_paciente/filepracticas/'){
                    return "<button class='btn btn-danger btn-sm' disabled title='Ver archivo'><i class='fa fa-file-pdf'></i></button>";
                }else{
                  return "<a class='btn btn-success btn-sm' href='../"+data+"' target='_blank' title='Ver H.C.'><i class='fas fa-eye'></i> Ver Historia Clínica</a>";
                }
            }   
        },    


       {"data":"tiene_factura",
          render: function(data,type,row){
              if(row.tiene_factura==0 && row.estado=='PENDIENTE'){
              return `
                     <button class='adjuntar btn btn-dark btn-sm' title='Adjuntar archivo de H.C.'>
  <i class='fa fa-upload'></i> Subir HC
</button>

              <button class="mostrar btn btn-success btn-sm" title="Mostrar prácticas del paciente">
                <i class="fa fa-eye"></i> Ver prácticas
              </button>
              <button class="editar btn btn-primary btn-sm" title="Editar datos de la práctica">
                <i class="fa fa-edit"></i> Editar
              </button>
              <button class="eliminar btn btn-danger btn-sm" title="Eliminar práctica">
                <i class="fa fa-trash"></i> Eliminar
              </button>
            `;
          }else if(row.tiene_factura==0 && row.estado=='COMPLETADO'){
            return `
                                                   <button class='adjuntar btn btn-warning btn-sm' title='Adjuntar archivo de H.C.'>
  <i class='fa fa-upload'></i> Editar HC
</button>

              <button class="mostrar btn btn-success btn-sm" title="Mostrar prácticas del paciente">
                <i class="fa fa-eye"></i> Ver prácticas
              </button>
              <button class="editar btn btn-primary btn-sm" title="Editar datos de la práctica">
                <i class="fa fa-edit"></i> Editar
              </button>
             <button class="eliminar btn btn-danger btn-sm" title="Eliminar práctica">
                <i class="fa fa-trash"></i> Eliminar
              </button>
            `;
              }else{
                return `
                <button class='adjuntar btn btn-warning btn-sm' title='Adjuntar archivo de H.C.'>
<i class='fa fa-upload'></i> Editar HC
</button>

<button class="mostrar btn btn-success btn-sm" title="Mostrar prácticas del paciente">
<i class="fa fa-eye"></i> Ver prácticas
</button>
<button class="editar btn btn-primary btn-sm" title="Editar datos de la práctica">
<i class="fa fa-edit"></i> Editar
</button>

`;
              }
      }
      },
                
    ],

    "language":idioma_espanol,
    select: true
});
tbl_paciente_practica.on('draw.td',function(){
  var PageInfo = $("#tabla_paciente_practica").DataTable().page.info();
  tbl_paciente_practica.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}

function listar_practica_paciente(){
  Cargar_Select_Obras_Sociales();
  document.getElementById('txt_fecha_desde').value='';
  document.getElementById('txt_fecha_hasta').value='';
  let idusu = document.getElementById('txtprincipalid').value;

  tbl_paciente_practica = $("#tabla_paciente_practica").DataTable({
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
          "url":"../controller/practicas_paciente/controlador_listar_practicas_paciente_medico.php",
          type:'POST',
          data:{
            idusu:idusu
          }
      },
      dom: 'Bfrtip', 
     
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          title: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          title: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          className: 'btn btn-pdf',
          orientation: 'landscape', // <-- Establece la orientación en horizontal
          pageSize: 'A4', // <-- Especifica el tamaño de la página
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      "columns":[
        {"defaultContent":""},
        {"data":"obra_social"},
        {"data":"Dni"},
        {"data":"PACIENTE"},
     
        {"data":"fecha_formateada"},
        {"data":"fecha_formateada2"},
        {"data":"USUARIO"},


        {
          "data": "estado",
          render: function(data, type, row) {
              if (data == 'PENDIENTE') {
                  return '<span class="badge bg-warning">PENDIENTE</span>';
              
            } else {
              return ` <span class="badge bg-success">COMPLETO</span>`;

              }
          }
      },        
      {"data":"historia_clinica",
        render: function(data,type,row){
                if(data=='' || data=='controller/practicas_paciente/filepracticas/'){
                    return "<button class='btn btn-danger btn-sm' disabled title='Ver archivo'><i class='fa fa-file-pdf'></i></button>";
                }else{
                  return "<a class='btn btn-success btn-sm' href='../"+data+"' target='_blank' title='Ver H.C.'><i class='fas fa-eye'></i> Ver Historia Clínica</a>";
                }
            }   
        },    


        {"data":"tiene_factura",
          render: function(data,type,row){
              if(row.tiene_factura==0 && row.estado=='PENDIENTE'){
              return `
                     <button class='adjuntar btn btn-dark btn-sm' title='Adjuntar archivo de H.C.'>
  <i class='fa fa-upload'></i> Subir HC
</button>

              <button class="mostrar btn btn-success btn-sm" title="Mostrar prácticas del paciente">
                <i class="fa fa-eye"></i> Ver prácticas
              </button>
              <button class="editar btn btn-primary btn-sm" title="Editar datos de la práctica">
                <i class="fa fa-edit"></i> Editar
              </button>
              <button class="eliminar btn btn-danger btn-sm" title="Eliminar práctica">
                <i class="fa fa-trash"></i> Eliminar
              </button>
            `;
          }else if(row.tiene_factura==0 && row.estado=='COMPLETADO'){
            return `
                                                   <button class='adjuntar btn btn-warning btn-sm' title='Adjuntar archivo de H.C.'>
  <i class='fa fa-upload'></i> Editar HC
</button>

              <button class="mostrar btn btn-success btn-sm" title="Mostrar prácticas del paciente">
                <i class="fa fa-eye"></i> Ver prácticas
              </button>
              <button class="editar btn btn-primary btn-sm" title="Editar datos de la práctica">
                <i class="fa fa-edit"></i> Editar
              </button>
             <button class="eliminar btn btn-danger btn-sm" title="Eliminar práctica">
                <i class="fa fa-trash"></i> Eliminar
              </button>
            `;
              }else{
                return `
                <button class='adjuntar btn btn-warning btn-sm' title='Adjuntar archivo de H.C.'>
<i class='fa fa-upload'></i> Editar HC
</button>

<button class="mostrar btn btn-success btn-sm" title="Mostrar prácticas del paciente">
<i class="fa fa-eye"></i> Ver prácticas
</button>
<button class="editar btn btn-primary btn-sm" title="Editar datos de la práctica">
<i class="fa fa-edit"></i> Editar
</button>

`;
              }
      }
      },
                
    ],

    "language":idioma_espanol,
    select: true
});
tbl_paciente_practica.on('draw.td',function(){
  var PageInfo = $("#tabla_paciente_practica").DataTable().page.info();
  tbl_paciente_practica.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}

//LISTAR POR OBRAS SOCIALES
function listar_practica_paciente_obras(){
  let obra = document.getElementById('select_obras_buscar').value;
  let idusu = document.getElementById('txtprincipalid').value;

  tbl_paciente_practica = $("#tabla_paciente_practica").DataTable({
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
          "url":"../controller/practicas_paciente/controlador_listar_practicas_paciente_obras_medico.php",
          type:'POST',
          data:{
            obra:obra,
            idusu:idusu
          }
      },
      dom: 'Bfrtip', 
     
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          title: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          title: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          className: 'btn btn-pdf',
          orientation: 'landscape', // <-- Establece la orientación en horizontal
          pageSize: 'A4', // <-- Especifica el tamaño de la página
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      "columns":[
        {"defaultContent":""},
        {"data":"obra_social"},
        {"data":"Dni"},
        {"data":"PACIENTE"},
    
        {"data":"fecha_formateada"},
        {"data":"fecha_formateada2"},
        {"data":"USUARIO"},


        {
          "data": "estado",
          render: function(data, type, row) {
              if (data == 'PENDIENTE') {
                  return '<span class="badge bg-warning">PENDIENTE</span>';
              
            } else {
              return ` <span class="badge bg-success">COMPLETO</span>`;

              }
          }
      },        
      {"data":"historia_clinica",
        render: function(data,type,row){
                if(data=='' || data=='controller/practicas_paciente/filepracticas/'){
                    return "<button class='btn btn-danger btn-sm' disabled title='Ver archivo'><i class='fa fa-file-pdf'></i></button>";
                }else{
                  return "<a class='btn btn-success btn-sm' href='../"+data+"' target='_blank' title='Ver H.C.'><i class='fas fa-eye'></i> Ver Historia Clínica</a>";
                }
            }   
        },    


        {"data":"tiene_factura",
          render: function(data,type,row){
            if(row.tiene_factura==0 && row.estado=='PENDIENTE'){
              return `
                     <button class='adjuntar btn btn-dark btn-sm' title='Adjuntar archivo de H.C.'>
  <i class='fa fa-upload'></i> Subir HC
</button>

              <button class="mostrar btn btn-success btn-sm" title="Mostrar prácticas del paciente">
                <i class="fa fa-eye"></i> Ver prácticas
              </button>
              <button class="editar btn btn-primary btn-sm" title="Editar datos de la práctica">
                <i class="fa fa-edit"></i> Editar
              </button>
              <button class="eliminar btn btn-danger btn-sm" title="Eliminar práctica">
                <i class="fa fa-trash"></i> Eliminar
              </button>
            `;
          }else if(row.tiene_factura==0 && row.estado=='COMPLETADO'){
            return `
                                                   <button class='adjuntar btn btn-warning btn-sm' title='Adjuntar archivo de H.C.'>
  <i class='fa fa-upload'></i> Editar HC
</button>

              <button class="mostrar btn btn-success btn-sm" title="Mostrar prácticas del paciente">
                <i class="fa fa-eye"></i> Ver prácticas
              </button>
              <button class="editar btn btn-primary btn-sm" title="Editar datos de la práctica">
                <i class="fa fa-edit"></i> Editar
              </button>
                    <button class="eliminar btn btn-danger btn-sm" title="Eliminar práctica">
                <i class="fa fa-trash"></i> Eliminar
              </button>

            `;
              }else{
                return `
                <button class='adjuntar btn btn-warning btn-sm' title='Adjuntar archivo de H.C.'>
<i class='fa fa-upload'></i> Editar HC
</button>

<button class="mostrar btn btn-success btn-sm" title="Mostrar prácticas del paciente">
<i class="fa fa-eye"></i> Ver prácticas
</button>
<button class="editar btn btn-primary btn-sm" title="Editar datos de la práctica">
<i class="fa fa-edit"></i> Editar
</button>

`;
              }
      }
      },
    ],

    "language":idioma_espanol,
    select: true
});
tbl_paciente_practica.on('draw.td',function(){
  var PageInfo = $("#tabla_paciente_practica").DataTable().page.info();
  tbl_paciente_practica.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}

//LISTAR POR FECHAS Y USUARIO
function listar_practica_paciente_fecha_usu(){
  let fechaini = document.getElementById('txt_fecha_desde').value;
  let fechafin = document.getElementById('txt_fecha_hasta').value;
  let idusu = document.getElementById('txtprincipalid').value;

  tbl_paciente_practica = $("#tabla_paciente_practica").DataTable({
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
          "url":"../controller/practicas_paciente/controlador_listar_practicas_paciente_fecha_usu_medico.php",
          type:'POST',
          data:{
            fechaini:fechaini,
            fechafin:fechafin,
            idusu:idusu
          }
      },
      dom: 'Bfrtip', 
     
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          title: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          title: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          className: 'btn btn-pdf',
          orientation: 'landscape', // <-- Establece la orientación en horizontal
          pageSize: 'A4', // <-- Especifica el tamaño de la página
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE PRÁCTICAS REALIZADAS A PACIENTES",
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      "columns":[
        {"defaultContent":""},
        {"data":"obra_social"},
        {"data":"Dni"},
        {"data":"PACIENTE"},
     
        {"data":"fecha_formateada"},
        {"data":"fecha_formateada2"},
        {"data":"USUARIO"},
        {
          "data": "estado",
          render: function(data, type, row) {
              if (data == 'PENDIENTE') {
                  return '<span class="badge bg-warning">PENDIENTE</span>';
              
            } else {
              return ` <span class="badge bg-success">COMPLETO</span>`;

              }
          }
      },        
      {"data":"historia_clinica",
        render: function(data,type,row){
                if(data=='' || data=='controller/practicas_paciente/filepracticas/'){
                    return "<button class='btn btn-danger btn-sm' disabled title='Ver archivo'><i class='fa fa-file-pdf'></i></button>";
                }else{
                  return "<a class='btn btn-success btn-sm' href='../"+data+"' target='_blank' title='Ver H.C.'><i class='fas fa-eye'></i> Ver Historia Clínica</a>";
                }
            }   
        },    


        {"data":"tiene_factura",
          render: function(data,type,row){
              if(row.tiene_factura==0 || row.estado=='PENDIENTE'){
              return `
                     <button class='adjuntar btn btn-dark btn-sm' title='Adjuntar archivo de H.C.'>
  <i class='fa fa-upload'></i> Subir HC
</button>

              <button class="mostrar btn btn-success btn-sm" title="Mostrar prácticas del paciente">
                <i class="fa fa-eye"></i> Ver prácticas
              </button>
              <button class="editar btn btn-primary btn-sm" title="Editar datos de la práctica">
                <i class="fa fa-edit"></i> Editar
              </button>
              <button class="eliminar btn btn-danger btn-sm" title="Eliminar práctica">
                <i class="fa fa-trash"></i> Eliminar
              </button>
            `;
              }else if(row.tiene_factura==0 && row.estado=='COMPLETADO'){
              return `
                                                   <button class='adjuntar btn btn-warning btn-sm' title='Adjuntar archivo de H.C.'>
  <i class='fa fa-upload'></i> Editar HC
</button>

              <button class="mostrar btn btn-success btn-sm" title="Mostrar prácticas del paciente">
                <i class="fa fa-eye"></i> Ver prácticas
              </button>
              <button class="editar btn btn-primary btn-sm" title="Editar datos de la práctica">
                <i class="fa fa-edit"></i> Editar
              </button>
             <button class="eliminar btn btn-danger btn-sm" title="Eliminar práctica">
                <i class="fa fa-trash"></i> Eliminar
              </button>
            `;
              }else{
                return `
                <button class='adjuntar btn btn-warning btn-sm' title='Adjuntar archivo de H.C.'>
<i class='fa fa-upload'></i> Editar HC
</button>

<button class="mostrar btn btn-success btn-sm" title="Mostrar prácticas del paciente">
<i class="fa fa-eye"></i> Ver prácticas
</button>
<button class="editar btn btn-primary btn-sm" title="Editar datos de la práctica">
<i class="fa fa-edit"></i> Editar
</button>

`;
              }
      }
      },

   
                
    ],

    "language":idioma_espanol,
    select: true
});
tbl_paciente_practica.on('draw.td',function(){
  var PageInfo = $("#tabla_paciente_practica").DataTable().page.info();
  tbl_paciente_practica.column(0, {page: 'current'}).nodes().each(function(cell, i){
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

$('#modal_registro').on('shown.bs.modal', function() {
  $('#select_area').select2({
    placeholder: "Seleccionar Área",
    allowClear: true,
      dropdownParent: $('#modal_registro')
  });
});

$('#modal_editar').on('shown.bs.modal', function() {
  $('#select_area_editar').select2({
    placeholder: "Seleccionar Área",
    allowClear: true,
      dropdownParent: $('#modal_editar')
  });
});




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
    $('#select_obras_buscar,#txt_obras_sociales').html(cadena);


    // Inicializar Select2 después de cargar opciones
    $('#select_obras_buscar,#txt_obras_sociales').select2({
      placeholder: "Seleccionar Obra Social",
      allowClear: true,
      width: '100%' // Asegura que use todo el ancho
    });
  });
} 
$('#modal_registro2').on('shown.bs.modal', function() {
  $('#txt_obras_sociales').select2({
      placeholder: "Seleccionar Obra Social",
      allowClear: true,
      dropdownParent: $('#modal_registro2')
  });
});

function Cargar_Select_Obras_Sociales2() {
  $.ajax({
    url: "../controller/obras_sociales/controlador_cargar_select_obras_sociales.php",
    type: 'POST'
  }).done(function(resp) {
    let data = JSON.parse(resp);
    cadena = "<option value='' disabled selected>Seleccione Obra Social</option>"; // Placeholder por defecto
    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) {
        cadena += "<option value='" + data[i][0] + "'>CUIT: " + data[i][1] + " - Nombre: " + data[i][2] + "</option>";
      }
      $('#select_obras, #select_obras_editar').html(cadena);
      
      // Cargar los pacientes correspondientes a la obra seleccionada por defecto
      var id_pacient = $("#select_paciente").val();
      Cargar_Select_Paciente(id_pacient, 'select_paciente');

   
      // Cargar las prácticas correspondientes a la obra seleccionada por defecto
      var id_practica = $("#select_practica").val();
      Cargar_Select_Practica(id_practica, 'select_practica');

      
    } else {
      $('#select_obras, #select_obras_editar').html(cadena);
    }
    $('#select_obras').select2({
      placeholder: "Seleccionar Obra Social",
      allowClear: true,
      width: '100%' // Asegura que use todo el ancho
    });
  });
}

$('#modal_registro').on('shown.bs.modal', function() {
  $('#select_obras').select2({
    placeholder: "Seleccionar Obra Social",
    allowClear: true,
      dropdownParent: $('#modal_registro')
  });
});

$('#modal_editar').on('shown.bs.modal', function() {
  $('#select_obras_editar').select2({
    placeholder: "Seleccionar Obra Social",
    allowClear: true,
      dropdownParent: $('#modal_editar')
  });
});

// Función para cargar pacientes
function Cargar_Select_Paciente(id) {
  console.log("Cargando pacientes para obra social ID:", id); // Debug
  
  // Limpiar el select de pacientes antes de la nueva carga
  $("#select_paciente").empty().trigger('change');
  
  $.ajax({
      url: "../controller/practicas_paciente/controlador_cargar_select_paciente_practica.php",
      type: 'POST',
      data: {
          id: id
      },
      beforeSend: function() {
          console.log("Enviando solicitud de pacientes..."); // Debug
      },
      success: function(response) {
          console.log("Respuesta recibida:", response); // Debug
          
          try {
              let data = JSON.parse(response);
              let cadena = "<option value=''>Seleccione un paciente</option>";
              
              if (data && data.length > 0) {
                  data.forEach(function(item) {
                      cadena += `<option value="${item[0]}">DNI: ${item[1]} - ${item[2]}</option>`;
                  });
                  console.log("Pacientes cargados exitosamente"); // Debug
              } else {
                  cadena = "<option value=''>No hay pacientes disponibles</option>";
                  console.log("No se encontraron pacientes"); // Debug
              }
              
              // Actualizar el select y reinicializar
              $("#select_paciente")
                  .html(cadena)
                  .select2({
                      dropdownParent: $("#modal_registro"),
                      placeholder: "Seleccione un paciente",
                      allowClear: true,
                      width: '100%'
                  });
              
          } catch (error) {
              console.error("Error al procesar datos de pacientes:", error);
              $("#select_paciente").html("<option value=''>Error al cargar pacientes</option>");
          }
      },
      error: function(xhr, status, error) {
          console.error("Error en la petición AJAX de pacientes:", error);
          $("#select_paciente").html("<option value=''>Error al cargar pacientes</option>");
      }
  });
}

// Función para cargar prácticas
function Cargar_Select_Practica(id) {
  console.log("Cargando prácticas para obra social ID:", id); // Debug
  
  // Limpiar los selects de prácticas antes de la nueva carga
  $("#select_practica, #select_practica_editar").empty().trigger('change');
  
  $.ajax({
      url: "../controller/practicas_paciente/controlador_cargar_select_paciente_practica2.php",
      type: 'POST',
      data: {
          id2: id
      },
      beforeSend: function() {
          console.log("Enviando solicitud de prácticas..."); // Debug
      },
      success: function(response) {
          console.log("Respuesta recibida:", response); // Debug
          
          try {
              let data = JSON.parse(response);
              let cadena = "<option value=''>Seleccione una práctica</option>";
              
              if (data && data.length > 0) {
                  data.forEach(function(item) {
                      cadena += `<option value="${item[0]}">Código: ${item[1]} - ${item[2]}</option>`;
                  });
                  console.log("Prácticas cargadas exitosamente"); // Debug
              } else {
                  cadena = "<option value=''>No hay prácticas disponibles</option>";
                  console.log("No se encontraron prácticas"); // Debug
              }
              
              // Actualizar ambos selects y reinicializarlos
              $("#select_practica")
                  .html(cadena)
                  .select2({
                      dropdownParent: $("#modal_registro"),
                      placeholder: "Seleccione una práctica",
                      allowClear: true,
                      width: '100%'
                  });
              
              $("#select_practica_editar")
                  .html(cadena)
                  .select2({
                      dropdownParent: $("#modal_editar"),
                      placeholder: "Seleccione una práctica",
                      allowClear: true,
                      width: '100%'
                  });
              
          } catch (error) {
              console.error("Error al procesar datos de prácticas:", error);
              $("#select_practica, #select_practica_editar")
                  .html("<option value=''>Error al cargar prácticas</option>");
          }
      },
      error: function(xhr, status, error) {
          console.error("Error en la petición AJAX de prácticas:", error);
          $("#select_practica, #select_practica_editar")
              .html("<option value=''>Error al cargar prácticas</option>");
      }
  });
}

// Event listeners actualizados
$(document).ready(function() {
  // Evento para cambio en obra social
  $("#select_obras").off('change').on('change', function() {
      let id = $(this).val();
      console.log("Obra social seleccionada:", id); // Debug
      
      if (id) {
          // Primero cargar pacientes
          Cargar_Select_Paciente(id);
          // Luego cargar prácticas
          Cargar_Select_Practica(id);
      } else {
          // Limpiar selects dependientes si no hay obra social seleccionada
          $("#select_paciente, #select_practica").empty().trigger('change');
      }
  });
  
  // Evento para cambio en práctica
  $("#select_practica, #select_practica_editar").off('change').on('change', function() {
      let id = $(this).val();
      if (id) {
          Traerprecio(id);
      }
  });
});

function Traerprecio(id) {
  $.ajax({
    url: "../controller/practicas_paciente/controlador_traermonto.php",
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
    actualizarSubtotal(); // Llamar al cálculo de subtotal después de obtener el precio
    actualizarSubtotal_editar();
  }).fail(function() {
    console.log("Error al traer el precio.");
  });
}

function actualizarSubtotal() {
  const precio = parseFloat($("#txt_precio").val()) || 0;
  const cantidad = parseInt($("#txt_cantidad").val()) || 0;
  const subtotal = precio * cantidad;
  $("#txt_subtotal").val(subtotal.toFixed(2));
}

$(document).ready(function() {
  actualizarSubtotal(); // Inicializar subtotal al cargar la página

  $("#txt_cantidad").on("change", function() {
    actualizarSubtotal();
  });
});

function actualizarSubtotal_editar() {
  const precio = parseFloat($("#txt_precio_editar").val()) || 0;
  const cantidad = parseInt($("#txt_cantidad_editar").val()) || 0;
  const subtotal = precio * cantidad;
  $("#txt_subtotal_editar").val(subtotal.toFixed(2));
}

$(document).ready(function() {
  actualizarSubtotal_editar(); // Inicializar subtotal al cargar la página

  $("#txt_cantidad_editar").on("change", function() {
    actualizarSubtotal_editar();
  });
});




//ELIMINAR PRACTICA PACIENTE
function Eliminar_Practica_paciente(id){
  $.ajax({
    "url":"../controller/practicas_paciente/controlador_eliminar_practicas_paciente.php",
    type:'POST',
    data:{
      id:id
    }
  }).done(function(resp){
    if(resp>0){
        Swal.fire("Mensaje de Confirmación","Se elimino la práctica del paciente con exito","success").then((value)=>{
          tbl_paciente_practica.ajax.reload();
        });
    }else{
      return Swal.fire("Mensaje de Advetencia","No se puede eliminar esta práctica por que esta siendo utilizado en las facturas, verifique por favor","warning");

    }
  })
}

//ENVIANDO AL BOTON DELETE
$('#tabla_paciente_practica').on('click','.eliminar',function(){
  var data = tbl_paciente_practica.row($(this).parents('tr')).data();

  if(tbl_paciente_practica.row(this).child.isShown()){
      var data = tbl_paciente_practica.row(this).data();
  }
  Swal.fire({
    title: 'Desea eliminar la práctica del paciente: <b style="color:blue">'+data.PACIENTE+'</b>?',
    text: "Una vez aceptado la práctica sera eliminada por completo!!!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_Practica_paciente(data.id_paciente_practica);
    }
  })
})

//MODAL VER PRÁCTICAS
$('#tabla_paciente_practica').on('click','.mostrar',function(){
  var data = tbl_paciente_practica.row($(this).parents('tr')).data();

  if(tbl_paciente_practica.row(this).child.isShown()){
      var data = tbl_paciente_practica.row(this).data();
  }
$("#modal_ver_practicas").modal('show');

  document.getElementById('lb_titulo').innerHTML="<b>PRÁCTICAS DEL PACIENTE:</b> "+data.PACIENTE+"";
  document.getElementById('lb_titulo2').innerHTML="<b>OBRA SOCIAL:</b> "+data.obra_social+"";

  listar_practicas(data.id_paciente_practica);

})
// VISTA DE PRACTICAS
var tbl_practica;
function listar_practicas(id) {
  tbl_practica = $("#tabla_ver_practicas").DataTable({
      "ordering": false,
      "bLengthChange": true,
      "searching": false,  // Deshabilita la barra de búsqueda
      "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
      "pageLength": 5,
      "destroy": true,
      pagingType: 'full_numbers',
      scrollCollapse: true,
      responsive: true,
      "async": false,
      "processing": true,
      "ajax": {
          "url": "../controller/practicas_paciente/controlador_listar_tabla_practicas_paciente.php",
          type: 'POST',
          data: {
              id: id
          },
          dataSrc: function(json) {
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
          filename: "LISTA DE PRACTICAS REALIZADAS",
          title: "LISTA DE PRACTICAS REALIZADAS",
          className: 'btn btn-success' 
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE PRACTICAS REALIZADAS",
          title: "LISTA DE PRACTICAS REALIZADAS",
          className: 'btn btn-danger'
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE PRACTICAS REALIZADAS",
          className: 'btn btn-primary' 
        }
      ],
      "columns": [
          { 
              "data": null, // Columna vacía para la numeración
              "render": function (data, type, row, meta) {
                  return meta.row + 1; // Devuelve el índice de la fila + 1
              }
          },
          { "data": "cod_practica" },
          { "data": "PRACTICA" },
          { "data": "CANTIDAD" }
      ],
      "language": idioma_espanol,
      select: true
  });
}



function Agregar_practica(){
  var id_practica=$("#select_practica").val();
  var practica=$("#select_practica option:selected").text();
  var precio=$("#txt_precio").val();
  var cantidad=$("#txt_cantidad").val();
  var subtotal=$("#txt_subtotal").val();


  if (!id_practica || id_practica.trim() === "" || !precio || precio === ""|| !cantidad || cantidad === "") {
    return Swal.fire("Mensaje de Advertencia", "Seleccione una práctica y un precio por favor", "warning");
}

  if(verificarid(id_practica)){
   return Swal.fire("Mensaje de Advertencia","La práctica ya fue agregado a la tabla","warning");
  }

  var datos_agregar ="<tr>";

  datos_agregar+="<td for='id'>"+id_practica+"</td>";
  datos_agregar+="<td>"+practica+"</td>";
  datos_agregar+="<td style='text-align:center; display: none;'>"+precio+"</td>";
  datos_agregar+="<td>"+cantidad+"</td>";
  datos_agregar+="<td style='text-align:center; display: none;'>"+subtotal+"</td>";

  datos_agregar+="<td><button class='btn btn-danger' onclick='remove(this)'><i class='fas fa-trash'><i></button></td>";
  datos_agregar+="</tr>";
  $("#tabla_practica").append(datos_agregar);
  SumarTotal();

document.getElementById('txt_cantidad').value="1";

}
//BORRAR REGISTRO
function remove(t){
  var td =t.parentNode;
  var tr=td.parentNode;
  var table =tr.parentNode;
  table.removeChild(tr);
  SumarTotal();

}
//SUMAR TOTAL
// SUMAR TOTAL
function SumarTotal() {
  let total = 0;

  // Recorremos cada fila de la tabla
  $("#tabla_practica tbody tr").each(function () {
    let precio = $(this).find('td').eq(4).text().trim(); // Tomamos el precio de la columna correcta (índice 2)
    
    if (precio !== "") { // Aseguramos que el valor no esté vacío
      total += parseFloat(precio) || 0; // Convertimos a número y evitamos NaN con || 0
    }
  });

  // Mostramos el total en el label
  $("#lbl_totalneto").html("<b>Total: </b>$AR " + total.toFixed(2));
}

//VALIDACIÓN
function verificarid(id){
  let idverificar=document.querySelectorAll('#tabla_practica td[for="id"]');
  return [].filter.call(idverificar, td=>td.textContent ===id).length===1;
}

function Registrar_Practica() {
  let count = $("#tabla_practica tbody#tbody_tabla_practica tr").length;
  if (count === 0) {
      return Swal.fire("Mensaje de Advertencia", "La tabla de prácticas debe tener al menos un registro", "warning");
  }

   //DATOS DEL DOCENTE
   let area = document.getElementById('select_area').value;
   let paciente = document.getElementById('select_paciente').value;
   let practica = document.getElementById('select_practica').value;
   let total = parseFloat(document.getElementById('lbl_totalneto').textContent.replace(/[^0-9.]/g, '')) || 0;
   let hc = document.getElementById('txt_hc').value;
   let idusu = document.getElementById('txtprincipalid').value;
   
   if (!area || !paciente || !practica) {
    return Swal.fire("Mensaje De Advertencia", "Debe llenar los datos de la práctica primero para guardar", "warning");
}
   // Obtener la fecha actual para generar nombres únicos
   let f = new Date();
   
   // Procesar Factura
   let histocli = "";
   if (hc.length > 0) {
     let extensionFact = hc.split('.').pop();
     histocli = "HC" + f.getDate() + "-" + (f.getMonth() + 1) + "-" + f.getFullYear() + "-" + f.getHours() + "-" + f.getMilliseconds() + "." + extensionFact;
   }
   
   // Procesar Nota de Crédito

   
   // Crear FormData
   let formData = new FormData();
   let hcObj = $("#txt_hc")[0].files[0]; // Obtener el archivo de factura
   
   if (hcObj) {
     formData.append("hc", hcObj, histocli);
   }
   
   // Agregar otros datos al FormData
   formData.append("area", area);
   formData.append("paciente", paciente);
   formData.append("total", total);
   formData.append("histocli", histocli);
   formData.append("hcObj", hcObj);
   formData.append("idusu", idusu);
   

     $.ajax({
       url: "../controller/practicas_paciente/controlador_registrar_practicas_docente.php",
       type:'POST',
       data:formData,
       contentType:false,
       processData:false,
       success:function(resp){
        if (resp > 0) {
          Registrar_Detalle_practicas(parseInt(resp));
          Swal.fire("Mensaje de Confirmación", "Datos registrados correctamente", "success").then(() => {
              tbl_paciente_practica.ajax.reload();
              $("#modal_registro").modal('hide');
          });
      } else {
          return Swal.fire("Mensaje De Advertencia", "Lo sentimos, no se pudo completar el registro", "warning");
      }
       }
     });
 }
 


// REGISTRO DETALLE PRACTICAS
function Registrar_Detalle_practicas(id) {
  let count = $("#tabla_practica tbody#tbody_tabla_practica tr").length;
  if (count === 0) {
      return Swal.fire("Mensaje de Advertencia", "El detalle de las prácticas debe tener al menos un registro", "warning");
  }

  let arreglo_practica = [];
  let arreglo_precio = [];
  let arreglo_cantidad = [];
  let arreglo_subtotal = [];

  $("#tabla_practica tbody#tbody_tabla_practica tr").each(function () {
      arreglo_practica.push($(this).find('td').eq(0).text().trim());
      arreglo_precio.push($(this).find('td').eq(2).text().trim());
      arreglo_cantidad.push($(this).find('td').eq(3).text().trim());
      arreglo_subtotal.push($(this).find('td').eq(4).text().trim());
  });

  let practicas = arreglo_practica.join(",");
  let precio = arreglo_precio.join(",");
  let cantidad = arreglo_cantidad.join(",");
  let subtotal = arreglo_subtotal.join(",");


  $.ajax({
      url: "../controller/practicas_paciente/controlador_detalle_practicas.php",
      type: 'POST',
      data: {
          id: id,
          practicas: practicas,
          precio:precio,
          cantidad:cantidad,
          subtotal:subtotal
      }
  }).done(function (resp) {
      if (resp > 0) {
          tbl_paciente_practica.ajax.reload();
          $("#modal_registro").modal('hide');
          Cargar_Select_Obras_Sociales2();
          Cargar_Select_Areas();
          Cargar_Select_Paciente();
          Cargar_Select_Practica();
          document.getElementById('txt_cantidad').value="1";
          $("#tabla_practica tbody#tbody_tabla_practica").empty();

      } else {
          return Swal.fire("Mensaje De Advertencia", "Lo sentimos, no se pudo completar el registro", "warning");
      }
  });
}


//EDITAR PRACTICAS - PACIENTE

$('#tabla_paciente_practica').on('click','.editar',function(){
  var data = tbl_paciente_practica.row($(this).parents('tr')).data();

  if(tbl_paciente_practica.row(this).child.isShown()){
      var data = tbl_paciente_practica.row(this).data();
  }
  $("#modal_editar").modal('show');
  $("#select_obras_editar").val(data.id_cuit).trigger('change');
  document.getElementById('txt_id_detalle').value = data.id_paciente_practica;
  document.getElementById('txt_area').value = data.area_nombre;
  document.getElementById('txt_paciente').value = data.PACIENTE;
  document.getElementById('txt_profesional_editar').value = data.USUARIO;
  document.getElementById('txt_fecha_editar').value = data.fecha_formateada;
  document.getElementById('lbl_totalneto_editar').innerHTML = '<b>Total:</b> $AR '+data.total;

  listar_practicas_del_paciente(data.id_paciente_practica);

})
var tbl_traer_datos;
function listar_practicas_del_paciente(id) {

  tbl_traer_datos = $("#tabla_practica_editar").DataTable({
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
    "url": "../controller/practicas_paciente/controlador_listar_detalle_practicas.php",
    "type": 'POST',
    "data": { id: id },
   
    },
    "columns": [
      {"data": "id_paciente_practica"},
      {"data": "id_practica"},
      {"data": "practica"},
      {"data": "precio_unitario", "visible": false},
      {"data": "cantidad"},
      {"data": "subtotal" , "visible": false}, // Oculta la columna
      {"defaultContent": "<button class='delete btn btn-danger btn-sm' title='Eliminar datos de especialidad'><i class='fa fa-trash'></i> Eliminar</button>"}
    ],
    "language": idioma_espanol,
    "select": true
  });
}

function Eliminar_detalle_practica_unico(id){
  $.ajax({
    "url":"../controller/practicas_paciente/controlador_eliminar_detalle_practica.php",
    type:'POST',
    data:{
      id:id
    }
  }).done(function(resp){
    if(resp>0){
        Swal.fire("Mensaje de Confirmación","Se elimino la práctica exitosamente","success").then((value)=>{
          tbl_traer_datos.ajax.reload();
          setTimeout(SumarTotal_Editar, 500); // Esperar un poco antes de recalcular

        });
    }else{
      return Swal.fire("Mensaje de Advetencia","No se puede eliminar esta práctica por que esta siendo utilizado en otros formularios, verifique por favor","warning");

    }
  })
}

//ENVIANDO AL BOTON DELETE
$('#tabla_practica_editar').on('click','.delete',function(){
  var data = tbl_traer_datos.row($(this).parents('tr')).data();

  if(tbl_traer_datos.row(this).child.isShown()){
      var data = tbl_traer_datos.row(this).data();
  }
  Swal.fire({
    title: 'Desea eliminar la práctica: '+data.practica+' seleccionado?',
    text: "Una vez aceptado la práctica sera eliminada!!!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_detalle_practica_unico(data.id_practica_paciente_total);
      SumarTotal_Editar();
    }
  })
})


function Agregar_practica_editar() {
  var id_practica_edi_principal = $("#txt_id_detalle").val();
  var id_practica_edi = $("#select_practica_editar").val();
  var practica_edi = $("#select_practica_editar option:selected").text();
  var precio_edi = $("#txt_precio_editar").val();
  var cantidad_edi = $("#txt_cantidad_editar").val();
  var subtotal_edi = $("#txt_subtotal_editar").val();

  if (!id_practica_edi || id_practica_edi.trim() === "" || !precio_edi || precio_edi === ""|| !cantidad_edi || cantidad_edi === "") {
    return Swal.fire("Mensaje de Advertencia", "Seleccione una práctica y un precio por favor", "warning");
}


  if (verificarid_editar(id_practica_edi)) {
    return Swal.fire("Mensaje de Advertencia", "La práctica ya fue agregada a la tabla", "warning");
  }

  var datos_agregar = "<tr>";
  datos_agregar += "<td>" + id_practica_edi_principal + "</td>";
  datos_agregar += "<td for='id'>" + id_practica_edi + "</td>";
  datos_agregar += "<td>" + practica_edi + "</td>";
  datos_agregar += "<td style='text-align:center; display: none;'>" + precio_edi + "</td>";
  datos_agregar += "<td>" + cantidad_edi + "</td>";
  datos_agregar += "<td style='text-align:center; display: none;'> " + subtotal_edi + "</td>";

  datos_agregar += "<td><button class='btn btn-danger' onclick='remove1(this)'><i class='fas fa-trash'></i></button></td>";
  datos_agregar += "</tr>";

  $("#tabla_practica_editar tbody").append(datos_agregar);
  SumarTotal_Editar();
}

// BORRAR REGISTRO
function remove1(t) {
  var td = t.parentNode;
  var tr = td.parentNode;
  tr.remove();
  SumarTotal_Editar();
}

// SUMAR TOTAL
function SumarTotal_Editar() {
  let total = 0;
  
  // Primero sumamos los registros de DataTables
  let tabla = $("#tabla_practica_editar").DataTable();
  tabla.rows().every(function() {
    let data = this.data();
    if (data && data.subtotal) {
      total += parseFloat(data.subtotal) || 0;
    }
  });
  
  // Luego sumamos las filas añadidas manualmente al DOM
  $("#tabla_practica_editar tbody tr").each(function() {
    // Verifica si la fila es nueva (no está en DataTables)
    if (!tabla.row(this).data()) {
      // La columna subtotal está en la posición 5 (0-based index)
      let subtotal = $(this).find('td').eq(5).text().trim();
      if (subtotal !== "") {
        total += parseFloat(subtotal) || 0;
      }
    }
  });

  $("#lbl_totalneto_editar").html("<b>Total: </b>$AR " + total.toFixed(2));
}

// Función para calcular subtotal cuando se modifica cantidad o precio
function calcularSubtotal_Editar() {
  let precio = parseFloat($("#txt_precio_editar").val()) || 0;
  let cantidad = parseFloat($("#txt_cantidad_editar").val()) || 0;
  let subtotal = precio * cantidad;
  $("#txt_subtotal_editar").val(subtotal.toFixed(2));
}

// Agregar event listeners para recalcular cuando cambian cantidad o precio
$("#txt_precio_editar, #txt_cantidad_editar").on('input', function() {
  calcularSubtotal_Editar();
});

// VALIDACIÓN
function verificarid_editar(id) {
  let idverificar = document.querySelectorAll('#tabla_practica_editar td[for="id"]');
  return [].filter.call(idverificar, td => td.textContent === id).length > 0;
}

//EDITANDO PRACTICAS
function Modificar_detalle_practicas() {
  let componentes = [];

  let totalText = document.getElementById('lbl_totalneto_editar')?.textContent.replace(/[^0-9.]/g, '').trim();
  let total = parseFloat(totalText) || 0;  // Validación robusta del total
  let idusu = document.getElementById('txtprincipalid')?.value.trim();
  let idprac = document.getElementById('txt_id_detalle')?.value.trim();


  
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


  // Enviar datos por AJAX
  $.ajax({
    url: '../controller/practicas_paciente/controlador_modificar_detalle_practicas.php',
    type: 'POST',
    data: {
      total: total.toFixed(2),
      idusu: idusu,
      idprac:idprac,
      componentes: JSON.stringify(componentes)
    },
    dataType: 'json' // Aseguramos que la respuesta sea JSON
  })
  .done(function (resp) {
    console.log("Respuesta del servidor:", resp);
    if (resp.status === 1) {
        Swal.fire("Mensaje de Confirmación", "Se actualizó correctamente la practica paciente", "success").then(() => {
          tbl_paciente_practica.ajax.reload();
            $("#modal_editar").modal('hide');
        });
    } else if (resp.status === 2) {
        Swal.fire("Mensaje de Información", "No se modificaron las prácticas porque ya existen o no había registros nuevos- solo se modifico el total", "success");
        tbl_paciente_practica.ajax.reload();
        $("#modal_editar").modal('hide');

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





//REGISTRAR PACIENTE
function AbrirRegistro2(){
  $("#modal_registro2").modal({backdrop:'static',keyboard:false})
  $("#modal_registro2").modal('show');
}
function Registrar_Paciente(){
  let dni = document.getElementById('txt_nro').value;
  let nom = document.getElementById('txt_nom').value;
  let epell = document.getElementById('txt_apelli').value;
  let direc = document.getElementById('txt_direccion').value;
  let local = document.getElementById('txt_local').value;
  let tele = document.getElementById('txt_tele').value;
  let obra = document.getElementById('txt_obras_sociales').value;
  let idusu = document.getElementById('txtprincipalid').value;


  if(dni.length==0 || nom.length==0 || epell.length==0 || tele.length==0|| obra.length==0){
      return Swal.fire("Mensaje de Advertencia","Tiene campos vacios","warning");
  }
  
  $.ajax({
    "url":"../controller/pacientes/controlador_registro_pacientes.php",
    type:'POST',
    data:{
      dni:dni,
      nom:nom,
      epell:epell,
      direc:direc,
      local:local,
      tele:tele,
      obra:obra,
      idusu:idusu
    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Nuevo Paciente registrado con el DNI N°: <b>"+dni+"</b>","success").then((value)=>{
          document.getElementById('txt_nro').value="";
          document.getElementById('txt_nom').value="";
          document.getElementById('txt_apelli').value="";
          document.getElementById('txt_direccion').value="";
          document.getElementById('txt_local').value="";
          document.getElementById('txt_tele').value="";

        $("#modal_registro2").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","El DNI ingresado ya se encuentra en la base de datos, revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo el registro","error");

    }
  })
}

//ADJUNTAR HISTORIA CLINICA
$('#tabla_paciente_practica').on('click','.adjuntar',function(){
  var data = tbl_paciente_practica.row($(this).parents('tr')).data();

  if(tbl_paciente_practica.row(this).child.isShown()){
      var data = tbl_paciente_practica.row(this).data();
  }
  $("#modal_adjuntar").modal('show');
  document.getElementById('id_txt_paci').value=data.id_paciente_practica;
  document.getElementById('txt_paci').value=data.PACIENTE;
  document.getElementById('txt_obrit').value=data.obra_social;
  document.getElementById('total').value=data.total;
  document.getElementById('foto_actual').value=data.historia_clinica;


})

function Modificar_HC(){
  let id = document.getElementById("id_txt_paci").value
  let archivo = document.getElementById("txt_hc_editar").value
  let archivoactual = document.getElementById("foto_actual").value

  if(id.length==0 || archivo.length==0){
    return Swal.fire("Mensaje de Advertencia","EL archivo no puede estar vacio","warning");
}

    let extension = archivo.split('.').pop();
    let nombrearchivo="";
    let f = new Date();
    if(archivo.length>0){
      nombrearchivo="HC"+f.getDate()+"-"+(f.getMonth()+1)+"-"+f.getFullYear()+"-"+f.getHours()+"-"+f.getMilliseconds()+"."+extension;
    }
    let formData = new FormData();
    let archivoobj = $("#txt_hc_editar")[0].files[0];

    formData.append("id",id);
    formData.append("nombrearchivo",nombrearchivo);
    formData.append("archivoactual",archivoactual);
    formData.append("archivoobj",archivoobj);
    $.ajax({
      url:"../controller/practicas_paciente/controlador_modificar_hc.php",
      type:'POST',
      data:formData,
      contentType:false,
      processData:false,
      success:function(resp){
        if(resp.length>0){
          Swal.fire("Mensaje de Confirmación","Archivo subido","success").then((value)=>{
            $("#modal_adjuntar").modal('hide');
            tbl_paciente_practica.ajax.reload();
            document.getElementById('txt_hc_editar').value="";

          });
        }else{
          Swal.fire("Mensaje de Advertencia","No se pudo subir el archivo","warning");
        }
      }
    });
}