// ============================================================
// ARCHIVO: console_envios_sunat.js
// Sistema de Envíos Masivos a SUNAT
// ============================================================

var tbl_pendientes;
var tbl_historial;
var comprobantesPendientes = [];
var totalExitosos = 0;
var totalErrores = 0;

// ============================================================
// INICIALIZACIÓN AL CARGAR LA PÁGINA
// ============================================================
$(document).ready(function() {
    cargarResumen();
    listar_pendientes_envio();
    listar_historial_envios();
    establecerFechasFiltro();
});

// ============================================================
// ESTABLECER FECHAS POR DEFECTO EN FILTROS
// ============================================================
function establecerFechasFiltro() {
    var hoy = new Date();
    var hace7dias = new Date();
    hace7dias.setDate(hace7dias.getDate() - 7);
    
    $('#txt_fecha_desde_envio').val(hace7dias.toISOString().split('T')[0]);
    $('#txt_fecha_hasta_envio').val(hoy.toISOString().split('T')[0]);
    $('#txt_fecha_desde_historial').val(hace7dias.toISOString().split('T')[0]);
    $('#txt_fecha_hasta_historial').val(hoy.toISOString().split('T')[0]);
}

// ============================================================
// CARGAR RESUMEN DE ESTADÍSTICAS
// ============================================================
function cargarResumen() {
    $.ajax({
        url: "../controller/comprobante/controller_comprobante.php",
        type: "POST",
        data: { accion: "OBTENER_RESUMEN_ENVIOS" },
        dataType: "json"
    }).done(function(data) {
        if (data) {
            $("#total_pendientes").text(data.pendientes || 0);
            $("#total_enviados").text(data.enviados || 0);
            $("#total_rechazados").text(data.rechazados || 0);
            $("#total_hoy").text(data.hoy || 0);
        }
    }).fail(function() {
        console.error("Error al cargar resumen");
    });
}

// ============================================================
// LISTAR COMPROBANTES PENDIENTES DE ENVÍO
// ============================================================
function listar_pendientes_envio() {
    if (tbl_pendientes) tbl_pendientes.destroy();
    
    tbl_pendientes = $("#tabla_pendientes_envio").DataTable({
        "ordering": true,
        "bLengthChange": true,
        "searching": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "pageLength": 10,
        "destroy": true,
        "processing": true,
        "ajax": {
            "url": "../controller/comprobante/controller_comprobante.php",
            "type": "POST",
            "data": { accion: "LISTAR_PENDIENTES_ENVIO" }
        },
        "columns": [
            { 
                "data": null,
                "orderable": false,
                "render": function(data) {
                    return `<input type="checkbox" name="check_comprobante" value="${data.id_comprobante}" 
                            data-serie="${data.serie}" data-correlativo="${data.correlativo}">`;
                }
            },
            { 
                "data": "tipo_comprobante",
                "render": function(data) {
                    if (data == "01") return '<span class="badge badge-info">FACTURA</span>';
                    if (data == "03") return '<span class="badge badge-primary">BOLETA</span>';
                    if (data == "07") return '<span class="badge badge-warning">N. CRÉDITO</span>';
                    if (data == "08") return '<span class="badge badge-secondary">N. DÉBITO</span>';
                    return data;
                }
            },
            { 
                "data": null,
                "render": data => `<b>${data.numero_comprobante}</b>`
            },
            { 
                "data": "fecha_emision",
                "render": data => data ? new Date(data).toLocaleDateString("es-PE") : "-"
            },
            { 
                "data": "razon_social",
                "render": data => data || "-"
            },
            { 
                "data": "total",
                "render": data => "S/ " + parseFloat(data).toFixed(2)
            },
            { 
                "data": "estado_sunat",
                "render": () => '<span class="badge badge-pendiente"><i class="fas fa-clock"></i> PENDIENTE</span>'
            },
            { 
                "data": null,
                "orderable": false,
                "render": function(data) {
                    return `
                        <button class="btn btn-success btn-sm" onclick="enviarIndividual(${data.id_comprobante}, '${data.serie}', '${data.correlativo}')" 
                                title="Enviar a SUNAT">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                        <button class="btn btn-info btn-sm" onclick="verDetallePendiente(${data.id_comprobante})" 
                                title="Ver Detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                    `;
                }
            }
        ],
        "language": { "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json" },
        "drawCallback": function() {
            let total = this.api().data().count();
            $("#btn_enviar_todos").prop('disabled', total === 0);
        }
    });
}

