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
            "<button class='mostrar btn btn-success btn-sm' title='Ver datos'><i class='fa fa-eye'></i> Mostrar</button> " +
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
  document.getElementById('id_chofer').value=data.id_chofer;
  document.getElementById('select_tipo_documento_editar').value=data.tipo_documen;
  document.getElementById('txt_dni_editar').value=data.nro_doc;
  document.getElementById('txt_nomb_editar').value=data.nombres_apellidos;
  document.getElementById('txt_celu1_editar').value=data.celular;

  document.getElementById('txt_celu2_editar').value=data.celular_2;
  document.getElementById('txt_procedencia_editar').value=data.procedencia;
  document.getElementById('txt_direc_editar').value=data.direccion;
  document.getElementById('txt_foto_actual').value=data.foto;

  var imgElement = document.getElementById('preview2');
  if (imgElement) {
      console.log('Data:', data); // Depuración
      console.log('Image URL:', data.foto); // Verificar URL
  
      if (data.foto && data.foto.trim() !== '') {
          imgElement.src = "../" + data.foto; // Ruta relativa
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
  

  document.getElementById('txt_marca_editar').value=data.marca_vehiculo;
  document.getElementById('txt_placa_editar').value=data.placa_vehiculo;
  document.getElementById('txt_clase_categoria_editar').value=data.clase_categoria;
  document.getElementById('txt_nro_licencia_editar').value=data.nro_licencia;
  document.getElementById('txt_fecha_expira_editar').value=data.fecha_vencimiento_licencia;
  document.getElementById('select_estado_editar').value=data.estado;


})

