var tbl_clientes;
function listar_clientes() {
  tbl_clientes = $("#tabla_clientes").DataTable({
    "ordering": true,
    "bLengthChange": true,
    "searching": { "regex": false },
    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    "pageLength": 10,
    "destroy": true,
    pagingType: 'full_numbers',
    scrollCollapse: true,
    responsive: true,
    "async": false,
    "processing": true,
    "ajax": {
      "url": "../controller/clientes/controlador_listar_clientes.php",
      type: 'POST'
    },
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'excelHtml5',
        text: '<i class="fas fa-file-excel"></i> Excel',
        titleAttr: 'Exportar a Excel',
        filename: function() {
          return "LISTA DE CLIENTES";
        },
        title: function() {
            return "LISTA DE CLIENTES";
        },
        className: 'btn btn-success btn-sm',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6],
          format: {
            body: function(data, row, column, node) {
              if (column === 0) {
                return row + 1;
              }
              // Limpiar HTML
              var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
              
              // Formatear columna 1 (Documento): agregar guion entre tipo y número
              if (column === 1 && cleanData) {
                // Buscar patrón: TEXTO seguido de NÚMEROS (sin espacio)
                cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
              }
              
              return cleanData;
            }
          }
        }
      },
      {
        extend: 'pdfHtml5',
        text: '<i class="fas fa-file-pdf"></i> PDF',
        titleAttr: 'Exportar a PDF',
        filename: function() {
            return "LISTA DE CLIENTES";
        },
        title: function() {
            return "LISTA DE CLIENTES";
        },
        className: 'btn btn-danger btn-sm',
        orientation: 'landscape',
        pageSize: 'A4',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6],
          format: {
            body: function(data, row, column, node) {
              if (column === 0) {
                return row + 1;
              }
              // Limpiar HTML
              var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
              
              // Formatear columna 1 (Documento): agregar guion entre tipo y número
              if (column === 1 && cleanData) {
                // Buscar patrón: TEXTO seguido de NÚMEROS (sin espacio)
                cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
              }
              
              return cleanData;
            }
          }
        }
      },
      {
        extend: 'print',
        text: '<i class="fa fa-print"></i> Imprimir',
        titleAttr: 'Imprimir',
        title: function() {
            return "LISTA DE CLIENTES";
        },
        className: 'btn btn-info btn-sm',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6],
          format: {
            body: function(data, row, column, node) {
              if (column === 0) {
                return row + 1;
              }
              // Limpiar HTML
              var cleanData = data.replace ? data.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim() : data;
              
              // Formatear columna 1 (Documento): agregar guion entre tipo y número
              if (column === 1 && cleanData) {
                // Buscar patrón: TEXTO seguido de NÚMEROS (sin espacio)
                cleanData = cleanData.replace(/([A-Za-zÁÉÍÓÚáéíóú\s]+)(\d+)/g, "$1 - $2");
              }
              
              return cleanData;
            }
          }
        }
      }
    ],

    "columns": [
      { "defaultContent": "" },
       {
        "data": null,
        "render": function(data, type, row) {
            return '<strong>' + row.tipo_documento + '</strong><br>' + row.nro_documento;
        }
    },

      { 
        "data": "nombre_completo",
        "render": function(data, type, row) {
          return "<strong>" + data + "</strong>";
        }
      },
      { "data": "procedencia" },
      { "data": "celular" },
      { "data": "direccion" },
      { "data": "total_viajes" },
      { "data": "fecha_ultimo_viaje" },

      {
        "defaultContent": "<button class='mostrar btn btn-success btn-sm' title='Ver datos'><i class='fa fa-eye'></i> Mostrar</button> <button class='editar btn btn-primary btn-sm' title='Editar datos de cliente'><i class='fa fa-edit'></i> Editar</button> <button class='eliminar btn btn-danger btn-sm' title='Eliminar datos de cliente'><i class='fa fa-trash'></i> Eliminar</button>"
      }
    ],

    "language": idioma_espanol,
    select: true
  });

  tbl_clientes.on('draw.td', function() {
    var PageInfo = $("#tabla_clientes").DataTable().page.info();
    tbl_clientes.column(0, { page: 'current' }).nodes().each(function(cell, i) {
      cell.innerHTML = i + 1 + PageInfo.start;
    });
  });
}

