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