# Módulo de Controladores de Usuarios

Este directorio contiene las clases controladoras para la gestión de usuarios en el sistema de inventario.

## Estructura

- `UserController.php`: Clase principal que maneja las solicitudes relacionadas con usuarios.

## Clase UserController

La clase `UserController` actúa como intermediario entre las vistas/interfaces y los servicios de negocio. Se encarga de:

- Recibir y validar las solicitudes del cliente
- Invocar los servicios apropiados
- Formatear y devolver las respuestas

## Funcionalidades

### Gestión de Usuarios

- `create($requestData)`: Procesa la solicitud para crear un nuevo usuario
- `update($userId, $requestData)`: Procesa la solicitud para actualizar un usuario existente
- `delete($userId)`: Procesa la solicitud para eliminar un usuario
- `getById($userId)`: Obtiene la información de un usuario específico
- `getAll()`: Obtiene la lista de todos los usuarios

### Autenticación y Seguridad

- `login($requestData)`: Procesa la solicitud de inicio de sesión
- `logout()`: Cierra la sesión del usuario actual
- `changePassword($userId, $requestData)`: Procesa la solicitud para cambiar la contraseña

## Respuestas

Todas las respuestas del controlador siguen un formato estándar:

```php
[
    'success' => true/false,  // Indica si la operación fue exitosa
    'message' => 'Mensaje descriptivo',  // Mensaje informativo o de error
    // Datos adicionales según la operación
]
```

## Uso

```php
// Crear una instancia del controlador
$userController = new UserController($conexion);

// Procesar una solicitud de inicio de sesión
$requestData = [
    'correo' => 'usuario@example.com',
    'contrasena' => 'contraseña_segura'
];

$response = $userController->login($requestData);

// Obtener todos los usuarios
$response = $userController->getAll();
```

## Notas

- El controlador realiza validaciones básicas de los datos de entrada
- Las respuestas incluyen mensajes descriptivos para facilitar el manejo de errores
- Las sesiones de usuario se gestionan mediante las funciones nativas de PHP