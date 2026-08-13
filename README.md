# 🌿 Limpieza y Desmalezado de Terrenos

Web corporativa para empresa de limpieza de terrenos, desarrollada con Laravel 12.

## 🚀 Características

- ✅ Posts con categorías y tags
- ✅ Panel de administración con Filament
- ✅ Subida de imágenes con Spatie Media Library
- ✅ Formulario de contacto con base de datos
- ✅ URLs semánticas para SEO (/categoria/titulo)
- ✅ Docker para desarrollo

## 📋 Requisitos

- Docker Desktop
- PHP 8.3+
- Composer

## 🛠️ Instalación

```bash
# Clonar repositorio
git clone https://github.com/TU-USUARIO/limpieza-terrenos.git
cd limpieza-terrenos

# Levantar contenedores
docker-compose up -d

# Instalar dependencias
docker-compose exec php composer install

# Configurar entorno
cp .env.example .env
docker-compose exec php php artisan key:generate

# Migrar base de datos
docker-compose exec php php artisan migrate --seed
```

## ☁️ Deploy a producción

El sitio corre en un VPS administrado con Laravel Forge. El deploy es manual: se hace `git pull` en el servidor y se corren los comandos habituales de Laravel — no hay CI/CD ni script de deploy automatizado en este repo.

### Antes de cada deploy con migraciones

1. Backup de las tablas que la migración vaya a tocar:
   ```bash
   mysqldump -u <user> -p <db_name> <tabla> > backup_<tabla>_$(date +%Y%m%d).sql
   ```
2. Revisar si la migración es aditiva (segura) o modifica columnas de tablas que ya tienen datos en producción — en ese caso, confirmar que la migración incluya un backfill antes de exigir `NOT NULL` o agregar una FK obligatoria.

### Pasos del deploy

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Después del deploy, verificar que el sitio responda normal y que el panel de Filament cargue sin errores.

### Variables de entorno específicas de producción

Además de las variables estándar de Laravel (`APP_KEY`, `DB_*`, `MAIL_*`), estas features requieren configuración propia en el `.env` del servidor:

| Variable | Para qué es |
|---|---|
| `DEEPSEEK_API_KEY` | Motor conversacional del bot de WhatsApp (Claudia) |
| `WHATSAPP_CLOUD_API_TOKEN` | Envío de mensajes/imágenes por WhatsApp Cloud API |
| `WHATSAPP_CLOUD_API_PHONE_NUMBER_ID` | Número de WhatsApp Business usado |
| `WHATSAPP_CLOUD_API_VERIFY_TOKEN` | Verificación de la URL del webhook con Meta |
| `WHATSAPP_CLOUD_API_APP_SECRET` | Valida la firma de cada webhook entrante — **en `APP_ENV=production`, si queda vacío el webhook rechaza todos los payloads** (fail-closed a propósito, ver `WhatsAppCloudApiService::verifySignature()`) |

Mientras estas variables no estén cargadas, el resto del sitio funciona normalmente — solo el bot de WhatsApp queda inactivo.

Paso manual adicional, fuera del código: dar de alta `https://<dominio>/webhook/whatsapp` en Meta Business Manager (WhatsApp > Configuration > Webhook) con el mismo `WHATSAPP_CLOUD_API_VERIFY_TOKEN`, y suscribir el campo `messages`.