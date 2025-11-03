// ============================================================
// FACTURAS ARCHIVADAS
// ============================================================
var tbl_facturas_archivadas;

function listar_facturas_archivadas() {
    let tipo = $('#filtro_tipo_comprobante').val();
    let fecha_desde = $('#filtro_fecha_desde').val();
    let fecha_hasta = $('#filtro_fecha_hasta').val();

    if (tbl_facturas_archivadas) {
        tbl_facturas_archivadas.destroy();
    }

    tbl_facturas_archivadas = $("#tabla_facturas_archivadas").DataTable({
        ordering: true,
        order: [[8, 'desc']], // Ordenar por fecha de anulación descendente
        bLengthChange: true,
        searching: true,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        pageLength: 10,
        destroy: true,
        processing: true,
        responsive: true,
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>><"row"<"col-sm-12 text-right"B>>rtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-success btn-sm',
                title: 'Facturas Archivadas',
                filename: 'Facturas_Archivadas_' + new Date().toISOString().slice(0, 10),
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                titleAttr: 'Exportar a PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Facturas Archivadas',
                filename: 'Facturas_Archivadas_' + new Date().toISOString().slice(0, 10),
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                titleAttr: 'Imprimir',
                className: 'btn btn-info btn-sm',
                title: 'Facturas Archivadas',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            }
        ],
        ajax: {
            url: "../controller/reportes/controller_reportes.php",
            type: "POST",
            data: {
                accion: 'LISTAR_FACTURAS_ARCHIVADAS',
                tipo: tipo,
                fecha_desde: fecha_desde,
                fecha_hasta: fecha_hasta
            }
        },
        columns: [
            { data: 'id_comprobante' },
            {
                data: 'tipo_comprobante',
                render: function(data) {
                    if (data == '01') return '<span class="badge badge-info">FACTURA</span>';
                    if (data == '03') return '<span class="badge badge-primary">BOLETA</span>';
                    if (data == '07') return '<span class="badge badge-warning">N. CRÉDITO</span>';
                    if (data == '08') return '<span class="badge badge-secondary">N. DÉBITO</span>';
                    return data;
                }
            },
            {
                data: null,
                render: (data) => '<b>' + data.numero_comprobante + '</b>'
            },
            {
                data: 'fecha_emision',
                render: function(data) {
                    if (!data) return '-';
                    const partes = data.split('-');
                    return `${partes[2]}/${partes[1]}/${partes[0]}`;
                }
            },
            { data: 'razon_social' },
            { data: 'numero_documento' },
            {
                data: 'total',
                render: (data) => 'S/ ' + parseFloat(data).toFixed(2)
            },
            {
                data: 'estado_sunat',
                render: function(data) {
                    if (data == 'ANULADO')
                        return '<span class="badge badge-danger"><i class="fas fa-ban"></i> ANULADO</span>';
                    return '<span class="badge badge-secondary">' + data + '</span>';
                }
            },
            {
                data: 'fecha_anulacion',
                render: function(data) {
                    if (!data) return '-';
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-PE') + ' ' + fecha.toLocaleTimeString('es-PE');
                }
            },
            {
                data: 'motivo_anulacion',
                render: (data) => data ? '<small>' + data + '</small>' : '-'
            },
            { data: 'usuario_nombre' },
            {
                data: null,
                orderable: false,
                render: function(data) {
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm" onclick="verDetalleArchivado(${data.id_comprobante})" title="Ver Detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-secondary btn-sm" onclick="descargarPDF(${data.id_comprobante})" title="Descargar PDF">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
        }
    });
}

