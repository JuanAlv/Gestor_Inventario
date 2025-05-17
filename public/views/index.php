<?php
// Iniciamos la sesión si no está activa
// Esto es necesario para poder acceder a los datos del usuario en toda la página
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cargamos las herramientas necesarias para trabajar:
// - El controlador que maneja el acceso de usuarios
// - La conexión a la base de datos para consultar información
require_once __DIR__ . '/../../app/controllers/login/loginController.php';
include('../../database/conexion.php');

// Creamos el controlador de acceso y verificamos que el usuario esté identificado
// Si no tiene sesión activa, será redirigido automáticamente a la pantalla de login
$loginController = new LoginController();
$loginController->requiereAutenticacion();

// Obtenemos los datos del usuario que ha iniciado sesión para mostrarlos en la página
$usuario = $loginController->obtenerUsuarioAutenticado();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Gestor de Inventario</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Estilos personalizados PENDIENTES POR MEJORAR-->
    <style>
        body { background-color:rgb(149, 255, 181); }
        .sidebar {
            min-height: 100vh;
            background-color:rgb(43, 45, 47);
            color: white;
        }
        .sidebar-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            transition: all 0.3s;
        }
        .sidebar-link:hover { background-color: rgba(255, 255, 255, 0.1); color: white; }
        .sidebar-link.active { background-color: #007bff; color: white; }
        .content { padding: 20px; }
        .welcome-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Panel PENDIENTE POR MEJORAR (agregar más opciones que están por definir) -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4><i class="fas fa-warehouse me-2"></i>Gestor de Inventario</h4>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="sidebar-link active" href="index.php">
                                <i class="fas fa-home me-2"></i>Inicio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="#">
                                <i class="fas fa-boxes me-2"></i>Inventario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="#">
                                <i class="fas fa-shopping-cart me-2"></i>Ventas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="#">
                                <i class="fas fa-users me-2"></i>Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="#">
                                <i class="fas fa-chart-bar me-2"></i>Reportes
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a class="sidebar-link text-danger" href="#" id="cerrarSesion">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Área principal -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Panel Principal</h1>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-2"></i><?php echo $usuario->nombre . ' ' . $usuario->apellido; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" id="cerrarSesionDropdown"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Panel de bienvenida -->
                <div class="welcome-card">
                    <h2>Bienvenido, <?php echo $usuario->nombre; ?>!</h2>
                    <p class="text-muted">Este es el panel de control del sistema de Gestión de Inventario RUKART. Desde aquí podrás administrar todos los aspectos de tu negocio.</p>
                </div>

                <!-- Indicadores principales -->
                <div class="row">
                    <!-- Productos -->
                    <div class="col-md-4 mb-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Productos</h5>
                                        <h2 class="mb-0">0</h2>
                                    </div>
                                    <i class="fas fa-boxes fa-3x opacity-50"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a href="#" class="text-white text-decoration-none">Ver detalles</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ventas -->
                    <div class="col-md-4 mb-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Ventas</h5>
                                        <h2 class="mb-0">0</h2>
                                    </div>
                                    <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a href="#" class="text-white text-decoration-none">Ver detalles</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Usuarios -->
                    <div class="col-md-4 mb-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Usuarios</h5>
                                        <h2 class="mb-0">1</h2>
                                    </div>
                                    <i class="fas fa-users fa-3x opacity-50"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a href="#" class="text-white text-decoration-none">Ver detalles</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Función para cerrar sesión
            function cerrarSesion() {
                $.ajax({
                    url: '/Gestor_Inventario/app/controllers/login/loginController.php',
                    type: 'POST',
                    data: {accion: 'cerrar_sesion'},
                    dataType: 'json',
                    success: function(response) {
                        if (response.exito) {
                            window.location.href = '/Gestor_Inventario/public/views/login.php';
                        }
                    }
                });
            }
            
            // Asignar evento a los botones de cerrar sesión
            $('#cerrarSesion, #cerrarSesionDropdown').click(function(e) {
                e.preventDefault();
                cerrarSesion();
            });
        });
    </script>
</body>
</html>