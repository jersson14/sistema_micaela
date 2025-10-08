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

// LISTAR CHOFERES VENCIDOS
var tbl_choferes_dash;
var alertasActivas = [];
var currentModalIndex = 0;
var modalMostradoHoy = false;

function listar_choferes_vencidos() {
  tbl_choferes_dash = $("#tabla_choferes_vencidos").DataTable({
    "ordering": false,
    "processing": true,
    responsive: true,
    "searching": false,
    "bPaginate": false,
    "ajax": {
      "url": "../controller/choferes/controlador_listar_choferes_vencidos.php",
      type: 'POST'
    },
    "columns": [
      { "data": "id_chofer" },
      { "data": null, render: data => `${data.tipo_documen} - ${data.nro_doc}` },
      { "data": "nombres_apellidos" },
      { "data": "marca_vehiculo" },
      { "data": "nro_licencia" },
      { "data": "clase_categoria" },
      { "data": "fecha_venci" },
      {
        "defaultContent": "<button class='mostrar btn btn-warning btn-sm'><i class='fa fa-eye'></i> Ver</button>"
      },
      {
        "data": "estado",
        render: function (data) {
          return data === 'ACTIVO'
            ? '<span class="badge bg-success">ACTIVO</span>'
            : '<span class="badge bg-danger">INACTIVO</span>';
        }
      }
    ],
    "language": idioma_espanol,
    select: false,

    "createdRow": function (row, data) {
      const diasRestantes = calcularDiasRestantes(data.fecha_venci);

      if (diasRestantes < 0) {
        $(row).css("background-color", "#ff4444"); // Rojo fuerte - VENCIDO
      } else if (diasRestantes <= 7) {
        $(row).css("background-color", "#ff6666"); // Rojo medio
      } else if (diasRestantes <= 14) {
        $(row).css("background-color", "#ff9999"); // Rojo suave
      } else if (diasRestantes <= 30) {
        $(row).css("background-color", "#ffcc99"); // Naranja
      } else if (diasRestantes <= 60) {
        $(row).css("background-color", "#ffff99"); // Amarillo
      } else {
        $(row).css("background-color", "#b3ffb3"); // Verde suave
      }
    },

    "drawCallback": function () {
      if (tbl_choferes_dash) {
        alertasActivas = [];
        currentModalIndex = 0; // Resetear índice
        
        tbl_choferes_dash.rows().every(function () {
          var data = this.data();
          var diasRestantes = calcularDiasRestantes(data.fecha_venci);

          // 🔹 Alertas automáticas usando RANGOS en lugar de días exactos
          // Vencido, ≤7 días, ≤14 días, ≤30 días, ≤60 días
          if (diasRestantes < 0 || diasRestantes <= 60) {
            alertasActivas.push({
              nombre: data.nombres_apellidos,
              fecha: data.fecha_venci,
              dias: diasRestantes,
              licencia: data.nro_licencia,
              documento: `${data.tipo_documen} - ${data.nro_doc}`,
              vehiculo: data.marca_vehiculo,
              categoria: data.clase_categoria
            });
          }
        });

        // 🔹 Ordenar alertas por urgencia (vencidas primero, luego por días restantes)
        alertasActivas.sort((a, b) => {
          if (a.dias < 0 && b.dias >= 0) return -1;
          if (a.dias >= 0 && b.dias < 0) return 1;
          return a.dias - b.dias;
        });

        console.log("Total de alertas encontradas:", alertasActivas.length);
        
        // 🔹 Mostrar alertas automáticas (sin restricción de "una por día")
        if (alertasActivas.length > 0) {
          setTimeout(function() {
            mostrarAlertasSecuenciales();
          }, 500);
        }
      }
    }
  });

  // Evento manual para botón "Ver"
  $('#tabla_choferes_vencidos').on('click', '.mostrar', function () {
    var data = tbl_choferes_dash.row($(this).parents('tr')).data();
    mostrarAlertaVencimiento({
      nombre: data.nombres_apellidos,
      fecha: data.fecha_venci,
      dias: calcularDiasRestantes(data.fecha_venci),
      licencia: data.nro_licencia,
      documento: `${data.tipo_documen} - ${data.nro_doc}`,
      vehiculo: data.marca_vehiculo,
      categoria: data.clase_categoria
    });
  });
}

// 🔹 Calcula días restantes desde hoy hasta la fecha de vencimiento
function calcularDiasRestantes(fechaVenci) {
  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  
  // Convertir la fecha desde formato DD-MM-YYYY o YYYY-MM-DD
  let venc;
  if (fechaVenci.includes('-')) {
    const partes = fechaVenci.split('-');
    
    // Si el formato es DD-MM-YYYY
    if (partes[0].length <= 2) {
      venc = new Date(partes[2], partes[1] - 1, partes[0]);
    } 
    // Si el formato es YYYY-MM-DD
    else {
      venc = new Date(fechaVenci);
    }
  } else {
    venc = new Date(fechaVenci);
  }
  
  venc.setHours(0, 0, 0, 0);
  const diff = venc - hoy;
  return Math.ceil(diff / (1000 * 60 * 60 * 24));
}

// 🔹 Muestra alertas de forma secuencial
function mostrarAlertasSecuenciales() {
  if (currentModalIndex < alertasActivas.length) {
    mostrarAlertaVencimiento(alertasActivas[currentModalIndex]);
  }
}

