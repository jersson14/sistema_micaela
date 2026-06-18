<script src="../js/console_envios_sunat.js?rev=<?php echo time(); ?>"></script>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>ENVÍOS A SUNAT</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">ENVÍOS SUNAT</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        
        <!-- TARJETAS DE RESUMEN -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="total_pendientes">0</h3>
                        <p>Comprobantes Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="total_enviados">0</h3>
                        <p>Enviados y Aceptados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="total_rechazados">0</h3>
                        <p>Rechazados por SUNAT</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total_hoy">0</h3>
                        <p>Enviados Hoy</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- PANEL DE ENVÍO MASIVO -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header" style="background-color:#28a745;">
                        <h3 class="card-title" style="color:white">
                            <i class="fas fa-paper-plane"></i>&nbsp;&nbsp;<b>Panel de Envío Masivo a SUNAT</b>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Información:</strong> Aquí puede enviar múltiples comprobantes pendientes a SUNAT de forma masiva.
                                </div>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label>Tipo de Documento:</label>
                                <select class="form-control" id="select_tipo_envio">
                                    <option value="">TODOS</option>
                                    <option value="01">FACTURAS</option>
                                    <option value="03">BOLETAS</option>
                                    <option value="07">NOTAS DE CRÉDITO</option>
                                    <option value="08">NOTAS DE DÉBITO</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label>Fecha Desde:</label>
                                <input type="date" class="form-control" id="txt_fecha_desde_envio">
                            </div>
                            
                            <div class="col-md-4 form-group">
                                <label>Fecha Hasta:</label>
                                <input type="date" class="form-control" id="txt_fecha_hasta_envio">
                            </div>
                            
                            <div class="col-md-12 text-center">
                                <button class="btn btn-primary btn-lg" onclick="buscarPendientes()">
                                    <i class="fas fa-search"></i> Buscar Pendientes
                                </button>
                                <button class="btn btn-success btn-lg" onclick="enviarTodosPendientes()" disabled id="btn_enviar_todos">
                                    <i class="fas fa-paper-plane"></i> Enviar Todos a SUNAT
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- LISTA DE COMPROBANTES PENDIENTES -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header" style="background-color:#ffc107;">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i>&nbsp;&nbsp;<b>Comprobantes Pendientes de Envío</b>
                        </h3>
                        <button class="btn btn-sm btn-light float-right" onclick="actualizarLista()">
                            <i class="fas fa-sync-alt"></i> Actualizar
                        </button>
                    </div>
                    <div class="table-responsive" style="text-align:center">
                        <div class="card-body">
                            <table id="tabla_pendientes_envio" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#023D77; color:#FFFFFF;">
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="check_all" onclick="seleccionarTodos()">
                                        </th>
                                        <th>Tipo</th>
                                        <th>Comprobante</th>
                                        <th>Fecha Emisión</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Estado</th>
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
        
        <!-- HISTORIAL DE ENVÍOS -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header" style="background-color:#17a2b8;">
                        <h3 class="card-title" style="color:white">
                            <i class="fas fa-history"></i>&nbsp;&nbsp;<b>Historial de Envíos a SUNAT</b>
                        </h3>
                    </div>
                    
                    <!-- FILTROS HISTORIAL -->
                    <div class="card-body">
                        <div class="row" style="border: 1px solid #ccc; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <div class="col-12">
                                <h5><i class="fas fa-filter"></i> Filtros</h5>
                            </div>
                            
                            <div class="col-md-2 form-group">
                                <label>Tipo:</label>
                                <select class="form-control" id="select_tipo_historial">
                                    <option value="">TODOS</option>
                                    <option value="01">FACTURAS</option>
                                    <option value="03">BOLETAS</option>
                                    <option value="07">N. CRÉDITO</option>
                                    <option value="08">N. DÉBITO</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2 form-group">
                                <label>Estado:</label>
                                <select class="form-control" id="select_estado_historial">
                                    <option value="">TODOS</option>
                                    <option value="ACEPTADO">ACEPTADO</option>
                                    <option value="ENVIADO">ENVIADO</option>
                                    <option value="RECHAZADO">RECHAZADO</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Fecha Desde:</label>
                                <input type="date" class="form-control" id="txt_fecha_desde_historial">
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Fecha Hasta:</label>
                                <input type="date" class="form-control" id="txt_fecha_hasta_historial">
                            </div>
                            
                            <div class="col-md-2">
                                <label>&nbsp;</label><br>
                                <button class="btn btn-info btn-block" onclick="listar_historial_filtro()">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
  
                    <div class="table-responsive" style="text-align:center">
                        <div class="col-md-12 mt-3">
                            <div class="btn-group" role="group">
                                <button class="btn btn-warning" onclick="generarReporteSunat()" title="Generar reporte PDF para declaraciones SUNAT">
                                    <i class="fas fa-file-pdf"></i> Reporte Declaraciones SUNAT
                                </button>
                                <button class="btn btn-success" onclick="exportarExcelSunat()" title="Exportar a Excel">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </button>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> Seleccione un rango de fechas y presione "Buscar" antes de generar el reporte
                            </small>
                        </div>
                        <div class="card-body">
                            <table id="tabla_historial_envios" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#17a2b8; color:#FFFFFF;">
                                    <tr>
                                        <th>N°</th>
                                        <th>Tipo</th>
                                        <th>Comprobante</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Fecha Envío</th>
                                        <th>Estado SUNAT</th>
                                        <th>Respuesta</th>
                                        <th>Hash</th>
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

