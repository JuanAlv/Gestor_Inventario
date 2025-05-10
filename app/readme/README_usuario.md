# Documentación de Componentes de Usuario

Este documento describe la estructura y relaciones entre los componentes relacionados con la gestión de usuarios en el sistema de Gestor de Inventario.

## Estructura de Archivos

```
app/
├── models/
│   └── usuario/
│       └── usuarioModel.php    # Modelo de datos de Usuario
├── service/
│   └── usuario/
│       └── usuarioService.php  # Servicios para gestión de usuarios
└── controllers/
    └── usuario/
        └── usuarioController.php # Controlador de usuarios
```

## Descripción de Componentes

### Modelo de Usuario (usuarioModel.php)

Clase que define la estructura de datos de un usuario en el sistema.

- **Propiedades**: id, nombre, apellido, correo, documento, id_tipo_documento, id_rol, contrasena, id_estado
- **Métodos**: Constructor que inicializa las propiedades desde un array de datos

### Servicio de Usuario (usuarioService.php)

Clase que proporciona métodos para interactuar con la base de datos relacionados con usuarios.

- **Dependencias**: Requiere el modelo de Usuario
- **Métodos principales**:
  - `crearUsuario`: Crea un nuevo usuario en la base de datos
  - `existeDocumentoOCorreo`: Verifica si ya existe un usuario con el mismo documento o correo
  - `existeDocumentoOCorreoExcluyendoUsuario`: Similar al anterior pero excluye al usuario actual
  - `obtenerUsuarios`: Obtiene todos los usuarios
  - `obtenerUsuarioPorId`: Obtiene un usuario específico por su ID
  - `eliminarUsuario`: Elimina un usuario
  - `actualizarUsuario`: Actualiza la información de un usuario
  - `buscar`: Busca usuarios por término
  - `obtenerUsuariosConInfo`: Obtiene usuarios con información adicional (roles, tipos de documento, etc.)
  - `obtenerUsuarioConInfoPorId`: Obtiene un usuario específico con información adicional

### Controlador de Usuario (usuarioController.php)

Clase que maneja las solicitudes relacionadas con usuarios y coordina la lógica de negocio.

- **Dependencias**: Requiere el servicio de Usuario, el modelo de Usuario y la conexión a la base de datos
- **Métodos principales**:
  - Métodos para crear, actualizar, eliminar y obtener usuarios
  - Validación de datos de usuario
  - Manejo de errores y respuestas

## Flujo de Datos

1. El **Controlador** recibe solicitudes del cliente
2. El **Controlador** valida los datos y utiliza el **Servicio** para realizar operaciones
3. El **Servicio** utiliza el **Modelo** para estructurar los datos
4. El **Servicio** interactúa con la base de datos
5. El **Controlador** devuelve respuestas al cliente

## Relaciones entre Componentes

- El **Controlador** depende del **Servicio** y del **Modelo**
- El **Servicio** depende del **Modelo**
- El **Modelo** no tiene dependencias de los otros componentes

Esta arquitectura sigue el patrón MVC (Modelo-Vista-Controlador) donde:
- **Modelo**: Representa los datos y la lógica de negocio
- **Servicio**: Proporciona una capa de abstracción para operaciones de datos
- **Controlador**: Maneja las solicitudes y coordina las respuestas