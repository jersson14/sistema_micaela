var tbl_roles;
function listar_roles(){
  tbl_roles = $("#tabla_roles").DataTable({
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
          "url":"../controller/roles/controlador_listar_roles.php",
          type:'POST'
      },
      dom: 'Bfrtip', 
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: function() {
            return "LISTA DE ROLES";
          },
          title: function() {
            return "LISTA DE ROLES";
          },
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: function() {
            return "LISTA DE ROLES";
          },
          title: function() {
            return "LISTA DE ROLES";
          },
          className: 'btn btn-pdf',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: function() {
            return "LISTA DE ROLES";
          },
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 2, 3, 4, 5] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
      
      "columns":[
        {"defaultContent":""},
        {"data":"rol"},
        {"data":"descripcion"},

        {"data":"estado",
            render: function(data,type,row){
                    if(data=='ACTIVO'){
                    return '<span class="badge bg-success">ACTIVO</span>';
                    }else{
                    return '<span class="badge bg-danger">INACTIVO</span>';
                    }
            }   
        },
        {"data":"fecha_formateada"},
        {"data":"fecha_formateada2"},
        {
          "defaultContent": "<button class='editar btn btn-primary btn-sm' title='Editar datos de área'><i class='fa fa-edit'></i> Editar</button> <button class='eliminar btn btn-danger btn-sm' title='Eliminar datos de área'><i class='fa fa-trash'></i> Eliminar</button>"
        },
            ],

    "language":idioma_espanol,
    select: true
  });

  tbl_roles.on('draw.td', function(){
    var PageInfo = $("#tabla_roles").DataTable().page.info();
    tbl_roles.column(0, {page: 'current'}).nodes().each(function(cell, i){
      cell.innerHTML = i + 1 + PageInfo.start;
    });
  });
}

$('#tabla_roles').on('click','.editar',function(){
  var data = tbl_roles.row($(this).parents('tr')).data();

  if(tbl_roles.row(this).child.isShown()){
      var data = tbl_roles.row(this).data();
  }
  $("#modal_editar").modal('show');
  document.getElementById('txt_id_rol').value=data.id_role;
  document.getElementById('txt_nombre_editar').value=data.rol;
  document.getElementById('txt_descripcion_editar').value=data.descripcion;
  document.getElementById('select_estado_editar').value=data.estado;
})

function AbrirRegistro(){
  $("#modal_registro").modal({backdrop:'static',keyboard:false})
  $("#modal_registro").modal('show');
}

function Registrar_Rol(){
  let rol = document.getElementById('txt_nombre_rol').value;
  let descripcion = document.getElementById('txt_descripcion').value;
  let estado = document.getElementById('txt_estado').value;
  let fecha = document.getElementById('txt_fecha_registro').value;

  if(rol.length==0){
      return Swal.fire("Mensaje de Advertencia","Ingrese nombre del rol es obligatorio","warning");
  }
  $.ajax({
    "url":"../controller/roles/controlador_registro_rol.php",
    type:'POST',
    data:{
        rol:rol,
        descripcion:descripcion,
        estado:estado,
        fecha:fecha
    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Nueva Rol registrado con el nombre: "+rol,"success").then((value)=>{
            tbl_roles.ajax.reload();
            document.getElementById('txt_nombre_rol').value="";
            document.getElementById('txt_descripcion').value="";
            $("#modal_registro").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","El rol que desea registrar ya se encuentra en la base de datos, revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo el registro","error");

    }
  })
}
function Modificar_Rol(){
  let id = document.getElementById('txt_id_rol').value;
  let rol = document.getElementById('txt_nombre_editar').value;
  let descripcion = document.getElementById('txt_descripcion_editar').value;
  let estado = document.getElementById('select_estado_editar').value;

  if(rol.length==0 || id.length==0|| estado.length==0){
      return Swal.fire("Mensaje de Advertencia","Tiene campos vacios, llene todo los campos obligatorios","warning");
  }
  $.ajax({
    "url":"../controller/roles/controlador_modificar_roles.php",
    type:'POST',
    data:{
      id:id,
      rol:rol,
      descripcion:descripcion,
      estado:estado
    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Datos actualizados del rol: "+rol,"success").then((value)=>{
            tbl_roles.ajax.reload();
            $("#modal_editar").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","El rol ingresado ya se encuentra en la base de datos, revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo la actualización","error");

    }
  })
}

//ELIMINAR AREAS
function Eliminar_rol(id){
  $.ajax({
    "url":"../controller/roles/controlador_eliminar_rol.php",
    type:'POST',
    data:{
      id:id
    }
  }).done(function(resp){
    if(resp>0){
        Swal.fire("Mensaje de Confirmación","Se elimino el rol con exito","success").then((value)=>{
          tbl_roles.ajax.reload();

        });
    }else{
      return Swal.fire("Mensaje de Advetencia","No se puede eliminar esta rol por que esta siendo utilizado en el módulo de usuarios, verifique por favor","warning");

    }
  })
}

//ENVIANDO AL BOTON DELETE
$('#tabla_roles').on('click','.eliminar',function(){
  var data = tbl_roles.row($(this).parents('tr')).data();

  if(tbl_roles.row(this).child.isShown()){
      var data = tbl_roles.row(this).data();
  }
  Swal.fire({
    title: 'Desea eliminar el rol con el nombre: '+data.rol+'?',
    text: "Una vez aceptado el rol sera eliminado!!!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_rol(data.id_role);
    }
  })
})