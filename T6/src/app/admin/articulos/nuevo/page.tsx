import { requireEditor } from "@/lib/auth";
import { PostForm } from "@/components/editor-forms";

export default async function NewPostPage() {
  const { client } = await requireEditor();
  const { data, error } = await client.from("categories").select("*").order("name");
  if (error) {
    console.error("No se pudieron leer las categorias", error.message);
    throw new Error("No se pudieron cargar las categorias.");
  }
  return <div className="form-panel"><h1>Una nueva nota.</h1><PostForm categories={data} /></div>;
}