function verDetalleArchivado(id) {
    $.ajax({
        url: "../controller/comprobante/controller_comprobante.php",
        type: "POST",
        data: {
            accion: "OBTENER_COMPROBANTE",
            id_comprobante: id
        },
        dataType: "json",
        success: function(data) {
            if (data) {
                let html = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>COMPROBANTE ANULADO</strong><br>
                        <small>Motivo: ${data.motivo_anulacion || 'No especificado'}</small><br>
                        <small>Fecha: ${data.fecha_anulacion || '-'}</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Tipo:</strong> ${data.tipo_comprobante == '01' ? 'FACTURA' : 'BOLETA'}<br>
                            <strong>Número:</strong> ${data.numero_comprobante}<br>
                            <strong>Fecha Emisión:</strong> ${data.fecha_emision}
                        </div>
                        <div class="col-md-6">
                            <strong>Cliente:</strong> ${data.razon_social}<br>
                            <strong>Documento:</strong> ${data.numero_documento}<br>
                            <strong>Total:</strong> S/ ${parseFloat(data.total).toFixed(2)}
                        </div>
                    </div>
                `;
                
                $('#contenido_detalle_archivado').html(html);
                $('#modal_detalle_archivado').modal('show');
            }
        },
        error: function() {
            Swal.fire('Error', 'No se pudo obtener el detalle', 'error');
        }
    });
}

// ============================================================
// REPORTE INGRESOS VS GASTOS
// ============================================================
var tbl_ingresos_gastos;

function listar_ingresos_gastos() {
    let fecha_desde = $('#filtro_ingreso_fecha_desde').val();
    let fecha_hasta = $('#filtro_ingreso_fecha_hasta').val();
    
    if (!fecha_desde || !fecha_hasta) {
        return Swal.fire('Advertencia', 'Seleccione rango de fechas', 'warning');
    }

    $.ajax({
        url: "../controller/reportes/controller_reportes.php",
        type: "POST",
        data: {
            accion: 'REPORTE_INGRESOS_GASTOS',
            fecha_desde: fecha_desde,
            fecha_hasta: fecha_hasta
        },
        dataType: "json",
        success: function(response) {
            if (response.status === 'success') {
                mostrarGraficaIngresosGastos(response.data);
                llenarTablaResumen(response.data);
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'No se pudo generar el reporte', 'error');
        }
    });
}

function mostrarGraficaIngresosGastos(data) {
    const ctx = document.getElementById('grafica_ingresos_gastos').getContext('2d');
    
    // Destruir gráfica anterior si existe
    if (window.chartIngresosGastos) {
        window.chartIngresosGastos.destroy();
    }
    
    window.chartIngresosGastos = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Ingresos', 'Gastos', 'Balance'],
            datasets: [{
                label: 'Montos (S/)',
                data: [
                    parseFloat(data.total_ingresos),
                    parseFloat(data.total_gastos),
                    parseFloat(data.balance)
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    parseFloat(data.balance) >= 0 ? 'rgba(23, 162, 184, 0.7)' : 'rgba(255, 193, 7, 0.7)'
                ],
                borderColor: [
                    'rgb(40, 167, 69)',
                    'rgb(220, 53, 69)',
                    parseFloat(data.balance) >= 0 ? 'rgb(23, 162, 184)' : 'rgb(255, 193, 7)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Ingresos vs Gastos',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'S/ ' + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });
}

function llenarTablaResumen(data) {
    let html = `
        <table class="table table-bordered">
            <tr>
                <th class="bg-success text-white">Total Ingresos</th>
                <td class="text-right"><b>S/ ${parseFloat(data.total_ingresos).toFixed(2)}</b></td>
            </tr>
            <tr>
                <th class="bg-danger text-white">Total Gastos</th>
                <td class="text-right"><b>S/ ${parseFloat(data.total_gastos).toFixed(2)}</b></td>
            </tr>
            <tr>
                <th class="${parseFloat(data.balance) >= 0 ? 'bg-info' : 'bg-warning'} text-white">Balance</th>
                <td class="text-right">
                    <b class="${parseFloat(data.balance) >= 0 ? 'text-success' : 'text-danger'}">
                        S/ ${parseFloat(data.balance).toFixed(2)}
                    </b>
                </td>
            </tr>
        </table>
    `;
    
    $('#tabla_resumen_ingresos_gastos').html(html);
}

// ============================================================
// REPORTE SERVICIOS PRESTADOS
// ============================================================
function listar_servicios_prestados() {
    let fecha_desde = $('#filtro_servicio_fecha_desde').val();
    let fecha_hasta = $('#filtro_servicio_fecha_hasta').val();
    
    if (!fecha_desde || !fecha_hasta) {
        return Swal.fire('Advertencia', 'Seleccione rango de fechas', 'warning');
    }

    $.ajax({
        url: "../controller/reportes/controller_reportes.php",
        type: "POST",
        data: {
            accion: 'REPORTE_SERVICIOS_PRESTADOS',
            fecha_desde: fecha_desde,
            fecha_hasta: fecha_hasta
        },
        dataType: "json",
        success: function(response) {
            if (response.status === 'success') {
                llenarTablaServicios(response.data);
                mostrarGraficaServicios(response.data);
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }
    });
}

function llenarTablaServicios(data) {
    let html = '';
    let total_general = 0;
    let cantidad_total = 0;
    
    data.forEach(function(servicio) {
        total_general += parseFloat(servicio.total_vendido);
        cantidad_total += parseInt(servicio.cantidad_vendida);
        
        html += `
            <tr>
                <td>${servicio.nombre}</td>
                <td class="text-center">${servicio.cantidad_vendida}</td>
                <td class="text-right">S/ ${parseFloat(servicio.costo).toFixed(2)}</td>
                <td class="text-right"><b>S/ ${parseFloat(servicio.total_vendido).toFixed(2)}</b></td>
            </tr>
        `;
    });
    
    html += `
        <tr class="bg-light">
            <th>TOTAL</th>
            <th class="text-center">${cantidad_total}</th>
            <th></th>
            <th class="text-right">S/ ${total_general.toFixed(2)}</th>
        </tr>
    `;
    
    $('#tbody_servicios_prestados').html(html);
}

function mostrarGraficaServicios(data) {
    const ctx = document.getElementById('grafica_servicios').getContext('2d');
    
    if (window.chartServicios) {
        window.chartServicios.destroy();
    }
    
    const labels = data.map(s => s.nombre);
    const valores = data.map(s => parseFloat(s.total_vendido));
    
    window.chartServicios = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: valores,
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Distribución de Servicios'
                }
            }
        }
    });
}

// ============================================================
// REPORTE DE CLIENTES
// ============================================================
// (Ya incluido en la vista correspondiente)

// ============================================================
// REPORTE DE CHOFERES  
// ============================================================
// (Ya incluido en la vista correspondiente)

// ============================================================
// REPORTE ESTADO SUNAT
// ============================================================
// (Ya incluido en la vista correspondiente)

// ============================================================
// UTILIDADES GENERALES
// ============================================================
function descargarPDF(id) {
    window.open("../view/MPDF/REPORTE/pdf_comprobante.php?id=" + id, "_blank");
}

function formatearFecha(fecha) {
    if (!fecha) return '-';
    let partes = fecha.split('-');
    return `${partes[2]}/${partes[1]}/${partes[0]}`;
}

function formatearMoneda(monto) {
    return 'S/ ' + parseFloat(monto || 0).toFixed(2);
}

function mostrarCargando() {
    Swal.fire({
        title: 'Cargando...',
        text: 'Procesando información',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
}

function cerrarCargando() {
    Swal.close();
}