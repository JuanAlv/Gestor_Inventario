<?php
require_once __DIR__ . '/../../models/login/loginModel.php';
require_once __DIR__ . '/../../models/usuario/usuarioModel.php';

/**
 * Servicio de autenticación y gestión de usuarios. Esta clase se encarga de toda la lógica relacionada con los usuarios:
 * - Verificar si las credenciales son correctas
 * - Crear nuevas cuentas de usuario
 * - Manejar el proceso de recuperación de contraseñas
 * - Controlar las sesiones activas
 * 
 * Utiliza el documento de identidad como la forma principal de identificar a cada persona en el sistema.
 */
class LoginService {
    private $conn; // Guarda la conexión a la base de datos para poder consultar y guardar información

    /**
     * Prepara el servicio para su uso.
     * Recibe y guarda la conexión a la base de datos para poder realizar consultas y operaciones con la información de usuarios.
     * @param mysqli $conexion Conexión activa a la base de datos MySQL
     */
    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    /**
     * Comprueba si un usuario puede acceder al sistema. Este método verifica que:
     * 1. El documento exista en la base de datos
     * 2. La contraseña proporcionada sea correcta
     * 3. El usuario esté activo en el sistema
     * 
     * Si todo es correcto, crea una sesión para el usuario.
     * @param Login $login Datos ingresados por el usuario (documento y contraseña)
     * @return UsuarioAutenticado|false Información del usuario si pudo entrar, o falso si no pudo
     */
    public function autenticar(Login $login) {
        // Buscar el usuario en la base de datos usando su documento de identidad y verificando que esté activo
        $sql = "SELECT * FROM usuarios WHERE documento = ? AND id_estado = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $login->documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        // Si no se encuentra ningún usuario activo con ese documento, terminar el proceso
        if ($resultado->num_rows === 0) {
            return false; // El usuario no existe o está desactivado en el sistema
        }
        
        $datos = $resultado->fetch_assoc();
        
        // Comprobar si la contraseña ingresada coincide con la almacenada en la base de datos
        if (!password_verify($login->contrasena, $datos['contrasena'])) {
            return false; // La contraseña proporcionada no es correcta
        }
        
        // Crear el objeto del usuario y establecer su sesión en el sistema
        $usuarioAutenticado = new UsuarioAutenticado($datos);
        $this->iniciarSesion($usuarioAutenticado, $login->recordar);
        
        return $usuarioAutenticado; // Devolver el objeto de usuario autenticado
    }
    
    /**
     * Registra un nuevo usuario en el sistema. Este método realiza las siguientes acciones:
     * 1. Verifica que no exista otro usuario con el mismo documento o correo
     * 2. Protege la contraseña convirtiéndola en un formato seguro
     * 3. Guarda todos los datos del nuevo usuario en la base de datos
     * 
     * @param RegistroUsuario $registro Información completa del nuevo usuario
     * @return bool Verdadero si se creó correctamente, falso si hubo algún problema
     */
    public function registrarUsuario(RegistroUsuario $registro) {
        // Comprobar si ya existe algún usuario registrado con el mismo documento o correo electrónico
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE documento = ? OR correo = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $registro->documento, $registro->correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        
        // Si encontramos coincidencias, cancelar el registro para evitar duplicados
        if ($fila['total'] > 0) {
            return false; // Ya existe un usuario con ese documento o correo
        }
        
        // Convertir la contraseña a un formato seguro antes de almacenarla en la base de datos
        $contrasenaHash = password_hash($registro->contrasena, PASSWORD_DEFAULT);
        
        // Crear el nuevo registro de usuario en la base de datos (con estado activo por defecto)
        $sql = "INSERT INTO usuarios (nombre, apellido, correo, documento, id_tipo_documento, id_rol, contrasena, id_estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 1)"; // El valor 1 en id_estado significa que el usuario estará activo
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssiis", 
            $registro->nombre,
            $registro->apellido,
            $registro->correo,
            $registro->documento,
            $registro->id_tipo_documento,
            $registro->id_rol,
            $contrasenaHash
        );
        
