<script src="../js/console_reportes.js?rev=<?php echo time(); ?>"></script>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>🚗 REPORTE DE CHOFERES</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">REPORTE CHOFERES</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        
        <!-- FILTROS -->
        <div class="row mb-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(90deg, #dc3545, #c82333);">
                        <h3 class="card-title text-white">
                            <i class="fas fa-filter"></i> <b>Filtros de Búsqueda</b>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Estado del Chofer:</label>
                                <select class="form-control" id="filtro_estado_chofer">
                                    <option value="">Todos</option>
                                    <option value="ACTIVO">ACTIVO</option>
                                    <option value="INACTIVO">INACTIVO</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Fecha Desde:</label>
                                <input type="date" class="form-control" id="filtro_choferes_fecha_desde">
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Fecha Hasta:</label>
                                <input type="date" class="form-control" id="filtro_choferes_fecha_hasta">
                            </div>
                            
                            <div class="col-md-3">
                                <label>&nbsp;</label><br>
                                <button class="btn btn-danger btn-block btn-lg" onclick="listar_reporte_choferes()">
                                    <i class="fas fa-search"></i> Generar Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARDS RESUMEN -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="total_choferes_activos">0</h3>
                        <p>Choferes Activos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="total_choferes_inactivos">0</h3>
                        <p>Choferes Inactivos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner text-white">
                        <h3 id="total_salidas_realizadas">0</h3>
                        <p>Salidas Realizadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bus"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total_facturado_choferes">S/ 0.00</h3>
                        <p>Total Facturado</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- ALERTA LICENCIAS POR VENCER -->
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-warning" id="alerta_licencias" style="display:none;">
                    <h5><i class="fas fa-exclamation-triangle"></i> <b>Licencias por Vencer</b></h5>
                    <div id="contenido_licencias_vencer"></div>
                </div>
            </div>
        </div>

        <!-- TABLA DE CHOFERES -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> <b>Listado de Choferes</b>
                        </h3>
                        <div class="card-tools">
                            <button class="btn btn-tool" onclick="exportarExcelChoferes()">
                                <i class="fas fa-file-excel text-white"></i> Exportar
                            </button>
                            <button class="btn btn-tool" onclick="imprimirReporteChoferes()">
                                <i class="fas fa-print text-white"></i> Imprimir
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla_reporte_choferes" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#dc3545; color:#FFFFFF;">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tipo Doc.</th>
                                        <th>N° Documento</th>
                                        <th>Nombres y Apellidos</th>
                                        <th>Celular</th>
                                        <th>Marca Vehículo</th>
                                        <th>Placa</th>
                                        <th>N° Licencia</th>
                                        <th>Venc. Licencia</th>
                                        <th>Estado</th>
                                        <th>Total Salidas</th>
                                        <th>Comprobantes</th>
                                        <th>Total Facturado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOP CHOFERES -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title"><i class="fas fa-trophy"></i> <b>Top 10 Choferes Más Activos</b></h3>
                    </div>
                    <div class="card-body">
                        <div class="row" id="contenido_top_choferes">
                            <div class="col-12 text-center text-muted">
                                <i class="fas fa-info-circle"></i> Genere el reporte para ver el ranking
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL DETALLE CHOFER -->
<div class="modal fade" id="modal_detalle_chofer" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-id-badge"></i> <b>DETALLE DEL CHOFER</b>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenido_detalle_chofer">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Establecer fechas (últimos 3 meses)
    var hoy = new Date();
    var hace3meses = new Date();
    hace3meses.setMonth(hace3meses.getMonth() - 3);
    
    $('#filtro_choferes_fecha_desde').val(hace3meses.toISOString().split('T')[0]);
    $('#filtro_choferes_fecha_hasta').val(hoy.toISOString().split('T')[0]);
});

var tbl_choferes;

