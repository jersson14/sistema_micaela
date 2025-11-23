function Iniciar_Sesion() {
  recuerdame();
  let usu = document.getElementById("txt_usuario").value;
  let con = document.getElementById("txt_contra").value;
  if (usu.length == 0 || con.length == 0) {
    return Swal.fire({
      icon: "warning",
      title: "Mensaje de Advertencia",
      text: "Llene todo los campos de la sesión",
      heightAuto: false,
    });
  }
  $.ajax({
    url: "controller/usuario/controlador_iniciar_sesion.php",
    type: "POST",
    data: {
      u: usu,
      c: con,
    },
  }).done(function (resp) {
    let response = JSON.parse(resp);
    
    // Compatibilidad con respuesta antigua y nueva
    if (response.success === false) {
      return Swal.fire({
        icon: "error",
        title: "Mensaje de Error",
        text: response.message || "Usuario o Contraseña Incorrectos",
        heightAuto: false,
      });
    }
    
    let data = response.data || response;
    
    if (data.length > 0 || (response.success && response.data)) {
      if (response.success) {
        data = [response.data];
      }
      
      if (data[0][7] == "INACTIVO") {
        return Swal.fire({
          icon: "warning",
          title: "Mensaje de Advertencia",
          text: "El usuario: " + usu + " se encuentra inactivo",
          heightAuto: false,
        });
      }
      
      // Debug: ver qué datos tenemos
      console.log("Response completo:", response);
      console.log("Data:", data);
      console.log("Data[0]:", data[0]);
      
      // Verificar que data[0] existe
      if (!data[0]) {
        console.error("ERROR: data[0] está vacío o undefined");
        return Swal.fire({
          icon: "error",
          title: "Error de Sesión",
          text: "No se pudieron obtener los datos del usuario",
          heightAuto: false,
        });
      }
      
      // Guardar tokens JWT en localStorage
      if (response.tokens) {
        localStorage.setItem('access_token', response.tokens.access_token);
        localStorage.setItem('refresh_token', response.tokens.refresh_token);
        localStorage.setItem('token_expires', Date.now() + (response.tokens.expires_in * 1000));
      }
      
      $.ajax({
        url: "controller/usuario/controlador_crear_sesion.php",
        type: "POST",
        data: {
          idusuario: data[0][0],
          DNIusuario: data[0][1],
          nombres: data[0][2], //solo nombre
          solonombres: data[0][4], // nombres completos
          usuario: data[0][7],
          rol: data[0][9],
          foto: data[0][15], // foto de usuario
          foto_empresa: data[0][17], // foto de empresa
          razon: data[0][18], // razón de la empresa
          nombre_rol: data[0][19], // nombre de rol
          sucursal: data[0][20], // nombre de sucursal
        },
      }).done(function (resp) {
        console.log("Respuesta crear sesión:", resp);
        
        try {
          let sessionResponse = JSON.parse(resp);
          
          if (sessionResponse.success) {
            console.log("Sesión creada exitosamente:", sessionResponse);
            
            let timerInterval;
            Swal.fire({
              title: "Bienvenido al Sistema",
              html: "Seras redireccionado en <b></b> milliseconds.",
              icon: "success",
              timer: 1200,
              timerProgressBar: true,
              heightAuto: false,
              didOpen: () => {
                Swal.showLoading();
                const b = Swal.getHtmlContainer().querySelector("b");
                timerInterval = setInterval(() => {
                  b.textContent = Swal.getTimerLeft();
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
              },
            }).then((result) => {
              if (result.dismiss === Swal.DismissReason.timer) {
                location.reload();
              }
            });
          } else {
            console.error("Error al crear sesión:", sessionResponse.message);
            Swal.fire({
              icon: "error",
              title: "Error de Sesión",
              text: sessionResponse.message || "No se pudo crear la sesión",
              heightAuto: false,
            });
          }
        } catch (e) {
          console.error("Error al parsear respuesta de sesión:", e, resp);
          Swal.fire({
            icon: "error",
            title: "Error de Sesión",
            text: "Error al procesar la respuesta del servidor",
            heightAuto: false,
          });
        }
      }).fail(function(xhr, status, error) {
        console.error("Error AJAX crear sesión:", {xhr, status, error});
        Swal.fire({
          icon: "error",
          title: "Error de Conexión",
          text: "No se pudo conectar con el servidor para crear la sesión",
          heightAuto: false,
        });
      });
    } else {
      Swal.fire({
        icon: "error",
        title: "Mensaje de Error",
        text: "Usuario o Contraseña Incorrectos",
        heightAuto: false,
      });
    }
  });
}

function recuerdame() {
  if (rmcheck.checked && usuarioInput.value != "" && passInput.value != "") {
    localStorage.usuario = usuarioInput.value;
    localStorage.pass = passInput.value;
    localStorage.checkbox = rmcheck.value;
  } else {
    localStorage.usuario = "";
    localStorage.pass = "";
    localStorage.checkbox = "";
  }
}

var tbl_usuario;
function listar_usuario() {
  tbl_usuario = $("#tabla_usuario").DataTable({
    pagingType: "full_numbers",
    scrollCollapse: true,
    responsive: true,
    ordering: false,
    bLengthChange: true,
    searching: { regex: false },
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "All"],
    ],
    pageLength: 10,
    destroy: true,
    pagingType: "full_numbers",
    scrollCollapse: true,
    responsive: true,
    async: false,
    processing: true,
    ajax: {
      url: "../controller/usuario/controlador_listar_usuario.php",
      type: "POST",
    },
    dom: "Bfrtip",

  buttons: [
  {
    extend: "excelHtml5",
    text: '<i class="fas fa-file-excel"></i> Excel',
    titleAttr: "Exportar a Excel",
    filename: "LISTA DE USUARIOS",
    title: "LISTA DE USUARIOS",
    className: "btn btn-success btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6], // Ajusta según tus columnas, SIN acciones
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
    filename: "LISTA DE USUARIOS",
    title: "LISTA DE USUARIOS",
    className: "btn btn-danger btn-sm",
    orientation: "landscape",
    pageSize: "A4",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6],
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
    title: "LISTA DE USUARIOS",
    className: "btn btn-info btn-sm",
    exportOptions: {
      columns: [0, 1, 2, 3, 4, 5, 6],
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

    columns: [
      { defaultContent: "" },
      { data: "dni_usuario" },
      {
        data: "usu_foto",
        render: function (data, type, row) {
          if (data == "controller/usuario/fotos/") {
            return '<img src="../img/vacio.png" class="img img-responsive" style="width:40px">';
          } else {
            return (
              '<img src="../' +
              data +
              '" class="img img-responsive" style="width:40px">'
            );
          }
        },
      },
      { data: "USUARIO" },
      { data: "usu_usuario" },
      { data: "usu_email" },
      { data: "usu_telefono" },
      { data: "rol" },
      { data: "sucrusal" },
      { data: "fecha_formateada" },
      {
        data: "usu_estatus",
        render: function (data, type, row) {
          if (data == "ACTIVO") {
            return '<span class="badge bg-success">ACTIVO</span>';
          } else {
            return '<span class="badge bg-danger">INACTIVO</span>';
          }
        },
      },
      {
        data: "usu_estatus",
        render: function (data, type, row) {
          if (data == "ACTIVO") {
            return "<button class='editar btn btn-primary btn-sm' title='Editar datos de usuario'><i class='fa fa-edit'></i></button>&nbsp;<button class='contra btn btn-warning btn-sm' title='Cambiar contraseña de usuario'><i class='fas fa-key'></i></button>&nbsp;<button class='btn btn-success btn-sm' disabled title='Activar usuario'><i class='fa fa-check-circle'></i></button>&nbsp;<button class='desactivar btn btn-danger btn-sm' title='Desactivar usuario'><i class='fa fa-times-circle'></i></button>";
          } else {
            return "<button class='editar btn btn-primary btn-sm' title='Editar datos de usuario'><i class='fa fa-edit'></i></button>&nbsp;<button class='contra btn btn-warning btn-sm' title='Cambiar contraseña de usuario'><i class='fas fa-key'></i></button>&nbsp;<button class='activar btn btn-success btn-sm' title='Activar usuario'><i class='fa fa-check-circle'></i></button>&nbsp;<button class='btn btn-danger btn-sm' disabled title='Desactivar usuario'><i class='fa fa-times-circle'></i></button>";
          }
        },
      },
    ],

    language: idioma_espanol,
    select: true,
  });
  tbl_usuario.on("draw.td", function () {
    var PageInfo = $("#tabla_usuario").DataTable().page.info();
    tbl_usuario
      .column(0, { page: "current" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1 + PageInfo.start;
      });
  });
}
function AbrirRegistro() {
  $("#modal_registro").modal({ backdrop: "static", keyboard: false });
  $("#modal_registro").modal("show");
}

//ABRIR MODAL EDITAR
$("#tabla_usuario").on("click", ".editar", function () {
  var data = tbl_usuario.row($(this).parents("tr")).data();

  if (tbl_usuario.row(this).child.isShown()) {
    var data = tbl_usuario.row(this).data();
  }
  $("#modal_editar").modal("show");
  document.getElementById("id_usuario").value = data.id_usuario;
  document.getElementById("txt_dni_editar").value = data.dni_usuario;
  document.getElementById("txt_nomb_editar").value = data.usu_nombre;
  document.getElementById("txt_apelli_editar").value = data.usu_apellido;

  document.getElementById("txt_correo_editar").value = data.usu_email;
  document.getElementById("txt_tele_editar").value = data.usu_telefono;
  document.getElementById("txt_direc_editar").value = data.usu_direccion;
  document.getElementById("txt_foto_actual").value = data.usu_foto;

  var imgElement = document.getElementById("preview2");
  if (imgElement) {
    console.log("Data:", data); // Depuración
    console.log("Image URL:", data.usu_foto); // Verificar URL

    if (data.usu_foto && data.usu_foto.trim() !== "") {
      imgElement.src = "../" + data.usu_foto; // Ruta relativa
    } else {
      imgElement.src = "../img/vacio.png"; // Ruta por defecto
    }

    imgElement.style.display = "block"; // Mostrar siempre la imagen

    // Manejar errores de carga
    imgElement.onerror = function () {
      console.error(
        "Error al cargar la imagen desde la ruta: " + imgElement.src
      );
      imgElement.src = "../img/vacio.png"; // Ruta por defecto
    };
  } else {
    console.error("Elemento img con id preview2 no encontrado");
  }

  document.getElementById("txt_usu_editar").value = data.usu_usuario;
  $("#txt_roles_editar").select2().val(data.id_role).trigger("change.select2");
  $("#txt_sucursal_editar")
    .select2()
    .val(data.id_sucursal)
    .trigger("change.select2");
});

$("#tabla_usuario").on("click", ".contra", function () {
  var data = tbl_usuario.row($(this).parents("tr")).data();

  if (tbl_usuario.row(this).child.isShown()) {
    var data = tbl_usuario.row(this).data();
  }
  $("#modal_contra").modal("show");
  document.getElementById("txt_idusuario_contra").value = data.id_usuario;
});
$("#tabla_usuario").on("click", ".desactivar", function () {
  var data = tbl_usuario.row($(this).parents("tr")).data();

  if (tbl_usuario.row(this).child.isShown()) {
    var data = tbl_usuario.row(this).data();
  }
  Swal.fire({
    title: "Desea desactivar al usuario " + data.USUARIO + "?",
    text: "Una vez desactivado el usuario no tendra acceso al sistema",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#005CA5",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Desactivar",
  }).then((result) => {
    if (result.isConfirmed) {
      Modificar_Estatus_Usuario(
        parseInt(data.id_usuario),
        "INACTIVO",
        data.USUARIO
      );
    }
  });
});

$("#tabla_usuario").on("click", ".activar", function () {
  var data = tbl_usuario.row($(this).parents("tr")).data();

  if (tbl_usuario.row(this).child.isShown()) {
    var data = tbl_usuario.row(this).data();
  }
  Swal.fire({
    title: "Desea activar al usuario " + data.USUARIO + "?",
    text: "Una vez activado el usuario tendra acceso al sistema",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#005CA5",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Activar",
  }).then((result) => {
    if (result.isConfirmed) {
      Modificar_Estatus_Usuario(
        parseInt(data.id_usuario),
        "ACTIVO",
        data.USUARIO
      );
    }
  });
});

function Modificar_Estatus_Usuario(id, estatus, user) {
  let esta = estatus;
  if (esta === "INACTIVO") {
    esta = "Desactivo";
  }
  $.ajax({
    url: "../controller/usuario/controlador_modificar_usuario_estatus.php",
    type: "POST",
    data: {
      id: id,
      estatus: estatus,
    },
  }).done(function (resp) {
    if (resp > 0) {
      Swal.fire(
        "Mensaje de Confirmación",
        "Se " + esta + " con exito El Usuario " + user,
        "success"
      ).then((value) => {
        tbl_usuario.ajax.reload();
      });
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se completo la actualización",
        "error"
      );
    }
  });
}

//REGISTROS DE USUARIOS
function Registrar_Usuario() {
  //DATOS DEL DOCENTE
  let dni = document.getElementById("txt_dni").value;
  let nombre = document.getElementById("txt_nomb").value;
  let apelli = document.getElementById("txt_apelli").value;
  let correo = document.getElementById("txt_correo").value;
  let tele = document.getElementById("txt_tele").value;
  let dire = document.getElementById("txt_direc").value;
  let foto = document.getElementById("txt_foto").value;

  //DATOS DEL USUARIO
  let usu = document.getElementById("txt_usu").value;
  let contra = document.getElementById("txt_contra").value;
  let rol = document.getElementById("txt_roles").value;
  let sucu = document.getElementById("txt_sucursal1").value;

  if (
    dni.length == 0 ||
    apelli.length == 0 ||
    correo.length == 0 ||
    dire.length == 0 ||
    nombre.length == 0
  ) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Tiene campos en el registro del docente",
      "warning"
    );
  }
  if (
    usu.length == 0 ||
    contra.length == 0 ||
    rol.length == 0 ||
    sucu.length == 0
  ) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Los datos del usuario son oblgatorios",
      "warning"
    );
  }

  let extension = foto.split(".").pop();
  let nombrefoto = "";
  let f = new Date();
  if (foto.length > 0) {
    nombrefoto =
      "IMG" +
      f.getDate() +
      "-" +
      (f.getMonth() + 1) +
      "-" +
      f.getFullYear() +
      "-" +
      f.getHours() +
      "-" +
      f.getMilliseconds() +
      "." +
      extension;
  }
  //CONDICIONANDO LOS CAMPOS VACIOS

  let formData = new FormData();
  let fotoobj = $("#txt_foto")[0].files[0];

  formData.append("dni", dni);
  formData.append("nombre", nombre);
  formData.append("apelli", apelli);
  formData.append("correo", correo);
  formData.append("tele", tele);
  formData.append("dire", dire);
  formData.append("nombrefoto", nombrefoto);
  formData.append("foto", fotoobj);

  formData.append("usu", usu);
  formData.append("contra", contra);
  formData.append("rol", rol);
  formData.append("sucu", sucu);
  $.ajax({
    url: "../controller/usuario/controlador_registro_usuario.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (resp) {
      if (resp.length > 0) {
        if (resp == 1) {
          Swal.fire(
            "Mensaje de Confirmación",
            "Se registro correctamente al usuario con el DNI N° <b>" +
              dni +
              "</b>",
            "success"
          ).then((value) => {
            // Limpiar todos los campos
            document.getElementById("txt_dni").value = "";
            document.getElementById("txt_nomb").value = "";
            document.getElementById("txt_apelli").value = "";
            document.getElementById("txt_correo").value = "";
            document.getElementById("txt_tele").value = "";
            document.getElementById("txt_direc").value = "";

            // Limpiar la vista previa de la imagen
            document.getElementById("preview").src = "#";
            document.getElementById("preview").alt = "Vista previa";

            document.getElementById("txt_usu").value = "";
            document.getElementById("txt_contra").value = "";

            // Cerrar el modal
            $("#modal_registro").modal("hide");
            tbl_usuario.ajax.reload();
          });
        } else {
          Swal.fire(
            "Mensaje de Advertencia",
            "El DNI o el USUARIO que intentas registrar ya se encuentra en la base de datos, revise por favor",
            "warning"
          );
        }
      } else {
        Swal.fire(
          "Mensaje de Advertencia",
          "No se pudo registrar al usuario",
          "warning"
        );
      }
    },
  });
}

