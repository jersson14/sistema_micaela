<script src="../js/console_tickets.js?rev=<?php echo time(); ?>"></script>
<link rel="stylesheet" href="../plantilla/plugins/icheck-bootstrap/icheck-bootstrap.min.css">

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>MANTENIMIENTO DE NOTAS DE SALIDA</b></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">NOTAS DE SALIDA</li>
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
                        <h3 class="card-title"><i class="fas fa-user"></i>&nbsp;&nbsp;<b>Listado de Notas de salida</b></h3>
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
                                        <option value="VALIDO">VALIDO</option>
                                        <option value="ANULADO">ANULADO</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-2" role="document">
                                    <label for="">&nbsp;</label><br>
                                    <button onclick="listar_nota_ruta_estado()" class="btn btn-danger mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Buscar registros</button>
                                </div>
                                <div class="col-12 col-md-2" role="document">
                                    <label for="">&nbsp;</label><br>
                                    <button onclick="listar_nota_salida()" class="btn btn-success mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Listar todos</button>
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
                                    <button onclick="listar_reservas_fecha_usu()" class="btn btn-danger mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Buscar registros</button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center mt-4">
                        <div class="col-lg-10 text-center">
                            <div class="alert border rounded p-3 shadow-sm" style="background-color: #f8f9fa;">
                                <h5 class="mb-3" style="color:#0154A0; font-weight: bold;">
                                    <i class="fas fa-list-alt me-2"></i>Leyenda de Estados
                                </h5>
                                <div class="d-flex flex-column gap-2 align-items-start">
                                    

                                    <!-- VALIDO -->
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-success p-2 px-3">
                                            <i class="fas fa-check-circle me-1"></i> VALIDO
                                        </span>
                                        <span class="text-start">
                                            <b>: Nota de salida válida y registrada</b>
                                        </span>
                                    </div>
                                
                                    <!-- ANULADO -->
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-danger p-2 px-3">
                                            <i class="fas fa-times-circle me-1"></i> ANULADO
                                        </span>
                                        <span class="text-start">
                                            <b>: Nota de salida anulada por el usuario.</b>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" style="text-align:center">
                        <div class="card-body">
                            <table id="tabla_nota_credito" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#023D77;color:#FFFFFF; ">
                                    <tr>
                                        <th style="text-align:center">#</th>
                                        <th style="text-align:center">Nro. Ticket</th>
                                        <th style="text-align:center">Fecha de emisión</th>
                                        <th style="text-align:center">Cliente</th>
                                        <th style="text-align:center">Servicio</th>
                                        <th style="text-align:center">Origen</th>
                                        <th style="text-align:center">Destino</th>
                                        <th style="text-align:center">Total</th>
                                        <th style="text-align:center">Estado</th>
                                        <th style="text-align:center">Usuario que registro</th>
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
</div>
<div class="modal fade" id="modal_registro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#1FA0E0;">
                <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>REGISTRO DE NOTA DE SALIDA</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 form-group" style="color:red">
                        <h6><b>Campos Obligatorios (*)</b></h6>
                    </div>
                    <div class="col-12">
                        <li class="header text-center" style="color:#FFFFFF;background-color:Black;"><b>DATOS DEL PASAJERO</b></li>
                    </div>
                    <div class="col-6 form-group"><br>
                        <label for="">Tipo de documento - Emisor<b style="color:red">(*)</b>:</label>
                        <select class="form-control" id="select_tipo_documento_emisor" style="width:100%">
                            <option value="" disabled>Seleccione</option>
                            <option value="DNI">DNI</option>
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
                                <button onclick="" class="btn btn-primary" id="prueba_emisor"><i class="fa fa-search"></i><b> RENIEC</b></button>
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
                    <div class="col-8 form-group">
                        <label for="">Nombres y apellidos - Emisor<b style="color:red">(*)</b>:</label>
                        <input type="text" class="form-control" id="txt_nomb_emisor" placeholder="Ingrese los nombres y apellidos" onkeypress="return sololetras(event)">
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Celular - Emisor(Opcional):</label>
                        <input type="text" class="form-control" id="txt_celu1_emisor" placeholder="Ingrese el celular" onkeypress="return soloNumeros(event)" maxlenght="9">
                    </div>
                    <div class="col-12"><br>
                        <li class="header text-center" style="color:#FFFFFF;background-color:Black;"><b>DATOS DEL VIAJE</b></li><br>
                    </div><br>
                    <div class="col-6 form-group">
                        <label for="">Fecha de emisión<b style="color:red">(*)</b>:</label>
                        <input type="date" class="form-control" id="txt_fecha_emitida" readonly>
                    </div>
                    <div class="col-6 form-group">
                        <label for="">Servicio<b style="color:red">(*)</b>:</label>
                        <select class="js-example-basic-single" id="select_servicio" style="width:100%" required></select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Origen<b style="color:red">(*)</b>:</label>
                        <select class="js-example-basic-single" id="select_origen" style="width:100%"></select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Destino<b style="color:red">(*)</b>:</label>
                        <select class="js-example-basic-single" id="select_destino" style="width:100%"></select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Moneda:</label>
                        <input type="text" class="form-control" value="SOLES" id="txt_moneda" >
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="">Base Gravada (Sin IGV):</label>
                        <input type="number" class="form-control" readonly id="txt_base_gravada" step="0.01" placeholder="0.00">
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="">IGV (18%):</label>
                        <input type="number" class="form-control" readonly id="txt_igv" step="0.01" readonly placeholder="0.00" style="background-color:#e9ecef;">
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="" style="font-size:16px; font-weight:bold;">TOTAL A PAGAR:</label>
                        <input type="number" class="form-control" id="txt_total" step="0.01"  style="font-size:20px; font-weight:bold; background-color:#fff3cd;" placeholder="0.00">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
                <button type="button" class="btn btn-success" onclick="Registrar_tickets()"><i class="fas fa-save"></i> Registrar</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#1FA0E0;">
                <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>MODIFICAR NOTA DE SALIDA</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 form-group" style="color:red">
                        <h6><b>Campos Obligatorios (*)</b></h6>
                    </div>
                    <div class="col-12">
                        <li class="header text-center" style="color:#FFFFFF;background-color:Black;"><b>DATOS DEL PASAJERO</b></li>
                    </div>
                    <div class="col-6 form-group"><br>
                        <input type="text" id="txt_id_nota" hidden>
                        <label for="">Tipo de documento - Emisor<b style="color:red">(*)</b>:</label>
                        <select class="form-control" id="select_tipo_documento_emisor_editar" style="width:100%">
                            <option value="DNI">DNI</option>
                            <option value="CARNET DE EXTRANJERIA">CARNET DE EXTRANJERIA</option>
                            <option value="PASAPORTE">PASAPORTE</option>
                        </select>
                    </div>
                    <div class="col-6 form-group"><br>
                        <label for="">Nro. Documento<b style="color:red">(*)</b>:</label>
                        <input type="text" class="form-control" id="txt_nro_documento_editar" placeholder="Ingrese los nombres y apellidos" onkeypress="return sololetras(event)">
                    </div>
                    <div class="col-8 form-group">
                        <label for="">Nombres y apellidos - Emisor<b style="color:red">(*)</b>:</label>
                        <input type="text" class="form-control" id="txt_nomb_emisor_editar" placeholder="Ingrese los nombres y apellidos" onkeypress="return sololetras(event)">
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Celular - Emisor<b style="color:red">(*)</b>:</label>
                        <input type="text" class="form-control" id="txt_celu1_emisor_editar" placeholder="Ingrese el celular" onkeypress="return soloNumeros(event)" maxlenght="9">
                    </div>
                    <div class="col-12"><br>
                        <li class="header text-center" style="color:#FFFFFF;background-color:Black;"><b>DATOS DEL VIAJE</b></li><br>
                    </div><br>
                    <div class="col-6 form-group">
                        <label for="">Fecha de actualización<b style="color:red">(*)</b>:</label>
                        <input type="date" class="form-control" id="txt_fecha_emitida_editar" readonly>
                    </div>
                    <div class="col-6 form-group">
                        <label for="">Servicio<b style="color:red">(*)</b>:</label>
                        <select class="js-example-basic-single" id="select_servicio_editar" style="width:100%" required></select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Origen<b style="color:red">(*)</b>:</label>
                        <select class="js-example-basic-single" id="select_origen_editar" style="width:100%"></select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Destino<b style="color:red">(*)</b>:</label>
                        <select class="js-example-basic-single" id="select_destino_editar" style="width:100%"></select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Moneda:</label>
                        <input type="text" class="form-control" value="SOLES" id="txt_moneda_editar" >
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="">Base Gravada (Sin IGV):</label>
                        <input type="number" class="form-control" readonly id="txt_base_gravada_editar" step="0.01" placeholder="0.00">
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="">IGV (18%):</label>
                        <input type="number" class="form-control" readonly id="txt_igv_editar" step="0.01" readonly placeholder="0.00" style="background-color:#e9ecef;">
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="" style="font-size:16px; font-weight:bold;">TOTAL A PAGAR:</label>
                        <input type="number" class="form-control" id="txt_total_editar" step="0.01"  style="font-size:20px; font-weight:bold; background-color:#fff3cd;" placeholder="0.00">
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
                <button type="button" class="btn btn-success" onclick="Modificar_Nota_salida()"><i class="fas fa-edit"></i> Modificar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_mostrar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#1FA0E0;">
                <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>DATOS DE LA RESERVA</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-12">
                        <li class="header text-center" style="color:#FFFFFF;background-color:Black;"><b>DATOS DE LA NOTA DE SALIDA</b></li>
                    </div>
                    <div class="col-6 form-group"><br>
                        <label for="">Tipo de documento:</label>
                        <select class="form-control" id="select_tipo_documento_emisor_mostrar" disabled style="width:100%">
                            <option value="" disabled>Seleccione</option>
                            <option value="DNI">DNI</option>
                            <option value="CARNET DE EXTRANJERIA">CARNET DE EXTRANJERIA</option>
                            <option value="PASAPORTE">PASAPORTE</option>
                        </select>
                    </div>
                    <div class="col-6 form-group"><br>
                        <label for="">Nro. Documento:</label>
                        <input type="text" class="form-control" disabled id="txt_nrodoc_mostrar" placeholder="Ingrese los nombres y apellidos" onkeypress="return sololetras(event)">
                    </div>
                    <div class="col-8 form-group">
                        <label for="">Nombres y apellidos:</label>
                        <input type="text" class="form-control" disabled id="txt_nomb_emisor_mostrar" placeholder="Ingrese los nombres y apellidos" onkeypress="return sololetras(event)">
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Celular:</label>
                        <input type="text" class="form-control" disabled id="txt_celu1_emisor_mostrar" placeholder="Ingrese el celular" onkeypress="return soloNumeros(event)" maxlenght="9">
                    </div>
                    <div class="col-12"><br>
                        <li class="header text-center" style="color:#FFFFFF;background-color:Black;"><b>DATOS DEL VIAJE</b></li><br>
                    </div><br>
                    <div class="col-6 form-group">
                        <label for="">Fecha de emision<b style="color:red">(*)</b>:</label>
                        <input type="date" class="form-control" disabled id="txt_fecha_emitida_mostrar" readonly>
                    </div>
                    <div class="col-6 form-group">
                        <label for="">Servicio<b style="color:red">(*)</b>:</label>
                        <select class="js-example-basic-single" disabled id="select_servicio_mostrar" style="width:100%" required></select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Origen<b style="color:red">(*)</b>:</label>
                        <select class="js-example-basic-single" disabled id="select_origen_mostrar" style="width:100%"></select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Destino<b style="color:red">(*)</b>:</label>
                        <select class="js-example-basic-single" disabled id="select_destino_mostrar" style="width:100%"></select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Moneda:</label>
                        <input type="text" class="form-control" disabled value="SOLES">
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="">Base Gravada (Sin IGV):</label>
                        <input type="number" class="form-control" disabled id="txt_base_gravada_mostrar" step="0.01" placeholder="0.00">
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="">IGV (18%):</label>
                        <input type="number" class="form-control" disabled id="txt_igv_mostrar" step="0.01" readonly placeholder="0.00" style="background-color:#e9ecef;">
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="" style="font-size:16px; font-weight:bold;">TOTAL A PAGAR:</label>
                        <input type="number" class="form-control" disabled id="txt_total_mostrar" step="0.01"  style="font-size:20px; font-weight:bold; background-color:#fff3cd;" placeholder="0.00">
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_motivo_anula" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#1FA0E0;">
                <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>ANULAR NOTA DE SALIDA</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="txt_id_nota_anula">
                    <div class="col-12 form-group">
                        <label for="">Fecha de anulación:</label>
                        <input type="datetime-local" class="form-control" disabled id="txt_fecha_anula">
                    </div>
                    <div class="col-12 form-group">
                        <label for="">Cliente:</label>
                        <input type="text" class="form-control" disabled id="txt_cliente">
                    </div>
                    <div class="col-12 form-group">
                        <label for="" style="color:red; font-weight:bold;">Motivo de anulación (*):</label>
                        <textarea disabled for="" id="txt_motivo_anulacion" rows="3" class="form-control" style="resize:none;" placeholder="Ingrese el motivo de anulación (mínimo 10 caracteres)" maxlength="500"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cancelar</button>
                <button type="button" class="btn btn-success" onclick="Confirmar_Anulacion_Modal()"><i class="fas fa-check"></i> Anular</button>
            </div>
        </div>
    </div>
