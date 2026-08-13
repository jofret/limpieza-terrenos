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

El sitio vive en hosting compartido de Hostinger (no Forge), en `~/domains/limpieza-y-desmalezado-de-terrenos.com.ar/`. La estructura tiene una particularidad: este repo es un monorepo (`docker/`, `README.md`, `src/` con el Laravel real adentro), pero el sitio en el servidor espera el contenido de Laravel **en la raíz** de la carpeta `laravel/` — sin la carpeta `src/` de por medio. Por eso `laravel/` en el servidor no clona el repo tal cual: sigue la rama `deploy`, que es una versión aplanada de `src/` generada con `git subtree`.

`public_html/` es el document root real (con su propio `index.php` que apunta a `../laravel/`), y `public_html/storage` es un symlink a `laravel/storage/app/public` — eso ya está armado, no hace falta `storage:link` en cada deploy.

El PHP del sistema (`php` en el PATH) es 7.4 — inservible para este proyecto. Hay que usar siempre `/opt/alt/php83/usr/bin/php` explícitamente, tanto para `artisan` como para invocar `composer`.

### 1. Regenerar y publicar la rama `deploy` (en tu máquina, antes de tocar el servidor)

Cada vez que haya cambios en `src/` para desplegar:

```bash
git branch -D deploy 2>/dev/null
git subtree split --prefix=src -b deploy
git push origin deploy --force-with-lease
```

### 2. Backup en el servidor, antes de migrar

```bash
mysqldump -u <db_user> -p'<db_pass>' <db_name> | gzip > ~/backups-produccion/limpieza_db_PREDEPLOY_$(date +%Y%m%d).sql.gz
```

Si la migración modifica una columna/tabla que ya tiene datos en producción (no solo `Schema::create`), confirmar que incluya un backfill antes de exigir `NOT NULL` o agregar una FK obligatoria — no asumir que porque corrió bien en dev va a correr bien en prod con datos reales.

### 3. Deploy en sí (en el servidor, dentro de `laravel/`)

```bash
PHP=/opt/alt/php83/usr/bin/php

git pull origin deploy
$PHP /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction
$PHP artisan migrate --force
$PHP artisan config:clear && $PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
```

Después del deploy, verificar que el sitio responda (`curl -I https://limpieza-y-desmalezado-de-terrenos.com.ar/`), que `/admin` redirija a login, y que el panel de Filament cargue sin errores.

### Nota sobre archivos que existen en el servidor pero no en git

Al adoptar `laravel/` como checkout git (agosto 2026) aparecieron varios archivos que corrían en producción sin estar versionados: `app/Console/Commands/GenerateSitemap.php` y cuatro migraciones de `posts` de marzo 2026. Git no los tocó (quedaron como untracked), pero conviene en algún momento agregarlos al repo para que dejen de ser una diferencia invisible entre server y código fuente.

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