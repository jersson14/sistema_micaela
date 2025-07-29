var tbl_choferes;
function listar_choferes(){
  tbl_choferes = $("#tabla_choferes").DataTable({
    pagingType: 'full_numbers',
    scrollCollapse: true,
    responsive: true,
      "ordering":false,   
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
          "url":"../controller/choferes/controlador_listar_choferes.php",
          type:'POST'
      },
      dom: 'Bfrtip',       
    
      buttons: [ 
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          titleAttr: 'Exportar a Excel',
          filename: "LISTA DE CONDUCTORES",
          title: "LISTA DE CONDUCTORES",
          className: 'btn btn-excel',
          exportOptions: {
            columns: [ 1, 3, 4, 5, 6, 7, 8,9] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          titleAttr: 'Exportar a PDF',
          filename: "LISTA DE CONDUCTORES",
          title: "LISTA DE CONDUCTORES",
          className: 'btn btn-pdf',
          orientation: 'landscape', // <-- Establece la orientación en horizontal
          pageSize: 'A4', // <-- Especifica el tamaño de la página
          exportOptions: {
            columns: [ 1, 3, 4, 5, 6, 7, 8,9] // Exportar solo hasta la columna 'estado'
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Imprimir',
          titleAttr: 'Imprimir',
          title: "LISTA DE CONDUCTORES",
          className: 'btn btn-print',
          exportOptions: {
            columns: [ 1, 3, 4, 5, 6, 7, 8,9] // Exportar solo hasta la columna 'estado'
          }
        }
      ],
   "columns":[
    {"defaultContent":""},
    {
        "data": null,
        "render": function(data, type, row) {
            return '<strong>' + row.tipo_documen + '</strong><br>' + row.nro_doc;
        }
    },
    {
        "data": "foto",
        "render": function(data, type, row) {
            if (data == 'controller/usuario/fotos/' || data == '' || data == null) {
                return '<img src="../img/vacio.png" class="img img-responsive" style="width:40px">';
            } else {
                return '<img src="../' + data + '" class="img img-responsive" style="width:40px">';
            }
        }
    },
    {"data":"nombres_apellidos"},
    {"data":"celular"},
    {"data":"procedencia"},
    {"data":"direccion"},
    {"data":"marca_vehiculo"},
    {"data":"placa_vehiculo"},
    {"data":"fecha_formateada"},
    {
        "data":"estado",
        "render": function(data, type, row) {
            if (data == 'ACTIVO') {
                return '<span class="badge bg-success">ACTIVO</span>';
            } else {
                return '<span class="badge bg-danger">INACTIVO</span>';
            }
        }
    },
    {
        "defaultContent":
            "<button class='ver btn btn-success btn-sm' title='Ver datos'><i class='fa fa-eye'></i> Mostrar</button> " +
            "<button class='editar btn btn-primary btn-sm' title='Editar datos de área'><i class='fa fa-edit'></i> Editar</button> " +
            "<button class='eliminar btn btn-danger btn-sm' title='Eliminar datos de área'><i class='fa fa-trash'></i> Eliminar</button>"
    }
],
    "language":idioma_espanol,
    select: true
});
tbl_choferes.on('draw.td',function(){
  var PageInfo = $("#tabla_choferes").DataTable().page.info();
  tbl_choferes.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}
function AbrirRegistro(){
  $("#modal_registro").modal({backdrop:'static',keyboard:false})
  $("#modal_registro").modal('show');
}

//ABRIR MODAL EDITAR
$('#tabla_choferes').on('click','.editar',function(){
  var data = tbl_choferes.row($(this).parents('tr')).data();

  if(tbl_choferes.row(this).child.isShown()){
      var data = tbl_choferes.row(this).data();
  }
  $("#modal_editar").modal('show');
  document.getElementById('id_usuario').value=data.id_usuario;
  document.getElementById('txt_dni_editar').value=data.dni_usuario;
  document.getElementById('txt_nomb_editar').value=data.usu_nombre;
  document.getElementById('txt_apelli_editar').value=data.usu_apellido;

  document.getElementById('txt_correo_editar').value=data.usu_email;
  document.getElementById('txt_tele_editar').value=data.usu_telefono;
  document.getElementById('txt_direc_editar').value=data.usu_direccion;
  document.getElementById('txt_foto_actual').value=data.usu_foto;

  var imgElement = document.getElementById('preview2');
  if (imgElement) {
      console.log('Data:', data); // Depuración
      console.log('Image URL:', data.usu_foto); // Verificar URL
  
      if (data.usu_foto && data.usu_foto.trim() !== '') {
          imgElement.src = "../" + data.usu_foto; // Ruta relativa
      } else {
          imgElement.src = '../img/vacio.png'; // Ruta por defecto
      }
  
      imgElement.style.display = 'block'; // Mostrar siempre la imagen
  
      // Manejar errores de carga
      imgElement.onerror = function () {
          console.error("Error al cargar la imagen desde la ruta: " + imgElement.src);
          imgElement.src = '../img/vacio.png'; // Ruta por defecto
      };
  } else {
      console.error('Elemento img con id preview2 no encontrado');
  }
  

document.getElementById('txt_usu_editar').value=data.usu_usuario;
  $("#txt_roles_editar").select2().val(data.id_role).trigger('change.select2');
  $("#txt_sucursal_editar").select2().val(data.id_sucursal).trigger('change.select2');


})



$('#tabla_choferes').on('click','.contra',function(){
  var data = tbl_choferes.row($(this).parents('tr')).data();

  if(tbl_choferes.row(this).child.isShown()){
      var data = tbl_choferes.row(this).data();
  }
  $("#modal_contra").modal('show');
  document.getElementById('txt_idusuario_contra').value=data.id_usuario;

})
$('#tabla_choferes').on('click','.desactivar',function(){
  var data = tbl_choferes.row($(this).parents('tr')).data();

  if(tbl_choferes.row(this).child.isShown()){
      var data = tbl_choferes.row(this).data();
  }
    Swal.fire({
      title: 'Desea desactivar al usuario '+data.USUARIO+'?',
      text: "Una vez desactivado el usuario no tendra acceso al sistema",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#005CA5',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, Desactivar'
    }).then((result) => {
      if (result.isConfirmed) {
        Modificar_Estatus_Usuario(parseInt(data.id_usuario),'INACTIVO',data.USUARIO);
      }
    })

})


$('#tabla_choferes').on('click','.activar',function(){
  var data = tbl_choferes.row($(this).parents('tr')).data();

  if(tbl_choferes.row(this).child.isShown()){
      var data = tbl_choferes.row(this).data();
  }
    Swal.fire({
      title: 'Desea activar al usuario '+data.USUARIO+'?',
      text: "Una vez activado el usuario tendra acceso al sistema",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#005CA5',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, Activar'
    }).then((result) => {
      if (result.isConfirmed) {
        Modificar_Estatus_Usuario(parseInt(data.id_usuario),'ACTIVO',data.USUARIO);
      }
    })

})


function Modificar_Estatus_Usuario(id,estatus,user){
  let esta=estatus;
  if(esta==="INACTIVO"){
    esta="Desactivo";
  }
  $.ajax({
    "url":"../controller/usuario/controlador_modificar_usuario_estatus.php",
    type:'POST',
    data:{
      id:id,
      estatus:estatus
    }
  }).done(function(resp){
    if(resp>0){
        Swal.fire("Mensaje de Confirmación","Se "+esta+" con exito El Usuario "+user,"success").then((value)=>{
          tbl_choferes.ajax.reload();
        });
    }else{
      return Swal.fire("Mensaje de Error","No se completo la actualización","error");

    }
  })
}

//REGISTROS DE USUARIOS
function Registrar_Usuario(){

  //DATOS DEL DOCENTE
  let dni = document.getElementById('txt_dni').value;
  let nombre = document.getElementById('txt_nomb').value;
  let apelli = document.getElementById('txt_apelli').value;
  let correo = document.getElementById('txt_correo').value;
  let tele = document.getElementById('txt_tele').value;
  let dire = document.getElementById('txt_direc').value;
  let foto = document.getElementById('txt_foto').value;


  //DATOS DEL USUARIO
  let usu = document.getElementById('txt_usu').value;
  let contra = document.getElementById('txt_contra').value;
  let rol = document.getElementById('txt_roles').value;
  let sucu = document.getElementById('txt_sucursal').value;

  
  if(dni.length==0|| apelli.length==0||correo.length==0||dire.length==0||nombre.length==0){
    return Swal.fire("Mensaje de Advertencia","Tiene campos en el registro del docente","warning");
  }
  if(usu.length==0||contra.length==0||rol.length==0||sucu.length==0){
    return Swal.fire("Mensaje de Advertencia","Los datos del usuario son oblgatorios","warning");
  }

    let extension = foto.split('.').pop();
    let nombrefoto="";
    let f = new Date();
    if(foto.length>0){
      nombrefoto="IMG"+f.getDate()+"-"+(f.getMonth()+1)+"-"+f.getFullYear()+"-"+f.getHours()+"-"+f.getMilliseconds()+"."+extension;
    }
    //CONDICIONANDO LOS CAMPOS VACIOS


    let formData = new FormData();
    let fotoobj = $("#txt_foto")[0].files[0];

    formData.append("dni",dni);
    formData.append("nombre",nombre);
    formData.append("apelli",apelli);
    formData.append("correo",correo);
    formData.append("tele",tele);
    formData.append("dire",dire);
    formData.append("nombrefoto",nombrefoto);
    formData.append("foto",fotoobj);

    formData.append("usu",usu);
    formData.append("contra",contra);
    formData.append("rol",rol);
    formData.append("sucu",sucu);
    $.ajax({
      url:"../controller/usuario/controlador_registro_usuario.php",
      type:'POST',
      data:formData,
      contentType:false,
      processData:false,
      success:function(resp){
        if(resp.length>0){
        if(resp==1){
          Swal.fire("Mensaje de Confirmación","Se registro correctamente al usuario con el DNI N° <b>"+dni+"</b>","success").then((value)=>{
            // Limpiar todos los campos
            document.getElementById('txt_dni').value = "";
            document.getElementById('txt_nomb').value = "";
            document.getElementById('txt_apelli').value = "";
            document.getElementById('txt_correo').value = "";
            document.getElementById('txt_tele').value = "";
            document.getElementById('txt_direc').value = "";


            // Limpiar la vista previa de la imagen
            document.getElementById('preview').src = '#';
            document.getElementById('preview').alt = 'Vista previa';

            document.getElementById('txt_usu').value = "";
            document.getElementById('txt_contra').value = "";

            // Cerrar el modal
            $("#modal_registro").modal('hide');
            tbl_choferes.ajax.reload();

          });
            }else{
            Swal.fire("Mensaje de Advertencia","El DNI o el USUARIO que intentas registrar ya se encuentra en la base de datos, revise por favor","warning");
            }
        }else{
          Swal.fire("Mensaje de Advertencia","No se pudo registrar al usuario","warning");
        }
      }
    });
}



function Modificar_Usuario(){

  //DATOS DEL DOCENTE
  let id = document.getElementById('id_usuario').value;
  let dni = document.getElementById('txt_dni_editar').value;
  let nombre = document.getElementById('txt_nomb_editar').value;
  let apelli = document.getElementById('txt_apelli_editar').value;
  let correo = document.getElementById('txt_correo_editar').value;
  let tele = document.getElementById('txt_tele_editar').value;
  let dire = document.getElementById('txt_direc_editar').value;
  let fotoactual = document.getElementById('txt_foto_actual').value;
  let foto = document.getElementById('txt_foto_editar').value;


  //DATOS DEL USUARIO
  let usu = document.getElementById('txt_usu_editar').value;
  let rol = document.getElementById('txt_roles_editar').value;
  let sucu = document.getElementById('txt_sucursal_editar').value;


  
  if(id.length==0||dni.length==0|| apelli.length==0||correo.length==0||dire.length==0||nombre.length==0){
    return Swal.fire("Mensaje de Advertencia","Tiene campos en el registro del docente","warning");
  }
  if(usu.length==0||rol.length==0||sucu.length==0){
    return Swal.fire("Mensaje de Advertencia","Los datos del usuario son oblgatorios","warning");
  }

    let extension = foto.split('.').pop();
    let nombrefoto="";
    let f = new Date();
    if(foto.length>0){
      nombrefoto="IMG"+f.getDate()+"-"+(f.getMonth()+1)+"-"+f.getFullYear()+"-"+f.getHours()+"-"+f.getMilliseconds()+"."+extension;
    }
    //CONDICIONANDO LOS CAMPOS VACIOS


    let formData = new FormData();
    let fotoobj = $("#txt_foto_editar")[0].files[0];

    formData.append("id",id);
    formData.append("dni",dni);
    formData.append("nombre",nombre);
    formData.append("apelli",apelli);
    formData.append("correo",correo);
    formData.append("tele",tele);
    formData.append("dire",dire);
    formData.append("fotoactual",fotoactual);
    formData.append("nombrefoto",nombrefoto);
    formData.append("foto",fotoobj);

    formData.append("usu",usu);
    formData.append("rol",rol);
    formData.append("sucu",sucu);
    $.ajax({
      url:"../controller/usuario/controlador_modificar_usuario.php",
      type:'POST',
      data:formData,
      contentType:false,
      processData:false,
      success:function(resp){
        if(resp.length>0){
        if(resp==1){
          Swal.fire("Mensaje de Confirmación","Se actualizo correctamente al usuario con el DNI N° <b>"+dni+"</b>","success").then((value)=>{
            // Cerrar el modal
            $("#modal_editar").modal('hide');
            tbl_choferes.ajax.reload();
            document.getElementById('txt_foto_editar').value="";

          });
            }else{
            Swal.fire("Mensaje de Advertencia","El DNI o USUARIO que intentas actualizar ya se encuentra en la base de datos, revise por favor","warning");
            }
        }else{
          Swal.fire("Mensaje de Advertencia","No se pudo actualizar al usuario","warning");
        }
      }
    });
}


