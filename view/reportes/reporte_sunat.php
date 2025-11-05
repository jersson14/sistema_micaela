<script src="../js/console_reportes.js?rev=<?php echo time(); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>☁️ REPORTE ESTADO SUNAT</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">ESTADO SUNAT</li>
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
                    <div class="card-header" style="background: linear-gradient(90deg, #17a2b8, #138496);">
                        <h3 class="card-title text-white">
                            <i class="fas fa-filter"></i> <b>Filtros de Búsqueda</b>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Estado SUNAT:</label>
                                <select class="form-control" id="filtro_estado_sunat">
                                    <option value="">Todos</option>
                                    <option value="PENDIENTE">PENDIENTE</option>
                                    <option value="ENVIADO">ENVIADO</option>
                                    <option value="ACEPTADO">ACEPTADO</option>
                                    <option value="RECHAZADO">RECHAZADO</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Fecha Desde:</label>
                                <input type="date" class="form-control" id="filtro_sunat_fecha_desde">
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Fecha Hasta:</label>
                                <input type="date" class="form-control" id="filtro_sunat_fecha_hasta">
                            </div>
                            
                            <div class="col-md-3">
                                <label>&nbsp;</label><br>
                                <button class="btn btn-info btn-block btn-lg" onclick="listar_reporte_sunat()">
                                    <i class="fas fa-cloud-upload-alt"></i> Generar Reporte
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
                <div class="small-box bg-warning">
                    <div class="inner text-white">
                        <h3 id="total_pendientes_sunat">0</h3>
                        <p>Pendientes de Envío</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="total_aceptados_sunat">0</h3>
                        <p>Aceptados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="total_rechazados_sunat">0</h3>
                        <p>Rechazados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="porcentaje_exito">0%</h3>
                        <p>Tasa de Éxito</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-percent"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRÁFICA Y RESUMEN -->
        <div class="row">
            <!-- GRÁFICA DE ESTADOS -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> <b>Distribución por Estado</b></h3>
                    </div>
                    <div class="card-body">
                        <canvas id="grafica_estados_sunat" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- ESTADÍSTICAS ADICIONALES -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title"><i class="fas fa-chart-bar"></i> <b>Estadísticas de Envío</b></h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-file-invoice"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Comprobantes</span>
                                        <span class="info-box-number" id="total_comprobantes_enviados">0</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Monto Total Aceptado</span>
                                        <span class="info-box-number" id="monto_total_aceptado">S/ 0.00</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-info"><i class="fas fa-calendar-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Envíos Hoy</span>
                                        <span class="info-box-number" id="envios_hoy">0</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-hourglass-half"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Tiempo Promedio</span>
                                        <span class="info-box-number" id="tiempo_promedio">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLA DE COMPROBANTES -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> <b>Detalle de Comprobantes Enviados</b>
                        </h3>
                        <div class="card-tools">
                            <button class="btn btn-tool" onclick="exportarExcelSunat()">
                                <i class="fas fa-file-excel text-white"></i> Exportar
                            </button>
                            <button class="btn btn-tool" onclick="refrescarTabla()">
                                <i class="fas fa-sync-alt text-white"></i> Refrescar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla_reporte_sunat" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#17a2b8; color:#FFFFFF;">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tipo</th>
                                        <th>Comprobante</th>
                                        <th>Fecha Emisión</th>
                                        <th>Fecha Envío</th>
                                        <th>Cliente</th>
                                        <th>N° Doc.</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Código Resp.</th>
                                        <th>Descripción</th>
                                        <th>Usuario</th>
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

        <!-- ALERTAS DE RECHAZADOS -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card border-danger" id="card_rechazados" style="display:none;">
                    <div class="card-header bg-danger text-white">
                        <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> <b>Comprobantes Rechazados - Requieren Atención</b></h3>
                    </div>
                    <div class="card-body">
                        <div id="contenido_rechazados"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL VER RESPUESTA SUNAT -->
<div class="modal fade" id="modal_respuesta_sunat" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i> <b>RESPUESTA DE SUNAT</b>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenido_respuesta_sunat">
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
    // Establecer fechas (últimos 7 días)
    var hoy = new Date();
    var hace7dias = new Date();
    hace7dias.setDate(hace7dias.getDate() - 7);
    
    $('#filtro_sunat_fecha_desde').val(hace7dias.toISOString().split('T')[0]);
    $('#filtro_sunat_fecha_hasta').val(hoy.toISOString().split('T')[0]);
    
    // Cargar datos iniciales
    listar_reporte_sunat();
});

var tbl_sunat;

