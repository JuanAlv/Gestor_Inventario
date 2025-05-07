<?php
// Clase Usuario
class Usuario {
    public $id;
    public $nombre;
    public $apellido;
    public $correo;
    public $documento;
    public $id_tipo_documento;
    public $id_rol;
    public $contrasena;
    public $id_estado;
    
    public function __construct($data) {
        $this->id = $data['id'] ?? null;
        $this->nombre = $data['nombre'] ?? '';
        $this->apellido = $data['apellido'] ?? '';
        $this->correo = $data['correo'] ?? '';
        $this->documento = $data['documento'] ?? '';
        $this->id_tipo_documento = $data['id_tipo_documento'] ?? 0;
        $this->id_rol = $data['id_rol'] ?? 0;
        $this->contrasena = $data['contrasena'] ?? '';
        $this->id_estado = $data['id_estado'] ?? 0;
    }
}