import assert from "node:assert/strict";
import { randomUUID } from "node:crypto";
import test from "node:test";
import { createClient } from "@supabase/supabase-js";

const url = process.env.NEXT_PUBLIC_SUPABASE_URL;
const key = process.env.NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY;
const base = process.env.T6_TEST_BASE_URL || "http://web:3000";
const cookies = new Map();

function client() {
  return createClient(url, key, { auth: { persistSession: false, autoRefreshToken: false } });
}

async function request(path, options = {}) {
  const response = await fetch(new URL(path, base), {
    ...options, redirect: "manual",
    headers: { Cookie: [...cookies].map(([name, value]) => `${name}=${value}`).join("; "), ...options.headers },
  });
  for (const cookie of response.headers.getSetCookie()) {
    const pair = cookie.split(";")[0];
    cookies.set(pair.slice(0, pair.indexOf("=")), pair.slice(pair.indexOf("=") + 1));
  }
  return response;
}

async function assertNotFound(path) {
  const response = await request(path);
  // Next.js sends 200 for streamed not-found responses and 404 before streaming.
  assert.ok([200, 404].includes(response.status));
  const html = await response.text();
  assert.match(html, /404 \/ FUERA DEL MAPA/);
  assert.match(html, /<meta name="robots" content="noindex"/);
}

// React's progressive-enhancement form includes the bound Server Action metadata.
async function submit(path, fields, actionMarker = "$ACTION_REF_") {
  const page = await request(path);
  assert.equal(page.status, 200, `No se pudo abrir ${path}`);
  const html = await page.text();
  const form = html.match(/<form\b[^>]*>[\s\S]*?<\/form>/g)?.find((item) => item.includes(actionMarker));
  assert.ok(form, `No se encontro el formulario editorial en ${path}`);
  const body = new FormData();
  for (const input of form.match(/<input\b[^>]*type="hidden"[^>]*>/g) || []) {
    const name = input.match(/\bname="([^"]+)"/)?.[1];
    const value = (input.match(/\bvalue="([^"]*)"/)?.[1] || "")
      .replaceAll("&quot;", '"').replaceAll("&#x27;", "'")
      .replaceAll("&lt;", "<").replaceAll("&gt;", ">").replaceAll("&amp;", "&");
    if (name) body.append(name, value);
  }
  for (const [name, value] of Object.entries(fields)) body.set(name, String(value));
  const response = await request(path, { method: "POST", body, headers: { Origin: base } });
  assert.equal(response.status, 303, `La accion de ${path} no termino correctamente`);
}