// ============================================================
// BUSCAR PENDIENTES CON FILTROS
// ============================================================
function buscarPendientes() {
    let tipo = $("#select_tipo_envio").val();
    let desde = $("#txt_fecha_desde_envio").val();
    let hasta = $("#txt_fecha_hasta_envio").val();
    
    if (tbl_pendientes) tbl_pendientes.destroy();
    
    tbl_pendientes = $("#tabla_pendientes_envio").DataTable({
        "ordering": true,
        "bLengthChange": true,
        "searching": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "pageLength": 10,
        "destroy": true,
        "processing": true,
        "ajax": {
            "url": "../controller/comprobante/controller_comprobante.php",
            "type": "POST",
            "data": {
                accion: "LISTAR_PENDIENTES_ENVIO",
                tipo_comprobante: tipo,
                fecha_desde: desde,
                fecha_hasta: hasta
            }
        },
        "columns": [
            { 
                "data": null,
                "orderable": false,
                "render": function(data) {
                    return `<input type="checkbox" name="check_comprobante" value="${data.id_comprobante}" 
                            data-serie="${data.serie}" data-correlativo="${data.correlativo}">`;
                }
            },
            { 
                "data": "tipo_comprobante",
                "render": function(data) {
                    if (data == "01") return '<span class="badge badge-info">FACTURA</span>';
                    if (data == "03") return '<span class="badge badge-primary">BOLETA</span>';
                    if (data == "07") return '<span class="badge badge-warning">N. CRÉDITO</span>';
                    if (data == "08") return '<span class="badge badge-secondary">N. DÉBITO</span>';
                    return data;
                }
            },
            { 
                "data": null,
                "render": data => `<b>${data.numero_comprobante}</b>`
            },
            { 
                "data": "fecha_emision",
                "render": data => data ? new Date(data).toLocaleDateString("es-PE") : "-"
            },
            { 
                "data": "razon_social",
                "render": data => data || "-"
            },
            { 
                "data": "total",
                "render": data => "S/ " + parseFloat(data).toFixed(2)
            },
            { 
                "data": "estado_sunat",
                "render": () => '<span class="badge badge-pendiente"><i class="fas fa-clock"></i> PENDIENTE</span>'
            },
            { 
                "data": null,
                "orderable": false,
                "render": function(data) {
                    return `
                        <button class="btn btn-success btn-sm" onclick="enviarIndividual(${data.id_comprobante}, '${data.serie}', '${data.correlativo}')" 
                                title="Enviar a SUNAT">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                        <button class="btn btn-info btn-sm" onclick="verDetallePendiente(${data.id_comprobante})" 
                                title="Ver Detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                    `;
                }
            }
        ],
        "language": { "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json" },
        "drawCallback": function() {
            let total = this.api().data().count();
            $("#btn_enviar_todos").prop('disabled', total === 0);
        }
    });
}

// ============================================================
// SELECCIONAR TODOS LOS CHECKBOXES
// ============================================================
function seleccionarTodos() {
    var checkAll = document.getElementById('check_all');
    var checkboxes = document.querySelectorAll('input[name="check_comprobante"]');
    
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = checkAll.checked;
    });
}

