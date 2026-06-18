<script src="../js/console_reportes.js?rev=<?php echo time(); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>🔧 REPORTE SERVICIOS PRESTADOS</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">SERVICIOS PRESTADOS</li>
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
                    <div class="card-header" style="background: linear-gradient(90deg, #6f42c1, #9561e2);">
                        <h3 class="card-title text-white">
                            <i class="fas fa-filter"></i> <b>Filtros de Búsqueda</b>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Fecha Desde <b style="color:red">(*)</b>:</label>
                                <input type="date" class="form-control" id="filtro_servicio_fecha_desde">
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label>Fecha Hasta <b style="color:red">(*)</b>:</label>
                                <input type="date" class="form-control" id="filtro_servicio_fecha_hasta">
                            </div>
                            
                            <div class="col-md-4">
                                <label>&nbsp;</label><br>
                                <button class="btn btn-primary btn-block btn-lg" onclick="listar_servicios_prestados()">
                                    <i class="fas fa-chart-pie"></i> Generar Reporte
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
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total_servicios_count">0</h3>
                        <p>Servicios Diferentes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tools"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="total_servicios_vendidos">0</h3>
                        <p>Total Vendidos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner text-white">
                        <h3 id="servicio_mas_vendido">-</h3>
                        <p>Servicio Más Vendido</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="total_recaudado">S/ 0.00</h3>
                        <p>Total Recaudado</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRÁFICA Y TABLA -->
        <div class="row">
            <!-- GRÁFICA PIE -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-purple text-white" style="background: #6f42c1 !important;">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> <b>Distribución de Servicios</b></h3>
                    </div>
                    <div class="card-body">
                        <canvas id="grafica_servicios" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- GRÁFICA BARRAS -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title"><i class="fas fa-chart-bar"></i> <b>Servicios Más Vendidos</b></h3>
                    </div>
                    <div class="card-body">
                        <canvas id="grafica_barras_servicios" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLA DETALLADA -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h3 class="card-title"><i class="fas fa-table"></i> <b>Detalle de Servicios Prestados</b></h3>
                        <button class="btn btn-sm btn-success float-right" onclick="exportarExcelServicios()">
                            <i class="fas fa-file-excel"></i> Exportar a Excel
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla_servicios_prestados" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead style="background-color:#343a40; color:#FFFFFF;">
                                    <tr>
                                        <th>Servicio</th>
                                        <th class="text-center">Cantidad Vendida</th>
                                        <th class="text-right">Precio Unitario</th>
                                        <th class="text-right">Total Vendido</th>
                                        <th class="text-center">% del Total</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_servicios_prestados">
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle"></i> Seleccione un rango de fechas y genere el reporte
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

<script>
$(document).ready(function() {
    // Establecer fechas por defecto (mes actual)
    var hoy = new Date();
    var primerDiaMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    
    $('#filtro_servicio_fecha_desde').val(primerDiaMes.toISOString().split('T')[0]);
    $('#filtro_servicio_fecha_hasta').val(hoy.toISOString().split('T')[0]);
});

function exportarExcelServicios() {
    if ($.fn.DataTable.isDataTable('#tabla_servicios_prestados')) {
        $('#tabla_servicios_prestados').DataTable().button('.buttons-excel').trigger();
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Reporte no generado',
            text: 'Primero debe generar el reporte',
            confirmButtonText: 'Entendido'
        });
    }
}

// Sobrescribir función para incluir indicadores y gráfica de barras
function llenarTablaServicios(data) {
    let html = '';
    let total_general = 0;
    let cantidad_total = 0;
    let max_vendido = 0;
    let servicio_top = '';
    
    data.forEach(function(servicio) {
        let total = parseFloat(servicio.total_vendido);
        total_general += total;
        cantidad_total += parseInt(servicio.cantidad_vendida);
        
        if (parseInt(servicio.cantidad_vendida) > max_vendido) {
            max_vendido = parseInt(servicio.cantidad_vendida);
            servicio_top = servicio.nombre;
        }
    });
    
    // Actualizar indicadores
    $('#total_servicios_count').text(data.length);
    $('#total_servicios_vendidos').text(cantidad_total);
    $('#servicio_mas_vendido').text(servicio_top || '-');
    $('#total_recaudado').text('S/ ' + total_general.toFixed(2));
    
    // Llenar tabla
    data.forEach(function(servicio) {
        let porcentaje = (parseFloat(servicio.total_vendido) / total_general * 100).toFixed(1);
        
        html += `
            <tr>
                <td><i class="fas fa-wrench text-primary"></i> ${servicio.nombre}</td>
                <td class="text-center"><span class="badge badge-info">${servicio.cantidad_vendida}</span></td>
                <td class="text-right">S/ ${parseFloat(servicio.costo).toFixed(2)}</td>
                <td class="text-right"><b class="text-success">S/ ${parseFloat(servicio.total_vendido).toFixed(2)}</b></td>
                <td class="text-center">
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: ${porcentaje}%">
                            ${porcentaje}%
                        </div>
                    </div>
                </td>
            </tr>
        `;
    });
    
    html += `
        <tr class="bg-dark text-white">
            <th>TOTAL GENERAL</th>
            <th class="text-center">${cantidad_total}</th>
            <th></th>
            <th class="text-right">S/ ${total_general.toFixed(2)}</th>
            <th></th>
        </tr>
    `;
    
    $('#tbody_servicios_prestados').html(html);
    
    // Inicializar DataTable
    if ($.fn.DataTable.isDataTable('#tabla_servicios_prestados')) {
        $('#tabla_servicios_prestados').DataTable().destroy();
    }
    
    $('#tabla_servicios_prestados').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i>',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-success btn-sm',
                title: 'Reporte Servicios Prestados',
                filename: 'Servicios_' + new Date().toISOString().slice(0, 10)
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
        },
        order: [[3, 'desc']]
    });
    
    // Gráfica de barras
    mostrarGraficaBarrasServicios(data);
}

function mostrarGraficaBarrasServicios(data) {
    const ctx = document.getElementById('grafica_barras_servicios').getContext('2d');
    
    if (window.chartBarrasServicios) {
        window.chartBarrasServicios.destroy();
    }
    
    // Ordenar por cantidad vendida
    const dataOrdenada = [...data].sort((a, b) => parseInt(b.cantidad_vendida) - parseInt(a.cantidad_vendida)).slice(0, 10);
    
    const labels = dataOrdenada.map(s => s.nombre);
    const valores = dataOrdenada.map(s => parseInt(s.cantidad_vendida));
    
    window.chartBarrasServicios = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Cantidad Vendida',
                data: valores,
                backgroundColor: 'rgba(111, 66, 193, 0.7)',
                borderColor: 'rgb(111, 66, 193)',
                borderWidth: 2
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Top 10 Servicios Más Vendidos',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
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
    font-size: 2.2rem;
    font-weight: bold;
    margin: 0;
}

.bg-purple {
    background-color: #6f42c1 !important;
}
</style>