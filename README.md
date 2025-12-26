# Turismo San Luis SL
Portal web para gestionar alojamientos turísticos en San Luis (Glamping, hoteles, cabañas, ecohoteles, etc.). Permite publicar propiedades, administrar reservas y valoraciones, y controlar la experiencia desde diferentes roles (visitante, cliente, propietario y administrador).

## Características principales
- **Catálogo público:** inicio con alojamientos activos, carrusel destacado y filtros por ubicación, operador y calificación.
- **Detalle del alojamiento:** información, servicios, galería y valoraciones; los clientes pueden reservar y dejar comentarios si ya finalizaron una estadía.
- **Registro y autenticación:** flujo para afiliados (propietarios) y clientes; validaciones de correo, contraseña y datos de negocio.
- **Panel del propietario:** crear, editar o eliminar alojamientos, cargar imágenes, seleccionar servicios, revisar agenda y reservas, y gestionar valoraciones recibidas.
- **Panel del administrador:** aprobar o rechazar solicitudes de afiliación y alojamientos, activar/desactivar propiedades, marcar alojamientos en el slider, administrar servicios ofrecidos y configurar la imagen de fondo.
- **Gestión de clientes y reservas:** módulos para revisar reservas (agenda y estados), ver listados de clientes y moderar valoraciones.

## Requisitos
- PHP 8+ con extensiones mysqli/PDO habilitadas.
- Servidor web (Apache recomendado; se puede usar XAMPP/Laragon/WAMP).
- MySQL/MariaDB.
- Acceso a un entorno donde puedas servir el directorio `public/` como raíz pública.

## Instalación y configuración rápida
1. **Clonar o copiar** este repositorio dentro del directorio público de tu servidor web, por ejemplo `C:\xampp\htdocs\TurismoSanLuisSL` en Windows o `/var/www/html/TurismoSanLuisSL` en Linux/macOS.
2. **Configurar variables de entorno:** duplica `.env.example` como `.env` y ajusta los valores:
   ```
   DB_HOST=127.0.0.1
   DB_NAME=turismo_sanluis_db
   DB_USER=root
   DB_PASS=
   APP_URL=http://localhost/TurismoSanluisSL/public
   ```
3. **Crear la base de datos:** importa `database_schema.sql` desde phpMyAdmin o ejecuta el script con tu cliente MySQL para crear tablas y datos básicos (usuarios demo incluidos).
4. **Iniciar servicios:** levanta Apache y MySQL desde tu stack (XAMPP, etc.).
5. **Sembrar usuarios (opcional si ya importaste el SQL):** visita una sola vez `http://localhost/TurismoSanLuisSL/public/sembrar_usuarios.php` para crear cuentas demo si no existen.
6. **Probar la app:** entra a `http://localhost/TurismoSanLuisSL/public/`.

## Usuarios demo
- Administrador: `admin@turismosl.com` / `123456`
- Propietario: `propietario@turismosl.com` / `123456`
- Cliente: `cliente@turismosl.com` / `123456`

> Por seguridad, elimina `public/sembrar_usuarios.php` después de usarlo en producción.

## Rutas útiles
- Inicio público: `/public/index.php`
- Registro propietario (afiliado): `/public/index.php?ruta=auth/register`
- Registro cliente: `/public/index.php?ruta=auth/register-cliente`
- Login: `/public/index.php?ruta=auth/login`
- Panel propietario: `/public/index.php?ruta=propietario/sitios`
- Panel administrador:
  - Afiliaciones: `/public/index.php?ruta=admin/afiliaciones`
  - Alojamientos: `/public/index.php?ruta=admin/alojamientos`
  - Servicios: `/public/index.php?ruta=admin/servicios`
  - Configuración: `/public/index.php?ruta=admin/configuracion`

## Archivos y carpetas relevantes
- `public/`: raíz pública de la aplicación (CSS, index.php, semillas, subidas).
- `public/storage/subidas/`: se guardan las imágenes cargadas de alojamientos.
- `controladores/`, `modelos/`, `vistas/`: MVC simple sin framework, organizado por roles/módulos.
- `config/config.php`: lee `.env` y expone las constantes de conexión y `APP_URL`.

## Recomendaciones
- Revisa los permisos de `public/storage/subidas` y `public/uploads` para permitir escritura del servidor web.
- Mantén el archivo `.env` fuera del control de versiones y configura contraseñas seguras en tu servidor.
- Después de pruebas locales, elimina semillas y credenciales por defecto antes de exponer la app públicamente.
