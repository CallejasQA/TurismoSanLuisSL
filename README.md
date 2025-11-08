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

Después de probar, eliminar public/sembrar_usuarios.php por seguridad.
