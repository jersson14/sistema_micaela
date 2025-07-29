<script src="../js/console_practicas.js?rev=<?php echo time(); ?>"></script>

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><b>MANTENIMIENTO DE PRÁCTICAS</b></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
          <li class="breadcrumb-item active">PRÁCTICAS</li>
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
            <h3 class="card-title"><i class="fas fa-users"></i>&nbsp;&nbsp;<b>Listado de Prácticas</b></h3>
            <button class="btn btn-success float-right" onclick="AbrirRegistro()"><i class="fas fa-plus"></i> Nuevo Registro</button>
          </div>
          <div class="table-responsive" style="text-align:left">
            <div class="card-body">
              <div class="row">
                <div class="col-4 form-group">
                  <label for="">Obras Sociales<b style="color:red">(*)</b>:</label>
                  <select class="js-example-basic-single" id="select_obras" style="width:100%">
                  </select>
                </div>
                <div class="col-2 form-group">
                  <label for="">Fecha inicio</label><b style="color:red">(*)</b>:</label>
                  <input type="date" class="form-control" id="txtfechainicio">

                </div>
                <div class="col-2 form-group">
                  <label for="">Fecha final<b style="color:red">(*)</b>:</label>
                  <input type="date" class="form-control" id="txtfechafin">

                </div>
                <div class="col-12 col-md-2" role="document">
                  <label for="">&nbsp;</label><br>
                  <button onclick="listar_practicas_filtro()" class="btn btn-danger mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Buscar prácticas</button>
                </div>
                <div class="col-12 col-md-2" role="document">
                  <label for="">&nbsp;</label><br>
                  <button onclick="listar_practicas()" class="btn btn-success mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Listar todo</button>
                </div>
              </div>
            </div>
          </div>
          <div class="table-responsive" style="text-align:center">
            <div class="card-body">
              <table id="tabla_practicas" class="table table-striped table-bordered" style="width:100%">
                <thead style="background-color:#023D77;color:#FFFFFF;">
                  <tr>
                    <th style="text-align:center">Nro.</th>
                    <th style="text-align:center">Código</th>
                    <th style="text-align:center">Práctica</th>
                    <th style="text-align:center">Valor monetario</th>
                    <th style="text-align:center">Obra Social</th>
                    <th style="text-align:center">Fecha de Registro</th>
                    <th style="text-align:center">Fecha de Actualización</th>
                    <th style="text-align:center">Usuario que Actualizo</th>
                    <th style="text-align:center">Estado</th>
                    <th style="text-align:center">Acción</th>
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
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1FA0E0;">
          <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>REGISTRO DE PRÁCTICA</b></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-12 form-group" style="color:red">
              <h6><b>Campos Obligatorios (*)</b></h6>
            </div><br>
            <div class="col-12 form-group">
              <label for="">Código<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_code" placeholder="Ingrese el código de la práctica">
            </div>
            <div class="col-12 form-group">
              <label for="">Práctica<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_practica" placeholder="Ingrese el nombre">
            </div>
            <div class="col-12 form-group">
              <label for="">Valor monetario<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_valor" placeholder="Ingrese el valor monetario" onkeypress="return soloNumeros(event)">
            </div>
            <div class="col-12 form-group">
              <label for="">Obra Social<b style="color:red">(*)</b>:</label>
              <select type="text" class="js-example-basic-single" id="txt_obras_sociales" style="width:100%"></select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
          <button type="button" class="btn btn-success" onclick="Registrar_Practicas()"><i class="fas fa-save"></i> Registrar</button>
        </div>
      </div>
    </div>
  </div>




  <div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1FA0E0;">
          <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>MODIFICAR DATOS DE PRÁCTICA</b></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-12 form-group" style="color:red">
              <h6><b>Campos Obligatorios (*)</b></h6>
            </div><br>
            <div class="col-12 form-group">
              <label for="">Código<b style="color:red">(*)</b>:</label>
              <input type="text" id="txt_id_practica" hidden>
              <input type="text" class="form-control" id="txt_code_editar" placeholder="Ingrese el código de la práctica">
            </div>
            <div class="col-12 form-group">
              <label for="">Práctica<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_practica_editar" placeholder="Ingrese el nombre">
            </div>
            <div class="col-12 form-group">
              <label for="">Valor monetario<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_valor_editar" placeholder="Ingrese el valor monetario" onkeypress="return soloNumeros(event)">
            </div>
            <div class="col-12 form-group">
              <label for="">Obra Social<b style="color:red">(*)</b>:</label>
              <select type="text" class="js-example-basic-single" id="txt_obras_sociales_editar" style="width:100%"></select>
            </div>
            <div class="col-12 form-group">
              <label for="">Estado<b style="color:red">(*)</b>:</label>
              <select name="" id="txt_estatus" class="form-control">
                <option value="ACTIVO">ACTIVO</option>
                <option value="INACTIVO">INACTIVO</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
          <button type="button" class="btn btn-success" onclick="Modificar_Paciente()"><i class="fas fa-edit"></i> Modificar</button>

        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_ver_historial" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div style="display: flex; flex-direction: column;">
            <h5 class="modal-title" id="lb_titulo"></h5>
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
                        <th colspan="4" style="text-align:center; font-size: 18px; font-weight: bold;">HISTORIAL DE PRÁCTICAS</th>
                      </tr>
                      <tr style="text-align:center;">
                        <th style="text-align:center;">Nro.</th>
                        <th style="text-align:center;">Usuario que modifico</th>
                        <th style="text-align:center;">Valor cambiado</th>
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




  <script>
    $(document).ready(function() {
      $('.js-example-basic-single').select2({
        placeholder: "Seleccionar Obra Social",
        allowClear: true
      });
      Cargar_Select_Obras_Sociales();
      listar_practicas();
    });
    $('#modal_registro').on('shown.bs.modal', function() {
      $('#select_obras').trigger('focus')
    })

    //var n = new Date();
    //var y= n.getFullYear();
    //var m= n.getMonth()+1;
    //var d= n.getDate();
    //if(d<10){
    //    d='0' + d;
    //}
    //if(m<10){
    //    m='0' + m;

    //}
    //document.getElementById('txtfechainicio').value = y + "-" + m + "-" + d;
    //document.getElementById('txtfechafin').value = y + "-" + m + "-" + d;
  </script>