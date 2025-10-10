<script src="../js/console_reservas_asis.js?rev=<?php echo time(); ?>"></script>
<link rel="stylesheet" href="../plantilla/plugins/icheck-bootstrap/icheck-bootstrap.min.css">

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>MANTENIMIENTO DE RESERVAS</b></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">RESERVAS</li>
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
                        <h3 class="card-title"><i class="fas fa-user"></i>&nbsp;&nbsp;<b>Listado de Reservas</b></h3>
                        <button class="btn btn-success float-right" onclick="AbrirRegistro()"><i class="fas fa-plus"></i> Nuevo Registro</button>
                    </div>
                    <div class="table-responsive" style="text-align:left">
                        <div class="card-body">
                            <div class="row" style="border: 1px solid #ccc; padding: 15px; border-radius: 8px;">

                                <div class="col-2 form-group">
                                    <label for="">Fecha desde:</label>
                                    <input type="date" class="form-control" id="txt_fecha_desde">
                                </div>
                                <div class="col-2 form-group">
                                    <label for="">Fecha hasta:</label>
                                    <input type="date" class="form-control" id="txt_fecha_hasta">
                                </div>
                                <div class="col-4 form-group">
                                    <label for="">Estado<b style="color:red">(*)</b>:</label>
                                    <select class="form-control" id="select_estado_buscar" style="width:100%">
                                        <option value="" disabled selected>Seleccione</option>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="COMPLETADO">COMPLETADO</option>
                                        <option value="ANULADO">ANULADO</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-2" role="document">
                                    <label for="">&nbsp;</label><br>
                                    <button onclick="listar_reservas_fecha_estado()" class="btn btn-danger mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Buscar registros</button>
                                </div>
                               <div class="col-12 col-md-2" role="document">
                                    <label for="">&nbsp;</label><br>
                                    <button onclick="listar_reservas()" class="btn btn-success mr-2" style="width:100%" onclick><i class="fas fa-search mr-1"></i>Listar todo</button>
                                </div>
                            </div>
                        </div>
                    </div>
               
                    <div class="table-responsive" style="text-align:center">
                        <div class="card-body">
                            <table id="tabla_reservas" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#023D77;color:#FFFFFF; ">
                                    <tr>
                                        <th style="text-align:center">Nro. Encomienda</th>
                                        <th style="text-align:center">Pasajero</th>
                                        <th style="text-align:center">Fecha de reserva</th>
                                        <th style="text-align:center">Fecha de viaje</th>
                                        <th style="text-align:center">Origen</th>
                                        <th style="text-align:center">Destino</th>
                                        <th style="text-align:center">Celular</th>
                                        <th style="text-align:center">Monto adelantado</th>
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
                <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>REGISTRO DE RESERVAS</b></h5>
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
                        <label for="">Celular - Emisor<b style="color:red">(*)</b>:</label>
                        <input type="text" class="form-control" id="txt_celu1_emisor" placeholder="Ingrese el celular" onkeypress="return soloNumeros(event)" maxlenght="9">
                    </div>
                    <div class="col-12"><br>
                        <li class="header text-center" style="color:#FFFFFF;background-color:Black;"><b>DATOS DE LA RESERVA</b></li><br>
                    </div><br>
                    <div class="col-6 form-group">
                        <label for="">Fecha y hora de reserva<b style="color:red">(*)</b>:</label>
                        <input type="datetime-local" class="form-control" id="txt_fecha_rerserva" readonly>
                    </div>
                    <div class="col-6 form-group">
                        <label for="">Fecha y hora de viaje<b style="color:red">(*)</b>:</label>
                        <input type="datetime-local" class="form-control" id="txt_fecha_viaje">
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Origen<b style="color:red">(*)</b>:</label>
                        <select class="js-example-basic-single" disabled id="select_origen" style="width:100%"></select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Destino<b style="color:red">(*)</b>:</label>
                        <select class="js-example-basic-single" disabled id="select_destino" style="width:100%"></select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Monto adelantado(Opcional):</label>
                        <input type="text" class="form-control" value="0.00" id="txt_monto_adelantado" placeholder="Ingrese el monto adelantado" onkeypress="return soloNumeros(event)">
                    </div>
                    <div class="col-12 form-group">
                        <label for="">Observaciones(Opcional):</label>
                        <textarea style="color:red" class="form-control" id="txt_observacion" rows="2" style="resize:none" placeholder="Ingrese alguna observación o descripción de la reserva"></textarea>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
                <button type="button" class="btn btn-success" onclick="Registrar_Reservas()"><i class="fas fa-save"></i> Registrar</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#1FA0E0;">
                <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>MODIFICAR ENCOMIENDA</b></h5>
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
                        <input type="text" id="txt_idreserva" hidden>
                        <label for="">Tipo de documento - Emisor<b style="color:red">(*)</b>:</label>
                        <select class="form-control" id="select_tipo_documento_emisor_editar" style="width:100%">
                            <option value="" disabled>Seleccione</option>
                            <option value="DNI">DNI</option>
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
                                <button onclick="" class="btn btn-primary" id="prueba_emisor_editar"><i class="fa fa-search"></i><b> RENIEC</b></button>
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
                    <div class="col-8 form-group">
                        <label for="">Nombres y apellidos - Emisor<b style="color:red">(*)</b>:</label>
                        <input type="text" class="form-control" id="txt_nomb_emisor_editar" placeholder="Ingrese los nombres y apellidos" onkeypress="return sololetras(event)">
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Celular - Emisor<b style="color:red">(*)</b>:</label>
                        <input type="text" class="form-control" id="txt_celu1_emisor_editar" placeholder="Ingrese el celular" onkeypress="return soloNumeros(event)" maxlenght="9">
                    </div>
                    <div class="col-12"><br>
                        <li class="header text-center" style="color:#FFFFFF;background-color:Black;"><b>DATOS DE LA RESERVA</b></li><br>
                    </div><br>
                    <div class="col-6 form-group">
                        <label for="">Fecha y hora de actualización<b style="color:red">(*)</b>:</label>
                        <input type="datetime-local" class="form-control" id="txt_fecha_rerserva_editar" readonly>
                    </div>
                    <div class="col-6 form-group">
                        <label for="">Fecha y hora de viaje<b style="color:red">(*)</b>:</label>
                        <input type="datetime-local" class="form-control" id="txt_fecha_viaje_editar">
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Origen<b style="color:red">(*)</b>:</label>
                        <select class="js-example-basic-single" disabled id="select_origen_editar" style="width:100%"></select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Destino<b style="color:red">(*)</b>:</label>
                        <select class="js-example-basic-single" disabled id="select_destino_editar" style="width:100%"></select>
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Monto adelantado(Opcional):</label>
                        <input type="text" class="form-control" value="0.00" id="txt_monto_adelantado_editar" placeholder="Ingrese el monto adelantado" onkeypress="return soloNumeros(event)">
                    </div>
                    <div class="col-12 form-group">
                        <label for="">Observaciones(Opcional):</label>
                        <textarea style="color:red" class="form-control" id="txt_observacion_editar" rows="2" style="resize:none" placeholder="Ingrese alguna observación o descripción de la reserva"></textarea>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
                <button type="button" class="btn btn-success" onclick="Modificar_Reservas()"><i class="fas fa-edit"></i> Modificar</button>
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
                        <li class="header text-center" style="color:#FFFFFF;background-color:Black;"><b>DATOS DEL PASAJERO</b></li>
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
                        <li class="header text-center" style="color:#FFFFFF;background-color:Black;"><b>DATOS DE LA RESERVA</b></li><br>
                    </div><br>
                    <div class="col-6 form-group">
                        <label for="">Fecha y hora de reserva:</label>
                        <input type="datetime-local" disabled class="form-control" id="txt_fecha_rerserva_mostrar" readonly>
                    </div>
                    <div class="col-6 form-group">
                        <label for="">Fecha y hora de viaje:</label>
                        <input type="datetime-local" disabled class="form-control" id="txt_fecha_viaje_mostrar">
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Origen:</label>
                        <input type="text" disabled class="form-control" id="select_origen_mostrar">
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Destino:</label>
                        <input type="text" disabled class="form-control" id="select_destino_mostrar">
                    </div>
                    <div class="col-4 form-group">
                        <label for="">Monto adelantado:</label>
                        <input type="text" class="form-control" disabled value="0.00" id="txt_monto_adelantado_mostrar" placeholder="Ingrese el monto adelantado" onkeypress="return soloNumeros(event)">
                    </div>
                    <div class="col-12 form-group">
                        <label for="">Observaciones:</label>
                        <textarea style="color:red" class="form-control" disabled id="txt_observacion_mostrar" rows="2" style="resize:none" placeholder="Ingrese alguna observación o descripción de la reserva"></textarea>
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
                <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>MOTIVO DE ANULACIÓN</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 form-group">
                        <label for="">Fecha de anulación:</label>
                        <input type="datetime-local" class="form-control" disabled id="txt_fecha_anula">
                    </div>
                    <!-- Campo de Motivo de anulación -->
                    <div class="col-12 form-group" id="div_anulacion2">
                        <label for="">Motivo de anulación:</label>
                        <textarea name="" id="txt_anula_enco2" rows="3" class="form-control" disabled style="resize:none;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
                <button type="button" class="btn btn-success" onclick="Modificar_Estado()"><i class="fas fa-edit"></i> Modificar</button>
            </div>
        </div>
    </div>
</div>





<!-- /.content -->
<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
        Cargar_Select_Usuarios();
        listar_reservas_pordia();
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

        var input = document.getElementById('txt_celu1_emisor');
    input.addEventListener('input', function() {
        if (this.value.length > 9)
            this.value = this.value.slice(0, 9);
    })

    var input = document.getElementById('txt_dni_emisor_editar');
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
    document.getElementById('txt_fecha_rerserva').value =
        y + "-" + m + "-" + d + "T" + h + ":" + min;
    document.getElementById('txt_fecha_rerserva_editar').value =
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

    // Cambiar la visibilidad según la selección del usuario
    document.getElementById('select_tipo_documento_emisor_editar').addEventListener('change', function() {
        const selectedValue = this.value;
        const dniSection = document.getElementById('dni_section');
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
    configurarBusquedaDNI("txt_dni_emisor_editar", "prueba_emisor_editar", "txt_nomb_emisor_editar");
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

    // Cambiar la visibilidad según la selección del usuario
    document.getElementById('select_tipo_documento_receptor_editar').addEventListener('change', function() {
        const selectedValue = this.value;
        const dniSection = document.getElementById('dni_section2_editar');
        const otrosDocumentosSection = document.getElementById('otros_documentos_section2_editar');

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