<script src="../js/console_notas_credito.js?rev=<?php echo time(); ?>"></script>
<link rel="stylesheet" href="../plantilla/plugins/icheck-bootstrap/icheck-bootstrap.min.css">

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>NOTAS DE CRÉDITO</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">NOTAS DE CRÉDITO</li>
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
                    <div class="card-header" style="background-color:#dc3545;">
                        <h3 class="card-title" style="color:white">
                            <i class="fas fa-file-alt"></i>&nbsp;&nbsp;<b>Lista de Notas de Crédito</b>
                        </h3>
                        <button class="btn btn-warning float-right" onclick="AbrirModalRegistro()">
                            <i class="fas fa-plus"></i> Nueva Nota de Crédito
                        </button>
                    </div>
                    
                    <!-- FILTROS -->
                    <div class="card-body">
                        <div class="row" style="border: 1px solid #ccc; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <div class="col-12">
                                <h5><i class="fas fa-filter"></i> Filtros de Búsqueda</h5>
                            </div>
                            
                            <div class="col-md-3 form-group">
                                <label>Estado:</label>
                                <select class="form-control" id="select_estado_filtro">
                                    <option value="">TODOS</option>
                                    <option value="PENDIENTE">PENDIENTE</option>
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
                                <button class="btn btn-primary btn-block" onclick="listar_notas_credito_filtro()">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- TABLA -->
                    <div class="table-responsive" style="text-align:center">
                        <div class="card-body">
                            <table id="tabla_notas_credito" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#dc3545; color:#FFFFFF;">
                                    <tr>
                                        <th style="text-align:center">N°</th>
                                        <th style="text-align:center">Nota de Crédito</th>
                                        <th style="text-align:center">Fecha Emisión</th>
                                        <th style="text-align:center">Comprobante Afectado</th>
                                        <th style="text-align:center">Cliente</th>
                                        <th style="text-align:center">Motivo</th>
                                        <th style="text-align:center">Monto</th>
                                        <th style="text-align:center">Estado</th>
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