function Modificar_Usuario() {
  //DATOS DEL DOCENTE
  let id = document.getElementById("id_usuario").value;
  let dni = document.getElementById("txt_dni_editar").value;
  let nombre = document.getElementById("txt_nomb_editar").value;
  let apelli = document.getElementById("txt_apelli_editar").value;
  let correo = document.getElementById("txt_correo_editar").value;
  let tele = document.getElementById("txt_tele_editar").value;
  let dire = document.getElementById("txt_direc_editar").value;
  let fotoactual = document.getElementById("txt_foto_actual").value;
  let foto = document.getElementById("txt_foto_editar").value;

  //DATOS DEL USUARIO
  let usu = document.getElementById("txt_usu_editar").value;
  let rol = document.getElementById("txt_roles_editar").value;
  let sucu = document.getElementById("txt_sucursal_editar").value;

  if (
    id.length == 0 ||
    dni.length == 0 ||
    apelli.length == 0 ||
    correo.length == 0 ||
    dire.length == 0 ||
    nombre.length == 0
  ) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Tiene campos en el registro del docente",
      "warning"
    );
  }
  if (usu.length == 0 || rol.length == 0 || sucu.length == 0) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Los datos del usuario son oblgatorios",
      "warning"
    );
  }

  let extension = foto.split(".").pop();
  let nombrefoto = "";
  let f = new Date();
  if (foto.length > 0) {
    nombrefoto =
      "IMG" +
      f.getDate() +
      "-" +
      (f.getMonth() + 1) +
      "-" +
      f.getFullYear() +
      "-" +
      f.getHours() +
      "-" +
      f.getMilliseconds() +
      "." +
      extension;
  }
  //CONDICIONANDO LOS CAMPOS VACIOS

  let formData = new FormData();
  let fotoobj = $("#txt_foto_editar")[0].files[0];

  formData.append("id", id);
  formData.append("dni", dni);
  formData.append("nombre", nombre);
  formData.append("apelli", apelli);
  formData.append("correo", correo);
  formData.append("tele", tele);
  formData.append("dire", dire);
  formData.append("fotoactual", fotoactual);
  formData.append("nombrefoto", nombrefoto);
  formData.append("foto", fotoobj);

  formData.append("usu", usu);
  formData.append("rol", rol);
  formData.append("sucu", sucu);
  $.ajax({
    url: "../controller/usuario/controlador_modificar_usuario.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (resp) {
      if (resp.length > 0) {
        if (resp == 1) {
          Swal.fire(
            "Mensaje de Confirmación",
            "Se actualizo correctamente al usuario con el DNI N° <b>" +
              dni +
              "</b>",
            "success"
          ).then((value) => {
            // Cerrar el modal
            $("#modal_editar").modal("hide");
            tbl_usuario.ajax.reload();
            document.getElementById("txt_foto_editar").value = "";
          });
        } else {
          Swal.fire(
            "Mensaje de Advertencia",
            "El DNI o USUARIO que intentas actualizar ya se encuentra en la base de datos, revise por favor",
            "warning"
          );
        }
      } else {
        Swal.fire(
          "Mensaje de Advertencia",
          "No se pudo actualizar al usuario",
          "warning"
        );
      }
    },
  });
}

