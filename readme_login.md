# Sistema de Login - Gestor de Inventario

## Descripción General

El sistema de login del Gestor de Inventario permite a los usuarios:

- **Iniciar sesión** usando su documento de identidad y contraseña
- **Registrar nuevos usuarios** con sus datos personales y de sistema
- **Recuperar contraseña** a través del documento de identidad

## Funcionalidades Principales

### 1. Inicio de Sesión

- Se utiliza el **documento de identidad** como identificador principal
- Se requiere contraseña
- Opción para recordar sesión

### 2. Registro de Usuario

Datos requeridos:
- Documento de identidad
- Tipo de documento
- Nombre
- Apellido
- Correo electrónico
- Rol en el sistema
- Contraseña (con confirmación)

### 3. Recuperación de Contraseña

- Se solicita el documento de identidad
- Se envía un correo con un enlace para restablecer la contraseña
- El enlace contiene un token de seguridad válido por 1 hora

## Archivos del Sistema

### Controlador
`app/controllers/login/loginController.php`
- Maneja las solicitudes HTTP relacionadas con la autenticación
- Procesa formularios de inicio de sesión, registro y recuperación

### Modelo
`app/models/login/loginModel.php`
- Define las clases para manejar datos de autenticación
- Incluye modelos para Login, Registro y Recuperación

### Servicio
`app/service/login/loginService.php`
- Implementa la lógica de negocio
- Interactúa con la base de datos
- Maneja la autenticación, registro y recuperación

## Flujo de Autenticación

1. Usuario ingresa documento y contraseña
2. Sistema verifica credenciales en la base de datos
3. Si son correctas, se crea una sesión
4. Si el usuario marcó "recordar", se crea una cookie

## Flujo de Recuperación

1. Usuario ingresa su documento
2. Sistema envía correo con enlace de recuperación
3. Usuario accede al enlace y establece nueva contraseña
4. Sistema actualiza la contraseña y elimina el token

## Seguridad

- Contraseñas almacenadas con hash seguro (password_hash)
- Tokens de recuperación generados aleatoriamente
- Tokens con tiempo de expiración
- Validación de datos en formularios