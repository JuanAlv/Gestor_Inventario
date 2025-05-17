<?php
/**
 * Modelos para el login
 */

// Clase login para manejar los datos de sesión
class Login {
    public $documento;     // Número de documento de identidad (identificador principal)
    public $contrasena;    // Contraseña del usuario
    public $recordar;      // Indica si se debe mantener la sesión activa
    
    public function __construct($datos = []) {
        $this->documento = $datos['documento'] ?? '';
        $this->contrasena = $datos['contrasena'] ?? '';
        $this->recordar = $datos['recordar'] ?? false;
    }
}

// Clase para el registro de nuevos usuarios
class RegistroUsuario {
    // Datos personales
    public $nombre;
    public $apellido;
    public $correo;
    public $documento;             // Número de ocumento de identidad (identificador principal)
    public $id_tipo_documento;     // Tipo de documento (CC, TI, CE, etc.)
    
    // Datos de sistema
    public $id_rol;                // Rol del usuario en el sistema
    
    // Datos de seguridad
    public $contrasena;
    public $confirmar_contrasena;
    
    public function __construct($datos = []) {
        // Asignar valores desde el array de datos o valores por defecto
        $this->nombre = $datos['nombre'] ?? '';
        $this->apellido = $datos['apellido'] ?? '';
        $this->correo = $datos['correo'] ?? '';
        $this->documento = $datos['documento'] ?? '';
        $this->id_tipo_documento = $datos['id_tipo_documento'] ?? 0;
        $this->id_rol = $datos['id_rol'] ?? 0;
        $this->contrasena = $datos['contrasena'] ?? '';
        $this->confirmar_contrasena = $datos['confirmar_contrasena'] ?? '';
    }
}

// Clase para la recuperación de contraseña
class RecuperacionContrasena {
    public $documento;             // Número de documento para identificar al usuario
    public $token;                // Token de recuperación
    public $nueva_contrasena;     // Nueva contraseña
    public $confirmar_contrasena; // Confirmación de la nueva contraseña
    
    public function __construct($datos = []) {
        $this->documento = $datos['documento'] ?? '';
        $this->token = $datos['token'] ?? '';
        $this->nueva_contrasena = $datos['nueva_contrasena'] ?? '';
        $this->confirmar_contrasena = $datos['confirmar_contrasena'] ?? '';
    }
}

// Clase para almacenar la información del usuario autenticado
class UsuarioAutenticado {
    // Datos básicos
    public $id;                   // ID en la base de datos
    public $nombre;               // Nombre del usuario
    public $apellido;             // Apellido del usuario
    public $correo;               // Correo electrónico
    public $documento;            // Número de documento de identidad
    public $id_tipo_documento;    // Tipo de documento
    public $id_rol;               // Rol del usuario
    public $id_estado;            // Estado del usuario (activo/inactivo)
    
    public function __construct($data) {
        $this->id = $data['id'] ?? null;
        $this->nombre = $data['nombre'] ?? '';
        $this->apellido = $data['apellido'] ?? '';
        $this->correo = $data['correo'] ?? '';
        $this->documento = $data['documento'] ?? '';
        $this->id_tipo_documento = $data['id_tipo_documento'] ?? 0;
        $this->id_rol = $data['id_rol'] ?? 0;
        $this->id_estado = $data['id_estado'] ?? 0;
    }
    
    // Método para obtener el nombre completo
    public function getNombreCompleto() {
        return $this->nombre . ' ' . $this->apellido;
    }
}