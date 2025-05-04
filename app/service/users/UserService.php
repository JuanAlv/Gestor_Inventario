<?php
namespace App\Service\Users;

use App\Models\Users\User;

class UserService {
    private $db;
    private $userModel;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);
    }

    /**
     * Crea un nuevo usuario en el sistema
     * 
     * @param array $userData Datos del usuario a crear
     * @return bool|int ID del usuario creado o false en caso de error
     */
    public function createUser($userData) {
        // Verificar si el usuario ya existe por documento o correo
        if ($this->userModel->findByDocumento($userData['documento']) || 
            $this->userModel->findByEmail($userData['correo'])) {
            return false; // Usuario ya existe
        }

        // Configurar los datos del usuario
        $this->userModel->setNombre($userData['nombre']);
        $this->userModel->setApellido($userData['apellido']);
        $this->userModel->setCorreo($userData['correo']);
        $this->userModel->setDocumento($userData['documento']);
        $this->userModel->setIdTipoDocumento($userData['id_tipo_documento']);
        $this->userModel->setIdRol($userData['id_rol']);
        $this->userModel->setContrasena($userData['contrasena']);
        $this->userModel->setEstado($userData['estado'] ?? 'activo');

        // Guardar el usuario
        if ($this->userModel->save()) {
            return $this->db->insert_id;
        }

        return false;
    }

    /**
     * Actualiza los datos de un usuario existente
     * 
     * @param int $userId ID del usuario a actualizar
     * @param array $userData Datos actualizados del usuario
     * @return bool Resultado de la operación
     */
    public function updateUser($userId, $userData) {
        if (!$this->userModel->findById($userId)) {
            return false; // Usuario no encontrado
        }

        // Verificar si el documento o correo ya está en uso por otro usuario
        if (isset($userData['documento'])) {
            $tempUser = new User($this->db);
            if ($tempUser->findByDocumento($userData['documento']) && $tempUser->getId() != $userId) {
                return false; // Documento ya en uso por otro usuario
            }
        }

        if (isset($userData['correo'])) {
            $tempUser = new User($this->db);
            if ($tempUser->findByEmail($userData['correo']) && $tempUser->getId() != $userId) {
                return false; // Correo ya en uso por otro usuario
            }
        }

        // Actualizar solo los campos proporcionados
        if (isset($userData['nombre'])) {
            $this->userModel->setNombre($userData['nombre']);
        }
        if (isset($userData['apellido'])) {
            $this->userModel->setApellido($userData['apellido']);
        }
        if (isset($userData['correo'])) {
            $this->userModel->setCorreo($userData['correo']);
        }
        if (isset($userData['documento'])) {
            $this->userModel->setDocumento($userData['documento']);
        }
        if (isset($userData['id_tipo_documento'])) {
            $this->userModel->setIdTipoDocumento($userData['id_tipo_documento']);
        }
        if (isset($userData['id_rol'])) {
            $this->userModel->setIdRol($userData['id_rol']);
        }
        if (isset($userData['estado'])) {
            $this->userModel->setEstado($userData['estado']);
        }

        return $this->userModel->update();
    }

    /**
     * Actualiza la contraseña de un usuario
     * 
     * @param int $userId ID del usuario
     * @param string $newPassword Nueva contraseña
     * @return bool Resultado de la operación
     */
    public function updatePassword($userId, $newPassword) {
        if (!$this->userModel->findById($userId)) {
            return false; // Usuario no encontrado
        }

        $this->userModel->setContrasena($newPassword);
        return $this->userModel->updatePassword();
    }

    /**
     * Elimina un usuario del sistema
     * 
     * @param int $userId ID del usuario a eliminar
     * @return bool Resultado de la operación
     */
    public function deleteUser($userId) {
        if (!$this->userModel->findById($userId)) {
            return false; // Usuario no encontrado
        }

        return $this->userModel->delete();
    }

    /**
     * Obtiene un usuario por su ID
     * 
     * @param int $userId ID del usuario
     * @return User|null Usuario encontrado o null
     */
    public function getUserById($userId) {
        if ($this->userModel->findById($userId)) {
            return $this->userModel;
        }
        return null;
    }

    /**
     * Obtiene un usuario por su documento
     * 
     * @param string $documento Documento del usuario
     * @return User|null Usuario encontrado o null
     */
    public function getUserByDocumento($documento) {
        if ($this->userModel->findByDocumento($documento)) {
            return $this->userModel;
        }
        return null;
    }

    /**
     * Obtiene un usuario por su correo electrónico
     * 
     * @param string $correo Correo del usuario
     * @return User|null Usuario encontrado o null
     */
    public function getUserByEmail($correo) {
        if ($this->userModel->findByEmail($correo)) {
            return $this->userModel;
        }
        return null;
    }

    /**
     * Obtiene todos los usuarios del sistema
     * 
     * @return array Lista de usuarios
     */
    public function getAllUsers() {
        return $this->userModel->findAll();
    }

    /**
     * Autentica un usuario por correo y contraseña
     * 
     * @param string $correo Correo del usuario
     * @param string $contrasena Contraseña del usuario
     * @return User|null Usuario autenticado o null
     */
    public function authenticateUser($correo, $contrasena) {
        if ($this->userModel->authenticate($correo, $contrasena)) {
            return $this->userModel;
        }
        return null;
    }
}