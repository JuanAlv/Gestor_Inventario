<?php
require_once 'models/Usuario.php';
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
        return $stmt->execute([
            $usuario->nombre,
            $usuario->apellido,
            $usuario->correo,
            $usuario->documento,
            $usuario->id_tipo_documento,
            $usuario->id_rol,
            password_hash($usuario->contrasena, PASSWORD_DEFAULT),
            $usuario->id_estado
        ]);
    }

// Otros métodos
    public function obtenerUsuarios() {
        $stmt = $this->conn->query("SELECT * FROM usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerUsuarioPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
   
    public function eliminarUsuario($id) {
        $stmt = $this->conn->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }
   
    public function actualizarUsuario(Usuario $usuario) {
        $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, documento = ?, id_tipo_documento = ?, id_rol = ?, id_estado = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $usuario->nombre,
            $usuario->apellido,
            $usuario->correo,
            $usuario->documento,
            $usuario->id_tipo_documento,
            $usuario->id_rol,
            $usuario->id_estado,
            $usuario->id
        ]);
    }
}