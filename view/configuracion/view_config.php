<script src="../js/console_empresa.js?rev=<?php echo time();?>"></script>

<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><b>MANTENIMIENTO DE LA EMPRESA</b></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
              <li class="breadcrumb-item active">EMPRESA</li>
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
                <h3 class="card-title"><i class="nav-icon fas fa-th"></i>&nbsp;&nbsp;<b>Listado de la empresa</b></h3>
              </div>
               <div class="table-responsive" style="text-align:center">
              <div class="card-body" style="display: block;">
                <table id="tabla_empresa" class="table table-striped table-bordered" style="width:100%; border-radius: 10px; overflow: hidden; border-collapse: separate; border-spacing: 0;">
                  <thead style="background-color:#023D77;color:white;">
                    <tr>
                      <th style="text-align:center; border-top: none;">Nro.</th>
                      <th style="text-align:center; border-top: none;">Logo</th>
                      <th style="text-align:center; border-top: none;">Nombre</th>
                      <th style="text-align:center; border-top: none;">Email</th>
                      <th style="text-align:center; border-top: none;">Código</th>
                      <th style="text-align:center; border-top: none;">Teléfono</th>
                      <th style="text-align:center; border-top: none;">Dirección</th>
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

        <!-- /.content-wrapper -->
        <div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header" style="background-color:#1FA0E0;">
                <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>EDITAR DATOS DE LA EMPRESA</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-12 form-group" style="color:red">
                    <h6><b>Campos Obligatorios (*)</b></h6>
                  </div><br>
                  <div class="col-6 form-group">
                    <input type="text" id="txt_id_empresa" hidden>
                    <label for="">Nombre<b style="color:red">(*)</b>:</label>
                    <input type="text" class="form-control" id="txt_nombre">
                  </div>
                  <div class="col-6 form-group">
                    <label for="">Razon social<b style="color:red">(*)</b>:</label>
                    <input type="text" class="form-control" id="txt_razon" >
                  </div>
                   <div class="col-6 form-group">
                    <label for="">Nombre Comercial<b style="color:red">(*)</b>:</label>
                    <input type="text" class="form-control" id="txt_nombre_co" >
                  </div>
                   <div class="col-6 form-group">
                    <label for="">Tipo Documento<b style="color:red">(*)</b>:</label>
                    <input type="text" class="form-control" id="txt_tipo_doc" >
                  </div>
                  <div class="col-6 form-group">
                    <label for="">N° Documento<b style="color:red">(*)</b>:</label>
                    <input type="text" class="form-control" id="txt_nro_doc" >
                  </div>
                  <div class="col-6 form-group">
                    <label for="">Email(opcional):</label>
                    <input type="text" class="form-control" id="txt_email">
                  </div>
                  <div class="col-6 form-group">
                    <label for="">Código(opcional):</label>
                    <input type="text" class="form-control" id="txt_codigo">
                  </div>
                  <div class="col-6 form-group">
                    <label for="">Teléfono / Celular<b style="color:red">(*)</b>:</label>
                    <input type="text" class="form-control" id="txt_telefono" maxlenght="9" onkeypress="return soloNumeros(event)">
                  </div>
                  <div class="col-12 form-group">
                    <label for="">Dirección<b style="color:red">(*)</b>:</label>
                    <input type="text" class="form-control" id="txt_direccion">
                  </div>
                    <div class="col-6 form-group">
                    <label for="">Ubigeo(opcional):</label>
                    <input type="text" class="form-control" id="txt_ubigeo">
                  </div>
                    <div class="col-6 form-group">
                    <label for="">Urbanización(opcional):</label>
                    <input type="text" class="form-control" id="txt_urbanizacion">
                  </div>
                    <div class="col-4 form-group">
                    <label for="">Distrito(opcional):</label>
                    <input type="text" class="form-control" id="txt_distrito">
                  </div>
                    <div class="col-4 form-group">
                    <label for="">Provincia(opcional):</label>
                    <input type="text" class="form-control" id="txt_provincia">
                  </div>
                <div class="col-4 form-group">
                    <label for="">Departamento(opcional):</label>
                    <input type="text" class="form-control" id="txt_departamento">
                  </div>
                <div class="col-4 form-group">
                    <label for="">Código de País(opcional):</label>
                    <input type="text" class="form-control" id="txt_codigo_pais">
                  </div>
                <div class="col-4 form-group">
                    <label for="">Usuario SOL(Solo factuación):</label>
                    <input type="text" class="form-control" id="txt_usuario_sol">
                  </div>
                    <div class="col-4 form-group">
                    <label for="">Clave SOL(Solo factuación):</label>
                    <input type="password" class="form-control" id="txt_clave_sol">
                  </div>

                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
                <button type="button" class="btn btn-success" onclick="Modificar_empresa()"><i class="fas fa-check"></i> Modificar</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="modal_editar_foto" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header" style="background: linear-gradient(135deg, #023D77, #0266C8)">
                <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>EDITAR FOTO DE LA INSTITUCIÓN: </b><label for="" id="lb_empresa"></label></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-12">
                    <input type="text" id="fotoactual" hidden>
                    <input type="text" id="txt_idempresa_foto" hidden>
                    <label for="checkboxSuccess2" style="align:justify;color:red">
                      OJO: Una vez cambiado el logo, tambien se cambiara el logo en los reportes y ticket.
                    </label>
                    <label for="">Subir Foto:</label>
                    <input class="form-control" type="file" id="txt_foto">
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
                <button type="button" class="btn btn-success" onclick="Modificar_Foto_Empresa()"><i class="fas fa-check"></i> Modificar</button>
              </div>
            </div>
          </div>
        </div>
    <script>
    $(document).ready(function () {
    listar_empresa();
      
    });
    $('#modal_registro').on('shown.bs.modal', function () {
      $('#txt_area').trigger('focus')
    })
    </script>