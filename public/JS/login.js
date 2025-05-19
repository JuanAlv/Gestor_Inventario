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