function listar_reporte_sunat() {
    let estado = $('#filtro_estado_sunat').val();
    let fecha_desde = $('#filtro_sunat_fecha_desde').val();
    let fecha_hasta = $('#filtro_sunat_fecha_hasta').val();
    
    if (tbl_sunat) {
        tbl_sunat.destroy();
    }
    
    tbl_sunat = $("#tabla_reporte_sunat").DataTable({
        ordering: true,
        order: [[4, 'desc']], // Ordenar por fecha de envío
        bLengthChange: true,
        searching: true,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
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
                title: 'Reporte Estado SUNAT'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                orientation: 'landscape'
            }
        ],
        ajax: {
            url: "../controller/reportes/controller_reportes.php",
            type: "POST",
            data: {
                accion: 'REPORTE_ESTADO_SUNAT',
                estado: estado,
                fecha_desde: fecha_desde,
                fecha_hasta: fecha_hasta
            },
            dataSrc: function(json) {
                actualizarCardsSunat(json.data);
                mostrarGraficaSunat(json.data);
                mostrarRechazados(json.data);
                return json.data;
            }
        },
        columns: [
            { data: 'id_comprobante' },
            {
                data: 'tipo_comprobante',
                render: function(data) {
                    if (data == '01') return '<span class="badge badge-info">FAC</span>';
                    if (data == '03') return '<span class="badge badge-primary">BOL</span>';
                    if (data == '07') return '<span class="badge badge-warning">NC</span>';
                    if (data == '08') return '<span class="badge badge-secondary">ND</span>';
                    return data;
                }
            },
            { data: 'numero_comprobante' },
            {
                data: 'fecha_emision',
                render: (data) => new Date(data).toLocaleDateString('es-PE')
            },
            {
                data: 'fecha_envio_sunat',
                render: function(data) {
                    if (!data) return '-';
                    let fecha = new Date(data);
                    return fecha.toLocaleString('es-PE');
                }
            },
            { data: 'razon_social' },
            { data: 'numero_documento' },
            {
                data: 'total',
                render: (data) => 'S/ ' + parseFloat(data).toFixed(2)
            },
            {
                data: 'estado_sunat',
                render: function(data) {
                    if (data == 'PENDIENTE')
                        return '<span class="badge badge-warning"><i class="fas fa-clock"></i> PENDIENTE</span>';
                    if (data == 'ACEPTADO' || data == 'ENVIADO')
                        return '<span class="badge badge-success"><i class="fas fa-check"></i> ACEPTADO</span>';
                    if (data == 'RECHAZADO')
                        return '<span class="badge badge-danger"><i class="fas fa-times"></i> RECHAZADO</span>';
                    return '<span class="badge badge-secondary">' + data + '</span>';
                }
            },
            { data: 'codigo_respuesta_sunat' },
            {
                data: 'descripcion_respuesta_sunat',
                render: (data) => data ? '<small>' + data.substring(0, 50) + '...</small>' : '-'
            },
            { data: 'usuario_emisor' },
            {
                data: null,
                orderable: false,
                render: (data) => `
                    <button class="btn btn-info btn-sm" onclick="verRespuestaSunat(${data.id_comprobante})" title="Ver Respuesta">
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

function actualizarCardsSunat(data) {
    let pendientes = data.filter(c => c.estado_sunat === 'PENDIENTE').length;
    let aceptados = data.filter(c => c.estado_sunat === 'ACEPTADO' || c.estado_sunat === 'ENVIADO').length;
    let rechazados = data.filter(c => c.estado_sunat === 'RECHAZADO').length;
    
    let total = data.length;
    let tasaExito = total > 0 ? ((aceptados / total) * 100).toFixed(1) : 0;
    
    $('#total_pendientes_sunat').text(pendientes);
    $('#total_aceptados_sunat').text(aceptados);
    $('#total_rechazados_sunat').text(rechazados);
    $('#porcentaje_exito').text(tasaExito + '%');
    
    // Calcular estadísticas adicionales
    let montoAceptado = data
        .filter(c => c.estado_sunat === 'ACEPTADO' || c.estado_sunat === 'ENVIADO')
        .reduce((sum, c) => sum + parseFloat(c.total), 0);
    
    let enviosHoy = data.filter(c => {
        if (!c.fecha_envio_sunat) return false;
        let hoy = new Date().toDateString();
        let envio = new Date(c.fecha_envio_sunat).toDateString();
        return hoy === envio;
    }).length;
    
    $('#total_comprobantes_enviados').text(data.length);
    $('#monto_total_aceptado').text('S/ ' + montoAceptado.toFixed(2));
    $('#envios_hoy').text(enviosHoy);
}

function mostrarGraficaSunat(data) {
    const ctx = document.getElementById('grafica_estados_sunat').getContext('2d');
    
    if (window.chartSunat) {
        window.chartSunat.destroy();
    }
    
    let pendientes = data.filter(c => c.estado_sunat === 'PENDIENTE').length;
    let aceptados = data.filter(c => c.estado_sunat === 'ACEPTADO' || c.estado_sunat === 'ENVIADO').length;
    let rechazados = data.filter(c => c.estado_sunat === 'RECHAZADO').length;
    
    window.chartSunat = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Aceptados', 'Pendientes', 'Rechazados'],
            datasets: [{
                data: [aceptados, pendientes, rechazados],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function mostrarRechazados(data) {
    let rechazados = data.filter(c => c.estado_sunat === 'RECHAZADO');
    
    if (rechazados.length > 0) {
        let html = '<ul>';
        rechazados.forEach(c => {
            html += `
                <li class="mb-2">
                    <b>${c.numero_comprobante}</b> - ${c.razon_social}<br>
                    <small class="text-danger">
                        <i class="fas fa-info-circle"></i> Código: ${c.codigo_respuesta_sunat || 'N/A'} - 
                        ${c.descripcion_respuesta_sunat || 'Sin descripción'}
                    </small>
                </li>
            `;
        });
        html += '</ul>';
        
        $('#contenido_rechazados').html(html);
        $('#card_rechazados').show();
    } else {
        $('#card_rechazados').hide();
    }
}

function verRespuestaSunat(id) {
    Swal.fire({
        title: 'Cargando...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: "../controller/comprobante/controller_comprobante.php",
        type: "POST",
        data: {
            accion: "OBTENER_COMPROBANTE",
            id_comprobante: id
        },
        dataType: "json",
        success: function(data) {
            Swal.close();
            
            if (data) {
                let html = `
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert ${data.estado_sunat === 'ACEPTADO' ? 'alert-success' : 'alert-danger'}">
                                <h5><i class="fas fa-info-circle"></i> Estado SUNAT: <b>${data.estado_sunat}</b></h5>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6><strong>Comprobante:</strong></h6>
                            <p>${data.numero_comprobante}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6><strong>Fecha Emisión:</strong></h6>
                            <p>${data.fecha_emision}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6><strong>Cliente:</strong></h6>
                            <p>${data.razon_social}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6><strong>Total:</strong></h6>
                            <p class="text-success"><strong>S/ ${parseFloat(data.total).toFixed(2)}</strong></p>
                        </div>
                        
                        <div class="col-md-12 mt-3">
                            <h6><strong>Respuesta de SUNAT:</strong></h6>
                            <div class="card">
                                <div class="card-body">
                                    <p><strong>Código:</strong> ${data.codigo_respuesta_sunat || 'N/A'}</p>
                                    <p><strong>Descripción:</strong></p>
                                    <p class="mb-0">${data.descripcion_respuesta_sunat || 'Sin descripción disponible'}</p>
                                </div>
                            </div>
                        </div>
                        
                        ${data.fecha_envio_sunat ? `
                        <div class="col-md-12 mt-3">
                            <p class="text-muted mb-0">
                                <i class="far fa-clock"></i> Enviado: ${new Date(data.fecha_envio_sunat).toLocaleString('es-PE')}
                            </p>
                        </div>
                        ` : ''}
                    </div>
                `;
                
                $('#contenido_respuesta_sunat').html(html);
                $('#modal_respuesta_sunat').modal('show');
            } else {
                Swal.fire('Error', 'No se encontró información del comprobante', 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudo obtener la información: ' + error, 'error');
        }
    });
}

function exportarExcelSunat() {
    tbl_sunat.button('.buttons-excel').trigger();
}

function refrescarTabla() {
    listar_reporte_sunat();
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Tabla actualizada',
        showConfirmButton: false,
        timer: 1500
    });
}
// 🔥 FUNCIÓN CORREGIDA: Actualizar Cards SUNAT
function actualizarCardsSunat(data) {
    // Contar estados
    let pendientes = data.filter(c => c.estado_sunat === 'PENDIENTE').length;
    let aceptados = data.filter(c => c.estado_sunat === 'ACEPTADO' || c.estado_sunat === 'ENVIADO').length;
    let rechazados = data.filter(c => c.estado_sunat === 'RECHAZADO').length;
    
    // Calcular tasa de éxito
    let total = data.length;
    let tasaExito = total > 0 ? ((aceptados / total) * 100).toFixed(1) : 0;
    
    $('#total_pendientes_sunat').text(pendientes);
    $('#total_aceptados_sunat').text(aceptados);
    $('#total_rechazados_sunat').text(rechazados);
    $('#porcentaje_exito').text(tasaExito + '%');
    
    // ===================================
    // 🔥 MONTO TOTAL ACEPTADO
    // ===================================
    let montoAceptado = data
        .filter(c => c.estado_sunat === 'ACEPTADO' || c.estado_sunat === 'ENVIADO')
        .reduce((sum, c) => sum + parseFloat(c.total || 0), 0);
    
    $('#monto_total_aceptado').text('S/ ' + montoAceptado.toFixed(2));
    $('#total_comprobantes_enviados').text(data.length);
    
    // ===================================
    // 🔥 ENVÍOS HOY (CORREGIDO)
    // ===================================
    let hoy = new Date();
    let hoyStr = hoy.toISOString().split('T')[0]; // Formato: YYYY-MM-DD
    
    let enviosHoy = data.filter(c => {
        if (!c.fecha_envio_sunat) return false;
        
        // Extraer solo la fecha (sin hora) de fecha_envio_sunat
        let fechaEnvio = c.fecha_envio_sunat.split(' ')[0]; // Si viene como "2025-11-05 14:30:00"
        
        // Si no tiene espacio, intentar con el formato ISO
        if (fechaEnvio.includes('T')) {
            fechaEnvio = fechaEnvio.split('T')[0]; // Si viene como "2025-11-05T14:30:00"
        }
        
        return fechaEnvio === hoyStr;
    }).length;
    
    $('#envios_hoy').text(enviosHoy);
    
    // ===================================
    // 🔥 TIEMPO PROMEDIO (NUEVO)
    // ===================================
    let comprobantesConTiempo = data.filter(c => 
        c.fecha_envio_sunat && 
        c.fecha_emision &&
        (c.estado_sunat === 'ACEPTADO' || c.estado_sunat === 'ENVIADO')
    );
    
    if (comprobantesConTiempo.length > 0) {
        let tiemposTotales = 0;
        
        comprobantesConTiempo.forEach(c => {
            try {
                // Parsear fechas
                let fechaEmision = new Date(c.fecha_emision);
                let fechaEnvio = new Date(c.fecha_envio_sunat);
                
                // Calcular diferencia en milisegundos
                let diferenciaMilisegundos = fechaEnvio - fechaEmision;
                
                // Convertir a minutos
                let diferenciaMinutos = diferenciaMilisegundos / (1000 * 60);
                
                // Solo contar si es positivo (envío después de emisión)
                if (diferenciaMinutos > 0) {
                    tiemposTotales += diferenciaMinutos;
                }
            } catch (error) {
                console.error('Error al calcular tiempo:', error);
            }
        });
        
        let tiempoPromedio = tiemposTotales / comprobantesConTiempo.length;
        
        // Formatear el tiempo promedio
        let textoTiempo = '';
        
        if (tiempoPromedio < 60) {
            // Menos de 1 hora - mostrar en minutos
            textoTiempo = Math.round(tiempoPromedio) + ' min';
        } else if (tiempoPromedio < 1440) {
            // Menos de 1 día - mostrar en horas
            let horas = Math.floor(tiempoPromedio / 60);
            let minutos = Math.round(tiempoPromedio % 60);
            textoTiempo = horas + 'h ' + minutos + 'm';
        } else {
            // Más de 1 día - mostrar en días
            let dias = Math.floor(tiempoPromedio / 1440);
            let horas = Math.floor((tiempoPromedio % 1440) / 60);
            textoTiempo = dias + 'd ' + horas + 'h';
        }
        
        $('#tiempo_promedio').text(textoTiempo);
        
    } else {
        $('#tiempo_promedio').text('N/A');
    }
    
    // ===================================
    // 🔥 LOG PARA DEBUG (opcional)
    // ===================================
    console.log('📊 Estadísticas SUNAT:', {
        total: total,
        pendientes: pendientes,
        aceptados: aceptados,
        rechazados: rechazados,
        tasaExito: tasaExito + '%',
        montoAceptado: 'S/ ' + montoAceptado.toFixed(2),
        enviosHoy: enviosHoy,
        tiempoPromedio: $('#tiempo_promedio').text()
    });
}
</script>

<style>
.info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: .25rem;
    margin-bottom: 1rem;
}

.info-box-icon {
    border-radius: .25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 70px;
}
</style>