<!-- MODAL VER RESPUESTA SUNAT -->
<div class="modal fade" id="modal_respuesta_sunat" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#17a2b8;">
                <h5 class="modal-title" style="color:white">
                    <b><i class="fas fa-info-circle"></i> RESPUESTA DE SUNAT</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span style="color:white">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenido_respuesta_sunat">
                <!-- Se llenará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PROGRESO DE ENVÍO -->
<div class="modal fade" id="modal_progreso_envio" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#28a745;">
                <h5 class="modal-title" style="color:white">
                    <b><i class="fas fa-paper-plane"></i> ENVIANDO COMPROBANTES A SUNAT</b>
                </h5>
            </div>
            <div class="modal-body">
                <div class="progress mb-3" style="height: 30px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         id="barra_progreso" 
                         style="width: 0%">0%</div>
                </div>
                
                <div id="log_envios" style="max-height: 400px; overflow-y: auto; background-color:#f8f9fa; padding:15px; border-radius:5px;">
                    <p><i class="fas fa-spinner fa-spin"></i> Iniciando envíos...</p>
                </div>
                
                <div class="mt-3">
                    <strong>Resumen:</strong>
                    <ul>
                        <li>Total a enviar: <span id="total_enviar">0</span></li>
                        <li>Enviados exitosamente: <span id="total_exitosos" class="text-success">0</span></li>
                        <li>Con errores: <span id="total_errores" class="text-danger">0</span></li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btn_cerrar_progreso" disabled>
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL VER DETALLE -->
<div class="modal fade" id="modal_detalle" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header" style="background: linear-gradient(90deg, #007bff, #17a2b8);">
                <h5 class="modal-title text-white">
                    <i class="fas fa-file-invoice"></i> <b>DETALLE DEL COMPROBANTE</b>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenido_detalle">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    cargarResumen();
    listar_pendientes_envio();
    listar_historial_envios();
    establecerFechasFiltro();
});

function establecerFechasFiltro() {
    var hoy = new Date();
    var hace7dias = new Date();
    hace7dias.setDate(hace7dias.getDate() - 7);
    
    $('#txt_fecha_desde_envio').val(hace7dias.toISOString().split('T')[0]);
    $('#txt_fecha_hasta_envio').val(hoy.toISOString().split('T')[0]);
    $('#txt_fecha_desde_historial').val(hace7dias.toISOString().split('T')[0]);
    $('#txt_fecha_hasta_historial').val(hoy.toISOString().split('T')[0]);
}

function seleccionarTodos() {
    var checkAll = document.getElementById('check_all');
    var checkboxes = document.querySelectorAll('input[name="check_comprobante"]');
    
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = checkAll.checked;
    });
}

function actualizarLista() {
    listar_pendientes_envio();
    cargarResumen();
}
</script>

<style>
    .badge-enviado { background-color: #28a745; color: #fff; }
    .badge-rechazado { background-color: #dc3545; color: #fff; }
    .badge-pendiente { background-color: #ffc107; color: #000; }
    
    #log_envios p {
        margin-bottom: 5px;
        padding: 5px;
        border-left: 3px solid #007bff;
        padding-left: 10px;
    }
    
    .envio-exitoso {
        border-left-color: #28a745 !important;
        background-color: #d4edda;
    }
    
    .envio-error {
        border-left-color: #dc3545 !important;
        background-color: #f8d7da;
    }
</style>