function listar_reporte_choferes() {
    let estado = $('#filtro_estado_chofer').val();
    let fecha_desde = $('#filtro_choferes_fecha_desde').val();
    let fecha_hasta = $('#filtro_choferes_fecha_hasta').val();
    
    if (tbl_choferes) {
        tbl_choferes.destroy();
    }
    
    tbl_choferes = $("#tabla_reporte_choferes").DataTable({
        ordering: true,
        order: [[10, 'desc']], // Ordenar por total salidas
        bLengthChange: true,
        searching: true,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        pageLength: 25,
        destroy: true,
        processing: true,
        responsive: true,
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>><"row"<"col-sm-12 text-right"B>>rtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Reporte de Choferes'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                orientation: 'landscape'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-info btn-sm'
            }
        ],
        ajax: {
            url: "../controller/reportes/controller_reportes.php",
            type: "POST",
            data: {
                accion: 'REPORTE_CHOFERES',
                estado: estado,
                fecha_desde: fecha_desde,
                fecha_hasta: fecha_hasta
            },
            dataSrc: function(json) {
                actualizarCardsChoferes(json.data);
                verificarLicenciasPorVencer(json.data);
                mostrarTopChoferes(json.data);
                return json.data;
            }
        },
        columns: [
            { data: 'id_chofer' },
            { data: 'tipo_documen' },
            { data: 'nro_doc' },
            { data: 'nombres_apellidos' },
            { data: 'celular' },
            { data: 'marca_vehiculo' },
            { data: 'placa_vehiculo' },
            { data: 'nro_licencia' },
            {
                data: 'fecha_vencimiento_licencia',
                render: function(data) {
                    if (!data) return '-';
                    
                    let hoy = new Date();
                    let vencimiento = new Date(data);
                    let diasDiff = Math.floor((vencimiento - hoy) / (1000 * 60 * 60 * 24));
                    
                    let clase = '';
                    if (diasDiff < 0) clase = 'text-danger font-weight-bold';
                    else if (diasDiff <= 30) clase = 'text-warning font-weight-bold';
                    else clase = 'text-success';
                    
                    return `<span class="${clase}">${vencimiento.toLocaleDateString('es-PE')}</span>`;
                }
            },
            {
                data: 'estado',
                render: function(data) {
                    if (data == 'ACTIVO')
                        return '<span class="badge badge-success">ACTIVO</span>';
                    return '<span class="badge badge-danger">INACTIVO</span>';
                }
            },
            {
                data: 'total_salidas',
                className: 'text-center',
                render: (data) => `<span class="badge badge-primary">${data || 0}</span>`
            },
            {
                data: 'total_comprobantes',
                className: 'text-center'
            },
            {
                data: 'total_facturado',
                className: 'text-right',
                render: (data) => '<b class="text-success">S/ ' + parseFloat(data || 0).toFixed(2) + '</b>'
            },
            {
                data: null,
                orderable: false,
                render: (data) => `
                    <button class="btn btn-info btn-sm" onclick="verDetalleChofer(${data.id_chofer})">
                        <i class="fas fa-eye"></i>
                    </button>
                `
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
        }
    });
}

function actualizarCardsChoferes(data) {
    let activos = data.filter(c => c.estado === 'ACTIVO').length;
    let inactivos = data.filter(c => c.estado === 'INACTIVO').length;
    let totalSalidas = data.reduce((sum, c) => sum + parseInt(c.total_salidas || 0), 0);
    let totalFacturado = data.reduce((sum, c) => sum + parseFloat(c.total_facturado || 0), 0);
    
    $('#total_choferes_activos').text(activos);
    $('#total_choferes_inactivos').text(inactivos);
    $('#total_salidas_realizadas').text(totalSalidas);
    $('#total_facturado_choferes').text('S/ ' + totalFacturado.toFixed(2));
}

function verificarLicenciasPorVencer(data) {
    let hoy = new Date();
    let licenciasPorVencer = data.filter(c => {
        if (!c.fecha_vencimiento_licencia) return false;
        let vencimiento = new Date(c.fecha_vencimiento_licencia);
        let diasDiff = Math.floor((vencimiento - hoy) / (1000 * 60 * 60 * 24));
        return diasDiff >= 0 && diasDiff <= 30;
    });
    
    if (licenciasPorVencer.length > 0) {
        let html = '<ul class="mb-0">';
        licenciasPorVencer.forEach(c => {
            let vencimiento = new Date(c.fecha_vencimiento_licencia);
            let diasDiff = Math.floor((vencimiento - hoy) / (1000 * 60 * 60 * 24));
            html += `<li><b>${c.nombres_apellidos}</b> - Vence en ${diasDiff} días (${vencimiento.toLocaleDateString('es-PE')})</li>`;
        });
        html += '</ul>';
        
        $('#contenido_licencias_vencer').html(html);
        $('#alerta_licencias').show();
    } else {
        $('#alerta_licencias').hide();
    }
}

