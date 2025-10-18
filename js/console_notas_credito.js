
// ============================================================
// VER DETALLE (Reutiliza la función existente)
// ============================================================
// Ya existe en console_comprobantes.js

// ============================================================
// ENVIAR A SUNAT (DESDE LISTADO) - Reutiliza función existente
// ============================================================
// Ya existe abrirModalEnviar() y confirmarEnvioSunat()

// ============================================================
// DESCARGAS - Reutilizan funciones existentes
// ============================================================
// descargarXML(), descargarCDR(), imprimirTicket() ya existen
// ============================================================
// VARIABLES GLOBALES
// ============================================================
var tbl_notas_credito;

// ============================================================
// INICIALIZACIÓN
// ============================================================
$(document).ready(function() {
    listar_notas_credito();
    establecerFechasFiltro();
    
    // Establecer fecha actual en el formulario
    var hoy = new Date().toISOString().split('T')[0];
    $('#txt_fecha_emision_nc').val(hoy);
});

// ============================================================
// ESTABLECER FECHAS POR DEFECTO EN FILTROS
// ============================================================
function establecerFechasFiltro() {
    var hoy = new Date();
    var hace30dias = new Date();
    hace30dias.setDate(hace30dias.getDate() - 30);
    
    $('#txt_fecha_desde').val(hace30dias.toISOString().split('T')[0]);
    $('#txt_fecha_hasta').val(hoy.toISOString().split('T')[0]);
}

// ============================================================
// CALCULAR TOTALES DE NOTA DE CRÉDITO
// ============================================================
function calcularTotalesNC() {
    var montoTotal = parseFloat($('#txt_monto_nc').val()) || 0;
    var montoMaximo = parseFloat($('#txt_monto_maximo').val().replace('S/ ', '')) || 0;
    
    if (montoTotal > montoMaximo) {
        Swal.fire('Advertencia', 'El monto no puede ser mayor al total del comprobante', 'warning');
        $('#txt_monto_nc').val(montoMaximo.toFixed(2));
        montoTotal = montoMaximo;
    }
    
    // Cálculo de IGV (18%)
    var baseGravada = montoTotal / 1.18;
    var igv = montoTotal - baseGravada;
    
    $('#txt_base_nc').val(baseGravada.toFixed(2));
    $('#txt_igv_nc').val(igv.toFixed(2));
    $('#txt_total_nc').val(montoTotal.toFixed(2));
}


// ============================================================
// LIMPIAR FORMULARIO
// ============================================================
function limpiarFormularioNC() {
    console.log('🔍 Limpiando formulario...'); // DEBUG
    
    $('#select_tipo_comp_buscar').val('');
    $('#txt_serie_buscar').val('');
    $('#txt_correlativo_buscar').val('');
    $('#datos_comprobante').hide();
    $('#txt_id_comprobante_afectado').val('');
    $('#select_motivo_nc').val('');
    $('#txt_descripcion_nc').val('');
    $('#txt_monto_nc').val('');
    $('#txt_monto_maximo').val('');
    $('#txt_base_nc').val('');
    $('#txt_igv_nc').val('');
    $('#txt_total_nc').val('');
    
    var hoy = new Date().toISOString().split('T')[0];
    $('#txt_fecha_emision_nc').val(hoy);
    
    $('#txt_serie_nc').val('FN01');
    $('#txt_correlativo_nc').val('Cargando...'); // AGREGAR ESTA LÍNEA
}
// ============================================================
// ABRIR MODAL DE REGISTRO
// ============================================================
function AbrirModalRegistro() {
    $('#modal_registro').modal('show');
    limpiarFormularioNC();
    
    // Llamar DESPUÉS de limpiar
    setTimeout(function() {
        obtenerCorrelativoNC('01');
    }, 100); // AGREGAR ESTE BLOQUE
}

// ============================================================
// BUSCAR COMPROBANTE A AFECTAR
// ============================================================

