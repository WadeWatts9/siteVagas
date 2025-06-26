# 🚀 Sistema de Gestión de Vacantes de Empleo

## 👨‍💻 Desarrollador

**Alan Canto** - Desarrollador Full Stack

---

## 📺 Demo del Proyecto

🎥 **Video Demo**: [Ver Demo del Sistema](https://youtu.be/PFNnrB5sttA)

---

## 📋 Descripción del Proyecto

Este es un sistema completo de gestión de vacantes de empleo desarrollado en **PHP** con base de datos **SQLite**. El sistema permite a las empresas publicar ofertas de trabajo y a los usuarios buscar y aplicar a las vacantes disponibles.

### ✨ Características Principales

- 🎨 **Diseño Moderno**: Interfaz atractiva con esquema de colores naranja
- 👥 **Gestión de Usuarios**: Registro, login y perfiles de usuarios
- 🏢 **Gestión de Empresas**: Publicación y administración de vacantes
- 📂 **Categorización**: Organización de vacantes por categorías
- 🔍 **Búsqueda y Filtros**: Filtrado por categorías y búsqueda avanzada
- 📱 **Responsive**: Diseño adaptable a dispositivos móviles
- 🔐 **Sistema de Roles**: Administradores y usuarios regulares
- 📊 **Panel de Administración**: Gestión completa del sistema

---

## 🛠️ Requisitos del Sistema

### 📋 Requisitos Mínimos

- **PHP**: Versión 7.4 o superior
- **SQLite**: Incluido con PHP
- **Navegador Web**: Chrome, Firefox, Safari, Edge
- **Servidor Web**: Apache, Nginx o servidor de desarrollo PHP

### 💻 Requisitos Recomendados

- **PHP**: Versión 8.0 o superior
- **RAM**: 512MB mínimo
- **Espacio en Disco**: 100MB disponibles
- **Conexión a Internet**: Para cargar recursos externos (Bootstrap, FontAwesome)

---

## 🚀 Instalación y Configuración

### 📥 Paso 1: Clonar o Descargar el Proyecto

```bash
git clone [URL_DEL_REPOSITORIO]
cd siteVagas
```

### 🔧 Paso 2: Verificar PHP

```bash
php --version
```

Si PHP no está instalado:

**En macOS (con Homebrew):**
```bash
brew install php
```

**En Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install php php-sqlite3
```

**En Windows:**
Descargar desde php.net

### 🗄️ Paso 3: Configurar la Base de Datos

La base de datos SQLite se crea automáticamente en la primera ejecución:
- Ubicación: `database/job_posting_system.db`
- Las tablas se crean automáticamente
- Se crea un usuario administrador por defecto

### 🚀 Paso 4: Ejecutar el Servidor

```bash
php -S localhost:8000
```

### 🌐 Paso 5: Acceder al Sistema

Abrir el navegador y visitar:
```
http://localhost:8000
```

---

## 👤 Usuarios por Defecto

### 🔑 Administrador
- **Email**: admin@example.com
- **Contraseña**: password
- **Rol**: Administrador

### 👤 Usuario Regular
- Crear cuenta nueva desde la página de registro

---

## 📁 Estructura del Proyecto

```
siteVagas/
├── 📁 admin/                 # Panel de administración
│   ├── categories.php       # Gestión de categorías
│   └── jobs.php            # Gestión de vacantes
├── 📁 assets/               # Recursos estáticos
│   └── 📁 css/
│       └── style.css       # Estilos principales
├── 📁 config/               # Configuración
│   ├── database.php        # Configuración principal
│   └── database_sqlite.php # Configuración SQLite
├── 📁 database/             # Base de datos
│   └── job_posting_system.db
├── 📁 includes/             # Archivos incluidos
├── 🔧 index.php            # Página principal
├── 🔐 login.php            # Página de login
├── 📝 register.php         # Página de registro
├── 👤 profile.php          # Perfil de usuario
├── 💼 job_details.php      # Detalles de vacante
├── 🚪 logout.php           # Cerrar sesión
└── 📊 seed_data.php        # Datos de ejemplo
```

---

## 🎯 Funcionalidades del Sistema

### 👥 Gestión de Usuarios

#### 🔐 Registro e Inicio de Sesión
- Registro de nuevos usuarios
- Inicio de sesión seguro
- Recuperación de contraseña
- Perfiles de usuario personalizables

#### 👤 Perfil de Usuario
- Información personal
- Foto de perfil
- Enlaces a redes sociales (LinkedIn)
- Historial de aplicaciones

### 🏢 Gestión de Vacantes

#### 📝 Publicación de Vacantes
- Título y descripción
- Requisitos del puesto
- Información de la empresa
- Ubicación y salario
- Categorización
- Logo de la empresa

#### 🔍 Búsqueda y Filtros
- Filtrado por categorías
- Búsqueda por palabras clave
- Ordenamiento por fecha
- Vista de vacantes activas

#### 📋 Aplicación a Vacantes
- Sistema de postulación
- Estado de aplicaciones
- Seguimiento de candidaturas

### 🔧 Panel de Administración

#### 📂 Gestión de Categorías
- Crear nuevas categorías
- Editar categorías existentes
- Eliminar categorías
- Organización jerárquica

#### 💼 Gestión de Vacantes
- Aprobar/rechazar vacantes
- Editar información
- Activar/desactivar
- Estadísticas de visualizaciones

---

## 🎨 Personalización

### 🎨 Esquema de Colores

El sistema utiliza un esquema de colores naranja personalizable:

```css
:root {
    --primary-color: #ff6b35;
    --secondary-color: #f7931e;
    --accent-color: #ffd23f;
    /* ... más colores */
}
```

### 📱 Diseño Responsive

- Adaptable a dispositivos móviles
- Navegación optimizada para touch
- Imágenes responsivas
- Tipografía escalable

---

## 🔒 Seguridad

### 🛡️ Medidas Implementadas

- **Autenticación Segura**: Hashing de contraseñas con `password_hash()`
- **Protección SQL**: Consultas preparadas con PDO
- **Validación de Datos**: Sanitización de entradas
- **Sesiones Seguras**: Gestión de sesiones PHP
- **CSRF Protection**: Tokens de seguridad

### 🔐 Roles y Permisos

- **Administrador**: Acceso completo al sistema
- **Usuario Regular**: Aplicar a vacantes y gestionar perfil
- **Empresa**: Publicar y gestionar vacantes propias

---

## 📊 Base de Datos

### 🗄️ Estructura de Tablas

#### 👥 Users
- Información de usuarios
- Roles y permisos
- Datos de perfil

#### 📂 Categories
- Categorías de vacantes
- Organización jerárquica

#### 💼 Job_Postings
- Información de vacantes
- Relaciones con categorías y empresas
- Estados de publicación

#### 📋 Job_Applications
- Aplicaciones de usuarios
- Estados de candidatura
- Historial de postulaciones

---

## 🚀 Despliegue en Producción

### 🌐 Servidor Web

**Apache:**
```apache
<VirtualHost *:80>
    ServerName tu-dominio.com
    DocumentRoot /ruta/al/proyecto
    
    <Directory /ruta/al/proyecto>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx:**
```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    root /ruta/al/proyecto;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

### 🔧 Configuración de Producción

1. **Permisos de Archivos:**
```bash
chmod 755 /ruta/al/proyecto
chmod 644 /ruta/al/proyecto/database/job_posting_system.db
```

2. **Configuración PHP:**
```ini
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
```

3. **Backup de Base de Datos:**
```bash
sqlite3 database/job_posting_system.db ".backup backup_$(date +%Y%m%d).db"
```

---

## 🐛 Solución de Problemas

### ❌ Errores Comunes

#### 🔧 Error de Conexión a Base de Datos
```bash
# Verificar permisos
chmod 755 database/
chmod 644 database/job_posting_system.db
```

#### 🚫 Error 500 - Servidor Interno
```bash
# Verificar logs de PHP
tail -f /var/log/php_errors.log
```

#### 📱 Problemas de Diseño Responsive
- Verificar CSS en `assets/css/style.css`
- Probar en diferentes navegadores
- Usar herramientas de desarrollo del navegador

### 🔍 Debug

Habilitar modo debug:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

---

## 📈 Mejoras Futuras

### 🚀 Funcionalidades Planificadas

- 📧 **Sistema de Notificaciones**: Email y push notifications
- 📊 **Analytics Avanzado**: Estadísticas detalladas
- 🤖 **Chatbot**: Asistente virtual para usuarios
- 📱 **App Móvil**: Aplicación nativa iOS/Android
- 🔍 **Búsqueda Avanzada**: Filtros múltiples y geolocalización
- 📄 **Generador de CV**: Creación de currículums
- 🎯 **Recomendaciones**: IA para sugerir vacantes
- 🌍 **Multiidioma**: Soporte para múltiples idiomas

---

## 👥 Contribución

### 🤝 Cómo Contribuir

1. **Fork** el proyecto
2. Crea una **rama** para tu feature (`git checkout -b feature/AmazingFeature`)
3. **Commit** tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. **Push** a la rama (`git push origin feature/AmazingFeature`)
5. Abre un **Pull Request**

### 📝 Estándares de Código

- Usar **PSR-12** para PHP
- Comentar código complejo
- Seguir convenciones de nomenclatura
- Incluir documentación para nuevas funcionalidades

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

---

## 📞 Soporte

### 📧 Contacto

- **Email**: soporte@ejemplo.com
- **Documentación**: Wiki del Proyecto
- **Issues**: GitHub Issues

### 🆘 Reportar Bugs

1. Verificar si el problema ya fue reportado
2. Crear un issue con:
   - Descripción detallada del problema
   - Pasos para reproducir
   - Información del sistema
   - Capturas de pantalla (si aplica)

---

## 🙏 Agradecimientos

- **Bootstrap** por el framework CSS
- **FontAwesome** por los iconos
- **Inter Font** por la tipografía
- **SQLite** por la base de datos ligera
- **PHP** por el lenguaje de programación

---

## 📊 Estadísticas del Proyecto

- **Líneas de Código**: ~2,500
- **Archivos**: 15+
- **Funcionalidades**: 20+
- **Tiempo de Desarrollo**: 2 semanas
- **Tecnologías**: PHP, SQLite, HTML5, CSS3, JavaScript

---

*¡Gracias por usar nuestro Sistema de Gestión de Vacantes! 🎉* 