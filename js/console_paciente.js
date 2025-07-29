var tbl_paciente;
function listar_paciente(){
  Cargar_Select_Obras_Sociales();
  document.getElementById('txtfechainicio').value='';
  document.getElementById('txtfechafin').value='';
  tbl_paciente = $("#tabla_paciente").DataTable({
    
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
          "url":"../controller/pacientes/controlador_listar_pacientes.php",
          type:'POST'
      },
      dom: 'Bfrtip',       

      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE PACIENTES",
          title: "LISTA DE PACIENTES",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9 ] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE PACIENTES",
          title: "LISTA DE PACIENTES",
          className: 'btn btn-pdf',
          orientation: 'landscape', // <-- Establece la orientación en horizontal
          pageSize: 'A4', // <-- Especifica el tamaño de la página
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9 ] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE PACIENTES",
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9 ] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      
      "columns":[
        {"defaultContent":""},
        {"data":"Dni"},
        {"data":"PACIENTE"},
        {"data":"Direccion"},
        {"data":"localidad"},
        {"data":"Telefono"},
        {"data":"OBRA"},
        {"data":"fecha_formateada"},
        {"data":"fecha_formateada2"},
        {"data":"USUARIO"},

      
        {
          "defaultContent": `
            <button class='mostrar btn btn-success btn-sm' title='Mostrar datos de área'>
              <i class='fa fa-eye'></i> Mostrar
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
tbl_paciente.on('draw.td',function(){
  var PageInfo = $("#tabla_paciente").DataTable().page.info();
  tbl_paciente.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}


function listar_paciente_filtro(){
  let obra = document.getElementById('select_obras').value;
  let fechaini = document.getElementById('txtfechainicio').value;
  let fechafin = document.getElementById('txtfechafin').value;
  tbl_paciente = $("#tabla_paciente").DataTable({
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
          "url":"../controller/pacientes/controlador_listar_pacientes_filtro.php",
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
          filename: "LISTA DE PACIENTES",
          title: "LISTA DE PACIENTES",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9 ] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE PACIENTES",
          title: "LISTA DE PACIENTES",
          className: 'btn btn-pdf',
          orientation: 'landscape', // <-- Establece la orientación en horizontal
          pageSize: 'A4', // <-- Especifica el tamaño de la página
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9 ] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE PACIENTES",
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9 ] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      
      "columns":[
        {"defaultContent":""},
        {"data":"Dni"},
        {"data":"PACIENTE"},
        {"data":"Direccion"},
        {"data":"localidad"},
        {"data":"Telefono"},
        {"data":"OBRA"},
        {"data":"fecha_formateada"},
        {"data":"fecha_formateada2"},
        {"data":"USUARIO"},

      
        {
          "defaultContent": `
            <button class='mostrar btn btn-success btn-sm' title='Mostrar datos de área'>
              <i class='fa fa-eye'></i> Mostrar
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
tbl_paciente.on('draw.td',function(){
  var PageInfo = $("#tabla_paciente").DataTable().page.info();
  tbl_paciente.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}

//MOSTRAR
$('#tabla_paciente').on('click','.mostrar',function(){
  var data = tbl_paciente.row($(this).parents('tr')).data();

  if(tbl_paciente.row(this).child.isShown()){
      var data = tbl_paciente.row(this).data();
  }
  $("#modal_mostrar").modal('show');
  document.getElementById('txt_nro_mostrar').value=data.Dni;
  document.getElementById('txt_nom_mostrar').value=data.Nombres;
  document.getElementById('txt_apelli_mostrar').value=data.Apellidos;
  document.getElementById('txt_direccion_mostrar').value=data.Direccion;
  document.getElementById('txt_local_mostrar').value=data.localidad;
  document.getElementById('txt_tele_mostrar').value=data.Telefono;
  $("#txt_obras_sociales_mostrar").select2().val(data.Id_obra_social).trigger('change.select2');

  document.getElementById('txt_fecha_reg').value=data.fecha_formateada;
  document.getElementById('txt_fecha_actu').value=data.fecha_formateada2;
  document.getElementById('txt_usu').value=data.USUARIO;

})

//EDITAR
$('#tabla_paciente').on('click','.editar',function(){
  var data = tbl_paciente.row($(this).parents('tr')).data();

  if(tbl_paciente.row(this).child.isShown()){
      var data = tbl_paciente.row(this).data();
  }
  $("#modal_editar").modal('show');
  document.getElementById('txt_id_paciente').value=data.id_paciente;
  document.getElementById('txt_nro_editar').value=data.Dni;
  document.getElementById('txt_nom_editar').value=data.Nombres;
  document.getElementById('txt_apelli_editar').value=data.Apellidos;
  document.getElementById('txt_direccion_editar').value=data.Direccion;
  document.getElementById('txt_local_editar').value=data.localidad;
  document.getElementById('txt_tele_editar').value=data.Telefono;
  $("#txt_obras_sociales_editar").select2().val(data.Id_obra_social).trigger('change.select2');
})

function AbrirRegistro(){
  $("#modal_registro").modal({backdrop:'static',keyboard:false})
  $("#modal_registro").modal('show');
}

//CARGAR OBRAS SOCIALES
// Modificar tu función actual
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
      
      // Actualizar todos los selects
      $('#txt_obras_sociales, #txt_obras_sociales_mostrar, #txt_obras_sociales_editar, #select_obras').html(cadena);
      
      // Inicializar Select2 para el select principal
      $('#select_obras').select2({
          placeholder: "Seleccionar Obra Social",
          allowClear: true
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
          tbl_paciente.ajax.reload();
          document.getElementById('txt_nro').value="";
          document.getElementById('txt_nom').value="";
          document.getElementById('txt_apelli').value="";
          document.getElementById('txt_direccion').value="";
          document.getElementById('txt_local').value="";
          document.getElementById('txt_tele').value="";
          Cargar_Select_Obras_Sociales();

        $("#modal_registro").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","El DNI ingresado ya se encuentra en la base de datos, revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo el registro","error");

    }
  })
}
function Modificar_Paciente(){

  let id = document.getElementById('txt_id_paciente').value;
  let dni = document.getElementById('txt_nro_editar').value;
  let nom = document.getElementById('txt_nom_editar').value;
  let epell = document.getElementById('txt_apelli_editar').value;
  let direc = document.getElementById('txt_direccion_editar').value;
  let local = document.getElementById('txt_local_editar').value;
  let tele = document.getElementById('txt_tele_editar').value;
  let obra = document.getElementById('txt_obras_sociales_editar').value;
  let idusu = document.getElementById('txtprincipalid').value;

  if(id.length==0||dni.length==0 || nom.length==0 || epell.length==0 || tele.length==0|| obra.length==0){
    return Swal.fire("Mensaje de Advertencia","Tiene campos vacios","warning");
  }
  
  $.ajax({
    "url":"../controller/pacientes/controlador_modificar_pacientes.php",
    type:'POST',
    data:{
        id:id,
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
        Swal.fire("Mensaje de Confirmación","Datos del Paciente Actualizado","success").then((value)=>{
          tbl_paciente.ajax.reload();
        $("#modal_editar").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","El DNI ingresado ya se encuentra en la base de datos, revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo el proceso","error");

    }
  })
}
///////VALIDAR EMAIL
function Eliminar_paciente(id){
  $.ajax({
    "url":"../controller/pacientes/controlador_eliminar_paciente.php",
    type:'POST',
    data:{
      id:id
    }
  }).done(function(resp){
    if(resp>0){
        Swal.fire("Mensaje de Confirmación","Se elimino el paciente con exito","success").then((value)=>{
          tbl_paciente.ajax.reload();

        });
    }else{
      return Swal.fire("Mensaje de Advetencia","No se puede eliminar esta paciente por que esta siendo utilizado en el módulo de Prácticas, verifique por favor","warning");

    }
  })
}

//ENVIANDO AL BOTON DELETE
$('#tabla_paciente').on('click','.eliminar',function(){
  var data = tbl_paciente.row($(this).parents('tr')).data();

  if(tbl_paciente.row(this).child.isShown()){
      var data = tbl_paciente.row(this).data();
  }
  Swal.fire({
    title: 'Desea eliminar al paciente con el N° de DNI: '+data.Dni+'?',
    text: "Una vez aceptado el paciente sera eliminado!!!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_paciente(data.id_paciente);
    }
  })
})