// ============================================================
// ENVIAR COMPROBANTE INDIVIDUAL
// ============================================================
function enviarIndividual(id, serie, correlativo) {
    Swal.fire({
        title: "¿Enviar a SUNAT?",
        html: `Comprobante: <b>${serie}-${correlativo}</b>`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, Enviar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Enviando...",
                html: `Procesando <b>${serie}-${correlativo}</b>`,
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => Swal.showLoading()
            });
            
            $.ajax({
                url: "../controller/comprobante/controller_comprobante.php",
                type: "POST",
                data: {
                    accion: "ENVIAR_SUNAT",
                    id_comprobante: id
                },
                dataType: "json"
            }).done(function(resp) {
                Swal.close();
                
                if (resp.status == "success") {
                    // Abrir ticket en popup
                    const popupWidth = 480;
                    const popupHeight = 700;
                    const left = (screen.width - popupWidth) / 2;
                    const top = (screen.height - popupHeight) / 2;

                    window.open(
                        "../view/MPDF/REPORTE/ticket_comprobante.php?id=" + id,
                        "TicketSUNAT",
                        `width=${popupWidth},height=${popupHeight},top=${top},left=${left},resizable=yes,scrollbars=yes`
                    );
                    
                    Swal.fire({
                        icon: "success",
                        title: "¡Enviado exitosamente!",
                        html: `<b>${serie}-${correlativo}</b> aceptado por SUNAT`,
                        showConfirmButton: true
                    }).then(() => {
                        listar_pendientes_envio();
                        cargarResumen();
                        listar_historial_envios();
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error al enviar",
                        html: resp.message || "No se pudo enviar a SUNAT",
                        showConfirmButton: true
                    });
                }
            }).fail(function() {
                Swal.close();
                Swal.fire("Error", "No se pudo comunicar con SUNAT", "error");
            });
        }
    });
}

// ============================================================
// ENVIAR TODOS LOS PENDIENTES (MASIVO)
// ============================================================
function enviarTodosPendientes() {
    let checkboxes = document.querySelectorAll('input[name="check_comprobante"]:checked');
    
    if (checkboxes.length === 0) {
        return Swal.fire("Advertencia", "Seleccione al menos un comprobante", "warning");
    }
    
    comprobantesPendientes = [];
    checkboxes.forEach(function(checkbox) {
        comprobantesPendientes.push({
            id: checkbox.value,
            serie: checkbox.dataset.serie,
            correlativo: checkbox.dataset.correlativo
        });
    });
    
    Swal.fire({
        title: "¿Confirmar envío masivo?",
        html: `Se enviarán <b>${comprobantesPendientes.length}</b> comprobantes a SUNAT`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, Enviar Todos",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            iniciarEnvioMasivo();
        }
    });
}

// ============================================================
// INICIAR ENVÍO MASIVO
// ============================================================
function iniciarEnvioMasivo() {
    totalExitosos = 0;
    totalErrores = 0;
    
    $("#total_enviar").text(comprobantesPendientes.length);
    $("#total_exitosos").text(0);
    $("#total_errores").text(0);
    $("#barra_progreso").css("width", "0%").text("0%");
    $("#log_envios").html('<p><i class="fas fa-spinner fa-spin"></i> Iniciando envíos...</p>');
    $("#btn_cerrar_progreso").prop("disabled", true);
    
    $("#modal_progreso_envio").modal("show");
    
    procesarEnvioMasivo(0);
}

