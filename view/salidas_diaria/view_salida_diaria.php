<script src="../js/console_salidas_diarias.js?rev=<?php echo time(); ?>"></script>
<link rel="stylesheet" href="../plantilla/plugins/icheck-bootstrap/icheck-bootstrap.min.css">

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>MANTENIMIENTO DE SALIDAS DIARIAS</b></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">SALIDAS DIARIAS</li>
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
                        <h3 class="card-title"><i class="fas fa-user"></i>&nbsp;&nbsp;<b>Listado de Salidas Diarias</b></h3>
                        <button class="btn btn-success float-right" onclick="AbrirRegistro()"><i class="fas fa-plus"></i> Nuevo Registro</button>
                    </div>
                    <div class="table-responsive" style="text-align:left">
                        <div class="card-body">
                            <div class="row" style="border: 1px solid #ccc; padding: 15px; border-radius: 8px;">

                                <div class="col-2 form-group">
                                    <label for="">Origen:</label>
                                    <select class="js-example-basic-single" id="select_origen_bus" style="width:100%">
                                    </select>
                                </div>
                                <div class="col-2 form-group">
                                    <label for="">Destino:</label>
                                    <select class="js-example-basic-single" id="select_destino_bus" style="width:100%">
                                    </select>
                                </div>
                                <div class="col-4 form-group">
                                    <label for="">Estado<b style="color:red">(*)</b>:</label>

                                    <select class="form-control" id="select_estado_buscar" style="width:100%">
                                        <option value="" disabled selected>Seleccione</option>
                                        <option value="EN TRANSITO">EN TRANSITO</option>
                                        <option value="COMPLETADO">COMPLETADO</option>
                                        <option value="INCOMPLETO">INCOMPLETO</option>
                                        <option value="ELIMINADO">ELIMINADO</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-2" role="document">
                                    <label for="">&nbsp;</label><br>
                                    <button onclick="listar_salidas_diarias_ruta_estado()" class="btn btn-danger mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Buscar registros</button>
                                </div>
                                <div class="col-12 col-md-2" role="document">
                                    <label for="">&nbsp;</label><br>
                                    <button onclick="listar_salidas_diarias()" class="btn btn-success mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Listar todos</button>
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
                                    <button onclick="listar_salidas_diarias_fecha_usu()" class="btn btn-danger mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Buscar registros</button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" style="text-align:center">
                        <div class="card-body">
                            <table id="tabla_salida_diaria" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#023D77;color:#FFFFFF; ">
                                    <tr>
                                        <th style="text-align:center">Nro. Salida</th>
                                        <th style="text-align:center">Conductor</th>
                                        <th style="text-align:center">Monto</th>
                                        <th style="text-align:center">Fecha y hora</th>
                                        <th style="text-align:center">Origen</th>
                                        <th style="text-align:center">Destino</th>
                                        <th style="text-align:center">Total Pasajeros</th>
                                        <th style="text-align:center">Total Encomiendas</th>
                                        <th style="text-align:center">Estado</th>
                                        <th style="text-align:center">Usuario</th>
                                        <th style="text-align:center">Acción</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
    <div class="modal fade" id="modal_registro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header" style="background-color:#1FA0E0;">
                    <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center">
                        <b>REGISTRO DE SALIDA DIARIA</b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 form-group" style="color:red">
                            <h6><b>Campos Obligatorios (*)</b></h6>
                        </div>

                        <!-- DATOS DE SALIDA -->
                        <div class="col-12 form-group">
                            <label>Conductor<b style="color:red">(*)</b>:</label>
                            <select class="js-example-basic-single" id="select_conductor" style="width:100%"></select>
                        </div>
                        <div class="col-6 form-group">
                            <label>Pago de Salida<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" value="3.00" id="txt_pago" onkeypress="return soloNumeros(event)">
                        </div>
                        <div class="col-6 form-group">
                            <label>Fecha y hora<b style="color:red">(*)</b>:</label>
                            <input type="datetime-local" class="form-control" id="txt_fecha_creacion" readonly>
                        </div>
                        <div class="col-6 form-group">
                            <label>Origen<b style="color:red">(*)</b>:</label>
                            <select class="js-example-basic-single" id="select_origen" style="width:100%"></select>
                        </div>
                        <div class="col-6 form-group">
                            <label>Destino<b style="color:red">(*)</b>:</label>
                            <select class="js-example-basic-single" id="select_destino" style="width:100%"></select>
                        </div>

                        <!-- SECCION PASAJEROS -->
                        <div class="col-12 mt-3">
                            <li class="header text-center" style="color:#FFFFFF;background-color:Black;">
                                <b>PASAJEROS</b>
                            </li>
                        </div>
                        <div class="col-6 form-group"><br>
                            <label for="">Tipo de documento - Emisor<b style="color:red">(*)</b>:</label>
                            <select class="form-control" id="select_tipo_documento_emisor" style="width:100%">
                                <option value="" disabled>Seleccione</option>
                                <option value="DNI" selected>DNI</option>
                                <option value="CARNET DE EXTRANJERIA">CARNET DE EXTRANJERIA</option>
                                <option value="PASAPORTE">PASAPORTE</option>
                            </select>
                        </div>
                        <div id="dni_section" class="col-6 form-group"><br>
                            <label for="">N° Documento Emisor<b style="color:red">(*)</b>:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="txt_dni_emisor" maxlength="8" onkeypress="return soloNumeros(event)">
                                <div class="input-group-append">
                                    <button onclick="buscarPorDocumento()" class="btn btn-success" id="prueba_buscar_emi"><i class="fa fa-search"></i><b> Buscar</b></button>
                                    <button onclick="" class="btn btn-primary" id="btn_buscar_reniec"><i class="fa fa-search"></i><b> RENIEC</b></button>
                                </div>
                            </div>
                        </div>
                        <div id="otros_documentos_section" class="col-6 form-group" style="display: none;"><br>
                            <label for="">N° Documento Emisor<b style="color:red">(*)</b>:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="txt_dni_emisor2">
                                <div class="input-group-append">
                                    <button onclick="buscarPorDocumento()" class="btn btn-success" id="prueba_buscar_emi"><i class="fa fa-search"></i><b> Buscar</b></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 form-group">
                            <label>Nombres y Apellidos<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_nombre_pasajero" onkeypress="return sololetras(event)">
                        </div>
                        <div class="col-3 form-group">
                            <label>Edad(Opcional):</label>
                            <input type="number" class="form-control" id="txt_edad">
                        </div>
                        <div class="col-3 form-group">
                            <label>Celular(Opcional):</label>
                            <input type="text" class="form-control" id="txt_cel_pasajero" maxlength="9" onkeypress="return soloNumeros(event)">
                        </div>
                        <div class="col-12 text-right">
                            <button type="button" class="btn btn-primary" onclick="agregarPasajero()">
                                <i class="fa fa-user-plus"></i> Agregar pasajero
                            </button>
                        </div>

                        <!-- TABLA PASAJEROS -->
                        <div class="col-12 mt-3">
                            <table class="table table-bordered table-sm text-center" id="tabla_pasajeros">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Tipo Documento</th>
                                        <th>Documento</th>
                                        <th>Nombres y Apellidos</th>
                                        <th>Edad</th>
                                        <th>Celular</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total pasajeros: <span id="total_pasajeros">0</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- SECCION ENCOMIENDAS -->
                        <div class="col-12 mt-4">
                            <li class="header text-center" style="color:#FFFFFF;background-color:Black;">
                                <b>ENCOMIENDAS</b>
                            </li>
                        </div>
                        <!-- SOLO TABLA DE ENCOMIENDAS -->
                        <div class="col-12 mt-3">
                            <table class="table table-bordered table-sm text-center" id="tabla_encomiendas">
                                <thead class="thead-dark">
                                    <tr>
                                        <th><input type="checkbox" id="check_all_encomiendas"></th>
                                        <th>#</th>
                                        <th>Emisor</th>
                                        <th>Receptor</th>
                                        <th>Pago</th>
                                        <th>Por pagar</th>
                                        <th>A domicilio</th>
                                        <th>Estado pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Aquí se llenarán dinámicamente las encomiendas -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="8" class="text-right">Total encomiendas: <span id="total_encomiendas">0</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- OBSERVACIONES -->
                        <div class="col-12 form-group mt-3">
                            <label>Descripción u Observación de la salida(Opcional):</label>
                            <textarea class="form-control" id="txt_descripcion" rows="3" style="resize:none" placeholder="Ingrese la descripción de la salida"></textarea>
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="Registrar_Salida_Diaria()"><i class="fas fa-save"></i> Registrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Editar datos de la salida diaria -->
    <div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header" style="background-color:#1FA0E0;">
                    <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center">
                        <b>MODIFICAR DE SALIDA DIARIA</b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 form-group" style="color:red">
                            <h6><b>Campos Obligatorios (*)</b></h6>
                        </div>

                        <!-- DATOS DE SALIDA -->
                        <div class="col-12 form-group">
                            <label>Conductor<b style="color:red"> (No se puede editar)</b>:</label>
                            <input type="text" id="id_salida_editar" hidden>
                            <select class="js-example-basic-single" id="select_conductor_editar" disabled style="width:100%"></select>
                        </div>
                        <div class="col-6 form-group">
                            <label>Pago de Salida<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_pago_editar" onkeypress="return soloNumeros(event)">
                        </div>
                        <div class="col-6 form-group">
                            <label>Fecha y hora de actualización<b style="color:red">(*)</b>:</label>
                            <input type="datetime-local" class="form-control" id="txt_fecha_actualizacion" readonly>
                        </div>
                        <div class="col-6 form-group">
                            <label>Origen<b style="color:red"> (No se puede editar)</b>:</label>
                            <select class="js-example-basic-single" id="select_origen_editar" style="width:100%" disabled></select>
                        </div>
                        <div class="col-6 form-group">
                            <label>Destino<b style="color:red"> (No se puede editar)</b>:</label>
                            <select class="js-example-basic-single" id="select_destino_editar" style="width:100%" disabled></select>
                        </div>

                        <!-- SECCION PASAJEROS -->
                        <div class="col-12 mt-3">
                            <li class="header text-center" style="color:#FFFFFF;background-color:Black;">
                                <b>PASAJEROS</b>
                            </li>
                        </div>
                        <div class="col-6 form-group"><br>
                            <label for="">Tipo de documento - Emisor<b style="color:red">(*)</b>:</label>
                            <select class="form-control" id="select_tipo_documento_emisor_editar" style="width:100%">
                                <option value="" disabled>Seleccione</option>
                                <option value="DNI" selected>DNI</option>
                                <option value="CARNET DE EXTRANJERIA">CARNET DE EXTRANJERIA</option>
                                <option value="PASAPORTE">PASAPORTE</option>
                            </select>
                        </div>
                        <div id="dni_section_editar" class="col-6 form-group"><br>
                            <label for="">N° Documento Emisor<b style="color:red">(*)</b>:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="txt_dni_emisor_editar" maxlength="8" onkeypress="return soloNumeros(event)">
                                <div class="input-group-append">
                                    <button onclick="buscarPorDocumentoEditar()" class="btn btn-success" id="prueba_buscar_emi_editar"><i class="fa fa-search"></i><b> Buscar</b></button>
                                    <button onclick="" class="btn btn-primary" id="btn_buscar_reniec_editar"><i class="fa fa-search"></i><b> RENIEC</b></button>
                                </div>
                            </div>
                        </div>
                        <div id="otros_documentos_section_editar" class="col-6 form-group" style="display: none;"><br>
                            <label for="">N° Documento Emisor<b style="color:red">(*)</b>:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="txt_dni_emisor2_editar">
                                <div class="input-group-append">
                                    <button onclick="buscarPorDocumentoEditar()" class="btn btn-success" id="prueba_buscar_emi_editar"><i class="fa fa-search"></i><b> Buscar</b></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 form-group">
                            <label>Nombres y Apellidos<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_nombre_pasajero_editar" onkeypress="return sololetras(event)">
                        </div>
                        <div class="col-3 form-group">
                            <label>Edad(Opcional):</label>
                            <input type="number" class="form-control" id="txt_edad_editar">
                        </div>
                        <div class="col-3 form-group">
                            <label>Celular(Opcional):</label>
                            <input type="text" class="form-control" id="txt_cel_pasajero_editar" maxlength="9" onkeypress="return soloNumeros(event)">
                        </div>
                        <div class="col-12 text-right">
                            <button type="button" class="btn btn-primary" onclick="agregarPasajeroEditar()">
                                <i class="fa fa-user-plus"></i> Agregar pasajero
                            </button>
                        </div>

                        <!-- TABLA PASAJEROS -->
                        <div class="col-12 mt-3">
                            <table class="table table-bordered table-sm text-center" id="tabla_pasajeros_editar">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Tipo Documento</th>
                                        <th>Documento</th>
                                        <th>Nombres y Apellidos</th>
                                        <th>Edad</th>
                                        <th>Celular</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6" class="text-right">Total pasajeros: <span id="total_pasajeros_editar">0</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- SECCION ENCOMIENDAS -->
                        <div class="col-12 mt-4">
                            <li class="header text-center" style="color:#FFFFFF;background-color:Black;">
                                <b>ENCOMIENDAS</b>
                            </li>
                        </div>
                        <!-- SOLO TABLA DE ENCOMIENDAS -->
                        <div class="col-12 mt-3">
                            <table class="table table-bordered table-sm text-center" id="tabla_encomiendas_editar">
                                <thead class="thead-dark">
                                    <tr>
                                        <th><input type="checkbox" id="check_all_encomiendas_editar"></th>
                                        <th>#</th>
                                        <th>Emisor</th>
                                        <th>Receptor</th>
                                        <th>Pago</th>
                                        <th>Por pagar</th>
                                        <th>A domicilio</th>
                                        <th>Estado pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Aquí se llenarán dinámicamente las encomiendas -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="8" class="text-right">Total encomiendas: <span id="total_encomiendas_editar">0</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- OBSERVACIONES -->
                        <div class="col-12 form-group mt-3">
                            <label>Descripción u Observación de la salida(Opcional):</label>
                            <textarea class="form-control" id="txt_descripcion_editar" rows="3" style="resize:none" placeholder="Ingrese la descripción de la salida"></textarea>
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="Moidificar_Salida_Diaria()"><i class="fas fa-save"></i> Modificar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal_mostrar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header" style="background-color:#1FA0E0;">
                    <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center">
                        <b>REGISTRO DE SALIDA DIARIA</b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 form-group" style="color:red">
                            <h6><b>Campos Obligatorios (*)</b></h6>
                        </div>

                        <!-- DATOS DE SALIDA -->
                        <div class="col-12 form-group">
                            <label>Conductor<b style="color:red">(*)</b>:</label>
                            <input type="text" id="id_salida" hidden>
                            <input type="text" class="form-control" id="select_conductor_mostrar" readonly>
                        </div>
                        <div class="col-6 form-group">
                            <label>Pago de Salida<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_pago_mostrar" readonly>
                        </div>
                        <div class="col-6 form-group">
                            <label>Fecha y hora<b style="color:red">(*)</b>:</label>
                            <input type="datetime-local" class="form-control" id="txt_fecha_creacion_mostrar" readonly>
                        </div>
                        <div class="col-6 form-group">
                            <label>Origen<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="select_origen_mostrar" readonly>
                        </div>
                        <div class="col-6 form-group">
                            <label>Destino<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="select_destino_mostrar" readonly>
                        </div>
                        <!-- SECCION PASAJEROS -->
                        <div class="col-12 mt-3">
                            <li class="header text-center" style="color:#FFFFFF;background-color:Black;">
                                <b>PASAJEROS</b>
                            </li>
                        </div>

                        <!-- TABLA PASAJEROS -->
                        <div class="col-12 mt-3">
                            <table class="table table-bordered table-sm text-center" id="tabla_pasajeros_mostrar">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Tipo Documento</th>
                                        <th>Documento</th>
                                        <th>Nombres y Apellidos</th>
                                        <th>Edad</th>
                                        <th>Celular</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6" class="text-right" style="background-color: #f8f9fa;">
                                            Total pasajeros: <span id="total_pasajeros_mostrar">0</span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- SECCION ENCOMIENDAS -->
                        <div class="col-12 mt-4">
                            <li class="header text-center" style="color:#FFFFFF;background-color:Black;">
                                <b>ENCOMIENDAS</b>
                            </li>
                        </div>
                        <!-- SOLO TABLA DE ENCOMIENDAS -->
                        <div class="col-12 mt-3">
                            <table class="table table-bordered table-sm text-center" id="tabla_encomiendas_mostrar">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Emisor</th>
                                        <th>Receptor</th>
                                        <th>Pago</th>
                                        <th>Por pagar</th>
                                        <th>A domicilio</th>
                                        <th>Estado pago</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7" class="text-right" style="background-color: #f8f9fa;">
                                            Total encomiendas: <span id="total_encomiendas_mostrar">0</span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- OBSERVACIONES -->
                        <div class="col-12 form-group mt-3">
                            <label>Descripción u Observación de la salida(Opcional):</label>
                            <textarea class="form-control" readonly id="txt_descripcion_mostrar" rows="3" style="resize:none" placeholder="Ingrese la descripción de la salida"></textarea>
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal_ver_historial" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div style="display: flex; flex-direction: column;">
                        <h5 class="modal-title" id="lb_titulo_historial"></h5>
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
                                                <th colspan="5" style="text-align:center; font-size: 18px; font-weight: bold;">HISTORIAL DE ESTADOS DE SALIDAS DIARIAS</th>
                                            </tr>
                                            <tr style="text-align:center;">
                                                <th style="text-align:center;">Nro.</th>
                                                <th style="text-align:center;">Usuario que modifico</th>
                                                <th style="text-align:center;">Estado</th>
                                                <th style="text-align:center;">Observación</th>
                                                <th style="text-align:center;">Fecha de creación</th>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#1FA0E0;">
                    <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>PAGO DE ENCOMIENDA</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 form-group" style="color:red">
                            <h6><b>Campos Obligatorios (*)</b></h6>
                        </div><br>

                        <div class="col-8 form-group">
                            <label for="">Emisor<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_emisor_pago" disabled>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Origen<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_origen_pago" disabled>
                        </div>
                        <div class="col-8 form-group">
                            <label for="">Receptor<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_receptor_pago" disabled>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Destino<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_destino_pago" disabled>
                        </div>
                        <div class="col-6 form-group">
                            <label for="">Estado<b style="color:red">(*)</b>:</label>
                            <input type="text" id="id_encomienda_pago" hidden>
                            <select class="form-control" id="select_estado_pago" style="width:100%">
                                <option value="">Seleccione</option>
                                <option value="EN TRANSITO">EN TRANSITO</option>
                                <option value="EN AGENCIA">EN AGENCIA</option>
                                <option value="ENTREGADO">ENTREGADO</option>
                                <option value="OBSERVADO">OBSERVADO</option>
                                <option value="ANULADO">ANULADO</option>
                            </select>
                        </div>

                        <div class="col-6 form-group">
                            <label for="">Saldo pendiente<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_saldo_pendiente" disabled>
                        </div>

                        <div class="col-6 form-group">
                            <label for="">Monto Recibido<b style="color:red">(*)</b>:</label>
                            <input type="number" step="0.01" class="form-control" id="txt_monto_recibido" oninput="calcularVuelto()" placeholder="Ingrese monto recibido">
                        </div>

                        <div class="col-6 form-group">
                            <label for="">Vuelto:</label>
                            <input type="text" class="form-control" id="txt_vuelto" disabled style="background-color: #f8f9fa; font-weight: bold; color: #28a745;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="Realizar_pago()"><i class="fas fa-edit"></i> Modificar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- /.content -->
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
            Cargar_Select_Usuarios();
            listar_salidas_diarias();
            Cargar_Select_Conductores();
            Cargar_Select_Rutas();
        });

        $('#modal_registro').on('shown.bs.modal', function() {
            $('#txt_dni_emisor').trigger('focus')
        })

        var input = document.getElementById('txt_dni_emisor');
        input.addEventListener('input', function() {
            if (this.value.length > 8)
                this.value = this.value.slice(0, 8);
        })
    </script>
    <script>
        var n = new Date();
        var y = n.getFullYear();
        var m = n.getMonth() + 1; // Los meses empiezan desde 0
        var d = n.getDate();
        var h = n.getHours();
        var min = n.getMinutes();
        var s = n.getSeconds();

        // Formato con dos dígitos
        if (d < 10) d = '0' + d;
        if (m < 10) m = '0' + m;
        if (h < 10) h = '0' + h;
        if (min < 10) min = '0' + min;
        if (s < 10) s = '0' + s;

        // Establece el valor con fecha y hora (YYYY-MM-DD HH:MM:SS)
        document.getElementById('txt_fecha_creacion').value =
            y + "-" + m + "-" + d + "T" + h + ":" + min;
        document.getElementById('txt_fecha_actualizacion').value =
            y + "-" + m + "-" + d + "T" + h + ":" + min;
        // PARA EMISOR
        // Mostrar la sección correcta al cargar la página
        window.addEventListener('DOMContentLoaded', function() {
            const selectTipoDocumento = document.getElementById('select_tipo_documento_emisor');
            const dniSection = document.getElementById('dni_section');
            const otrosDocumentosSection = document.getElementById('otros_documentos_section');

            if (selectTipoDocumento.value === 'DNI') {
                dniSection.style.display = 'block';
                otrosDocumentosSection.style.display = 'none';
            }
        });

        // Cambiar la visibilidad según la selección del usuario
        document.getElementById('select_tipo_documento_emisor').addEventListener('change', function() {
            const selectedValue = this.value;
            const dniSection = document.getElementById('dni_section');
            const otrosDocumentosSection = document.getElementById('otros_documentos_section');

            if (selectedValue === 'DNI') {
                dniSection.style.display = 'block';
                otrosDocumentosSection.style.display = 'none';
            } else if (selectedValue === 'CARNET DE EXTRANJERIA' || selectedValue === 'PASAPORTE') {
                dniSection.style.display = 'none';
                otrosDocumentosSection.style.display = 'block';
            } else {
                dniSection.style.display = 'none';
                otrosDocumentosSection.style.display = 'none';
            }
        });


        // PARA EDITAR EMISOR
        // Mostrar la sección correcta al cargar la página
        window.addEventListener('DOMContentLoaded', function() {
            const selectTipoDocumento = document.getElementById('select_tipo_documento_emisor_editar');
            const dniSection = document.getElementById('dni_section_editar');
            const otrosDocumentosSection = document.getElementById('otros_documentos_section_editar');

            if (selectTipoDocumento.value === 'DNI') {
                dniSection.style.display = 'block';
                otrosDocumentosSection.style.display = 'none';
            }
        });

        // Cambiar la visibilidad según la selección del usuario
        document.getElementById('select_tipo_documento_emisor_editar').addEventListener('change', function() {
            const selectedValue = this.value;
            const dniSection = document.getElementById('dni_section_editar');
            const otrosDocumentosSection = document.getElementById('otros_documentos_section_editar');

            if (selectedValue === 'DNI') {
                dniSection.style.display = 'block';
                otrosDocumentosSection.style.display = 'none';
            } else if (selectedValue === 'CARNET DE EXTRANJERIA' || selectedValue === 'PASAPORTE') {
                dniSection.style.display = 'none';
                otrosDocumentosSection.style.display = 'block';
            } else {
                dniSection.style.display = 'none';
                otrosDocumentosSection.style.display = 'none';
            }
        });


        function configurarBusquedaDNI(inputId, botonId, nombreId) {
            // Detectar Enter en el input
            document.getElementById(inputId).addEventListener("keyup", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    document.getElementById(botonId).click();
                }
            });

            // Click en el botón
            $("#" + botonId).click(function() {
                var dni = $("#" + inputId).val();

                $.ajax({
                    type: "POST",
                    url: "consulta-dni-ajax.php",
                    data: {
                        dni: dni
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data == 1) {
                            Swal.fire("Mensaje de Confirmación", "El DNI tiene que tener 8 dígitos", "warning");
                        } else if (data.error) {
                            Swal.fire("Error", "Error en la consulta: " + data.error, "error");
                        } else if (!data.first_name) {
                            Swal.fire("Mensaje de Confirmación", "El DNI ingresado no existe", "warning");
                        } else {
                            $("#" + nombreId).val(
                                data.first_name + ' ' + data.first_last_name + ' ' + data.second_last_name
                            );
                        }
                    }
                });
            });
        }

        // Configurar para el emisor (buscar en RENIEC)
        configurarBusquedaDNI("txt_dni_emisor", "btn_buscar_reniec", "txt_nombre_pasajero");
        configurarBusquedaDNI("txt_dni_emisor_editar", "btn_buscar_reniec_editar", "txt_nombre_pasajero_editar");
    </script>



    <script>
        $(document).ready(function() {
            // Inicialmente ocultar ambos campos hasta que se seleccione un estado
            $('#div_observacion').hide();
            $('#div_anulacion').hide();

            // Evento change para el select de estado
            $('#select_estado_editar2').change(function() {
                var estado = $(this).val();

                // Ocultar ambos campos y limpiar valores
                $('#div_observacion').hide();
                $('#div_anulacion').hide();
                $('#text_observacion_enco').val('');
                $('#txt_anula_enco').val('');

                // Si es ANULADO: solo mostrar campo de anulación
                if (estado == 'ANULADO') {
                    $('#div_anulacion').show();
                }
                // Para todos los demás estados: solo mostrar campo de observación
                else if (estado != '') {
                    $('#div_observacion').show();
                }
            });

            // Opcional: También puedes agregar validación al enviar el formulario
            window.Modificar_Rol = function() {
                var estado = $('#select_estado_editar2').val();

                // Validaciones básicas
                if (!estado) {
                    alert('Debe seleccionar un estado');
                    return false;
                }

                // Validaciones según el estado seleccionado
                if (estado == 'ANULADO' && !$('#txt_anula_enco').val().trim()) {
                    alert('Debe ingresar el motivo de anulación');
                    return false;
                }

                if (estado != 'ANULADO' && estado != '' && !$('#text_observacion_enco').val().trim()) {
                    alert('Debe ingresar una observación');
                    return false;
                }

                // Aquí iría tu lógica para modificar el rol
                console.log('Modificando estado a:', estado);

                // Ejemplo de envío (ajusta según tu implementación)
                // Tu código de envío aquí...
            };
        });
    </script>
    <script>
        function calcularVuelto() {
            // Obtener los valores de los campos
            var saldoPendiente = parseFloat(document.getElementById('txt_saldo_pendiente').value.replace('S/', '').replace(',', '').trim()) || 0;
            var montoRecibido = parseFloat(document.getElementById('txt_monto_recibido').value) || 0;
            var campoVuelto = document.getElementById('txt_vuelto');

            // Calcular el vuelto
            var vuelto = montoRecibido - saldoPendiente;

            // Mostrar el resultado
            if (montoRecibido > 0 && saldoPendiente > 0) {
                if (vuelto >= 0) {
                    campoVuelto.value = 'S/ ' + vuelto.toFixed(2);
                    campoVuelto.style.color = '#28a745'; // Verde para vuelto positivo
                } else {
                    campoVuelto.value = 'S/ ' + Math.abs(vuelto).toFixed(2) + ' (Falta)';
                    campoVuelto.style.color = '#dc3545'; // Rojo para faltante
                }
            } else {
                campoVuelto.value = '';
            }
        }

        // Función para limpiar el modal cuando se cierre
        $('#modal_pagar').on('hidden.bs.modal', function() {
            document.getElementById('txt_monto_recibido').value = '';
            document.getElementById('txt_vuelto').value = '';
        });
    </script>
    <styLe>
        table.dataTable .dropdown-menu {
            position: absolute !important;
            z-index: 1050 !important;
            /* más alto que la tabla */
        }
    </styLe>