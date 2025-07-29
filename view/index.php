<?php
session_start();
if (!isset($_SESSION['S_ID'])) {
  header('Location: ../index.php');
}
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TOURS MICAELA</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../plantilla/plugins//fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="icon" href="../img/logo.jpg" type="image/jpg">

  <link rel="stylesheet" href="../plantilla/dist//css/adminlte.min.css">
  <link href="../utilitario/DataTables/datatables.min.css" type="text/css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body class="">
  <div class="wrapper">
    <?php if ($_SESSION['S_ROL'] == "1") { ?>
      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
          </li>
        </ul>
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
          <!-- Notifications Dropdown Menu -->

          <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
              <img src="../<?php echo $_SESSION['S_FOTO']; ?>" class="img-circle elevation-1" width="15" height="18">
              <b>Usuario: <?php echo $_SESSION['S_COMPLETOS'] ?></b>
              <i class="fas fa-caret-down"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              <div class="dropdown-divider"></div>
              <a href="../controller/usuario/controlador_cerrar_sesion.php" class="dropdown-item">
                <i class="fas fa-power-off mr-2"></i><u><b>Cerrar Sesión</b></u>
              </a>
              <div class="dropdown-divider"></div>
            </div>
          </li>
        </ul>

      </nav>
    <?php
    }
    ?>
    <?php if ($_SESSION['S_ROL'] == "2") { ?>
      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
          </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
          <!-- Notifications Dropdown Menu -->


          <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
              <img src="../<?php echo $_SESSION['S_FOTO']; ?>" class="img-circle elevation-1" width="15" height="18">

              <b>Usuario: <?php echo $_SESSION['S_COMPLETOS'] ?></b>
              <i class="fas fa-caret-down"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              <div class="dropdown-divider"></div>
              <a href="../controller/usuario/controlador_cerrar_sesion.php" class="dropdown-item">
                <i class="fas fa-power-off mr-2"></i><u><b>Cerrar Sesión</b></u>
              </a>
              <div class="dropdown-divider"></div>
            </div>
          </li>
        </ul>

      </nav>
    <?php
    }
    ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="index.php" class="brand-link">
        <img src="../img/logo.jpg" alt="<?php echo $_SESSION['S_RAZON']; ?>" width="100%" height="auto">
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-1 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="../<?php echo $_SESSION['S_FOTO']; ?>" class="img-circle elevation-2" style="max-width: 100%;height: auto;">
          </div>
          <div class="info">
            <a style="text-align:center;" href="#" class="d-block"><i class="fa fa-circle text-success fa-0x"></i> ¡Hola!<br> <b style="color:white"><?php echo $_SESSION['S_NOMBRE']; ?></b></a>
            <a style="text-align:center;margin:5px;color:white;font-size:15px" href="#" class="d-block">&nbsp;&nbsp;<b style="text-align:center"><i class="fa fa-user text-success fa-0x"></i><em> ROL: <?php echo $_SESSION['S_NOMBRE_ROL']; ?></em></b></a>
          </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-1">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="header text-center" style="color:#FFFFFF; background-color:#023D77; border-radius: 10px;">
              <b>GESTIÓN DE VIAJES</b>
            </li>

            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <?php if ($_SESSION['S_ROL'] == "1") { ?>
              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','servicios/view_servicios.php')" class="nav-link">
                  <i class="nav-icon fas fa-concierge-bell"></i>
                  <p style="color:white">Servicios</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','choferes/view_choferes.php')" class="nav-link">
                  <i class="nav-icon fas fa-id-badge"></i>
                  <p style="color:white">Conductores</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','paciente/view_paciente.php')" class="nav-link">
                  <i class="nav-icon fas fa-user-friends"></i>
                  <p style="color:white">Gestión de clientes</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','practicas/view_practicas.php')" class="nav-link">
                  <i class="nav-icon fas fa-box"></i>
                  <p style="color:white">Encomiendas</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','practicas_paciente/view_practicas_paciente.php')" class="nav-link">
                  <i class="nav-icon fas fa-route"></i>
                  <p style="color:white">Salidas diarias</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-file-invoice-dollar"></i>
                  <p style="color:white">
                    Comprobantes de pago
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" onclick="cargar_contenido('contenido_principal','facturas/view_facturas.php')" class="nav-link">
                      <i class="nav-icon fas fa-file-invoice"></i>
                      <p style="color:white">Facturas</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" onclick="cargar_contenido('contenido_principal','boletas/view_boletas.php')" class="nav-link">
                      <i class="nav-icon fas fa-receipt"></i>
                      <p style="color:white">Boletas</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" onclick="cargar_contenido('contenido_principal','notas_credito/view_notas_credito.php')" class="nav-link">
                      <i class="nav-icon fas fa-file-alt"></i>
                      <p style="color:white">Notas de crédito</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" onclick="cargar_contenido('contenido_principal','notas_debito/view_notas_debito.php')" class="nav-link">
                      <i class="nav-icon fas fa-file-signature"></i>
                      <p style="color:white">Notas de débito</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" onclick="cargar_contenido('contenido_principal','sunat/envios_sunat.php')" class="nav-link">
                      <i class="nav-icon fas fa-paper-plane"></i>
                      <p style="color:white">Envíos a SUNAT</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" onclick="cargar_contenido('contenido_principal','consultas/view_consultas_comprobante.php')" class="nav-link">
                      <i class="nav-icon fas fa-search-dollar"></i>
                      <p style="color:white">Consultas de comprobantes</p>
                    </a>
                  </li>
                </ul>
              </li>


              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-wallet"></i>
                  <p style="color:white">
                    Ingresos y Gastos
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" onclick="cargar_contenido('contenido_principal','indicadores/view_indicadores.php')" class="nav-link">
                      <i class="nav-icon fas fa-chart-line"></i>
                      <p style="color:white">Indicadores</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" onclick="cargar_contenido('contenido_principal','ingresos/view_ingresos.php')" class="nav-link">
                      <i class="nav-icon fas fa-arrow-down"></i>
                      <p style="color:white">Ingresos</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" onclick="cargar_contenido('contenido_principal','gastos/view_gastos.php')" class="nav-link">
                      <i class="nav-icon fas fa-arrow-up"></i>
                      <p style="color:white">Gastos</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="header text-center" style="color:#FFFFFF; background-color:#023D77; border-radius: 10px;">
                <b>REPORTES</b>
              </li>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','facturas/view_facturas.php')" class="nav-link">
                  <i class="nav-icon fas fa-file-invoice-dollar"></i>
                  <p style="color:white">Gestión de Facturas</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','facturas/view_facturas_archivadas.php')" class="nav-link">
                  <i class="nav-icon fas fa-file-archive"></i>
                  <p style="color:white">Facturas Archivadas</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','reportes/reporte_ventas.php')" class="nav-link">
                  <i class="nav-icon fas fa-chart-bar"></i>
                  <p style="color:white">Reporte de Ventas</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','reportes/reporte_ingresos_gastos.php')" class="nav-link">
                  <i class="nav-icon fas fa-balance-scale"></i>
                  <p style="color:white">Ingresos vs Gastos</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','reportes/reporte_servicios.php')" class="nav-link">
                  <i class="nav-icon fas fa-concierge-bell"></i>
                  <p style="color:white">Servicios Prestados</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','reportes/reporte_salidas_diarias.php')" class="nav-link">
                  <i class="nav-icon fas fa-route"></i>
                  <p style="color:white">Salidas Diarias</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','reportes/reporte_clientes.php')" class="nav-link">
                  <i class="nav-icon fas fa-users"></i>
                  <p style="color:white">Reporte de Clientes</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','reportes/reporte_choferes.php')" class="nav-link">
                  <i class="nav-icon fas fa-id-badge"></i>
                  <p style="color:white">Reporte de Choferes</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','reportes/reporte_comprobantes_sunat.php')" class="nav-link">
                  <i class="nav-icon fas fa-cloud-upload-alt"></i>
                  <p style="color:white">Estado de Envío SUNAT</p>
                </a>
              </li>

              <li class="header text-center" style="color:#FFFFFF; background-color:#023D77; border-radius: 10px;">
                <b>CONFIGURACIÓN Y MANUAL</b>
              </li>

              <!-- Usuario y Roles -->
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-users-cog"></i>
                  <p style="color:white">
                    Usuario y Roles
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a onclick="cargar_contenido('contenido_principal','usuario/view_usuario.php')" class="nav-link">
                      <i class="fas fa-user"></i>
                      <p style="color:white">Usuarios</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a onclick="cargar_contenido('contenido_principal','roles/view_roles.php')" class="nav-link">
                      <i class="fas fa-user-shield"></i>
                      <p style="color:white">Roles</p>
                    </a>
                  </li>
                </ul>
              </li>

              <!-- Sucursales -->
              <li class="nav-item">
                <a onclick="cargar_contenido('contenido_principal','sucursales/view_sucursales.php')" class="nav-link">
                  <i class="nav-icon fas fa-store-alt"></i>
                  <p style="color:white">Sucursales</p>
                </a>
              </li>

              <!-- Configuración General -->
              <li class="nav-item">
                <a onclick="cargar_contenido('contenido_principal','configuracion/view_config.php')" class="nav-link">
                  <i class="nav-icon fas fa-cogs"></i>
                  <p style="color:white">Configuración General</p>
                </a>
              </li>

              <!-- Manual -->
              <li class="nav-item">
                <a href="../manual_admin.pdf" target="_blank" class="nav-link">
                  <i class="nav-icon fas fa-file-pdf"></i>
                  <p style="color:white">Manual de Usuario</p>
                </a>
              </li>


            <?php
            }
            ?>
            <?php if ($_SESSION['S_ROL'] == "FACTURA") { ?>


              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','facturas/view_facturas_solo.php')" class="nav-link">
                  <i class="nav-icon fas fa-file-invoice-dollar"></i>

                  <p style="color:white">
                    Gestión de Facturas
                  </p>
                </a>
              </li>

              <li class="header text-center" style="color:#FFFFFF; background-color:#023D77; border-radius: 10px;">
                <b>MANUAL</b>
              </li>
              <li class="nav-item">
                <a href="../manual_doctor.pdf" target="blank" onclick="" class="nav-link">
                  <i class="nav-icon fas fa-file-pdf"></i>
                  <p style="color:white">
                    Manual de Usuario
                  </p>
                </a>
              </li>

            <?php
            }
            ?>
            <?php if ($_SESSION['S_ROL'] == "2") { ?>

              <li class="nav-item">
                <a href="#" onclick="cargar_contenido('contenido_principal','facturas/view_facturas_solo.php')" class="nav-link">
                  <i class="nav-icon fas fa-stethoscope"></i>
                  <p style="color:white">
                    Prácticas - Paciente
                  </p>
                </a>
              </li>

              <li class="header text-center" style="color:#FFFFFF; background-color:#023D77; border-radius: 10px;">
                <b>MANUAL</b>
              </li>
              <li class="nav-item">
                <a href="../manual_doctor.pdf" target="blank" onclick="" class="nav-link">
                  <i class="nav-icon fas fa-file-pdf"></i>
                  <p style="color:white">
                    Manual de Usuario
                  </p>
                </a>
              </li>

            <?php
            }
            ?>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>
    <input type="text" id="txtprincipalid" value="<?php echo $_SESSION['S_ID']; ?>" hidden>
    <input type="text" id="txtprincipalusu" value="<?php echo $_SESSION['S_USU']; ?>" hidden>
    <input type="text" id="txtprincipalrol" value="<?php echo $_SESSION['S_ROL']; ?>" hidden>
    <input type="text" id="txtfotoempresa" value="<?php echo $_SESSION['S_FOTO_EMPRESA']; ?>" hidden>
    <input type="text" id="txtnombrerol" value="<?php echo $_SESSION['S_NOMBRE_ROL']; ?>" hidden>

    <input type="text" id="txtrazon" value="<?php echo $_SESSION['S_RAZON']; ?>" hidden>


    <div class="content-wrapper" id="contenido_principal">


      <!-- Content Wrapper. Contains page content -->

      <!-- Content Header (Page header) -->

      <?php if ($_SESSION['S_ROL'] == "1") { ?>
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-home"></i>
                  <b>BIENVENIDOS AL SISTEMA - TOURS MICAELA</b>
                </h1>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> INICIO</a></li>
                  <li class="breadcrumb-item active">DASHBOARD PRINCIPAL</li>
                </ol>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta de fecha y hora actual -->
        <div class="col-md-12 mb-4">
          <div class="card card-outline card-primary shadow-lg" style="border: none; border-radius: 15px; overflow: hidden;">
            <div class="card-body text-center py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative;">
              <!-- Efectos decorativos -->
              <div style="position: absolute; top: -50px; left: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%; animation: float 3s ease-in-out infinite;"></div>
              <div style="position: absolute; top: 20px; right: -30px; width: 60px; height: 60px; background: rgba(255,255,255,0.08); border-radius: 50%; animation: float 4s ease-in-out infinite reverse;"></div>
              <div style="position: absolute; bottom: -30px; left: 50%; width: 80px; height: 80px; background: rgba(255,255,255,0.05); border-radius: 50%; transform: translateX(-50%); animation: float 5s ease-in-out infinite;"></div>

              <!-- Contenido principal -->
              <div class="row align-items-center">
                <div class="col-md-5">
                  <div class="d-flex align-items-center justify-content-center">
                    <div class="text-center">
                      <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="far fa-calendar-alt text-white" style="font-size: 2.5rem;"></i>
                      </div>
                      <h3 class="text-white mb-1" style="font-weight: 600; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                        <span id="fecha_actual">Cargando fecha...</span>
                      </h3>
                      <p class="text-white-50 mb-0" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">
                        Fecha actual
                      </p>
                    </div>
                  </div>
                </div>

                <div class="col-md-2">
                  <div class="text-center">
                    <div style="width: 2px; height: 100px; background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.5), transparent); margin: 0 auto;"></div>
                  </div>
                </div>

                <div class="col-md-5">
                  <div class="d-flex align-items-center justify-content-center">
                    <div class="text-center">
                      <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="far fa-clock text-white" style="font-size: 2.5rem;"></i>
                      </div>
                      <h3 class="text-white mb-1" style="font-weight: 600; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); font-family: 'Courier New', monospace;">
                        <span id="hora_actual">00:00:00</span>
                      </h3>
                      <p class="text-white-50 mb-0" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">
                        Hora actual
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Información adicional -->
              <div class="row mt-4">
                <div class="col-12">
                  <div class="d-flex justify-content-center align-items-center" style="background: rgba(255,255,255,0.1); border-radius: 25px; padding: 10px 20px; backdrop-filter: blur(10px);">
                    <i class="fas fa-map-marker-alt text-white me-2" style="font-size: 1.2rem;"></i>
                    <span class="text-white" style="font-weight: 500; margin-left: 8px;">
                      Sistema de Transporte - Abancay, Apurímac
                    </span>
                    <span class="text-white-50 mx-3">|</span>
                    <i class="fas fa-user-tie text-white me-2" style="font-size: 1.2rem;"></i>
                    <span class="text-white" style="font-weight: 500; margin-left: 8px;">
                      Panel Administrativo
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Estilos CSS para animaciones -->
        <style>
          @keyframes float {

            0%,
            100% {
              transform: translateY(0px);
            }

            50% {
              transform: translateY(-10px);
            }
          }

          @keyframes pulse {

            0%,
            100% {
              opacity: 0.8;
            }

            50% {
              opacity: 1;
            }
          }

          .card-outline.card-primary {
            transition: all 0.3s ease;
          }

          .card-outline.card-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 123, 255, 0.3) !important;
          }

          #fecha_actual,
          #hora_actual {
            animation: pulse 2s infinite;
          }
        </style>

        <div class="col-md-12">
          <div class="card card-primary">
            <div class="card-header py-2" style="background: linear-gradient(135deg, #023D77, #0266C8)">
              <h5 class="m-0" style="font-family:cooper; text-align:center; line-height: 1; padding: 0;">
                <i class="fas fa-chart-pie" style="margin-right: 8px;"></i>PANEL DE CONTROL EJECUTIVO
              </h5>
              <div class="card-tools" style="position: absolute; right: 10px; top: 5px;">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="table-responsive" style="text-align:center">
              <div class="card-body" style="background-color:white">

                <!-- Sección de Servicios y Personal -->
                <div class="row mb-3">
                  <div class="col-12">
                    <h6 class="text-primary"><i class="fas fa-cogs"></i> <b>SERVICIOS Y PERSONAL</b></h6>
                    <hr style="border-top: 2px solid #007bff;">
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <b>Total de Servicios</b>
                        <h3 id="total_facturas"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-concierge-bell"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','servicios/view_servicios.php')" class="small-box-footer">
                        <b>Ver Servicios</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <b>Total de Choferes</b>
                        <h3 id="total_fact_pendiente"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-id-card"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','choferes/view_choferes.php')" class="small-box-footer">
                        <b>Ver Choferes</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <b>Total de Clientes</b>
                        <h3 id="total_fact_cobradas"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-users"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','clientes/view_clientes.php')" class="small-box-footer">
                        <b>Ver Clientes</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                      <div class="inner">
                        <b>Total de Comprobantes</b>
                        <h3 id="total_fact_rechazada"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-file-alt"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','comprobantes/view_comprobantes.php')" class="small-box-footer">
                        <b>Ver Comprobantes</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>
                </div>

                <!-- Sección de Encomiendas -->
                <div class="row mb-3 mt-4">
                  <div class="col-12">
                    <h6 class="text-success"><i class="fas fa-box"></i> <b>GESTIÓN DE ENCOMIENDAS</b></h6>
                    <hr style="border-top: 2px solid #28a745;">
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-dark">
                      <div class="inner">
                        <b>Encomiendas del Día</b>
                        <h3 id="total_practicas_paciente"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','encomiendas/view_encomiendas_dia.php')" class="small-box-footer">
                        <b>Ver del Día</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <b>Encomiendas Semanales</b>
                        <h3 id="total_practicas"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-calendar-week"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','encomiendas/view_encomiendas_semana.php')" class="small-box-footer">
                        <b>Ver Semanales</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <b>Encomiendas del Mes</b>
                        <h3 id="total_pacientes"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','encomiendas/view_encomiendas_mes.php')" class="small-box-footer">
                        <b>Ver del Mes</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <b>Total de Encomiendas</b>
                        <h3 id="total_obras_sociales"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-boxes"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','encomiendas/view_encomiendas.php')" class="small-box-footer">
                        <b>Ver Todas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>
                </div>

                <!-- Sección de Salidas/Rutas -->
                <div class="row mb-3 mt-4">
                  <div class="col-12">
                    <h6 class="text-warning"><i class="fas fa-route"></i> <b>CONTROL DE SALIDAS Y RUTAS</b></h6>
                    <hr style="border-top: 2px solid #ffc107;">
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-dark">
                      <div class="inner">
                        <b>Salidas del Día</b>
                        <h3 id="total_salidas_dia"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-truck-loading"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','salidas/view_salidas_dia.php')" class="small-box-footer">
                        <b>Ver Salidas del Día</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <b>Salidas Semanales</b>
                        <h3 id="total_salidas_semana"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-shipping-fast"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','salidas/view_salidas_semana.php')" class="small-box-footer">
                        <b>Ver Semanales</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <b>Salidas del Mes</b>
                        <h3 id="total_salidas_mes"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-map-marked-alt"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','salidas/view_salidas_mes.php')" class="small-box-footer">
                        <b>Ver del Mes</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <b>Total Salidas Diarias</b>
                        <h3 id="total_salidas_diarias"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-clock"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','salidas/view_salidas_programadas.php')" class="small-box-footer">
                        <b>Ver Programadas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>
                </div>

                <!-- Sección Financiera -->
                <div class="row mb-3 mt-4">
                  <div class="col-12">
                    <h6 class="text-success"><i class="fas fa-money-bill-wave"></i> <b>GESTIÓN FINANCIERA</b></h6>
                    <hr style="border-top: 2px solid #28a745;">
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <b>Ingresos de Hoy</b>
                        <h3 id="total_ingresos_hoy">S/. <span>0</span></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-cash-register"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','ingresos/view_ingresos.php')" class="small-box-footer">
                        <b>Ver Ingresos</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                      <div class="inner">
                        <b>Gastos de Hoy</b>
                        <h3 id="total_gastos_hoy">S/. <span>0</span></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-credit-card"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','gastos/view_gastos.php')" class="small-box-footer">
                        <b>Ver Gastos</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <b>Ingresos del Mes</b>
                        <h3 id="total_ingresos_mes_actual">S/. <span>0</span></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-chart-line"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','ingresos/view_ingresos.php')" class="small-box-footer">
                        <b>Ver Ingresos</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                      <div class="inner">
                        <b>Gastos del Mes</b>
                        <h3 id="total_gastos_mes_actual">S/. <span>0</span></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-chart-line-down"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','gastos/view_gastos.php')" class="small-box-footer">
                        <b>Ver Gastos</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>
                </div>

                <!-- Sección de Comprobantes -->
                <div class="row mb-3 mt-4">
                  <div class="col-12">
                    <h6 class="text-info"><i class="fas fa-file-invoice"></i> <b>COMPROBANTES ELECTRÓNICOS</b></h6>
                    <hr style="border-top: 2px solid #17a2b8;">
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <b>Facturas Emitidas</b>
                        <h3 id="total_facturas_emitidas"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','facturas/view_facturas.php')" class="small-box-footer">
                        <b>Ver Facturas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <b>Boletas Emitidas</b>
                        <h3 id="total_boletas_emitidas"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-receipt"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','boletas/view_boletas.php')" class="small-box-footer">
                        <b>Ver Boletas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <b>Notas de Crédito</b>
                        <h3 id="total_notas_credito"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-file-invoice"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','notas_credito/view_notas_credito.php')" class="small-box-footer">
                        <b>Ver N. Crédito</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                      <div class="inner">
                        <b>Notas de Débito</b>
                        <h3 id="total_notas_debito"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-file-contract"></i>
                      </div>
                      <a href="#" onclick="cargar_contenido('contenido_principal','notas_debito/view_notas_debito.php')" class="small-box-footer">
                        <b>Ver N. Débito</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
        <script>
          function actualizarFechaHora() {
            const ahora = new Date();
            const opciones = {
              weekday: 'long',
              year: 'numeric',
              month: 'long',
              day: 'numeric'
            };

            document.getElementById('fecha_actual').textContent = ahora.toLocaleDateString('es-ES', opciones);
            document.getElementById('hora_actual').textContent = ahora.toLocaleTimeString('es-ES');
          }
          // Actualizar cada segundo
          setInterval(actualizarFechaHora, 1000);
          actualizarFechaHora(); // Ejecutar inmediatamente
        </script>
        <!-- Script para actualizar fecha y hora -->


      <?php } ?>
      <?php if ($_SESSION['S_ROL'] == "2") { ?>

        <!-- Main content -->

        <div class="content">
          <div class="container-fluid">
            <div class="row justify-content-center">
              <div class="col-lg-10 text-center">
                <div class="p-5 rounded shadow-lg"
                  style="background: linear-gradient(135deg, #023D77, #0266C8); 
                    color: white; 
                    border-radius: 20px; 
                    margin-bottom: 20px;">
                  <h1 class="display-3 font-weight-bold" style="font-family: Cooper;">
                    ¡BIENVENIDO AL SISTEMA!
                  </h1>
                  <p class="lead" style="font-size: 1.3rem;">
                    Gestiona y revisa las prácticas realizadas a cada paciente de manera eficiente y organizada.
                  </p>
                  <hr class="my-4 border-light">
                  <a href="#seccion_practicas" onclick="cargar_contenido('contenido_principal','practicas_paciente/view_practicas_paciente2.php')" class="btn btn-light btn-lg font-weight-bold"

                    style="border-radius: 10px;">
                    <i class="fas fa-stethoscope"></i> Ver Prácticas
                  </a>
                </div>
              </div>
            </div> <!-- /.row -->
          </div> <!-- /.container-fluid -->
        </div> <!-- /.content -->



        <!-- /.content -->

        <!-- /.content-wrapper -->
      <?php
      }
      ?>
      <?php if ($_SESSION['S_ROL'] == "FACTURA") { ?>

        <!-- Main content -->

        <div class="content">
          <div class="container-fluid">
            <div class="row justify-content-center">
              <div class="col-lg-10 text-center">
                <div class="p-5 rounded shadow-lg"
                  style="background: linear-gradient(135deg, #023D77, #0266C8); 
                    color: white; 
                    border-radius: 20px; 
                    margin-bottom: 20px;">
                  <h1 class="display-3 font-weight-bold" style="font-family: Cooper;">
                    ¡BIENVENIDO AL SISTEMA!
                  </h1>
                  <p class="lead" style="font-size: 1.3rem;">
                    Gestiona y revisa las facturas realizadas a las obras sociales de manera eficiente y organizada.
                  </p>
                  <hr class="my-4 border-light">
                  <a href="#seccion_practicas" onclick="cargar_contenido('contenido_principal','facturas/view_facturas_solo.php')" class="btn btn-light btn-lg font-weight-bold"

                    style="border-radius: 10px;">
                    <i class="fas fa-stethoscope"></i> Ver Facturas
                  </a>
                </div>
              </div>
            </div> <!-- /.row -->
          </div> <!-- /.container-fluid -->
        </div> <!-- /.content -->


        <!-- /.content -->

        <!-- /.content-wrapper -->
      <?php
      }
      ?>
    </div>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
      <div class="p-3">
        <h5>Title</h5>
        <p>Sidebar content</p>
      </div>
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
      <!-- To the right -->
      <div class="float-right d-none d-sm-inline">
        <em>Versión 1.0.0</em>
      </div>
      <!-- Default to the left -->
      <strong>Copyright &copy; 2025 <a href="https://samicnestorkirchner.org/" target="_blank"><em> Empresa de Transportes Tours Micaela - "Llegamos a tu felicidad"</em></a></strong>
    </footer>
  </div>
  <!-- ./wrapper -->
  <!-- MODAL EDITAR HORARIO -->




  <!-- REQUIRED SCRIPTS -->
  <script>
    function cargar_contenido(id, vista) {
      $("#" + id).load(vista);
    }
    var idioma_espanol = {
      select: {
        rows: "%d fila seleccionada"
      },
      "sProcessing": "Procesando...",
      "sLengthMenu": "Mostrar _MENU_ registros",
      "sZeroRecords": "No se encontraron resultados",
      "sEmptyTable": "Ning&uacute;n dato disponible en esta tabla",
      "sInfo": "Registros del (_START_ al _END_) total de _TOTAL_ registros",
      "sInfoEmpty": "Registros del (0 al 0) total de 0 registros",
      "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
      "sInfoPostFix": "",
      "sSearch": "Buscar:",
      "sUrl": "",
      "sInfoThousands": ",",
      "sLoadingRecords": "<b>No se encontraron datos</b>",
      "oPaginate": {
        "sFirst": "Primero",
        "sLast": "Último",
        "sNext": "Siguiente",
        "sPrevious": "Atras"
      },
      "oAria": {
        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
      }
    }

    function sololetras(e) {
      key = e.keyCode || e.which;

      teclado = String.fromCharCode(key).toLowerCase();

      letras = "qwertyuiopasdfghjklñzxcvbnmáéíóú ";

      especiales = "8-37-38-46-164";

      teclado_especial = false;

      for (var i in especiales) {
        if (key == especiales[i]) {
          teclado_especial = true;
          break;
        }
      }

      if (letras.indexOf(teclado) == -1 && !teclado_especial) {
        return false;
      }
    }


    function soloNumeros(e) {
      tecla = (document.all) ? e.keyCode : e.which;
      if (tecla == 8) {
        return true;
      }
      // Patron de entrada, en este caso solo acepta numeros
      patron = /[0-9]/;
      tecla_final = String.fromCharCode(tecla);
      return patron.test(tecla_final);
    }



    ///////VALIDAR EMAIL
    function validar_email(email) {
      var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return regex.test(email) ? true : false;
    }
  </script>
  <!-- jQuery -->
  <script src="../plantilla/plugins//jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="../plantilla/plugins//bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../plantilla/dist/js/adminlte.min.js"></script>


  <script src="../utilitario/DataTables/datatables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="../js/console_usuario.js?rev=<?php echo time(); ?>"></script>

