# Bitácora — T6 / Módulo 4

Blog tecnológico con Next.js 16 App Router y Supabase. Una landing editorial,
artículos por tema y una redacción protegida para crear, editar, publicar y
eliminar artículos y categorías.

## Enlaces de entrega

- Repositorio previsto: https://github.com/celtaced/kfsj/tree/main/T6
- Sitio local: http://localhost:3006
- **Producción: pendiente de desplegar en Vercel y agregar aquí la URL real.**

La aplicación local y su build de producción fueron comprobados con Supabase
real. Un build exitoso no sustituye el despliegue exigido por la rúbrica.

## Tecnologías

- Next.js 16.4.6, React 19, TypeScript 6 y ESLint.
- Supabase PostgreSQL, Auth, RLS y clientes SSR.
- Zod para validar formularios del servidor.
- Docker con Node.js 22; no necesitas instalar Node en Windows.
- CSS responsive e ilustraciones SVG originales, sin fuentes externas.

TypeScript 6 se utiliza porque el ESLint incluido en esta versión de Next.js
todavía no admite TypeScript 7.

## Instalación local con Docker

Desde la carpeta del proyecto:

```powershell
Set-Location "C:\ruta\al\proyecto\T6"
Copy-Item .env.example .env.local
```

Completa las variables indicadas abajo, prepara Supabase y ejecuta:

```powershell
docker compose build
docker compose run --rm --no-deps web npm ci
docker compose up -d
```

Abre http://localhost:3006. El puerto se expone únicamente en la máquina local.
Los módulos y la caché de Next.js están en volúmenes Docker para evitar el costo
de miles de archivos dentro de OneDrive.

```powershell
docker compose logs --tail 60 web
docker compose restart web
docker compose stop
```

Después de cambiar variables públicas, reinicia el servidor local; en Vercel,
realiza un nuevo despliegue.

### Alternativa sin Docker

Con Node.js 22.18 o superior, desde `T6`:

```powershell
npm ci
npm run dev
```

En este caso se usa http://localhost:3000: cambia también
`NEXT_PUBLIC_SITE_URL` y los redirects de Supabase a ese puerto.

## Variables de entorno

| Variable | Uso |
| --- | --- |
| `NEXT_PUBLIC_SUPABASE_URL` | URL del proyecto Supabase. |
| `NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY` | Clave **publishable** pública del proyecto. |
| `NEXT_PUBLIC_SITE_URL` | Origen completo de la aplicación, sin barra final; local: `http://localhost:3006`. |

No se necesita una clave `service_role` ni una contraseña de PostgreSQL.
La clave publishable identifica el proyecto; los permisos los determinan RLS
y la sesión autenticada. Nunca uses una clave secreta en una variable
`NEXT_PUBLIC_*`.

`.env.local` y las credenciales de prueba están ignorados por Git.
`.env.example` solo contiene nombres y valores de ejemplo.

## Preparar Supabase

1. Crea un proyecto Supabase.
2. En SQL Editor, ejecuta **una sola vez** la migración
   [202611270001_blog.sql](supabase/migrations/202611270001_blog.sql).
   En el proyecto Supabase usado durante el desarrollo **ya fue ejecutada**.
   No la repitas: crea tablas y carga datos, no es un script idempotente.
3. Obtén Project URL y la clave publishable desde Connect / API Keys.
4. En Authentication > URL Configuration:
   - Site URL local: `http://localhost:3006`.
   - Redirect URL: `http://localhost:3006/auth/callback`.
   - Al desplegar, configura el dominio de producción y agrega
     `https://TU-DOMINIO/auth/callback`.
5. Crea una cuenta desde `/acceso` y confirma el correo, o crea un usuario
   confirmado desde Authentication > Users.
6. Concede acceso editorial desde SQL Editor, sustituyendo el correo:

```sql
insert into public.editors (user_id)
select id from auth.users where email = 'editor@ejemplo.com'
on conflict (user_id) do nothing;
```

Comprueba que el usuario existe y que la consulta incorporó su UUID.
Crear una cuenta **no** concede permisos automáticamente.

### Modelo y políticas

- `categories`: tres categorías iniciales.
- `posts`: seis artículos iniciales, relacionados con categorías.
- `editors`: usuarios autorizados por el operador del proyecto.
- Visitantes: leen categorías y únicamente artículos publicados.
- Editores autenticados: CRUD de categorías y artículos; pueden leer borradores.
- Ningún cliente puede agregar, modificar o eliminar membresías editoriales.
- Una FK impide eliminar una categoría que tenga artículos relacionados.
- Slugs únicos, restricciones de longitud y trigger de `updated_at`.

No hay base de datos Docker duplicada: el entorno local usa el proyecto Supabase
configurado. Las modificaciones editoriales se guardan realmente en ese proyecto.

