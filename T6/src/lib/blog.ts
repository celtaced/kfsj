import "server-only";
import { cache } from "react";
import type { Article } from "./database.types";
import { publicClient } from "./supabase/public";

export const getCategories = cache(async () => {
  const { data, error } = await publicClient().from("categories").select("*").order("name");
  if (error) {
    console.error("No se pudieron consultar las categorias", error.message);
    throw new Error("No se pudieron cargar las categorias.");
  }
  return data;
});

export const getArticles = cache(async (): Promise<Article[]> => {
  const [categories, result] = await Promise.all([
    getCategories(),
    publicClient().from("posts").select("*").eq("published", true)
      .order("created_at", { ascending: false }),
  ]);
  if (result.error) {
    console.error("No se pudieron consultar los articulos", result.error.message);
    throw new Error("No se pudieron cargar los articulos.");
  }
  return result.data.map((post) => {
    const category = categories.find((item) => item.id === post.category_id);
    if (!category) throw new Error("Un articulo tiene una categoria inexistente.");
    return { ...post, category };
  });
});

export async function getArticle(slug: string) {
  return (await getArticles()).find((post) => post.slug === slug);
}
