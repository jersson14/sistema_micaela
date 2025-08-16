<script src="../js/console_clientes.js?rev=<?php echo time(); ?>"></script>

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><b>MANTENIMIENTO DE CLIENTES</b></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
          <li class="breadcrumb-item active">CLIENTES</li>
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
            <h3 class="card-title"><i class="nav-icon fas fas fa-users"></i>&nbsp;&nbsp;<b>Listado de Clientes</b></h3>
          </div>
          <div class="table-responsive" style="text-align:center">
            <div class="card-body">
              <table id="tabla_clientes" class="table table-striped table-bordered" style="width:100%">
                <thead style="background-color:#023D77;color:#FFFFFF;">
                  <tr>
                    <th style="text-align:center">Nro.</th>
                    <th style="text-align:center">Documento de Identidad</th>
                    <th style="text-align:center">Cliente</th>
                    <th style="text-align:center">Procedencia</th>
                    <th style="text-align:center">Celular</th>
                    <th style="text-align:center">Dirección</th>
                    <th style="text-align:center">Total de viajes</th>
                    <th style="text-align:center">Ultimo viaje</th>
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

  <div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1FA0E0;">
          <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>EDITAR DATOS DEL CLIENTE</b></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        <div class="row">
            <div class="col-12 form-group" style="color:red">
              <h6><b>Campos Obligatorios (*)</b></h6>
            </div>
            <div class="col-6 form-group">
              <label for="">Tipo de documento<b style="color:red">(*)</b>:</label>
              <input type="text" id="txt_idcliente" hidden>
              <select class="form-control" id="select_tipo_doc" style="width:100%">
                  <option value="" disabled selected>Seleccione</option>
                  <option value="DNI">DNI</option>
                  <option value="CARNET DE EXTRANJERIA">CARNET DE EXTRANJERIA</option>
                  <option value="PASAPORTE">PASAPORTE</option>
              </select>            
            </div>
            <div class="col-6 form-group">
              <label for="">Nro. Documento<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" placeholder="Ingrese el Nro de Documento" id="txt_nro_doc" onkeypress="return soloNumeros(event)">
            </div>
            <div class="col-6 form-group">
              <label for="">Nombres completos<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" placeholder="Ingrese los nombres" id="txt_nombres" onkeypress="return sololetras(event)">
            </div>
            <div class="col-6 form-group">
              <label for="">Procedencia(Opciona):</label>
              <input type="text" class="form-control" placeholder="Ingrese los apellidos" id="txt_procedencia" onkeypress="return sololetras(event)">
            </div>
            <div class="col-6 form-group">
              <label for="">Celular<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" placeholder="Ingrese el celular" id="txt_celular" onkeypress="return soloNumeros(event)">
            </div>
            <div class="col-6 form-group">
              <label for="">Email(Opcional):</label>
              <input type="email" class="form-control" placeholder="Ingrese el correo electronico" id="txt_email">
            </div>
            <div class="col-12 form-group">
              <label for="">Dirección(Opcional):</label>
              <textarea class="form-control" id="txt_direccion" rows="2" style="resize:none" placeholder="Ingrese la dirección"></textarea>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
          <button type="button" class="btn btn-success" onclick="Modificar_Cliente()"><i class="fas fa-edit"></i> Modificar</button>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="modal_mostrar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1FA0E0;">
          <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>DATOS DEL CLIENTE</b></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        <div class="row">
            <div class="col-12 form-group" style="color:red">
              <h6><b>*LOS DATOS AQUI SOLO SON DE VISTA*</b></h6>
            </div>
            <div class="col-6 form-group">
              <label for="">Tipo de documento:</label>
              <input type="text" id="txt_idcliente_mostrar" hidden>
              <select class="form-control" id="select_tipo_doc_mostrar" style="width:100%" disabled>
                  <option value="" disabled selected>Seleccione</option>
                  <option value="DNI">DNI</option>
                  <option value="RUC">RUC</option>
                  <option value="CARNET DE EXTRANJERIA">CARNET DE EXTRANJERIA</option>
                  <option value="PASAPORTE">PASAPORTE</option>
              </select>            
            </div>
            <div class="col-6 form-group">
              <label for="">Nro. Documento:</label>
              <input type="text" readonly class="form-control" placeholder="Ingrese el Nro de Documento" id="txt_nro_doc_mostrar" onkeypress="return soloNumeros(event)">
            </div>
            <div class="col-6 form-group">
              <label for="">Nombres completos:</label>
              <input type="text" readonly class="form-control" placeholder="Ingrese los nombres" id="txt_nombres_mostrar" onkeypress="return sololetras(event)">
            </div>
            <div class="col-6 form-group">
              <label for="">Procedencia:</label>
              <input type="text" readonly class="form-control" placeholder="Ingrese los apellidos" id="txt_proce_mostrar" onkeypress="return sololetras(event)">
            </div>
            <div class="col-6 form-group">
              <label for="">Celular:</label>
              <input type="text" readonly class="form-control" placeholder="Ingrese el celular" id="txt_celular_mostrar" onkeypress="return soloNumeros(event)">
            </div>
            <div class="col-6 form-group">
              <label for="">Dirección:</label>
              <textarea class="form-control" readonly id="txt_direccion_mostrar" rows="2" style="resize:none" placeholder="Ingrese la dirección"></textarea>
            </div>
            <div class="col-4 form-group">
              <label for="">Email:</label>
              <input type="email" readonly class="form-control" placeholder="Ingrese el correo electronico" id="txt_email_mostrar">
            </div>
             <div class="col-4 form-group">
              <label for="">Total viajes:</label>
              <input type="text" readonly class="form-control" placeholder="Ingrese la región" id="txt_total_viaje_mostrar">
            </div>
             <div class="col-4 form-group">
              <label for="">Fecha de Ultimo viaje:</label>
              <input type="text" readonly class="form-control" placeholder="Ingrese la provincia" id="txt_ulti_viaje_mostrar">
            </div>
             <div class="col-6 form-group">
              <label for="">Fecha de registro:</label>
              <input type="text" readonly class="form-control" placeholder="Ingrese la provincia" id="txt_fecha_regis">
            </div>
             <div class="col-6 form-group">
              <label for="">Fecha de ultima actualización:</label>
              <input type="text" readonly class="form-control" placeholder="Ingrese la provincia" id="txt_fecha_actualiza">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
        listar_clientes();

    });



  </script>