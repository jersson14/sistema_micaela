<?php
session_start();
if (!isset($_SESSION['S_ID'])) {
  header('Location: ../index.php');
}
?>
<script src="../js/console_facturas_solo.js?rev=<?php echo time(); ?>"></script>
<link rel="stylesheet" href="../plantilla/plugins/icheck-bootstrap/icheck-bootstrap.min.css">

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><b>MANTENIMIENTO DE FACTURAS</b></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
          <li class="breadcrumb-item active">FACTURAS</li>
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
            <h3 class="card-title"><i class="nav-icon fas fa-th"></i>&nbsp;&nbsp;<b>Listado de Facturas</b></h3>
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
                <div class="col-2 form-group">
                  <label for="">Estado de Factura:</label>
                  <select class="form-control" id="select_estado" style="width:100%">
                    <option value="">Seleccione</option>
                    <option value="FACTURADA">FACTURADA</option>
                    <option value="COBRADA">COBRADA</option>
                    <option value="RECHAZADA">RECHAZADA</option>
                    <option value="PENDIENTE">PENDIENTE</option>

                  </select>
                </div>
                <div class="col-12 col-md-2" role="document">
                  <label for="">&nbsp;</label><br>
                  <button onclick="listar_practica_paciente_obras()" class="btn btn-danger mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Buscar registros</button>
                </div>
                <div class="col-12 col-md-2" role="document">
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
                    <th style="text-align:center">Saldo Cobrado</th>
                    <th style="text-align:center">Saldo Pendiente</th>
                    <th style="text-align:center">Ver Factura</th>
                    <th style="text-align:center">Ver Nota de crédito</th>
                    <th style="text-align:center">Fecha Nota de crédito</th>
                    <th style="text-align:center">Fecha registro</th>
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
  <div class="modal fade" id="modal_registro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1FA0E0;">
          <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center">
            <b>REGISTRO DE FACTURA</b>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row" style="border: 1px solid #ccc; padding: 15px; border-radius: 8px;">
            <div class="col-12 form-group" style="color:red">
              <h6><b>Campos Obligatorios (*)</b></h6>
            </div>

            <div class="col-12 form-group">
              <label>Nro. Factura<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_nro_factura" placeholder="Ingrese el Nro. de Factura">
            </div>

            <div class="col-12 form-group">
              <label>Filtro por Obra Social<b style="color:red">(*)</b>:</label>
              <select class="js-example-basic-single form-control" id="select_obras"></select>
            </div>

            <div class="col-9 form-group">
              <label>Pacientes que se realizaron prácticas<b style="color:red">(*)</b>:</label>
              <select class="js-example-basic-single form-control" id="select_practica"></select>
            </div>

            <div class="col-3 form-group">
              <label>Subtotal<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_precio" disabled>
            </div>

            <div class="col-12 form-group">
              <button type="button" class="btn btn-success btn-block" onclick="Agregar_practica()">
                <i class="fas fa-plus"></i> <b>Agregar Detalle</b>
              </button>
            </div>

            <!-- Tabla con marco -->
            <div class="col-12 table-responsive" style="text-align:center">
              <table id="tabla_detalle_factura" style="width:100%" class="table">
                <thead class="thead-dark">
                  <tr>
                    <th>Id.</th>
                    <th>Practica - Paciente</th>
                    <th>Subtotal</th>
                    <th>Acci&oacute;n</th>
                  </tr>
                </thead>
                <tbody id="tbody_tabla_practica">
                </tbody>
              </table>
              <div class="col-9">
              </div>
              <div class="d-flex justify-content-end">
                <div class="text-right">
                  <h3 id="lbl_totalneto1"></h3>
                  <hr>
                </div>
              </div>

            </div>
            <hr>
            <hr>
            <div class="col-6 form-group">
              <label>Archivo de Factura <b style="color:red">(*)</b>:</label>
              <div class="custom-file position-relative">
                <input type="file" class="custom-file-input" id="txt_factura" accept="image/*,application/pdf" onchange="updateFileLabel(event)">
                <label class="custom-file-label" id="label_txt_factura" for="txt_factura">Seleccione Factura...</label>
                <button type="button" class="btn btn-danger btn-sm btn-clear-file" id="btn_clear_factura" onclick="clearFactura()">X</button>
              </div>
            </div>

            <div class="col-6 form-group">
              <label>Fecha registro<b style="color:red">(*)</b>:</label>
              <input type="date" class="form-control" id="txt_fecha" disabled>
            </div>

            <div class="col-6 form-group">
              <label>Archivo de Nota de Crédito (Opcional):</label>
              <div class="custom-file position-relative">
                <input type="file" class="custom-file-input" id="txt_notacre" accept="image/*,application/pdf" onchange="updateFileLabel2(event)">

                <label class="custom-file-label" id="label_txt_notacre" for="txt_notacre">Seleccione Nota de crédito...</label>
                <button type="button" class="btn btn-danger btn-sm btn-clear-file" id="btn_clear_notacre" onclick="clearNotacre()">X</button>
              </div>
            </div>
            <div class="col-6 form-group">
              <label>Fecha Nota de Crédito (Opcional):</label>
              <input type="date" class="form-control" id="txt_fecha_nota">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">
            <i class="fas fa-times ml-1"></i> Cerrar
          </button>
          <button type="button" class="btn btn-success" onclick="Registrar_Practica_paciente()">
            <i class="fas fa-save"></i> Registrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1FA0E0;">
          <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center">
            <b>EDITAR DATOS DE FACTURA</b>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row" style="border: 1px solid #ccc; padding: 15px; border-radius: 8px;">
            <div class="col-12 form-group" style="color:red">
              <h6><b>Campos Obligatorios (*)</b></h6>
            </div>

            <div class="col-12 form-group">
              <label>Nro. Factura<b style="color:red">(*)</b>:</label>
              <input type="text" id="id_factura" hidden>
              <input type="text" class="form-control" id="txt_nro_factura_editar" placeholder="Ingrese el Nro. de Factura" disabled>
            </div>

            <div class="col-12 form-group">
              <label>Filtro por Obra Social<b style="color:red">(*)</b>:</label>
              <select class="js-example-basic-single form-control" id="select_obras_editar" disabled></select>
            </div>

            <div class="col-9 form-group">
              <label>Pacientes que se realizaron prácticas<b style="color:red">(*)</b>:</label>
              <select class="js-example-basic-single form-control" id="select_practica_editar"></select>
            </div>

            <div class="col-3 form-group">
              <label>Subtotal<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_precio_editar" disabled>
            </div>

            <div class="col-12 form-group">
              <button type="button" class="btn btn-success btn-block" onclick="Agregar_practica_editar()">
                <i class="fas fa-plus"></i> <b>Agregar Detalle</b>
              </button>
            </div>

            <!-- Tabla con marco -->
            <div class="col-12 table-responsive" style="text-align:center">
              <table id="tabla_detalle_factura_editar" style="width:100%" class="table">
                <thead class="thead-dark">
                  <tr>
                    <th>Id principal.</th>
                    <th>Id.</th>
                    <th>Practica - Paciente</th>
                    <th>Subtotal</th>
                    <th>Acci&oacute;n</th>
                  </tr>
                </thead>
                <tbody id="tbody_tabla_practica_editar">
                </tbody>
              </table>
              <div class="col-9">
              </div>
              <div class="d-flex justify-content-end">
                <div class="text-right"><br>
                  <h3 id="lbl_totalneto1_editar"></h3>
                  <hr>
                </div>
              </div>

            </div>
            <hr>
            <hr>
            <div class="col-6 form-group">
              <label>Archivo de Factura <b style="color:red">(*)</b>:</label>

              <div class="custom-file position-relative">
                <input type="text" id="facturaactual" hidden>

                <input type="file" class="custom-file-input" id="txt_factura_editar" accept="image/*,application/pdf" onchange="updateFileLabelEditar(event)">
                <label class="custom-file-label" id="label_txt_factura_editar" for="txt_factura_editar">Seleccione Factura...</label>
                <button type="button" class="btn btn-danger btn-sm btn-clear-file" id="btn_clear_factura_editar" onclick="clearFacturaeditar()">X</button>
              </div>
            </div>

            <div class="col-6 form-group">
              <label>Fecha de actualización<b style="color:red">(*)</b>:</label>
              <input type="date" class="form-control" id="txt_fecha_editar" disabled>
            </div>

            <div class="col-6 form-group">
              <label>Archivo de Nota de Crédito (Opcional):</label>
              <div class="custom-file position-relative">
                <input type="text" id="notaactual" hidden>

                <input type="file" class="custom-file-input" id="txt_notacre_editar" accept="image/*,application/pdf" onchange="updateFileLabel2_editar(event)">

                <label class="custom-file-label" id="label_txt_notacre_editar" for="txt_notacre_editar">Seleccione Nota de crédito...</label>
                <button type="button" class="btn btn-danger btn-sm btn-clear-file" id="btn_clear_notacre_editar" onclick="clearNotacreeditar()">X</button>
              </div>
            </div>
            <div class="col-6 form-group">
              <label>Fecha Nota de Crédito (Opcional):</label>
              <input type="date" class="form-control" id="txt_fecha_nota_editar">
            </div>

          </div>
          <div class="alert alert-warning alert-dismissible" style=" text-align: justify;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-exclamation-triangle"></i> ¡Aviso Importante!</h5>
            Si agregaste o eliminaste una práctica de un paciente, asegúrate de hacer clic en el botón <b>Modificar</b> para actualizar el TOTAL GENERAL en la BASE DE DATOS.
          </div>
        </div>



        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">
            <i class="fas fa-times ml-1"></i> Cerrar
          </button>
          <button type="button" class="btn btn-success" onclick="Modificar_Practica_paciente()">
            <i class="fas fa-edit"></i> Modificar
          </button>
        </div>
      </div>
    </div>
  </div>


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






  <div class="modal fade" id="modal_estado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1FA0E0;">
          <div style="display: flex; flex-direction: column;color:white">
            <h5 class="modal-title" id="lb_tituloesta"></h5>
            <h5 class="modal-title" id="lb_titulo2esta" style="margin-top: 10px;"></h5> <!-- Espaciado entre títulos -->
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
              <label for="">Estado<b style="color:red">(*)</b>:</label>
              <input type="text" id="id_estado" hidden>
              <select class="form-control" id="select_estado_edit" style="width:100%">
                <option value="FACTURADA">FACTURADA</option>
                <option value="COBRADA">COBRADA</option>
                <option value="RECHAZADA">RECHAZADA</option>
                <option value="PENDIENTE">PENDIENTE</option>
              </select>
            </div>
            <div class="col-12 form-group">
              <label for="">Motivo:</label>
              <textarea class="form-control" id="txt_motivo" rows="4" style="resize:none" placeholder="Ingrese el motivo"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
          <button type="button" class="btn btn-success" onclick="Modificar_Estado()"><i class="fas fa-edit"></i> Editar</button>
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



  <div class="modal fade" id="modal_pagar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1FA0E0;">
          <div style="display: flex; flex-direction: column;color:white">
            <h5 class="modal-title" id="lb_tituloesta_pagar"></h5>
            <h5 class="modal-title" id="lb_titulo2esta_pagar" style="margin-top: 10px;"></h5> <!-- Espaciado entre títulos -->
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
              <label for="">Saldo Pendiente<b style="color:red">(*)</b>:</label>
              <input type="text" id="id_pago" hidden>
              <input type="text" class="form-control" id="txt_total" disabled>
            </div>
            <div class="col-12 form-group">
              <label for="">Monto a Cancelar<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_pagar" placeholder="Ingrese el monto a cancelar" onkeypress="return soloNumeros(event)">
            </div>
            <div class="col-12 form-group">
              <label for="">Nuevo Saldo Pendiente<b style="color:red">(*)</b>:</label>
              <input type="text" class="form-control" id="txt_saldo" disabled>
            </div>
            <div class="col-12 form-group">
              <label for="">Fecha de pago<b style="color:red">(*)</b>:</label>
              <input type="date" class="form-control" id="txt_fecha_pago" disabled>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
          <button type="button" class="btn btn-success" onclick="Realizar_pago()"><i class="fas fa-check"></i> Pagar</button>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="modal_ver_pagos" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div style="display: flex; flex-direction: column;color:black">
            <h5 class="modal-title" id="lb_tituloesta_history"></h5>
            <h5 class="modal-title" id="lb_titulo2esta_history" style="margin-top: 10px;"></h5> <!-- Espaciado entre títulos -->
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
                  <table id="tabla_ver_pagos" class="display compact" style="width:100%; text-align:center;">
                    <thead style="background-color:#0252A0;color:#FFFFFF;">
                      <tr>
                        <th colspan="6" style="text-align:center; font-size: 18px; font-weight: bold;">HISTORIAL DE PAGOS</th>
                      </tr>
                      <tr style="text-align:center;">
                        <th style="text-align:center;">Nro.</th>
                        <th style="text-align:center;">Usuario que registro</th>
                        <th style="text-align:center;">Monto cancelado</th>
                        <th style="text-align:center;">Fecha de pago</th>
                        <th style="text-align:center;">Estado</th>
                        <th style="text-align:center;">Acción</th>
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


  <div class="modal fade" id="modal_ver_anulado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#1FA0E0;">
          <div style="display: flex; flex-direction: column;color:white">
            <h5 class="modal-title"><b>MOTIVO DE ANULACIÓN</b></h5>
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
              <label for="">Fecha de anulación:</label>
              <input type="datetime" class="form-control" id="txt_fecha_anulado2" disabled>
            </div>
            <div class="col-12 form-group">
              <label for="">Motivo:</label>
              <textarea class="form-control" id="txt_motivo2" disabled rows="4" style="resize:none" placeholder="Ingrese el motivo"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
          <button type="button" class="btn btn-success" onclick="Realizar_pago()"><i class="fas fa-check"></i> Pagar</button>
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
    // Add an event listener to the payment input field
    document.getElementById('txt_pagar').addEventListener('input', calculateBalance);

    // Function to calculate the balance
    function calculateBalance() {
      // Get the values from the fields
      const totalValue = parseFloat(document.getElementById('txt_total').value) || 0;
      const paymentValue = parseFloat(document.getElementById('txt_pagar').value) || 0;

      // Calculate the balance
      const balance = totalValue - paymentValue;

      // Display the balance in the saldo field
      document.getElementById('txt_saldo').value = balance.toFixed(2);
    }

    // Also calculate when the total field changes
    document.getElementById('txt_total').addEventListener('input', calculateBalance);

    // Initialize the calculation when the modal opens
    $('#modal_pagar').on('shown.bs.modal', function() {
      calculateBalance();
    });
  </script>
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
      Cargar_Select_Obras_Sociales2();
      Cargar_Select_Usuarios();
      Cargar_Select_Areas();
      listar_facturas_diario();
    });
    //TRAER DATOS DE PACIENTE




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
    document.getElementById('txt_fecha_editar').value = y + "-" + m + "-" + d;
    document.getElementById('txt_fecha').value = y + "-" + m + "-" + d;
    document.getElementById('txt_fecha_pago').value = y + "-" + m + "-" + d;
  </script>

  <script>
    function updateFileLabel(event) {
      var input = event.target;
      var label = document.getElementById('label_txt_factura');

      if (input.files && input.files[0]) {
        var fileName = input.files[0].name;
        label.innerHTML = "Subir Factura (" + fileName + ")";
      }
    }

    function clearFactura() {
      var fileInput = document.getElementById('txt_factura');
      var fileLabel = document.getElementById('label_txt_factura');

      // Limpiar el input de archivo
      fileInput.value = '';

      // Restablecer el texto del label
      fileLabel.innerHTML = "Seleccione Factura...";
    }
  </script>

  <script>
    function updateFileLabel2(event) {
      var input = event.target;
      var label = document.getElementById('label_txt_notacre');

      if (input.files && input.files[0]) {
        var fileName = input.files[0].name;
        label.innerHTML = "Subir Nota de crédito (" + fileName + ")";
      }
    }

    function clearNotacre() {
      var fileInput = document.getElementById('txt_notacre');
      var fileLabel = document.getElementById('label_txt_notacre');

      // Limpiar el input de archivo
      fileInput.value = '';

      // Restablecer el texto del label
      fileLabel.innerHTML = "Seleccione Nota de crédito...";
    }
  </script>

  <script>
    function updateFileLabelEditar(event) {
      var input = event.target;
      var label = document.getElementById('label_txt_factura_editar');

      if (input.files && input.files[0]) {
        var fileName = input.files[0].name;
        label.innerHTML = "Subir Factura (" + fileName + ")";
      }
    }

    function clearFacturaeditar() {
      var fileInput = document.getElementById('txt_factura_editar');
      var fileLabel = document.getElementById('label_txt_factura_editar');

      // Limpiar el input de archivo
      fileInput.value = '';

      // Restablecer el texto del label
      fileLabel.innerHTML = "Seleccione Factura...";
    }
  </script>

  <script>
    function updateFileLabel2_editar(event) {
      var input = event.target;
      var label = document.getElementById('label_txt_notacre_editar');

      if (input.files && input.files[0]) {
        var fileName = input.files[0].name;
        label.innerHTML = "Subir Nota de crédito (" + fileName + ")";
      }
    }

    function clearNotacreeditar() {
      var fileInput = document.getElementById('txt_notacre_editar');
      var fileLabel = document.getElementById('label_txt_notacre_editar');

      // Limpiar el input de archivo
      fileInput.value = '';

      // Restablecer el texto del label
      fileLabel.innerHTML = "Seleccione Nota de crédito...";
    }
  </script>
  <style>
    /* Estilo para la tabla */
    #tabla_detalle_factura {
      border: 2px solid #1FA0E0;
      border-radius: 8px;
    }

    #tabla_detalle_factura thead {
      background-color: #1FA0E0;
      color: white;
    }

    #tabla_detalle_factura th,
    #tabla_detalle_factura td {
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