function buscarComprobante() {
    var tipo_comp = $('#select_tipo_comp_buscar').val();
    var serie = $('#txt_serie_buscar').val().trim().toUpperCase(); // LEER DE TXT_SERIE_BUSCAR
    var correlativo = $('#txt_correlativo_buscar').val().trim();

    if (!tipo_comp) {
        return Swal.fire('Advertencia', 'Seleccione el tipo de comprobante', 'warning');
    }
    if (!serie) { // CAMBIAR VARIABLE
        return Swal.fire('Advertencia', 'Ingrese la serie del comprobante', 'warning');
    }
    if (!correlativo) {
        return Swal.fire('Advertencia', 'Ingrese el correlativo del comprobante', 'warning');
    }

    Swal.fire({
        title: 'Buscando comprobante...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '../controller/comprobante/controller_comprobante.php',
        type: 'POST',
        data: {
            accion: 'BUSCAR_COMPROBANTE',
            tipo_comprobante: tipo_comp,
            serie: serie, // CAMBIAR: Enviar la serie del comprobante a buscar
            correlativo: correlativo
        },
        dataType: 'json'
    }).done(function(resp) {
        Swal.close();
        if (resp.status == 'success') {
            var data = resp.data;
            
            // Mostrar datos del comprobante
            $('#txt_id_comprobante_afectado').val(data.id_comprobante);
            $('#span_cliente').text(data.razon_social);
            $('#span_documento').text(data.numero_documento);
            $('#span_fecha').text(data.fecha_emision);
            $('#span_total').text('S/ ' + parseFloat(data.total).toFixed(2));
            $('#txt_monto_maximo').val('S/ ' + parseFloat(data.total).toFixed(2));
            
            // AHORA SÍ: Establecer serie de la NOTA DE CRÉDITO según tipo de comprobante encontrado
            var serie_nc = (tipo_comp == '01') ? 'FN01' : 'BN01';
            $('#txt_serie_nc').val(serie_nc);
            
            // Obtener correlativo de la nota de crédito
            obtenerCorrelativoNC(tipo_comp);
            
            $('#datos_comprobante').fadeIn();
            Swal.fire('Éxito', 'Comprobante encontrado', 'success');
        } else {
            $('#datos_comprobante').hide();
            Swal.fire('Advertencia', resp.message, 'warning');
        }
    }).fail(function() {
        Swal.close();
        Swal.fire('Error', 'Error al buscar el comprobante', 'error');
    });
}


// ============================================================
// GUARDAR NOTA DE CRÉDITO
// ============================================================
function guardarNotaCredito(estado) {
    // Validaciones
    var id_comprobante_afectado = $('#txt_id_comprobante_afectado').val();
    var serie_nc = $('#txt_serie_nc').val(); // AGREGAR
    var correlativo_nc = $('#txt_correlativo_nc').val(); // AGREGAR
    var motivo = $('#select_motivo_nc').val();
    var motivo2 = $('#select_motivo_nc option:selected').text(); // Devuelve: "01 - ANULACIÓN DE LA OPERACIÓN"
    var descripcion = $('#txt_descripcion_nc').val().trim();
    var monto = $('#txt_monto_nc').val();
    var base = $('#txt_base_nc').val();
    var igv = $('#txt_igv_nc').val();
    var total = $('#txt_total_nc').val();
    
    if (!id_comprobante_afectado) {
        return Swal.fire('Advertencia', 'Primero debe buscar el comprobante a afectar', 'warning');
    }
    
    if (!motivo) {
        return Swal.fire('Advertencia', 'Seleccione el motivo de la nota de crédito', 'warning');
    }
    
    if (!descripcion) {
        return Swal.fire('Advertencia', 'Ingrese la descripción u observación', 'warning');
    }
    
    if (!monto || parseFloat(monto) <= 0) {
        return Swal.fire('Advertencia', 'Ingrese un monto válido', 'warning');
    }
    
    var id_usuario = $('#txtprincipalid').val();
    
    var formData = {
        accion: 'REGISTRAR_NOTA_CREDITO',
        id_comprobante_origen: id_comprobante_afectado,
        serie: serie_nc, // AGREGAR
        correlativo: correlativo_nc, // AGREGAR
        motivo_nota: motivo,
        motivo2: motivo2,
        observaciones: descripcion,
        total_gravada: base,
        total_igv: igv,
        total: total,
        estado_sunat: estado,
        id_usuario: id_usuario
    };
    
    Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../controller/comprobante/controller_comprobante.php',
        type: 'POST',
        data: formData,
        dataType: 'json'
    }).done(function(resp) {
        Swal.close();
        
        if (resp.status == 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Nota de Crédito Guardada',
                text: resp.message,
                showConfirmButton: true
            }).then(() => {
                $('#modal_registro').modal('hide');
                tbl_notas_credito.ajax.reload();
                limpiarFormularioNC();
            });
        } else {
            Swal.fire('Error', resp.message, 'error');
        }
    }).fail(function() {
        Swal.close();
        Swal.fire('Error', 'Error al guardar la nota de crédito', 'error');
    });
}


