var tbl_rutas;
function listar_rutas(){
  tbl_rutas = $("#tabla_rutas").DataTable({
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
          "url":"../controller/rutas/controlador_listar_rutas.php",
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
          {"data":"descripcion"},
         
          {"data":"fecha_formateada"},
          {"data":"fecha_formateada2"},
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
tbl_rutas.on('draw.td',function(){
  var PageInfo = $("#tabla_rutas").DataTable().page.info();
  tbl_rutas.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}



//EDITAR
$('#tabla_rutas').on('click','.editar',function(){
  var data = tbl_rutas.row($(this).parents('tr')).data();

  if(tbl_rutas.row(this).child.isShown()){
      var data = tbl_rutas.row(this).data();
  }
  $("#modal_editar").modal('show');
  document.getElementById('txt_id_ruta').value=data.idrutas;
  document.getElementById('txt_ruta_editar').value=data.nombre;
  document.getElementById('txt_descripcion_editar').value=data.descripcion;
  document.getElementById('select_estado_editar').value=data.estado;

})

function AbrirRegistro(){
  $("#modal_registro").modal({backdrop:'static',keyboard:false})
  $("#modal_registro").modal('show');
}

function Registrar_Ruta(){
  let nom = document.getElementById('txt_nombre_ruta').value;
  let desc = document.getElementById('txt_descripcion').value;


  if(nom.length==0){
      return Swal.fire("Mensaje de Advertencia","El nombre de la ruta es un campo obligatorio","warning");
  }
  $.ajax({
    "url":"../controller/rutas/controlador_registro_rutas.php",
    type:'POST',
    data:{
      nom:nom,
      desc:desc
    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Nueva ruta registrado con el nombre: <b>"+nom+"</b>","success").then((value)=>{
          tbl_rutas.ajax.reload();
          document.getElementById('txt_nombre_ruta').value="";
          document.getElementById('txt_descripcion').value="";
          $("#modal_registro").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","El nombre de la ruta que intentas registrar ya se encuentra en la base de datos, revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo el registro","error");

    }
  })
}
function Modificar_Ruta(){
  let id = document.getElementById('txt_id_ruta').value;
  let nom = document.getElementById('txt_ruta_editar').value;
  let desc = document.getElementById('txt_descripcion_editar').value;
  let estado = document.getElementById('select_estado_editar').value;

  if(id.length==0||nom.length==0){
    return Swal.fire("Mensaje de Advertencia","Tiene campos vacios, revise por favor","warning");
  }
  $.ajax({
    "url":"../controller/rutas/controlador_modificar_ruta.php",
    type:'POST',
    data:{
      id:id,
      nom:nom,
      desc:desc,
      estado:estado    
    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Datos actualizados correctamente!!!","success").then((value)=>{
          tbl_rutas.ajax.reload();
        $("#modal_editar").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","EL nombre de la ruta que intenta actualizar ya se encuentra en la base de datos, ingrese otro nombre o revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo la actualización","error");

    }
  })
}

//ELIMINAR
function Eliminar_Ruta(id){
  $.ajax({
    "url":"../controller/rutas/controlador_eliminar_ruta.php",
    type:'POST',
    data:{
      id:id
    }
  }).done(function(resp){
    if(resp>0){
        Swal.fire("Mensaje de Confirmación","Se elimino la ruta con exito","success").then((value)=>{
          tbl_rutas.ajax.reload();

        });
    }else{
      return Swal.fire("Mensaje de Advetencia","No se puede eliminar la ruta por que esta siendo utilizado en los módulo de ENCOMIENDAS Y SALIDAS DIARIAS, verifique por favor","warning");

    }
  })
}

//ENVIANDO AL BOTON DELETE
$('#tabla_rutas').on('click','.eliminar',function(){
  var data = tbl_rutas.row($(this).parents('tr')).data();

  if(tbl_rutas.row(this).child.isShown()){
      var data = tbl_rutas.row(this).data();
  }
  Swal.fire({
    title: 'Desea eliminar la ruta con el nombre: '+data.nombre+'?',
    text: "Una vez aceptado la ruta sera eliminado!!!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_Ruta(data.idrutas);
    }
  })
})