// ============================================================
// PROCESAR ENVÍO MASIVO (RECURSIVO)
// ============================================================
function procesarEnvioMasivo(indice) {
    if (indice >= comprobantesPendientes.length) {
        finalizarEnvioMasivo();
        return;
    }
    
    let comprobante = comprobantesPendientes[indice];
    let progreso = Math.round(((indice + 1) / comprobantesPendientes.length) * 100);
    
    $("#barra_progreso").css("width", progreso + "%").text(progreso + "%");
    
    agregarLog(`<i class="fas fa-paper-plane"></i> Enviando: <b>${comprobante.serie}-${comprobante.correlativo}</b>...`);
    
    $.ajax({
        url: "../controller/comprobante/controller_comprobante.php",
        type: "POST",
        data: {
            accion: "ENVIAR_SUNAT",
            id_comprobante: comprobante.id
        },
        dataType: "json"
    }).done(function(resp) {
        if (resp.status == "success") {
            totalExitosos++;
            agregarLog(`<i class="fas fa-check-circle text-success"></i> <b>${comprobante.serie}-${comprobante.correlativo}</b> - Aceptado`, "envio-exitoso");
        } else {
            totalErrores++;
            agregarLog(`<i class="fas fa-times-circle text-danger"></i> <b>${comprobante.serie}-${comprobante.correlativo}</b> - Error: ${resp.message}`, "envio-error");
        }
        
        $("#total_exitosos").text(totalExitosos);
        $("#total_errores").text(totalErrores);
        
        // Esperar 1 segundo entre envíos
        setTimeout(() => procesarEnvioMasivo(indice + 1), 1000);
    }).fail(function() {
        totalErrores++;
        agregarLog(`<i class="fas fa-times-circle text-danger"></i> <b>${comprobante.serie}-${comprobante.correlativo}</b> - Error de conexión`, "envio-error");
        $("#total_errores").text(totalErrores);
        
        setTimeout(() => procesarEnvioMasivo(indice + 1), 1000);
    });
}

// ============================================================
// FINALIZAR ENVÍO MASIVO
// ============================================================
function finalizarEnvioMasivo() {
    $("#btn_cerrar_progreso").prop("disabled", false);
    agregarLog(`<hr><b><i class="fas fa-flag-checkered"></i> Proceso completado</b>`);
    
    Swal.fire({
        icon: totalErrores === 0 ? "success" : "warning",
        title: "Envío masivo completado",
        html: `
            <b>Exitosos:</b> ${totalExitosos}<br>
            <b>Con errores:</b> ${totalErrores}
        `,
        showConfirmButton: true
    });
    
    listar_pendientes_envio();
    cargarResumen();
    listar_historial_envios();
}

// ============================================================
// AGREGAR LOG AL MODAL DE PROGRESO
// ============================================================
function agregarLog(mensaje, clase = "") {
    let log = $("#log_envios");
    log.append(`<p class="${clase}">${mensaje}</p>`);
    log.scrollTop(log[0].scrollHeight);
}

