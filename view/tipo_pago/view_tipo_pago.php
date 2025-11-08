<script src="../js/console_tipo_pago.js?rev=<?php echo time(); ?>"></script>

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><b>MANTENIMIENTO DE TIPOS DE PAGO</b></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
          <li class="breadcrumb-item active">TIPOS DE PAGO</li>
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
            <h3 class="card-title"><i class="nav-icon fas fa-th"></i>&nbsp;&nbsp;<b>Listado de la tipos de pago</b></h3>
            <button class="btn btn-success float-right" onclick="AbrirRegistro()"><i class="fas fa-plus"></i> Nuevo Registro</button>
          </div>
          <div class="table-responsive" style="text-align:center">
            <div class="card-body" style="display: block;">
              <table id="tabla_tipo_pagos" class="table table-striped table-bordered" style="width:100%; border-radius: 10px; overflow: hidden; border-collapse: separate; border-spacing: 0;">
                <thead style="background-color:#023D77;color:white;">
                  <tr>
                    <th style="text-align:center; border-top: none;">Nro.</th>
                    <th style="text-align:center; border-top: none;">Tipo pago</th>
                    <th style="text-align:center; border-top: none;">Descripción</th>
                    <th style="text-align:center; border-top: none;">Fecha de creación</th>
                    <th style="text-align:center; border-top: none;">Fecha de actualización</th>
                    <th style="text-align:center; border-top: none;">Estado</th>
                    <th style="text-align:center; border-top: none;">Acciones</th>
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
          <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>REGISTRO DE TIPO DE PAGO</b></h5>
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
              <label for="">Nombre de tipo de pago<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_nombre_tipopago" placeholder="Ingrese el nombre del tipo de pago" maxlength="50">
            </div>

            <div class="col-12 form-group">
              <label for="">Descripción(opcional):</label>
              <textarea name="" id="txt_descripcion" rows="3" class="form-control" style="resize:none;" placeholder="Ingrese una descripción"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
          <button type="button" class="btn btn-success" onclick="Registrar_Sucursal()"><i class="fas fa-save"></i> Registrar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /.content-wrapper -->
  <div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1FA0E0;">
          <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>EDITAR DATOS DEL TIPO DE PAGO</b></h5>
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
            <input type="text" id="txt_idtipo_pago" hidden>
              <label for="">Nombre de tipo de pago<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_nombre_tipopago_editar" placeholder="Ingrese el nombre del tipo de pago" maxlength="50">
            </div>
            <div class="col-12 form-group">
              <label for="">Descripción(opcional):</label>
              <textarea name="" id="txt_descripcion_editar" rows="3" class="form-control" style="resize:none;" placeholder="Ingrese una descripción"></textarea>
            </div>
            <div class="col-12 form-group">
              <label for="">Estado<b style="color:red">(*)</b>:</label>
              <select class="form-control" id="select_estado_editar" style="width:100%">
                <option value="">Seleccione</option>
                <option value="ACTIVO">ACTIVO</option>
                <option value="INACTIVO">INACTIVO</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
          <button type="button" class="btn btn-success" onclick="Modificar_Sucursal()"><i class="fas fa-check"></i> Modificar</button>
        </div>
      </div>
    </div>
  </div>


  <script>
    $(document).ready(function() {
      listar_tipo_pago();

    });
    $('#modal_registro').on('shown.bs.modal', function() {
      $('#txt_nombre_rol').trigger('focus')
    })
  </script>