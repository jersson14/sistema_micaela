<script src="../js/console_sucursal.js?rev=<?php echo time(); ?>"></script>

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><b>MANTENIMIENTO DE SUCURSALES</b></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
          <li class="breadcrumb-item active">SUCURSALES</li>
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
            <h3 class="card-title"><i class="nav-icon fas fa-th"></i>&nbsp;&nbsp;<b>Listado de la sucursales</b></h3>
            <button class="btn btn-success float-right" onclick="AbrirRegistro()"><i class="fas fa-plus"></i> Nuevo Registro</button>
          </div>
          <div class="table-responsive" style="text-align:center">
            <div class="card-body" style="display: block;">
              <table id="tabla_sucursal" class="table table-striped table-bordered" style="width:100%; border-radius: 10px; overflow: hidden; border-collapse: separate; border-spacing: 0;">
                <thead style="background-color:#023D77;color:white;">
                  <tr>
                    <th style="text-align:center; border-top: none;">Nro.</th>
                    <th style="text-align:center; border-top: none;">Sucursal</th>
                    <th style="text-align:center; border-top: none;">Telefono 1</th>
                    <th style="text-align:center; border-top: none;">Teléfono 2</th>
                    <th style="text-align:center; border-top: none;">Dirección</th>
                    <th style="text-align:center; border-top: none;">Descripción</th>
                    <th style="text-align:center; border-top: none;">Fecha de creación</th>
                    <th style="text-align:center; border-top: none;">Fecha de ultima actualización</th>
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
          <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>REGISTRO DE SUCURSALES</b></h5>
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
              <label for="">Nombre de sucursal<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_nombre_sucur" onkeypress="return sololetras(event)" placeholder="Ingrese el nombre de la sucursal" maxlength="50">
            </div>
            <div class="col-12 form-group">
              <label for="">Teléfono 1<b style="color:red">(*)</b>:</label>
              <input type="text" placeholder="Ingrese el primer teléfono" class="form-control" id="txt_tele1" onkeypress="return soloNumeros(event)" maxlenght="9">
            </div>
            <div class="col-12 form-group">
              <label for="">Teléfono 2(opcional):</label>
              <input type="text" placeholder="Ingrese el segundo teléfono" class="form-control" id="txt_tele2" onkeypress="return soloNumeros(event)" maxlenght="9">
            </div>
            <div class="col-12 form-group">
              <label for="">Dirección<b style="color:red">(*)</b>:</label>
              <input type="text" placeholder="Ingrese la dirección" class="form-control" id="txt_direccion">
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
          <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>EDITAR DATOS DE LA SUCURSAL</b></h5>
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
              <input type="text" id="txt_id_sucursal" hidden>
              <label for="">Nombre de sucursal<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_nombre_sucur_editar" onkeypress="return sololetras(event)" placeholder="Ingrese el nombre de la sucursal" maxlength="50">
            </div>
            <div class="col-12 form-group">
              <label for="">Teléfono 1<b style="color:red">(*)</b>:</label>
              <input type="text" placeholder="Ingrese el primer teléfono" class="form-control" id="txt_tele1_editar" onkeypress="return soloNumeros(event)" maxlenght="9">
            </div>
            <div class="col-12 form-group">
              <label for="">Teléfono 2(opcional):</label>
              <input type="text" placeholder="Ingrese el segundo teléfono" class="form-control" id="txt_tele2_editar" onkeypress="return soloNumeros(event)" maxlenght="9">
            </div>
            <div class="col-12 form-group">
              <label for="">Dirección<b style="color:red">(*)</b>:</label>
              <input type="text" placeholder="Ingrese la dirección" class="form-control" id="txt_direccion_editar">
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
      listar_sucursales();

    });
    $('#modal_registro').on('shown.bs.modal', function() {
      $('#txt_nombre_rol').trigger('focus')
    })
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
    document.getElementById('txt_fecha_registro').value = y + "-" + m + "-" + d;


    var input = document.getElementById('txt_tele1');
    input.addEventListener('input', function() {
      if (this.value.length > 9)
        this.value = this.value.slice(0, 9);
    })

    var input = document.getElementById('txt_tele2');
    input.addEventListener('input', function() {
      if (this.value.length > 9)
        this.value = this.value.slice(0, 9);
    })
  </script>