// ============================================================
// LISTAR HISTORIAL DE ENVÍOS
// ============================================================
function listar_historial_envios() {
    if (tbl_historial) tbl_historial.destroy();
    
    tbl_historial = $("#tabla_historial_envios").DataTable({
        "ordering": true,
        "bLengthChange": true,
        "searching": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "pageLength": 10,
        "destroy": true,
        "processing": true,
        "ajax": {
            "url": "../controller/comprobante/controller_comprobante.php",
            "type": "POST",
            "data": { accion: "LISTAR_HISTORIAL_ENVIOS" }
        },
        "columns": [
            { "data": "id_comprobante" },
            { 
                "data": "tipo_comprobante",
                "render": function(data) {
                    if (data == "01") return '<span class="badge badge-info">FACTURA</span>';
                    if (data == "03") return '<span class="badge badge-primary">BOLETA</span>';
                    if (data == "07") return '<span class="badge badge-warning">N. CRÉDITO</span>';
                    if (data == "08") return '<span class="badge badge-secondary">N. DÉBITO</span>';
                    return data;
                }
            },
            { 
                "data": null,
                "render": data => `<b>${data.numero_comprobante}</b>`
            },
            { 
                "data": "razon_social",
                "render": data => data || "-"
            },
            { 
                "data": "total",
                "render": data => "S/ " + parseFloat(data).toFixed(2)
            },
            { 
                "data": "fecha_envio_sunat",
                "render": data => data ? new Date(data).toLocaleString("es-PE") : "-"
            },
            { 
                "data": "estado_sunat",
                "render": function(data) {
                    if (data == "ENVIADO" || data == "ACEPTADO") 
                        return '<span class="badge badge-enviado"><i class="fas fa-check-circle"></i> ACEPTADO</span>';
                    if (data == "RECHAZADO") 
                        return '<span class="badge badge-rechazado"><i class="fas fa-times-circle"></i> RECHAZADO</span>';
                    return data;
                }
            },
            { 
                "data": "descripcion_respuesta_sunat",
                "render": data => data ? `<small class="text-muted">${data}</small>` : "-"
            },
            { 
                "data": "hash_cpe",
                "render": data => data ? `<code style="font-size:10px;">${data.substring(0, 20)}...</code>` : "-"
            },
            { 
                "data": null,
                "orderable": false,
                "render": function(data) {
                    let botones = `
                        <button class="btn btn-info btn-sm" onclick="verRespuestaSunat(${data.id_comprobante})" 
                                title="Ver Respuesta SUNAT">
                            <i class="fas fa-info-circle"></i>
                        </button>`;
                    
                    if (data.estado_sunat == "ENVIADO" || data.estado_sunat == "ACEPTADO") {
                        botones += `
                            <button class="btn btn-primary btn-sm" onclick="descargarXML('${data.serie}', '${data.correlativo}')" 
                                    title="Descargar XML">
                                <i class="fas fa-file-code"></i>
                            </button>
                            <button class="btn btn-secondary btn-sm" onclick="descargarCDR('${data.serie}', '${data.correlativo}')" 
                                    title="Descargar CDR">
                                <i class="fas fa-file-archive"></i>
                            </button>`;
                    }
                    
                    return botones;
                }
            }
        ],
        "language": { "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json" },
        "order": [[0, "desc"]]
    });
}

// ============================================================
// LISTAR HISTORIAL CON FILTROS
// ============================================================
function listar_historial_filtro() {
    let tipo = $("#select_tipo_historial").val();
    let estado = $("#select_estado_historial").val();
    let desde = $("#txt_fecha_desde_historial").val();
    let hasta = $("#txt_fecha_hasta_historial").val();
    
    if (tbl_historial) tbl_historial.destroy();
    
    tbl_historial = $("#tabla_historial_envios").DataTable({
        "ordering": true,
        "bLengthChange": true,
        "searching": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "pageLength": 10,
        "destroy": true,
        "processing": true,
        "ajax": {
            "url": "../controller/comprobante/controller_comprobante.php",
            "type": "POST",
            "data": {
                accion: "LISTAR_HISTORIAL_ENVIOS",
                tipo_comprobante: tipo,
                estado_sunat: estado,
                fecha_desde: desde,
                fecha_hasta: hasta
            }
        },
        "columns": [
            { "data": "id_comprobante" },
            { 
                "data": "tipo_comprobante",
                "render": function(data) {
                    if (data == "01") return '<span class="badge badge-info">FACTURA</span>';
                    if (data == "03") return '<span class="badge badge-primary">BOLETA</span>';
                    if (data == "07") return '<span class="badge badge-warning">N. CRÉDITO</span>';
                    if (data == "08") return '<span class="badge badge-secondary">N. DÉBITO</span>';
                    return data;
                }
            },
            { 
                "data": null,
                "render": data => `<b>${data.numero_comprobante}</b>`
            },
            { 
                "data": "razon_social",
                "render": data => data || "-"
            },
            { 
                "data": "total",
                "render": data => "S/ " + parseFloat(data).toFixed(2)
            },
            { 
                "data": "fecha_envio_sunat",
                "render": data => data ? new Date(data).toLocaleString("es-PE") : "-"
            },
            { 
                "data": "estado_sunat",
                "render": function(data) {
                    if (data == "ENVIADO" || data == "ACEPTADO") 
                        return '<span class="badge badge-enviado"><i class="fas fa-check-circle"></i> ACEPTADO</span>';
                    if (data == "RECHAZADO") 
                        return '<span class="badge badge-rechazado"><i class="fas fa-times-circle"></i> RECHAZADO</span>';
                    return data;
                }
            },
            { 
                "data": "descripcion_respuesta_sunat",
                "render": data => data ? `<small class="text-muted">${data}</small>` : "-"
            },
            { 
                "data": "hash_cpe",
                "render": data => data ? `<code style="font-size:10px;">${data.substring(0, 20)}...</code>` : "-"
            },
            { 
                "data": null,
                "orderable": false,
                "render": function(data) {
                    let botones = `
                        <button class="btn btn-info btn-sm" onclick="verRespuestaSunat(${data.id_comprobante})" 
                                title="Ver Respuesta SUNAT">
                            <i class="fas fa-info-circle"></i>
                        </button>`;
                    
                    if (data.estado_sunat == "ENVIADO" || data.estado_sunat == "ACEPTADO") {
                        botones += `
                            <button class="btn btn-primary btn-sm" onclick="descargarXML('${data.serie}', '${data.correlativo}')" 
                                    title="Descargar XML">
                                <i class="fas fa-file-code"></i>
                            </button>
                            <button class="btn btn-secondary btn-sm" onclick="descargarCDR('${data.serie}', '${data.correlativo}')" 
                                    title="Descargar CDR">
                                <i class="fas fa-file-archive"></i>
                            </button>`;
                    }
                    
                    return botones;
                }
            }
        ],
        "language": { "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json" },
        "order": [[0, "desc"]]
    });
}

