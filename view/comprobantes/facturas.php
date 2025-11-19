<script src="../js/console_comprobantes.js?rev=<?php echo time(); ?>"></script>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>FACTURACIÓN ELECTRÓNICA</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">COMPROBANTES</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header" style="background-color:#1FA0E0;">
                        <h3 class="card-title" style="color:white"><i class="fas fa-file-invoice"></i>&nbsp;&nbsp;<b>Nuevo Comprobante Electrónico</b></h3>
                        <button class="btn btn-warning float-right" onclick="cargar_contenido('contenido_principal','comprobantes/comprobantes_lista.php')">
                            <i class="fas fa-list"></i> Ver Comprobantes
                        </button>
                    </div>

                    <div class="card-body">
                        <!-- DATOS GENERALES -->
                        <div class="row" style="border: 2px solid #1FA0E0; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <div class="col-12">
                                <h5 style="background-color:#023D77; color:white; padding:10px; border-radius:5px;">
                                    <i class="fas fa-file-alt"></i> DATOS DEL COMPROBANTE
                                    
                                    <!-- Checkbox para habilitar edición -->
                                    <div class="float-right custom-control custom-switch" style="margin-top: -3px;">
                                        <input type="checkbox" class="custom-control-input" id="check_editar_serie" onchange="habilitarEdicionSerie()">
                                        <label class="custom-control-label" for="check_editar_serie" style="cursor: pointer;">
                                            <i class="fas fa-edit"></i> Editar Serie/Correlativo
                                        </label>
                                    </div>
                                </h5>
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="">Tipo de Comprobante <b style="color:red">(*)</b>:</label>
                                <select class="form-control" id="select_tipo_comprobante" onchange="cambiarTipoComprobante()">
                                    <option value="">Seleccione</option>
                                    <option value="01">01 - FACTURA</option>
                                    <option value="03">03 - BOLETA DE VENTA</option>
                                </select>
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="">
                                    Serie <b style="color:red">(*)</b>:
                                    <i class="fas fa-lock text-muted" id="icon_serie" style="font-size: 12px;"></i>
                                </label>
                                <input type="text" class="form-control" id="txt_serie" readonly>
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="">
                                    Correlativo:
                                    <i class="fas fa-lock text-muted" id="icon_correlativo" style="font-size: 12px;"></i>
                                </label>
                                <input type="text" class="form-control" id="txt_correlativo" readonly placeholder="Automático">
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="">Fecha de Emisión <b style="color:red">(*)</b>:</label>
                                <input type="date" class="form-control" id="txt_fecha_emision" readonly>
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="">Moneda:</label>
                                <select class="form-control" id="select_moneda">
                                    <option value="PEN">PEN - Soles</option>
                                    <option value="USD">USD - Dólares</option>
                                </select>
                            </div>
                        </div>


                        <!-- DATOS DEL CLIENTE -->
                        <div class="row" style="border: 2px solid #28a745; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <div class="col-12">
                                <h5 style="background-color:#28a745; color:white; padding:10px; border-radius:5px;">
                                    <i class="fas fa-user"></i> DATOS DEL CLIENTE
                                </h5>
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="">Tipo de Documento <b style="color:red">(*)</b>:</label>
                                <select class="form-control" id="select_tipo_documento_cliente" onchange="cambiarTipoDocCliente()">
                                    <option value="">Seleccione</option>
                                    <option value="1">1 - DNI</option>
                                    <option value="6">6 - RUC</option>
                                    <option value="4">4 - CARNET DE EXTRANJERÍA</option>
                                    <option value="7">7 - PASAPORTE</option>
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="">N° Documento <b style="color:red">(*)</b>:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="txt_numero_documento" maxlength="11" onkeypress="return soloNumeros(event)">
                                    <div class="input-group-append">
                                        <button onclick="buscarPorDocumento()" class="btn btn-success" id="prueba_buscar_emi"><i class="fa fa-search"></i><b> Buscar</b></button>
                                        <button class="btn btn-primary" onclick="buscarCliente()" id="btn_buscar_cliente">
                                            <i class="fas fa-search"></i><b> RENIEC / SUNAT</b>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="">Razón Social / Nombres <b style="color:red">(*)</b>:</label>
                                <input type="text" class="form-control" id="txt_razon_social" placeholder="Ingrese razón social o nombres completos">
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="">Dirección:</label>
                                <input type="text" class="form-control" id="txt_direccion" placeholder="Dirección del cliente">
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="">Teléfono:</label>
                                <input type="text" class="form-control" id="txt_telefono" placeholder="Teléfono del cliente">
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="">Departamento:</label>
                                <input type="text" class="form-control" id="txt_departamento" value="APURIMAC">
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="">Provincia:</label>
                                <input type="text" class="form-control" id="txt_provincia" value="ABANCAY">
                            </div>
                        </div>

                        <!-- DATOS DEL SERVICIO -->
                        <div class="row" style="border: 2px solid #ffc107; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <div class="col-12">
                                <h5 style="background-color:#ffc107; color:white; padding:10px; border-radius:5px;">
                                    <i class="fas fa-bus"></i> DATOS DEL SERVICIO
                                </h5>
                            </div>

                            <!-- BUSCAR SERVICIO/RUTA -->
                            <div class="col-md-3 form-group">
                                <label for="">Buscar Servicio / Ruta <b style="color:red">(*)</b>:</label>
                                <select class="js-example-basic-single" id="select_servicio" style="width:100%">
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="">Cantidad <b style="color:red">(*)</b>:</label>
                                <input type="number" class="form-control" id="txt_cantidad" value="1" min="1" onchange="calcularTotalesServicio()">
                            </div>
                              <div class="col-md-3 form-group">
                                <label for="">Tipo de Pago <b style="color:red">(*)</b>:</label>
                                <select class="js-example-basic-single" id="select_tipo_pago" style="width:100%">
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Fecha y Hora de Viaje <b style="color:red">(*)</b>:</label>
                                <input type="datetime-local" class="form-control" id="txt_fecha_viaje" >
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="">Conductor <b style="color:red">(*)</b>:</label>
                                <select class="js-example-basic-single" id="select_conductor" style="width:100%">
                                </select>
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="">Origen <b style="color:red">(*)</b>:</label>
                                <select class="js-example-basic-single" id="select_origen" style="width:100%">
                                </select>
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="">Destino <b style="color:red">(*)</b>:</label>
                                <select class="js-example-basic-single" id="select_destino" style="width:100%">
                                </select>
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="">Observaciones:</label>
                                <textarea class="form-control" id="txt_observaciones" rows="2" placeholder="Observaciones adicionales"></textarea>
                            </div>
                        </div>

                        <!-- TOTALES -->
                        <div class="row" style="border: 2px solid #dc3545; padding: 20px; border-radius: 8px;">
                            <div class="col-12">
                                <h5 style="background-color:#dc3545; color:white; padding:10px; border-radius:5px;">
                                    <i class="fas fa-calculator"></i> IMPORTES
                                </h5>
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="">Base Gravada (Sin IGV):</label>
                                <input type="number" class="form-control" readonly id="txt_base_gravada" step="0.01" placeholder="0.00">
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="">IGV (18%):</label>
                                <input type="number" class="form-control" readonly id="txt_igv" step="0.01" readonly placeholder="0.00" style="background-color:#e9ecef;">
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="" style="font-size:16px; font-weight:bold;">TOTAL A PAGAR:</label>
                                <input type="number" class="form-control" id="txt_total" step="0.01"  style="font-size:20px; font-weight:bold; background-color:#fff3cd;" placeholder="0.00">
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="">Forma de Pago <b style="color:red">(*)</b>:</label>
                                <select class="form-control" id="select_forma_pago">
                                    <option value="CONTADO">CONTADO</option>
                                    <option value="CREDITO">CRÉDITO</option>
                                </select>
                            </div>

                          
                        </div>

                        <!-- BOTONES DE ACCIÓN -->
                        <div class="row mt-4">
                            <div class="col-md-12 text-center">
                                <button class="btn btn-secondary btn-lg" onclick="limpiarFormulario()">
                                    <i class="fas fa-eraser"></i> Limpiar
                                </button>
                                <button class="btn btn-success btn-lg" onclick="guardarComprobante('PENDIENTE')">
                                    <i class="fas fa-save"></i> Guardar como PENDIENTE
                                </button>
                                <button class="btn btn-primary btn-lg" onclick="guardarYEnviar()">
                                    <i class="fas fa-paper-plane"></i> Guardar y Enviar a SUNAT
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
        Cargar_Select_Rutas();
        Cargar_Select_Conductores();
        Cargar_Select_Tipopago();
        Cargar_Select_Servicios();
        
        // INICIALIZAR CON FACTURA POR DEFECTO
        document.getElementById('select_tipo_comprobante').value = '01';
        cambiarTipoComprobante(); // Esto establecerá la serie F001 y el tipo de documento RUC
    });

    var n = new Date();
    var y = n.getFullYear();
    var m = n.getMonth() + 1;
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
    document.getElementById('txt_fecha_viaje').value =
        y + "-" + m + "-" + d + "T" + h + ":" + min;

    // Establece solo la fecha (YYYY-MM-DD)
    document.getElementById('txt_fecha_emision').value = y + "-" + m + "-" + d;

    function cambiarTipoComprobante() {
        var tipo = document.getElementById('select_tipo_comprobante').value;
        var serie = document.getElementById('txt_serie');

        if (tipo == '01') {
            serie.value = 'FPP1';
            // Para factura, solo RUC
            document.getElementById('select_tipo_documento_cliente').value = '6';
        } else if (tipo == '03') {
            serie.value = 'B001';
        }

        obtenerCorrelativo();
    }

    function cambiarTipoDocCliente() {
        var tipoComprobante = document.getElementById('select_tipo_comprobante').value;
        var tipoDoc = document.getElementById('select_tipo_documento_cliente').value;

        // Validar: Factura solo para RUC
        if (tipoComprobante == '01' && tipoDoc != '6') {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Las facturas solo se emiten con RUC (tipo 6)',
            });
            document.getElementById('select_tipo_documento_cliente').value = '6';
        }
    }

    function limpiarFormulario() {
        document.getElementById('select_tipo_comprobante').value = '';
        document.getElementById('txt_serie').value = '';
        document.getElementById('txt_correlativo').value = '';
        document.getElementById('txt_cantidad').value = '';
        document.getElementById('select_tipo_documento_cliente').value = '';
        document.getElementById('txt_numero_documento').value = '';
        document.getElementById('txt_razon_social').value = '';
        document.getElementById('select_conductor').value = '';
        document.getElementById('txt_base_gravada').value = '';
        document.getElementById('txt_igv').value = '';
        document.getElementById('txt_total').value = '';
        document.getElementById('select_tipo_pago').value = '';
        Cargar_Select_Conductores();
        Cargar_Select_Tipopago();
        Cargar_Select_Servicios();
        Cargar_Select_Rutas();
    }

    // Cuando cambie la cantidad, recalcular desde el total
    $("#txt_cantidad").on("input", function() {
        calcularDesdeTotal();
    });

    // O si quieres que funcione en ambas direcciones:
    $("#txt_total").on("input", function() {
        calcularDesdeTotal();
    });