function Modificar_Contra() {
  let id = document.getElementById("txt_idusuario_contra").value;
  let con = document.getElementById("txt_contra_nueva").value;

  if (id.length == 0 || con.length == 0) {
    return Swal.fire(
      "Mensaje de Advertencia",
      "Tiene campos vacios",
      "warning"
    );
  }
  $.ajax({
    url: "../controller/usuario/controlador_modificar_usuario_contra.php",
    type: "POST",
    data: {
      id: id,
      con: con,
    },
  }).done(function (resp) {
    if (resp > 0) {
      Swal.fire(
        "Mensaje de Confirmación",
        "Contraseña del Usuario Actualizada",
        "success"
      ).then((value) => {
        tbl_usuario.ajax.reload();
        $("#modal_contra").modal("hide");
      });
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se completo la actualización",
        "error"
      );
    }
  });
}

function Cargar_Select_roles() {
  $.ajax({
    url: "../controller/roles/controlador_cargar_select_roles.php",
    type: "POST",
  }).done(function (resp) {
    let data = JSON.parse(resp);
    if (data.length > 0) {
      let cadena = "";
      for (let i = 0; i < data.length; i++) {
        cadena +=
          "<option value='" + data[i][0] + "'>" + data[i][1] + "</option>";
      }
      document.getElementById("txt_roles").innerHTML = cadena;
      document.getElementById("txt_roles_editar").innerHTML = cadena;
    } else {
      cadena += "<option value=''>No hay empleado en la base de datos</option>";
      document.getElementById("txt_roles").innerHTML = cadena;
      document.getElementById("txt_roles_editar").innerHTML = cadena;
    }
  });
}