$('#tabla_choferes').on('click','.mostrar',function(){
  var data = tbl_choferes.row($(this).parents('tr')).data();

  if(tbl_choferes.row(this).child.isShown()){
      var data = tbl_choferes.row(this).data();
  }
  $("#modal_mostrar").modal('show');
  document.getElementById('select_tipo_documento_mostrar').value=data.tipo_documen;
  document.getElementById('txt_dni_mostrar').value=data.nro_doc;
  document.getElementById('txt_nomb_mostrar').value=data.nombres_apellidos;
  document.getElementById('txt_celu1_mostrar').value=data.celular;

  document.getElementById('txt_celu2_mostrar').value=data.celular_2;
  document.getElementById('txt_procedencia_mostrar').value=data.procedencia;
  document.getElementById('txt_direc_mostrar').value=data.direccion;
  document.getElementById('txt_foto_actual').value=data.foto;

  var imgElement = document.getElementById('preview3');
  if (imgElement) {
      console.log('Data:', data); // Depuración
      console.log('Image URL:', data.foto); // Verificar URL
  
      if (data.foto && data.foto.trim() !== '') {
          imgElement.src = "../" + data.foto; // Ruta relativa
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
  

  document.getElementById('txt_marca_mostrar').value=data.marca_vehiculo;
  document.getElementById('txt_placa_mostrar').value=data.placa_vehiculo;
  document.getElementById('txt_clase_categoria_mostrar').value=data.clase_categoria;
  document.getElementById('txt_nro_licencia_mostrar').value=data.nro_licencia;
  document.getElementById('txt_fecha_expira_mostrar').value=data.fecha_vencimiento_licencia;
  document.getElementById('select_estado_mostrar').value=data.estado;


})




//REGISTROS DE CHOFERES
function Registrar_Choferes(){

  //DATOS DEL DOCENTE
  let tipo_doc = document.getElementById('select_tipo_documento').value;
  let dni = document.getElementById('txt_dni').value;
  let dni2 = document.getElementById('txt_dni2').value;
  let nom_ape = document.getElementById('txt_nomb').value;
  let celu = document.getElementById('txt_celu1').value;
  let celu2 = document.getElementById('txt_celu2').value;
  let proc = document.getElementById('txt_procedencia').value;
  let dire = document.getElementById('txt_direc').value;
  let foto = document.getElementById('txt_foto').value;


  //DATOS DEL CARRO
  let marca = document.getElementById('txt_marca').value;
  let placa = document.getElementById('txt_placa').value;
  let clase_cate = document.getElementById('txt_clase_categoria').value;
  let nro_lice = document.getElementById('txt_nro_licencia').value;
  let fec_ven = document.getElementById('txt_fecha_expira').value;
  let idusuario = document.getElementById('txtprincipalid').value;
  
  if(tipo_doc.length==0|| nom_ape.length==0||celu.length==0||marca.length==0||placa.length==0||clase_cate.length==0||nro_lice.length==0||fec_ven.length==0){
    return Swal.fire("Mensaje de Advertencia","Tiene campos vacios en el formulario, revise por favor","warning");
  }
   // Validar documento según tipo
    let documentoFinal = '';
    if (tipo_doc === 'DNI') {
        if (!dni) {
            return Swal.fire("Mensaje de Advertencia", "El campo DNI es obligatorio", "warning");
        }
        documentoFinal = dni;
    } else {
        if (!dni2) {
            return Swal.fire("Mensaje de Advertencia", "El campo de documento es obligatorio", "warning");
        }
        documentoFinal = dni2;
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

    formData.append("tipo_doc",tipo_doc);
    formData.append("documentoFinal",documentoFinal);
    formData.append("nom_ape",nom_ape);
    formData.append("celu",celu);
    formData.append("celu2",celu2);
    formData.append("proc",proc);
    formData.append("dire",dire);
    formData.append("nombrefoto",nombrefoto);
    formData.append("foto",fotoobj);

    formData.append("marca",marca);
    formData.append("placa",placa);
    formData.append("clase_cate",clase_cate);
    formData.append("nro_lice",nro_lice);
    formData.append("fec_ven",fec_ven);
    formData.append("idusuario",idusuario);

    $.ajax({
      url:"../controller/choferes/controlador_registro_choferes.php",
      type:'POST',
      data:formData,
      contentType:false,
      processData:false,
      success:function(resp){
        if(resp.length>0){
        if(resp==1){
          Swal.fire("Mensaje de Confirmación","Se registro correctamente al chofer con el DNI N° <b>"+documentoFinal+"</b>","success").then((value)=>{
            // Limpiar todos los campos
            document.getElementById('txt_dni').value = "";
            document.getElementById('txt_dni2').value = "";
            document.getElementById('txt_nomb').value = "";
            document.getElementById('txt_celu1').value = "";
            document.getElementById('txt_celu2').value = "";
            document.getElementById('txt_procedencia').value = "";
            document.getElementById('txt_direc').value = "";

            document.getElementById('txt_marca').value = "";
            document.getElementById('txt_placa').value = "";
            document.getElementById('txt_clase_categoria').value = "";
            document.getElementById('txt_nro_licencia').value = "";
            document.getElementById('txt_fecha_expira').value = "";

            // Limpiar la vista previa de la imagen
            document.getElementById('preview').src = '#';
            document.getElementById('preview').alt = 'Vista previa';


            // Cerrar el modal
            $("#modal_registro").modal('hide');
            tbl_choferes.ajax.reload();

          });
            }else{
            Swal.fire("Mensaje de Advertencia","El DNI que intentas registrar ya se encuentra en la base de datos, revise por favor","warning");
            }
        }else{
          Swal.fire("Mensaje de Advertencia","No se pudo registrar al usuario","warning");
        }
      }
    });
}



function Modificar_Choferes(){

  //DATOS DEL DOCENTE
  let id = document.getElementById('id_chofer').value;
  let dni = document.getElementById('txt_dni_editar').value;
  let nom_ape = document.getElementById('txt_nomb_editar').value;
  let celu1 = document.getElementById('txt_celu1_editar').value;
  let celu2 = document.getElementById('txt_celu2_editar').value;
  let proc = document.getElementById('txt_procedencia_editar').value;
  let dire = document.getElementById('txt_direc_editar').value;
  let fotoactual = document.getElementById('txt_foto_actual').value;
  let foto = document.getElementById('txt_foto_editar').value;


  //DATOS DEL CARRO
  let marca = document.getElementById('txt_marca_editar').value;
  let placa = document.getElementById('txt_placa_editar').value;
  let clase_cate = document.getElementById('txt_clase_categoria_editar').value;
  let nro_lice = document.getElementById('txt_nro_licencia_editar').value;
  let fec_ven = document.getElementById('txt_fecha_expira_editar').value;
  let esta = document.getElementById('select_estado_editar').value;
  let idusuario = document.getElementById('txtprincipalid').value;


  
  if(id.length==0||dni.length==0|| nom_ape.length==0||celu1.length==0||marca.length==0||placa.length==0||clase_cate.length==0||nro_lice.length==0||fec_ven.length==0){
    return Swal.fire("Mensaje de Advertencia","Los campos obligatorios siempre deben ir llenos","warning");
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
    formData.append("nom_ape",nom_ape);
    formData.append("celu1",celu1);
    formData.append("celu2",celu2);
    formData.append("proc",proc);
    formData.append("dire",dire);
    formData.append("fotoactual",fotoactual);
    formData.append("nombrefoto",nombrefoto);
    formData.append("foto",fotoobj);

    formData.append("marca",marca);
    formData.append("placa",placa);
    formData.append("clase_cate",clase_cate);
    formData.append("nro_lice",nro_lice);
    formData.append("fec_ven",fec_ven);
    formData.append("esta",esta);
    formData.append("idusuario",idusuario);

    $.ajax({
      url:"../controller/choferes/controlador_modificar_choferes.php",
      type:'POST',
      data:formData,
      contentType:false,
      processData:false,
      success:function(resp){
        if(resp.length>0){
        if(resp==1){
          Swal.fire("Mensaje de Confirmación","Se actualizo correctamente el chofer con el DNI N° <b>"+dni+"</b>","success").then((value)=>{
            // Cerrar el modal
            $("#modal_editar").modal('hide');
            tbl_choferes.ajax.reload();
            document.getElementById('txt_foto_editar').value="";

          });
            }else{
            Swal.fire("Mensaje de Advertencia","El DNI que intentas actualizar ya se encuentra en la base de datos, revise por favor","warning");
            }
        }else{
          Swal.fire("Mensaje de Advertencia","No se pudo actualizar al usuario","warning");
        }
      }
    });
}


//ELIMINAR AREAS
function Eliminar_chofer(id){
  $.ajax({
    "url":"../controller/choferes/controlador_eliminar_chofer.php",
    type:'POST',
    data:{
      id:id
    }
  }).done(function(resp){
    if(resp>0){
        Swal.fire("Mensaje de Confirmación","Se elimino el chofer con exito","success").then((value)=>{
          tbl_choferes.ajax.reload();

        });
    }else{
      return Swal.fire("Mensaje de Advetencia","No se puede eliminar esta CHOFER por que esta siendo utilizado en otros módulos como encomienda y salidas diarias, verifique por favor","warning");

    }
  })
}

//ENVIANDO AL BOTON DELETE
$('#tabla_choferes').on('click','.eliminar',function(){
  var data = tbl_choferes.row($(this).parents('tr')).data();

  if(tbl_choferes.row(this).child.isShown()){
      var data = tbl_choferes.row(this).data();
  }
  Swal.fire({
    title: 'Desea eliminar al chofer con el nombre: '+data.nombres_apellidos+'?',
    text: "Una vez aceptado el chofer sera eliminado!!!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar'
  }).then((result) => {
    if (result.isConfirmed) {
      Eliminar_chofer(data.id_chofer);
    }
  })
})