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
                    <div class="card-header" style="background-color:#1FA0E0; padding:10px;">
                        <h3 class="card-title" style="color:white; font-size:16px;"><i class="fas fa-file-invoice"></i>&nbsp;<b>Nuevo Comprobante</b></h3>
                        <button class="btn btn-warning btn-sm float-right" onclick="cargar_contenido('contenido_principal','comprobantes/comprobantes_lista.php')">
                            <i class="fas fa-list"></i> Ver Lista
                        </button>
                        
                    </div><br>
              <div class="col-12 form-group" style="color:red">
                        <h6><b>Campos Obligatorios (*)</b></h6>
                    </div>
                    <div class="card-body" style="padding:15px;">
                        <!-- DATOS GENERALES -->
                        <div class="row" style="border: 2px solid #1FA0E0; padding: 12px; border-radius: 5px; margin-bottom: 12px;">
                            <div class="col-12" style="margin-bottom:8px;">
                                <h6 style="background-color:#023D77; color:white; padding:6px 10px; border-radius:3px; margin:0; font-size:14px;">
                                    <i class="fas fa-file-alt"></i> DATOS DEL COMPROBANTE
                                    
                                    <!-- Checkbox para habilitar edición -->
                                    <div class="float-right custom-control custom-switch" style="margin-top: -2px;">
                                        <input type="checkbox" class="custom-control-input" id="check_editar_serie" onchange="habilitarEdicionSerie()">
                                        <label class="custom-control-label" for="check_editar_serie" style="cursor: pointer; font-size:13px;">
                                            <i class="fas fa-edit"></i> Editar Serie
                                        </label>
                                    </div>
                                </h6>
                            </div>

                            <div class="col-md-3 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Tipo Comprobante <b style="color:red">(*)</b>:</label>
                                <select class="form-control form-control-sm" id="select_tipo_comprobante" onchange="cambiarTipoComprobante()">
                                    <option value="">Seleccione</option>
                                    <option value="01">01 - FACTURA</option>
                                    <option value="03">03 - BOLETA DE VENTA</option>
                                </select>
                            </div>

                            <div class="col-md-2 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">
                                    Serie <b style="color:red">(*)</b>:
                                    <i class="fas fa-lock text-muted" id="icon_serie" style="font-size: 11px;"></i>
                                </label>
                                <input type="text" class="form-control form-control-sm" id="txt_serie" readonly>
                            </div>

                            <div class="col-md-2 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">
                                    Correlativo:
                                    <i class="fas fa-lock text-muted" id="icon_correlativo" style="font-size: 11px;"></i>
                                </label>
                                <input type="text" class="form-control form-control-sm" id="txt_correlativo" readonly placeholder="Automático">
                            </div>

                            <div class="col-md-3 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Fecha Emisión <b style="color:red">(*)</b>:</label>
                                <input type="date" class="form-control form-control-sm" id="txt_fecha_emision" readonly>
                            </div>

                            <div class="col-md-2 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Moneda:</label>
                                <select class="form-control form-control-sm" id="select_moneda">
                                    <option value="PEN">PEN - Soles</option>
                                    <option value="USD">USD - Dólares</option>
                                </select>
                            </div>
                        </div>


                        <!-- DATOS DEL CLIENTE -->
                        <div class="row" style="border: 2px solid #28a745; padding: 12px; border-radius: 5px; margin-bottom: 12px;">
                            <div class="col-12" style="margin-bottom:8px;">
                                <h6 style="background-color:#28a745; color:white; padding:6px 10px; border-radius:3px; margin:0; font-size:14px;">
                                    <i class="fas fa-user"></i> DATOS DEL CLIENTE
                                </h6>
                            </div>

                            <div class="col-md-2 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Tipo Doc. <b style="color:red">(*)</b>:</label>
                                <select class="form-control form-control-sm" id="select_tipo_documento_cliente" onchange="cambiarTipoDocCliente()">
                                    <option value="">Seleccione</option>
                                    <option value="1">1 - DNI</option>
                                    <option value="6">6 - RUC</option>
                                    <option value="4">4 - CARNET EXT.</option>
                                    <option value="7">7 - PASAPORTE</option>
                                </select>
                            </div>

                            <div class="col-md-3 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">N° Documento <b style="color:red">(*)</b>:</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="txt_numero_documento" maxlength="11" onkeypress="return soloNumeros(event)">
                                    <div class="input-group-append">
                                        <button onclick="buscarPorDocumento()" class="btn btn-success btn-sm"><i class="fa fa-search"></i><b> Buscar</b></button>
                                        <button class="btn btn-primary btn-sm" onclick="buscarCliente()" id="btn_buscar_cliente">
                                            <i class="fas fa-search"></i><b> RENIEC / SUNAT</b>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Razón Social / Nombres <b style="color:red">(*)</b>:</label>
                                <input type="text" class="form-control form-control-sm" id="txt_razon_social" placeholder="Ingrese razón social o nombres completos">
                            </div>

                            <div class="col-md-3 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Dirección:</label>
                                <input type="text" class="form-control form-control-sm" id="txt_direccion" placeholder="Dirección del cliente">
                            </div>

                            <div class="col-md-2 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Teléfono:</label>
                                <input type="text" class="form-control form-control-sm" id="txt_telefono" placeholder="Teléfono del cliente">
                            </div>

                            <div class="col-md-2 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Departamento:</label>
                                <input type="text" class="form-control form-control-sm" id="txt_departamento" value="APURIMAC">
                            </div>

                            <div class="col-md-2 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Provincia:</label>
                                <input type="text" class="form-control form-control-sm" id="txt_provincia" value="ABANCAY">
                            </div>
                        </div>

                        <!-- DATOS DEL SERVICIO -->
                        <div class="row" style="border: 2px solid #ffc107; padding: 12px; border-radius: 5px; margin-bottom: 12px;">
                            <div class="col-12" style="margin-bottom:8px;">
                                <h6 style="background-color:#ffc107; color:white; padding:6px 10px; border-radius:3px; margin:0; font-size:14px;">
                                    <i class="fas fa-bus"></i> DATOS DEL SERVICIO
                                </h6>
                            </div>

                            <div class="col-md-3 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Servicio / Ruta <b style="color:red">(*)</b>:</label>
                                <select class="js-example-basic-single" id="select_servicio" style="width:100%">
                                </select>
                            </div>

                            <div class="col-md-2 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Cantidad <b style="color:red">(*)</b>:</label>
                                <input type="number" class="form-control form-control-sm" id="txt_cantidad" value="1" min="1" onchange="calcularTotalesServicio()">
                            </div>

                            <div class="col-md-2 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Tipo Pago <b style="color:red">(*)</b>:</label>
                                <select class="js-example-basic-single" id="select_tipo_pago" style="width:100%">
                                </select>
                            </div>

                            <div class="col-md-3 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Fecha/Hora Viaje <b style="color:red">(*)</b>:</label>
                                <input type="datetime-local" class="form-control form-control-sm" id="txt_fecha_viaje">
                            </div>

                            <div class="col-md-4 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Conductor <b style="color:red">(*)</b>:</label>
                                <select class="js-example-basic-single" id="select_conductor" style="width:100%">
                                </select>
                            </div>

                            <div class="col-md-4 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Origen <b style="color:red">(*)</b>:</label>
                                <select class="js-example-basic-single" id="select_origen" style="width:100%">
                                </select>
                            </div>

                            <div class="col-md-4 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Destino <b style="color:red">(*)</b>:</label>
                                <select class="js-example-basic-single" id="select_destino" style="width:100%">
                                </select>
                            </div>

                            <!-- DATOS DEL PASAJERO (COMPACTO) -->
                            <div class="col-12" style="margin-top:8px; margin-bottom:6px;">
                                <div class="alert alert-info" role="alert" style="background-color:#e7f3ff; border-left: 3px solid #2196F3; padding:8px; margin:0;">
                                    <small style="margin:0; color:#1976D2;"><i class="fas fa-user-tag"></i> <b>DATOS DEL PASAJERO (Opcional)</b> - Si el pasajero es diferente al cliente que paga, escriba su DNI: el nombre se busca solo y se agrega a Observaciones. Use "Agregar" para ingresar otro pasajero.</small>
                                </div>
                            </div>

                            <div class="col-md-2 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">DNI Pasajero:</label>
                                <input type="text" class="form-control form-control-sm" id="txt_dni_pasajero" maxlength="8" onkeypress="return soloNumeros(event)" placeholder="Ej: 12345678">
                            </div>

                            <div class="col-md-5 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Nombres y Apellidos:</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="txt_nombre_pasajero" placeholder="Se completa al escribir el DNI" readonly style="background-color:#f8f9fa;">
                                    <div class="input-group-append">
                                        <button class="btn btn-info" onclick="buscarPasajeroReniec()" id="btn_buscar_pasajero">
                                            <i class="fas fa-search"></i><b> RENIEC</b>
                                        </button>
                                        <button class="btn btn-success" onclick="agregarPasajeroAObservaciones()" id="btn_agregar_pasajero" title="Agregar a observaciones">
                                            <i class="fas fa-plus"></i><b> Agregar</b>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">&nbsp;</label>
                                <button class="btn btn-warning btn-sm btn-block" onclick="limpiarDatosPasajero()">
                                    <i class="fas fa-eraser"></i> Limpiar Pasajero
                                </button>
                            </div>

                            <div class="col-md-12 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Observaciones:</label>
                                <textarea class="form-control form-control-sm" id="txt_observaciones" rows="2" placeholder="Observaciones adicionales del viaje..."></textarea>
                                <small class="text-muted"><i class="fas fa-info-circle"></i> Los datos del pasajero se agregarán automáticamente aquí</small>
                            </div>
                        </div>

                        <!-- TOTALES (MÁS COMPACTO) -->
                        <div class="row" style="border: 2px solid #dc3545; padding: 12px; border-radius: 5px;">
                            <div class="col-12" style="margin-bottom:8px;">
                                <h6 style="background-color:#dc3545; color:white; padding:6px 10px; border-radius:3px; margin:0; font-size:14px;">
                                    <i class="fas fa-calculator"></i> IMPORTES
                                </h6>
                            </div>

                            <div class="col-md-3 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Base Gravada:</label>
                                <input type="number" class="form-control form-control-sm" readonly id="txt_base_gravada" step="0.01" placeholder="0.00">
                            </div>

                            <div class="col-md-3 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">IGV (18%):</label>
                                <input type="number" class="form-control form-control-sm" readonly id="txt_igv" step="0.01" readonly placeholder="0.00" style="background-color:#e9ecef;">
                            </div>

                            <div class="col-md-3 form-group" style="margin-bottom:8px;">
                                <label style="font-size:14px; font-weight:bold; margin-bottom:3px;">TOTAL A PAGAR:</label>
                                <input type="number" class="form-control form-control-sm" id="txt_total" step="0.01" style="font-size:16px; font-weight:bold; background-color:#fff3cd;" placeholder="0.00">
                            </div>

                            <div class="col-md-3 form-group" style="margin-bottom:8px;">
                                <label style="font-size:13px; margin-bottom:3px;">Forma de Pago <b style="color:red">(*)</b>:</label>
                                <select class="form-control form-control-sm" id="select_forma_pago">
                                    <option value="CONTADO">CONTADO</option>
                                    <option value="CREDITO">CRÉDITO</option>
                                </select>
                            </div>
                        </div>

                        <!-- BOTONES DE ACCIÓN -->
                        <div class="row" style="margin-top:12px;">
                            <div class="col-md-12 text-center">
                                <button class="btn btn-secondary btn-lg" onclick="limpiarFormulario()">
                                    <i class="fas fa-eraser"></i> Limpiar
                                </button>

                                <button id="btn_guardar_enviar_sunat" class="btn btn-primary btn-lg" onclick="guardarYEnviar()">
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
        cambiarTipoComprobante();
    });

    var n = new Date();
    var y = n.getFullYear();
    var m = n.getMonth() + 1;
    var d = n.getDate();
    var h = n.getHours();
    var min = n.getMinutes();

    if (d < 10) d = '0' + d;
    if (m < 10) m = '0' + m;
    if (h < 10) h = '0' + h;
    if (min < 10) min = '0' + min;

    document.getElementById('txt_fecha_viaje').value = y + "-" + m + "-" + d + "T" + h + ":" + min;
    document.getElementById('txt_fecha_emision').value = y + "-" + m + "-" + d;

    function cambiarTipoComprobante() {
        var tipo = document.getElementById('select_tipo_comprobante').value;
        var serie = document.getElementById('txt_serie');

        if (tipo == '01') {
            serie.value = 'FPP1';
            document.getElementById('select_tipo_documento_cliente').value = '6';
        } else if (tipo == '03') {
            serie.value = 'BPP1';
        }

        obtenerCorrelativo();
    }

    function cambiarTipoDocCliente() {
        var tipoComprobante = document.getElementById('select_tipo_comprobante').value;
        var tipoDoc = document.getElementById('select_tipo_documento_cliente').value;

        if (tipoComprobante == '01' && tipoDoc != '6') {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Las facturas solo se emiten con RUC (tipo 6)',
            });
            document.getElementById('select_tipo_documento_cliente').value = '6';
        }
    }

    $("#txt_cantidad").on("input", function() {
        calcularDesdeTotal();
    });

    $("#txt_total").on("input", function() {
        calcularDesdeTotal();
    });

    // ============================================================
    // BÚSQUEDA AUTOMÁTICA DEL CLIENTE
    // Al completar el documento (8 dígitos DNI / 11 dígitos RUC) se busca solo:
    // primero en la base de datos y, si no está, en RENIEC / SUNAT.
    // ============================================================
    var ultimoDocCliente = '';
    var temporizadorCliente = null;

    $('#txt_numero_documento').on('input', function () {
        var tipo = $('#select_tipo_documento_cliente').val();
        var numero = $(this).val().replace(/\D/g, '');
        if ($(this).val() !== numero) $(this).val(numero);

        var largo = tipo === '6' ? 11 : (tipo === '1' ? 8 : 0);
        if (!largo || numero.length !== largo) {
            ultimoDocCliente = '';
            return;
        }
        if (numero === ultimoDocCliente) return;

        clearTimeout(temporizadorCliente);
        temporizadorCliente = setTimeout(function () { autoBuscarCliente(tipo, numero); }, 200);
    });

    // Si cambia el tipo de documento, se vuelve a buscar con el número ya escrito
    $('#select_tipo_documento_cliente').on('change', function () {
        ultimoDocCliente = '';
        $('#txt_numero_documento').trigger('input');
    });

    async function autoBuscarCliente(tipo, numero) {
        ultimoDocCliente = numero;
        var $nombre = $('#txt_razon_social');
        $nombre.val('').attr('placeholder', 'Buscando...');
        try {
            var datos = await consultarDocumento(tipo, numero);
            // Si mientras tanto el usuario cambió el número, se descarta esta respuesta
            if ($('#txt_numero_documento').val() !== numero) return;

            if (datos) {
                $nombre.val(datos.nombre);
                $('#txt_direccion').val(datos.direccion);
                if (datos.telefono) $('#txt_telefono').val(datos.telefono);
                if (datos.departamento) $('#txt_departamento').val(datos.departamento);
                if (datos.provincia) $('#txt_provincia').val(datos.provincia);
                if (datos.distrito) $('#txt_distrito').val(datos.distrito);
                avisoRapido('success', datos.nombre + ' (' + datos.fuente + ')');
            } else {
                avisoRapido('warning', 'No se encontró el documento. Ingrese los datos a mano.');
            }
        } catch (e) {
            avisoRapido('error', 'No se pudo consultar el documento. Ingrese los datos a mano.');
        } finally {
            $nombre.attr('placeholder', 'Ingrese razón social o nombres completos');
        }
    }

    // ============================================================
    // PASAJERO: al escribir los 8 dígitos del DNI se busca solo y su nombre se
    // pone automáticamente en Observaciones.
    // - Si se corrige el DNI, la línea se reemplaza (no se duplica).
    // - "Agregar" confirma al pasajero y deja los campos listos para otro.
    // - "Limpiar Pasajero" quita al pasajero que aún no se confirmó.
    // ============================================================
    var lineaPasajeroAuto = null;
    var ultimoDniPasajero = '';

    $('#txt_dni_pasajero').on('input', function () {
        var dni = $(this).val().replace(/\D/g, '');
        if ($(this).val() !== dni) $(this).val(dni);
        if (dni.length !== 8) {
            ultimoDniPasajero = '';
            return;
        }
        if (dni !== ultimoDniPasajero) buscarPasajeroReniec();
    });

    async function buscarPasajeroReniec() {
        var dni = $('#txt_dni_pasajero').val().trim();
        if (dni.length !== 8) {
            return avisoRapido('warning', 'El DNI del pasajero debe tener 8 dígitos');
        }

        ultimoDniPasajero = dni;
        var $nombre = $('#txt_nombre_pasajero');
        $nombre.val('').attr('placeholder', 'Buscando...');
        try {
            var datos = await consultarDocumento('1', dni);
            if ($('#txt_dni_pasajero').val().trim() !== dni) return;

            if (datos) {
                $nombre.val(datos.nombre);
                ponerPasajeroEnObservaciones(dni, datos.nombre);
                avisoRapido('success', 'Pasajero: ' + datos.nombre);
            } else {
                avisoRapido('warning', 'DNI del pasajero no encontrado');
            }
        } catch (e) {
            avisoRapido('error', 'No se pudo consultar el DNI del pasajero');
        } finally {
            $nombre.attr('placeholder', 'Se completa al escribir el DNI');
        }
    }

    function ponerPasajeroEnObservaciones(dni, nombre) {
        var obs = document.getElementById('txt_observaciones');
        var linea = 'PASAJERO: ' + nombre + ' - DNI: ' + dni;
        var lineas = obs.value ? obs.value.split('\n') : [];

        // Reemplaza al pasajero automático anterior que aún no se confirmó (corrección de DNI)
        if (lineaPasajeroAuto) {
            lineas = lineas.filter(function (l) { return l !== lineaPasajeroAuto; });
        }
        if (lineas.indexOf(linea) === -1) {
            lineas.unshift(linea);
        }
        obs.value = lineas.join('\n');
        lineaPasajeroAuto = linea;
    }

    // Confirma al pasajero actual y deja los campos listos para otro pasajero
    function agregarPasajeroAObservaciones() {
        var dni = $('#txt_dni_pasajero').val().trim();
        var nombre = $('#txt_nombre_pasajero').val().trim();
        if (dni === '' || nombre === '') {
            return avisoRapido('warning', 'Escriba el DNI del pasajero');
        }
        ponerPasajeroEnObservaciones(dni, nombre);
        lineaPasajeroAuto = null;
        limpiarDatosPasajero();
        avisoRapido('success', 'Pasajero agregado. Puede ingresar otro.');
    }

    function limpiarDatosPasajero() {
        // Quita de Observaciones al pasajero automático que no se confirmó con "Agregar"
        if (lineaPasajeroAuto) {
            var obs = document.getElementById('txt_observaciones');
            obs.value = obs.value.split('\n').filter(function (l) { return l !== lineaPasajeroAuto; }).join('\n');
            lineaPasajeroAuto = null;
        }
        ultimoDniPasajero = '';
        document.getElementById('txt_dni_pasajero').value = '';
        document.getElementById('txt_nombre_pasajero').value = '';
    }

    function soloNumeros(e) {
        var key = e.keyCode || e.which;
        var tecla = String.fromCharCode(key).toLowerCase();
        var letras = "0123456789";
        var especiales = [8, 37, 39, 46];

        var tecla_especial = false;
        for (var i in especiales) {
            if (key == especiales[i]) {
                tecla_especial = true;
                break;
            }
        }

        if (letras.indexOf(tecla) == -1 && !tecla_especial) {
            return false;
        }
    }

    function habilitarEdicionSerie() {
        var checkbox = document.getElementById('check_editar_serie');
        var serie = document.getElementById('txt_serie');
        var correlativo = document.getElementById('txt_correlativo');
        var iconSerie = document.getElementById('icon_serie');
        var iconCorrelativo = document.getElementById('icon_correlativo');
        
        if (checkbox.checked) {
            serie.removeAttribute('readonly');
            correlativo.removeAttribute('readonly');
            serie.style.backgroundColor = '#fff3cd';
            correlativo.style.backgroundColor = '#fff3cd';
            serie.style.borderColor = '#ffc107';
            correlativo.style.borderColor = '#ffc107';
            
            iconSerie.classList.remove('fa-lock');
            iconSerie.classList.add('fa-lock-open');
            iconSerie.classList.remove('text-muted');
            iconSerie.classList.add('text-warning');
            
            iconCorrelativo.classList.remove('fa-lock');
            iconCorrelativo.classList.add('fa-lock-open');
            iconCorrelativo.classList.remove('text-muted');
            iconCorrelativo.classList.add('text-warning');
            
            Swal.fire({
                icon: 'info',
                title: 'Edición Habilitada',
                text: 'Puede modificar serie y correlativo',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            serie.setAttribute('readonly', 'readonly');
            correlativo.setAttribute('readonly', 'readonly');
            serie.style.backgroundColor = '';
            correlativo.style.backgroundColor = '';
            serie.style.borderColor = '';
            correlativo.style.borderColor = '';
            
            iconSerie.classList.remove('fa-lock-open');
            iconSerie.classList.add('fa-lock');
            iconSerie.classList.remove('text-warning');
            iconSerie.classList.add('text-muted');
            
            iconCorrelativo.classList.remove('fa-lock-open');
            iconCorrelativo.classList.add('fa-lock');
            iconCorrelativo.classList.remove('text-warning');
            iconCorrelativo.classList.add('text-muted');
            
            cambiarTipoComprobante();
        }
    }
</script>
