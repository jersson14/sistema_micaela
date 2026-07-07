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
                                    <option value=""  selected>Seleccione</option>
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
<!-- MODAL ANULAR BOLETA (COMUNICAR A SUNAT) -->
<div class="modal fade" id="modal_anular_boleta" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#dc3545;">
                <h5 class="modal-title" style="color:white">
                    <i class="fas fa-file-invoice"></i> <b>ANULAR BOLETA Y COMUNICAR A SUNAT</b>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt_id_comprobante_anular_boleta">
                <input type="hidden" id="txt_serie_anular_boleta">
                <input type="hidden" id="txt_correlativo_anular_boleta">
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Importante:</strong><br>
                    Se anulará la boleta <span id="span_numero_anular_boleta" class="font-weight-bold"></span><br>
                    <small>Esta acción generará una Comunicación de Baja que será enviada a SUNAT.</small>
                </div>
                
                <div class="form-group">
                    <label>Motivo de Anulación <b style="color:red">(*)</b>:</label>
                    <select class="form-control" id="select_motivo_anulacion_boleta">
                        <option value="">Seleccione un motivo</option>
                        <option value="01">Error en el RUC</option>
                        <option value="02">Error en la descripción</option>
                        <option value="03">Error en el monto</option>
                        <option value="04">Otro (especifique abajo)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Observaciones adicionales <b style="color:red">(*)</b>:</label>
                    <textarea class="form-control" id="txt_motivo_anulacion_boleta" rows="3" 
                              placeholder="Describa brevemente el motivo de la anulación" 
                              style="resize:none"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmarAnulacionBoleta()">
                    <i class="fas fa-ban"></i> Anular y Comunicar a SUNAT
                </button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL EDITAR COMPROBANTE -->
<div class="modal fade" id="modal_editar_comprobante" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, #FFA500, #FF8C00);">
                <h5 class="modal-title text-white">
                    <i class="fas fa-edit"></i> <b>EDITAR COMPROBANTE PENDIENTE</b>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt_id_comprobante_editar">
                
                <div class="row">
                    <!-- DATOS DEL COMPROBANTE -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <b>Datos del Comprobante</b>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Tipo Comprobante:</label>
                                        <select class="form-control" id="edit_tipo_comprobante" disabled>
                                            <option value="01">FACTURA</option>
                                            <option value="03">BOLETA</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Serie:</label>
                                        <input type="text" class="form-control" id="edit_serie" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Correlativo:</label>
                                        <input type="text" class="form-control" id="edit_correlativo" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Fecha Emisión:</label>
                                        <input type="date" class="form-control" id="edit_fecha_emision">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Moneda:</label>
                                        <select class="form-control" id="edit_moneda">
                                            <option value="PEN">Soles</option>
                                            <option value="USD">Dólares</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DATOS DEL CLIENTE -->
                    <div class="col-md-12 mt-3">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <b>Datos del Cliente</b>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Tipo Doc:</label>
                                        <select class="form-control" id="edit_tipo_documento_cliente">
                                            <option value="1">DNI</option>
                                            <option value="6">RUC</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Nº Documento:</label>
                                        <input type="text" class="form-control" id="edit_numero_documento">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Razón Social / Nombres:</label>
                                        <input type="text" class="form-control" id="edit_razon_social">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Teléfono:</label>
                                        <input type="text" class="form-control" id="edit_telefono">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label>Dirección:</label>
                                        <input type="text" class="form-control" id="edit_direccion">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Departamento:</label>
                                        <input type="text" class="form-control" id="edit_departamento">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Provincia:</label>
                                        <input type="text" class="form-control" id="edit_provincia">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Distrito:</label>
                                        <input type="text" class="form-control" id="edit_distrito">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DATOS DEL SERVICIO -->
                    <div class="col-md-12 mt-3">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <b>Datos del Servicio</b>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Servicio:</label>
                                        <select class="form-control" id="edit_servicio"></select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Cantidad:</label>
                                        <input type="number" class="form-control" id="edit_cantidad" value="1" min="1">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Conductor:</label>
                                        <select class="form-control" id="edit_conductor"></select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Fecha Viaje:</label>
                                        <input type="date" class="form-control" id="edit_fecha_viaje">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label>Origen:</label>
                                        <select class="form-control" id="edit_origen"></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Destino:</label>
                                        <select class="form-control" id="edit_destino"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MONTOS -->
                    <div class="col-md-12 mt-3">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <b>Montos</b>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Base Gravada:</label>
                                        <input type="number" class="form-control" id="edit_base_gravada" step="0.01" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label>IGV (18%):</label>
                                        <input type="number" class="form-control" id="edit_igv" step="0.01" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Total:</label>
                                        <input type="number" class="form-control" id="edit_total" step="0.01" >
                                    </div>
                                    <div class="col-md-2">
                                        <label>Forma Pago:</label>
                                        <select class="form-control" disabled id="edit_forma_pago">
                                            <option value="CONTADO">CONTADO</option>
                                            <option value="CREDITO">CRÉDITO</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Tipo de Pago:</label>
                                        <select class="form-control" id="edit_tipo_pago"></select>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <label>Observaciones:</label>
                                        <textarea class="form-control" id="edit_observaciones" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-warning" onclick="actualizarComprobante()">
                    <i class="fas fa-save"></i> Actualizar Comprobante
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Cargar selects al abrir modal
$('#modal_editar_comprobante').on('shown.bs.modal', function () {
    Cargar_Select_Servicios_Edit();
    Cargar_Select_Conductores_Edit();
    Cargar_Select_Rutas_Edit();
    Cargar_Select_Tipopago_Edit();
});

