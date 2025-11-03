<script src="../js/console_reportes.js?rev=<?php echo time(); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>🚗 REPORTE SALIDAS DIARIAS</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">SALIDAS DIARIAS</li>
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
                    <div class="card-header" style="background: linear-gradient(90deg, #fd7e14, #ffc107);">
                        <h3 class="card-title text-white">
                            <i class="fas fa-filter"></i> <b>Filtros de Búsqueda</b>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Fecha Desde <b style="color:red">(*)</b>:</label>
                                <input type="date" class="form-control" id="filtro_salida_fecha_desde">
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Fecha Hasta <b style="color:red">(*)</b>:</label>
                                <input type="date" class="form-control" id="filtro_salida_fecha_hasta">
                            </div>

                            <div class="col-md-3 form-group">
                                <label>Chofer:</label>
                                <select class="form-control select2" id="filtro_chofer" style="width: 100%;">
                                    <option value="">TODOS</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label>&nbsp;</label><br>
                                <button class="btn btn-primary btn-block btn-lg" onclick="listar_salidas_diarias()">
                                    <i class="fas fa-search"></i> Buscar Salidas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- INDICADORES -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3 id="total_salidas">0</h3>
                        <p>Total Salidas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-car"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="total_facturado_salidas">S/ 0.00</h3>
                        <p>Total Facturado</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner text-white">
                        <h3 id="promedio_salida">S/ 0.00</h3>
                        <p>Promedio por Salida</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total_choferes">0</h3>
                        <p>Choferes Activos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRÁFICAS -->
        <div class="row">
            <!-- GRÁFICA TEMPORAL -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title"><i class="fas fa-chart-line"></i> <b>Salidas por Día</b></h3>
                    </div>
                    <div class="card-body">
                        <canvas id="grafica_salidas_tiempo" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- GRÁFICA POR CHOFER -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> <b>Por Chofer</b></h3>
                    </div>
                    <div class="card-body">
                        <canvas id="grafica_salidas_chofer" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLA DETALLADA -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h3 class="card-title"><i class="fas fa-table"></i> <b>Detalle de Salidas</b></h3>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-success" onclick="exportarExcelSalidas()">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="exportarPDFSalidas()">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla_salidas_diarias" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead style="background-color:#343a40; color:#FFFFFF;">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Chofer</th>
                                        <th>Placa</th>
                                        <th>Cliente</th>
                                        <th>Origen</th>
                                        <th>Destino</th>
                                        <th>Servicio</th>
                                        <th class="text-right">Monto</th>
                                        <th>Comprobante</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se cargarán dinámicamente -->
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                                        <td colspan="9" class="text-right">TOTAL:</td>
                                        <td class="text-right" id="footer_total">S/ 0.00</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RESUMEN POR CHOFER -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h3 class="card-title"><i class="fas fa-user-tie"></i> <b>Resumen por Chofer</b></h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th>Chofer</th>
                                        <th class="text-center">N° Salidas</th>
                                        <th class="text-right">Total Facturado</th>
                                        <th class="text-right">Promedio</th>
                                        <th class="text-center">% del Total</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_resumen_choferes">
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle"></i> Genere el reporte para ver el resumen
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL VER DETALLE SALIDA -->
<div class="modal fade" id="modal_detalle_salida">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title"><i class="fas fa-info-circle"></i> Detalle de Salida</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="contenido_detalle_salida">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
var tbl_salidas_diarias;

$(document).ready(function() {
    // Fechas por defecto (hoy)
    var hoy = new Date().toISOString().split('T')[0];
    $('#filtro_salida_fecha_desde').val(hoy);
    $('#filtro_salida_fecha_hasta').val(hoy);
    
    // Cargar choferes
    cargarChoferes();
    
    // Inicializar select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Seleccione...'
    });
});

function cargarChoferes() {
    $.ajax({
        url: "../controller/chofer/controller_chofer.php",
        type: "POST",
        data: { accion: 'LISTAR_CHOFERES_COMBO' },
        dataType: "json",
        success: function(data) {
            let html = '<option value="">TODOS</option>';
            data.forEach(function(chofer) {
                html += `<option value="${chofer.id_chofer}">${chofer.nombre_completo}</option>`;
            });
            $('#filtro_chofer').html(html);
        }
    });
}