// ============================================================
// VER RESPUESTA SUNAT
// ============================================================
function verRespuestaSunat(id) {
    $.ajax({
        url: "../controller/comprobante/controller_comprobante.php",
        type: "POST",
        data: {
            accion: "OBTENER_RESPUESTA_SUNAT",
            id_comprobante: id
        },
        dataType: "json"
    }).done(function(data) {
        if (data) {
            let html = `
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Comprobante:</th>
                            <td><b>${data.numero_comprobante}</b></td>
                        </tr>
                        <tr>
                            <th>Fecha Envío:</th>
                            <td>${data.fecha_envio_sunat || "-"}</td>
                        </tr>
                        <tr>
                            <th>Estado SUNAT:</th>
                            <td>
                                ${data.estado_sunat == "ACEPTADO" || data.estado_sunat == "ENVIADO" 
                                    ? '<span class="badge badge-success">ACEPTADO</span>' 
                                    : '<span class="badge badge-danger">RECHAZADO</span>'}
                            </td>
                        </tr>
                        <tr>
                            <th>Código Respuesta:</th>
                            <td>${data.codigo_respuesta_sunat || "-"}</td>
                        </tr>
                        <tr>
                            <th>Descripción:</th>
                            <td>${data.descripcion_respuesta_sunat || "-"}</td>
                        </tr>
                        <tr>
                            <th>Hash CPE:</th>
                            <td><code style="font-size:11px;">${data.hash_cpe || "-"}</code></td>
                        </tr>
                        <tr>
                            <th>Notas SUNAT:</th>
                            <td><small class="text-muted">${data.notas_sunat || "Sin notas"}</small></td>
                        </tr>
                    </table>
                </div>
            `;
            
            $("#contenido_respuesta_sunat").html(html);
            $("#modal_respuesta_sunat").modal("show");
        }
    }).fail(function() {
        Swal.fire("Error", "No se pudo obtener la respuesta de SUNAT", "error");
    });
}



// ============================================================
// DESCARGAR XML
// ============================================================
function descargarXML(serie, correlativo) {
    let url = "../greenter/xml/" + serie + "-" + correlativo + ".xml";
    window.open(url, "_blank");
}

// ============================================================
// DESCARGAR CDR
// ============================================================
function descargarCDR(serie, correlativo) {
    let url = "../greenter/cdr/R-" + serie + "-" + correlativo + ".zip";
    window.open(url, "_blank");
}

