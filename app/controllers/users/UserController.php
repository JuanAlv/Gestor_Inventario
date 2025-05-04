<?php
namespace App\Controllers\Users;

use App\Service\Users\UserService;

class UserController {
    private $userService;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->userService = new UserService($db);
    }

    /**
     * Procesa la solicitud para crear un nuevo usuario
     * 
     * @param array $requestData Datos de la solicitud
     * @return array Respuesta con resultado y mensaje
     */
    public function create($requestData) {
        // Validar datos requeridos
        $requiredFields = ['nombre', 'apellido', 'correo', 'documento', 'id_tipo_documento', 'id_rol', 'contrasena'];
        foreach ($requiredFields as $field) {
            if (!isset($requestData[$field]) || empty($requestData[$field])) {
                return [
                    'success' => false,
                    'message' => "El campo {$field} es requerido"
                ];
            }
        }

        // Validar formato de correo
        if (!filter_var($requestData['correo'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'El formato del correo electrónico no es válido'
            ];
        }

        // Intentar crear el usuario
        $userId = $this->userService->createUser($requestData);
        
        if ($userId) {
            return [
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'user_id' => $userId
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudo crear el usuario. El correo o documento ya existe.'
            ];
        }
    }

    /**
     * Procesa la solicitud para actualizar un usuario existente
     * 
     * @param int $userId ID del usuario a actualizar
     * @param array $requestData Datos de la solicitud
     * @return array Respuesta con resultado y mensaje
     */
    public function update($userId, $requestData) {
        // Verificar que el usuario existe
        $user = $this->userService->getUserById($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }

        // Validar formato de correo si se proporciona
        if (isset($requestData['correo']) && !filter_var($requestData['correo'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'El formato del correo electrónico no es válido'
            ];
        }

        // Intentar actualizar el usuario
        $result = $this->userService->updateUser($userId, $requestData);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Usuario actualizado exitosamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudo actualizar el usuario. Verifique que el correo o documento no estén en uso.'
            ];
        }
    }

    /**
     * Procesa la solicitud para cambiar la contraseña de un usuario
     * 
     * @param int $userId ID del usuario
     * @param array $requestData Datos de la solicitud
     * @return array Respuesta con resultado y mensaje
     */
    public function changePassword($userId, $requestData) {
        // Validar datos requeridos
        if (!isset($requestData['contrasena']) || empty($requestData['contrasena'])) {
            return [
                'success' => false,
                'message' => 'La nueva contraseña es requerida'
            ];
        }

        // Verificar que el usuario existe
        $user = $this->userService->getUserById($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }

        // Intentar actualizar la contraseña
        $result = $this->userService->updatePassword($userId, $requestData['contrasena']);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudo actualizar la contraseña'
            ];
        }
    }

    /**
     * Procesa la solicitud para eliminar un usuario
     * 
     * @param int $userId ID del usuario a eliminar
     * @return array Respuesta con resultado y mensaje
     */
    public function delete($userId) {
        // Verificar que el usuario existe
        $user = $this->userService->getUserById($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }

        // Intentar eliminar el usuario
        $result = $this->userService->deleteUser($userId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudo eliminar el usuario'
            ];
        }
    }

    /**
     * Procesa la solicitud para obtener un usuario por su ID
     * 
     * @param int $userId ID del usuario
     * @return array Respuesta con resultado y datos del usuario
     */
    public function getById($userId) {
        $user = $this->userService->getUserById($userId);
        
        if ($user) {
            return [
                'success' => true,
                'user' => [
                    'id' => $user->getId(),
                    'nombre' => $user->getNombre(),
                    'apellido' => $user->getApellido(),
                    'correo' => $user->getCorreo(),
                    'documento' => $user->getDocumento(),
                    'id_tipo_documento' => $user->getIdTipoDocumento(),
                    'id_rol' => $user->getIdRol(),
                    'estado' => $user->getEstado()
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }
    }

    /**
     * Procesa la solicitud para obtener todos los usuarios
     * 
     * @return array Respuesta con resultado y lista de usuarios
     */
    public function getAll() {
        $users = $this->userService->getAllUsers();
        $usersList = [];
        
        foreach ($users as $user) {
            $usersList[] = [
                'id' => $user->getId(),
                'nombre' => $user->getNombre(),
                'apellido' => $user->getApellido(),
                'correo' => $user->getCorreo(),
                'documento' => $user->getDocumento(),
                'id_tipo_documento' => $user->getIdTipoDocumento(),
                'id_rol' => $user->getIdRol(),
                'estado' => $user->getEstado()
            ];
        }
        
        return [
            'success' => true,
            'users' => $usersList
        ];
    }

    /**
     * Procesa la solicitud de autenticación de usuario
     * 
     * @param array $requestData Datos de la solicitud
     * @return array Respuesta con resultado y datos del usuario autenticado
     */
    public function login($requestData) {
        // Validar datos requeridos
        if (!isset($requestData['correo']) || empty($requestData['correo']) ||
            !isset($requestData['contrasena']) || empty($requestData['contrasena'])) {
            return [
                'success' => false,
                'message' => 'Correo y contraseña son requeridos'
            ];
        }

        // Intentar autenticar al usuario
        $user = $this->userService->authenticateUser($requestData['correo'], $requestData['contrasena']);
        
        if ($user) {
            // Verificar si el usuario está activo
            if ($user->getEstado() !== 'activo') {
                return [
                    'success' => false,
                    'message' => 'Usuario inactivo. Contacte al administrador.'
                ];
            }
            
            // Iniciar sesión
            session_start();
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_nombre'] = $user->getNombre() . ' ' . $user->getApellido();
            $_SESSION['user_rol'] = $user->getIdRol();
            
            return [
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'user' => [
                    'id' => $user->getId(),
                    'nombre' => $user->getNombre(),
                    'apellido' => $user->getApellido(),
                    'correo' => $user->getCorreo(),
                    'id_rol' => $user->getIdRol()
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Credenciales inválidas'
            ];
        }
    }

    /**
     * Cierra la sesión del usuario actual
     * 
     * @return array Respuesta con resultado y mensaje
     */
    public function logout() {
        session_start();
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Sesión cerrada exitosamente'
        ];
    }
}