## Rutas y arquitectura

| Ruta | Comportamiento |
| --- | --- |
| `/` | Landing, artículo destacado, categorías y últimas notas. |
| `/articulos?q=texto&categoria=slug` | Búsqueda y filtro mediante `searchParams`. |
| `/articulos/[slug]` | Artículo dinámico, metadata y relacionados. |
| `/categorias/[slug]` | Categoría dinámica y sus artículos. |
| `/acerca` | Descripción del proyecto. |
| `/acceso` | Inicio de sesión y registro. |
| `/admin` | Redacción protegida y CRUD de artículos. |
| `/admin/categorias` | CRUD de categorías. |

Las páginas públicas consultan Supabase desde Server Components. Las dos rutas
públicas dinámicas usan `generateStaticParams`, `params` asíncronos e ISR de
60 segundos; los slugs nuevos pueden generarse bajo demanda. Las escrituras
editoriales invalidan las rutas para reflejar los cambios.

Los formularios, el menú móvil, las confirmaciones y los errores interactivos
son Client Components. Cada Server Action valida de nuevo la sesión, el permiso
editorial y los datos. El Proxy renueva las cookies de sesión y evita cachear
las rutas de autenticación y administración.

Hay layouts anidados, `loading.tsx`, `error.tsx`, página no encontrada,
mensajes de validación y estados vacíos. El contenido es texto plano escapado,
no HTML ejecutable. Incluye navegación por teclado, foco visible,
enlace para saltar al contenido y soporte para movimiento reducido.

Next.js puede responder con HTTP 200 si un `notFound()` o una redirección ocurre
después de empezar el streaming. La página no encontrada incluye `noindex`;
las pruebas comprueban su contenido y no solamente el código HTTP.

## Validación

Desde `T6`:

```powershell
docker compose run --rm --no-deps web npm run lint
docker compose run --rm --no-deps web npm test
docker compose run --rm --no-deps --volume t6_build:/app/.next web npm run build
```

El volumen de build separado evita sobrescribir la caché del servidor dev.
Las cinco pruebas unitarias comprueban validación y normalización de formularios.

### Integración real

Guarda **solo localmente** estas variables adicionales en `.env.local`:

```dotenv
T6_TEST_EMAIL=correo-del-editor
T6_TEST_PASSWORD=contraseña-de-prueba
```

La cuenta debe estar confirmada y pertenecer a `editors`. Con el servidor
Docker activo:

```powershell
docker compose run --rm --no-deps web npm run test:integration
```

Las pruebas verifican datos públicos, rutas, búsqueda, protección de `/admin`,
RLS, login y creación/edición/publicación a través de los formularios SSR y sus
Server Actions. También comprueban borradores ocultos, FK y eliminación mediante
la API Supabase. Crean únicamente registros temporales con slugs únicos y los
limpian al terminar; no modifican los artículos originales.

Fuera de Docker configura `T6_TEST_BASE_URL=http://localhost:3000` (o el puerto
correspondiente) antes de ejecutar `npm run test:integration`.
Estas comprobaciones HTTP no son una grabación E2E de navegador.

## Despliegue en Vercel

1. Publica esta carpeta en la rama `main` del repositorio personal.
2. Vercel > Add New Project > importa `celtaced/kfsj`.
3. **Root Directory: `T6`**. Framework Preset: Next.js.
4. Mantén el install `npm ci` y build `npm run build`.
   No uses el Dockerfile como comando de Vercel.
5. Agrega las tres variables públicas de la tabla anterior a Production.
   No agregues credenciales editoriales de prueba.
6. Despliega. Supabase debe estar activo y permitir lecturas anónimas:
   `generateStaticParams` consulta la base durante el build.
7. Configura `NEXT_PUBLIC_SITE_URL` con la URL HTTPS asignada y vuelve a desplegar.
8. Actualiza Site URL y Redirect URLs en Supabase Auth.
9. Comprueba landing, un artículo, una categoría, búsqueda y login editorial.
10. Sustituye la nota pendiente de producción de este README por la URL real
    y entrega **ambos enlaces**, Vercel y GitHub.

No se requiere un token de Vercel dentro del proyecto. La importación se hace
con la cuenta personal ya vinculada con GitHub.

## Relación con la rúbrica

| Criterio | Implementación |
| --- | --- |
| Next.js 16+ / App Router | Server y Client Components, layouts anidados, loading y error. |
| Rutas dinámicas | Artículos y categorías, SSG, params y búsqueda con searchParams. |
| Supabase | Tres tablas, relación FK, seis semillas, RLS y CRUD editorial. |
| Diseño y UX | Landing editorial responsive, estados y accesibilidad. |
| Despliegue y entregables | Docker y build comprobados; instrucciones de Vercel; pendiente URL pública verificada. |
