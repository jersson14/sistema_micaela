<script src="../js/console_comprobantes.js?rev=<?php echo time(); ?>"></script>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>LISTA DE COMPROBANTES ELECTRÓNICOS</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">COMPROBANTES</li>
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
                    <div class="card-header" style="background-color:#1FA0E0;">
                        <h3 class="card-title" style="color:white">
                            <i class="fas fa-list"></i>&nbsp;&nbsp;<b>Listado de Comprobantes</b>
                        </h3>
                        <button class="btn btn-success float-right" onclick="cargar_contenido('contenido_principal','comprobantes/facturas.php')">
                            <i class="fas fa-plus"></i> Nuevo Comprobante
                        </button>
                    </div>
                    
                    <!-- FILTROS DE BÚSQUEDA -->
                    <div class="card-body">
                        <div class="row" style="border: 1px solid #ccc; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <div class="col-12">
                                <h5><i class="fas fa-filter"></i> Filtros de Búsqueda</h5>
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Estado SUNAT:</label>
                                <select class="form-control" id="select_estado_filtro">
                                    <option value="">TODOS</option>
                                    <option value="PENDIENTE">PENDIENTE</option>
                                    <option value="ENVIADO">ENVIADO</option>
                                    <option value="ACEPTADO">ACEPTADO</option>
                                    <option value="ANULADO">ANULADO</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Fecha Desde:</label>
                                <input type="date" class="form-control" id="txt_fecha_desde">
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Fecha Hasta:</label>
                                <input type="date" class="form-control" id="txt_fecha_hasta">
                            </div>
                            
                            <div class="col-md-3">
                                <label>&nbsp;</label><br>
                                <button class="btn btn-primary btn-block" onclick="listar_comprobantes_filtro()">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- TABLA DE COMPROBANTES -->
                    <div class="table-responsive" style="text-align:center">
                        <div class="card-body">
                            <table id="tabla_comprobantes" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#023D77; color:#FFFFFF;">
                                    <tr>
                                        <th style="text-align:center">N°</th>
                                        <th style="text-align:center">Tipo</th>
                                        <th style="text-align:center">Comprobante</th>
                                        <th style="text-align:center">Fecha Emisión</th>
                                        <th style="text-align:center">Cliente</th>
                                        <th style="text-align:center">N° Documento</th>
                                        <th style="text-align:center">Origen - Destino</th>
                                        <th style="text-align:center">Total</th>
                                        <th style="text-align:center">Estado SUNAT</th>
                                        <th style="text-align:center">Respuesta SUNAT</th>
                                        <th style="text-align:center">Usuario</th>
                                        <th style="text-align:center">Acciones</th>
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


<!-- MODAL ENVIAR A SUNAT -->
<div class="modal fade" id="modal_enviar_sunat" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#28a745;">
                <h5 class="modal-title" style="color:white">
                    <b>ENVIAR A SUNAT</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt_id_comprobante_enviar">
                <input type="hidden" id="txt_serie_enviar">
                <input type="hidden" id="txt_correlativo_enviar">
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Información:</strong><br>
                    Se enviará el comprobante <span id="span_numero_enviar"></span> a SUNAT para su validación y aceptación.
                </div>
                
                <p><strong>¿Está seguro de enviar este comprobante a SUNAT?</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="confirmarEnvioSunat()">
                    <i class="fas fa-paper-plane"></i> Sí, Enviar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ANULAR COMPROBANTE -->
<div class="modal fade" id="modal_anular" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#dc3545;">
                <h5 class="modal-title" style="color:white">
                    <b>ANULAR COMPROBANTE</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt_id_comprobante_anular">
                
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Advertencia:</strong><br>
                    Esta acción no se puede revertir. El comprobante quedará anulado.
                </div>
                
                <div class="form-group">
                    <label>Motivo de Anulación <b style="color:red">(*)</b>:</label>
                    <textarea class="form-control" id="txt_motivo_anulacion" rows="3" 
                              placeholder="Ingrese el motivo de la anulación" 
                              style="resize:none"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmarAnulacion()">
                    <i class="fas fa-ban"></i> Anular Comprobante
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    listar_comprobantes();
    establecerFechasFiltro();
});

function establecerFechasFiltro() {
    var hoy = new Date();
    var hace30dias = new Date();
    hace30dias.setDate(hace30dias.getDate() - 30);
    
    $('#txt_fecha_desde').val(hace30dias.toISOString().split('T')[0]);
    $('#txt_fecha_hasta').val(hoy.toISOString().split('T')[0]);
}

function NuevoComprobante() {
    window.location.href = 'factura.php';
}
</script>

<style>
    .badge-pendiente {
        background-color: #ffc107;
        color: #000;
    }
    .badge-enviado {
        background-color: #28a745;
        color: #fff;
    }
    .badge-anulado {
        background-color: #dc3545;
        color: #fff;
    }
</style>