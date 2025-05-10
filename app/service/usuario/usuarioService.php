<?php
require_once __DIR__ . '/../../models/usuario/usuarioModel.php';
// Servicio de usuarios
class UsuarioService {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }
// Métodos
    public function crearUsuario(Usuario $usuario) {
        $sql = "INSERT INTO usuarios (nombre, apellido, correo, documento, id_tipo_documento, id_rol, contrasena, id_estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $contrasenaHash = password_hash($usuario->contrasena, PASSWORD_DEFAULT);
        $stmt->bind_param("ssssiiis", 
            $usuario->nombre,
            $usuario->apellido,
            $usuario->correo,
            $usuario->documento,
            $usuario->id_tipo_documento,
            $usuario->id_rol,
            $contrasenaHash,
            $usuario->id_estado
        );
        return $stmt->execute();
    }

// Verificar si existe un usuario con el mismo documento o correo
    public function existeDocumentoOCorreo($documento, $correo) {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE documento = ? OR correo = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $documento, $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        return $fila['total'] > 0;
    }
    
    // Verificar si existe un usuario con el mismo documento o correo, excluyendo el usuario actual
    public function existeDocumentoOCorreoExcluyendoUsuario($documento, $correo, $id_usuario) {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE (documento = ? OR correo = ?) AND id != ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $documento, $correo, $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        return $fila['total'] > 0;
    }
    
    // Obtener todos los usuarios
    public function obtenerUsuarios() {
        $resultado = $this->conn->query("SELECT * FROM usuarios");
        $usuarios = [];
        while ($fila = $resultado->fetch_assoc()) {
            $usuarios[] = $fila;
        }
        return $usuarios;
    }
    
    // Obtener un usuario por ID
    public function obtenerUsuarioPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado->fetch_assoc();
        
        if ($datos) {
            return new Usuario($datos);
        }
        
        return null;
    }
    
    // Eliminar un usuario
    public function eliminarUsuario($id) {
        $stmt = $this->conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // Actualizar un usuario
    public function actualizarUsuario(Usuario $usuario) {
        // Si la contraseña está presente y no está vacía, actualizarla también
        if (!empty($usuario->contrasena)) {
            $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, documento = ?, id_tipo_documento = ?, id_rol = ?, contrasena = ?, id_estado = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $contrasenaHash = password_hash($usuario->contrasena, PASSWORD_DEFAULT);
            $stmt->bind_param("ssssiisis", 
                $usuario->nombre,
                $usuario->apellido,
                $usuario->correo,
                $usuario->documento,
                $usuario->id_tipo_documento,
                $usuario->id_rol,
                $contrasenaHash,
                $usuario->id_estado,
                $usuario->id
            );
            return $stmt->execute();
        } else {
            // Si no hay contraseña nueva, mantener la actual
            $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, documento = ?, id_tipo_documento = ?, id_rol = ?, id_estado = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssiisi", 
                $usuario->nombre,
                $usuario->apellido,
                $usuario->correo,
                $usuario->documento,
                $usuario->id_tipo_documento,
                $usuario->id_rol,
                $usuario->id_estado,
                $usuario->id
            );
            return $stmt->execute();
        }
    }
    
    // Método para buscar usuarios por término
    public function buscar($termino) {
        $termino = "%$termino%";
        $sql = "SELECT * FROM usuarios WHERE 
                nombre LIKE ? OR 
                apellido LIKE ? OR 
                correo LIKE ? OR 
                documento LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $termino, $termino, $termino, $termino);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuarios = [];
        while ($fila = $resultado->fetch_assoc()) {
            $usuarios[] = $fila;
        }
        return $usuarios;
    }
    
    // Método para obtener usuarios con información de roles y tipos de documento
    public function obtenerUsuariosConInfo() {
        $sql = "SELECT u.*, r.nombre as nombre_rol, td.nombre as tipo_documento, e.nombre as estado 
                FROM usuarios u 
                LEFT JOIN roles r ON u.id_rol = r.id 
                LEFT JOIN tipos_documento td ON u.id_tipo_documento = td.id 
                LEFT JOIN estados e ON u.id_estado = e.id 
                ORDER BY u.nombre ASC";
        $resultado = $this->conn->query($sql);
        $usuarios = [];
        while ($fila = $resultado->fetch_assoc()) {
            $usuarios[] = $fila;
        }
        return $usuarios;
    }
    
    // Método para obtener un usuario con información adicional
    public function obtenerUsuarioConInfoPorId($id) {
        $sql = "SELECT u.*, r.nombre as nombre_rol, td.nombre as tipo_documento, e.nombre as estado 
                FROM usuarios u 
                LEFT JOIN roles r ON u.id_rol = r.id 
                LEFT JOIN tipos_documento td ON u.id_tipo_documento = td.id 
                LEFT JOIN estados e ON u.id_estado = e.id 
                WHERE u.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
}