<script src="../js/console_reportes.js?rev=<?php echo time(); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>📦 REPORTE DE ENCOMIENDAS</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">REPORTE ENCOMIENDAS</li>
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
                    <div class="card-header" style="background: linear-gradient(90deg, #e83e8c, #d63384);">
                        <h3 class="card-title text-white">
                            <i class="fas fa-filter"></i> <b>Filtros de Búsqueda</b>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 form-group">
                                <label>Fecha Desde:</label>
                                <input type="date" class="form-control" id="filtro_enc_fecha_desde">
                            </div>
                            
                            <div class="col-md-2 form-group">
                                <label>Fecha Hasta:</label>
                                <input type="date" class="form-control" id="filtro_enc_fecha_hasta">
                            </div>

                            <div class="col-md-2 form-group">
                                <label>Estado:</label>
                                <select class="form-control" id="filtro_enc_estado">
                                    <option value="">TODOS</option>
                                    <option value="PENDIENTE">PENDIENTE</option>
                                    <option value="EN TRANSITO">EN TRANSITO</option>
                                    <option value="EN AGENCIA">EN AGENCIA</option>
                                    <option value="ENTREGADO">ENTREGADO</option>
                                    <option value="OBSERVADO">OBSERVADO</option>
                                    <option value="ANULADO">ANULADO</option>
                                    <option value="INCOMPLETO">INCOMPLETO</option>
                                </select>
                            </div>

                            <div class="col-md-2 form-group">
                                <label>Estado Pago:</label>
                                <select class="form-control" id="filtro_enc_estado_pago">
                                    <option value="">TODOS</option>
                                    <option value="PAGADO">PAGADO</option>
                                    <option value="POR PAGAR">POR PAGAR</option>
                                    <option value="ANULADO">ANULADO</option>
                                </select>
                            </div>

                            <div class="col-md-2 form-group">
                                <label>Origen:</label>
                                <select class="form-control select2" id="filtro_enc_origen" style="width: 100%;">
                                    <option value="">TODOS</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label>&nbsp;</label><br>
                                <button class="btn btn-primary btn-block btn-lg" onclick="listar_reporte_encomiendas()">
                                    <i class="fas fa-search"></i> Buscar
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
                        <h3 id="total_encomiendas">0</h3>
                        <p>Total Encomiendas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="total_entregadas">0</h3>
                        <p>Entregadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner text-white">
                        <h3 id="total_transito">0</h3>
                        <p>En Tránsito</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-truck"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3 id="total_facturado_enc">S/ 0.00</h3>
                        <p>Total Facturado</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRÁFICAS -->
        <div class="row">
            <!-- GRÁFICA ESTADOS -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> <b>Distribución por Estado</b></h3>
                    </div>
                    <div class="card-body">
                        <canvas id="grafica_estados_enc" height="280"></canvas>
                    </div>
                </div>
            </div>

            <!-- GRÁFICA TEMPORAL -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title"><i class="fas fa-chart-line"></i> <b>Encomiendas por Día</b></h3>
                    </div>
                    <div class="card-body">
                        <canvas id="grafica_temporal_enc" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOP RUTAS -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title"><i class="fas fa-route"></i> <b>Top Rutas Más Utilizadas</b></h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Pos.</th>
                                        <th>Origen</th>
                                        <th>Destino</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-right">Total Facturado</th>
                                        <th class="text-right">Promedio</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_top_rutas">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
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

        <!-- TABLA COMPLETA -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> <b>Listado Completo de Encomiendas</b>
                        </h3>
                        <div class="card-tools">
                            <button class="btn btn-tool" onclick="exportarExcelEncomiendas()">
                                <i class="fas fa-file-excel text-white"></i> Exportar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla_reporte_encomiendas" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#e83e8c; color:#FFFFFF;">
                                    <tr>
                                        <th>ID</th>
                                        <th>Boleta</th>
                                        <th>Fecha</th>
                                        <th>Origen</th>
                                        <th>Destino</th>
                                        <th>Emisor</th>
                                        <th>Receptor</th>
                                        <th>Conductor</th>
                                        <th>Descripción</th>
                                        <th class="text-right">Monto</th>
                                        <th class="text-right">Por Pagar</th>
                                        <th>Estado</th>
                                        <th>Estado Pago</th>
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

<!-- MODAL DETALLE ENCOMIENDA -->
<div class="modal fade" id="modal_detalle_encomienda" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-box"></i> <b>DETALLE DE ENCOMIENDA</b>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenido_detalle_encomienda">
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


<style>
/* Estilos para Select2 */
.select2-container--bootstrap4 .select2-selection {
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
    min-height: 38px !important;
}

.select2-container--bootstrap4 .select2-selection--single {
    height: calc(2.25rem + 2px) !important;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    line-height: 2.25rem !important;
    padding-left: 12px !important;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
    height: calc(2.25rem + 2px) !important;
}

.select2-container--bootstrap4.select2-container--focus .select2-selection {
    border-color: #80bdff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

/* Ajustar el dropdown */
.select2-container--bootstrap4 .select2-dropdown {
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
}

.select2-container--bootstrap4 .select2-results__option {
    padding: 8px 12px !important;
}

.select2-container--bootstrap4 .select2-results__option--highlighted {
    background-color: #007bff !important;
    color: white !important;
}

/* Timeline para el modal de detalle */
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, #007bff, #6c757d);
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -1.5rem;
    top: 0.3rem;
    width: 1rem;
    height: 1rem;
    background: #007bff;
    border: 3px solid white;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
}

.timeline-marker.bg-secondary {
    background: #6c757d;
    box-shadow: 0 0 0 3px rgba(108, 117, 125, 0.2);
}

.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}
</style>
