<script src="../js/console_paciente.js?rev=<?php echo time();?>"></script>

<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><b>MANTENIMIENTO DE PACIENTES</b></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
              <li class="breadcrumb-item active">PACIENTES</li>
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
                <h3 class="card-title"><i class="fas fa-users"></i>&nbsp;&nbsp;<b>Listado de Pacientes</b></h3>
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
                            <button onclick="listar_paciente_filtro()" class="btn btn-danger mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Buscar pacientes</button>
                        </div>
                        <div class="col-12 col-md-2" role="document">
                            <label for="">&nbsp;</label><br>
                            <button onclick="listar_paciente()" class="btn btn-success mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Listar todo</button>
                        </div>
                    </div>
                </div>
              </div>
              <div class="table-responsive" style="text-align:center">
              <div class="card-body">
              <table id="tabla_paciente" class="table table-striped table-bordered" style="width:100%">
                  <thead style="background-color:#023D77;color:#FFFFFF;">
                      <tr>
                          <th style="text-align:center">Nro.</th>
                          <th style="text-align:center">DNI</th>
                          <th style="text-align:center">Paciente</th>
                          <th style="text-align:center">Dirección</th>
                          <th style="text-align:center">Localidad</th>
                          <th style="text-align:center">Teléfono</th>
                          <th style="text-align:center">Obra Social</th>
                          <th style="text-align:center">Fecha de Registro</th>
                          <th style="text-align:center">Fecha de Actualización</th>
                          <th style="text-align:center">Usuario que Actualizo</th>
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
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#1FA0E0;">
        <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>REGISTRO DE PACIENTES</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 form-group" style="color:red">
            <h6><b>Campos Obligatorios (*)</b></h6>
          </div><br>
          <div class="col-4 form-group">
            <label for="">DNI<b style="color:red">(*)</b>:</label>
            <input type="text" class="form-control" id="txt_nro" placeholder="Ingrese el DNI" onkeypress="return soloNumeros(event)" maxlenght="8">
          </div>
          <div class="col-4 form-group">
            <label for="">Nombres<b style="color:red">(*)</b>:</label>
            <input type="text" class="form-control" id="txt_nom" placeholder="Ingrese los nombres" onkeypress="return sololetras(event)">
          </div>
          <div class="col-4 form-group">
            <label for="">Apellidos<b style="color:red">(*)</b>:</label>
            <input type="text" class="form-control" id="txt_apelli" placeholder="Ingrese los apellidos" onkeypress="return sololetras(event)">
          </div>
          <div class="col-12 form-group">
            <label for="">Dirección(Opcional):</label>
            <textarea class="form-control" id="txt_direccion" rows="3" style="resize:none" placeholder="Ingrese la dirección"></textarea>
            </div>
          <div class="col-6 form-group">
            <label for="">Localidad(Opcional):</label>
            <input type="text" class="form-control" id="txt_local" placeholder="Ingrese la localidad">
          </div>
          <div class="col-6 form-group">
            <label for="">Teléfono<b style="color:red">(*)</b>:</label>
            <input type="text" class="form-control" id="txt_tele" onkeypress="return soloNumeros(event)" placeholder="Ingrese el teléfono o celular">
          </div>
          <div class="col-12 form-group">
              <label for="">Obra Social<b style="color:red">(*)</b>:</label>
              <select type="text" class="js-example-basic-single" id="txt_obras_sociales" style="width:100%"></select>
          </div>
         
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
        <button type="button" class="btn btn-success" onclick="Registrar_Paciente()"><i class="fas fa-save"></i> Registrar</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modal_mostrar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#1FA0E0;">
        <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>DATOS DE PACIENTE</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">

          <div class="col-4 form-group">
            <label for="">DNI:</label>
            <input type="text" class="form-control" id="txt_nro_mostrar" placeholder="Ingrese el DNI" onkeypress="return soloNumeros(event)" maxlenght="8" readonly>
          </div>
          <div class="col-4 form-group">
            <label for="">Nombres:</label>
            <input type="text" class="form-control" id="txt_nom_mostrar" placeholder="Ingrese los nombres" onkeypress="return sololetras(event)" readonly>
          </div>
          <div class="col-4 form-group">
            <label for="">Apellidos:</label>
            <input type="text" class="form-control" id="txt_apelli_mostrar" placeholder="Ingrese los apellidos" onkeypress="return sololetras(event)" readonly>
          </div>
          <div class="col-12 form-group">
            <label for="">Dirección:</label>
            <textarea class="form-control" id="txt_direccion_mostrar" rows="3" style="resize:none" placeholder="Ingrese la dirección" readonly></textarea>
            </div>
          <div class="col-6 form-group">
            <label for="">Localidad:</label>
            <input type="text" class="form-control" id="txt_local_mostrar" placeholder="Ingrese la localidad" readonly>
          </div>
          <div class="col-6 form-group">
            <label for="">Teléfono:</label>
            <input type="text" class="form-control" id="txt_tele_mostrar" onkeypress="return soloNumeros(event)" placeholder="Ingrese el teléfono o celular" readonly>
          </div>
          <div class="col-12 form-group">
              <label for="">Obra Social:</label>
              <select type="text" class="form-control js-example-basic-single" id="txt_obras_sociales_mostrar" style="width:100%" disabled></select>
          </div>
         
          <div class="col-3 form-group">
            <label for="">Fecha Registro:</label>
            <input type="text" class="form-control" id="txt_fecha_reg"  readonly>
          </div>
          <div class="col-3 form-group">
            <label for="">Fecha de Actualización:</label>
            <input type="text" class="form-control" id="txt_fecha_actu" readonly>
          </div>
          <div class="col-6 form-group">
            <label for="">Usuario que actualizo/registro:</label>
            <input type="text" class="form-control" id="txt_usu" readonly>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#1FA0E0;">
        <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>MODIFICAR DATOS DE PACIENTE</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
        <div class="col-12 form-group" style="color:red">
            <h6><b>Campos Obligatorios (*)</b></h6>
          </div><br>
          <div class="col-4 form-group">
            <label for="">DNI<b style="color:red">(*)</b>:</label>
            <input type="text" id="txt_id_paciente" hidden>
            <input type="text" class="form-control" id="txt_nro_editar" placeholder="Ingrese el DNI" onkeypress="return soloNumeros(event)" maxlenght="8">
          </div>
          <div class="col-4 form-group">
            <label for="">Nombres<b style="color:red">(*)</b>:</label>
            <input type="text" class="form-control" id="txt_nom_editar" placeholder="Ingrese los nombres" onkeypress="return sololetras(event)">
          </div>
          <div class="col-4 form-group">
            <label for="">Apellidos<b style="color:red">(*)</b>:</label>
            <input type="text" class="form-control" id="txt_apelli_editar" placeholder="Ingrese los apellidos" onkeypress="return sololetras(event)">
          </div>
          <div class="col-12 form-group">
            <label for="">Dirección(Opcional):</label>
            <textarea class="form-control" id="txt_direccion_editar" rows="3" style="resize:none" placeholder="Ingrese la dirección"></textarea>
            </div>
          <div class="col-6 form-group">
            <label for="">Localidad(Opcional):</label>
            <input type="text" class="form-control" id="txt_local_editar" placeholder="Ingrese la localidad">
          </div>
          <div class="col-6 form-group">
            <label for="">Teléfono<b style="color:red">(*)</b>:</label>
            <input type="text" class="form-control" id="txt_tele_editar" onkeypress="return soloNumeros(event)" placeholder="Ingrese el teléfono o celular">
          </div>
          <div class="col-12 form-group">
              <label for="">Obra Social<b style="color:red">(*)</b>:</label>
              <select type="text" class="js-example-basic-single" id="txt_obras_sociales_editar" style="width:100%"></select>
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

<script>
$(document).ready(function () {
  $('.js-example-basic-single').select2({
    placeholder: "Seleccionar Obra Social",
    allowClear: true
  });
  Cargar_Select_Obras_Sociales();
  listar_paciente();
});
$('#modal_registro').on('shown.bs.modal', function () {
  $('#txt_nro').trigger('focus')
})
var input=  document.getElementById('txt_nro');
input.addEventListener('input',function(){
  if (this.value.length > 8) 
     this.value = this.value.slice(0,8); 
})
var input=  document.getElementById('txt_tele');
input.addEventListener('input',function(){
  if (this.value.length > 11) 
     this.value = this.value.slice(0,11); 
})
var input=  document.getElementById('txt_nro_editar');
input.addEventListener('input',function(){
  if (this.value.length > 8) 
     this.value = this.value.slice(0,8); 
})
var input=  document.getElementById('txt_tele_editar');
input.addEventListener('input',function(){
  if (this.value.length > 11) 
     this.value = this.value.slice(0,9); 
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
