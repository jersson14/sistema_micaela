var tbl_empresa;
function listar_empresa(){
  tbl_empresa = $("#tabla_empresa").DataTable({
    "ordering":false,   
    "processing": true,
    responsive: true,
    "searching": false ,
    "bPaginate": false,
    "ajax":{
        "url":"../controller/empresa/controlador_listar_empresa.php",
        type:'POST'
    },
   
     
      "columns":[
        {"defaultContent":""},
        {"data":"logo",
            render: function(data,type,row){
                return '<img src="../'+data+'" class="img img-responsive" style="width: 80px">';
            }   
         },
        {"data":"nombre"},
        {"data":"email"},
        {"data":"codigo"},
        {"data":"telefono"},
        {"data":"direccion"},
        {"defaultContent":"<button class='editar btn btn-primary  btn-sm' title='Editar datos de empresa'><i class='fa fa-edit'></i> Editar</button>&nbsp;<button class='foto btn btn-warning btn-sm' title='Cambiar logo'><i class='fa fa-image'></i> Cambiar foto</button>"},

    ],

    "language":idioma_espanol,
    select: true
});
tbl_empresa.on('draw.td',function(){
  var PageInfo = $("#tabla_empresa").DataTable().page.info();
  tbl_empresa.column(0, {page: 'current'}).nodes().each(function(cell, i){
    cell.innerHTML = i + 1 + PageInfo.start;
  });
});
}
$('#tabla_empresa').on('click','.editar',function(){
    var data = tbl_empresa.row($(this).parents('tr')).data();
  
    if(tbl_empresa.row(this).child.isShown()){
        var data = tbl_empresa.row(this).data();
    }
    $("#modal_editar").modal('show');
    document.getElementById('txt_id_empresa').value=data.id_empresa;
    document.getElementById('txt_nombre').value=data.nombre;
    document.getElementById('txt_razon').value=data.razon_social;
    document.getElementById('txt_nombre_co').value=data.nombre_comercial;
    document.getElementById('txt_tipo_doc').value=data.tipo_documento;
    document.getElementById('txt_nro_doc').value=data.numero_documento;
    document.getElementById('txt_email').value=data.email;
    document.getElementById('txt_codigo').value=data.codigo;
    document.getElementById('txt_telefono').value=data.telefono;
    document.getElementById('txt_direccion').value=data.direccion;

    document.getElementById('txt_ubigeo').value=data.ubigeo;
    document.getElementById('txt_urbanizacion').value=data.urbanizacion;
    document.getElementById('txt_distrito').value=data.distrito;
    document.getElementById('txt_provincia').value=data.provincia;
    document.getElementById('txt_departamento').value=data.departamento;
    document.getElementById('txt_codigo_pais').value=data.codigo_pais;
    document.getElementById('txt_usuario_sol').value=data.usuario_sol;
    document.getElementById('txt_clave_sol').value=data.clave_sol;


  })
  function Modificar_empresa(){
    let id = document.getElementById('txt_id_empresa').value;
    let nom = document.getElementById('txt_nombre').value;
    let raz = document.getElementById('txt_razon').value;
    let nomco = document.getElementById('txt_nombre_co').value;
    let tipo_doc = document.getElementById('txt_tipo_doc').value;
    let nro_doc = document.getElementById('txt_nro_doc').value;
    let email = document.getElementById('txt_email').value;
    let codi = document.getElementById('txt_codigo').value;
    let tele = document.getElementById('txt_telefono').value;
    let dire = document.getElementById('txt_direccion').value;

    let ubi = document.getElementById('txt_ubigeo').value;
    let urb = document.getElementById('txt_urbanizacion').value;
    let dis = document.getElementById('txt_distrito').value;
    let pro = document.getElementById('txt_provincia').value;
    let dep = document.getElementById('txt_departamento').value;
    let codpa = document.getElementById('txt_codigo_pais').value;
    let ususol = document.getElementById('txt_usuario_sol').value;
    let passol = document.getElementById('txt_clave_sol').value;


    if(id.length==0 || nom.length==0 || raz.length==0 || nomco.length==0 || tipo_doc.length==0 || nro_doc.length==0|| tele.length==0 || dire.length==0 ){
        return Swal.fire("Mensaje de Advertencia","Tiene campos vacios","warning");
    }
    if(validar_email(email)){
  
    }else{
      return Swal.fire("Mensaje de Advertencia","El formato de Email es incorrecto","warning");
  
    }
    $.ajax({
      "url":"../controller/empresa/controlador_modificar_empresa.php",
      type:'POST',
      data:{
          id:id,
          nom:nom,
          raz:raz,
          nomco:nomco,
          tipo_doc:tipo_doc,
          nro_doc:nro_doc,
          email:email,
          codi:codi,
          tele:tele,
          dire:dire,
          ubi:ubi,
          urb:urb,
          dis:dis,
          pro:pro,
          dep:dep,
          codpa:codpa,
          ususol:ususol,
          passol:passol


      }
    }).done(function(resp){
      if(resp>0){
          Swal.fire("Mensaje de Confirmación","Datos de la empreza Actualizado","success").then((value)=>{
          tbl_empresa.ajax.reload();
          $("#modal_editar").modal('hide');

          });
        
      }else{
        return Swal.fire("Mensaje de Error","No se completo el proceso","error");
  
      }
    })
  }
  function validar_email(email) {
    var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email) ? true : false;
  }
  $('#tabla_empresa').on('click','.foto',function(){
    var data = tbl_empresa.row($(this).parents('tr')).data();
  
    if(tbl_empresa.row(this).child.isShown()){
        var data = tbl_empresa.row(this).data();
    }
    $("#modal_editar_foto").modal('show');
    document.getElementById('txt_idempresa_foto').value=data.id_empresa;
    document.getElementById('lb_empresa').innerHTML=data.nombre;
    document.getElementById('fotoactual').value=data.logo;
  
  })
  function Modificar_Foto_Empresa(){
    let id = document.getElementById("txt_idempresa_foto").value
    let foto = document.getElementById("txt_foto").value
    let fotoactual = document.getElementById("fotoactual").value
  
    if(id.length==0 || foto.length==0){
      return Swal.fire("Mensaje de Advertencia","Tiene campos vacios","warning");
  }
  
      let extension = foto.split('.').pop();
      let nombrefoto="";
      let f = new Date();
      if(foto.length>0){
        nombrefoto="IMG"+f.getDate()+"-"+(f.getMonth()+1)+"-"+f.getFullYear()+"-"+f.getHours()+"-"+f.getMilliseconds()+"."+extension;
      }
      let formData = new FormData();
      let fotoobj = $("#txt_foto")[0].files[0];
  
      formData.append("id",id);
      formData.append("nombrefoto",nombrefoto);
      formData.append("fotoactual",fotoactual);
      formData.append("foto",fotoobj);
      $.ajax({
        url:"../controller/empresa/controlador_empresa_modificar_foto.php",
        type:'POST',
        data:formData,
        contentType:false,
        processData:false,
        success:function(resp){
          if(resp.length>0){
            Swal.fire("Mensaje de Confirmación","Foto actualizada","success").then((value)=>{
              $("#modal_editar_foto").modal('hide');
              tbl_empresa.ajax.reload();
              document.getElementById('txt_foto').value="";
  
            });
          }else{
            Swal.fire("Mensaje de Advertencia","No se pudo actualizar la foto","warning");
          }
        }
      });
  }