function Cargar_Select_Servicios_Edit() {
    $.ajax({
        url: "../controller/servicios/controlador_cargar_select_servicios.php",
        type: "POST",
    }).done(function (resp) {
        let data = JSON.parse(resp);
        let cadena = "<option value=''>Seleccionar servicio</option>";
        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
                cadena += "<option value='" + data[i][0] + "'>" + data[i][1] + "</option>";
            }
        }
        $("#edit_servicio").html(cadena);
    });
}

function Cargar_Select_Conductores_Edit() {
    $.ajax({
        url: "../controller/choferes/controlador_cargar_select_choferes.php",
        type: "POST",
    }).done(function (resp) {
        let data = JSON.parse(resp);
        let cadena = "<option value=''>Seleccionar conductor</option>";
        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
                cadena += "<option value='" + data[i][0] + "'>DNI: " + data[i][1] + " - " + data[i][2] + "</option>";
            }
        }
        $("#edit_conductor").html(cadena);
    });
}

function Cargar_Select_Rutas_Edit() {
    $.ajax({
        url: "../controller/rutas/controlador_cargar_select_rutas.php",
        type: "POST",
    }).done(function (resp) {
        let data = JSON.parse(resp);
        let cadena = "<option value=''>Seleccionar ruta</option>";
        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
                cadena += "<option value='" + data[i][0] + "'>" + data[i][1] + "</option>";
            }
        }
        $("#edit_origen").html(cadena);
        $("#edit_destino").html(cadena);
    });
}

function Cargar_Select_Tipopago_Edit() {
    $.ajax({
        url: "../controller/tipo_pago/controlador_cargar_select_tipo_pago.php",
        type: "POST",
    }).done(function (resp) {
        let data = JSON.parse(resp);
        let cadena = "<option value=''>Seleccionar tipo pago</option>";
        if (data.length > 0) {
            for (let i = 0; i < data.length; i++) {
                cadena += `<option value="${data[i][0]}">${data[i][1]}</option>`;
            }
        }
        $("#edit_tipo_pago").html(cadena);
    });
}
</script>
<script>
$(document).ready(function() {
    establecerFechasFiltro();
    listar_comprobantes();
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
    /* SOLUCIÓN MÍNIMA PARA DROPDOWNS */
.dataTables_wrapper {
  overflow: visible !important;
}

.dataTables_scrollBody {
  overflow: visible !important;
}

.table-responsive {
  overflow: visible !important;
}

.dropdown-menu {
  position: absolute !important;
  z-index: 9999 !important;
}
</style>