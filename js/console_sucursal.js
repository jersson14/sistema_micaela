var tbl_sucursal;
function listar_sucursales(){
  tbl_sucursal = $("#tabla_sucursal").DataTable({
      "ordering":true,   
      "bLengthChange":true,
      "searching": { "regex": false },
      "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
      "pageLength": 10,
      "destroy":true,
      pagingType: 'full_numbers',
      scrollCollapse: true,
      responsive: true,
      "async": false ,
      "processing": true,
      "ajax":{
          "url":"../controller/sucursal/controlador_listar_sucursal.php",
          type:'POST'
      },
      dom: 'Bfrtip', 
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: function() {
            return "LISTA DE SUCURSALES";
          },
          title: function() {
            return "LISTA DE SUCURSALES";
          },
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6,7] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: function() {
            return "LISTA DE SUCURSALES";
          },
          title: function() {
            return "LISTA DE SUCURSALES";
          },
          className: 'btn btn-pdf',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6,7] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: function() {
            return "LISTA DE SUCURSALES";
          },
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6,7] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      
      "columns":[
        {"defaultContent":""},
        {"data":"sucrusal"},
        {"data":"telefono1"},
        {"data":"telefono2"},
        {"data":"direccion"},
        {"data":"descripcion"},
        {"data":"fecha_formateada"},
        {"data":"fecha_formateada2"},

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
          "defaultContent": "<button class='editar btn btn-primary btn-sm' title='Editar datos de área'><i class='fa fa-edit'></i> Editar</button> <button class='eliminar btn btn-danger btn-sm' title='Eliminar datos de área'><i class='fa fa-trash'></i> Eliminar</button>"
        }
            ],

    "language":idioma_espanol,
    select: true
  });

  tbl_sucursal.on('draw.td', function(){
    var PageInfo = $("#tabla_sucursal").DataTable().page.info();
    tbl_sucursal.column(0, {page: 'current'}).nodes().each(function(cell, i){
      cell.innerHTML = i + 1 + PageInfo.start;
    });
  });
}

$('#tabla_sucursal').on('click','.editar',function(){
  var data = tbl_sucursal.row($(this).parents('tr')).data();

  if(tbl_sucursal.row(this).child.isShown()){
      var data = tbl_sucursal.row(this).data();
  }
  $("#modal_editar").modal('show');
  document.getElementById('txt_id_sucursal').value=data.id_sucursal;
  document.getElementById('txt_nombre_sucur_editar').value=data.sucrusal;
  document.getElementById('txt_tele1_editar').value=data.telefono1;
  document.getElementById('txt_tele2_editar').value=data.telefono2;
  document.getElementById('txt_direccion_editar').value=data.direccion;
  document.getElementById('txt_descripcion_editar').value=data.descripcion;
  document.getElementById('select_estado_editar').value=data.estado;
})

function AbrirRegistro(){
  $("#modal_registro").modal({backdrop:'static',keyboard:false})
  $("#modal_registro").modal('show');
}

function Registrar_Sucursal(){
  let sucur = document.getElementById('txt_nombre_sucur').value;
  let tele1 = document.getElementById('txt_tele1').value;
  let tele2 = document.getElementById('txt_tele2').value;
  let direc = document.getElementById('txt_direccion').value;
  let desc = document.getElementById('txt_descripcion').value;

  if(sucur.length==0|| tele1.length==0 || direc.length==0){
      return Swal.fire("Mensaje de Advertencia","Tiene campos vacios, ingrese los campos obligatorios","warning");
  }
  $.ajax({
    "url":"../controller/sucursal/controlador_registro_sucursal.php",
    type:'POST',
    data:{
      sucur:sucur,
      tele1:tele1,
      tele2:tele2,
      direc:direc,
      desc:desc
    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Nueva sucursal registrada con el nombre: "+sucur,"success").then((value)=>{
          tbl_sucursal.ajax.reload();
          document.getElementById('txt_nombre_sucur').value="";
          document.getElementById('txt_tele1').value="";
          document.getElementById('txt_tele2').value="";
          document.getElementById('txt_direccion').value="";
          document.getElementById('txt_descripcion').value="";
        $("#modal_registro").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","La sucursal que desea registrar ya se encuentra en la base de datos, revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo el registro","error");

    }
  })
}
function Modificar_Sucursal(){
  let id = document.getElementById('txt_id_sucursal').value;
  let sucu = document.getElementById('txt_nombre_sucur_editar').value;
  let tele1 = document.getElementById('txt_tele1_editar').value;
  let tele2 = document.getElementById('txt_tele2_editar').value;
  let direc = document.getElementById('txt_direccion_editar').value;
  let desc = document.getElementById('txt_descripcion_editar').value;
  let esta = document.getElementById('select_estado_editar').value;

  if(sucu.length==0|| tele1.length==0 || direc.length==0||id.length==0){
      return Swal.fire("Mensaje de Advertencia","Tiene campos vacios, por favor revise","warning");
  }
  $.ajax({
    "url":"../controller/sucursal/controlador_modificar_sucursal.php",
    type:'POST',
    data:{
      id:id,
      sucu:sucu,
      tele1:tele1,
      tele2:tele2,
      direc:direc,
      desc:desc,
      esta:esta
    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Datos actualizados de la sucursal: "+sucu,"success").then((value)=>{
          tbl_sucursal.ajax.reload();
        $("#modal_editar").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","La sucursal ingresada ya se encuentra en la base de datos, revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo la actualización","error");

    }
  })
}

//ELIMINAR AREAS
function Eliminar_sucursal(id){
  $.ajax({
    "url":"../controller/sucursal/controlador_eliminar_sucursal.php",
    type:'POST',
    data:{
      id:id
    }
  }).done(function(resp){
    if(resp>0){
        Swal.fire("Mensaje de Confirmación","Se elimino la sucursal con exito","success").then((value)=>{
          tbl_sucursal.ajax.reload();

        });
    }else{
      return Swal.fire("Mensaje de Advetencia","No se puede eliminar esta sucursal por que esta siendo utilizado en el módulo de usuarios, verifique por favor","warning");

    }
  })
}

//ENVIANDO AL BOTON DELETE
$('#tabla_sucursal').on('click','.eliminar',function(){
  var data = tbl_sucursal.row($(this).parents('tr')).data();

  if(tbl_sucursal.row(this).child.isShown()){
      var data = tbl_sucursal.row(this).data();
  }
  Swal.fire({
    title: 'Desea eliminar la sucursal con el nombre: '+data.sucrusal+'?',
    text: "Una vez aceptado la sucursal sera eliminado!!!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_sucursal(data.id_sucursal);
    }
  })
})