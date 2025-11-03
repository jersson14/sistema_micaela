<script src="../js/console_reportes.js?rev=<?php echo time(); ?>"></script>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>📁 FACTURAS ARCHIVADAS</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">FACTURAS ARCHIVADAS</li>
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
                    <div class="card-header" style="background: linear-gradient(90deg, #6c757d, #495057);">
                        <h3 class="card-title text-white">
                            <i class="fas fa-archive"></i> Comprobantes Anulados
                        </h3>
                        <button class="btn btn-primary float-right" onclick="cargar_contenido('contenido_principal','comprobantes/comprobantes_lista.php')">
                            <i class="fas fa-arrow-left"></i> Volver a Comprobantes
                        </button>
                    </div>

                    <!-- FILTROS -->
                    <div class="card-body">
                        <div class="row" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-bottom: 20px; background-color: #f8f9fa;">
                            <div class="col-12">
                                <h5><i class="fas fa-filter"></i> <b>Filtros de Búsqueda</b></h5>
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Tipo Comprobante:</label>
                                <select class="form-control" id="filtro_tipo_comprobante">
                                    <option value="">Todos</option>
                                    <option value="01">FACTURA</option>
                                    <option value="03">BOLETA</option>
                                    <option value="07">NOTA DE CRÉDITO</option>
                                    <option value="08">NOTA DE DÉBITO</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Fecha Desde:</label>
                                <input type="date" class="form-control" id="filtro_fecha_desde">
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Fecha Hasta:</label>
                                <input type="date" class="form-control" id="filtro_fecha_hasta">
                            </div>
                            
                            <div class="col-md-3">
                                <label>&nbsp;</label><br>
                                <button class="btn btn-primary btn-block" onclick="listar_facturas_archivadas()">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TABLA -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla_facturas_archivadas" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#6c757d; color:#FFFFFF;">
                                    <tr>
                                        <th>N°</th>
                                        <th>Tipo</th>
                                        <th>Comprobante</th>
                                        <th>Fecha Emisión</th>
                                        <th>Cliente</th>
                                        <th>N° Documento</th>
                                        <th>Total</th>
                                        <th>Estado SUNAT</th>
                                        <th>Fecha Anulación</th>
                                        <th>Motivo Anulación</th>
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
    </div>
</div>

<!-- MODAL VER DETALLE -->
<div class="modal fade" id="modal_detalle_archivado" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice"></i> <b>DETALLE DE COMPROBANTE ANULADO</b>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenido_detalle_archivado">
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
    // Establecer fechas por defecto (último mes)
    var hoy = new Date();
    var hace30dias = new Date();
    hace30dias.setDate(hace30dias.getDate() - 30);
    
    $('#filtro_fecha_desde').val(hace30dias.toISOString().split('T')[0]);
    $('#filtro_fecha_hasta').val(hoy.toISOString().split('T')[0]);
    
    // Cargar datos iniciales
    listar_facturas_archivadas();
});
</script>