<?php
// Iniciamos la sesión si no está activa para poder gestionar el estado del usuario
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cargamos el controlador que maneja todo el proceso de acceso al sistema
require_once __DIR__ . '/../../app/controllers/login/loginController.php';
$loginController = new LoginController();

// Si el usuario ya tiene una sesión activa, lo enviamos directamente a la página principal
// Esto evita que alguien con sesión iniciada vuelva a la pantalla de login innecesariamente
if ($loginController->estaAutenticado()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Gestor de Inventario</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .login-container {
            max-width: 450px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .login-header { text-align: center; margin-bottom: 30px; }
        .btn-login { background-color: #007bff; }
        .alert { display: none; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h2><i class="fas fa-warehouse me-2"></i>Gestor de Inventario</h2>
                <p class="text-muted">Por favor, ingrese sus credenciales para acceder al sistema</p>
            </div>
            
            <form id="loginForm" method="post">
                <input type="hidden" name="accion" value="iniciar_sesion">
                
                <div class="mb-3">
                    <label for="documento" class="form-label">Documento</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        <input type="text" class="form-control" id="documento" name="documento" placeholder="Ingrese su documento" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="recordar" name="recordar">
                    <label class="form-check-label" for="recordar">Recordar sesión</label>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-login">Iniciar Sesión</button>
                </div>
            </form>
            
            <div class="alert alert-danger" id="errorAlert" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <span id="errorMessage"></span>
            </div>
            
            <div class="alert alert-success" id="successAlert" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <span id="successMessage"></span>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Mostrar/ocultar contraseña
            $('#togglePassword').click(function() {
                const passwordField = $('#contrasena');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });
            
            // Enviar formulario con AJAX
            $('#loginForm').submit(function(e) {
                e.preventDefault();
                $('#errorAlert, #successAlert').hide();
                
                $.ajax({
                    url: '/Gestor_Inventario/app/controllers/login/loginController.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.exito) {
                            $('#successMessage').text(response.mensaje);
                            $('#successAlert').show();
                            setTimeout(function() {
                                window.location.href = '/Gestor_Inventario/public/views/index.php';
                            }, 1000);
                        } else {
                            $('#errorMessage').text(response.errores.join('. '));
                            $('#errorAlert').show();
                        }
                    },
                    error: function() {
                        $('#errorMessage').text('Error al procesar la solicitud. Intente nuevamente.');
                        $('#errorAlert').show();
                    }
                });
            });
        });
    </script>
</body>
</html>