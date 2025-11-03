<script src="../js/console_reportes.js?rev=<?php echo time(); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>👥 REPORTE DE CLIENTES</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">REPORTE CLIENTES</li>
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
                    <div class="card-header" style="background: linear-gradient(90deg, #6f42c1, #563d7c);">
                        <h3 class="card-title text-white">
                            <i class="fas fa-filter"></i> <b>Filtros de Búsqueda</b>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Tipo de Cliente:</label>
                                <select class="form-control" id="filtro_tipo_cliente">
                                    <option value="todos">Todos</option>
                                    <option value="frecuentes">Clientes Frecuentes (5+ viajes)</option>
                                    <option value="nuevos">Clientes Nuevos (≤2 viajes)</option>
                                    <option value="inactivos">Clientes Inactivos (+30 días)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Fecha Desde:</label>
                                <input type="date" class="form-control" id="filtro_clientes_fecha_desde">
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Fecha Hasta:</label>
                                <input type="date" class="form-control" id="filtro_clientes_fecha_hasta">
                            </div>
                            
                            <div class="col-md-3">
                                <label>&nbsp;</label><br>
                                <button class="btn btn-primary btn-block btn-lg" onclick="listar_reporte_clientes()">
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
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total_clientes_registrados">0</h3>
                        <p>Clientes Registrados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="clientes_frecuentes">0</h3>
                        <p>Clientes Frecuentes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner text-white">
                        <h3 id="clientes_nuevos">0</h3>
                        <p>Clientes Nuevos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="clientes_inactivos">0</h3>
                        <p>Clientes Inactivos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRÁFICA Y TOP CLIENTES -->
        <div class="row">
            <!-- GRÁFICA -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> <b>Distribución de Clientes</b></h3>
                    </div>
                    <div class="card-body">
                        <canvas id="grafica_clientes" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- TOP 10 CLIENTES -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title"><i class="fas fa-trophy"></i> <b>Top 10 Mejores Clientes</b></h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Pos.</th>
                                        <th>Cliente</th>
                                        <th class="text-center">Viajes</th>
                                        <th class="text-right">Total Gastado</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_top_clientes">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> Genere el reporte para ver datos
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLA COMPLETA DE CLIENTES -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> <b>Listado Completo de Clientes</b>
                        </h3>
                        <div class="card-tools">
                            <button class="btn btn-tool" onclick="exportarExcelClientes()">
                                <i class="fas fa-file-excel text-white"></i> Exportar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla_reporte_clientes" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#6f42c1; color:#FFFFFF;">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tipo Doc.</th>
                                        <th>N° Documento</th>
                                        <th>Nombre Completo</th>
                                        <th>Celular</th>
                                        <th>Email</th>
                                        <th>Procedencia</th>
                                        <th>Total Viajes</th>
                                        <th>Último Viaje</th>
                                        <th>Comprobantes</th>
                                        <th>Total Gastado</th>
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

    </div>
</div>

<!-- MODAL DETALLE CLIENTE -->
<div class="modal fade" id="modal_detalle_cliente" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user"></i> <b>DETALLE DEL CLIENTE</b>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenido_detalle_cliente">
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
    // Verificar que Chart.js esté cargado
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js no está cargado. Incluye el script en tu HTML.');
        Swal.fire({
            icon: 'warning',
            title: 'Librería no encontrada',
            text: 'Chart.js no está cargado. Las gráficas no se mostrarán.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }
    
    // Establecer fechas (últimos 6 meses)
    var hoy = new Date();
    var hace6meses = new Date();
    hace6meses.setMonth(hace6meses.getMonth() - 6);
    
    $('#filtro_clientes_fecha_desde').val(hace6meses.toISOString().split('T')[0]);
    $('#filtro_clientes_fecha_hasta').val(hoy.toISOString().split('T')[0]);
});

var tbl_clientes;

