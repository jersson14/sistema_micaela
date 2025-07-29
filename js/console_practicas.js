var tbl_practicas;
function listar_practicas(){
  Cargar_Select_Obras_Sociales();
  document.getElementById('txtfechainicio').value='';
  document.getElementById('txtfechafin').value='';

  tbl_practicas = $("#tabla_practicas").DataTable({
      "ordering":false,   
      "bLengthChange":true,
      "searching": { "regex": false },
      "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
      "pageLength": 10,
      "destroy":true,
      "async": false ,
      pagingType: 'full_numbers',
      scrollCollapse: true,
      responsive: true,
      "processing": true,
      "ajax":{
          "url":"../controller/practicas/controlador_listar_practicas.php",
          type:'POST'
      },
      dom: 'Bfrtip',       
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE PRÁCTICAS",
          title: "LISTA DE PRÁCTICAS",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE PRÁCTICAS",
          title: "LISTA DE PRÁCTICAS",
          className: 'btn btn-pdf',
          orientation: 'landscape', // <-- Establece la orientación en horizontal
          pageSize: 'A4', // <-- Especifica el tamaño de la página
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE PRÁCTICAS",
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      
     "columns": [
  {"defaultContent": ""},
  {"data": "cod_practica"},
  {"data": "practica"},
  {
    "data": "valor",
    "render": function (data, type, row) {
      return `<strong>$AR ${data}</strong>`;
    }
  },
  {"data": "OBRA"},
  {"data": "fecha_formateada"},
  {"data": "fecha_formateada2"},
  {"data": "USUARIO"},
  {"data":"estado",
    render: function(data,type,row){
            if(data=='ACTIVO'){
            return '<span class="badge bg-success">ACTIVO</span>';
            }else{
            return '<span class="badge bg-danger">INACTIVO</span>';
            }
    }   
},
  {
    "defaultContent": `

<button class="historial btn btn-warning btn-sm" title="Ver historial de ediciones">
  <i class="fa fa-history"></i> Historial
</button>


      <button class='editar btn btn-primary btn-sm' title='Editar datos de área'>
        <i class='fa fa-edit'></i> Editar
      </button>
      <button class='eliminar btn btn-danger btn-sm' title='Eliminar datos de área'>
        <i class='fa fa-trash'></i> Eliminar
      </button>
    `
  }
],

    "language":idioma_espanol,
    select: true
});
tbl_practicas.on('draw.td',function(){
  var PageInfo = $("#tabla_practicas").DataTable().page.info();
  tbl_practicas.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}


function listar_practicas_filtro(){
  let obra = document.getElementById('select_obras').value;
  let fechaini = document.getElementById('txtfechainicio').value;
  let fechafin = document.getElementById('txtfechafin').value;
  tbl_practicas = $("#tabla_practicas").DataTable({
      "ordering":false,   
      "bLengthChange":true,
      "searching": { "regex": false },
      "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
      "pageLength": 10,
      "destroy":true,
      "async": false ,
      pagingType: 'full_numbers',
      scrollCollapse: true,
      responsive: true,
      "processing": true,
      "ajax":{
          "url":"../controller/practicas/controlador_listar_practicas_filtro.php",
          type:'POST',
          data:{
            obra:obra,
            fechaini:fechaini,
            fechafin:fechafin
          }
      },
      dom: 'Bfrtip',       
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE PRÁCTICAS",
          title: "LISTA DE PRÁCTICAS",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE PRÁCTICAS",
          title: "LISTA DE PRÁCTICAS",
          className: 'btn btn-pdf',
          orientation: 'landscape', // <-- Establece la orientación en horizontal
          pageSize: 'A4', // <-- Especifica el tamaño de la página
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE PRÁCTICAS",
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6, 7, 8] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      "columns": [
        {"defaultContent": ""},
        {"data": "cod_practica"},
        {"data": "practica"},
        {
          "data": "valor",
          "render": function (data, type, row) {
            return `<strong>$AR ${data}</strong>`;
          }
        },
        {"data": "OBRA"},
        {"data": "fecha_formateada"},
        {"data": "fecha_formateada2"},
        {"data": "USUARIO"},
        {"data":"estado",
          render: function(data,type,row){
                  if(data=='ACTIVO'){
                  return '<span class="badge bg-success">ACTIVO</span>';
                  }else{
                  return '<span class="badge bg-danger">INACTIVO</span>';
                  }
          }   
      },
      {
        "defaultContent": `
    
    <button class="historial btn btn-warning btn-sm" title="Ver historial de ediciones">
      <i class="fa fa-history"></i> Historial
    </button>
    
    
          <button class='editar btn btn-primary btn-sm' title='Editar datos de área'>
            <i class='fa fa-edit'></i> Editar
          </button>
          <button class='eliminar btn btn-danger btn-sm' title='Eliminar datos de área'>
            <i class='fa fa-trash'></i> Eliminar
          </button>
        `
      }
      ],

    "language":idioma_espanol,
    select: true
});
tbl_practicas.on('draw.td',function(){
  var PageInfo = $("#tabla_practicas").DataTable().page.info();
  tbl_practicas.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}


//EDITAR
$('#tabla_practicas').on('click','.editar',function(){
  var data = tbl_practicas.row($(this).parents('tr')).data();

  if(tbl_practicas.row(this).child.isShown()){
      var data = tbl_practicas.row(this).data();
  }
  $("#modal_editar").modal('show');
  document.getElementById('txt_id_practica').value=data.id_práctica;
  document.getElementById('txt_code_editar').value=data.cod_practica;
  document.getElementById('txt_practica_editar').value=data.practica;
  document.getElementById('txt_valor_editar').value=data.valor;
  $("#txt_obras_sociales_editar").select2().val(data.id_obras).trigger('change.select2');
  document.getElementById('txt_estatus').value=data.estado;

})

function AbrirRegistro(){
  $("#modal_registro").modal({backdrop:'static',keyboard:false})
  $("#modal_registro").modal('show');
}

//CARGAR OBRAS SOCIALES
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
    $('#txt_obras_sociales').html(cadena);
    $('#txt_obras_sociales_editar').html(cadena);
    $('#select_obras').html(cadena);

    // Inicializar Select2 después de cargar opciones
    $('#txt_obras_sociales').select2({
      placeholder: "Seleccionar Obra Social",
      allowClear: true,
      width: '100%' // Asegura que use todo el ancho
    });
  });
}
// Agregar estos event listeners
$('#modal_registro').on('shown.bs.modal', function() {
  $('#txt_obras_sociales').select2({
      placeholder: "Seleccionar Obra Social",
      allowClear: true,
      dropdownParent: $('#modal_registro')
  });
});

