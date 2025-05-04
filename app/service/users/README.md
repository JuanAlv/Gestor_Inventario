# Módulo de Servicios de Usuarios

Este directorio contiene las clases de servicio para la gestión de usuarios en el sistema de inventario.

## Estructura

- `UserService.php`: Clase principal que implementa la lógica de negocio para la gestión de usuarios.

## Clase UserService

La clase `UserService` actúa como una capa intermedia entre los controladores y los modelos, implementando la lógica de negocio para las operaciones con usuarios. Proporciona métodos para:

- Crear, actualizar y eliminar usuarios
- Buscar usuarios por diferentes criterios
- Autenticar usuarios
- Gestionar contraseñas

## Funcionalidades

### Gestión de Usuarios

- `createUser($userData)`: Crea un nuevo usuario verificando que no exista previamente
- `updateUser($userId, $userData)`: Actualiza los datos de un usuario existente
- `deleteUser($userId)`: Elimina un usuario del sistema
- `getAllUsers()`: Obtiene todos los usuarios registrados

### Búsqueda de Usuarios

- `getUserById($userId)`: Obtiene un usuario por su ID
- `getUserByDocumento($documento)`: Obtiene un usuario por su número de documento
- `getUserByEmail($correo)`: Obtiene un usuario por su correo electrónico

### Autenticación y Seguridad

- `authenticateUser($correo, $contrasena)`: Verifica las credenciales de un usuario
- `updatePassword($userId, $newPassword)`: Actualiza la contraseña de un usuario

## Uso

```php
// Crear una instancia del servicio
$userService = new UserService($conexion);

// Crear un nuevo usuario
$userData = [
    'nombre' => 'Juan',
    'apellido' => 'Pérez',
    'correo' => 'juan@example.com',
    'documento' => '12345678',
    'id_tipo_documento' => 1,
    'id_rol' => 2,
    'contrasena' => 'contraseña_segura'
];

$userId = $userService->createUser($userData);

// Autenticar un usuario
$user = $userService->authenticateUser('juan@example.com', 'contraseña_segura');
```

## Notas

- El servicio realiza validaciones adicionales antes de ejecutar operaciones en la base de datos
- Se verifica la unicidad de documentos y correos electrónicos
- Las contraseñas se manejan de forma segura a través del modelo