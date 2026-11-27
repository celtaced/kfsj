import { z } from "zod";
import { notFound } from "next/navigation";
import { requireEditor } from "@/lib/auth";
import { PostForm } from "@/components/editor-forms";

export default async function EditPostPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  if (!z.uuid().safeParse(id).success) notFound();
  const { client } = await requireEditor();
  const [post, categories] = await Promise.all([
    client.from("posts").select("*").eq("id", id).maybeSingle(),
    client.from("categories").select("*").order("name"),
  ]);
  if (post.error || categories.error) {
    console.error("No se pudo cargar el editor", post.error?.message ?? categories.error?.message);
    throw new Error("No se pudo cargar el editor.");
  }
  if (!post.data) notFound();
  return <div className="form-panel"><h1>Afina tu idea.</h1><PostForm post={post.data} categories={categories.data} /></div>;
}