function mostrarTopChoferes(data) {
    // Ordenar por total de salidas
    let top10 = data.sort((a, b) => parseInt(b.total_salidas) - parseInt(a.total_salidas)).slice(0, 10);
    
    let html = '';
    top10.forEach((chofer, index) => {
        let medalla = '';
        if (index === 0) medalla = '🥇';
        else if (index === 1) medalla = '🥈';
        else if (index === 2) medalla = '🥉';
        
        html += `
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5>${medalla} ${index + 1}. ${chofer.nombres_apellidos}</h5>
                        <p class="mb-1"><i class="fas fa-bus"></i> <b>${chofer.total_salidas || 0}</b> salidas realizadas</p>
                        <p class="mb-1"><i class="fas fa-file-invoice"></i> ${chofer.total_comprobantes || 0} comprobantes</p>
                        <p class="mb-0"><i class="fas fa-money-bill-wave"></i> Total facturado: <b class="text-success">S/ ${parseFloat(chofer.total_facturado || 0).toFixed(2)}</b></p>
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#contenido_top_choferes').html(html);
}

// 🔧 FUNCIÓN CORREGIDA: Ver Detalle del Chofer
function verDetalleChofer(id) {
    $.ajax({
        url: "../controller/reportes/controller_reportes.php",
        type: "POST",
        data: {
            accion: "OBTENER_DETALLE_CHOFER",
            id_chofer: id
        },
        dataType: "json",
        beforeSend: function() {
            Swal.fire({
                title: 'Cargando...',
                text: 'Obteniendo información del chofer',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(data) {
            Swal.close();
            
            if (data && !data.error) {
                // Calcular días hasta vencimiento de licencia
                let alertaLicencia = '';
                if (data.fecha_vencimiento_licencia) {
                    let hoy = new Date();
                    let vencimiento = new Date(data.fecha_vencimiento_licencia);
                    let diasDiff = Math.floor((vencimiento - hoy) / (1000 * 60 * 60 * 24));
                    
                    if (diasDiff < 0) {
                        alertaLicencia = `<div class="alert alert-danger mt-2">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <b>¡LICENCIA VENCIDA!</b> Venció hace ${Math.abs(diasDiff)} días
                        </div>`;
                    } else if (diasDiff <= 30) {
                        alertaLicencia = `<div class="alert alert-warning mt-2">
                            <i class="fas fa-exclamation-circle"></i> 
                            <b>¡Licencia por vencer!</b> Faltan ${diasDiff} días
                        </div>`;
                    }
                }
                
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-user"></i> <b>Datos Personales</b></h5>
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th width="40%">Tipo Documento:</th>
                                    <td>${data.tipo_documen || '-'}</td>
                                </tr>
                                <tr>
                                    <th>N° Documento:</th>
                                    <td><b>${data.nro_doc || '-'}</b></td>
                                </tr>
                                <tr>
                                    <th>Nombres:</th>
                                    <td>${data.nombres_apellidos || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Celular:</th>
                                    <td>${data.celular || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        ${data.estado === 'ACTIVO' ? 
                                            '<span class="badge badge-success">ACTIVO</span>' : 
                                            '<span class="badge badge-danger">INACTIVO</span>'}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5><i class="fas fa-car"></i> <b>Datos del Vehículo</b></h5>
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th width="40%">Marca:</th>
                                    <td>${data.marca_vehiculo || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Placa:</th>
                                    <td><b>${data.placa_vehiculo || 'SIN PLACA'}</b></td>
                                </tr>
                                <tr>
                                    <th>N° Licencia:</th>
                                    <td>${data.nro_licencia || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Venc. Licencia:</th>
                                    <td>
                                        ${data.fecha_vencimiento_licencia ? 
                                            new Date(data.fecha_vencimiento_licencia).toLocaleDateString('es-PE') : 
                                            '-'}
                                    </td>
                                </tr>
                            </table>
                            ${alertaLicencia}
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon"><i class="fas fa-bus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Salidas</span>
                                    <span class="info-box-number">${data.total_salidas || 0}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-file-invoice"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Comprobantes</span>
                                    <span class="info-box-number">${data.total_comprobantes || 0}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Facturado</span>
                                    <span class="info-box-number">S/ ${parseFloat(data.total_facturado || 0).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#contenido_detalle_chofer').html(html);
                $('#modal_detalle_chofer').modal('show');
                
            } else {
                Swal.fire('Error', data.error || 'No se encontró información del chofer', 'error');
            }
        },
        error: function(xhr) {
            Swal.close();
            console.error('Error:', xhr.responseText);
            Swal.fire('Error', 'No se pudo obtener el detalle del chofer', 'error');
        }
    });
}

function exportarExcelChoferes() {
    tbl_choferes.button('.buttons-excel').trigger();
}

function imprimirReporteChoferes() {
    tbl_choferes.button('.buttons-print').trigger();
}
</script>