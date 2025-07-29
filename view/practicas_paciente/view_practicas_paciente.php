<?php
session_start();
if (!isset($_SESSION['S_ID'])) {
  header('Location: ../index.php');
}
?>
<script src="../js/console_practicas_paciente.js?rev=<?php echo time(); ?>"></script>
<link rel="stylesheet" href="../plantilla/plugins/icheck-bootstrap/icheck-bootstrap.min.css">

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><b>MANTENIMIENTO DE PRÁCTICAS - PACIENTE</b></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
          <li class="breadcrumb-item active">PRÁCTICAS - PACIENTE</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <!-- /.col-md-6 -->
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><i class="nav-icon fas fa-th"></i>&nbsp;&nbsp;<b>Listado de Prácticas - Paciente</b></h3>
            <button class="btn btn-success float-right" onclick="AbrirRegistro()"><i class="fas fa-plus"></i> Nuevo Registro</button>

          </div>
          <div class="table-responsive" style="text-align:left">
            <div class="card-body">
              <div class="row" style="border: 1px solid #ccc; padding: 15px; border-radius: 8px;">
                <div class="col-6 form-group">
                  <label for="">Obras Sociales:</label>
                  <select class="js-example-basic-single" id="select_obras_buscar" style="width:100%">
                  </select>
                </div>

                <div class="col-12 col-md-3" role="document">
                  <label for="">&nbsp;</label><br>
                  <button onclick="listar_practica_paciente_obras()" class="btn btn-danger mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Buscar registros</button>
                </div>
                <div class="col-12 col-md-3" role="document">
                  <label for="">&nbsp;</label><br>
                  <button onclick="listar_practica_paciente()" class="btn btn-success mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Listar todos</button>
                </div>
              </div>
            </div>
          </div>
          <div class="table-responsive" style="text-align:left">
            <div class="card-body">
              <div class="row" style="border: 1px solid #ccc; padding: 15px; border-radius: 8px;">
                <div class="col-3 form-group">
                  <label for="">Fecha desde:</label>
                  <input type="date" class="form-control" id="txt_fecha_desde">
                </div>
                <div class="col-3 form-group">
                  <label for="">Fecha hasta:</label>
                  <input type="date" class="form-control" id="txt_fecha_hasta">

                </div>
                <div class="col-3 form-group">
                  <label for="">Usuario:</label>
                  <select class="js-example-basic-single" id="select_usuario" style="width:100%">
                  </select>
                </div>
                <div class="col-12 col-md-3" role="document">
                  <label for="">&nbsp;</label><br>
                  <button onclick="listar_practica_paciente_fecha_usu()" class="btn btn-danger mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Buscar registros</button>
                </div>

              </div>
            </div>
          </div>
          <div class="table-responsive" style="text-align:center">
            <div class="card-body">
              <table id="tabla_paciente_practica" class="table table-striped table-bordered" style="width:100%">
                <thead style="background-color:#023D77;color:#FFFFFF;">
                  <tr>
                    <th style="text-align:center">Nro.</th>
                    <th style="text-align:center">Obra Social</th>
                    <th style="text-align:center">Área</th>
                    <th style="text-align:center">DNI</th>
                    <th style="text-align:center">Paciente</th>
                    <th style="text-align:center">Total</th>
                    <th style="text-align:center">Fecha registro</th>
                    <th style="text-align:center">Fecha actualización</th>
                    <th style="text-align:center">Usuario que registro</th>
                    <th style="text-align:center">Estado</th>
                    <th style="text-align:center">Ver H.C.</th>
                    <th style="text-align:center">Acciones</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

        </div>
        <!-- /.col-md-6 -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->

  <!-- Modal -->
  <div class="modal fade" id="modal_registro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1FA0E0;">
          <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>REGISTRO DE PRÁCTICAS - PACIENTE</b></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <div class="row">
            <div class="col-12 form-group" style="color:red">
              <h6><b>Campos Obligatorios (*)</b></h6>
            </div>
            <div class="col-12 form-group">
              <label for="">Obra Social<b style="color:red">(*)</b>:</label>
              <select class="js-example-basic-single" id="select_obras" style="width:100%">
              </select>
            </div>
            <div class="col-6 form-group">
              <label for="">Área<b style="color:red">(*)</b>:</label>
              <select class="js-example-basic-single" id="select_area" style="width:100%">
              </select>
            </div>
            <div class="col-6 form-group">
              <label for="">Paciente<b style="color:red">(*)</b>:</label>
              <select class="js-example-basic-single" id="select_paciente" style="width:100%">
              </select>
            </div>
            <div class="col-6 form-group">
              <label for="">Tipo de Práctica<b style="color:red">(*)</b>:</label>
              <select class="js-example-basic-single" id="select_practica" style="width:100%">
              </select>
            </div>
            <div class="col-2 form-group">
              <label for="">Precio Unitario<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_precio" disabled>
            </div>
            <div class="col-2 form-group">
              <label for="">Cantidad<b style="color:red">(*)</b>:</label>
              <input type="number" class="form-control" id="txt_cantidad" value="1">
            </div>
            <div class="col-2 form-group">
              <label for="">Subtotal<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_subtotal" disabled>
            </div>
            <div class="col-4 form-group">
              <label>Archivo de H.C. <b style="color:black">(Opcional)</b>:</label>
              <div class="custom-file position-relative">
                <input type="file" class="custom-file-input" id="txt_hc" accept="application/pdf" onchange="updateFileLabel(event)">
                <label class="custom-file-label" id="label_txt_hc" for="txt_hc">Seleccione H.C...</label>
                <button type="button" class="btn btn-danger btn-sm btn-clear-file" id="btn_clear_hc" onclick="clearFactura()">X</button>
              </div>
            </div>
            <div class="col-4 form-group">
              <label for="">Profesional Responsable<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_profesional" value="<?php echo $_SESSION['S_COMPLETOS']; ?>" disabled>
            </div>

            <div class="col-4 form-group">
              <label for="">Fecha registro<b style="color:red">(*)</b>:</label>
              <input type="date" class="form-control" id="txt_fecha" disabled>
            </div>
            <div class="col-12 form-group">
              <button type="button" class="btn btn-success btn-block" onclick="Agregar_practica()">
                <i class="fas fa-plus"></i> <b>Agregar Práctica</b>
              </button>
            </div>
            <div class="col-12 table-responsive" style="text-align:center">
              <table id="tabla_practica" style="width:100%" class="table">
                <thead class="thead-dark">
                  <tr>
                    <th>Id.</th>
                    <th>Practica</th>
                    <th>Precio unitario</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acci&oacute;n</th>
                  </tr>
                </thead>
                <tbody id="tbody_tabla_practica">
                </tbody>
              </table>
              <div class="col-9">
              </div>
              <div class="text-right">
                <h3 id="lbl_totalneto"></h3>
                <hr>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
          <button type="button" class="btn btn-success" onclick="Registrar_Practica()"><i class="fas fa-save"></i> Registrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_ver_practicas" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div style="display: flex; flex-direction: column;">
            <h5 class="modal-title" id="lb_titulo"></h5>
            <h5 class="modal-title" id="lb_titulo2" style="margin-top: 10px;"></h5> <!-- Espaciado entre títulos -->
          </div>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-12" style="text-align:center">
              <div class="table-responsive" style="text-align:center">
                <div class="card-body">
                  <!-- Título general -->
                  <table id="tabla_ver_practicas" class="display compact" style="width:100%; text-align:center;">
                    <thead style="background-color:#0252A0;color:#FFFFFF;">
                      <tr>
                        <th colspan="6" style="text-align:center; font-size: 18px; font-weight: bold;">PRÁCTICAS REALIZADAS</th>
                      </tr>
                      <tr style="text-align:center;">
                        <th style="text-align:center;">Código</th>
                        <th style="text-align:center;">Práctica</th>
                        <th style="text-align:center;">Precio unitario</th>
                        <th style="text-align:center;">Cantidad</th>
                        <th style="text-align:center;">Subtotal</th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr>
                        <th colspan="4" style="text-align:right;">Total:</th>
                        <th style="text-align:center;" id="total_sub_total">S/. 0.00</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">
            <i class="fa fa-arrow-right-from-bracket"></i> Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>




  <div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1FA0E0;">
          <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>MODIFICAR DATOS DE PRÁCTICAS - PACIENTE</b></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <div class="row">
            <div class="col-12 form-group" style="color:red">
              <h6><b>Campos Obligatorios (*)</b></h6>
            </div>
            <div class="col-12 form-group">
              <label for="">Obra Social<b style="color:red">(*)</b>:</label>
              <input type="text" id="txt_id_detalle" hidden>
              <select class="js-example-basic-single" id="select_obras_editar" style="width:100%" disabled>
              </select>
            </div>
            <div class="col-6 form-group">
              <label for="">Área<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_area" disabled>
              </select>
            </div>
            <div class="col-6 form-group">
              <label for="">Paciente<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_paciente" disabled>

            </div>
            <div class="col-6 form-group">
              <label for="">Tipo de Práctica<b style="color:red">(*)</b>:</label>
              <select class="js-example-basic-single" id="select_practica_editar" style="width:100%">
              </select>
            </div>
            <div class="col-2 form-group">
              <label for="">Precio Unitario<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_precio_editar" disabled>
            </div>
            <div class="col-2 form-group">
              <label for="">Cantidad<b style="color:red">(*)</b>:</label>
              <input type="number" class="form-control" id="txt_cantidad_editar" value="1">
            </div>
            <div class="col-2 form-group">
              <label for="">Subtotal<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_subtotal_editar" disabled>
            </div>
            <div class="col-6 form-group">
              <label for="">Profesional Responsable<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_profesional_editar" value="<?php echo $_SESSION['S_COMPLETOS']; ?>" disabled>
            </div>

            <div class="col-6 form-group">
              <label for="">Fecha registro<b style="color:red">(*)</b>:</label>
              <input type="datetime" class="form-control" id="txt_fecha_editar" disabled>
            </div>
            <div class="col-12 form-group">
              <button type="button" class="btn btn-success btn-block" onclick="Agregar_practica_editar()">
                <i class="fas fa-plus"></i> <b>Agregar Práctica</b>
              </button>
            </div>
            <div class="col-12 table-responsive" style="text-align:center">
              <table id="tabla_practica_editar" style="width:100%" class="table">
                <thead class="thead-dark">
                  <tr>
                    <th style="text-align:center">Id principal</th>
                    <th style="text-align:center">Id.</th>
                    <th style="text-align:center">Practica</th>
                    <th style='text-align:center; display: none;'>Precio unitario</th>
                    <th style="text-align:center">Cantidad</th>
                    <th style="text-align:center; display: none;">Subtotal</th>
                    <th style="text-align:center">Acci&oacute;n</th>
                  </tr>
                </thead>
                <tbody id="tbody_tabla_practica_editar">
                </tbody>
              </table>
              <div class="col-9">
              </div>
              <div class="text-left">

                <h3 id="lbl_totalneto_editar"></h3>
                <hr>
              </div>

              <div class="alert alert-warning alert-dismissible" style=" text-align: justify;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-exclamation-triangle"></i> ¡Aviso Importante!</h5>
                Si agregaste o eliminaste una práctica, asegúrate de hacer clic en el botón <b>Modificar</b> para actualizar el TOTAL GENERAL en la BASE DE DATOS.
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
          <button type="button" class="btn btn-success" onclick="Modificar_detalle_practicas()"><i class="fas fa-edit"></i> Modificar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_adjuntar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1FA0E0;">
          <div style="display: flex; flex-direction: column;color:white">
            <h5 class="modal-title"><b>ADJUNTAR HISTORIA CLÍNICA</b></h5>
          </div>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-12 form-group" style="color:red">
              <h6><b>Campos Obligatorios (*)</b></h6>
            </div>
            <div class="col-12 form-group">
              <label for="">Obra Social:</label>
              <input type="text" class="form-control" id="txt_obrit" disabled>
            </div>
            <div class="col-12 form-group">
              <label for="">Paciente:</label>
              <input type="text" class="form-control" id="id_txt_paci" hidden>
              <input type="text" class="form-control" id="txt_paci" disabled>
            </div>

            <div class="col-12 form-group">
              <label for="">Total:</label>
              <input type="text" class="form-control" id="total" disabled>
            </div>
            <input type="text" id="foto_actual" hidden>

            <div class="col-12 form-group">
              <label>Archivo de H.C. <b style="color:red">(*)</b>:</label>
              <div class="custom-file position-relative">
                <input type="file" class="custom-file-input" id="txt_hc_editar" accept="application/pdf" onchange="updateFileLabel2(event)">
                <label class="custom-file-label" id="label_txt_hc_editar" for="txt_hc_editar">Seleccione H.C...</label>
                <button type="button" class="btn btn-danger btn-sm btn-clear-file" id="btn_clear_hc_editar" onclick="clearFactura2()">X</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
          <button type="button" class="btn btn-success" onclick="Modificar_HC()"><i class="fas fa-upload"></i> Subir H.C.</button>
        </div>
      </div>
    </div>
  </div>



  <style>
    .hidden {
      display: none;
    }
  </style>

  <script>
    $(document).ready(function() {
      // Función para inicializar todos los select2 básicos
      function initializeAllSelect2() {
        // Inicializar select2 para obras sociales
        $('#select_obras').select2({
          placeholder: "Seleccionar obra social...",
          allowClear: true,
          width: '100%'
        });

        // Inicializar select2 para pacientes
        $('#select_paciente').select2({
          placeholder: "Seleccionar paciente...",
          allowClear: true,
          width: '100%'
        });

        // Inicializar select2 para prácticas
        $('#select_practica').select2({
          placeholder: "Seleccionar práctica...",
          allowClear: true,
          width: '100%'
        });
        $('#select_practica_editar').select2({
          placeholder: "Seleccionar práctica...",
          allowClear: true,
          width: '100%'
        });
        // Inicializar otros select2 básicos
        $('.js-example-basic-single').select2({
          placeholder: "Seleccionar...",
          allowClear: true,
          width: '100%'
        });
      }

      // Inicializar al cargar la página
      initializeAllSelect2();

      // Reinicializar cuando se abre el modal de registro
      $('#modal_registro').on('shown.bs.modal', function() {
        // Destruir instancias previas de select2
        $('#select_obras, #select_paciente, #select_practica').select2('destroy');

        // Reinicializar con el modal como padre
        $('#select_obras').select2({
          dropdownParent: $('#modal_registro'),
          placeholder: "Seleccionar obra social...",
          allowClear: true,
          width: '100%'
        });
        $('#select_area').select2({
          dropdownParent: $('#modal_registro'),
          placeholder: "Seleccionar Área",
          allowClear: true,
          width: '100%'
        });
        $('#select_paciente').select2({
          dropdownParent: $('#modal_registro'),
          placeholder: "Seleccionar paciente...",
          allowClear: true,
          width: '100%'
        });

        $('#select_practica').select2({
          dropdownParent: $('#modal_registro'),
          placeholder: "Seleccionar práctica...",
          allowClear: true,
          width: '100%'
        });

      });
      $('#modal_editar').on('shown.bs.modal', function() {
        // Destruir instancias previas de select2
        $('#select_obras, #select_practica_editar').select2('destroy');

        // Reinicializar con el modal como padre
        $('#select_obras').select2({
          dropdownParent: $('#modal_editar'),
          placeholder: "Seleccionar obra social...",
          allowClear: true,
          width: '100%'
        });

        $('#select_practica_editar').select2({
          dropdownParent: $('#modal_editar'),
          placeholder: "Seleccionar práctica...",
          allowClear: true,
          width: '100%'
        });

      });

      // Manejar el cambio en obra social
      $('#select_obras').off('change').on('change', function() {
        var id = $(this).val();
        if (id) {
          // Cargar pacientes
          $.ajax({
            url: "../controller/practicas_paciente/controlador_cargar_select_paciente_practica.php",
            type: 'POST',
            data: {
              id: id
            },
            success: function(response) {
              try {
                var data = JSON.parse(response);
                var options = '<option value="">Seleccionar paciente...</option>';

                if (data.length > 0) {
                  data.forEach(function(item) {
                    options += `<option value="${item[0]}">DNI: ${item[1]} - ${item[2]}</option>`;
                  });
                }

                $('#select_paciente')
                  .html(options)
                  .trigger('change');
              } catch (e) {
                console.error("Error al procesar respuesta:", e);
              }
            }
          });

          // Cargar prácticas
          $.ajax({
            url: "../controller/practicas_paciente/controlador_cargar_select_paciente_practica2.php",
            type: 'POST',
            data: {
              id2: id
            },
            success: function(response) {
              try {
                var data = JSON.parse(response);
                var options = '<option value="">Seleccionar práctica...</option>';

                if (data.length > 0) {
                  data.forEach(function(item) {
                    options += `<option value="${item[0]}">Código: ${item[1]} - ${item[2]}</option>`;
                  });
                }

                $('#select_practica')
                  .html(options)
                  .trigger('change');
              } catch (e) {
                console.error("Error al procesar respuesta:", e);
              }
            }
          });
        }
      });

      // Cargar datos iniciales
      Cargar_Select_Obras_Sociales();
      Cargar_Select_Obras_Sociales2();
      Cargar_Select_Usuarios();
      Cargar_Select_Areas();
      listar_practica_paciente_diario();
    });
    //TRAER DATOS DE PACIENTE


    $("#select_obras").change(function() {
      var id = $("#select_obras").val();
      Cargar_Select_Paciente(id);
    });



    //TRAER DATOS DE PRACTICA
    $("#select_obras").change(function() {
      var id = $("#select_obras").val();
      Cargar_Select_Practica(id);
    });

    $("#select_obras_editar").change(function() {
      var id = $("#select_obras_editar").val();
      Cargar_Select_Practica(id);
    });


    //TRAER MONTO DE PRACTICA
    $("#select_practica").change(function() {
      var id = $("#select_practica").val();
      Traerprecio(id);
    });

    $("#select_practica_editar").change(function() {
      var id = $("#select_practica_editar").val();
      Traerprecio(id);
    });

    //TRAER FECHA ACTUAL
    var n = new Date();
    var y = n.getFullYear();
    var m = n.getMonth() + 1; // Los meses empiezan desde 0, por eso se suma 1
    var d = n.getDate();

    // Si el día o el mes es menor a 10, se le agrega un '0' al inicio
    if (d < 10) {
      d = '0' + d;
    }
    if (m < 10) {
      m = '0' + m;
    }

    // Establece el valor del campo de fecha con el formato YYYY-MM-DD
    document.getElementById('txt_fecha').value = y + "-" + m + "-" + d;
  </script>

  <style>
    /* Estilo para la tabla */
    #tabla_practica {
      border: 2px solid #1FA0E0;
      border-radius: 8px;
    }

    #tabla_practica thead {
      background-color: #1FA0E0;
      color: white;
    }

    #tabla_practica th,
    #tabla_practica td {
      text-align: center;
      border: 1px solid #ddd;
      padding: 10px;
    }

    /* Asegura que los inputs y selects ocupen el ancho completo */
    .form-group input,
    .form-group select {
      width: 100%;
    }

    /* Botón de limpiar archivos */
    .btn-clear-file {
      position: absolute;
      right: 80px;
      top: 50%;
      transform: translateY(-50%);
      z-index: 10;
    }
  </style>

  <style>
    /* Estilo para la tabla */
    #tabla_practica_editar {
      border: 2px solid #1FA0E0;
      border-radius: 8px;
    }

    #tabla_practica_editar thead {
      background-color: #1FA0E0;
      color: white;
    }

    #tabla_practica_editar th,
    #tabla_practica_editar td {
      text-align: center;
      border: 1px solid #ddd;
      padding: 10px;
    }

    /* Asegura que los inputs y selects ocupen el ancho completo */
    .form-group input,
    .form-group select {
      width: 100%;
    }

    /* Botón de limpiar archivos */
    .btn-clear-file {
      position: absolute;
      right: 80px;
      top: 50%;
      transform: translateY(-50%);
      z-index: 10;
    }
  </style>
  <script>
    function updateFileLabel(event) {
      var input = event.target;
      var label = document.getElementById('label_txt_hc');

      if (input.files && input.files[0]) {
        var fileName = input.files[0].name;
        label.innerHTML = "Subir H.C. (" + fileName + ")";
      }
    }

    function clearFactura() {
      var fileInput = document.getElementById('txt_hc');
      var fileLabel = document.getElementById('label_txt_hc');

      // Limpiar el input de archivo
      fileInput.value = '';

      // Restablecer el texto del label
      fileLabel.innerHTML = "Seleccione H.C...";
    }
  </script>

  <script>
    function updateFileLabel2(event) {
      var input = event.target;
      var label = document.getElementById('label_txt_hc_editar');

      if (input.files && input.files[0]) {
        var fileName = input.files[0].name;
        label.innerHTML = "Subir Factura (" + fileName + ")";
      }
    }

    function clearFactura2() {
      var fileInput = document.getElementById('txt_hc_editar');
      var fileLabel = document.getElementById('label_txt_hc_editar');

      // Limpiar el input de archivo
      fileInput.value = '';

      // Restablecer el texto del label
      fileLabel.innerHTML = "Seleccione H.C...";
    }
  </script>