</script>
<script>
function habilitarEdicionSerie() {
    var checkbox = document.getElementById('check_editar_serie');
    var serie = document.getElementById('txt_serie');
    var correlativo = document.getElementById('txt_correlativo');
    var iconSerie = document.getElementById('icon_serie');
    var iconCorrelativo = document.getElementById('icon_correlativo');
    
    if (checkbox.checked) {
        // Habilitar edición
        serie.removeAttribute('readonly');
        correlativo.removeAttribute('readonly');
        serie.style.backgroundColor = '#fff3cd'; // Color amarillo suave
        correlativo.style.backgroundColor = '#fff3cd';
        serie.style.borderColor = '#ffc107';
        correlativo.style.borderColor = '#ffc107';
        
        // Cambiar iconos a desbloquear
        iconSerie.classList.remove('fa-lock');
        iconSerie.classList.add('fa-lock-open');
        iconSerie.classList.remove('text-muted');
        iconSerie.classList.add('text-warning');
        
        iconCorrelativo.classList.remove('fa-lock');
        iconCorrelativo.classList.add('fa-lock-open');
        iconCorrelativo.classList.remove('text-muted');
        iconCorrelativo.classList.add('text-warning');
        
        // Alerta informativa
        Swal.fire({
            icon: 'info',
            title: 'Edición Habilitada',
            text: 'Ahora puedes modificar la serie y el correlativo manualmente.',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } else {
        // Deshabilitar edición
        serie.setAttribute('readonly', 'readonly');
        correlativo.setAttribute('readonly', 'readonly');
        serie.style.backgroundColor = '';
        correlativo.style.backgroundColor = '';
        serie.style.borderColor = '';
        correlativo.style.borderColor = '';
        
        // Cambiar iconos a bloqueado
        iconSerie.classList.remove('fa-lock-open');
        iconSerie.classList.add('fa-lock');
        iconSerie.classList.remove('text-warning');
        iconSerie.classList.add('text-muted');
        
        iconCorrelativo.classList.remove('fa-lock-open');
        iconCorrelativo.classList.add('fa-lock');
        iconCorrelativo.classList.remove('text-warning');
        iconCorrelativo.classList.add('text-muted');
        
        // Restaurar valores automáticos
        cambiarTipoComprobante();
    }
}
</script>