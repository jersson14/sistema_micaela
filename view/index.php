<?php
session_start();

// Verificar sesión tradicional
if (!isset($_SESSION['S_ID'])) {
  header('Location: ../index.php');
  exit;
}

// MODO PRUEBA: Verificar expiración de token JWT
if (isset($_SESSION['S_ID'])) {
  // Si hay sesión pero no hay token válido en el cliente, cerrar sesión
  // Esto se maneja desde JavaScript, pero agregamos verificación del lado del servidor
  
  // Verificar si la sesión tiene más de 2 horas
  if (!isset($_SESSION['LOGIN_TIME'])) {
    $_SESSION['LOGIN_TIME'] = time();
  }
  
  $tiempo_transcurrido = time() - $_SESSION['LOGIN_TIME'];
  $tiempo_maximo = 2 * 3600; // 2 horas
  
  if ($tiempo_transcurrido > $tiempo_maximo) {
    // Sesión expirada, cerrar
    session_destroy();
    header('Location: ../index.php?expired=1');
    exit;
  }
  
  // Actualizar tiempo de última actividad
  $_SESSION['LAST_ACTIVITY'] = time();
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
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap4.min.css">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../plantilla/plugins//fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="icon" href="../img/logito.png" type="image/jpg">

  <link rel="stylesheet" href="../plantilla/dist//css/adminlte.min.css">
  <link href="../utilitario/DataTables/datatables.min.css" type="text/css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
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
    <?php if ($_SESSION['S_ROL'] == "2" || $_SESSION['S_ROL'] == "5") { ?>
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
        <img src="../img/logito.png" alt="<?php echo $_SESSION['S_RAZON']; ?>" width="100%" height="auto">
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
            <a style="text-align:center;margin:5px;color:white;font-size:15px" href="#" class="d-block">
              &nbsp;&nbsp;
              <b style="text-align:center">
                <i class="fa fa-building text-info"></i>
                <em> SUCURSAL: <?php echo $_SESSION['S_SUCURSAL']; ?></em>
              </b>
            </a>

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
                <a href="#servicios" data-modulo="servicios" class="nav-link">
                  <i class="nav-icon fas fa-concierge-bell"></i>
                  <p style="color:white">Servicios</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#rutas" data-modulo="rutas" class="nav-link">
                  <i class="nav-icon fas fa-map-marked-alt"></i>
                  <p style="color:white">Rutas</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#choferes" data-modulo="choferes" class="nav-link">
                  <i class="nav-icon fas fa-id-badge"></i>
                  <p style="color:white">Conductores</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#clientes" data-modulo="clientes" class="nav-link">
                  <i class="nav-icon fas fa-user-friends"></i>
                  <p style="color:white">Gestión de clientes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#reservas" data-modulo="reservas" class="nav-link">
                  <i class="nav-icon fas fa-calendar-check"></i>
                  <p style="color:white">Reservas</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#encomiendas" data-modulo="encomiendas" class="nav-link">
                  <i class="nav-icon fas fa-box"></i>
                  <p style="color:white">Encomiendas</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#salidas" data-modulo="salidas" class="nav-link">
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
                    <a href="#facturas" data-modulo="facturas" class="nav-link">
                      <i class="nav-icon fas fa-file-invoice"></i>
                      <p style="color:white">Comprobantes</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#comprobantes-lista" data-modulo="comprobantes-lista" class="nav-link">
                      <i class="nav-icon fas fa-search-dollar"></i>
                      <p style="color:white">Consultas de comprobantes</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#sunat" data-modulo="sunat" class="nav-link">
                      <i class="nav-icon fas fa-paper-plane"></i>
                      <p style="color:white">Envíos a SUNAT</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#notas-credito" data-modulo="notas-credito" class="nav-link">
                      <i class="nav-icon fas fa-file-alt"></i>
                      <p style="color:white">Notas de crédito</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#notas-debito" data-modulo="notas-debito" class="nav-link">
                      <i class="nav-icon fas fa-file-signature"></i>
                      <p style="color:white">Notas de débito</p>
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
                    <a href="#indicadores" data-modulo="indicadores" class="nav-link">
                      <i class="nav-icon fas fa-chart-line"></i>
                      <p style="color:white">Indicadores</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#ingresos" data-modulo="ingresos" class="nav-link">
                      <i class="nav-icon fas fa-arrow-down"></i>
                      <p style="color:white">Ingresos</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#gastos" data-modulo="gastos" class="nav-link">
                      <i class="nav-icon fas fa-arrow-up"></i>
                      <p style="color:white">Gastos</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#"  class="nav-link">
                  <i class="nav-icon fas fa-map-marked-alt"></i>
                  <p style="color:white">GPS - Vehículos</p>
                </a>
              </li>
              <li class="header text-center" style="color:#FFFFFF; background-color:#023D77; border-radius: 10px;">
                <b>REPORTES</b>
              </li>
              <li class="nav-item">
                <a href="#archivadas" data-modulo="archivadas" class="nav-link">
                  <i class="nav-icon fas fa-file-archive"></i>
                  <p style="color:white">Facturas Archivadas</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="#reporte-ingresos-gastos" data-modulo="reporte-ingresos-gastos" class="nav-link">
                  <i class="nav-icon fas fa-balance-scale"></i>
                  <p style="color:white">Ingresos vs Gastos</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#reporte-servicios" data-modulo="reporte-servicios" class="nav-link">
                  <i class="nav-icon fas fa-concierge-bell"></i>
                  <p style="color:white">Servicios Prestados</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#reporte-salidas" data-modulo="reporte-salidas" class="nav-link">
                  <i class="nav-icon fas fa-route"></i>
                  <p style="color:white">Salidas Diarias</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#reporte-clientes" data-modulo="reporte-clientes" class="nav-link">
                  <i class="nav-icon fas fa-users"></i>
                  <p style="color:white">Reporte de Clientes</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#reporte-choferes" data-modulo="reporte-choferes" class="nav-link">
                  <i class="nav-icon fas fa-id-badge"></i>
                  <p style="color:white">Reporte de Choferes</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#reporte-encomiendas" data-modulo="reporte-encomiendas" class="nav-link">
                  <i class="nav-icon fas fa-id-badge"></i>
                  <p style="color:white">Reporte de Encomiendas</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#reporte-sunat" data-modulo="reporte-sunat" class="nav-link">
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
                    <a href="#usuarios" data-modulo="usuarios" class="nav-link">
                      <i class="fas fa-user"></i>
                      <p style="color:white">Usuarios</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#roles" data-modulo="roles" class="nav-link">
                      <i class="fas fa-user-shield"></i>
                      <p style="color:white">Roles</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#tipo-pago" data-modulo="tipo-pago" class="nav-link">
                  <i class="nav-icon fas fa-credit-card"></i>
                  <p style="color:white">Tipos de pago</p>
                </a>
              </li>

              <!-- Sucursales -->
              <li class="nav-item">
                <a href="#sucursales" data-modulo="sucursales" class="nav-link">
                  <i class="nav-icon fas fa-store-alt"></i>
                  <p style="color:white">Sucursales</p>
                </a>
              </li>

              <!-- Configuración General -->
              <li class="nav-item">
                <a href="#configuracion" data-modulo="configuracion" class="nav-link">
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
            <?php if ($_SESSION['S_ROL'] == "2") { ?>

              <li class="nav-item">
                <a href="#conductores-asis" data-modulo="conductores-asis" class="nav-link">
                  <i class="nav-icon fas fa-id-badge"></i>
                  <p style="color:white">Conductores</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#clientes-asis" data-modulo="clientes-asis" class="nav-link">
                  <i class="nav-icon fas fa-user-friends"></i>
                  <p style="color:white">Gestión de clientes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#reservas-asis" data-modulo="reservas-asis" class="nav-link">
                  <i class="nav-icon fas fa-calendar-check"></i>
                  <p style="color:white">Reservas</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-boxes"></i>
                  <p style="color:white">
                    Encomiendas
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                <a href="#encomiendas-asis" data-modulo="encomiendas-asis" class="nav-link">
                      <i class="nav-icon fas fa-inbox"></i>
                      <p style="color:white">Recibir encomienda</p>
                    </a>
                  </li>
                  <li class="nav-item">
                <a href="#encomiendas-asis_envio" data-modulo="encomiendas-asis_envio" class="nav-link">
                      <i class="nav-icon fas fa-shipping-fast"></i>
                      <p style="color:white">Enviar encomienda</p>
                    </a>
                  </li>
                </ul>
              </li>

              <li class="nav-item">
                <a href="#salidas-asis" data-modulo="salidas-asis" class="nav-link">
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
                    <a href="#facturas" data-modulo="facturas" class="nav-link">
                      <i class="nav-icon fas fa-file-invoice"></i>
                      <p style="color:white">Comprobantes</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#comprobantes-lista" data-modulo="comprobantes-lista" class="nav-link">
                      <i class="nav-icon fas fa-search-dollar"></i>
                      <p style="color:white">Consultas de comprobantes</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#sunat" data-modulo="sunat" class="nav-link">
                      <i class="nav-icon fas fa-paper-plane"></i>
                      <p style="color:white">Envíos a SUNAT</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#notas-credito" data-modulo="notas-credito" class="nav-link">
                      <i class="nav-icon fas fa-file-alt"></i>
                      <p style="color:white">Notas de crédito</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#notas-debito" data-modulo="notas-debito" class="nav-link">
                      <i class="nav-icon fas fa-file-signature"></i>
                      <p style="color:white">Notas de débito</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#"  class="nav-link">
                  <i class="nav-icon fas fa-map-marked-alt"></i>
                  <p style="color:white">GPS - Vehículos</p>
                </a>
              </li>

              <li class="header text-center" style="color:#FFFFFF; background-color:#023D77; border-radius: 10px;">
                <b>REPORTES</b>
              </li>
              <li class="nav-item">
                <a href="#archivadas" data-modulo="archivadas" class="nav-link">
                  <i class="nav-icon fas fa-file-archive"></i>
                  <p style="color:white">Facturas Archivadas</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="#reporte-servicios" data-modulo="reporte-servicios" class="nav-link">
                  <i class="nav-icon fas fa-concierge-bell"></i>
                  <p style="color:white">Servicios Prestados</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#reporte-salidas" data-modulo="reporte-salidas" class="nav-link">
                  <i class="nav-icon fas fa-route"></i>
                  <p style="color:white">Salidas Diarias</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#reporte-clientes" data-modulo="reporte-clientes" class="nav-link">
                  <i class="nav-icon fas fa-users"></i>
                  <p style="color:white">Reporte de Clientes</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#reporte-choferes" data-modulo="reporte-choferes" class="nav-link">
                  <i class="nav-icon fas fa-id-badge"></i>
                  <p style="color:white">Reporte de Choferes</p>
                </a>
              </li>




              <li class="header text-center" style="color:#FFFFFF; background-color:#023D77; border-radius: 10px;">
                <b>MANUAL</b>
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
            <?php if ($_SESSION['S_ROL'] == "5") { ?>


              <li class="nav-item">
                <a href="#salidas-con" data-modulo="salidas-con" class="nav-link">
                  <i class="nav-icon fas fa-route"></i>
                  <p style="color:white">Salidas diarias</p>
                </a>
              </li>


              <li class="header text-center" style="color:#FFFFFF; background-color:#023D77; border-radius: 10px;">
                <b>MANUAL</b>
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
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>
    <input type="text" id="txtprincipalid" value="<?php echo $_SESSION['S_ID']; ?>" hidden>
    <input type="text" id="txtDNIusuario" value="<?php echo $_SESSION['S_DNIUSUARIO']; ?>" hidden>
    <input type="text" id="txtprincipalusu" value="<?php echo $_SESSION['S_USU']; ?>" hidden>
    <input type="text" id="txtprincipalrol" value="<?php echo $_SESSION['S_ROL']; ?>" hidden>
    <input type="text" id="txtfotoempresa" value="<?php echo $_SESSION['S_FOTO_EMPRESA']; ?>" hidden>
    <input type="text" id="txtnombrerol" value="<?php echo $_SESSION['S_NOMBRE_ROL']; ?>" hidden>
    <input type="text" id="txtrazon" value="<?php echo $_SESSION['S_RAZON']; ?>" hidden>
    <input type="text" id="txt_sucursal" value="<?php echo $_SESSION['S_SUCURSAL']; ?>" hidden>


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
                      Sistema de Transporte - Abancay - Cusco y Viceversa
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
                        <h3 id="total_servicios"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-concierge-bell"></i>
                      </div>
                      <a href="#servicios" data-modulo="servicios" class="small-box-footer">
                        <b>Ver Servicios</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <b>Total de Choferes</b>
                        <h3 id="total_choferes"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-id-card"></i>
                      </div>
                      <a href="#choferes" data-modulo="choferes" class="small-box-footer">
                        <b>Ver Choferes</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <b>Total de Clientes</b>
                        <h3 id="total_clientes"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-users"></i>
                      </div>
                      <a href="#clientes" data-modulo="clientes" class="small-box-footer">
                        <b>Ver Clientes</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>
                  <!-- FALTA TOTAL COMPROBANTES -->
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                      <div class="inner">
                        <b>Total de Comprobantes</b>
                        <h3 id="total_comprobantes"><sup style="font-size: 20px"></sup>0</h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-file-alt"></i>
                      </div>
                      <a href="#comprobantes-lista" data-modulo="comprobantes-lista" class="small-box-footer">
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
                        <h3 id="total_encomiendas_dia"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-box"></i>
                      </div>
                      <a href="#encomiendas" data-modulo="encomiendas" class="small-box-footer">
                        <b>Ver encomiendas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <b>Encomiendas Semanales</b>
                        <h3 id="total_encomiendas_semanales"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-box"></i>
                      </div>
                      <a href="#encomiendas" data-modulo="encomiendas" class="small-box-footer">
                        <b>Ver encomiendas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <b>Encomiendas del Mes</b>
                        <h3 id="total_encomiendas_mes"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-box"></i>
                      </div>
                      <a href="#encomiendas" data-modulo="encomiendas" class="small-box-footer">
                        <b>Ver encomiendas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <b>Total de Encomiendas</b>
                        <h3 id="total_encomiendas"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-box"></i>
                      </div>
                      <a href="#encomiendas" data-modulo="encomiendas" class="small-box-footer">
                        <b>Ver encomiendas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
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
                        <i class="fas fa-route"></i>
                      </div>
                      <a href="#salidas" data-modulo="salidas" class="small-box-footer">
                        <b>Ver salidas diarias</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
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
                        <i class="fas fa-route"></i>
                      </div>
                      <a href="#salidas" data-modulo="salidas" class="small-box-footer">
                        <b>Ver salidas diarias</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
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
                        <i class="fas fa-route"></i>
                      </div>
                      <a href="#salidas" data-modulo="salidas" class="small-box-footer">
                        <b>Ver salidas diarias</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
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
                        <i class="fas fa-route"></i>
                      </div>
                      <a href="#salidas" data-modulo="salidas" class="small-box-footer">
                        <b>Ver salidas diarias</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>
                </div>
                <!-- Sección de Reservas -->
                <div class="row mb-3 mt-4">
                  <div class="col-12">
                    <h6 class="text-danger"><i class="fas fa-route"></i> <b>CONTROL DE RESERVAS</b></h6>
                    <hr style="border-top: 2px solid #df1616ff;">
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-dark">
                      <div class="inner">
                        <b>Reservas pendientes del Día</b>
                        <h3 id="total_reservas_pendientes_dia"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                      </div>
                      <a href="#reservas" data-modulo="reservas" class="small-box-footer">
                        <b>Ver reservas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <b>Total Reservas Semanales</b>
                        <h3 id="total_reservas_semana"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                      </div>
                      <a href="#reservas" data-modulo="reservas" class="small-box-footer">
                        <b>Ver reservas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <b>Total Reservas del Mes</b>
                        <h3 id="total_reservas_mes"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                      </div>
                      <a href="#reservas" data-modulo="reservas" class="small-box-footer">
                        <b>Ver reservas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <b>Total Reservas</b>
                        <h3 id="total_reservas"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                      </div>
                      <a href="#reservas" data-modulo="reservas" class="small-box-footer">
                        <b>Ver reservas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
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
                        <h3 id="total_ingresos_hoy"><span></span></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-arrow-down"></i>
                      </div>
                      <a href="#ingresos" data-modulo="ingresos" class="small-box-footer">
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
                        <i class="fas fa-arrow-up"></i>
                      </div>
                      <a href="#gastos" data-modulo="gastos" class="small-box-footer">
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
                        <i class="fas fa-arrow-down"></i>
                      </div>
                      <a href="#ingresos" data-modulo="ingresos" class="small-box-footer">
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
                        <i class="fas fa-arrow-up"></i>
                      </div>
                      <a href="#gastos" data-modulo="gastos" class="small-box-footer">
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
                        <h3 id="total_facturas_emitidas"><sup style="font-size: 20px"></sup>0</h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                      </div>
                      <a href="#comprobantes-lista" data-modulo="comprobantes-lista" class="small-box-footer">
                        <b>Ver Facturas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <b>Boletas Emitidas</b>
                        <h3 id="total_boletas_emitidas"><sup style="font-size: 20px"></sup>0</h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-receipt"></i>
                      </div>
                      <a href="#comprobantes-lista" data-modulo="comprobantes-lista" class="small-box-footer">
                        <b>Ver Boletas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <b>Notas de Crédito</b>
                        <h3 id="total_notas_credito"><sup style="font-size: 20px"></sup>0</h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-file-invoice"></i>
                      </div>
                      <a href="#notas-credito" data-modulo="notas-credito" class="small-box-footer">
                        <b>Ver N. Crédito</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                      <div class="inner">
                        <b>Notas de Débito</b>
                        <h3 id="total_notas_debito"><sup style="font-size: 20px"></sup>0</h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-file-contract"></i>
                      </div>
                      <a href="#notas-debito" data-modulo="notas-debito" class="small-box-footer">
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

        <div class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <div class="card card-primary">
                  <div class="card-header">
                    <h5 class="m-0" style="font-family:cooper;text-align:center">
                      <i class="fas fa-bullhorn"></i><b> VENCIMIENTO DE LICENCIAS</b>
                    </h5>
                  </div>
                  <div class="table-responsive" style="text-align:center">
                    <div class="card-body" style="overflow: hidden; border-radius: 20px;">
                      <table id="tabla_choferes_vencidos" class="table table-striped table-bordered" style="width:100%; border-radius: 20px; overflow: hidden;">
                        <thead style="background-color:#023D77;color:white;">
                          <tr>
                            <th style="text-align:center">Nro.</th>
                            <th style="text-align:center">Tipo Doc y N°</th>
                            <th style="text-align:center">Conductor</th>
                            <th style="text-align:center">Vehículo</th>
                            <th style="text-align:center">Licencia</th>
                            <th style="text-align:center">Categoría</th>
                            <th style="text-align:center">Fecha de vencimiento</th>
                            <th style="text-align:center">Acción</th>
                            <th style="text-align:center">Estado</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Mejorado de Alertas de Vencimiento -->
        <div class="modal fade" id="modal_ver" tabindex="-1" role="dialog" aria-labelledby="modalAlertaLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
          <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.3); overflow: hidden;">
              <!-- El contenido se genera dinámicamente desde JavaScript -->
            </div>
          </div>
        </div>

        <style>
          /* Estilos adicionales para el modal */
          #modal_ver .modal-dialog {
            max-width: 700px;
          }

          #modal_ver .modal-content {
            animation: modalSlideIn 0.3s ease-out;
          }

          @keyframes modalSlideIn {
            from {
              transform: translateY(-50px);
              opacity: 0;
            }

            to {
              transform: translateY(0);
              opacity: 1;
            }
          }

          /* Hover effect para las filas de la tabla */
          #tabla_choferes_vencidos tbody tr {
            transition: all 0.3s ease;
            cursor: pointer;
          }

          #tabla_choferes_vencidos tbody tr:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
          }

          /* Estilos para el botón Ver */
          .mostrar {
            transition: all 0.3s ease;
            border: none;
            font-weight: bold;
          }

          .mostrar:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
          }
        </style>

      <?php } ?>
      <?php if ($_SESSION['S_ROL'] == "2") { ?>

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
                      Sistema de Transporte - Abancay - Cusco y Viceversa
                    </span>
                    <span class="text-white-50 mx-3">|</span>
                    <i class="fas fa-user-tie text-white me-2" style="font-size: 1.2rem;"></i>
                    <span class="text-white" style="font-weight: 500; margin-left: 8px;">
                      Panel Asistente
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
                <i class="fas fa-chart-pie" style="margin-right: 8px;"></i>PANEL DE CONTROL EJECUTIVO POR SUCURSAL
              </h5>
              <div class="card-tools" style="position: absolute; right: 10px; top: 5px;">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="table-responsive" style="text-align:center">
              <div class="card-body" style="background-color:white">



                <!-- Sección de Encomiendas -->
                <div class="row mb-3 mt-4">
                  <div class="col-12">
                    <h6 class="text-success"><i class="fas fa-box"></i> <b>GESTIÓN DE ENVIO DE ENCOMIENDAS</b></h6>
                    <hr style="border-top: 2px solid #28a745;">
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-dark">
                      <div class="inner">
                        <b>Encomiendas del Día</b>
                        <h3 id="total_encomiendas_dia_asis"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-box"></i>
                      </div>
                      <a href="#encomiendas-asis" data-modulo="encomiendas-asis" class="small-box-footer">
                        <b>Ver encomiendas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <b>Encomiendas Semanales</b>
                        <h3 id="total_encomiendas_semanales_asis"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-box"></i>
                      </div>
                      <a href="#encomiendas-asis" data-modulo="encomiendas-asis" class="small-box-footer">
                        <b>Ver encomiendas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <b>Encomiendas del Mes</b>
                        <h3 id="total_encomiendas_mes_asis"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-box"></i>
                      </div>
                      <a href="#encomiendas-asis" data-modulo="encomiendas-asis" class="small-box-footer">
                        <b>Ver encomiendas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <b>Total de Encomiendas</b>
                        <h3 id="total_encomiendas_asis"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-box"></i>
                      </div>
                      <a href="#encomiendas-asis" data-modulo="encomiendas-asis" class="small-box-footer">
                        <b>Ver encomiendas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
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
                        <h3 id="total_salidas_dia_asis"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-route"></i>
                      </div>
                      <a href="#salidas-asis" data-modulo="salidas-asis" class="small-box-footer">
                        <b>Ver salidas diarias</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <b>Salidas Semanales</b>
                        <h3 id="total_salidas_semana_asis"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-route"></i>
                      </div>
                      <a href="#salidas-asis" data-modulo="salidas-asis" class="small-box-footer">
                        <b>Ver salidas diarias</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <b>Salidas del Mes</b>
                        <h3 id="total_salidas_mes_asis"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-route"></i>
                      </div>
                      <a href="#salidas-asis" data-modulo="salidas-asis" class="small-box-footer">
                        <b>Ver salidas diarias</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <b>Total Salidas Diarias</b>
                        <h3 id="total_salidas_diarias_asis"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-route"></i>
                      </div>
                      <a href="#salidas-asis" data-modulo="salidas-asis" class="small-box-footer">
                        <b>Ver salidas diarias</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>
                </div>

                <!-- Sección de Reservas -->
                <div class="row mb-3 mt-4">
                  <div class="col-12">
                    <h6 class="text-danger"><i class="fas fa-route"></i> <b>CONTROL DE RESERVAS</b></h6>
                    <hr style="border-top: 2px solid #df1616ff;">
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-dark">
                      <div class="inner">
                        <b>Reservas pendientes del Día</b>
                        <h3 id="total_reservas_pendientes_dia_asis"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                      </div>
                      <a href="#reservas-asis" data-modulo="reservas-asis" class="small-box-footer">
                        <b>Ver reservas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <b>Total Reservas Semanales</b>
                        <h3 id="total_reservas_semana_asis"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                      </div>
                      <a href="#reservas-asis" data-modulo="reservas-asis" class="small-box-footer">
                        <b>Ver reservas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <b>Total Reservas del Mes</b>
                        <h3 id="total_reservas_mes_asis"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                      </div>
                      <a href="#reservas-asis" data-modulo="reservas-asis" class="small-box-footer">
                        <b>Ver reservas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <b>Total Reservas</b>
                        <h3 id="total_reservas_asis"><sup style="font-size: 20px"></sup></h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                      </div>
                      <a href="#reservas-asis" data-modulo="reservas-asis" class="small-box-footer">
                        <b>Ver reservas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
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
                        <b>Facturas Emitidas por Sucursal</b>
                        <h3 id="total_facturas_emitidas_sucu"><sup style="font-size: 20px"></sup>0</h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                      </div>
                      <a href="#comprobantes-lista" data-modulo="comprobantes-lista" class="small-box-footer">
                        <b>Ver Facturas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <b>Boletas Emitidas por Sucursal</b>
                        <h3 id="total_boletas_emitidas_sucu"><sup style="font-size: 20px"></sup>0</h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-receipt"></i>
                      </div>
                      <a href="#comprobantes-lista" data-modulo="comprobantes-lista" class="small-box-footer">
                        <b>Ver Boletas</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <b>Notas de Crédito por Sucursal</b>
                        <h3 id="total_notas_credito_sucu"><sup style="font-size: 20px"></sup>0</h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-file-invoice"></i>
                      </div>
                      <a href="#notas-credito" data-modulo="notas-credito" class="small-box-footer">
                        <b>Ver N. Crédito</b>&nbsp;<i class="fas fa-arrow-circle-right"></i>
                      </a>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                      <div class="inner">
                        <b>Notas de Débito por Sucursal</b>
                        <h3 id="total_notas_debito_sucu"><sup style="font-size: 20px"></sup>0</h3>
                      </div>
                      <div class="icon">
                        <i class="fas fa-file-contract"></i>
                      </div>
                      <a href="#notas-debito" data-modulo="notas-debito" class="small-box-footer">
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
        <div class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <div class="card card-primary">
                  <div class="card-header">
                    <h5 class="m-0" style="font-family:cooper;text-align:center">
                      <i class="fas fa-bullhorn"></i><b> VENCIMIENTO DE LICENCIAS</b>
                    </h5>
                  </div>
                  <div class="table-responsive" style="text-align:center">
                    <div class="card-body" style="overflow: hidden; border-radius: 20px;">
                      <table id="tabla_choferes_vencidos" class="table table-striped table-bordered" style="width:100%; border-radius: 20px; overflow: hidden;">
                        <thead style="background-color:#023D77;color:white;">
                          <tr>
                            <th style="text-align:center">Nro.</th>
                            <th style="text-align:center">Tipo Doc y N°</th>
                            <th style="text-align:center">Conductor</th>
                            <th style="text-align:center">Vehículo</th>
                            <th style="text-align:center">Licencia</th>
                            <th style="text-align:center">Categoría</th>
                            <th style="text-align:center">Fecha de vencimiento</th>
                            <th style="text-align:center">Acción</th>
                            <th style="text-align:center">Estado</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Mejorado de Alertas de Vencimiento -->
        <div class="modal fade" id="modal_ver" tabindex="-1" role="dialog" aria-labelledby="modalAlertaLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
          <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.3); overflow: hidden;">
              <!-- El contenido se genera dinámicamente desde JavaScript -->
            </div>
          </div>
        </div>

        <style>
          /* Estilos adicionales para el modal */
          #modal_ver .modal-dialog {
            max-width: 700px;
          }

          #modal_ver .modal-content {
            animation: modalSlideIn 0.3s ease-out;
          }

          @keyframes modalSlideIn {
            from {
              transform: translateY(-50px);
              opacity: 0;
            }

            to {
              transform: translateY(0);
              opacity: 1;
            }
          }

          /* Hover effect para las filas de la tabla */
          #tabla_choferes_vencidos tbody tr {
            transition: all 0.3s ease;
            cursor: pointer;
          }

          #tabla_choferes_vencidos tbody tr:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
          }

          /* Estilos para el botón Ver */
          .mostrar {
            transition: all 0.3s ease;
            border: none;
            font-weight: bold;
          }

          .mostrar:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
          }
        </style>

      <?php
      }
      ?>
      <?php if ($_SESSION['S_ROL'] == "5") { ?>

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

        <!-- Tarjeta de fecha, hora y botón de registro -->
        <div class="col-md-12 mb-4">
          <div class="card card-outline card-primary shadow-lg" style="border: none; border-radius: 15px; overflow: hidden;">
            <div class="card-body text-center py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative;">
              <!-- Efectos decorativos -->
              <div style="position: absolute; top: -50px; left: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%; animation: float 3s ease-in-out infinite;"></div>
              <div style="position: absolute; top: 20px; right: -30px; width: 60px; height: 60px; background: rgba(255,255,255,0.08); border-radius: 50%; animation: float 4s ease-in-out infinite reverse;"></div>
              <div style="position: absolute; bottom: -30px; left: 50%; width: 80px; height: 80px; background: rgba(255,255,255,0.05); border-radius: 50%; transform: translateX(-50%); animation: float 5s ease-in-out infinite;"></div>

              <!-- Contenido principal -->
              <div class="row align-items-center">
                <div class="col-md-4">
                  <div class="d-flex align-items-center justify-content-center">
                    <div class="text-center">
                      <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                        <i class="far fa-calendar-alt text-white" style="font-size: 2rem;"></i>
                      </div>
                      <h4 class="text-white mb-1" style="font-weight: 600; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); font-size: 1.3rem;">
                        <span id="fecha_actual">Cargando fecha...</span>
                      </h4>
                      <p class="text-white-50 mb-0" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
                        Fecha actual
                      </p>
                    </div>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="d-flex align-items-center justify-content-center">
                    <div class="text-center">
                      <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                        <i class="far fa-clock text-white" style="font-size: 2rem;"></i>
                      </div>
                      <h4 class="text-white mb-1" style="font-weight: 600; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); font-family: 'Courier New', monospace; font-size: 1.3rem;">
                        <span id="hora_actual">00:00:00</span>
                      </h4>
                      <p class="text-white-50 mb-0" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
                        Hora actual
                      </p>
                    </div>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="d-flex align-items-center justify-content-center">
                    <div class="text-center">
                      <a href="#salidas-con" data-modulo="salidas-con" class="btn btn-lg text-white shadow-lg" style="background: rgba(255,255,255,0.25); border: 2px solid rgba(255,255,255,0.5); border-radius: 50px; padding: 15px 35px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; transition: all 0.3s ease; backdrop-filter: blur(10px); text-decoration: none; font-size: 0.95rem;">
                        <i class="fas fa-bus" style="font-size: 1.5rem; margin-bottom: 8px; display: block;"></i>
                        Registrar<br>Salida Diaria

                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Información adicional -->
              <div class="row mt-4">
                <div class="col-12">
                  <div class="d-flex justify-content-center align-items-center flex-wrap" style="background: rgba(255,255,255,0.1); border-radius: 25px; padding: 10px 20px; backdrop-filter: blur(10px);">
                    <i class="fas fa-map-marker-alt text-white me-2" style="font-size: 1.2rem;"></i>
                    <span class="text-white" style="font-weight: 500; margin-left: 8px;">
                      Sistema de Transporte - Abancay - Cusco y Viceversa
                    </span>
                    <span class="text-white-50 mx-3">|</span>
                    <i class="fas fa-user-tie text-white me-2" style="font-size: 1.2rem;"></i>
                    <span class="text-white" style="font-weight: 500; margin-left: 8px;">
                      Panel Conductor
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta de fotografías lado a lado -->
        <div class="col-md-12">
          <div class="card card-primary">
            <div class="card-header py-2" style="background: linear-gradient(135deg, #023D77, #0266C8)">
              <h5 class="m-0 text-center" style="font-family:cooper; line-height: 1;">
                <i class="fas fa-image" style="margin-right: 8px;"></i>NUESTRAS RUTAS
              </h5>
            </div>

            <div class="card-body p-0">
              <div class="row g-0">
                <!-- Foto Cusco -->
                <div class="col-md-6 position-relative foto-container">
                  <img src="../Fotos/1.jpg" class="img-fluid w-100" alt="Cusco" style="height: 350px; object-fit: contain; background: #f8f9fa;">
                  <div class="foto-overlay">
                    <div class="foto-text">
                      <i class="fas fa-map-marker-alt mb-2" style="font-size: 2.5rem;"></i>
                      <h3 class="font-weight-bold">CUSCO</h3>
                      <p class="mb-0">Ciudad Imperial del Perú</p>
                    </div>
                  </div>
                </div>

                <!-- Foto Abancay -->
                <div class="col-md-6 position-relative foto-container">
                  <img src="../Fotos/10.jpg" class="img-fluid w-100" alt="Abancay" style="height: 350px; object-fit: contain; background: #f8f9fa;">
                  <div class="foto-overlay">
                    <div class="foto-text">
                      <i class="fas fa-map-marker-alt mb-2" style="font-size: 2.5rem;"></i>
                      <h3 class="font-weight-bold">ABANCAY</h3>
                      <p class="mb-0">Capital de Apurímac</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Estilos CSS -->
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

          .btn:hover {
            background: rgba(255, 255, 255, 0.35) !important;
            border-color: rgba(255, 255, 255, 0.7) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3) !important;
          }

          .foto-container {
            overflow: hidden;
            position: relative;
          }

          .foto-container img {
            transition: transform 0.5s ease;
          }

          .foto-container:hover img {
            transform: scale(1.05);
          }

          .foto-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, transparent 50%);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 30px;
            opacity: 0;
            transition: opacity 0.4s ease;
          }

          .foto-container:hover .foto-overlay {
            opacity: 1;
          }

          .foto-text {
            color: white;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
          }

          .foto-text h3 {
            font-size: 2rem;
            margin-bottom: 5px;
            letter-spacing: 2px;
          }

          .foto-text p {
            font-size: 1.1rem;
            opacity: 0.9;
          }

          @media (max-width: 768px) {
            .foto-container img {
              height: 300px !important;
            }

            .col-md-4 {
              margin-bottom: 20px;
            }
          }
        </style>

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

        <style>
          /* Animación de entrada del modal */
          .modal.fade .modal-dialog {
            transition: transform 0.4s ease-out;
            transform: translateY(-100px);
          }

          .modal.show .modal-dialog {
            transform: translateY(0);
          }

          /* Efectos hover en las filas de información */
          .info-row:hover {
            background-color: #f8f9fa !important;
            transition: background-color 0.3s ease;
            cursor: default;
          }

          /* Animación del botón */
          .btn-alerta {
            transition: all 0.3s ease;
          }

          .btn-alerta:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3) !important;
          }

          .btn-alerta:active {
            transform: scale(0.98);
          }

          /* Efecto de pulso en el icono principal */
          @keyframes pulse {

            0%,
            100% {
              transform: scale(1);
            }

            50% {
              transform: scale(1.1);
            }
          }

          .icon-pulse {
            animation: pulse 2s infinite;
          }
        </style>

      <?php } ?>
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
  <!-- JWT Handler - Manejo automático de tokens -->
  <script src="../js/jwt_handler.js"></script>
  <!-- Bootstrap 4 -->
  <script src="../plantilla/plugins//bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../plantilla/dist/js/adminlte.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

  <script src="../utilitario/DataTables/datatables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="../js/console_usuario.js?rev=<?php echo time(); ?>"></script>
  <script src="../js/console_choferes.js?php echo time(); ?>"></script>

  <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<!-- Chart.js - NECESARIO para las gráficas -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>


  <!-- AQUÍ VA EL NUEVO SCRIPT -->
