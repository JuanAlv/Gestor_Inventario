<?php
require_once 'services/UsuarioService.php';
require_once 'models/Usuario.php';

// Controlador de usuarios
class UsuarioController {
    private $usuarioService;
    
    // Constructor
    public function __construct($conexion) {
        $this->usuarioService = new UsuarioService($conexion);
    }
    
    // Crear un usuario
    public function crear($data) {
        try {
            if ($this->validarDatos($data)) {
                $usuario = new Usuario($data);
                return $this->usuarioService->crearUsuario($usuario);
            }
            throw new Exception("Datos de usuario no válidos");
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    // Listar todos los usuarios
    public function listar() {
        try {
            return $this->usuarioService->obtenerUsuarios();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }
    
    // Ver un usuario
    public function ver($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception("ID no válido");
            }
            return $this->usuarioService->obtenerUsuarioPorId($id);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }
    
    // Eliminar un usuario
    public function eliminar($id) {
        try {
            // Validar que el ID sea válido
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception("ID no válido");
            }
            // Eliminar el usuario
            return $this->usuarioService->eliminarUsuario($id);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    // Actualizar un usuario
    public function actualizar($data) {
        try {
            // Validar que el ID sea válido
            if (!isset($data['id']) || !is_numeric($data['id']) || $data['id'] <= 0) {
                throw new Exception("ID no válido");
            }
            // Validar los datos del usuario
            if (!$this->validarDatos($data)) {
                throw new Exception("Datos de usuario no válidos");
            }
            // Crear un objeto Usuario con los datos recibidos
            $usuario = new Usuario($data);
            return $this->usuarioService->actualizarUsuario($usuario);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    // Validar los datos del usuario
    private function validarDatos($data) {
        // Campos requeridos
        $requiredFields = [
            'nombre', 'apellido', 'correo', 'documento',
            'id_tipo_documento', 'id_rol', 'contrasena', 'id_estado'
        ];
       
        // Validar que todos los campos requeridos estén presentes
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                return false;
            }
        }

        // Validar formato del correo electrónico
        if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Validar que los campos numéricos sean enteros positivos
        $numericFields = ['id_tipo_documento', 'id_rol', 'id_estado'];
        foreach ($numericFields as $field) {
            if (!is_numeric($data[$field]) || $data[$field] <= 0) {
                return false;
            }
        }
        // Si todos los campos son válidos, devolver true
        return true;
    }
}