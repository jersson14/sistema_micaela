var tbl_servicios;
function listar_servicios(){
  tbl_servicios = $("#tabla_servicios").DataTable({
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
          "url":"../controller/servicios/controlador_listar_servicios.php",
          type:'POST'
      },
      dom: 'Bfrtip', 
   
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: function() {
            return "LISTA DE SERVICIOS"
          },
          title: function() {
            return "LISTA DE SERVICIOS"
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
            return "LISTA DE SERVICIOS"
          },
          title: function() {
            return "LISTA DE SERVICIOS"
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
            return "LISTA DE SERVICIOS"
          },
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5, 6,7] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      "columns": [
          {"defaultContent":""},
          {"data":"nombre"},
          {
              "data":"costo",
              "render": function(data, type, row) {
                  return `<span style="font-weight: bold; font-size: 18px;">S/ ${data}</span>`;
              }
          },
          {"data":"descripcion"},
         
          {"data":"fecha_formateada"},
          {"data":"fecha_formateada2"},
          {"data":"USUARIO"},
           {
              "data":"estado",
              "render": function(data, type, row){
                  if(data === 'ACTIVO'){
                      return '<span class="badge bg-success">ACTIVO</span>';
                  } else {
                      return '<span class="badge bg-danger">INACTIVO</span>';
                  }
              }   
          },
          {
              "defaultContent": `
                  <button class='editar btn btn-primary btn-sm' title='Editar datos de servicio'>
                    <i class='fa fa-edit'></i> Editar
                  </button>
                  <button class='eliminar btn btn-danger btn-sm' title='Eliminar datos de servicio'>
                    <i class='fa fa-trash'></i> Eliminar
                  </button>
              `
          }
      ],

    "language":idioma_espanol,
    select: true
});
tbl_servicios.on('draw.td',function(){
  var PageInfo = $("#tabla_servicios").DataTable().page.info();
  tbl_servicios.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}



//EDITAR
$('#tabla_servicios').on('click','.editar',function(){
  var data = tbl_servicios.row($(this).parents('tr')).data();

  if(tbl_servicios.row(this).child.isShown()){
      var data = tbl_servicios.row(this).data();
  }
  $("#modal_editar").modal('show');
  document.getElementById('txt_id_servicio').value=data.id_servicio;
  document.getElementById('txt_servicio_editar').value=data.nombre;
  document.getElementById('txt_costo_editar').value=data.costo;
  document.getElementById('txt_descripcion_editar').value=data.descripcion;
  document.getElementById('select_estado_editar').value=data.estado;

})

function AbrirRegistro(){
  $("#modal_registro").modal({backdrop:'static',keyboard:false})
  $("#modal_registro").modal('show');
}

function Registrar_Servicio(){
  let serv = document.getElementById('txt_servicio').value;
  let cost = document.getElementById('txt_costo').value;
  let desc = document.getElementById('txt_descripcion').value;
  let idusu = document.getElementById('txtprincipalid').value;


  if(serv.length==0||cost.length==0||idusu.length==0){
      return Swal.fire("Mensaje de Advertencia","Tiene campos vacios","warning");
  }
  $.ajax({
    "url":"../controller/servicios/controlador_registro_servicios.php",
    type:'POST',
    data:{
      serv:serv,
      cost:cost,
      desc:desc,
      idusu:idusu
    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Nuevo servicio registrado con el nombre: <b>"+serv+"</b>","success").then((value)=>{
          tbl_servicios.ajax.reload();
          document.getElementById('txt_servicio').value="";
          document.getElementById('txt_costo').value="";
          document.getElementById('txt_descripcion').value="";
          $("#modal_registro").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","El nombre del servicio que intentas registrar ya se encuentra en la base de datos, revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo el registro","error");

    }
  })
}
function Modificar_Obra_Social(){
  let id = document.getElementById('txt_id_servicio').value;
  let serv = document.getElementById('txt_servicio_editar').value;
  let cost = document.getElementById('txt_costo_editar').value;
  let desc = document.getElementById('txt_descripcion_editar').value;
  let estado = document.getElementById('select_estado_editar').value;
  let idusu = document.getElementById('txtprincipalid').value;

  if(id.length==0||serv.length==0||cost.length==0||estado.length==0||idusu.length==0){
    return Swal.fire("Mensaje de Advertencia","Tiene campos vacios, revise por favor","warning");
  }
  $.ajax({
    "url":"../controller/servicios/controlador_modificar_servicios.php",
    type:'POST',
    data:{
      id:id,
      serv:serv,
      cost:cost,
      desc:desc,
      estado:estado,
      idusu:idusu
    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Datos actualizados correctamente!!!","success").then((value)=>{
          tbl_servicios.ajax.reload();
        $("#modal_editar").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","El servicio que intenta actualizar ya se encuentra en la base de datos, ingrese otro nombre o revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo la actualización","error");

    }
  })
}

//ELIMINAR
function Eliminar_Servicios(id){
  $.ajax({
    "url":"../controller/servicios/controlador_eliminar_servicio.php",
    type:'POST',
    data:{
      id:id
    }
  }).done(function(resp){
    if(resp>0){
        Swal.fire("Mensaje de Confirmación","Se elimino el servicio con exito","success").then((value)=>{
          tbl_servicios.ajax.reload();

        });
    }else{
      return Swal.fire("Mensaje de Advetencia","No se puede eliminar el servicio por que esta siendo utilizado en el módulo de COMPROBANTES, verifique por favor","warning");

    }
  })
}

//ENVIANDO AL BOTON DELETE
$('#tabla_servicios').on('click','.eliminar',function(){
  var data = tbl_servicios.row($(this).parents('tr')).data();

  if(tbl_servicios.row(this).child.isShown()){
      var data = tbl_servicios.row(this).data();
  }
  Swal.fire({
    title: 'Desea eliminar el servicio con el nombre: '+data.nombre+'?',
    text: "Una vez aceptado el servicio sera eliminado!!!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_Servicios(data.id_servicio);
    }
  })
})