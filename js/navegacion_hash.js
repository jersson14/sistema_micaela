// ===== ARCHIVO: js/navegacion_hash.js =====
// Este archivo maneja la navegación basada en hash URLs

// Mapeo de módulos (ruta hash -> archivo PHP)
const MODULOS = {
// MODULOS PARA EL ADMINISTRADOR
  'servicios': 'servicios/view_servicios.php',
  'rutas': 'rutas/view_rutas.php',
  'choferes': 'choferes/view_choferes.php',
  'clientes': 'clientes/view_clientes.php',
  'reservas': 'reservas/view_reservas.php',
  'encomiendas': 'encomiendas/view_encomiendas.php',
  'nota_salida': 'tickets/view_nota_salida.php',
  'salidas': 'salidas_diaria/view_salida_diaria.php',
  'facturas': 'comprobantes/facturas.php',
  'comprobantes-lista': 'comprobantes/comprobantes_lista.php',
  'sunat': 'comprobantes/envios_sunat.php',
  'notas-credito': 'comprobantes/view_notas_credito.php',
  'notas-debito': 'comprobantes/view_notas_debito.php',
  'indicadores': 'indicadores/view_indicadores.php',
  'ingresos': 'ingresos/view_ingresos.php',
  'gastos': 'gastos/view_gastos.php',
  'archivadas': 'reportes/comprobantes_archivadas.php',
  'reporte-ingresos-gastos': 'reportes/reporte_ingresos_gastos.php',
  'reporte-servicios': 'reportes/reporte_servicios.php',
  'reporte-salidas': 'reportes/reporte_salidas.php',
  'reporte-clientes': 'reportes/reporte_clientes.php',
  'reporte-choferes': 'reportes/reporte_choferes.php',
  'reporte-encomiendas': 'reportes/reporte_encomiendas.php',
  'reporte-sunat': 'reportes/reporte_sunat.php',
  'usuarios': 'usuario/view_usuario.php',
  'roles': 'roles/view_roles.php',
  'tipo-pago': 'tipo_pago/view_tipo_pago.php',
  'sucursales': 'sucursales/view_sucursales.php',
  'configuracion': 'configuracion/view_config.php',

// MODULOS PARA EL ASISTENTE
  'conductores-asis': 'choferes/view_choferes_asis.php',
  'clientes-asis': 'clientes/view_clientes_asis.php',
  'reservas-asis': 'reservas/view_reservas_asi.php',
  'encomiendas-asis': 'encomiendas/view_encomienda_asis.php',
  'encomiendas-asis_envio': 'encomiendas/view_encomienda_env.php',
  'salidas-asis': 'salidas_diaria/view_salida_diaria_asis.php',

  // MODULOS PARA EL CONDUCTOR

  'salidas-con': 'salidas_diaria/view_salida_diaria_con.php',

};

// Función principal para cargar contenido por hash
function navegarPorHash(hash) {
  // Remover el # del hash
  const modulo = hash.replace('#', '').trim();
  
  // Si el hash está vacío, no hacer nada (mostrar dashboard)
  if (!modulo) {
    return;
  }
  
  // Buscar el módulo en el mapeo
  const ruta = MODULOS[modulo];
  
  if (ruta) {
    // Cargar el contenido
    cargarContenido('contenido_principal', ruta);
    // Marcar el item del menú como activo
    marcarMenuActivo(modulo);
  } else {
    console.warn('Módulo no encontrado:', modulo);
  }
}

// Función para marcar el menú activo
function marcarMenuActivo(modulo) {
  // Remover clase active de todos los items
  document.querySelectorAll('.nav-link').forEach(link => {
    link.classList.remove('active');
  });
  
  // Agregar clase active al link correspondiente
  const selector = `[data-modulo="${modulo}"]`;
  const activeLink = document.querySelector(selector);
  if (activeLink) {
    activeLink.classList.add('active');
  }
}

// Función wrapper para cargar contenido (la que ya tienes)
function cargarContenido(id, vista) {
  $("#" + id).load(vista);
}

// Escuchar cambios en el hash
window.addEventListener('hashchange', function() {
  navegarPorHash(window.location.hash);
});

// Al cargar la página, navegar según el hash actual
document.addEventListener('DOMContentLoaded', function() {
  const hashActual = window.location.hash;
  if (hashActual) {
    navegarPorHash(hashActual);
  }
});