$('#tabla_clientes').on('click', '.editar', async function () {
  let data = tbl_clientes.row($(this).parents('tr')).data();
  if (tbl_clientes.row(this).child.isShown()) {
    data = tbl_clientes.row(this).data();
  }

  $("#modal_editar").modal('show');
  
  // Desactivar selects temporalmente para evitar interacción mientras carga
  document.getElementById('txt_idcliente').value=data.id_cliente;
  document.getElementById('select_tipo_doc').value=data.tipo_documento;
  document.getElementById('txt_nro_doc').value=data.nro_documento;
  document.getElementById('txt_nombres').value=data.nombre_completo;
  document.getElementById('txt_procedencia').value=data.procedencia;
  document.getElementById('txt_celular').value=data.celular;
  document.getElementById('txt_direccion').value=data.direccion;
  document.getElementById('txt_email').value=data.email;
});


$('#tabla_clientes').on('click','.mostrar',function(){
  var data = tbl_clientes.row($(this).parents('tr')).data();

  if(tbl_clientes.row(this).child.isShown()){
      var data = tbl_clientes.row(this).data();
  }
  $("#modal_mostrar").modal('show');
  document.getElementById('txt_idcliente_mostrar').value=data.id_cliente;
  document.getElementById('select_tipo_doc_mostrar').value=data.tipo_documento;
  document.getElementById('txt_nro_doc_mostrar').value=data.nro_documento;
  document.getElementById('txt_nombres_mostrar').value=data.nombre_completo;
  document.getElementById('txt_proce_mostrar').value=data.procedencia;
  document.getElementById('txt_celular_mostrar').value=data.celular;
  document.getElementById('txt_direccion_mostrar').value=data.direccion;
  document.getElementById('txt_email_mostrar').value=data.email;
  document.getElementById('txt_total_viaje_mostrar').value=data.total_viajes;
  document.getElementById('txt_ulti_viaje_mostrar').value=data.fecha_ultimo_viaje;
  document.getElementById('txt_fecha_regis').value=data.fecha_formateada;
  document.getElementById('txt_fecha_actualiza').value=data.fecha_formateada2;

})


function AbrirRegistro(){
  $("#modal_registro").modal({backdrop:'static',keyboard:false})
  $("#modal_registro").modal('show');
}

//EDITAR CLIENTE
function Modificar_Cliente(){
  let id = document.getElementById('txt_idcliente').value;
  let tipo = document.getElementById('select_tipo_doc').value;
  let nro = document.getElementById('txt_nro_doc').value;
  let nombre = document.getElementById('txt_nombres').value;
  let proce = document.getElementById('txt_procedencia').value;
  let celular = document.getElementById('txt_celular').value;
  let direccion = document.getElementById('txt_direccion').value;
  let email = document.getElementById('txt_email').value;


  if(tipo.length==0||nro.length==0||nombre.length==0||celular.length==0||id.length==0){
    return Swal.fire("Mensaje de Advertencia","Tiene campos vacios, revise los campos obligatorios","warning");
  }
  $.ajax({
    "url":"../controller/clientes/controlador_modificar_clientes.php",
    type:'POST',
    data:{
      id:id,
      tipo:tipo,
      nro:nro,
      nombre:nombre,
      proce:proce,
      celular:celular,
      direccion:direccion,
      email:email
    }
  }).done(function(resp){
    if(resp>0){
      if(resp==1){
        Swal.fire("Mensaje de Confirmación","Datos actualizados del cliente: <b>"+nombre+"</b>","success").then((value)=>{
          tbl_clientes.ajax.reload();
        $("#modal_editar").modal('hide');
        });
      }else{
        Swal.fire("Mensaje de Advertencia","El DNI del cliente ingresado ya se encuentra en la base de datos, revise por favor, no se puede insertar un DNI repetido","warning");
      }
    }else{
      return Swal.fire("Mensaje de Error","No se completo la actualización","error");

    }
  })
}

//ELIMINAR AREAS
function Eliminar_cliente(id){
  $.ajax({
    "url":"../controller/clientes/controlador_eliminar_clientes.php",
    type:'POST',
    data:{
      id:id
    }
  }).done(function(resp){
    if(resp>0){
        Swal.fire("Mensaje de Confirmación","Se elimino el cliente con exito","success").then((value)=>{
          tbl_clientes.ajax.reload();

        });
    }else{
      return Swal.fire("Mensaje de Advetencia","No se puede eliminar este cliente por que tiene expedientes registrados, verifique por favor","warning");

    }
  })
}

//ENVIANDO AL BOTON DELETE
$('#tabla_clientes').on('click','.eliminar',function(){
  var data = tbl_clientes.row($(this).parents('tr')).data();

  if(tbl_clientes.row(this).child.isShown()){
      var data = tbl_clientes.row(this).data();
  }
  Swal.fire({
    title: 'Desea eliminar al cliente con el nombre: '+data.nombre_completo+'?',
    text: "Una vez aceptado el cliente sera eliminado!!!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
        Eliminar_cliente(data.id_cliente);
    }
  })
})