function listar_salidas_diarias() {
    let fecha_desde = $('#filtro_salida_fecha_desde').val();
    let fecha_hasta = $('#filtro_salida_fecha_hasta').val();
    let id_chofer = $('#filtro_chofer').val();
    
    if (!fecha_desde || !fecha_hasta) {
        return Swal.fire('Advertencia', 'Seleccione rango de fechas', 'warning');
    }

    if (tbl_salidas_diarias) {
        tbl_salidas_diarias.destroy();
    }

    tbl_salidas_diarias = $("#tabla_salidas_diarias").DataTable({
        ordering: true,
        order: [[1, 'desc'], [2, 'desc']],
        bLengthChange: true,
        searching: true,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        pageLength: 25,
        destroy: true,
        processing: true,
        responsive: true,
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
        ajax: {
            url: "../controller/reportes/controller_reportes.php",
            type: "POST",
            data: {
                accion: 'REPORTE_SALIDAS_DIARIAS',
                fecha_desde: fecha_desde,
                fecha_hasta: fecha_hasta,
                id_chofer: id_chofer
            },
            dataSrc: function(json) {
                if (json.status === 'success') {
                    actualizarIndicadoresSalidas(json.data);
                    generarGraficasSalidas(json.data);
                    generarResumenChoferes(json.data);
                    return json.data;
                }
                return [];
            }
        },
        columns: [
            { data: 'id_salida' },
            {
                data: 'fecha_salida',
                render: function(data) {
                    if (!data) return '-';
                    const partes = data.split('-');
                    return `${partes[2]}/${partes[1]}/${partes[0]}`;
                }
            },
            { data: 'hora_salida' },
            { data: 'chofer_nombre' },
            {
                data: 'placa_vehiculo',
                render: (data) => `<span class="badge badge-dark">${data}</span>`
            },
            { data: 'cliente_nombre' },
            { data: 'origen' },
            { data: 'destino' },
            { data: 'servicio_nombre' },
            {
                data: 'monto',
                className: 'text-right',
                render: (data) => '<b>S/ ' + parseFloat(data).toFixed(2) + '</b>'
            },
            {
                data: 'numero_comprobante',
                render: function(data) {
                    return data ? `<span class="badge badge-info">${data}</span>` : '-';
                }
            },
            {
                data: 'estado',
                render: function(data) {
                    if (data == 'COMPLETADO')
                        return '<span class="badge badge-success"><i class="fas fa-check"></i> COMPLETADO</span>';
                    if (data == 'EN CURSO')
                        return '<span class="badge badge-warning"><i class="fas fa-spinner"></i> EN CURSO</span>';
                    if (data == 'CANCELADO')
                        return '<span class="badge badge-danger"><i class="fas fa-times"></i> CANCELADO</span>';
                    return '<span class="badge badge-secondary">' + data + '</span>';
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function(data) {
                    return `
                        <button class="btn btn-info btn-sm" onclick="verDetalleSalida(${data.id_salida})" title="Ver Detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                    `;
                }
            }
        ],
        footerCallback: function(row, data, start, end, display) {
            let api = this.api();
            let total = api.column(9, { page: 'current' }).data().reduce(function(a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0);
            $('#footer_total').html('S/ ' + total.toFixed(2));
        },
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
        }
    });
}

function actualizarIndicadoresSalidas(data) {
    let total_salidas = data.length;
    let total_facturado = data.reduce((sum, item) => sum + parseFloat(item.monto), 0);
    let promedio = total_salidas > 0 ? total_facturado / total_salidas : 0;
    let choferes_unicos = [...new Set(data.map(item => item.id_chofer))].length;
    
    $('#total_salidas').text(total_salidas);
    $('#total_facturado_salidas').text('S/ ' + total_facturado.toFixed(2));
    $('#promedio_salida').text('S/ ' + promedio.toFixed(2));
    $('#total_choferes').text(choferes_unicos);
}

function generarGraficasSalidas(data) {
    // Agrupar por fecha
    let salidasPorFecha = {};
    data.forEach(item => {
        let fecha = item.fecha_salida;
        if (!salidasPorFecha[fecha]) {
            salidasPorFecha[fecha] = { cantidad: 0, monto: 0 };
        }
        salidasPorFecha[fecha].cantidad++;
        salidasPorFecha[fecha].monto += parseFloat(item.monto);
    });
    
    // Gráfica temporal
    const ctx1 = document.getElementById('grafica_salidas_tiempo').getContext('2d');
    if (window.chartSalidasTiempo) window.chartSalidasTiempo.destroy();
    
    window.chartSalidasTiempo = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: Object.keys(salidasPorFecha).map(f => {
                const p = f.split('-');
                return `${p[2]}/${p[1]}`;
            }),
            datasets: [{
                label: 'Cantidad de Salidas',
                data: Object.values(salidasPorFecha).map(v => v.cantidad),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4,
                yAxisID: 'y'
            }, {
                label: 'Monto (S/)',
                data: Object.values(salidasPorFecha).map(v => v.monto),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left'
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
    
    // Gráfica por chofer
    let salidasPorChofer = {};
    data.forEach(item => {
        let chofer = item.chofer_nombre;
        if (!salidasPorChofer[chofer]) {
            salidasPorChofer[chofer] = 0;
        }
        salidasPorChofer[chofer]++;
    });
    
    const ctx2 = document.getElementById('grafica_salidas_chofer').getContext('2d');
    if (window.chartSalidasChofer) window.chartSalidasChofer.destroy();
    
    window.chartSalidasChofer = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: Object.keys(salidasPorChofer),
            datasets: [{
                data: Object.values(salidasPorChofer),
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                    '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                ]
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

function generarResumenChoferes(data) {
    let resumen = {};
    let total_general = 0;
    
    data.forEach(item => {
        let chofer = item.chofer_nombre;
        let monto = parseFloat(item.monto);
        
        if (!resumen[chofer]) {
            resumen[chofer] = { cantidad: 0, total: 0 };
        }
        resumen[chofer].cantidad++;
        resumen[chofer].total += monto;
        total_general += monto;
    });
    
    let html = '';
    Object.keys(resumen).sort((a, b) => resumen[b].total - resumen[a].total).forEach(chofer => {
        let datos = resumen[chofer];
        let promedio = datos.total / datos.cantidad;
        let porcentaje = (datos.total / total_general * 100).toFixed(1);
        
        html += `
            <tr>
                <td><i class="fas fa-user text-primary"></i> ${chofer}</td>
                <td class="text-center"><span class="badge badge-info">${datos.cantidad}</span></td>
                <td class="text-right"><b>S/ ${datos.total.toFixed(2)}</b></td>
                <td class="text-right">S/ ${promedio.toFixed(2)}</td>
                <td class="text-center">
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" style="width: ${porcentaje}%">
                            ${porcentaje}%
                        </div>
                    </div>
                </td>
            </tr>
        `;
    });
    
    $('#tbody_resumen_choferes').html(html);
}

function verDetalleSalida(id) {
    $.ajax({
        url: "../controller/salida/controller_salida.php",
        type: "POST",
        data: {
            accion: "OBTENER_SALIDA",
            id_salida: id
        },
        dataType: "json",
        success: function(data) {
            if (data) {
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-calendar"></i> Información General</h5>
                            <table class="table table-sm">
                                <tr><th>Fecha:</th><td>${data.fecha_salida}</td></tr>
                                <tr><th>Hora:</th><td>${data.hora_salida}</td></tr>
                                <tr><th>Estado:</th><td>${data.estado}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-user"></i> Personal y Vehículo</h5>
                            <table class="table table-sm">
                                <tr><th>Chofer:</th><td>${data.chofer_nombre}</td></tr>
                                <tr><th>Placa:</th><td>${data.placa_vehiculo}</td></tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h5><i class="fas fa-route"></i> Ruta</h5>
                            <table class="table table-sm">
                                <tr><th>Origen:</th><td>${data.origen}</td></tr>
                                <tr><th>Destino:</th><td>${data.destino}</td></tr>
                                <tr><th>Distancia:</th><td>${data.distancia || '-'}</td></tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-user-tie"></i> Cliente</h5>
                            <table class="table table-sm">
                                <tr><th>Nombre:</th><td>${data.cliente_nombre}</td></tr>
                                <tr><th>Documento:</th><td>${data.cliente_documento}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-money-bill"></i> Facturación</h5>
                            <table class="table table-sm">
                                <tr><th>Servicio:</th><td>${data.servicio_nombre}</td></tr>
                                <tr><th>Monto:</th><td><b class="text-success">S/ ${parseFloat(data.monto).toFixed(2)}</b></td></tr>
                                <tr><th>Comprobante:</th><td>${data.numero_comprobante || '-'}</td></tr>
                            </table>
                        </div>
                    </div>
                `;
                
                $('#contenido_detalle_salida').html(html);
                $('#modal_detalle_salida').modal('show');
            }
        }
    });
}

function exportarExcelSalidas() {
    if (tbl_salidas_diarias) {
        // Crear botón temporal de exportación
        let btn = $('<button>').appendTo('body').hide();
        let dt = tbl_salidas_diarias;
        
        $.fn.dataTable.ext.buttons.excelHtml5.action.call(dt, null, dt, btn, {
            title: 'Reporte Salidas Diarias',
            filename: 'Salidas_' + new Date().toISOString().slice(0, 10)
        });
        
        btn.remove();
    } else {
        Swal.fire('Advertencia', 'Primero genere el reporte', 'warning');
    }
}

function exportarPDFSalidas() {
    Swal.fire('Información', 'Función en desarrollo', 'info');
}
</script>

<style>
.small-box {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.small-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.small-box .inner {
    padding: 15px;
}

.small-box .icon {
    font-size: 70px;
    position: absolute;
    right: 15px;
    top: 15px;
    opacity: 0.3;
}

.small-box h3 {
    font-size: 2rem;
    font-weight: bold;
    margin: 0;
}
</style>