<!-- MODAL NUEVA NOTA DE CRÉDITO -->
<div class="modal fade" id="modal_registro" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#dc3545;">
                <h5 class="modal-title" style="color:white">
                    <b><i class="fas fa-file-alt"></i> NUEVA NOTA DE CRÉDITO</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span style="color:white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 form-group" style="color:red">
                        <h6><b>Campos Obligatorios (*)</b></h6>
                    </div>
                    
                    <!-- BUSCAR COMPROBANTE A AFECTAR -->
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Paso 1:</strong> Busque el comprobante (Factura o Boleta) que desea afectar con esta nota de crédito.
                        </div>
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>Tipo Comprobante <b style="color:red">(*)</b>:</label>
                        <select class="form-control" id="select_tipo_comp_buscar">
                            <option value="">Seleccione</option>
                            <option value="01">FACTURA</option>
                            <option value="03">BOLETA</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>Serie <b style="color:red">(*)</b>:</label>
                        <input type="text" class="form-control" id="txt_serie_buscar" placeholder="Ej: F001">
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>Correlativo <b style="color:red">(*)</b>:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="txt_correlativo_buscar" placeholder="Ej: 00000001">
                            <div class="input-group-append">
                                <button class="btn btn-primary" onclick="buscarComprobante()">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DATOS DEL COMPROBANTE ENCONTRADO -->
                    <div class="col-12" id="datos_comprobante" style="display:none;">
                        <div class="card" style="background-color:#f8f9fa;">
                            <div class="card-body">
                                <h6><i class="fas fa-check-circle text-success"></i> <b>Comprobante Encontrado</b></h6>
                                <hr>
                                <input type="hidden" id="txt_id_comprobante_afectado">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Cliente:</strong> <span id="span_cliente"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>N° Documento:</strong> <span id="span_documento"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Fecha Emisión:</strong> <span id="span_fecha"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Total:</strong> <span id="span_total" style="font-size:18px; font-weight:bold; color:#dc3545;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12"><hr></div>
                    
                    <!-- DATOS DE LA NOTA DE CRÉDITO -->
                    <div class="col-12">
                        <h5 style="background-color:#dc3545; color:white; padding:10px; border-radius:5px;">
                            <i class="fas fa-edit"></i> DATOS DE LA NOTA DE CRÉDITO
                        </h5>
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>Serie Nota de Crédito:</label>
                        <input type="text" class="form-control" id="txt_serie_nc" value="FN01" readonly>
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>Correlativo:</label>
                    <input type="text" class="form-control" id="txt_correlativo_nc" readonly placeholder="Automático">                    
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>Fecha Emisión:</label>
                        <input type="date" class="form-control" id="txt_fecha_emision_nc" readonly>
                    </div>
                    
                    <div class="col-md-12 form-group">
                        <label>Motivo / Sustento <b style="color:red">(*)</b>:</label>
                        <select class="form-control" id="select_motivo_nc">
                            <option value="">Seleccione el motivo</option>
                            <option value="01">01 - ANULACIÓN DE LA OPERACIÓN</option>
                            <option value="02">02 - ANULACIÓN POR ERROR EN EL RUC</option>
                            <option value="03">03 - CORRECCIÓN POR ERROR EN LA DESCRIPCIÓN</option>
                            <option value="04">04 - DESCUENTO GLOBAL</option>
                            <option value="05">05 - DESCUENTO POR ÍTEM</option>
                            <option value="06">06 - DEVOLUCIÓN TOTAL</option>
                            <option value="07">07 - DEVOLUCIÓN POR ÍTEM</option>
                            <option value="08">08 - BONIFICACIÓN</option>
                            <option value="09">09 - DISMINUCIÓN EN EL VALOR</option>
                            <option value="13">13 - AJUSTES - AFECTOS AL IVAP</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12 form-group">
                        <label>Descripción / Observación <b style="color:red">(*)</b>:</label>
                        <textarea class="form-control" id="txt_descripcion_nc" rows="3" 
                                  placeholder="Ej: Anulación de viaje por cancelación del pasajero"></textarea>
                    </div>
                    
                    <div class="col-md-6 form-group">
                        <label>Monto a Devolver <b style="color:red">(*)</b>:</label>
                        <input type="number" class="form-control" id="txt_monto_nc" step="0.01" 
                               placeholder="0.00" onchange="calcularTotalesNC()">
                        <small class="text-muted">Ingrese el monto CON IGV</small>
                    </div>
                    
                    <div class="col-md-6 form-group">
                        <label>Monto Máximo Permitido:</label>
                        <input type="text" class="form-control" id="txt_monto_maximo" readonly 
                               style="background-color:#fff3cd; font-weight:bold;">
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>Base Gravada:</label>
                        <input type="number" class="form-control" id="txt_base_nc" readonly>
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>IGV (18%):</label>
                        <input type="number" class="form-control" id="txt_igv_nc" readonly>
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>Total Nota Crédito:</label>
                        <input type="number" class="form-control" id="txt_total_nc" readonly 
                               style="font-size:18px; font-weight:bold; background-color:#f8d7da;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>

                <button type="button" class="btn btn-danger" onclick="guardarYEnviarNC()">
                    <i class="fas fa-paper-plane"></i> Guardar y Enviar a SUNAT
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    listar_notas_credito();
    establecerFechasFiltro();
    
    // Establecer fecha actual en el formulario
    var hoy = new Date().toISOString().split('T')[0];
    $('#txt_fecha_emision_nc').val(hoy);
});

function establecerFechasFiltro() {
    var hoy = new Date();
    var hace30dias = new Date();
    hace30dias.setDate(hace30dias.getDate() - 30);
    
    $('#txt_fecha_desde').val(hace30dias.toISOString().split('T')[0]);
    $('#txt_fecha_hasta').val(hoy.toISOString().split('T')[0]);
}

function calcularTotalesNC() {
    var montoTotal = parseFloat($('#txt_monto_nc').val()) || 0;
    var montoMaximo = parseFloat($('#txt_monto_maximo').val().replace('S/ ', '')) || 0;
    
    if (montoTotal > montoMaximo) {
        Swal.fire('Advertencia', 'El monto no puede ser mayor al total del comprobante', 'warning');
        $('#txt_monto_nc').val(montoMaximo.toFixed(2));
        montoTotal = montoMaximo;
    }
    
    var baseGravada = montoTotal / 1.18;
    var igv = montoTotal - baseGravada;
    
    $('#txt_base_nc').val(baseGravada.toFixed(2));
    $('#txt_igv_nc').val(igv.toFixed(2));
    $('#txt_total_nc').val(montoTotal.toFixed(2));
}

function AbrirModalRegistro() {
    $('#modal_registro').modal('show');
    limpiarFormularioNC();
}

function limpiarFormularioNC() {
    $('#select_tipo_comp_buscar').val('');
    $('#txt_serie_buscar').val('');
    $('#txt_correlativo_buscar').val('');
    $('#datos_comprobante').hide();
    $('#select_motivo_nc').val('');
    $('#txt_descripcion_nc').val('');
    $('#txt_monto_nc').val('');
    $('#txt_base_nc').val('');
    $('#txt_igv_nc').val('');
    $('#txt_total_nc').val('');
}
</script>

<style>
    .badge-pendiente { background-color: #ffc107; color: #000; }
    .badge-enviado { background-color: #28a745; color: #fff; }
    .badge-anulado { background-color: #dc3545; color: #fff; }
</style>