$('#modal_editar').on('shown.bs.modal', function() {
  $('#txt_obras_sociales_editar').select2({
      placeholder: "Seleccionar Obra Social",
      allowClear: true,
      dropdownParent: $('#modal_editar')
  });
});



function Registrar_Practicas(){
  let code = document.getElementById('txt_code').value;
  let pract = document.getElementById('txt_practica').value;
  let valor = document.getElementById('txt_valor').value;
  let obra = document.getElementById('txt_obras_sociales').value;
  let idusu = document.getElementById('txtprincipalid').value;


  if(code.length==0 || pract.length==0 || valor.length==0 || obra.length==0){
      return Swal.fire("Mensaje de Advertencia","Tiene campos vacios","warning");
  }
  
  $.ajax({
    "url":"../controller/practicas/controlador_registro_practicas.php",
    type:'POST',
    data:{
      code:code,
      pract:pract,
      valor:valor,
      obra:obra,
      idusu:idusu,

    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Nueva práctica registrada con el Código N°: <b>"+code+"</b>","success").then((value)=>{
          tbl_practicas.ajax.reload();
          document.getElementById('txt_code').value="";
          document.getElementById('txt_practica').value="";
          document.getElementById('txt_valor').value="";

        $("#modal_registro").modal('hide');
        Cargar_Select_Obras_Sociales();

        });
      }else{
        Swal.fire("Mensaje de Advertencia","El Código ingresado ya se encuentra en la base de datos, revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo el registro","error");

    }
  })
}
function Modificar_Paciente(){

  let id = document.getElementById('txt_id_practica').value;
  let code = document.getElementById('txt_code_editar').value;
  let pract = document.getElementById('txt_practica_editar').value;
  let valor = document.getElementById('txt_valor_editar').value;
  let obra = document.getElementById('txt_obras_sociales_editar').value;
  let status = document.getElementById('txt_estatus').value;
  let idusu = document.getElementById('txtprincipalid').value;

  if(id.length==0||code.length==0 || pract.length==0 || valor.length==0 || status.length==0|| obra.length==0){
    return Swal.fire("Mensaje de Advertencia","Tiene campos vacios","warning");
  }
  
  $.ajax({
    "url":"../controller/practicas/controlador_modificar_practicas.php",
    type:'POST',
    data:{
        id:id,
        code:code,
        pract:pract,
        valor:valor,
        obra:obra,
        status:status,
        idusu:idusu
    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Datos de la Práctica Actualizado","success").then((value)=>{
          tbl_practicas.ajax.reload();
        $("#modal_editar").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","El Código que esta ingresando ya se encuentra en la base de datos, revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo el proceso","error");

    }
  })
}
///////VALIDAR EMAIL
function Eliminar_paciente(id){
  $.ajax({
    "url":"../controller/practicas/controlador_eliminar_practicas.php",
    type:'POST',
    data:{
      id:id
    }
  }).done(function(resp){
    if(resp>0){
        Swal.fire("Mensaje de Confirmación","Se elimino la práctica con exito","success").then((value)=>{
          tbl_practicas.ajax.reload();

        });
    }else{
      return Swal.fire("Mensaje de Advetencia","No se puede eliminar esta práctica por que esta siendo utilizado en el módulo de Registros, verifique por favor","warning");

    }
  })
}

//ENVIANDO AL BOTON DELETE
$('#tabla_practicas').on('click','.eliminar',function(){
  var data = tbl_practicas.row($(this).parents('tr')).data();

  if(tbl_practicas.row(this).child.isShown()){
      var data = tbl_practicas.row(this).data();
  }
  Swal.fire({
    title: 'Desea eliminar la práctica con el Código N°: '+data.cod_practica+'?',
    text: "Una vez aceptado la práctica sera eliminada!!!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_paciente(data.id_práctica);
    }
  })
})


//MODAL VER HISTORIAL
$('#tabla_practicas').on('click','.historial',function(){
  var data = tbl_practicas.row($(this).parents('tr')).data();

  if(tbl_practicas.row(this).child.isShown()){
      var data = tbl_practicas.row(this).data();
  }
$("#modal_ver_historial").modal('show');

  document.getElementById('lb_titulo').innerHTML="<b>HISTORIAL DE PRACTICAS:</b> "+data.practica+"";

  listar_historial(data.id_práctica);

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
          "url": "../controller/practicas/controlador_listar_historial_practicas.php",
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
            "data": "Valor_monetario",
            "render": function (data, type, row) {
              return `<strong>$AR ${data}</strong>`;
            }
          },
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