        return $stmt->execute();
    }
    
    /**
     * Establece la sesión del usuario en el navegador.
     * @param UsuarioAutenticado $usuario Objeto con los datos del usuario que inició sesión
     * @param bool $recordar Indica si se debe mantener la sesión activa después de cerrar el navegador
     */
    private function iniciarSesion(UsuarioAutenticado $usuario, $recordar = false) {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Guardar datos del usuario en la sesión
        $_SESSION['usuario_id'] = $usuario->id;
        $_SESSION['usuario_nombre'] = $usuario->nombre;
        $_SESSION['usuario_apellido'] = $usuario->apellido;
        $_SESSION['usuario_correo'] = $usuario->correo;
        $_SESSION['usuario_documento'] = $usuario->documento;
        $_SESSION['usuario_rol'] = $usuario->id_rol;
        
        // Si se debe recordar la sesión, crear una cookie
        if ($recordar) {
            $token = bin2hex(random_bytes(16)); // Generar token seguro (32 caracteres)
            setcookie('recordar_sesion', $token, time() + (86400 * 30), '/'); // 30 días
        }
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function cerrarSesion() {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Eliminar todas las variables de sesión
        $_SESSION = array();
        
        // Eliminar la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"
            ]);
        }
        
        // Eliminar la cookie de recordar sesión
        setcookie('recordar_sesion', '', time() - 42000, '/');
        
        // Destruir la sesión
        session_destroy();
        
        return true;
    }
    
    /**
     * Verifica si el usuario está autenticado
     * @return bool True si el usuario está autenticado, false en caso contrario
     */
    public function estaAutenticado() {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['usuario_id']);
    }
    
    /**
     * Obtiene el usuario autenticado
     * @return UsuarioAutenticado|null El usuario autenticado o null si no hay usuario autenticado
     */
    public function obtenerUsuarioAutenticado() {
        if (!$this->estaAutenticado()) {
            return null;
        }
        
        // Buscar usuario por ID
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['usuario_id']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 0) {
            return null; // Usuario no encontrado
        }
        
        $datos = $resultado->fetch_assoc();
        return new UsuarioAutenticado($datos);
    }

    /**
     * Inicia el proceso de recuperación de contraseña usando el documento como identificador.
     * @param string $documento Documento del usuario
     * @return bool True si se envió el correo de recuperación, false en caso contrario
     */
    public function recuperarContrasena($documento) {
        // Buscar usuario por documento (identificador principal)
        $sql = "SELECT * FROM usuarios WHERE documento = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        // Verificar si se encontró el usuario
        if ($resultado->num_rows === 0) {
            return false; // Usuario no encontrado
        }
        
        $datos = $resultado->fetch_assoc();
        $correo = $datos['correo'];
        
        // Generar token único y seguro para la recuperación
        $token = bin2hex(random_bytes(16)); // 32 caracteres hexadecimales
        $expira = date('Y-m-d H:i:s', time() + 3600); // Token válido por 1 hora
        
        // Guardar token en la base de datos
        $sql = "UPDATE usuarios SET token_recuperacion = ?, expira_token = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $token, $expira, $datos['id']);
        $resultado = $stmt->execute();
        
        // Verificar si se actualizó correctamente
        if (!$resultado) {
            return false;
        }
        
        // Preparar el correo con el enlace de recuperación
        $enlace = "http://localhost/Gestor_Inventario/public/views/reset_password.php?token=" . $token;
        $asunto = "Recuperación de contraseña - Gestor de Inventario";
        
        // Crear mensaje personalizado
        $mensaje = "Hola {$datos['nombre']},\n\n";
        $mensaje .= "Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para crear una nueva contraseña:\n\n";
        $mensaje .= $enlace . "\n\n";
        $mensaje .= "Este enlace expirará en 1 hora.\n\n";
        $mensaje .= "Si no solicitaste este cambio, puedes ignorar este correo.\n\n";
        $mensaje .= "Saludos,\n. Equipo Gestor de Inventario RUKART";
        
        $cabeceras = "From: noreply@gestorinventario.com" . "\r\n";
        
        // Enviar correo y retornar resultado
        return mail($correo, $asunto, $mensaje, $cabeceras);
    }
    
    /**
     * Verifica si un token de recuperación es válido.
     * @param string $token Token de recuperación
     * @return int|false ID del usuario si el token es válido, false en caso contrario
     */
    public function verificarTokenRecuperacion($token) {
        $sql = "SELECT id FROM usuarios WHERE token_recuperacion = ? AND expira_token > NOW()";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 0) {
            return false; // Token inválido o expirado
        }
        
        $datos = $resultado->fetch_assoc();
        return $datos['id'];
    }
    
    /**
     * Cambia la contraseña de un usuario.
     * @param int $idUsuario ID del usuario
     * @param string $nuevaContrasena Nueva contraseña
     * @return bool True si se cambió la contraseña, false en caso contrario
     */
    public function cambiarContrasena($idUsuario, $nuevaContrasena) {
        // Encriptar la nueva contraseña
        $contrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
        
        // Actualizar contraseña y limpiar token
        $sql = "UPDATE usuarios SET contrasena = ?, token_recuperacion = NULL, expira_token = NULL WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $contrasenaHash, $idUsuario);
        
        return $stmt->execute();
    }
}