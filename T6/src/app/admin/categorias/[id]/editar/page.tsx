import { notFound } from "next/navigation";
import { z } from "zod";
import { CategoryForm } from "@/components/editor-forms";
import { requireEditor } from "@/lib/auth";

export default async function EditCategoryPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  if (!z.uuid().safeParse(id).success) notFound();
  const { client } = await requireEditor();
  const { data, error } = await client.from("categories").select("*").eq("id", id).maybeSingle();
  if (error) {
    console.error("No se pudo cargar la categoria", error.message);
    throw new Error("No se pudo cargar la categoria.");
  }
  if (!data) notFound();
  return <div className="form-panel"><h1>Ajusta este tema.</h1><CategoryForm category={data} /></div>;
}
