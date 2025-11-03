<script src="../js/console_reportes.js?rev=<?php echo time(); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>💰 REPORTE INGRESOS VS GASTOS</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">INGRESOS VS GASTOS</li>
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
                    <div class="card-header" style="background: linear-gradient(90deg, #28a745, #20c997);">
                        <h3 class="card-title text-white">
                            <i class="fas fa-filter"></i> <b>Filtros de Búsqueda</b>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Fecha Desde <b style="color:red">(*)</b>:</label>
                                <input type="date" class="form-control" id="filtro_ingreso_fecha_desde">
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label>Fecha Hasta <b style="color:red">(*)</b>:</label>
                                <input type="date" class="form-control" id="filtro_ingreso_fecha_hasta">
                            </div>
                            
                            <div class="col-md-4">
                                <label>&nbsp;</label><br>
                                <button class="btn btn-primary btn-block btn-lg" onclick="listar_ingresos_gastos()">
                                    <i class="fas fa-chart-line"></i> Generar Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RESUMEN EJECUTIVO -->
        <div class="row">
            <div class="col-lg-4">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="total_ingresos_display">S/ 0.00</h3>
                        <p>Total Ingresos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="total_gastos_display">S/ 0.00</h3>
                        <p>Total Gastos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="small-box" id="balance_box" style="background: linear-gradient(90deg, #17a2b8, #20c997);">
                    <div class="inner text-white">
                        <h3 id="balance_display">S/ 0.00</h3>
                        <p>Balance</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRÁFICA Y TABLA -->
        <div class="row">
            <!-- GRÁFICA -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title"><i class="fas fa-chart-bar"></i> <b>Gráfica Comparativa</b></h3>
                    </div>
                    <div class="card-body">
                        <canvas id="grafica_ingresos_gastos" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- TABLA RESUMEN -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h3 class="card-title"><i class="fas fa-table"></i> <b>Resumen Detallado</b></h3>
                    </div>
                    <div class="card-body">
                        <div id="tabla_resumen_ingresos_gastos">
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> Seleccione un rango de fechas y haga clic en "Generar Reporte"
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETALLE DIARIO -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title"><i class="fas fa-calendar-alt"></i> <b>Detalle Día por Día</b></h3>
                        <button class="btn btn-sm btn-light float-right" onclick="exportarExcelIngresosGastos()">
                            <i class="fas fa-file-excel"></i> Exportar a Excel
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla_detalle_diario" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#343a40; color:#FFFFFF;">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Ingresos</th>
                                        <th>Gastos</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se llenarán dinámicamente -->
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
    
    $('#filtro_ingreso_fecha_desde').val(primerDiaMes.toISOString().split('T')[0]);
    $('#filtro_ingreso_fecha_hasta').val(hoy.toISOString().split('T')[0]);
    
    // 🔥 NO inicializar DataTable aquí, se hará después de cargar datos
});

function exportarExcelIngresosGastos() {
    // Verificar si DataTable está inicializado
    if ($.fn.DataTable.isDataTable('#tabla_detalle_diario')) {
        $('#tabla_detalle_diario').DataTable().button('.buttons-excel').trigger();
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Reporte no generado',
            text: 'Primero haga clic en "Generar Reporte"',
            confirmButtonText: 'Entendido'
        });
    }
}
</script>

<style>
.small-box {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
</style>