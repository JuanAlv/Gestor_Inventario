<?php
require_once __DIR__ . '/../../service/login/loginService.php';
require_once __DIR__ . '/../../models/login/loginModel.php';
require_once __DIR__ . '/../../../database/conexion.php';

/**
 * Controlador de acceso al sistema. Gestiona todo lo relacionado con la entrada y salida de usuarios al sistema:
 * - Inicio y cierre de sesión
 * - Registro de nuevos usuarios
 * - Recuperación de contraseñas olvidadas
 */
class LoginController {
    private $loginService; // Servicio que contiene todas las reglas y operaciones de autenticación
    
    /**
     * Inicializa el controlador. Se ejecuta automáticamente al crear el controlador y realiza dos tareas:
     * 1. Prepara el servicio de login para poder usarlo
     * 2. Revisa si el usuario ha enviado algún formulario (como iniciar sesión o registrarse) y ejecuta la acción correspondiente
     */
    public function __construct() {
        global $conexion;
        $this->loginService = new LoginService($conexion);
        
        // Detectar si el usuario ha enviado un formulario con alguna acción de autenticación
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['accion'])) {
                // Ejecutar la función correspondiente según la acción solicitada
                switch ($_POST['accion']) {
                    case 'iniciar_sesion':
                        $this->procesarInicioSesion(); // Verificar credenciales e iniciar sesión
                        break;
                    case 'cerrar_sesion':
                        $this->procesarCierreSesion(); // Finalizar la sesión actual
                        break;
                    case 'registrar_usuario':
                        $this->procesarRegistroUsuario(); // Crear una nueva cuenta de usuario
                        break;
                    case 'recuperar_contrasena':
                        $this->procesarRecuperacionContrasena(); // Enviar correo para restablecer contraseña
                        break;
                    case 'cambiar_contrasena':
                        $this->procesarCambioContrasena(); // Actualizar contraseña con el token
                        break;
                }
            }
        }
    }
    
    /**
     * Procesa el intento de inicio de sesión. Se encarga de:
     * 1. Revisar que el documento y contraseña no estén vacíos
     * 2. Intentar identificar al usuario con esos datos
     * 3. Responder si pudo entrar al sistema o si hubo algún problema (como datos incorrectos o usuario inactivo)
     */
    private function procesarInicioSesion() {
        // Validar datos de entrada
        $errores = $this->validarDatosLogin($_POST);
        
        // Si hay errores, enviar respuesta de error y terminar
        if (!empty($errores)) {
            $this->enviarRespuestaJSON([
                'exito' => false,
                'errores' => $errores,
                'tipo' => 'error'
            ]);
            return;
        }
        
        // Crear Login con los datos del formulario e intentar autenticar
        $login = new Login([
            'documento' => $_POST['documento'],
            'contrasena' => $_POST['contrasena'],
            'recordar' => isset($_POST['recordar']) ? true : false
        ]);
        
        $usuario = $this->loginService->autenticar($login);
        
        // Preparar y enviar respuesta según resultado
        if ($usuario) {
            $this->enviarRespuestaJSON([
                'exito' => true,
                'mensaje' => 'Inicio de sesión exitoso',
                'tipo' => 'success',
                'usuario' => [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'rol' => $usuario->id_rol
                ]
            ]);
        } else {
            $this->enviarRespuestaJSON([
                'exito' => false,
                'errores' => ['Documento o contraseña incorrectos, o usuario inactivo'],
                'tipo' => 'error'
            ]);
        }
    }
    
    /**
     * Crea una nueva cuenta de usuario en el sistema. 
     * Recibe todos los datos personales y credenciales del nuevo usuario.
     * verifica que sean correctos y crea la cuenta si todo está en orden.
     * Responde con el resultado del proceso (éxito o errores encontrados).  
     */
    private function procesarRegistroUsuario() {
        // Validar datos
        $errores = $this->validarDatosRegistro($_POST);
        if (!empty($errores)) {
            $respuesta = [
                'exito' => false,
                'errores' => $errores,
                'tipo' => 'error'
            ];
            $this->enviarRespuestaJSON($respuesta);
            return;
        }
        
        // Crear objeto RegistroUsuario
        $registro = new RegistroUsuario([
            'nombre' => $_POST['nombre'],
            'apellido' => $_POST['apellido'],
            'correo' => $_POST['correo'],
            'documento' => $_POST['documento'],
            'id_tipo_documento' => $_POST['id_tipo_documento'],
            'id_rol' => $_POST['id_rol'],
            'contrasena' => $_POST['contrasena'],
            'confirmar_contrasena' => $_POST['confirmar_contrasena']
        ]);
        
        // Intentar registrar al usuario
        $resultado = $this->loginService->registrarUsuario($registro);
        
        if ($resultado) {
            $respuesta = [
                'exito' => true,
                'mensaje' => 'Usuario registrado correctamente',
                'tipo' => 'success'
            ];
        } else {
            $respuesta = [
                'exito' => false,
                'errores' => ['No se pudo registrar el usuario. El documento o correo ya existe.'],
                'tipo' => 'error'
            ];
        }
        
        $this->enviarRespuestaJSON($respuesta);
    }
    
    /**
     * Maneja el proceso cuando un usuario olvidó su contraseña.
     * Recibe el documento del usuario, verifica que exista en el sistema y envía un correo con instrucciones para crear una nueva contraseña.
     * Responde con el resultado del proceso.  
     */
    private function procesarRecuperacionContrasena() {
        // Validar datos
        if (empty($_POST['documento'])) {
            $respuesta = [
                'exito' => false,
                'errores' => ['El documento es obligatorio'],
                'tipo' => 'error'
            ];
            $this->enviarRespuestaJSON($respuesta);
            return;
        }
        
        // Intentar recuperar contraseña
        $resultado = $this->loginService->recuperarContrasena($_POST['documento']);
        
        if ($resultado) {
            $respuesta = [
                'exito' => true,
                'mensaje' => 'Se ha enviado un correo con instrucciones para recuperar la contraseña',
                'tipo' => 'success'
            ];
        } else {
            $respuesta = [
                'exito' => false,
                'errores' => ['No se encontró un usuario con ese documento'],
                'tipo' => 'error'
            ];
        }
        
        $this->enviarRespuestaJSON($respuesta);
    }
    
    /**
     * Establece una nueva contraseña para el usuario.
     * Este método se usa cuando un usuario ha solicitado recuperar su contraseña.
     * Verifica que el código de recuperación (token) sea válido y que la nueva
     * contraseña cumpla con los requisitos de seguridad antes de cambiarla.
     */
    private function procesarCambioContrasena() {
        // Validar datos
        $errores = [];
        
        if (empty($_POST['token'])) {
            $errores[] = 'El token es obligatorio';
        }
        
        if (empty($_POST['nueva_contrasena'])) {
            $errores[] = 'La nueva contraseña es obligatoria';
        } elseif (strlen($_POST['nueva_contrasena']) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if ($_POST['nueva_contrasena'] !== $_POST['confirmar_contrasena']) {
            $errores[] = 'Las contraseñas no coinciden';
        }
        
        if (!empty($errores)) {
            $respuesta = [
                'exito' => false,
                'errores' => $errores,
                'tipo' => 'error'
            ];
            $this->enviarRespuestaJSON($respuesta);
            return;
        }
        
        // Verificar token y cambiar contraseña
        $idUsuario = $this->loginService->verificarTokenRecuperacion($_POST['token']);
        
        if (!$idUsuario) {
            $respuesta = [
                'exito' => false,
                'errores' => ['El token es inválido o ha expirado'],
                'tipo' => 'error'
            ];
            $this->enviarRespuestaJSON($respuesta);
            return;
        }
        
        // Cambiar contraseña 
        $resultado = $this->loginService->cambiarContrasena($idUsuario, $_POST['nueva_contrasena']);
        
        if ($resultado) {
            $respuesta = [
                'exito' => true,
                'mensaje' => 'Contraseña cambiada correctamente',
                'tipo' => 'success'
            ];
        } else {
            $respuesta = [
                'exito' => false,
                'errores' => ['No se pudo cambiar la contraseña'],
                'tipo' => 'error'
            ];
        }
        
        $this->enviarRespuestaJSON($respuesta);
    }
    
    /**
     * Finaliza la sesión activa del usuario.
     * Elimina toda la información de la sesión actual para que el usuario deba identificarse nuevamente para acceder al sistema.
     */
    private function procesarCierreSesion() {
        $resultado = $this->loginService->cerrarSesion();
        
        $respuesta = [
            'exito' => $resultado,
            'mensaje' => 'Sesión cerrada exitosamente',
            'tipo' => 'success'
        ];
        
        $this->enviarRespuestaJSON($respuesta);
    }
    
    /**
     * Comprueba si hay un usuario con sesión activa.
     * @return bool Devuelve verdadero si hay alguien con sesión iniciada, o falso si no hay nadie identificado en el sistema
     */
    public function estaAutenticado() {
        return $this->loginService->estaAutenticado();
    }
    
    /**
     * Obtiene los datos del usuario que tiene sesión activa.
     * @return UsuarioAutenticado|null Devuelve el objeto con la información del usuario, o null si no hay ningún usuario con sesión iniciada
     */
    public function obtenerUsuarioAutenticado() {
        return $this->loginService->obtenerUsuarioAutenticado();
    }
    
    /**
     * Revisa que los datos de inicio de sesión estén completos.
     * @param array $datos Información enviada por el usuario (documento y contraseña).
     * @return array Lista de problemas encontrados (vacía si todo está correcto).
     */
    private function validarDatosLogin($datos) {
        $errores = [];
        
        // Validar documento
        if (empty($datos['documento'])) {
            $errores[] = 'El número de documento es obligatorio';
        }
        
        // Validar contraseña
        if (empty($datos['contrasena'])) {
            $errores[] = 'La contraseña es obligatoria';
        }
        
        return $errores;
    }
    
    /**
     * Comprueba que todos los datos para crear un usuario sean correctos.
     * Verifica que todos los campos obligatorios estén completos y que cumplan con el formato esperado (como el correo electrónico válido
     * o que las contraseñas coincidan).
     * @param array $datos Información personal y credenciales del nuevo usuario
     * @return array Lista de problemas encontrados (vacía si todo está correcto)
     */
    private function validarDatosRegistro($datos) {
        $errores = [];
        
        // Validar nombre
        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre es obligatorio';
        }
        
        // Validar apellido
        if (empty($datos['apellido'])) {
            $errores[] = 'El apellido es obligatorio';
        }
        
        // Validar correo
        if (empty($datos['correo'])) {
            $errores[] = 'El correo es obligatorio';
        } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo no es válido';
        }
        
        // Validar documento
        if (empty($datos['documento'])) {
            $errores[] = 'El número de documento es obligatorio';
        }
        
        // Validar tipo de documento
        if (empty($datos['id_tipo_documento'])) {
            $errores[] = 'El tipo de documento es obligatorio';
        }
        
        // Validar rol
        if (empty($datos['id_rol'])) {
            $errores[] = 'El rol es obligatorio';
        }
        
        // Validar contraseña
        if (empty($datos['contrasena'])) {
            $errores[] = 'La contraseña es obligatoria';
        } elseif (strlen($datos['contrasena']) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        // Validar confirmación de contraseña
        if ($datos['contrasena'] !== $datos['confirmar_contrasena']) {
            $errores[] = 'Las contraseñas no coinciden';
        }
        
        return $errores;
    }
    
    /**
     * Envía el resultado al navegador en formato JSON.
     * Este método prepara la respuesta para que pueda ser procesada por JavaScript en el navegador del usuario, indicando si la
     * operación fue exitosa o si hubo errores.
     * 
     * @param array $datos Información que se enviará como respuesta
     */
    private function enviarRespuestaJSON($datos) {
        header('Content-Type: application/json');
        echo json_encode($datos);
        exit;
    }
    
    /**
     * Comprueba si el usuario actual tiene un rol específico. 
     * Útil para verificar permisos antes de mostrar opciones o permitir acciones que solo ciertos tipos de usuarios pueden realizar.
     * @param int $idRol Número que identifica el rol que queremos verificar.
     * @return bool Verdadero si el usuario tiene ese rol, falso en caso contrario.
     */
    public function tieneRol($idRol) {
        if (!$this->estaAutenticado()) {
            return false;
        }
        
        $usuario = $this->obtenerUsuarioAutenticado();
        return $usuario->id_rol == $idRol;
    }
    
    /**
     * Protege páginas que requieren identificación.
     * Si alguien intenta acceder a una página protegida sin haber iniciado sesión, este método lo envía automáticamente a la pantalla de login.
     * Se usa al inicio de las páginas que solo usuarios identificados pueden ver.
     */
    public function requiereAutenticacion() {
        if (!$this->estaAutenticado()) {
            header('Location: /Gestor_Inventario/public/views/login.php');
            exit;
        }
    }
    
    /**
     * Evita que usuarios ya identificados vean la página de login.
     * Si un usuario con sesión activa intenta acceder a la pantalla de inicio de sesión, este método lo redirige automáticamente a la página principal del sistema.
     * Se usa para evitar inicios de sesión innecesarios.
     */
    public function requiereNoAutenticacion() {
        if ($this->estaAutenticado()) {
            header('Location: /Gestor_Inventario/public/views/index.php');
            exit;
        }
    }
}