</body>

</html>
<script>
  $(document).ready(function() {
    Total_facturas();
    Total_facturas_pendientes();
    Total_facturas_cobradas();
    Total_facturas_rechazadas();
    Total_practicas_paciente();
    Total_practicas();
    Total_pacientes();
    Total_obras_sociales();

  });
</script>

<style>
  /* Color de fondo principal del aside con degradado */
  .main-sidebar {
    background: linear-gradient(135deg, #023D77, #0266C8) !important;
    color: white !important;
    /* Texto en blanco */
  }

  /* Eliminar cualquier color heredado del tema dark */
  .sidebar-dark-primary {
    background: linear-gradient(135deg, #023D77, #0266C8) !important;
    color: white !important;
  }

  /* Asegurar que la elevación no afecte el color */
  .elevation-4 {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
  }
</style>

<style>
  /* Estilos para la tabla con bordes redondeados */
  .table-bordered {
    border: 1px solid #dee2e6;
    border-radius: 10px;
  }

  /* Bordes redondeados para las celdas de las esquinas */
  .table-bordered thead th:first-child {
    border-top-left-radius: 10px;
  }

  .table-bordered thead th:last-child {
    border-top-right-radius: 10px;
  }

  .table-bordered tbody tr:last-child td:first-child {
    border-bottom-left-radius: 10px;
  }

  .table-bordered tbody tr:last-child td:last-child {
    border-bottom-right-radius: 10px;
  }

  /* Ajustes para mantener los bordes consistentes */
  .table-bordered th,
  .table-bordered td {
    border: 1px solid #dee2e6;
  }

  /* Estilos para el card contenedor */
  .card {
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }

  /* Ajuste para el responsive */
  .table-responsive {
    border-radius: 10px;
    overflow: hidden;
  }
</style>
<style>
  .carousel {
    position: relative;
    overflow: hidden;
  }

  .carousel-item {
    transition: transform 1s ease, opacity 1s ease;
  }


  .carousel-item.active {
    display: block;
  }
</style>