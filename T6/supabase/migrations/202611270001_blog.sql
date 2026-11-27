begin;

create table public.editors (
  user_id uuid primary key references auth.users(id) on delete cascade,
  created_at timestamptz not null default now()
);

create table public.categories (
  id uuid primary key default gen_random_uuid(),
  name text not null check (char_length(name) between 2 and 60),
  slug text not null unique check (slug ~ '^[a-z0-9]+(-[a-z0-9]+)*$' and char_length(slug) <= 100),
  description text not null check (char_length(description) between 10 and 240)
);

create table public.posts (
  id uuid primary key default gen_random_uuid(),
  category_id uuid not null references public.categories(id) on delete restrict,
  title text not null check (char_length(title) between 5 and 140),
  slug text not null unique check (slug ~ '^[a-z0-9]+(-[a-z0-9]+)*$' and char_length(slug) <= 160),
  excerpt text not null check (char_length(excerpt) between 20 and 300),
  content text not null check (char_length(content) between 80 and 30000),
  cover text not null default 'code' check (cover in ('code', 'cloud', 'design')),
  reading_minutes integer not null default 5 check (reading_minutes between 1 and 60),
  published boolean not null default false,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

create index posts_category_id_idx on public.posts(category_id);
create index posts_published_created_idx on public.posts(published, created_at desc);

create function public.touch_updated_at() returns trigger
language plpgsql set search_path = '' as $$
begin
  new.updated_at = now();
  return new;
end;
$$;
create trigger posts_updated_at before update on public.posts
for each row execute function public.touch_updated_at();

alter table public.editors enable row level security;
alter table public.categories enable row level security;
alter table public.posts enable row level security;

create policy "Editors can read their membership" on public.editors
for select to authenticated using (user_id = (select auth.uid()));

create policy "Public categories" on public.categories
for select to anon, authenticated using (true);

create policy "Editors create categories" on public.categories
for insert to authenticated with check (
  exists (select 1 from public.editors where user_id = (select auth.uid()))
);
create policy "Editors update categories" on public.categories
for update to authenticated
using (exists (select 1 from public.editors where user_id = (select auth.uid())))
with check (exists (select 1 from public.editors where user_id = (select auth.uid())));
create policy "Editors delete categories" on public.categories
for delete to authenticated using (
  exists (select 1 from public.editors where user_id = (select auth.uid()))
);

create policy "Read published posts or editorial drafts" on public.posts
for select to anon, authenticated using (
  published or exists (select 1 from public.editors where user_id = (select auth.uid()))
);
create policy "Editors create posts" on public.posts
for insert to authenticated with check (
  exists (select 1 from public.editors where user_id = (select auth.uid()))
);
create policy "Editors update posts" on public.posts
for update to authenticated
using (exists (select 1 from public.editors where user_id = (select auth.uid())))
with check (exists (select 1 from public.editors where user_id = (select auth.uid())));
create policy "Editors delete posts" on public.posts
for delete to authenticated using (
  exists (select 1 from public.editors where user_id = (select auth.uid()))
);

revoke all on public.editors, public.categories, public.posts from anon, authenticated;
grant select on public.categories, public.posts to anon;
grant select on public.editors to anon;
grant select, insert, update, delete on public.categories, public.posts to authenticated;
grant select on public.editors to authenticated;

insert into public.categories (id, name, slug, description) values
('10000000-0000-4000-8000-000000000001', 'Desarrollo', 'desarrollo', 'Ideas y herramientas para construir software que vale la pena.'),
('10000000-0000-4000-8000-000000000002', 'Cloud & datos', 'cloud-datos', 'Arquitecturas, bases de datos y servicios que conectan nuestras ideas.'),
('10000000-0000-4000-8000-000000000003', 'Diseño & UX', 'diseno-ux', 'Interfaces humanas, accesibles y pensadas para todos.');

insert into public.posts (category_id, title, slug, excerpt, content, cover, reading_minutes, published, created_at) values
('10000000-0000-4000-8000-000000000001', 'Menos JavaScript, mejores experiencias', 'menos-javascript-mejores-experiencias',
'Server Components cambió la forma en que pensamos la web. Una guía para enviar al navegador solo lo que realmente necesita.',
E'El mejor JavaScript es, a veces, el que no enviamos.\n\nLos Server Components de React permiten resolver consultas, transformar datos y preparar la interfaz en el servidor. El usuario recibe el contenido sin descargar toda esa lógica.\n\nEso no significa renunciar a la interactividad. Un menú, un formulario o un botón de guardar siguen siendo componentes del cliente. La clave es definir fronteras pequeñas y claras.\n\nEn Next.js, comienza con componentes del servidor y agrega la directiva use client solamente donde necesitas estado o eventos. Así reduces el peso inicial y haces que el contenido sea más fácil de descubrir.\n\nAntes de optimizar, mide. Observa el tamaño del JavaScript, la velocidad en un teléfono real y el tiempo hasta que la página responde a una interacción. La arquitectura importa cuando mejora la experiencia de alguien.',
'code', 6, true, '2026-11-26T12:00:00Z'),
('10000000-0000-4000-8000-000000000002', 'Tu base de datos también necesita límites', 'rls-seguridad-desde-los-datos',
'Una introducción práctica a Row Level Security y por qué ocultar un botón no equivale a proteger tus datos.',
E'La seguridad no empieza en un botón oculto.\n\nRow Level Security permite que PostgreSQL decida qué filas puede leer o modificar cada usuario. En Supabase, las políticas pueden consultar la identidad validada por el servicio de autenticación.\n\nUna clave pública identifica al proyecto, pero no debe dar permisos administrativos. Para un blog, cualquier visitante puede leer artículos publicados. Solo una cuenta editorial puede crear, modificar o eliminar contenido.\n\nPrueba las políticas directamente contra la API, no solo desde la interfaz. Un visitante debe ser incapaz de publicar un artículo aunque construya su propia petición HTTP.\n\nMantén una estrategia de mínimo privilegio. Habilita RLS, asigna permisos explícitos y nunca envíes una clave service_role al navegador.',
'cloud', 5, true, '2026-11-25T12:00:00Z'),
('10000000-0000-4000-8000-000000000003', 'Diseñar para la espera', 'disenar-para-la-espera',
'Skeletons, estados vacíos y errores útiles: los pequeños detalles que hacen que una aplicación se sienta cuidada.',
E'Esperar también forma parte de una interfaz.\n\nUn estado de carga bien diseñado explica qué está pasando sin prometer que todo terminó. Un skeleton mantiene la estructura visual mientras llega el contenido.\n\nLos estados vacíos son una oportunidad para guiar. En lugar de dejar una pantalla en blanco, explica por qué no hay resultados y ofrece una acción útil, como limpiar los filtros.\n\nUn error debe reconocer el problema, conservar lo que sea posible y dar una salida. No muestres detalles internos ni mensajes que culpen al usuario.\n\nRevisa las animaciones con prefers-reduced-motion y anuncia los cambios importantes a lectores de pantalla. La calidad se nota tanto en el camino feliz como cuando algo tarda o falla.',
'design', 4, true, '2026-11-24T12:00:00Z'),
('10000000-0000-4000-8000-000000000001', 'TypeScript como conversación con tu futuro yo', 'typescript-tu-futuro-yo',
'Tipos claros y validaciones en los límites para cambiar código con confianza, sin convertirlo en un laberinto.',
E'Un tipo describe una intención.\n\nTypeScript permite descubrir errores antes de ejecutar una aplicación y explica cómo deben conectarse sus piezas. Una función bien tipada comunica qué recibe, qué devuelve y qué casos debemos considerar.\n\nLos tipos no validan datos externos por sí solos. Una petición HTTP o un formulario todavía necesitan comprobaciones en tiempo de ejecución.\n\nEvita usar any para silenciar el compilador. Modela los estados posibles y valida las entradas antes de transformar datos.\n\nEl objetivo no es escribir el tipo más sofisticado. Es ayudar a la siguiente persona a entender el código, incluso cuando esa persona seas tú dentro de seis meses.',
'code', 5, true, '2026-11-23T12:00:00Z'),
('10000000-0000-4000-8000-000000000002', 'Serverless sin perder el control', 'serverless-sin-perder-control',
'Qué cambia cuando delegas la infraestructura, y qué responsabilidades siguen siendo tuyas.',
E'Serverless no significa que los servidores desaparecen.\n\nUna plataforma administra el despliegue y la infraestructura, mientras el equipo se concentra en el comportamiento del producto. Eso facilita empezar, pero no elimina la necesidad de diseñar bien.\n\nSigue siendo tu responsabilidad configurar secretos, permisos, límites y observabilidad. También debes entender los costos y las restricciones de la plataforma.\n\nSepara el entorno local de producción. Documenta las variables, comprueba los permisos de la base de datos y verifica las rutas después de cada despliegue.\n\nLa mejor plataforma es la que permite entregar valor sin esconder los problemas importantes.',
'cloud', 7, true, '2026-11-22T12:00:00Z'),
('10000000-0000-4000-8000-000000000003', 'Accesibilidad desde el primer componente', 'accesibilidad-primer-componente',
'Contraste, foco visible y HTML semántico: una base sencilla para una web que más personas puedan usar.',
E'La accesibilidad funciona mejor cuando no se deja para el final.\n\nUsa enlaces para navegar y botones para acciones. Etiqueta tus formularios y explica los errores junto al campo correspondiente.\n\nPrueba la interfaz con un teclado. El foco debe ser visible y el orden de navegación debe tener sentido.\n\nNo dependas solo del color para comunicar una categoría o un estado. Combina texto, estructura y contraste suficiente.\n\nEl HTML semántico es una gran herramienta: encabezados bien ordenados, regiones reconocibles y controles nativos mejoran la experiencia sin necesidad de inventar todo de nuevo.',
'design', 4, true, '2026-11-21T12:00:00Z');

commit;
