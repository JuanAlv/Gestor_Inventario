<?php
namespace App\Models\Users;

class User {
    private $db;
    private $id;
    private $nombre;
    private $apellido;
    private $correo;
    private $documento;
    private $id_tipo_documento;
    private $id_rol;
    private $contrasena;
    private $estado;

    public function __construct($db) {
        $this->db = $db;
    }

    // Getters y Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getApellido() {
        return $this->apellido;
    }

    public function setApellido($apellido) {
        $this->apellido = $apellido;
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function setCorreo($correo) {
        $this->correo = $correo;
    }

    public function getDocumento() {
        return $this->documento;
    }

    public function setDocumento($documento) {
        $this->documento = $documento;
    }

    public function getIdTipoDocumento() {
        return $this->id_tipo_documento;
    }

    public function setIdTipoDocumento($id_tipo_documento) {
        $this->id_tipo_documento = $id_tipo_documento;
    }

    public function getIdRol() {
        return $this->id_rol;
    }

    public function setIdRol($id_rol) {
        $this->id_rol = $id_rol;
    }

    public function getContrasena() {
        return $this->contrasena;
    }

    public function setContrasena($contrasena) {
        $this->contrasena = password_hash($contrasena, PASSWORD_DEFAULT);
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    // Métodos para operaciones CRUD
    public function save() {
        $query = "INSERT INTO usuarios (nombre, apellido, correo, documento, id_tipo_documento, id_rol, contrasena, estado) "
              . "VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssiiis", 
            $this->nombre, 
            $this->apellido, 
            $this->correo, 
            $this->documento, 
            $this->id_tipo_documento, 
            $this->id_rol, 
            $this->contrasena, 
            $this->estado
        );
        
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, documento = ?, "
              . "id_tipo_documento = ?, id_rol = ?, estado = ? WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssiisi", 
            $this->nombre, 
            $this->apellido, 
            $this->correo, 
            $this->documento, 
            $this->id_tipo_documento, 
            $this->id_rol, 
            $this->estado, 
            $this->id
        );
        
        return $stmt->execute();
    }

    public function updatePassword() {
        $query = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $this->contrasena, $this->id);
        
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM usuarios WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $this->id);
        
        return $stmt->execute();
    }

    public function findById($id) {
        $query = "SELECT * FROM usuarios WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->mapUserData($row);
            return true;
        }
        
        return false;
    }

    public function findByDocumento($documento) {
        $query = "SELECT * FROM usuarios WHERE documento = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->mapUserData($row);
            return true;
        }
        
        return false;
    }

    public function findByEmail($correo) {
        $query = "SELECT * FROM usuarios WHERE correo = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->mapUserData($row);
            return true;
        }
        
        return false;
    }

    public function findAll() {
        $query = "SELECT * FROM usuarios";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $users = [];
        
        while ($row = $result->fetch_assoc()) {
            $user = new User($this->db);
            $user->mapUserData($row);
            $users[] = $user;
        }
        
        return $users;
    }

    private function mapUserData($row) {
        $this->id = $row['id'];
        $this->nombre = $row['nombre'];
        $this->apellido = $row['apellido'];
        $this->correo = $row['correo'];
        $this->documento = $row['documento'];
        $this->id_tipo_documento = $row['id_tipo_documento'];
        $this->id_rol = $row['id_rol'];
        $this->contrasena = $row['contrasena'];
        $this->estado = $row['estado'];
    }

    public function authenticate($correo, $contrasena) {
        if ($this->findByEmail($correo)) {
            return password_verify($contrasena, $this->contrasena);
        }
        return false;
    }
}