function listar_reporte_clientes() {
    let tipo_filtro = $('#filtro_tipo_cliente').val();
    let fecha_desde = $('#filtro_clientes_fecha_desde').val();
    let fecha_hasta = $('#filtro_clientes_fecha_hasta').val();
    
    if (tbl_clientes) {
        tbl_clientes.destroy();
    }
    
    tbl_clientes = $("#tabla_reporte_clientes").DataTable({
        ordering: true,
        order: [[10, 'desc']], // Ordenar por total gastado
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
                title: 'Reporte de Clientes'
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
                accion: 'REPORTE_CLIENTES',
                tipo_filtro: tipo_filtro,
                fecha_desde: fecha_desde,
                fecha_hasta: fecha_hasta
            },
            dataSrc: function(json) {
                console.log("📊 Respuesta del servidor (Clientes):", json);
                
                if (json.error) {
                    console.error("❌ Error:", json.error);
                    Swal.fire('Error', json.error, 'error');
                    return [];
                }
                
                if (!json.data || !Array.isArray(json.data)) {
                    console.error("❌ Formato de datos inválido");
                    return [];
                }
                
                actualizarCardsClientes(json.data);
                actualizarTopClientes(json.data);
                return json.data;
            },
            error: function(xhr, error, thrown) {
                console.error("❌ Error AJAX:", error);
                console.error("Respuesta del servidor:", xhr.responseText);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error en la petición',
                    html: '<p>No se pudo cargar los datos</p><pre>' + xhr.responseText.substring(0, 500) + '</pre>',
                    width: '600px'
                });
            }
        },
        columns: [
            { data: 'id_cliente' },
            { data: 'tipo_documento' },
            { data: 'nro_documento' },
            { data: 'nombre_completo' },
            { data: 'celular' },
            { data: 'email' },
            { data: 'procedencia' },
            {
                data: 'total_viajes',
                className: 'text-center',
                render: function(data) {
                    let badge = 'secondary';
                    if (data >= 10) badge = 'success';
                    else if (data >= 5) badge = 'primary';
                    else if (data >= 2) badge = 'info';
                    return `<span class="badge badge-${badge}">${data || 0}</span>`;
                }
            },
            {
                data: 'ultimo_viaje',
                render: (data) => data ? new Date(data).toLocaleDateString('es-PE') : '-'
            },
            {
                data: 'comprobantes_emitidos',
                className: 'text-center'
            },
            {
                data: 'total_gastado',
                className: 'text-right',
                render: (data) => '<b>S/ ' + parseFloat(data || 0).toFixed(2) + '</b>'
            },
            {
                data: null,
                orderable: false,
                render: (data) => `
                    <button class="btn btn-info btn-sm" onclick="verDetalleCliente(${data.id_cliente})">
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

function actualizarCardsClientes(data) {
    $('#total_clientes_registrados').text(data.length);
    
    let frecuentes = data.filter(c => c.total_viajes >= 5).length;
    let nuevos = data.filter(c => c.total_viajes <= 2).length;
    
    // Calcular inactivos (más de 30 días sin viaje)
    let hoy = new Date();
    let inactivos = data.filter(c => {
        if (!c.ultimo_viaje) return false;
        let ultimoViaje = new Date(c.ultimo_viaje);
        let diasDiff = Math.floor((hoy - ultimoViaje) / (1000 * 60 * 60 * 24));
        return diasDiff > 30;
    }).length;
    
    $('#clientes_frecuentes').text(frecuentes);
    $('#clientes_nuevos').text(nuevos);
    $('#clientes_inactivos').text(inactivos);
    
    // 🔥 CREAR GRÁFICA DE DISTRIBUCIÓN
    mostrarGraficaDistribucionClientes(frecuentes, nuevos, inactivos, data.length);
}

// 🔥 NUEVA FUNCIÓN: Gráfica de Distribución de Clientes
function mostrarGraficaDistribucionClientes(frecuentes, nuevos, inactivos, total) {
    const ctx = document.getElementById('grafica_clientes');
    
    if (!ctx) {
        console.error('Canvas grafica_clientes no encontrado');
        return;
    }
    
    const ctxContext = ctx.getContext('2d');
    
    // Destruir gráfica anterior si existe
    if (window.chartDistribucionClientes) {
        window.chartDistribucionClientes.destroy();
    }
    
    let ocasionales = total - frecuentes - nuevos - inactivos;
    if (ocasionales < 0) ocasionales = 0;
    
    window.chartDistribucionClientes = new Chart(ctxContext, {
        type: 'doughnut',
        data: {
            labels: ['Frecuentes (5+ viajes)', 'Nuevos (≤2 viajes)', 'Inactivos (+30 días)', 'Ocasionales'],
            datasets: [{
                data: [frecuentes, nuevos, inactivos, ocasionales],
                backgroundColor: [
                    '#28a745', // Verde - Frecuentes
                    '#ffc107', // Amarillo - Nuevos
                    '#dc3545', // Rojo - Inactivos
                    '#17a2b8'  // Azul - Ocasionales
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Distribución de Clientes por Tipo',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function actualizarTopClientes(data) {
    // Ordenar por total gastado
    let top10 = data.sort((a, b) => parseFloat(b.total_gastado) - parseFloat(a.total_gastado)).slice(0, 10);
    
    let html = '';
    top10.forEach((cliente, index) => {
        let medalla = '';
        if (index === 0) medalla = '🥇';
        else if (index === 1) medalla = '🥈';
        else if (index === 2) medalla = '🥉';
        
        html += `
            <tr>
                <td class="text-center">${medalla} ${index + 1}</td>
                <td>${cliente.nombre_completo}</td>
                <td class="text-center"><span class="badge badge-primary">${cliente.total_viajes || 0}</span></td>
                <td class="text-right"><b class="text-success">S/ ${parseFloat(cliente.total_gastado || 0).toFixed(2)}</b></td>
            </tr>
        `;
    });
    
    $('#tbody_top_clientes').html(html);
}

function verDetalleCliente(id) {
    Swal.fire('Info', 'Función en desarrollo', 'info');
}

function exportarExcelClientes() {
    tbl_clientes.button('.buttons-excel').trigger();
}
</script>