<script src="../js/navegacion_hash.js?rev=<?php echo time(); ?>"></script>
</body>

</html>
<script>
  $(document).ready(function() {
    Total_servicios();
    Total_choferes();
    Total_clientes();
    // Total_comprobantes();
    Total_comprobantes();
    Total_facturas();
    Total_boletas();
    Total_Notas_Credito();
    Total_Notas_Debito();
    // encomiendas
    Total_encomiendas_dia();
    Total_encomiendas_semanales();
    Total_encomiendas_mes();
    Total_encomiendas();
    // salidas
    Total_salidas_dia();
    Total_salidas_semana();
    Total_salidas_mes();
    Total_salidas();
    // reservas
    Total_reservas_dia();
    Total_reservas_semana();
    Total_reservas_mes();
    Total_reservas();
    // ingresos y gastos
    Total_ingresos_hoy();
    Total_gastos_hoy();
    Total_ingresos_mes_actual();
    Total_gastos_mes_actual();
    //asistentete
    Total_salidas_dia_asis();
    Total_salidas_semana_asis();
    Total_salidas_mes_asis();
    Total_salidas_asis();

    Total_encomiendas_dia_asis();
    Total_encomiendas_semana_asis();
    Total_encomiendas_mes_asis();
    Total_encomiendas_asis();

    Total_reservas_pendientes_dia_asis();
    Total_reservas_semanales_asis();
    Total_reservas_mes_asis();
    Total_reservas_asis();
    listar_choferes_vencidos();
    //asistentes comprobantes
    Total_facturas_sucu();
    Total_boletas_sucu();
    Total_nota_credito_sucu();
    Total_nota_debito_sucu();
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