// ============================================================
// GUARDAR Y ENVIAR A SUNAT
// ============================================================
function guardarYEnviarNC() {
    // Validaciones previas
    var id_comprobante_afectado = $('#txt_id_comprobante_afectado').val();
    var motivo = $('#select_motivo_nc').val();
    var descripcion = $('#txt_descripcion_nc').val().trim();
    var monto = $('#txt_monto_nc').val();
    
    if (!id_comprobante_afectado || !motivo || !descripcion || !monto || parseFloat(monto) <= 0) {
        return Swal.fire('Advertencia', 'Complete todos los campos obligatorios', 'warning');
    }
    
    Swal.fire({
        title: '¿Confirmar envío a SUNAT?',
        text: 'Se guardará y enviará la nota de crédito a SUNAT',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            procesarGuardarYEnviar();
        }
    });
}

function procesarGuardarYEnviar() {
    var id_comprobante_afectado = $('#txt_id_comprobante_afectado').val();
    var serie_nc = $('#txt_serie_nc').val(); // AGREGAR
    var correlativo_nc = $('#txt_correlativo_nc').val(); // AGREGAR
    var motivo = $('#select_motivo_nc').val();
    var motivo2 = $('#select_motivo_nc option:selected').text(); // Devuelve: "01 - ANULACIÓN DE LA OPERACIÓN"
    var descripcion = $('#txt_descripcion_nc').val().trim();
    var base = $('#txt_base_nc').val();
    var igv = $('#txt_igv_nc').val();
    var total = $('#txt_total_nc').val();
    var id_usuario = $('#txtprincipalid').val();
    
    Swal.fire({
        title: 'Procesando...',
        html: 'Paso 1/2: Registrando nota de crédito...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../controller/comprobante/controller_comprobante.php',
        type: 'POST',
        data: {
            accion: 'REGISTRAR_NOTA_CREDITO',
            id_comprobante_origen: id_comprobante_afectado,
            serie: serie_nc, // AGREGAR
            correlativo: correlativo_nc, // AGREGAR
            motivo_nota: motivo,
            motivo2: motivo2,
            observaciones: descripcion,
            total_gravada: base,
            total_igv: igv,
            total: total,
            estado_sunat: 'PENDIENTE',
            id_usuario: id_usuario
        },
        dataType: 'json'
    }).done(function(resp) {
        if (resp.status == 'success') {
            // Actualizar mensaje
            Swal.update({
                html: 'Paso 2/2: Enviando a SUNAT...'
            });
            
            // Enviar a SUNAT
            enviarNotaCreditoSunat(resp.id_comprobante);
        } else {
            Swal.close();
            Swal.fire('Error', resp.message, 'error');
        }
    }).fail(function() {
        Swal.close();
        Swal.fire('Error', 'Error al registrar la nota de crédito', 'error');
    });
}

function enviarNotaCreditoSunat(id_comprobante) {
    $.ajax({
        url: '../controller/comprobante/controller_comprobante.php',
        type: 'POST',
        data: {
            accion: 'ENVIAR_SUNAT',
            id_comprobante: id_comprobante
        },
        dataType: 'json'
    }).done(function(resp) {
        Swal.close();
        
        if (resp.status == 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Nota de Crédito Aceptada!',
                html: resp.message,
                showConfirmButton: true
            }).then(() => {
                $('#modal_registro').modal('hide');
                tbl_notas_credito.ajax.reload();
                limpiarFormularioNC();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error al enviar a SUNAT',
                html: resp.message,
                showConfirmButton: true
            });
        }
    }).fail(function() {
        Swal.close();
        Swal.fire('Error', 'Error al comunicarse con SUNAT', 'error');
    });
}

