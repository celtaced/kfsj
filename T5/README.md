# T5 - API de E-commerce segura

API REST educativa construida con Laravel 12, Sanctum, MySQL, Stripe y Swagger UI.
Los importes se expresan en centavos de USD; nunca se reciben numeros de tarjeta
en esta API.

## Requisitos

- Docker Desktop y Docker Compose.
- Para probar pagos, una cuenta Stripe en modo de prueba y Stripe CLI.

El contenedor usa PHP de la imagen `composer:2` (PHP 8.4 o superior segun su
version), Composer y la extension PDO MySQL. La version exacta de dependencias
se encuentra en `composer.lock`.

## Inicio en Windows con Docker

Desde PowerShell, en esta carpeta:

```powershell
Copy-Item .env.example .env
docker compose build
docker compose run --rm --no-deps app composer install
docker compose run --rm --no-deps app php artisan key:generate
docker compose up -d
docker compose exec app php artisan migrate --seed
```

No sobrescribas `.env` si ya configuraste credenciales. Espera a que MySQL
termine de iniciar. El primer arranque puede tardar varios minutos.

- Swagger UI: <http://localhost:8002/api/documentation>
- Especificacion OpenAPI: <http://localhost:8002/openapi.yaml>
- Catalogo: <http://localhost:8002/api/products>

Detener: `docker compose down`. MySQL y las dependencias de Composer quedan en
volumenes Docker; el volumen `t5_vendor` evita instalar dependencias dentro de
la carpeta sincronizada. El codigo fuente permanece en esta carpeta.
Para ver errores: `docker compose logs app` o `docker compose logs db`.

La configuracion MySQL incluida es **solo para desarrollo local**; modifica las
credenciales antes de cualquier otro uso. No se publica el puerto de MySQL.
El puerto web solo escucha en `127.0.0.1`. Para un despliegue real se requiere
HTTPS, contrasenas seguras, `APP_DEBUG=false` y una configuracion de red segura.

## Autenticacion y catalogo

1. En Swagger, ejecuta `POST /api/register` con nombre, correo, contrasena de
   al menos 8 caracteres y su confirmacion.
2. Copia el `token` de la respuesta en **Authorize**. Tambien puedes obtener
   un token con `POST /api/login`. `POST /api/logout` revoca el token actual.
3. El catalogo es publico. Para crear, editar o eliminar productos, una cuenta
   debe ser administradora. Despues de registrarla, un operador puede otorgar
   permisos desde la terminal (nunca mediante el registro publico):

```powershell
docker compose exec app php artisan user:make-admin tu-correo@ejemplo.com
```

Los clientes pueden consultar y comprar, pero no administrar el catalogo.
El registro e inicio de sesion tienen limite de intentos.

## Flujo de compra y Stripe

1. Configura `STRIPE_SECRET` con tu clave **secreta de prueba** (`sk_test_...`)
   en `.env`; nunca la incluyas en Git. La moneda predeterminada es `usd`.
2. Usa `POST /api/orders` con IDs y cantidades. El servidor calcula los precios
   y guarda una copia de los productos en la orden; ignora precios enviados por
   el cliente.
3. Llama a `POST /api/orders/{id}/payment-intent`. La respuesta contiene
   `client_secret`; entregalo solo al propietario para confirmar el pago desde
   Stripe.js o una SDK oficial. No registres ese secreto en logs ni lo compartas.
4. El estado definitivo llega mediante `POST /api/stripe/webhook`. Para una
   prueba local, inicia Stripe CLI en modo prueba:

```powershell
stripe listen --events payment_intent.succeeded,payment_intent.payment_failed --forward-to localhost:8002/api/stripe/webhook
```

5. Copia el secreto `whsec_...` mostrado por Stripe CLI en
   `STRIPE_WEBHOOK_SECRET` de `.env`, y ejecuta:

```powershell
docker compose exec app php artisan config:clear
```

Stripe firma los eventos; el servidor comprueba la firma, importe y moneda,
registra exito o fallo y tolera reenvios sin convertir un pago exitoso en fallo.
El cliente no puede marcar una orden como pagada. Usa tarjetas de prueba
oficiales de Stripe solo en su formulario o SDK, nunca en peticiones a esta API.
Si el webhook falla, Stripe lo reintentara. Una orden ajena responde 404.

## Endpoints

La documentacion interactiva detalla solicitudes, respuestas y codigos:

| Metodo | Ruta | Acceso |
|---|---|---|
| POST | `/api/register`, `/api/login` | Publico |
| POST | `/api/logout` | Token |
| GET | `/api/products`, `/api/products/{id}` | Publico |
| POST | `/api/products` | Administrador |
| PUT/PATCH/DELETE | `/api/products/{id}` | Administrador |
| GET/POST | `/api/orders` | Token |
| GET | `/api/orders/{id}` | Propietario |
| POST | `/api/orders/{id}/payment-intent` | Propietario |
| POST | `/api/stripe/webhook` | Firma Stripe |

## Pruebas

```powershell
docker compose exec app php artisan test
```

Las pruebas usan SQLite en memoria y simulan webhooks firmados; no realizan
cobros. Para validar Stripe de extremo a extremo se necesitan credenciales y
una integracion cliente con Stripe.js o una SDK oficial. Los ejemplos no son
credenciales reales.

## Organizacion

- `routes/api.php`: rutas REST.
- `app/Http/Requests`: validaciones Form Request.
- `app/Http/Controllers`: interfaz HTTP; `app/Services`: logica de ordenes y pagos.
- `app/Models` y `database/migrations`: modelos y esquema relacional.
- `database/seeders`: productos de ejemplo.
- `public/openapi.yaml`: contrato OpenAPI; Swagger UI se sirve desde Composer.

Material de referencia: clases M3S6 a M3S10 (PHP, MVC, Eloquent, APIs REST,
autenticacion, validacion, OWASP y OpenAPI).