// 🔹 Muestra un modal mejorado tipo alerta
function mostrarAlertaVencimiento(data) {
  let mensaje = "";
  let icono = "";
  let colorHeader = "";
  let nivelAlerta = "";
  let colorBorde = "";

  if (data.dias < 0) {
    // VENCIDO
    nivelAlerta = "LICENCIA VENCIDA";
    icono = "🚫";
    colorHeader = "#8B0000";
    colorBorde = "#8B0000";
    mensaje = `La licencia del conductor <strong>${data.nombre}</strong> YA VENCIÓ hace <strong>${Math.abs(data.dias)}</strong> día(s).`;
  } else if (data.dias <= 7) {
    // 1 semana o menos
    nivelAlerta = "ALERTA CRÍTICA";
    icono = "🔴";
    colorHeader = "#dc3545";
    colorBorde = "#dc3545";
    mensaje = `La licencia del conductor <strong>${data.nombre}</strong> CADUCARÁ en <strong>1 semana</strong> (<strong>${data.dias}</strong> día(s)).`;
  } else if (data.dias <= 14) {
    // 2 semanas
    nivelAlerta = "ALERTA URGENTE";
    icono = "⚠️";
    colorHeader = "#fd7e14";
    colorBorde = "#fd7e14";
    mensaje = `La licencia del conductor <strong>${data.nombre}</strong> CADUCARÁ en <strong>2 semanas</strong>.`;
  } else if (data.dias <= 30) {
    // 1 mes
    nivelAlerta = "ALERTA IMPORTANTE";
    icono = "⚡";
    colorHeader = "#ffc107";
    colorBorde = "#ffc107";
    mensaje = `La licencia del conductor <strong>${data.nombre}</strong> CADUCARÁ en <strong>1 mes</strong>.`;
  } else if (data.dias <= 60) {
    // 2 meses
    nivelAlerta = "AVISO PREVENTIVO";
    icono = "ℹ️";
    colorHeader = "#17a2b8";
    colorBorde = "#17a2b8";
    mensaje = `La licencia del conductor <strong>${data.nombre}</strong> CADUCARÁ en <strong>2 meses</strong>.`;
  }

  // Construir HTML del modal mejorado
  const modalHTML = `
    <div class="modal-header" style="background: linear-gradient(135deg, ${colorHeader} 0%, ${colorHeader}dd 100%); color:white; border-radius: 15px 15px 0 0; border: none;">
      <h4 class="modal-title w-100 text-center mb-0" style="font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
        <span style="font-size: 2rem;">${icono}</span><br>
        ${nivelAlerta}
      </h4>
    </div>
    <div class="modal-body" style="padding: 30px;">
      <div class="alert-content" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div class="text-center mb-4">
          <div style="font-size: 4rem; margin-bottom: 15px;">${icono}</div>
          <p style="font-size: 18px; line-height: 1.8; color: #333; margin-bottom: 20px;">${mensaje}</p>
        </div>
        
        <div class="info-grid" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
          <div class="info-row" style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
            <strong style="color: #555;">📋 Documento:</strong>
            <span style="color: #333;">${data.documento}</span>
          </div>
          <div class="info-row" style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
            <strong style="color: #555;">🚗 Vehículo:</strong>
            <span style="color: #333;">${data.vehiculo}</span>
          </div>
          <div class="info-row" style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
            <strong style="color: #555;">🪪 Licencia N°:</strong>
            <span style="color: #333;">${data.licencia}</span>
          </div>
          <div class="info-row" style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
            <strong style="color: #555;">📊 Categoría:</strong>
            <span style="color: #333;">${data.categoria}</span>
          </div>
          <div class="info-row" style="display: flex; justify-content: space-between; padding: 10px 0;">
            <strong style="color: #555;">📅 Fecha Vencimiento:</strong>
            <span style="color: #333; font-weight: bold;">${data.fecha}</span>
          </div>
        </div>

        ${data.dias < 0 ? 
          '<div class="mt-3 p-3" style="background-color: #fff3cd; border-left: 4px solid #856404; border-radius: 5px;"><strong>⚠️ Acción Requerida:</strong> El conductor NO debe operar vehículos hasta renovar su licencia.</div>' : 
          '<div class="mt-3 p-3" style="background-color: #d1ecf1; border-left: 4px solid #0c5460; border-radius: 5px;"><strong>📌 Recomendación:</strong> Coordinar la renovación de la licencia con anticipación.</div>'
        }
      </div>
    </div>
    <div class="modal-footer justify-content-center" style="border-top: 2px solid ${colorBorde}; padding: 20px;">
      <button type="button" class="btn btn-lg" style="background-color: ${colorHeader}; color: white; padding: 12px 30px; font-weight: bold; border-radius: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.2); transition: all 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" onclick="cerrarYSiguiente()">
        <i class="fas fa-check-circle"></i> Entendido
      </button>
    </div>
  `;

  $("#modal_ver .modal-content").html(modalHTML);
  $("#modal_ver").modal({ backdrop: 'static', keyboard: false });
  $("#modal_ver").modal('show');
}

// 🔹 Función para cerrar modal y mostrar siguiente alerta
function cerrarYSiguiente() {
  currentModalIndex++;
  console.log("Cerrando modal. Índice actual:", currentModalIndex, "Total alertas:", alertasActivas.length);
  
  $("#modal_ver").modal('hide');
  
  // Esperar a que el modal se cierre completamente antes de mostrar el siguiente
  $('#modal_ver').on('hidden.bs.modal', function (e) {
    // Remover el event listener para evitar múltiples llamadas
    $(this).off('hidden.bs.modal');
    
    if (currentModalIndex < alertasActivas.length) {
      console.log("Mostrando siguiente alerta...");
      // Delay más largo para asegurar que el modal anterior se cerró
      setTimeout(function() {
        mostrarAlertaVencimiento(alertasActivas[currentModalIndex]);
      }, 600);
    } else {
      console.log("No hay más alertas por mostrar");
      currentModalIndex = 0; // Resetear para la próxima vez
    }
  });
}