// ============================================================
// LISTAR NOTAS DE CRÉDITO CON EXPORTACIÓN
// ============================================================
function listar_notas_credito() {
    tbl_notas_credito = $("#tabla_notas_credito").DataTable({
        "ordering": true,
        "bLengthChange": true,
        "searching": true,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        "pageLength": 10,
        "destroy": true,
        "processing": true,
        "responsive": true,
        "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>><"row"<"col-sm-12 text-right"B>>rtip',
        "buttons": [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                },
                title: 'Notas de Crédito',
                filename: 'Notas_Credito_' + new Date().toISOString().slice(0,10)
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                titleAttr: 'Exportar a PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                },
                title: 'Notas de Crédito',
                filename: 'Notas_Credito_' + new Date().toISOString().slice(0,10),
                orientation: 'landscape',
                pageSize: 'A4',
                customize: function(doc) {
                    doc.styles.title = {
                        color: '#dc3545',
                        fontSize: '18',
                        alignment: 'center',
                        bold: true
                    };
                    doc.defaultStyle.fontSize = 8;
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                titleAttr: 'Imprimir',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                },
                title: 'Notas de Crédito',
                customize: function(win) {
                    $(win.document.body).css('font-size', '10pt');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', '10pt');
                }
            }
        ],
        "ajax": {
            "url": "../controller/comprobante/controller_comprobante.php",
            "type": "POST",
            "data": { 
                accion: "LISTAR_NOTAS_CREDITO"
            }
        },
        "columns": [
            { "data": "id_comprobante" },
            { 
                "data": null,
                "render": data => "<b>" + data.serie + "-" + data.correlativo + "</b>"
            },
            { 
                "data": "fecha_emision",
                "render": data => data ? new Date(data).toLocaleDateString("es-PE") : "-"
            },
            { 
                "data": null,
                "render": data => data.comprobante_afectado || "-"
            },
            { "data": "razon_social" },
            { "data": "motivo_nota" },
            { 
                "data": "total",
                "render": data => "S/ " + parseFloat(data).toFixed(2)
            },
            { 
                "data": "estado_sunat",
                "render": function(data) {
                    if (data == "PENDIENTE") return '<span class="badge badge-warning"><i class="fas fa-clock"></i> PENDIENTE</span>';
                    if (data == "ACEPTADO") return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> ACEPTADO</span>';
                    if (data == "RECHAZADO") return '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> RECHAZADO</span>';
                    if (data == "ANULADO") return '<span class="badge badge-secondary"><i class="fas fa-ban"></i> ANULADO</span>';
                    return data;
                }
            },
            { "data": "usu_nombre" },
            { 
                "data": null,
                "orderable": false,
                "render": function(data) {
                    let estado = data.estado_sunat;
                    let estado_doc = data.estado_documento;
                    let botones = `
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" data-boundary="window">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" style="z-index:1050;">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="verDetalle(${data.id_comprobante})">
                                    <i class="fas fa-eye text-info"></i> Ver Detalle
                                </a>`;
                    
                    if (estado == "PENDIENTE" && estado_doc == "ACTIVO") {
                        botones += `
                                <a class="dropdown-item" href="javascript:void(0)" onclick="abrirModalEnviar(${data.id_comprobante}, '${data.serie}', '${data.correlativo}')">
                                    <i class="fas fa-paper-plane text-success"></i> Enviar a SUNAT
                                </a>`;
                    }
                    
                    if ((estado == "ENVIADO" || estado == "ACEPTADO") && estado_doc == "ACTIVO") {
                        botones += `
                                <a class="dropdown-item" href="javascript:void(0)" onclick="descargarXMLNC('${data.serie}', '${data.correlativo}')">
                                    <i class="fas fa-file-code text-primary"></i> Descargar XML
                                </a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="descargarCDRNC('${data.serie}', '${data.correlativo}')">
                                    <i class="fas fa-file-archive text-secondary"></i> Descargar CDR
                                </a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="imprimirTicketNC(${data.id_comprobante})">
                                    <i class="fas fa-print text-dark"></i> Imprimir
                                </a>`;
                    }
                    
                    botones += `
                            </div>
                        </div>`;
                    return botones;
                }
            }
        ],
        "language": { 
            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
            "buttons": {
                "excel": "Excel",
                "pdf": "PDF",
                "print": "Imprimir"
            }
        }
    });
}