// ============================================================
// ACTUALIZAR LISTA
// ============================================================
function actualizarLista() {
    listar_pendientes_envio();
    cargarResumen();
    Swal.fire({
        icon: "success",
        title: "Lista actualizada",
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
}

// ============================================================
// VER DETALLE DEL COMPROBANTE (COMPLETO Y FUNCIONAL)
// ============================================================





function verDetallePendiente(id) {
    $.ajax({
        url: "../controller/comprobante/controller_comprobante.php",
        type: "POST",
        data: {
            accion: "OBTENER_COMPROBANTE",
            id_comprobante: id
        },
        dataType: "json"
    }).done(function (data) {
        if (data) {
            let html = `
                <div class="container-fluid px-3 py-2">

                    <!-- ENCABEZADO -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="text-primary fw-bold mb-0">
                                    <i class="fas fa-file-invoice"></i> ${data.tipo_comprobante == "01" ? "FACTURA" : "BOLETA"}
                                </h5>
                                <span class="badge bg-secondary fs-6">${data.numero_comprobante}</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4"><i class="far fa-calendar-alt text-muted"></i> <b>Fecha:</b> ${data.fecha_emision}</div>
                                <div class="col-md-4"><i class="fas fa-coins text-muted"></i> <b>Moneda:</b> ${data.moneda || "Soles"}</div>
                                <div class="col-md-4"><i class="fas fa-credit-card text-muted"></i> <b>Tipo de Pago:</b> ${data.tipo_pago_actual || "No especificado"}</div>
                            </div>
                        </div>
                    </div>

                    <!-- DATOS DEL CLIENTE -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-primary text-white py-2">
                            <i class="fas fa-user-circle"></i> <b>Datos del Cliente</b>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Razón Social:</strong><br>
                                    ${data.razon_social}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>N° Documento:</strong><br>
                                    ${data.numero_documento}
                                </div>
                                <div class="col-md-12 mb-2">
                                    <strong>Dirección:</strong><br>
                                    ${data.direccion || "-"}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RESUMEN DE MONTOS -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-success text-white py-2">
                            <i class="fas fa-cash-register"></i> <b>Resumen del Comprobante</b>
                        </div>
                        <div class="card-body p-3">
                            <div class="row text-center">
                                <div class="col-md-3 mb-2">
                                    <h6 class="text-muted mb-1">Base Gravada</h6>
                                    <span class="fw-bold">S/ ${parseFloat(data.total_gravada).toFixed(2)}</span>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <h6 class="text-muted mb-1">IGV</h6>
                                    <span class="fw-bold">S/ ${parseFloat(data.total_igv).toFixed(2)}</span>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <h6 class="text-muted mb-1">Descuento</h6>
                                    <span class="fw-bold">S/ ${(parseFloat(data.total_descuento) || 0).toFixed(2)}</span>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <h6 class="text-muted mb-1">Total</h6>
                                    <span class="fw-bold text-success fs-5">S/ ${parseFloat(data.total).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ESTADO SUNAT -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-info text-white py-2">
                            <i class="fas fa-paper-plane"></i> <b>Estado SUNAT</b>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <strong>Estado:</strong>
                                    <span class="badge ${data.estado_sunat === 'ACEPTADO' ? 'bg-success' : 'bg-danger'}">
                                        ${data.estado_sunat}
                                    </span>
                                </div>
                                ${data.fecha_envio_sunat ? `
                                    <div class="col-md-4 mb-2">
                                        <strong>Fecha Envío:</strong> ${data.fecha_envio_sunat}
                                    </div>` : ""}
                                ${data.descripcion_respuesta_sunat ? `
                                    <div class="col-md-12 mt-2">
                                        <strong>Respuesta SUNAT:</strong><br>
                                        <small class="text-muted">${data.descripcion_respuesta_sunat}</small>
                                    </div>` : ""}
                            </div>
                        </div>
                    </div>
            `;

            // DETALLE DE ÍTEMS
            if (data.detalles && data.detalles.length > 0) {
                html += `
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-warning text-dark py-2">
                            <i class="fas fa-list"></i> <b>Detalle de Ítems</b>
                        </div>
                        <div class="card-body p-3 table-responsive">
                            <table class="table table-hover table-bordered table-sm align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unit.</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                data.detalles.forEach((item, i) => {
                    html += `
                        <tr>
                            <td class="text-center">${i + 1}</td>
                            <td>${item.descripcion}</td>
                            <td class="text-center">${item.cantidad}</td>
                            <td class="text-end">S/ ${parseFloat(item.precio_unitario).toFixed(2)}</td>
                            <td class="text-end fw-bold">S/ ${parseFloat(item.subtotal).toFixed(2)}</td>
                        </tr>`;
                });
                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>`;
            }

            html += `</div>`;

            $("#contenido_detalle").html(html);
            $("#modal_detalle").modal("show");

        } else {
            Swal.fire("Advertencia", "No se encontró información del comprobante.", "warning");
        }
    }).fail(function () {
        Swal.fire("Error", "No se pudo obtener el detalle del comprobante.", "error");
    });
}

// declaracion SUNAT
function generarReporteSunat() {
    // Obtener filtros actuales
    let tipo = $("#select_tipo_historial").val();
    let estado = $("#select_estado_historial").val();
    let fecha_desde = $("#txt_fecha_desde_historial").val();
    let fecha_hasta = $("#txt_fecha_hasta_historial").val();
    
    // Validar fechas
    if (!fecha_desde || !fecha_hasta) {
        Swal.fire({
            icon: "warning",
            title: "Seleccione rango de fechas",
            text: "Debe especificar fecha desde y fecha hasta para generar el reporte",
            showConfirmButton: true
        });
        return;
    }
    
    // Confirmar generación
    Swal.fire({
        title: "¿Generar Reporte SUNAT?",
        html: `Se generará el reporte desde <b>${fecha_desde}</b> hasta <b>${fecha_hasta}</b>`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, Generar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: "Generando reporte...",
                html: "Por favor espere",
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => Swal.showLoading()
            });
            
            // Construir URL con parámetros
            let url = "../view/MPDF/REPORTE/reporte_declaracion_sunat.php";
            url += "?tipo=" + encodeURIComponent(tipo);
            url += "&estado=" + encodeURIComponent(estado);
            url += "&fecha_desde=" + encodeURIComponent(fecha_desde);
            url += "&fecha_hasta=" + encodeURIComponent(fecha_hasta);
            
            console.log("URL generada:", url); // Debug
            
            // Abrir en nueva ventana
            window.open(url, "_blank");
            
            // Cerrar loading después de 1 segundo
            setTimeout(() => {
                Swal.close();
                Swal.fire({
                    icon: "success",
                    title: "Reporte generado",
                    text: "El reporte se abrió en una nueva ventana",
                    timer: 2000,
                    showConfirmButton: false
                });
            }, 1000);
        }
    });
}

// ============================================================
// EXPORTAR A EXCEL (ALTERNATIVA)
// ============================================================
function exportarExcelSunat() {
    let tipo = $("#select_tipo_historial").val();
    let estado = $("#select_estado_historial").val();
    let fecha_desde = $("#txt_fecha_desde_historial").val();
    let fecha_hasta = $("#txt_fecha_hasta_historial").val();
    
    if (!fecha_desde || !fecha_hasta) {
        Swal.fire("Advertencia", "Seleccione rango de fechas", "warning");
        return;
    }
    
    window.location.href = "../controller/comprobante/exportar_excel_sunat.php" +
        "?tipo=" + tipo +
        "&estado=" + estado +
        "&fecha_desde=" + fecha_desde +
        "&fecha_hasta=" + fecha_hasta;
}