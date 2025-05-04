# Módulo de Modelos de Usuarios

Este directorio contiene las clases de modelo para la gestión de usuarios en el sistema de inventario.

## Estructura

- `User.php`: Clase principal que representa un usuario en el sistema.

## Clase User

La clase `User` implementa el modelo de datos para la tabla `usuarios` de la base de datos. Proporciona métodos para:

- Acceder y modificar los atributos de un usuario (getters y setters)
- Realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
- Buscar usuarios por diferentes criterios (ID, documento, correo)
- Autenticar usuarios mediante credenciales

## Atributos

Los atributos de la clase corresponden a los campos de la tabla `usuarios` en la base de datos:

- `id`: Identificador único del usuario
- `nombre`: Nombre del usuario
- `apellido`: Apellido del usuario
- `correo`: Correo electrónico (único)
- `documento`: Número de documento (único)
- `id_tipo_documento`: Tipo de documento (referencia a la tabla `tipo_documentos`)
- `id_rol`: Rol del usuario (referencia a la tabla `tipo_roles`)
- `contrasena`: Contraseña del usuario (almacenada con hash)
- `estado`: Estado del usuario ('activo' o 'inactivo')

## Uso

```php
// Crear una instancia del modelo
$userModel = new User($conexion);

// Buscar un usuario por ID
$userModel->findById(1);

// Crear un nuevo usuario
$userModel->setNombre("Juan");
$userModel->setApellido("Pérez");
// ... configurar demás atributos
$userModel->save();
```

## Notas

- Las contraseñas se almacenan utilizando la función `password_hash()` de PHP para mayor seguridad.
- Los métodos de búsqueda devuelven `true` si encuentran el usuario y cargan sus datos en la instancia actual.
- El método `findAll()` devuelve un array de objetos `User`.