// ============================================================
// LISTAR CON FILTROS Y EXPORTACIÓN
// ============================================================
function listar_notas_credito_filtro() {
    let estado = $("#select_estado_filtro").val();
    let fecha_desde = $("#txt_fecha_desde").val();
    let fecha_hasta = $("#txt_fecha_hasta").val();

    if (tbl_notas_credito) tbl_notas_credito.destroy();

    tbl_notas_credito = $("#tabla_notas_credito").DataTable({
        "ordering": true,
        "bLengthChange": true,
        "searching": true,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        "pageLength": 10,
        "destroy": true,
        "processing": true,
        "responsive": true,
        "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>><"row"<"col-sm-12 text-right"B>>rtip',
        "buttons": [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                },
                title: 'Notas de Crédito - Filtrado',
                filename: 'Notas_Credito_Filtrado_' + new Date().toISOString().slice(0,10)
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                titleAttr: 'Exportar a PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                },
                title: 'Notas de Crédito - Filtrado',
                filename: 'Notas_Credito_Filtrado_' + new Date().toISOString().slice(0,10),
                orientation: 'landscape',
                pageSize: 'A4',
                customize: function(doc) {
                    doc.styles.title = {
                        color: '#dc3545',
                        fontSize: '18',
                        alignment: 'center',
                        bold: true
                    };
                    doc.defaultStyle.fontSize = 8;
                    
                    // Agregar información de filtros
                    let filterInfo = 'Filtros aplicados: ';
                    if (estado) filterInfo += 'Estado: ' + estado + ' | ';
                    if (fecha_desde) filterInfo += 'Desde: ' + fecha_desde + ' | ';
                    if (fecha_hasta) filterInfo += 'Hasta: ' + fecha_hasta;
                    
                    doc.content[1].text = filterInfo;
                    doc.content[1].margin = [0, 0, 0, 12];
                    doc.content[1].fontSize = 9;
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                titleAttr: 'Imprimir',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                },
                title: 'Notas de Crédito - Filtrado',
                messageTop: function() {
                    let msg = '<strong>Filtros aplicados:</strong><br>';
                    if (estado) msg += 'Estado: ' + estado + '<br>';
                    if (fecha_desde) msg += 'Desde: ' + fecha_desde + '<br>';
                    if (fecha_hasta) msg += 'Hasta: ' + fecha_hasta;
                    return msg;
                },
                customize: function(win) {
                    $(win.document.body).css('font-size', '10pt');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', '10pt');
                }
            }
        ],
        "ajax": {
            "url": "../controller/comprobante/controller_comprobante.php",
            "type": "POST",
            "data": {
                accion: "LISTAR_NOTAS_CREDITO",
                estado: estado,
                fecha_desde: fecha_desde,
                fecha_hasta: fecha_hasta
            }
        },
        "columns": [
            { "data": "id_comprobante" },
            { 
                "data": null,
                "render": data => "<b>" + data.serie + "-" + data.correlativo + "</b>"
            },
            { 
                "data": "fecha_emision",
                "render": data => data ? new Date(data).toLocaleDateString("es-PE") : "-"
            },
            { 
                "data": null,
                "render": data => data.comprobante_afectado || "-"
            },
            { "data": "razon_social" },
            { "data": "motivo_nota" },
            { 
                "data": "total",
                "render": data => "S/ " + parseFloat(data).toFixed(2)
            },
            { 
                "data": "estado_sunat",
                "render": function(data) {
                    if (data == "PENDIENTE") return '<span class="badge badge-warning"><i class="fas fa-clock"></i> PENDIENTE</span>';
                    if (data == "ACEPTADO") return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> ACEPTADO</span>';
                    if (data == "RECHAZADO") return '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> RECHAZADO</span>';
                    if (data == "ANULADO") return '<span class="badge badge-secondary"><i class="fas fa-ban"></i> ANULADO</span>';
                    return data;
                }
            },
            { "data": "usu_nombre" },
            { 
                "data": null,
                "orderable": false,
                "render": function(data) {
                    let estado = data.estado_sunat;
                    let estado_doc = data.estado_documento;
                    let botones = `
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" data-boundary="window">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" style="z-index:1050;">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="verDetalle(${data.id_comprobante})">
                                    <i class="fas fa-eye text-info"></i> Ver Detalle
                                </a>`;
                    
                    if (estado == "PENDIENTE" && estado_doc == "ACTIVO") {
                        botones += `
                                <a class="dropdown-item" href="javascript:void(0)" onclick="abrirModalEnviar(${data.id_comprobante}, '${data.serie}', '${data.correlativo}')">
                                    <i class="fas fa-paper-plane text-success"></i> Enviar a SUNAT
                                </a>`;
                    }
                    
                    if ((estado == "ENVIADO" || estado == "ACEPTADO") && estado_doc == "ACTIVO") {
                        botones += `
                                <a class="dropdown-item" href="javascript:void(0)" onclick="descargarXMLNC('${data.serie}', '${data.correlativo}')">
                                    <i class="fas fa-file-code text-primary"></i> Descargar XML
                                </a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="descargarCDRNC('${data.serie}', '${data.correlativo}')">
                                    <i class="fas fa-file-archive text-secondary"></i> Descargar CDR
                                </a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="imprimirTicketNC(${data.id_comprobante})">
                                    <i class="fas fa-print text-dark"></i> Imprimir
                                </a>`;
                    }
                    
                    botones += `
                            </div>
                        </div>`;
                    return botones;
                }
            }
        ],
        "language": { 
            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json",
            "buttons": {
                "excel": "Excel",
                "pdf": "PDF",
                "print": "Imprimir"
            }
        }
    });
}