test("Supabase real, RLS y formularios editoriales", { timeout: 120000 }, async (t) => {
  assert.ok(url && key, "Configura la conexion publica en .env.local");
  assert.ok(process.env.T6_TEST_EMAIL && process.env.T6_TEST_PASSWORD,
    "Configura T6_TEST_EMAIL y T6_TEST_PASSWORD en .env.local con un usuario editorial confirmado");
  const anon = client();
  const editor = client();
  const login = await editor.auth.signInWithPassword({
    email: process.env.T6_TEST_EMAIL, password: process.env.T6_TEST_PASSWORD,
  });
  assert.equal(login.error, null, "No se pudo autenticar la cuenta de prueba");
  const membership = await editor.from("editors").select("user_id").eq("user_id", login.data.user.id).single();
  assert.equal(membership.error, null, "Concede acceso a la cuenta en public.editors");

  const categorySlug = `t6-test-${randomUUID()}`;
  const postSlug = `t6-test-${randomUUID()}`;
  let categoryId;
  let postId;
  try {
    await t.test("lectura publica, rutas dinamicas, busqueda y proteccion del administrador", async () => {
      const posts = await anon.from("posts").select("slug,published");
      const categories = await anon.from("categories").select("slug");
      assert.equal(posts.error, null);
      assert.equal(categories.error, null);
      assert.ok(posts.data.length >= 5);
      assert.ok(categories.data.length >= 1);
      assert.ok(posts.data.every((post) => post.published));
      for (const path of ["/", `/articulos/${posts.data[0].slug}`, `/categorias/${categories.data[0].slug}`]) {
        const response = await request(path);
        assert.equal(response.status, 200, path);
      }
      const empty = await request("/articulos?q=zzzz-inexistente-zzzz");
      assert.equal(empty.status, 200);
      assert.match(await empty.text(), /No encontramos/i);
      await assertNotFound("/articulos/no-existe-este-articulo");
      const admin = await request("/admin");
      if ([303, 307].includes(admin.status)) {
        assert.match(admin.headers.get("location"), /\/acceso/);
      } else {
        assert.equal(admin.status, 200);
        const html = await admin.text();
        assert.match(html, /http-equiv="refresh" content="1;url=\/acceso"/);
        assert.doesNotMatch(html, /<h1[^>]*>Tus pr[oó]ximas ideas/);
      }
    });

    await t.test("visitantes no escriben ni pueden concederse permisos", async () => {
      const denied = await anon.from("categories").insert({
        name: "Prueba sin permisos", slug: `t6-denied-${randomUUID()}`,
        description: "Esta escritura anonima debe rechazarse.",
      });
      assert.equal(denied.error?.code, "42501");
      const hidden = await anon.from("editors").select("*");
      assert.equal(hidden.error, null);
      assert.deepEqual(hidden.data, []);
      const selfGrant = await editor.from("editors").insert({ user_id: login.data.user.id });
      assert.equal(selfGrant.error?.code, "42501");
    });

    await t.test("login y creacion desde los Server Actions del sitio", async () => {
      await submit("/acceso", { email: process.env.T6_TEST_EMAIL, password: process.env.T6_TEST_PASSWORD });
      const admin = await request("/admin");
      assert.equal(admin.status, 200);
      assert.match(await admin.text(), /Tus pr[oó]ximas ideas/);
      await submit("/admin/categorias/nueva", {
        name: "Categoria temporal T6", slug: categorySlug, description: "Categoria creada para verificar el CRUD.",
      });
      const category = await editor.from("categories").select("id").eq("slug", categorySlug).single();
      assert.equal(category.error, null);
      categoryId = category.data.id;
      await submit("/admin/articulos/nuevo", {
        title: "Articulo temporal de prueba", slug: postSlug, category_id: categoryId,
        excerpt: "Resumen temporal para verificar la publicacion de articulos.",
        content: "Este contenido pertenece exclusivamente a una prueba automatizada. Se elimina al finalizar y no modifica los articulos originales del blog.",
        cover: "code", reading_minutes: 3,
      });
      const post = await editor.from("posts").select("id,published").eq("slug", postSlug).single();
      assert.equal(post.error, null);
      postId = post.data.id;
      assert.equal(post.data.published, false);
      const draft = await anon.from("posts").select("id").eq("id", postId);
      assert.equal(draft.error, null);
      assert.deepEqual(draft.data, []);
      await assertNotFound(`/articulos/${postSlug}`);
    });

    await t.test("edicion, publicacion y rutas nuevas con invalidacion de cache", async () => {
      assert.ok(postId && categoryId, "La prueba de creacion debe haber completado");
      await submit(`/admin/categorias/${categoryId}/editar`, {
        name: "Categoria temporal editada", slug: categorySlug, description: "Descripcion actualizada desde el formulario.",
      });
      await submit(`/admin/articulos/${postId}/editar`, {
        title: "Articulo temporal editado", slug: postSlug, category_id: categoryId,
        excerpt: "Resumen actualizado para verificar la publicacion de articulos.",
        content: "Este contenido fue actualizado mediante un Server Action real. La prueba comprueba que publicar invalida la cache y habilita nuevas rutas dinamicas.",
        cover: "cloud", reading_minutes: 4, published: "on",
      });
      const post = await anon.from("posts").select("title,published,reading_minutes").eq("id", postId).single();
      assert.equal(post.error, null);
      assert.equal(post.data.title, "Articulo temporal editado");
      assert.equal(post.data.published, true);
      assert.equal(post.data.reading_minutes, 4);
      const article = await request(`/articulos/${postSlug}`);
      assert.equal(article.status, 200);
      assert.match(await article.text(), /Articulo temporal editado/);
      const category = await request(`/categorias/${categorySlug}`);
      assert.equal(category.status, 200);
      assert.match(await category.text(), /Categoria temporal editada/);
    });

    await t.test("FK evita eliminar categorias en uso; editor puede eliminar sus registros de prueba", async () => {
      assert.ok(postId && categoryId, "La prueba de creacion debe haber completado");
      const inUse = await editor.from("categories").delete().eq("id", categoryId);
      assert.equal(inUse.error?.code, "23503");
      const removedPost = await editor.from("posts").delete().eq("id", postId).select("id").single();
      assert.equal(removedPost.error, null);
      const removedCategory = await editor.from("categories").delete().eq("id", categoryId).select("id").single();
      assert.equal(removedCategory.error, null);
    });
  } finally {
    // Exact unique slugs also clean up if a form saved before an assertion failed.
    const postCleanup = await editor.from("posts").delete().eq("slug", postSlug);
    const categoryCleanup = await editor.from("categories").delete().eq("slug", categorySlug);
    assert.equal(postCleanup.error, null, "No se pudo limpiar el articulo temporal");
    assert.equal(categoryCleanup.error, null, "No se pudo limpiar la categoria temporal");
    if (cookies.size) await submit("/admin", {}, "$ACTION_ID_");
    const logout = await editor.auth.signOut();
    assert.equal(logout.error, null);
  }
});
