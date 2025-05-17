# Documentación del Sistema de Login

## Estructura de Archivos

```
app/
├── models/
│   └── login/
│       └── loginModel.php    # Modelos de datos para Login, RegistroUsuario, RecuperacionContrasena y UsuarioAutenticado
├── service/
│   └── login/
│       └── loginService.php  # Servicios para autenticación, registro y recuperación de contraseña
└── controllers/
    └── login/
        └── loginController.php # Controlador de login
```

## Descripción General

El sistema de login proporciona funcionalidades para la autenticación de usuarios mediante documento de identidad, registro de nuevos usuarios, recuperación de contraseña y gestión de sesiones en el sistema de Gestión de Inventario.

## Componentes

### Modelos

#### Login
Clase que encapsula los datos de inicio de sesión:
- `documento`: Documento de identidad del usuario
- `contrasena`: Contraseña del usuario
- `recordar`: Indica si se debe mantener la sesión activa

#### RegistroUsuario
Clase que encapsula los datos para el registro de un nuevo usuario:
- `nombre`: Nombre del usuario
- `apellido`: Apellido del usuario
- `correo`: Correo electrónico del usuario
- `documento`: Documento de identidad del usuario
- `id_tipo_documento`: Tipo de documento (CC, TI, CE, etc.)
- `id_rol`: Rol del usuario en el sistema
- `contrasena`: Contraseña del usuario
- `confirmar_contrasena`: Confirmación de la contraseña

#### RecuperacionContrasena
Clase que encapsula los datos para la recuperación de contraseña:
- `documento`: Documento de identidad del usuario
- `token`: Token de recuperación
- `nueva_contrasena`: Nueva contraseña
- `confirmar_contrasena`: Confirmación de la nueva contraseña

#### UsuarioAutenticado
Clase que almacena la información del usuario que ha iniciado sesión:
- Datos personales (nombre, apellido, correo, documento)
- Información de sistema (id, roles, estado)

### Servicios

#### LoginService
Proporciona la lógica de negocio para la autenticación, registro y recuperación de contraseña:
- `autenticar()`: Verifica las credenciales del usuario usando el documento
- `registrarUsuario()`: Registra un nuevo usuario en el sistema
- `recuperarContrasena()`: Inicia el proceso de recuperación de contraseña
- `verificarTokenRecuperacion()`: Verifica si un token de recuperación es válido
- `cambiarContrasena()`: Cambia la contraseña de un usuario
- `cerrarSesion()`: Finaliza la sesión del usuario
- `estaAutenticado()`: Verifica si hay una sesión activa
- `obtenerUsuarioAutenticado()`: Recupera los datos del usuario en sesión

### Controladores

#### LoginController
Gestiona las solicitudes relacionadas con la autenticación, registro y recuperación de contraseña:
- `procesarInicioSesion()`: Maneja el proceso de login con documento
- `procesarRegistroUsuario()`: Maneja el registro de nuevos usuarios
- `procesarRecuperacionContrasena()`: Maneja la solicitud de recuperación de contraseña
- `procesarCambioContrasena()`: Maneja el cambio de contraseña
- `procesarCierreSesion()`: Maneja el cierre de sesión
- `requiereAutenticacion()`: Restringe acceso a usuarios no autenticados
- `requiereNoAutenticacion()`: Restringe acceso a usuarios ya autenticados

## Flujo de Autenticación

1. El usuario ingresa su documento y contraseña en el formulario de login
2. El controlador recibe la solicitud y la procesa
3. El servicio verifica las credenciales contra la base de datos
4. Si son válidas, se crea una sesión y se almacena la información del usuario
5. El usuario es redirigido al panel principal

## Flujo de Registro

1. El usuario completa el formulario de registro con sus datos personales
2. El controlador valida los datos y los envía al servicio
3. El servicio verifica que no exista otro usuario con el mismo documento o correo
4. Si no existe, se crea el nuevo usuario con estado activo
5. El usuario puede iniciar sesión inmediatamente

## Flujo de Recuperación de Contraseña

1. El usuario ingresa su documento en el formulario de recuperación
2. El sistema envía un correo con un enlace que contiene un token único
3. El usuario accede al enlace y establece una nueva contraseña
4. El sistema verifica el token y actualiza la contraseña

## Seguridad

- Las contraseñas se almacenan utilizando `password_hash()` y se verifican con `password_verify()`
- Los tokens de recuperación son generados aleatoriamente y tienen un tiempo de expiración
- Se implementa protección contra accesos no autorizados mediante redirecciones
- Las sesiones se gestionan de forma segura con opciones de recordatorio opcional

## Integración con el Sistema

El sistema de login está integrado con el resto de la aplicación mediante:
- Verificación de autenticación en cada página protegida
- Control de acceso basado en roles de usuario
- Redirecciones automáticas según el estado de autenticación