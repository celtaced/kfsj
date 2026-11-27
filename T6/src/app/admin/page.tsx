import Link from "next/link";
import { requireEditor } from "@/lib/auth";
import { DeleteForm } from "@/components/editor-forms";

export default async function AdminPage({ searchParams }: { searchParams: Promise<{ guardado?: string }> }) {
  const { client } = await requireEditor();
  const [result, query] = await Promise.all([client.from("posts").select("*").order("created_at", { ascending: false }), searchParams]);
  if (result.error) {
    console.error("No se pudieron leer los articulos editoriales", result.error.message);
    throw new Error("No se pudieron cargar los articulos.");
  }
  return <>
    <div className="section-heading"><h1>Tus próximas ideas.</h1><Link href="/admin/articulos/nuevo" className="button primary">Nuevo artículo +</Link></div>
    {query.guardado === "1" && <p className="form-message" role="status">Artículo guardado. El blog ya refleja los cambios.</p>}
    <ul className="editor-list">{result.data.map((post) => <li key={post.id} className="editor-row">
      <div><h2>{post.title}</h2><p><span className={`badge ${post.published ? "" : "draft"}`}>{post.published ? "Publicado" : "Borrador"}</span>/{post.slug}</p></div>
      <div className="row-actions">{post.published && <Link className="text-link" href={`/articulos/${post.slug}`}>Ver ↗</Link>}
        <Link className="button secondary" href={`/admin/articulos/${post.id}/editar`}>Editar</Link><DeleteForm kind="post" id={post.id} /></div>
    </li>)}</ul>
    {!result.data.length && <p>Todavía no hay artículos. Crea tu primera idea.</p>}
  </>;
}
