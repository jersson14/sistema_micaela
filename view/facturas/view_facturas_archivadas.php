<?php
session_start();
if (!isset($_SESSION['S_ID'])) {
  header('Location: ../index.php');
}
?>
<script src="../js/console_facturas_archivadas.js?rev=<?php echo time(); ?>"></script>
<link rel="stylesheet" href="../plantilla/plugins/icheck-bootstrap/icheck-bootstrap.min.css">

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><b>MANTENIMIENTO DE FACTURAS ARCHIVADAS</b></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
          <li class="breadcrumb-item active">FACTURAS ARCHIVADAS</li>
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
            <h3 class="card-title"><i class="nav-icon fas fa-th"></i>&nbsp;&nbsp;<b>Listado de Facturas Archivadas</b></h3>

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
                  <button onclick="listar_facturas()" class="btn btn-success mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Listar todos</button>
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
              <table id="tabla_facturas" class="table table-striped table-bordered" style="width:100%">
                <thead style="background-color:#023D77;color:#FFFFFF;">
                  <tr>
                    <th style="text-align:center">Nro.</th>
                    <th style="text-align:center">Obra Social</th>
                    <th style="text-align:center">Nro. Factura</th>
                    <th style="text-align:center">Monto Total</th>
                    <th style="text-align:center">Ver Factura</th>
                    <th style="text-align:center">Ver Nota de crédito</th>
                    <th style="text-align:center">Fecha Nota de crédito</th>
                    <th style="text-align:center">Fecha de archivo
                    <th style="text-align:center">Estado</th>
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




  <div class="modal fade" id="modal_ver_facturas_paci" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div style="display: flex; flex-direction: column;">
            <h5 class="modal-title" id="lb_titulo_facturas"></h5>
            <h5 class="modal-title" id="lb_titulo2_facturas" style="margin-top: 10px;"></h5> <!-- Espaciado entre títulos -->
            <h5 class="modal-title" id="lb_titulo3_facturas" style="margin-top: 10px;"></h5> <!-- Espaciado entre títulos -->
            <h5 class="modal-title" id="lb_titulo4_facturas" style="margin-top: 10px;"></h5> <!-- Espaciado entre títulos -->

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
                  <table id="tabla_ver_facturas_paci" class="display compact" style="width:100%; text-align:center;">
                    <thead style="background-color:#0252A0;color:#FFFFFF;">
                      <tr>
                        <th colspan="4" style="text-align:center; font-size: 18px; font-weight: bold;">DETALLE FACTURA</th>
                      </tr>
                      <tr style="text-align:center;">
                        <th style="text-align:center;">Nro.</th>
                        <th style="text-align:center;">DNI</th>
                        <th style="text-align:center;">Paciente - Práctica</th>
                        <th style="text-align:center;">Subtotal</th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr>
                        <th colspan="2" style="text-align:right;">Total:</th>
                        <th style="text-align:center;" id="total_sub_total">S/. 0.00</th>
                        <th></th>
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



  <div class="modal fade" id="modal_ver_historial" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div style="display: flex; flex-direction: column;color:black">
            <h5 class="modal-title" id="lb_titulo_historial"></h5>
            <h5 class="modal-title" id="lb_titulo_historial2" style="margin-top: 10px;"></h5> <!-- Espaciado entre títulos -->
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
                  <table id="tabla_ver_historial" class="display compact" style="width:100%; text-align:center;">
                    <thead style="background-color:#0252A0;color:#FFFFFF;">
                      <tr>
                        <th colspan="5" style="text-align:center; font-size: 18px; font-weight: bold;">HISTORIAL DE MODIFICACIÓN</th>
                      </tr>
                      <tr style="text-align:center;">
                        <th style="text-align:center;">Nro.</th>
                        <th style="text-align:center;">Usuario que modifico</th>
                        <th style="text-align:center;">Estado cambiado</th>
                        <th style="text-align:center;">Motivo</th>

                        <th style="text-align:center;">Fecha de modificación</th>
                      </tr>
                    </thead>

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
        // Inicializar select2 para prácticas
        $('#select_practica').select2({
          placeholder: "Seleccionar práctica - paciente",
          allowClear: true,
          width: '100%'
        });
        $('#select_practica_editar').select2({
          placeholder: "Seleccionar práctica - paciente",
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

        $('#select_practica').select2({
          dropdownParent: $('#modal_registro'),
          placeholder: "Seleccionar práctica - paciente",
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
          placeholder: "Seleccionar práctica - paciente",
          allowClear: true,
          width: '100%'
        });

      });

      // Manejar el cambio en obra social
      $('#select_obras').off('change').on('change', function() {
        var id = $(this).val();
        if (id) {
          // Cargar pacientes

          // Cargar prácticas
          $.ajax({
            url: "../controller/facturas/controlador_cargar_select_paciente_practica_factura.php",
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
                    options += `<option value="${item[0]}">DNI: ${item[1]} - ${item[2]}</option>`;
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
      Cargar_Select_Usuarios();
      listar_facturas_diario();
    });
    //TRAER DATOS DE PACIENTE



    </script>
