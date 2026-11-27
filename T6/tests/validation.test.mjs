import assert from "node:assert/strict";
import test from "node:test";
import { categorySchema, postSchema } from "../src/lib/validation.ts";

const post = {
  title: "Una idea para compartir",
  slug: "una-idea-para-compartir",
  category_id: "10000000-0000-4000-8000-000000000001",
  excerpt: "Una introduccion suficientemente larga para el articulo.",
  content: "Un articulo con contenido que supera la longitud minima requerida para publicarlo en el blog.",
  cover: "code",
  reading_minutes: "5",
  published: false,
};

test("normaliza texto y minutos de lectura recibidos del formulario", () => {
  const parsed = postSchema.parse({ ...post, title: `  ${post.title}  ` });
  assert.equal(parsed.title, post.title);
  assert.equal(parsed.reading_minutes, 5);
  assert.equal(parsed.published, false);
});

test("rechaza slugs que no pueden usarse como rutas publicas", () => {
  for (const slug of ["", "Mi-Idea", "dos palabras", "-idea", "idea--nueva", "x".repeat(101)]) {
    assert.equal(postSchema.safeParse({ ...post, slug }).success, false, slug);
  }
});

test("exige categoria valida, contenido suficiente e ilustracion permitida", () => {
  for (const change of [
    { category_id: "no-es-un-uuid" }, { content: "Muy corto" },
    { excerpt: "Corto" }, { cover: "imagen-externa" },
  ]) {
    assert.equal(postSchema.safeParse({ ...post, ...change }).success, false);
  }
});

test("limita los minutos a enteros entre 1 y 60", () => {
  for (const reading_minutes of ["", "abc", "0", "61", "1.5"]) {
    assert.equal(postSchema.safeParse({ ...post, reading_minutes }).success, false);
  }
  for (const reading_minutes of ["1", "60"]) {
    assert.equal(postSchema.parse({ ...post, reading_minutes }).reading_minutes, Number(reading_minutes));
  }
});

test("valida y normaliza categorias antes de persistirlas", () => {
  const category = { name: "  Desarrollo  ", slug: "desarrollo", description: "Ideas para construir aplicaciones." };
  assert.equal(categorySchema.parse(category).name, "Desarrollo");
  assert.equal(categorySchema.safeParse({ ...category, description: "Breve" }).success, false);
  assert.equal(categorySchema.safeParse({ ...category, name: "x".repeat(61) }).success, false);
});
