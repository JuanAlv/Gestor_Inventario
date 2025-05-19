<?php
require_once __DIR__ . '/../../service/usuario/usuarioService.php';
require_once __DIR__ . '/../../models/usuario/usuarioModel.php';
require_once __DIR__ . '/../../../database/conexion.php';

class UsuarioController {
    private $usuarioService;
    
    // Constructor
    public function __construct() {
        global $conexion;
        $this->usuarioService = new UsuarioService($conexion);
        
        // Verificar si se está solicitando un usuario por ID
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtenerPorId' && isset($_GET['id'])) {
            $usuario = $this->obtenerPorId($_GET['id']);
            
            if ($usuario) {
                $datos = [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'correo' => $usuario->correo,
                    'documento' => $usuario->documento,
                    'id_tipo_documento' => $usuario->id_tipo_documento,
                    'id_rol' => $usuario->id_rol,
                    'id_estado' => $usuario->id_estado
                ];
                
                header('Content-Type: application/json');
                echo json_encode($datos);
                exit;
            }
        }
    }
    
    // Obtener todos los usuarios para mostrar en la tabla
    public function obtenerTodos() {
        return $this->usuarioService->obtenerUsuarios();
    }
    
    // Obtener un usuario por su ID
    public function obtenerPorId($id) {
        return $this->usuarioService->obtenerUsuarioPorId($id);
    }
    
    // Crear un nuevo usuario
    public function crear($datos) {
        // Validar datos
        $errores = $this->validarDatos($datos);
        if(!empty($errores)) {
            return ['exito' => false, 'errores' => $errores, 'tipo' => 'error'];
        }
        // Verificar si el documento o correo ya existen
        if ($this->usuarioService->existeDocumentoOCorreo($datos['documento'], $datos['correo'])) {
            return ['exito' => false, 'errores' => ['El documento o correo ya existen'], 'tipo' => 'error'];
        }
        
        // Crear objeto Usuario
        $usuario = new Usuario([
            'id' => null,
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'correo' => $datos['correo'],
            'documento' => $datos['documento'],
            'id_tipo_documento' => $datos['id_tipo_documento'],
            'id_rol' => $datos['id_rol'],
            'contrasena' => $datos['contrasena'],
            'id_estado' => $datos['id_estado']
        ]);
        
        // Guardar en la base de datos
        $resultado = $this->usuarioService->crearUsuario($usuario);
        
        if ($resultado) {
            return ['exito' => true, 'mensaje' => 'Usuario creado correctamente', 'tipo' => 'success'];
        }
        else {
            return ['exito' => false, 'errores' => ['Error al crear el usuario'], 'tipo' => 'error'];
        }
    }
    
    // Actualizar un usuario ya existente
    public function actualizar($id, $datos) {
        // Validar datos
        $errores = $this->validarDatos($datos, true);
        if(!empty($errores)) {
            return ['exito' => false, 'errores' => $errores, 'tipo' => 'error'];
        }
        
        // Verificar si el documento o correo ya existen (excluyendo el usuario actual)
        if ($this->usuarioService->existeDocumentoOCorreoExcluyendoUsuario($datos['documento'], $datos['correo'], $id)) {
            return ['exito' => false, 'errores' => ['El documento o correo ya existen en otro usuario'], 'tipo' => 'error'];
        }
        
        // Actualizar datos
        $usuario = new Usuario([
            'id' => $id,
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'correo' => $datos['correo'],
            'documento' => $datos['documento'],
            'id_tipo_documento' => $datos['id_tipo_documento'],
            'id_rol' => $datos['id_rol'],
            'contrasena' => $datos['contrasena'],
            'id_estado' => $datos['id_estado']
        ]);
        
        // Actualizar en la base de datos
        $resultado = $this->usuarioService->actualizarUsuario($usuario);
        
        if ($resultado) {
            return ['exito' => true, 'mensaje' => 'Usuario actualizado correctamente', 'tipo' => 'success'];
        } else {
            return ['exito' => false, 'errores' => ['Error al actualizar el usuario'], 'tipo' => 'error'];
        }
    }
    
    // Eliminar un usuario
    public function eliminar($id) {
        $resultado = $this->usuarioService->eliminarUsuario($id);
        // Actualizar en la base de datos
        if ($resultado) {
            return ['exito' => true, 'mensaje' => 'Usuario eliminado correctamente', 'tipo' => 'success'];
        } else {
            return ['exito' => false, 'errores' => ['Error al eliminar el usuario'], 'tipo' => 'error'];
        }
    }
    
    // Validar datos
    private function validarDatos($datos, $esActualizacion = false) {
        $errores = [];
        
    // Validar campos
        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre es obligatorio';
        }
    
        if (empty($datos['apellido'])) {
            $errores[] = 'El apellido es obligatorio';
        }
    
        if (empty($datos['documento'])) {
            $errores[] = 'El documento es obligatorio';
        } elseif (!ctype_digit($datos['documento']) || intval($datos['documento']) <= 0) {
            $errores[] = 'El documento no es válido';
        }
    
        if (empty($datos['correo'])) {
            $errores[] = 'El correo es obligatorio';
        } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo no es válido';
        }
        
        if (empty($datos['id_tipo_documento'])) {
            $errores[] = 'El tipo de documento es obligatorio';
        }
        
        if (empty($datos['id_rol'])) {
            $errores[] = 'El rol es obligatorio';
        }
        
        // La contraseña es opcional durante la actualización
        if (!$esActualizacion && empty($datos['contrasena'])) {
            $errores[] = 'La contraseña es obligatoria';
        }
        
        if (empty($datos['id_estado'])) {
            $errores[] = 'El estado es obligatorio';
        }
    
        return $errores;
    }
    
    // Buscar usuarios
    public function buscar($termino) {
        return $this->usuarioService->buscar($termino);
    }
    
    // Obtener usuarios con información adicional
    public function obtenerUsuariosConInfo() {
        return $this->usuarioService->obtenerUsuariosConInfo();
    }
    
    // Obtener un usuario con información adicional por ID
    public function obtenerUsuarioConInfoPorId($id) {
        return $this->usuarioService->obtenerUsuarioConInfoPorId($id);
    }
}