function Cargar_Select_sucursal() {
  $.ajax({
    url: "../controller/sucursal/controlador_cargar_select_sucursal.php",
    type: "POST",
  }).done(function (resp) {
    console.log("Respuesta del servidor:", resp);

    let data;
    try {
      data = JSON.parse(resp);
    } catch (e) {
      console.error("Error al parsear JSON:", e, resp);
      return;
    }

    let cadena = "";
    if (data.length > 0) {
      data.forEach(row => {
        cadena += `<option value="${row.id_sucursal}">${row.sucrusal}</option>`;
      });
    } else {
      cadena = "<option value='' disabled selected>No hay sucursales disponibles</option>";
    }

    $("#txt_sucursal1").html(cadena).trigger('change.select2');
    $("#txt_sucursal_editar").html(cadena).trigger('change.select2');
  });
}



// TOTALES
function Total_servicios() {
  $.ajax({
    url: "../controller/usuario/controlador_total_servicios.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_servicios").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_choferes() {
  $.ajax({
    url: "../controller/usuario/controlador_total_choferes.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_choferes").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_clientes() {
  $.ajax({
    url: "../controller/usuario/controlador_total_clientes.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_clientes").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_encomiendas_dia() {
  $.ajax({
    url: "../controller/usuario/controlador_total_encomiendas_dia.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_encomiendas_dia").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_encomiendas_semanales() {
  $.ajax({
    url: "../controller/usuario/controlador_total_encomiendas_semanales.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_encomiendas_semanales").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}
function Total_encomiendas_mes() {
  $.ajax({
    url: "../controller/usuario/controlador_total_encomiendas_mes.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_encomiendas_mes").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}
function Total_encomiendas() {
  $.ajax({
    url: "../controller/usuario/controlador_total_encomiendas.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_encomiendas").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_salidas_dia() {
  $.ajax({
    url: "../controller/usuario/controlador_total_salidas_dia.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_salidas_dia").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_salidas_semana() {
  $.ajax({
    url: "../controller/usuario/controlador_total_salidas_semana.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_salidas_semana").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_salidas_mes() {
  $.ajax({
    url: "../controller/usuario/controlador_total_salidas_mes.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_salidas_mes").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_salidas() {
  $.ajax({
    url: "../controller/usuario/controlador_total_salidas.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_salidas_diarias").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_ingresos_hoy() {
  $.ajax({
    url: "../controller/usuario/controlador_total_ingresos_hoy.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_ingresos_hoy").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_gastos_hoy() {
  $.ajax({
    url: "../controller/usuario/controlador_total_gastos_hoy.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_gastos_hoy").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_ingresos_mes_actual() {
  $.ajax({
    url: "../controller/usuario/controlador_total_ingresos_mes.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_ingresos_mes_actual").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_gastos_mes_actual() {
  $.ajax({
    url: "../controller/usuario/controlador_total_gatos_mes.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_gastos_mes_actual").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_reservas_dia() {
  $.ajax({
    url: "../controller/usuario/controlador_total_reservas_dia.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_reservas_pendientes_dia").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_reservas_semana() {
  $.ajax({
    url: "../controller/usuario/controlador_total_reservas_semana.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_reservas_semana").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_reservas_mes() {
  $.ajax({
    url: "../controller/usuario/controlador_total_reservas_mes.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_reservas_mes").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_reservas() {
  $.ajax({
    url: "../controller/usuario/controlador_total_reservas.php",
    type: "POST",
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_reservas").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

// ASISTENTES
//ENCOMIENDAS ASISTENTES
function Total_encomiendas_dia_asis() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_encomiendas_diaria_asis.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_encomiendas_dia_asis").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_encomiendas_semana_asis() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_encomiendas_semana_asis.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_encomiendas_semanales_asis").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}
function Total_encomiendas_mes_asis() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_encomiendas_mes_asis.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_encomiendas_mes_asis").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_encomiendas_asis() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_encomiendas_asis.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_encomiendas_asis").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

// SALIDAS ASISTENTES
function Total_salidas_dia_asis() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_salidas_diaria_asis.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_salidas_dia_asis").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_salidas_semana_asis() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_salidas_semana_asis.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_salidas_semana_asis").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_salidas_mes_asis() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_salidas_mes_asis.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_salidas_mes_asis").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_salidas_asis() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_total_salidas_asis.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_salidas_diarias_asis").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

//RESERVAS ASISTENTES
function Total_reservas_pendientes_dia_asis() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_reservas_pendiente_asis.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_reservas_pendientes_dia_asis").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_reservas_semanales_asis() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_reservas_semanales_asis.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_reservas_semana_asis").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_reservas_mes_asis() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_reservas_mes_asis.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_reservas_mes_asis").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_reservas_asis() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_reservas_asis.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_reservas_asis").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

//COMPROBANTES
function Total_comprobantes() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_total_comprobantes.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_comprobantes").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}


function Total_facturas() {

  $.ajax({
    url: "../controller/usuario/controlador_total_facturas.php",
    type: "POST",

  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_facturas_emitidas").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_boletas() {

  $.ajax({
    url: "../controller/usuario/controlador_total_boletas.php",
    type: "POST",

  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_boletas_emitidas").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_Notas_Credito() {

  $.ajax({
    url: "../controller/usuario/controlador_total_notas_credito.php",
    type: "POST",

  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_notas_credito").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_Notas_Debito() {

  $.ajax({
    url: "../controller/usuario/controlador_total_notas_debito.php",
    type: "POST",

  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_notas_debito").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

// COMPROBANTES POR SUCURSAL
function Total_facturas_sucu() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_total_factura_sucu.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_facturas_emitidas_sucu").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_boletas_sucu() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_total_boletas_sucu.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_boletas_emitidas_sucu").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_nota_credito_sucu() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_total_notacredito_sucu.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_notas_credito_sucu").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}

function Total_nota_debito_sucu() {
  let ori = document.getElementById("txt_sucursal").value;

  $.ajax({
    url: "../controller/usuario/controlador_total_notadebito_sucu.php",
    type: "POST",
    data: {
      ori: ori,
    },
  }).done(function (resp) {
    var data = JSON.parse(resp);
    var cadena = "";
    if (data.length > 0) {
      $("#total_notas_debito_sucu").html(data[0][0]);
    } else {
      return Swal.fire(
        "Mensaje de Error",
        "No se pudo traer los resultados",
        "error"
      );
    }
  });
}