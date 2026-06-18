var tbl_tipos_pagos;
function listar_tipo_pago(){
  tbl_tipos_pagos = $("#tabla_tipo_pagos").DataTable({
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
          "url":"../controller/tipo_pago/controlador_listar_tipo_pago.php",
          type:'POST'
      },
      dom: 'Bfrtip', 
     buttons: [
  {
    extend: "excelHtml5",
    text: '<i class="fas fa-file-excel"></i> Excel',
    titleAttr: "Exportar a Excel",
    filename: "LISTA DE TIPOS DE PAGO",
    title: "LISTA DE TIPOS DE PAGO",
    className: "btn btn-success btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3], // Ajusta según tus columnas, SIN acciones
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
    filename: "LISTA DE TIPOS DE PAGO",
    title: "LISTA DE TIPOS DE PAGO",
    className: "btn btn-danger btn-sm",
    orientation: "landscape",
    pageSize: "A4",
    exportOptions: {
      columns: [0, 1, 2, 3],
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
    extend: "print",
    text: '<i class="fa fa-print"></i> Imprimir',
    titleAttr: "Imprimir",
    title: "LISTA DE TIPOS DE PAGO",
    className: "btn btn-info btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3],
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

      
      "columns":[
        {"defaultContent":""},
        {"data":"tipo_pago"},
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

  tbl_tipos_pagos.on('draw.td', function(){
    var PageInfo = $("#tabla_tipo_pagos").DataTable().page.info();
    tbl_tipos_pagos.column(0, {page: 'current'}).nodes().each(function(cell, i){
      cell.innerHTML = i + 1 + PageInfo.start;
    });
  });
}

$('#tabla_tipo_pagos').on('click','.editar',function(){
  var data = tbl_tipos_pagos.row($(this).parents('tr')).data();

  if(tbl_tipos_pagos.row(this).child.isShown()){
      var data = tbl_tipos_pagos.row(this).data();
  }
  $("#modal_editar").modal('show');

  document.getElementById('txt_idtipo_pago').value=data.id_tipo_pago;
  document.getElementById('txt_nombre_tipopago_editar').value=data.tipo_pago;
  document.getElementById('txt_descripcion_editar').value=data.descripcion;
  document.getElementById('select_estado_editar').value=data.estado;
})

function AbrirRegistro(){
  $("#modal_registro").modal({backdrop:'static',keyboard:false})
  $("#modal_registro").modal('show');
}

function Registrar_Sucursal(){

  let tipopa = document.getElementById('txt_nombre_tipopago').value;
  let desc = document.getElementById('txt_descripcion').value;

  if(tipopa.length==0){
      return Swal.fire("Mensaje de Advertencia","Tiene campos vacios, ingrese los campos obligatorios","warning");
  }
  $.ajax({
    "url":"../controller/tipo_pago/controlador_registro_tipo_pago.php",
    type:'POST',
    data:{

      tipopa:tipopa,
      desc:desc
    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Nuevo tipo de pago registrado con el nombre: "+tipopa,"success").then((value)=>{
          tbl_tipos_pagos.ajax.reload();
          document.getElementById('txt_nombre_tipopago').value="";
          document.getElementById('txt_descripcion').value="";

        $("#modal_registro").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","El tipo de pago que desea registrar ya se encuentra en la base de datos, revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo el registro","error");

    }
  })
}
function Modificar_Sucursal(){
  let id = document.getElementById('txt_idtipo_pago').value;
  let tipopa = document.getElementById('txt_nombre_tipopago_editar').value;
  let desc = document.getElementById('txt_descripcion_editar').value;
  let esta = document.getElementById('select_estado_editar').value;

  if(tipopa.length==0||id.length==0){
      return Swal.fire("Mensaje de Advertencia","Tiene campos vacios, por favor revise","warning");
  }
  $.ajax({
    "url":"../controller/tipo_pago/controlador_modificar_tipo_pago.php",
    type:'POST',
    data:{
      id:id,
      tipopa:tipopa,
      desc:desc,
      esta:esta
    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Datos actualizados del tipo de pago: "+tipopa,"success").then((value)=>{
          tbl_tipos_pagos.ajax.reload();
        $("#modal_editar").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","El tipo de pago ingresado ya se encuentra en la base de datos, revise por favor","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo la actualización","error");

    }
  })
}

//ELIMINAR AREAS
function Eliminar_tipo_pago(id){
  $.ajax({
    "url":"../controller/tipo_pago/controlador_eliminar_tipo_pago.php",
    type:'POST',
    data:{
      id:id
    }
  }).done(function(resp){
    if(resp>0){
        Swal.fire("Mensaje de Confirmación","Se elimino el tipo de pago con exito","success").then((value)=>{
          tbl_tipos_pagos.ajax.reload();

        });
    }else{
      return Swal.fire("Mensaje de Advetencia","No se puede eliminar este tipo de pagos por que esta siendo utilizado en otros módulos, verifique por favor","warning");

    }
  })
}

//ENVIANDO AL BOTON DELETE
$('#tabla_tipo_pagos').on('click','.eliminar',function(){
  var data = tbl_tipos_pagos.row($(this).parents('tr')).data();

  if(tbl_tipos_pagos.row(this).child.isShown()){
      var data = tbl_tipos_pagos.row(this).data();
  }
  Swal.fire({
    title: 'Desea eliminar el tipo de pago con el nombre: '+data.tipo_pago+'?',
    text: "Una vez aceptado el tipo de pago sera eliminado!!!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_tipo_pago(data.id_tipo_pago);
    }
  })
})