// ============================================================
// VER DETALLE DE NOTA DE CRÉDITO - DISEÑO PROFESIONAL
// ============================================================
function verDetalle(id_comprobante) {
    $.ajax({
        url: "../controller/comprobante/controller_comprobante.php",
        type: 'POST',
        data: {
            accion: 'OBTENER_COMPROBANTE',
            id_comprobante: id_comprobante
        },
        dataType: 'json'
    }).done(function(data) {
        if (data) {
            // Determinar el tipo de comprobante
            let tipoNota = data.tipo_comprobante === '07' ? 'CRÉDITO' : 'DÉBITO';
            let colorHeader = data.tipo_comprobante === '07' ? '#dc3545' : '#17a2b8';
            
            // Obtener el estado visual
            let estadoBadge = getEstadoBadge(data.estado_sunat);
            
            let html = `
                <style>
                    .detalle-nc-container {
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        padding: 0;
                    }
                    .nc-header {
                        background: linear-gradient(135deg, ${colorHeader} 0%, ${colorHeader}dd 100%);
                        color: white;
                        padding: 20px;
                        border-radius: 8px 8px 0 0;
                        text-align: center;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    }
                    .nc-header h3 {
                        margin: 0;
                        font-size: 24px;
                        font-weight: 600;
                        letter-spacing: 0.5px;
                    }
                    .nc-numero {
                        font-size: 28px;
                        font-weight: 700;
                        margin: 8px 0 0 0;
                        letter-spacing: 1px;
                    }
                    .nc-body {
                        background: #f8f9fa;
                        padding: 25px;
                    }
                    .info-section {
                        background: white;
                        border-radius: 8px;
                        padding: 20px;
                        margin-bottom: 20px;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                        border-left: 4px solid ${colorHeader};
                    }
                    .info-section-title {
                        font-size: 16px;
                        font-weight: 600;
                        color: #2c3e50;
                        margin-bottom: 15px;
                        padding-bottom: 10px;
                        border-bottom: 2px solid #e9ecef;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                    }
                    .info-row {
                        display: flex;
                        padding: 10px 0;
                        border-bottom: 1px solid #f1f3f5;
                    }
                    .info-row:last-child {
                        border-bottom: none;
                    }
                    .info-label {
                        font-weight: 600;
                        color: #495057;
                        min-width: 150px;
                        font-size: 14px;
                    }
                    .info-value {
                        color: #212529;
                        flex: 1;
                        font-size: 14px;
                    }
                    .comprobante-afectado {
                        background: #fff3cd;
                        border-left-color: #ffc107;
                    }
                    .comprobante-afectado .info-section-title {
                        color: #856404;
                    }
                    .motivo-section {
                        background: #e7f3ff;
                        border-left-color: #0066cc;
                    }
                    .motivo-section .info-section-title {
                        color: #004085;
                    }
                    .motivo-texto {
                        background: white;
                        padding: 12px;
                        border-radius: 6px;
                        font-style: italic;
                        color: #495057;
                        line-height: 1.6;
                        border: 1px solid #cfe2ff;
                    }
                    .resumen-section {
                        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                        color: white;
                        border-radius: 8px;
                        padding: 25px;
                        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
                        border: none;
                    }
                    .resumen-title {
                        font-size: 18px;
                        font-weight: 600;
                        text-align: center;
                        margin-bottom: 20px;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                    }
                    .resumen-grid {
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 20px;
                        margin-top: 15px;
                    }
                    .resumen-item {
                        text-align: center;
                        padding: 15px;
                        background: rgba(255,255,255,0.15);
                        border-radius: 8px;
                        backdrop-filter: blur(10px);
                    }
                    .resumen-label {
                        font-size: 13px;
                        font-weight: 500;
                        margin-bottom: 8px;
                        opacity: 0.95;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    }
                    .resumen-valor {
                        font-size: 26px;
                        font-weight: 700;
                        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
                    }
                    .total-destacado {
                        background: rgba(255,255,255,0.25);
                        border: 2px solid white;
                    }
                    .estado-badge {
                        display: inline-block;
                        padding: 6px 16px;
                        border-radius: 20px;
                        font-size: 12px;
                        font-weight: 600;
                        margin-top: 8px;
                        letter-spacing: 0.5px;
                    }
                    .badge-pendiente { background: #ffc107; color: #000; }
                    .badge-aceptado { background: #28a745; color: #fff; }
                    .badge-rechazado { background: #dc3545; color: #fff; }
                    .badge-enviado { background: #17a2b8; color: #fff; }
                </style>
                
                <div class="detalle-nc-container">
                    <!-- ENCABEZADO -->
                    <div class="nc-header">
                        <h3>📋 NOTA DE ${tipoNota}</h3>
                        <div class="nc-numero">${data.serie}-${data.correlativo}</div>
                        ${estadoBadge}
                    </div>
                    
                    <div class="nc-body">
                        <!-- INFORMACIÓN GENERAL -->
                        <div class="info-section">
                            <div class="info-section-title">
                                📅 Información General
                            </div>
                            <div class="info-row">
                                <div class="info-label">Fecha de Emisión:</div>
                                <div class="info-value"><strong>${data.fecha_emision}</strong></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Cliente:</div>
                                <div class="info-value"><strong>${data.razon_social}</strong></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">${data.tipo_documento === '6' ? 'RUC' : 'DNI'}:</div>
                                <div class="info-value">${data.numero_documento}</div>
                            </div>
                        </div>
                        
                        <!-- COMPROBANTE AFECTADO -->
                        <div class="info-section comprobante-afectado">
                            <div class="info-section-title">
                                📄 Comprobante Afectado
                            </div>
                            <div class="info-row">
                                <div class="info-label">Tipo:</div>
                                <div class="info-value"><strong>${data.tipo_comprobante_origen === '01' ? 'FACTURA' : 'BOLETA'}</strong></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Número:</div>
                                <div class="info-value"><strong style="font-size: 16px;">${data.serie_origen}-${data.correlativo_origen}</strong></div>
                            </div>
                        </div>
                        
                        <!-- MOTIVO -->
                        <div class="info-section motivo-section">
                            <div class="info-section-title">
                                📝 Motivo / Sustento
                            </div>
                            <div class="info-row">
                                <div class="info-label">Código:</div>
                                <div class="info-value"><strong>${data.motivo_nota || '01'}</strong></div>
                            </div>
                            <div class="motivo-texto">
                                ${data.texto_motivo || data.observaciones || 'ANULACIÓN DE LA OPERACIÓN'}
                            </div>
                        </div>
                        
                        <!-- RESUMEN FINANCIERO -->
                        <div class="resumen-section">
                            <div class="resumen-title">💰 Resumen Financiero</div>
                            <div class="resumen-grid">
                                <div class="resumen-item">
                                    <div class="resumen-label">Base Gravada</div>
                                    <div class="resumen-valor">S/ ${parseFloat(data.total_gravada).toFixed(2)}</div>
                                </div>
                                <div class="resumen-item">
                                    <div class="resumen-label">IGV (18%)</div>
                                    <div class="resumen-valor">S/ ${parseFloat(data.total_igv).toFixed(2)}</div>
                                </div>
                                <div class="resumen-item total-destacado">
                                    <div class="resumen-label">Total</div>
                                    <div class="resumen-valor">S/ ${parseFloat(data.total).toFixed(2)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            Swal.fire({
                html: html,
                width: '700px',
                showCloseButton: true,
                showConfirmButton: false,
                padding: 0,
                customClass: {
                    popup: 'rounded-lg',
                    htmlContainer: 'p-0'
                }
            });
        }
    }).fail(function() {
        Swal.fire('Error', 'No se pudo cargar el detalle', 'error');
    });
}

// ============================================================
// FUNCIÓN AUXILIAR: OBTENER BADGE DE ESTADO
// ============================================================
function getEstadoBadge(estado) {
    const estados = {
        'PENDIENTE': { clase: 'badge-pendiente', icono: '⚠️', texto: 'PENDIENTE' },
        'ENVIADO': { clase: 'badge-enviado', icono: '📤', texto: 'ENVIADO' },
        'ACEPTADO': { clase: 'badge-aceptado', icono: '✅', texto: 'ACEPTADO' },
        'RECHAZADO': { clase: 'badge-rechazado', icono: '❌', texto: 'RECHAZADO' }
    };
    
    const estadoInfo = estados[estado?.toUpperCase()] || estados['PENDIENTE'];
    
    return `<div class="estado-badge ${estadoInfo.clase}">
                ${estadoInfo.icono} ${estadoInfo.texto}
            </div>`;
}

// ============================================================
// ENVIAR A SUNAT (DESDE LISTADO)
// ============================================================
function abrirModalEnviar(id_comprobante, serie, correlativo) {
    Swal.fire({
        title: '¿Enviar a SUNAT?',
        html: `Se enviará la Nota de Crédito <strong>${serie}-${correlativo}</strong> a SUNAT`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Enviando a SUNAT...',
                html: 'Por favor espere, esto puede tardar hasta 30 segundos...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { 
                    Swal.showLoading(); 
                }
            });
            
            $.ajax({
                url: "../controller/comprobante/controller_comprobante.php",
                type: 'POST',
                data: {
                    accion: 'ENVIAR_SUNAT',
                    id_comprobante: id_comprobante // CAMBIAR AQUÍ
                },
                dataType: 'json',
                timeout: 60000 // 60 segundos de timeout
            }).done(function(resp) {
                Swal.close();
                
                if (resp.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        html: resp.message,
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        tbl_notas_credito.ajax.reload();
                    });
                } else if (resp.status == 'info') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Enviado',
                        html: resp.message,
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        tbl_notas_credito.ajax.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: resp.message + '<br><br><small>' + (resp.output || '') + '</small>',
                        confirmButtonText: 'Aceptar'
                    });
                }
            }).fail(function(jqXHR, textStatus) {
                Swal.close();
                
                if (textStatus === 'timeout') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tiempo agotado',
                        text: 'La operación tardó demasiado. Verifica el estado en la lista.',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        tbl_notas_credito.ajax.reload();
                    });
                } else {
                    Swal.fire('Error', 'Error de conexión con el servidor', 'error');
                }
            });
        }
    });
}

// ============================================================
// DESCARGAS
// ============================================================
function descargarXMLNC(serie, correlativo) {
    let url = "../greenter/xml/" + serie + "-" + correlativo + ".xml";
    window.open(url, "_blank");
}

function descargarCDRNC(serie, correlativo) {
    let url = "../greenter/cdr/R-" + serie + "-" + correlativo + ".zip";
    window.open(url, "_blank");
}

function imprimirTicketNC(id) {
    window.open("../view/MPDF/REPORTE/ticket_nota_de_credito.php?id=" + id, "_blank");
}

// ============================================================
// OBTENER CORRELATIVO DE NOTA DE CRÉDITO
// ============================================================
function obtenerCorrelativoNC(tipo_comprobante) {
    console.log('Obteniendo correlativo para tipo:', tipo_comprobante); // DEBUG
    
    $.ajax({
        url: '../controller/comprobante/controller_comprobante.php',
        type: 'POST',
        data: {
            accion: 'OBTENER_CORRELATIVO_NC',
            tipo_comprobante: tipo_comprobante
        },
        dataType: 'json'
    }).done(function(resp) {
        console.log('Respuesta del servidor:', resp); // DEBUG
        
        if (resp.status == 'success') {
            $('#txt_correlativo_nc').val(resp.correlativo);
        } else {
            $('#txt_correlativo_nc').val('00000001');
        }
    }).fail(function(error) {
        console.error('Error al obtener correlativo:', error); // DEBUG
        $('#txt_correlativo_nc').val('00000001');
    });
}