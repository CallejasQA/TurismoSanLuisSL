TurismoSanluisSL - Versión en español y consistente

Instrucciones rápidas:
1. Colocar la carpeta descomprimida en C:\xampp\htdocs\TurismoSanLuisSL
2. Copiar .env.example -> .env y ajustar si tu MySQL tiene contraseña
3. Importar database_schema.sql en phpMyAdmin (o ejecutar el script)
4. Iniciar Apache y MySQL desde XAMPP
5. Ejecutar UNA VEZ: http://localhost/TurismoSanLuisSL/public/sembrar_usuarios.php
6. Probar en: http://localhost/TurismoSanLuisSL/public/

Usuarios de prueba (siembra):
- admin@turismosl.com / 123456  (rol: admin)
- propietario@turismosl.com / 123456 (rol: propietario)

Notas de funcionamiento:
- La página de inicio muestra los alojamientos aprobados/activos con enlace a detalle.
- Las imágenes subidas se guardan en public/storage/subidas (ya incluida la carpeta) para que sean accesibles desde el navegador.
- Los propietarios gestionan sus alojamientos en /public/index.php?ruta=propietario/sitios.
- Los administradores validan solicitudes en /public/index.php?ruta=admin/afiliaciones y aprueban/activan alojamientos en /public/index.php?ruta=admin/alojamientos.
Después de probar, eliminar public/sembrar_usuarios.php por seguridad.

Flujo básico para sincronizar tus cambios con Git:
1) Verifica el estado actual: `git status`.
2) Trae los últimos cambios del remoto antes de trabajar: `git pull origin work` (ajusta la rama si usas otra).
3) Agrega tus modificaciones: `git add .` (o archivos específicos).
4) Crea un commit descriptivo: `git commit -m "Mensaje claro del cambio"`.
5) Envía el commit al remoto: `git push origin work`.
6) Si aparece un conflicto después de `git pull`, resuélvelo en los archivos indicados, vuelve a ejecutar `git add` y `git commit`.
