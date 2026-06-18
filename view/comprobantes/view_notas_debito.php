<script src="../js/console_notas_debito.js?rev=<?php echo time(); ?>"></script>
<link rel="stylesheet" href="../plantilla/plugins/icheck-bootstrap/icheck-bootstrap.min.css">

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>NOTAS DE DÉBITO</b></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">NOTAS DE DÉBITO</li>
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
                    <div class="card-header" style="background-color:#007bff;">
                        <h3 class="card-title" style="color:white">
                            <i class="fas fa-file-signature"></i>&nbsp;&nbsp;<b>Lista de Notas de Débito</b>
                        </h3>
                        <button class="btn btn-warning float-right" onclick="AbrirModalRegistro()">
                            <i class="fas fa-plus"></i> Nueva Nota de Débito
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
                                <button class="btn btn-primary btn-block" onclick="listar_notas_debito_filtro()">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- TABLA -->
                    <div class="table-responsive" style="text-align:center">
                        <div class="card-body">
                            <table id="tabla_notas_debito" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#007bff; color:#FFFFFF;">
                                    <tr>
                                        <th style="text-align:center">N°</th>
                                        <th style="text-align:center">Nota de Débito</th>
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

<!-- MODAL NUEVA NOTA DE DÉBITO -->
<div class="modal fade" id="modal_registro" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#007bff;">
                <h5 class="modal-title" style="color:white">
                    <b><i class="fas fa-file-signature"></i> NUEVA NOTA DE DÉBITO</b>
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
                            <strong>Paso 1:</strong> Busque el comprobante (Factura o Boleta) que desea incrementar con esta nota de débito.
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
                                        <strong>Total Original:</strong> <span id="span_total" style="font-size:18px; font-weight:bold; color:#007bff;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12"><hr></div>
                    
                    <!-- DATOS DE LA NOTA DE DÉBITO -->
                    <div class="col-12">
                        <h5 style="background-color:#007bff; color:white; padding:10px; border-radius:5px;">
                            <i class="fas fa-edit"></i> DATOS DE LA NOTA DE DÉBITO
                        </h5>
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>Serie Nota de Débito:</label>
                        <input type="text" class="form-control" id="txt_serie_nd" value="FD01" readonly>
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>Correlativo:</label>
                        <input type="text" class="form-control" id="txt_correlativo_nd" readonly placeholder="Automático">
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>Fecha Emisión:</label>
                        <input type="date" class="form-control" id="txt_fecha_emision_nd" readonly>
                    </div>
                    
                    <div class="col-md-12 form-group">
                        <label>Motivo / Sustento <b style="color:red">(*)</b>:</label>
                        <select class="form-control" id="select_motivo_nd">
                            <option value="">Seleccione el motivo</option>
                            <option value="01">01 - INTERÉS POR MORA</option>
                            <option value="02">02 - AUMENTO EN EL VALOR</option>
                            <option value="03">03 - PENALIDADES</option>
                            <option value="11">11 - AJUSTES DE OPERACIONES DE EXPORTACIÓN</option>
                            <option value="12">12 - AJUSTES - AFECTOS AL IVAP</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12 form-group">
                        <label>Descripción / Observación <b style="color:red">(*)</b>:</label>
                        <textarea class="form-control" id="txt_descripcion_nd" rows="3" 
                                  placeholder="Ej: Cobro de intereses por pago tardío"></textarea>
                    </div>
                    
                    <div class="col-md-12 form-group">
                        <label>Concepto del Cobro Adicional <b style="color:red">(*)</b>:</label>
                        <input type="text" class="form-control" id="txt_concepto_nd" 
                               placeholder="Ej: Equipaje extra, Cambio de asiento, Intereses, etc.">
                    </div>
                    
                    <div class="col-md-12 form-group">
                        <label>Monto a Incrementar <b style="color:red">(*)</b>:</label>
                        <input type="number" class="form-control" id="txt_monto_nd" step="0.01" 
                               placeholder="0.00" onchange="calcularTotalesND()">
                        <small class="text-muted">Ingrese el monto CON IGV que se agregará al comprobante</small>
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>Base Gravada:</label>
                        <input type="number" class="form-control" id="txt_base_nd" readonly>
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>IGV (18%):</label>
                        <input type="number" class="form-control" id="txt_igv_nd" readonly>
                    </div>
                    
                    <div class="col-md-4 form-group">
                        <label>Total Nota Débito:</label>
                        <input type="number" class="form-control" id="txt_total_nd" readonly 
                               style="font-size:18px; font-weight:bold; background-color:#d1ecf1;">
                    </div>
                    
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Nota:</strong> Al emitir esta nota de débito, el cliente deberá pagar adicionalmente el monto indicado.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <button type="button" class="btn btn-success" onclick="guardarNotaDebito('PENDIENTE')">
                    <i class="fas fa-save"></i> Guardar como PENDIENTE
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarYEnviarND()">
                    <i class="fas fa-paper-plane"></i> Guardar y Enviar a SUNAT
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    listar_notas_debito();
    establecerFechasFiltro();
    
    // Establecer fecha actual en el formulario
    var hoy = new Date().toISOString().split('T')[0];
    $('#txt_fecha_emision_nd').val(hoy);
});

function establecerFechasFiltro() {
    var hoy = new Date();
    var hace30dias = new Date();
    hace30dias.setDate(hace30dias.getDate() - 30);
    
    $('#txt_fecha_desde').val(hace30dias.toISOString().split('T')[0]);
    $('#txt_fecha_hasta').val(hoy.toISOString().split('T')[0]);
}

function calcularTotalesND() {
    var montoTotal = parseFloat($('#txt_monto_nd').val()) || 0;
    
    var baseGravada = montoTotal / 1.18;
    var igv = montoTotal - baseGravada;
    
    $('#txt_base_nd').val(baseGravada.toFixed(2));
    $('#txt_igv_nd').val(igv.toFixed(2));
    $('#txt_total_nd').val(montoTotal.toFixed(2));
}

function AbrirModalRegistro() {
    $('#modal_registro').modal('show');
    limpiarFormularioND();
}

function limpiarFormularioND() {
    $('#select_tipo_comp_buscar').val('');
    $('#txt_serie_buscar').val('');
    $('#txt_correlativo_buscar').val('');
    $('#datos_comprobante').hide();
    $('#select_motivo_nd').val('');
    $('#txt_descripcion_nd').val('');
    $('#txt_concepto_nd').val('');
    $('#txt_monto_nd').val('');
    $('#txt_base_nd').val('');
    $('#txt_igv_nd').val('');
    $('#txt_total_nd').val('');
}
</script>

<style>
    .badge-pendiente { background-color: #ffc107; color: #000; }
    .badge-enviado { background-color: #28a745; color: #fff; }
    .badge-anulado { background-color: #dc3545; color: #fff; }
</style>