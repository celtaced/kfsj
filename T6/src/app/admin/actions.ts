"use server";

import { z } from "zod";
import { redirect } from "next/navigation";
import { revalidatePath } from "next/cache";
import { requireEditor } from "@/lib/auth";
import { categorySchema, postSchema, type ActionState } from "@/lib/validation";

function invalidate() {
  revalidatePath("/", "layout");
}

function databaseError(error: { code: string; message: string }): ActionState {
  console.error("Operacion editorial rechazada", error.code, error.message);
  if (error.code === "23505") return { message: "Ese slug ya está en uso. Elige otro." };
  if (error.code === "23503") return { message: "La categoría está en uso o ya no existe. Revisa los artículos relacionados." };
  if (error.code === "42501") return { message: "No tienes permisos para realizar esta operación." };
  return { message: "No pudimos guardar los cambios. Intenta de nuevo." };
}

export async function savePost(id: string | null, _: ActionState, form: FormData): Promise<ActionState> {
  const { client } = await requireEditor();
  if (id && !z.uuid().safeParse(id).success) return { message: "El identificador no es válido." };
  const result = postSchema.safeParse({
    title: form.get("title"), slug: form.get("slug"), category_id: form.get("category_id"),
    excerpt: form.get("excerpt"), content: form.get("content"), cover: form.get("cover"),
    reading_minutes: form.get("reading_minutes"), published: form.get("published") === "on",
  });
  if (!result.success) return { message: "Revisa los campos.", errors: z.flattenError(result.error).fieldErrors };
  const query = id
    ? client.from("posts").update(result.data).eq("id", id)
    : client.from("posts").insert(result.data);
  const { data, error } = await query.select("id").maybeSingle();
  if (error) return databaseError(error);
  if (!data) return { message: "El artículo no existe o no tienes acceso." };
  invalidate();
  redirect("/admin?guardado=1");
}

export async function deletePost(id: string): Promise<ActionState> {
  const { client } = await requireEditor();
  if (!z.uuid().safeParse(id).success) return { message: "El identificador no es válido." };
  const { data, error } = await client.from("posts").delete().eq("id", id).select("id").maybeSingle();
  if (error) return databaseError(error);
  if (!data) return { message: "El artículo no existe o no tienes acceso." };
  invalidate();
  return { message: "Artículo eliminado." };
}

export async function saveCategory(id: string | null, _: ActionState, form: FormData): Promise<ActionState> {
  const { client } = await requireEditor();
  if (id && !z.uuid().safeParse(id).success) return { message: "El identificador no es válido." };
  const result = categorySchema.safeParse({
    name: form.get("name"), slug: form.get("slug"), description: form.get("description"),
  });
  if (!result.success) return { message: "Revisa los campos.", errors: z.flattenError(result.error).fieldErrors };
  const query = id
    ? client.from("categories").update(result.data).eq("id", id)
    : client.from("categories").insert(result.data);
  const { data, error } = await query.select("id").maybeSingle();
  if (error) return databaseError(error);
  if (!data) return { message: "La categoría no existe o no tienes acceso." };
  invalidate();
  redirect("/admin/categorias?guardado=1");
}

export async function deleteCategory(id: string): Promise<ActionState> {
  const { client } = await requireEditor();
  if (!z.uuid().safeParse(id).success) return { message: "El identificador no es válido." };
  const { data, error } = await client.from("categories").delete().eq("id", id).select("id").maybeSingle();
  if (error) return databaseError(error);
  if (!data) return { message: "La categoría no existe o no tienes acceso." };
  invalidate();
  return { message: "Categoría eliminada." };
}