</div>





<!-- /.content -->
<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
        listar_nota_salida_pordia();
        Cargar_Select_Usuarios();
        Cargar_Select_Rutas();
        Cargar_Select_Servicios()
    });

    $('#modal_registro').on('shown.bs.modal', function() {
        $('#txt_dni_emisor').trigger('focus')
    })


    var input = document.getElementById('txt_dni_emisor');
    input.addEventListener('input', function() {
        if (this.value.length > 8)
            this.value = this.value.slice(0, 8);
    })

        var input = document.getElementById('txt_celu1_emisor');
    input.addEventListener('input', function() {
        if (this.value.length > 9)
            this.value = this.value.slice(0, 9);
    })

    var input = document.getElementById('txt_nro_documento_editar');
    input.addEventListener('input', function() {
        if (this.value.length > 8)
            this.value = this.value.slice(0, 8);
    })

            var input = document.getElementById('txt_celu1_emisor_editar');
    input.addEventListener('input', function() {
        if (this.value.length > 9)
            this.value = this.value.slice(0, 9);
    })
</script>
<script>
    var n = new Date();
    var y = n.getFullYear();
    var m = n.getMonth() + 1;
    var d = n.getDate();

    // Formato con dos dígitos
    if (m < 10) m = '0' + m;
    if (d < 10) d = '0' + d;

    // Asignar solo la fecha
    document.getElementById('txt_fecha_emitida').value =
        y + "-" + m + "-" + d;

    document.getElementById('txt_fecha_emitida_editar').value =
        y + "-" + m + "-" + d;


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

 


    // PARA EMISOR EDITAR
    // Mostrar la sección correcta al cargar la página
    window.addEventListener('DOMContentLoaded', function() {
        const selectTipoDocumento = document.getElementById('select_tipo_documento_emisor_editar');
        const dniSection = document.getElementById('dni_section');
        const otrosDocumentosSection = document.getElementById('otros_documentos_section_editar');

        if (selectTipoDocumento.value === 'DNI') {
            dniSection.style.display = 'block';
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
                }, // SIEMPRE 'dni' (no dni2)
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

    // 👉 Configuras para emisor y receptor
    configurarBusquedaDNI("txt_dni_emisor", "prueba_emisor", "txt_nomb_emisor");
</script>

<script>
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
    // document.getElementById('select_tipo_documento_emisor_editar').addEventListener('change', function() {
    //     const selectedValue = this.value;
    //     const dniSection = document.getElementById('dni_section_editar');
    //     const otrosDocumentosSection = document.getElementById('otros_documentos_section_editar');

    //     if (selectedValue === 'DNI') {
    //         dniSection.style.display = 'block';
    //         otrosDocumentosSection.style.display = 'none';
    //     } else if (selectedValue === 'CARNET DE EXTRANJERIA' || selectedValue === 'PASAPORTE') {
    //         dniSection.style.display = 'none';
    //         otrosDocumentosSection.style.display = 'block';
    //     } else {
    //         dniSection.style.display = 'none';
    //         otrosDocumentosSection.style.display = 'none';
    //     }
    // });

    // PARA RECEPTOR
    // Mostrar la sección correcta al cargar la página
    window.addEventListener('DOMContentLoaded', function() {
        const selectTipoDocumento = document.getElementById('select_tipo_documento_receptor_editar');
        const dniSection = document.getElementById('dni_section2_editar');
        const otrosDocumentosSection = document.getElementById('otros_documentos_section2_editar');

        if (selectTipoDocumento.value === 'DNI') {
            dniSection.style.display = 'block';
            otrosDocumentosSection.style.display = 'none';
        }
    });

</script>



